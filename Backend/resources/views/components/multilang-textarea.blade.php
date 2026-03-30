{{--
    Multi-language tabbed textarea component.
    Stores descriptions as XML-tagged string: <ID>...</ID><EN>...</EN><ZH>...</ZH>

    Props:
    - name: form field name (e.g. 'description')
    - value: current raw description string (for edit mode; empty for create)
    - required: boolean
    - rows: textarea rows (default 4)
    - placeholder: placeholder text
    - label: label text (optional)
    - xModel: Alpine.js x-model binding name for edit forms (optional)
--}}
@props([
    'name' => 'description',
    'value' => '',
    'required' => false,
    'rows' => 4,
    'placeholder' => '',
    'label' => '',
    'xModel' => '',
])

<!-- Multi-language description textarea with auto-translate -->
<div x-data="{
    activeTab: 'id',
    descriptions: { id: '', en: '', zh: '' },
    translating: false,
    translateError: '',

    {{-- <!-- Initialize: parse existing XML-tagged value into per-language fields --> --}}
    init() {
        const raw = this.getInitialValue();
        this.parseValue(raw);

        {{-- <!-- If xModel binding exists, watch for external changes --> --}}
        @if($xModel)
        this.$watch('{{ $xModel }}', (val) => {
            if (val !== this.composedValue()) {
                this.parseValue(val || '');
            }
        });
        @endif
    },

    getInitialValue() {
        @if($xModel)
        return this.{{ $xModel }} || '';
        @else
        return @js($value);
        @endif
    },

    {{-- <!-- Parse XML tags from raw string into per-language object --> --}}
    parseValue(raw) {
        if (!raw) return;
        const regex = /<(ID|EN|ZH)>([\s\S]*?)<\/\1>/gi;
        let match;
        let hasTag = false;
        while ((match = regex.exec(raw)) !== null) {
            this.descriptions[match[1].toLowerCase()] = match[2].trim();
            hasTag = true;
        }
        if (!hasTag) {
            this.descriptions.id = raw.trim();
        }
    },

    {{-- <!-- Compose per-language fields back into XML-tagged string --> --}}
    composedValue() {
        let parts = [];
        ['id', 'en', 'zh'].forEach(lang => {
            const text = (this.descriptions[lang] || '').trim();
            if (text) {
                const tag = lang.toUpperCase();
                parts.push('<' + tag + '>' + text + '</' + tag + '>');
            }
        });
        return parts.join('\n');
    },

    {{-- <!-- Sync composed value to hidden input and optional xModel --> --}}
    syncValue() {
        const val = this.composedValue();
        this.$refs.hiddenInput.value = val;
        @if($xModel)
        this.{{ $xModel }} = val;
        @endif
    },

    {{-- <!-- Auto-translate from current tab to empty language tabs --> --}}
    async autoTranslate() {
        const sourceText = this.descriptions[this.activeTab]?.trim();
        if (!sourceText) {
            this.translateError = '{{ __('ui.translate_empty_source') }}';
            setTimeout(() => this.translateError = '', 3000);
            return;
        }

        this.translating = true;
        this.translateError = '';

        const targets = ['id', 'en', 'zh'].filter(lang => lang !== this.activeTab);

        for (const target of targets) {
            try {
                const response = await fetch('{{ route('api.translate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        text: sourceText,
                        source: this.activeTab,
                        target: target,
                    }),
                });

                const json = await response.json();
                if (json.status === 'success' && json.data?.translated) {
                    this.descriptions[target] = json.data.translated;
                }
            } catch (e) {
                console.error('Translation failed for', target, e);
                this.translateError = '{{ __('ui.translate_failed') }}';
            }
        }

        this.translating = false;
        this.syncValue();
    },

    tabLabel(lang) {
        return { id: '{{ __('ui.description_tab_id') }}', en: '{{ __('ui.description_tab_en') }}', zh: '{{ __('ui.description_tab_zh') }}' }[lang] || lang.toUpperCase();
    }
}" x-init="init()" x-effect="syncValue()">

    {{-- <!-- Label --> --}}
    @if($label)
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    @endif

    {{-- <!-- Tab bar + auto-translate button --> --}}
    <div class="flex items-center justify-between mb-2">
        <div class="flex space-x-1">
            <template x-for="lang in ['id', 'en', 'zh']" :key="lang">
                <button type="button"
                    @click="activeTab = lang"
                    :class="activeTab === lang
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500'"
                    class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors duration-150"
                    x-text="tabLabel(lang)">
                </button>
            </template>
        </div>

        {{-- <!-- Auto-translate button --> --}}
        <button type="button"
            @click="autoTranslate()"
            :disabled="translating"
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md transition-colors duration-150 bg-green-600 hover:bg-green-700 text-white disabled:opacity-50 disabled:cursor-not-allowed">
            <template x-if="translating">
                <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </template>
            <template x-if="!translating">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                </svg>
            </template>
            <span x-text="translating ? '{{ __('ui.translating') }}' : '{{ __('ui.auto_translate') }}'"></span>
        </button>
    </div>

    {{-- <!-- Error message --> --}}
    <div x-show="translateError" x-text="translateError"
        class="text-xs text-red-500 mb-2" x-cloak></div>

    {{-- <!-- Textareas (one per language, shown based on active tab) --> --}}
    <template x-for="lang in ['id', 'en', 'zh']" :key="'ta-' + lang">
        <textarea
            x-show="activeTab === lang"
            x-model="descriptions[lang]"
            @input="syncValue()"
            rows="{{ $rows }}"
            :placeholder="'{{ $placeholder }}'"
            :required="lang === 'id' && {{ $required ? 'true' : 'false' }}"
            class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
        </textarea>
    </template>

    {{-- <!-- Hidden input that holds the composed XML string for form submission --> --}}
    <input type="hidden" name="{{ $name }}" x-ref="hiddenInput" :value="composedValue()">

    {{-- <!-- Help note --> --}}
    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
        {{ __('ui.multilang_help_note') }}
    </p>
</div>

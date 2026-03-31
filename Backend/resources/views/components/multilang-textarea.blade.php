{{--
    Multi-language rich text editor component using Quill.js.
    Stores descriptions as XML-tagged string: <ID>...</ID><EN>...</EN><ZH>...</ZH>
    Content inside each tag is HTML (bold, italic, color, lists, links).

    Props:
    - name: form field name (e.g. 'description')
    - value: current raw description string (for edit mode; empty for create)
    - required: boolean
    - rows: editor height in rows (default 4, converted to rem)
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

@once
{{-- Load Quill.js CSS and JS from CDN (only once per page) --}}
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<style>
    /* Quill editor dark mode overrides */
    html.dark .ql-toolbar.ql-snow {
        background-color: rgb(55, 65, 81) !important;
        border-color: rgb(75, 85, 99) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }
    html.dark .ql-container.ql-snow {
        background-color: rgb(55, 65, 81) !important;
        border-color: rgb(75, 85, 99) !important;
        color: #e5e7eb !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }
    html.dark .ql-editor.ql-blank::before {
        color: rgb(156, 163, 175) !important;
    }
    html.dark .ql-snow .ql-stroke {
        stroke: #e5e7eb !important;
    }
    html.dark .ql-snow .ql-fill {
        fill: #e5e7eb !important;
    }
    html.dark .ql-snow .ql-picker-label {
        color: #e5e7eb !important;
    }
    html.dark .ql-snow .ql-picker-options {
        background-color: rgb(55, 65, 81) !important;
        border-color: rgb(75, 85, 99) !important;
    }
    /* Quill editor sizing */
    .multilang-quill .ql-editor {
        min-height: {{ $rows * 1.5 }}rem;
    }
    .multilang-quill .ql-toolbar.ql-snow {
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .multilang-quill .ql-container.ql-snow {
        border-radius: 0 0 0.5rem 0.5rem;
    }
</style>
@endonce

@php $uid = 'mlrt_' . uniqid(); @endphp

<!-- Multi-language rich text description editor with auto-translate -->
<div x-data="{
    activeTab: 'id',
    descriptions: { id: '', en: '', zh: '' },
    translating: false,
    translateError: '',
    editors: {},

    init() {
        const raw = this.getInitialValue();
        this.parseValue(raw);

        @if($xModel)
        this.$watch('{{ $xModel }}', (val) => {
            if (val !== this.composedValue()) {
                this.parseValue(val || '');
                this.syncEditorsFromData();
            }
        });
        @endif

        this.$nextTick(() => this.initEditors());
    },

    getInitialValue() {
        @if($xModel)
        return this.{{ $xModel }} || '';
        @else
        return @js($value);
        @endif
    },

    initEditors() {
        const toolbarOptions = [
            ['bold', 'italic'],
            [{ 'color': [] }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['link'],
            ['clean']
        ];

        ['id', 'en', 'zh'].forEach(lang => {
            const el = document.getElementById('{{ $uid }}_' + lang);
            if (!el || this.editors[lang]) return;

            const quill = new Quill(el, {
                theme: 'snow',
                placeholder: '{{ $placeholder }}',
                modules: { toolbar: toolbarOptions }
            });

            if (this.descriptions[lang]) {
                quill.root.innerHTML = this.descriptions[lang];
            }

            quill.on('text-change', () => {
                const html = quill.root.innerHTML;
                this.descriptions[lang] = (html === '<p><br></p>') ? '' : html;
                this.syncValue();
            });

            this.editors[lang] = quill;
        });
    },

    syncEditorsFromData() {
        ['id', 'en', 'zh'].forEach(lang => {
            if (this.editors[lang]) {
                const current = this.editors[lang].root.innerHTML;
                const target = this.descriptions[lang] || '';
                if (current !== target && !(current === '<p><br></p>' && target === '')) {
                    this.editors[lang].root.innerHTML = target;
                }
            }
        });
    },

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

    syncValue() {
        const val = this.composedValue();
        this.$refs.hiddenInput.value = val;
        @if($xModel)
        this.{{ $xModel }} = val;
        @endif
    },

    async autoTranslate() {
        const sourceHtml = this.descriptions[this.activeTab]?.trim();
        if (!sourceHtml) {
            this.translateError = '{{ __('ui.translate_empty_source') }}';
            setTimeout(() => this.translateError = '', 3000);
            return;
        }

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = sourceHtml;
        const sourceText = tempDiv.textContent || tempDiv.innerText || '';

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
                    this.descriptions[target] = '<p>' + json.data.translated + '</p>';
                }
            } catch (e) {
                console.error('Translation failed for', target, e);
                this.translateError = '{{ __('ui.translate_failed') }}';
            }
        }

        this.translating = false;
        this.syncEditorsFromData();
        this.syncValue();
    },

    tabLabel(lang) {
        return { id: '{{ __('ui.description_tab_id') }}', en: '{{ __('ui.description_tab_en') }}', zh: '{{ __('ui.description_tab_zh') }}' }[lang] || lang.toUpperCase();
    }
}" x-init="init()" x-effect="syncValue()">

    @if($label)
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    @endif

    {{-- Tab bar + auto-translate button --}}
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

    <div x-show="translateError" x-text="translateError"
        class="text-xs text-red-500 mb-2" x-cloak></div>

    {{-- Quill editors: one per language, toggle visibility by active tab --}}
    <div x-show="activeTab === 'id'" class="multilang-quill">
        <div id="{{ $uid }}_id"></div>
    </div>
    <div x-show="activeTab === 'en'" class="multilang-quill">
        <div id="{{ $uid }}_en"></div>
    </div>
    <div x-show="activeTab === 'zh'" class="multilang-quill">
        <div id="{{ $uid }}_zh"></div>
    </div>

    {{-- Hidden input for form submission --}}
    <input type="hidden" name="{{ $name }}" x-ref="hiddenInput" :value="composedValue()">

    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
        {{ __('ui.multilang_help_note') }}
    </p>
</div>

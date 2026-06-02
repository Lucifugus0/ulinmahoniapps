{{-- Terms & Conditions tab partial — multilang rich text editor for legal content --}}

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-2">Terms & Conditions</h3>
    <p class="text-sm text-gray-500 mb-4">Manage the Terms of Services content displayed on the website and mobile app.</p>

    <!-- Multilang rich text editor for terms content -->
    <x-multilang-textarea
        name="legal_terms"
        :value="''"
        :x-model="'legalTermsContent'"
        :required="true"
        :rows="16"
        :label="__('ui.cm_legal_terms_title')"
    />

    <div class="mt-4">
        <button @click="saveLegalPage('terms-of-services', legalTermsContent)"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
            {{ __('ui.room_btn_save') }}
        </button>
    </div>
</div>

{{-- Footer tab partial — app links, company description, quick links, contact us, follow us, accepted payments --}}

<!-- ====== Sub-section 0: App Links (App Store, Play Store, Login URL) ====== -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">App Links</h3>
    <p class="text-sm text-gray-500 mb-4">Links shown in the footer for App Store, Play Store, and Login/Register buttons.</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">App Store URL</label>
            <input type="text" x-model="appStoreUrl" placeholder="https://apps.apple.com/..."
                class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Play Store URL</label>
            <input type="text" x-model="playStoreUrl" placeholder="https://play.google.com/..."
                class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Login / Register URL</label>
            <input type="text" x-model="loginRegisterUrl" placeholder="/login or https://..."
                class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>
    <div class="mt-4">
        <button @click="saveFooterContent('app_store_url', appStoreUrl); saveFooterContent('play_store_url', playStoreUrl); saveFooterContent('login_register_url', loginRegisterUrl);"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
            {{ __('ui.room_btn_save') }}
        </button>
    </div>
</div>

{{-- Company Description hidden — no longer shown in footer design --}}

<!-- ====== Sub-section 2: Quick Links CRUD ====== -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Links</h3>

    <!-- Add Quick Link form -->
    <div class="flex flex-wrap gap-3 mb-4">
        <input type="text" x-model="newQuickLink.label" placeholder="Label"
            class="flex-1 min-w-[150px] border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <input type="text" x-model="newQuickLink.url" placeholder="URL (e.g. /about)"
            class="flex-1 min-w-[200px] border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <select x-model="newQuickLink.link_group"
            class="w-40 border-2 border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            <option value="quick_links">Quick Links</option>
            <option value="business">Business</option>
        </select>
        <input type="number" x-model.number="newQuickLink.sort_order" placeholder="Sort" min="0"
            class="w-20 border-2 border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <button @click="addQuickLink()" :disabled="!newQuickLink.label.trim() || !newQuickLink.url.trim()"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors duration-200">
            {{ __('ui.add') ?? 'Add' }}
        </button>
    </div>

    <!-- Quick Links table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full min-w-[800px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sort</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Group</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Label</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">URL</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.status') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(link, index) in quickLinks" :key="link.idrec">
                    <tr>
                        <!-- Sort order -->
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <span x-show="editingQuickLinkId !== link.idrec" x-text="link.sort_order"></span>
                            <input x-show="editingQuickLinkId === link.idrec" type="number" x-model.number="editingQuickLink.sort_order" min="0"
                                class="w-16 border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <!-- Link Group -->
                        <td class="px-4 py-3 text-sm">
                            <span x-show="editingQuickLinkId !== link.idrec"
                                class="px-2 py-0.5 rounded-full text-xs font-medium"
                                :class="link.link_group === 'business' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'"
                                x-text="link.link_group === 'business' ? 'Business' : 'Quick Links'"></span>
                            <select x-show="editingQuickLinkId === link.idrec" x-model="editingQuickLink.link_group"
                                class="border border-gray-300 rounded px-2 py-1 text-sm">
                                <option value="quick_links">Quick Links</option>
                                <option value="business">Business</option>
                            </select>
                        </td>
                        <!-- Label -->
                        <td class="px-4 py-3 text-sm text-gray-800">
                            <span x-show="editingQuickLinkId !== link.idrec" x-text="link.label"></span>
                            <input x-show="editingQuickLinkId === link.idrec" type="text" x-model="editingQuickLink.label"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <!-- URL -->
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <span x-show="editingQuickLinkId !== link.idrec" x-text="link.url"></span>
                            <input x-show="editingQuickLinkId === link.idrec" type="text" x-model="editingQuickLink.url"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <!-- Status toggle -->
                        <td class="px-4 py-3 text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" :checked="link.status === 1" @change="toggleQuickLinkStatus(link)" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                        </td>
                        <!-- Actions -->
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <button x-show="editingQuickLinkId !== link.idrec" @click="startEditQuickLink(link)"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">{{ __('ui.edit') }}</button>
                                <button x-show="editingQuickLinkId === link.idrec" @click="saveEditQuickLink(link.idrec)"
                                    class="text-green-600 hover:text-green-800 text-sm font-medium">{{ __('ui.room_btn_save') }}</button>
                                <button x-show="editingQuickLinkId === link.idrec" @click="cancelEditQuickLink()"
                                    class="text-gray-600 hover:text-gray-800 text-sm font-medium">{{ __('ui.cancel') }}</button>
                                <button x-show="editingQuickLinkId !== link.idrec" @click="deleteQuickLink(link.idrec)"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">{{ __('ui.delete') }}</button>
                            </div>
                        </td>
                    </tr>
                </template>
                <!-- Empty state -->
                <tr x-show="quickLinks.length === 0">
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">No quick links yet.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ====== Sub-section 3: Contact Us CRUD ====== -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Us</h3>

    <!-- Add Contact form -->
    <div class="mb-4 space-y-3">
        <!-- Row 1: Icon picker (full width) -->
        <div class="max-w-md">
            <label class="block text-xs font-medium text-gray-500 mb-1">Icon</label>
            <x-fa-icon-picker model="newContact.icon_class" name="new_contact_icon" placeholder="fas fa-phone" />
        </div>
        <!-- Row 2: Other fields -->
        <div class="flex flex-wrap gap-3 items-start">
            <input type="text" x-model="newContact.label" placeholder="Label"
                class="flex-1 min-w-[120px] border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="text" x-model="newContact.value" placeholder="Value (e.g. +62 812...)"
                class="flex-1 min-w-[150px] border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="text" x-model="newContact.link_url" placeholder="Link URL (optional)"
                class="flex-1 min-w-[180px] border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="number" x-model.number="newContact.sort_order" placeholder="Sort" min="0"
                class="w-20 border-2 border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button @click="addContact()" :disabled="!newContact.label.trim()"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors duration-200">
                {{ __('ui.add') ?? 'Add' }}
            </button>
        </div>
    </div>

    <!-- Contacts table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full min-w-[800px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sort</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Icon</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Label</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Value</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Link URL</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.status') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(contact, index) in contacts" :key="contact.idrec">
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <span x-show="editingContactId !== contact.idrec" x-text="contact.sort_order"></span>
                            <input x-show="editingContactId === contact.idrec" type="number" x-model.number="editingContact.sort_order" min="0"
                                class="w-16 border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <!-- Icon preview / edit picker -->
                        <td class="px-4 py-3 text-center">
                            <span x-show="editingContactId !== contact.idrec">
                                <i :class="contact.icon_class" class="text-lg text-gray-600"></i>
                            </span>
                            <div x-show="editingContactId === contact.idrec" class="min-w-[280px]">
                                <x-fa-icon-picker model="editingContact.icon_class" name="edit_contact_icon" placeholder="fas fa-phone" />
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                            <span x-show="editingContactId !== contact.idrec" x-text="contact.label"></span>
                            <input x-show="editingContactId === contact.idrec" type="text" x-model="editingContact.label"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <span x-show="editingContactId !== contact.idrec" x-text="contact.value"></span>
                            <input x-show="editingContactId === contact.idrec" type="text" x-model="editingContact.value"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <span x-show="editingContactId !== contact.idrec" x-text="contact.link_url" class="truncate max-w-[200px] inline-block"></span>
                            <input x-show="editingContactId === contact.idrec" type="text" x-model="editingContact.link_url"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" :checked="contact.status === 1" @change="toggleContactStatus(contact)" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <button x-show="editingContactId !== contact.idrec" @click="startEditContact(contact)"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">{{ __('ui.edit') }}</button>
                                <button x-show="editingContactId === contact.idrec" @click="saveEditContact(contact.idrec)"
                                    class="text-green-600 hover:text-green-800 text-sm font-medium">{{ __('ui.room_btn_save') }}</button>
                                <button x-show="editingContactId === contact.idrec" @click="cancelEditContact()"
                                    class="text-gray-600 hover:text-gray-800 text-sm font-medium">{{ __('ui.cancel') }}</button>
                                <button x-show="editingContactId !== contact.idrec" @click="deleteContact(contact.idrec)"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">{{ __('ui.delete') }}</button>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="contacts.length === 0">
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">No contacts yet.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ====== Sub-section 4: Follow Us (Social Media) CRUD ====== -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Follow Us</h3>

    <!-- Add Social form -->
    <div class="mb-4 space-y-3">
        <!-- Row 1: Icon picker (full width) -->
        <div class="max-w-md">
            <label class="block text-xs font-medium text-gray-500 mb-1">Icon</label>
            <x-fa-icon-picker model="newSocial.icon_class" name="new_social_icon" placeholder="fab fa-instagram" />
        </div>
        <!-- Row 2: Other fields -->
        <div class="flex flex-wrap gap-3 items-start">
            <input type="text" x-model="newSocial.name" placeholder="Name (e.g. Instagram)"
                class="flex-1 min-w-[120px] border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="text" x-model="newSocial.icon_image_url" placeholder="Icon Image URL (optional)"
                class="flex-1 min-w-[180px] border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="text" x-model="newSocial.url" placeholder="URL (e.g. https://instagram.com/...)"
                class="flex-1 min-w-[200px] border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="text" x-model="newSocial.hover_color" placeholder="Hover Color (e.g. #E1306C)"
                class="w-40 border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="number" x-model.number="newSocial.sort_order" placeholder="Sort" min="0"
                class="w-20 border-2 border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button @click="addSocial()" :disabled="!newSocial.name.trim()"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors duration-200">
                {{ __('ui.add') ?? 'Add' }}
            </button>
        </div>
    </div>

    <!-- Socials table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full min-w-[800px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sort</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Icon</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">URL</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.status') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(social, index) in socials" :key="social.idrec">
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <span x-show="editingSocialId !== social.idrec" x-text="social.sort_order"></span>
                            <input x-show="editingSocialId === social.idrec" type="number" x-model.number="editingSocial.sort_order" min="0"
                                class="w-16 border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                            <span x-show="editingSocialId !== social.idrec" x-text="social.name"></span>
                            <input x-show="editingSocialId === social.idrec" type="text" x-model="editingSocial.name"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <!-- Icon preview: show Font Awesome icon or image -->
                        <td class="px-4 py-3 text-center">
                            <template x-if="editingSocialId !== social.idrec">
                                <span>
                                    <i x-show="social.icon_class" :class="social.icon_class" class="text-lg text-gray-600"></i>
                                    <img x-show="social.icon_image_url && !social.icon_class" :src="social.icon_image_url" class="w-6 h-6 inline-block">
                                </span>
                            </template>
                            <template x-if="editingSocialId === social.idrec">
                                <div class="space-y-1 min-w-[280px]">
                                    <x-fa-icon-picker model="editingSocial.icon_class" name="edit_social_icon" placeholder="fab fa-instagram" />
                                    <input type="text" x-model="editingSocial.icon_image_url" class="w-full border border-gray-300 rounded px-2 py-1 text-sm" placeholder="Image URL">
                                </div>
                            </template>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <span x-show="editingSocialId !== social.idrec" x-text="social.url" class="truncate max-w-[200px] inline-block"></span>
                            <input x-show="editingSocialId === social.idrec" type="text" x-model="editingSocial.url"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" :checked="social.status === 1" @change="toggleSocialStatus(social)" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <button x-show="editingSocialId !== social.idrec" @click="startEditSocial(social)"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">{{ __('ui.edit') }}</button>
                                <button x-show="editingSocialId === social.idrec" @click="saveEditSocial(social.idrec)"
                                    class="text-green-600 hover:text-green-800 text-sm font-medium">{{ __('ui.room_btn_save') }}</button>
                                <button x-show="editingSocialId === social.idrec" @click="cancelEditSocial()"
                                    class="text-gray-600 hover:text-gray-800 text-sm font-medium">{{ __('ui.cancel') }}</button>
                                <button x-show="editingSocialId !== social.idrec" @click="deleteSocial(social.idrec)"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">{{ __('ui.delete') }}</button>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="socials.length === 0">
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">No social links yet.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ====== Sub-section 5: Accepted Payments CRUD ====== -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Accepted Payments</h3>

    <!-- Add Payment form -->
    <div class="flex flex-wrap gap-3 mb-4 items-end">
        <input type="text" x-model="newPayment.name" placeholder="Payment Name (e.g. BCA)"
            class="flex-1 min-w-[150px] border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Icon Image</label>
            <input type="file" x-ref="paymentIconInput" accept="image/*" @change="newPaymentIcon = $event.target.files[0]"
                class="border-2 border-gray-200 rounded-lg px-3 py-1.5 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100">
        </div>
        <input type="number" x-model.number="newPayment.sort_order" placeholder="Sort" min="0"
            class="w-20 border-2 border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <button @click="addPayment()" :disabled="!newPayment.name.trim()"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors duration-200">
            {{ __('ui.add') ?? 'Add' }}
        </button>
    </div>

    <!-- Payments table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full min-w-[800px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sort</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Icon</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.status') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(payment, index) in payments" :key="payment.idrec">
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <span x-show="editingPaymentId !== payment.idrec" x-text="payment.sort_order"></span>
                            <input x-show="editingPaymentId === payment.idrec" type="number" x-model.number="editingPayment.sort_order" min="0"
                                class="w-16 border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                            <span x-show="editingPaymentId !== payment.idrec" x-text="payment.name"></span>
                            <input x-show="editingPaymentId === payment.idrec" type="text" x-model="editingPayment.name"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <!-- Icon thumbnail preview -->
                        <td class="px-4 py-3 text-center">
                            <img x-show="payment.icon_url" :src="payment.icon_url" class="w-10 h-6 object-contain inline-block" :alt="payment.name">
                            <span x-show="!payment.icon_url" class="text-gray-400 text-xs">No icon</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" :checked="payment.status === 1" @change="togglePaymentStatus(payment)" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <button x-show="editingPaymentId !== payment.idrec" @click="startEditPayment(payment)"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">{{ __('ui.edit') }}</button>
                                <button x-show="editingPaymentId === payment.idrec" @click="saveEditPayment(payment.idrec)"
                                    class="text-green-600 hover:text-green-800 text-sm font-medium">{{ __('ui.room_btn_save') }}</button>
                                <button x-show="editingPaymentId === payment.idrec" @click="cancelEditPayment()"
                                    class="text-gray-600 hover:text-gray-800 text-sm font-medium">{{ __('ui.cancel') }}</button>
                                <button x-show="editingPaymentId !== payment.idrec" @click="deletePayment(payment.idrec)"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">{{ __('ui.delete') }}</button>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="payments.length === 0">
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">No accepted payments yet.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

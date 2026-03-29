<!-- Content Management page — tagline and hero video CRUD with Alpine.js tabs -->
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                {{ __('ui.content_management_title') }}
            </h1>
            <p class="text-gray-600 mt-1">{{ __('ui.content_management_desc') }}</p>
        </div>

        <!-- Tabbed Interface -->
        <div x-data="contentManagement()" x-init="init()">
            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200 mb-6">
                <button @click="activeTab = 'taglines'" :class="activeTab === 'taglines' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-6 py-3 text-sm font-semibold border-b-2 transition-colors duration-200">
                    {{ __('ui.tagline_tab') }}
                </button>
                <button @click="activeTab = 'videos'" :class="activeTab === 'videos' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-6 py-3 text-sm font-semibold border-b-2 transition-colors duration-200">
                    {{ __('ui.video_tab') }}
                </button>
            </div>

            <!-- ==================== TAGLINES TAB ==================== -->
            <div x-show="activeTab === 'taglines'" x-transition>
                <!-- Add Tagline Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('ui.add_tagline') }}</h3>
                    <div class="flex gap-3">
                        <input type="text" x-model="newTagline" @keydown.enter="addTagline()"
                            placeholder="{{ __('ui.tagline_placeholder') }}"
                            class="flex-1 border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button @click="addTagline()" :disabled="!newTagline.trim() || isLoading"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors duration-200 flex items-center gap-2">
                            <svg x-show="isLoading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('ui.add_tagline') }}
                        </button>
                    </div>
                </div>

                <!-- Tagline List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">{{ __('ui.tagline_text') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.status') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(tagline, index) in taglines" :key="tagline.idrec">
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-600" x-text="index + 1"></td>
                                    <td class="px-6 py-4">
                                        <!-- View mode -->
                                        <span x-show="editingId !== tagline.idrec" class="text-sm text-gray-800" x-text="tagline.tagline"></span>
                                        <!-- Edit mode -->
                                        <input x-show="editingId === tagline.idrec" type="text" x-model="editingText"
                                            @keydown.enter="saveEdit(tagline.idrec)" @keydown.escape="cancelEdit()"
                                            class="w-full border-2 border-blue-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <!-- Status toggle switch -->
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" :checked="tagline.status === 1" @change="toggleStatus(tagline)" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                        </label>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <!-- Edit button -->
                                            <button x-show="editingId !== tagline.idrec" @click="startEdit(tagline)"
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">{{ __('ui.edit') }}</button>
                                            <!-- Save button -->
                                            <button x-show="editingId === tagline.idrec" @click="saveEdit(tagline.idrec)"
                                                class="text-green-600 hover:text-green-800 text-sm font-medium">{{ __('ui.room_btn_save') }}</button>
                                            <!-- Cancel button -->
                                            <button x-show="editingId === tagline.idrec" @click="cancelEdit()"
                                                class="text-gray-600 hover:text-gray-800 text-sm font-medium">{{ __('ui.cancel') }}</button>
                                            <!-- Delete button -->
                                            <button x-show="editingId !== tagline.idrec" @click="deleteTagline(tagline.idrec)"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium">{{ __('ui.delete') }}</button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <!-- Empty state -->
                            <tr x-show="taglines.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">{{ __('ui.no_taglines') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ==================== HERO VIDEOS TAB ==================== -->
            <div x-show="activeTab === 'videos'" x-transition x-cloak>
                <!-- Upload Video Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('ui.upload_video') }}</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.video_title') }}</label>
                            <input type="text" x-model="newVideoTitle" placeholder="{{ __('ui.video_title_placeholder') }}"
                                class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.video_file') }}</label>
                            <input type="file" x-ref="videoFileInput" accept="video/mp4" @change="validateVideoFile($event)"
                                class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1">{{ __('ui.video_constraints') }}</p>
                            <!-- Video validation error message -->
                            <p x-show="videoError" x-text="videoError" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <!-- Upload progress bar -->
                        <div x-show="uploadProgress > 0 && uploadProgress < 100" class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="'width: ' + uploadProgress + '%'"></div>
                        </div>
                        <button @click="uploadVideo()" :disabled="!newVideoTitle.trim() || !selectedVideoFile || isUploading"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 transition-colors duration-200 flex items-center gap-2">
                            <svg x-show="isUploading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('ui.upload_video') }}
                        </button>
                    </div>
                </div>

                <!-- Video List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">{{ __('ui.video_title') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.file_size') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.status') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ __('ui.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(video, index) in videos" :key="video.idrec">
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-600" x-text="index + 1"></td>
                                    <td class="px-6 py-4 text-sm text-gray-800" x-text="video.title"></td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-600" x-text="video.file_size_formatted"></td>
                                    <td class="px-6 py-4 text-center">
                                        <span x-show="video.status === 1" class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">{{ __('ui.active') }}</span>
                                        <button x-show="video.status !== 1" @click="activateVideo(video.idrec)"
                                            class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-blue-100 hover:text-blue-700 cursor-pointer transition-colors">
                                            {{ __('ui.set_active') }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button x-show="video.status !== 1" @click="deleteVideo(video.idrec)"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium">{{ __('ui.delete') }}</button>
                                        <span x-show="video.status === 1" class="text-gray-400 text-xs">—</span>
                                    </td>
                                </tr>
                            </template>
                            <!-- Empty state -->
                            <tr x-show="videos.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">{{ __('ui.no_videos') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        /** Alpine.js component for Content Management — tagline and hero video CRUD */
        function contentManagement() {
            return {
                activeTab: 'taglines',
                // Tagline state
                taglines: [],
                newTagline: '',
                editingId: null,
                editingText: '',
                isLoading: false,
                // Video state
                videos: [],
                newVideoTitle: '',
                selectedVideoFile: null,
                videoError: '',
                isUploading: false,
                uploadProgress: 0,

                init() {
                    this.fetchTaglines();
                    this.fetchVideos();
                },

                // ==================== TAGLINE METHODS ====================

                async fetchTaglines() {
                    try {
                        const res = await fetch('{{ route("content-management.taglines.list") }}');
                        const data = await res.json();
                        if (data.success) this.taglines = data.data;
                    } catch (e) { console.error('Error fetching taglines:', e); }
                },

                async addTagline() {
                    if (!this.newTagline.trim()) return;
                    this.isLoading = true;
                    try {
                        const res = await fetch('{{ route("content-management.taglines.store") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ tagline: this.newTagline.trim() })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.newTagline = '';
                            this.fetchTaglines();
                            this.showToast(data.message, 'success');
                        } else {
                            this.showToast(Object.values(data.errors).flat().join(', '), 'error');
                        }
                    } catch (e) { this.showToast('Error adding tagline', 'error'); }
                    this.isLoading = false;
                },

                startEdit(tagline) {
                    this.editingId = tagline.idrec;
                    this.editingText = tagline.tagline;
                },

                cancelEdit() {
                    this.editingId = null;
                    this.editingText = '';
                },

                async saveEdit(id) {
                    if (!this.editingText.trim()) return;
                    try {
                        const res = await fetch(`{{ url('/settings/content-management/taglines') }}/${id}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ tagline: this.editingText.trim() })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.cancelEdit();
                            this.fetchTaglines();
                            this.showToast(data.message, 'success');
                        }
                    } catch (e) { this.showToast('Error updating tagline', 'error'); }
                },

                async toggleStatus(tagline) {
                    const newStatus = tagline.status === 1 ? 0 : 1;
                    try {
                        const res = await fetch(`{{ url('/settings/content-management/taglines') }}/${tagline.idrec}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ status: newStatus })
                        });
                        const data = await res.json();
                        if (data.success) this.fetchTaglines();
                    } catch (e) { this.showToast('Error toggling status', 'error'); }
                },

                async deleteTagline(id) {
                    if (!confirm('{{ __("ui.confirm_delete_tagline") }}')) return;
                    try {
                        const res = await fetch(`{{ url('/settings/content-management/taglines') }}/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.fetchTaglines();
                            this.showToast(data.message, 'success');
                        }
                    } catch (e) { this.showToast('Error deleting tagline', 'error'); }
                },

                // ==================== VIDEO METHODS ====================

                async fetchVideos() {
                    try {
                        const res = await fetch('{{ route("content-management.videos.list") }}');
                        const data = await res.json();
                        if (data.success) this.videos = data.data;
                    } catch (e) { console.error('Error fetching videos:', e); }
                },

                /** Client-side validation for video file (MP4, max 100MB) */
                validateVideoFile(event) {
                    const file = event.target.files[0];
                    this.videoError = '';
                    this.selectedVideoFile = null;

                    if (!file) return;

                    if (file.type !== 'video/mp4') {
                        this.videoError = '{{ __("ui.video_constraints") }}';
                        this.$refs.videoFileInput.value = '';
                        return;
                    }

                    // 100MB = 104857600 bytes
                    if (file.size > 104857600) {
                        this.videoError = 'File size exceeds 100MB limit.';
                        this.$refs.videoFileInput.value = '';
                        return;
                    }

                    this.selectedVideoFile = file;
                },

                async uploadVideo() {
                    if (!this.newVideoTitle.trim() || !this.selectedVideoFile) return;
                    this.isUploading = true;
                    this.uploadProgress = 0;

                    const formData = new FormData();
                    formData.append('title', this.newVideoTitle.trim());
                    formData.append('video', this.selectedVideoFile);

                    try {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '{{ route("content-management.videos.store") }}');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                        /** Track upload progress */
                        xhr.upload.onprogress = (e) => {
                            if (e.lengthComputable) {
                                this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                            }
                        };

                        xhr.onload = () => {
                            const data = JSON.parse(xhr.responseText);
                            if (data.success) {
                                this.newVideoTitle = '';
                                this.selectedVideoFile = null;
                                this.$refs.videoFileInput.value = '';
                                this.uploadProgress = 0;
                                this.fetchVideos();
                                this.showToast(data.message, 'success');
                            } else {
                                const errors = data.errors ? Object.values(data.errors).flat().join(', ') : 'Upload failed';
                                this.showToast(errors, 'error');
                            }
                            this.isUploading = false;
                        };

                        xhr.onerror = () => {
                            this.showToast('Upload failed', 'error');
                            this.isUploading = false;
                        };

                        xhr.send(formData);
                    } catch (e) {
                        this.showToast('Error uploading video', 'error');
                        this.isUploading = false;
                    }
                },

                async activateVideo(id) {
                    try {
                        const res = await fetch(`{{ url('/settings/content-management/videos') }}/${id}/activate`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.fetchVideos();
                            this.showToast(data.message, 'success');
                        }
                    } catch (e) { this.showToast('Error activating video', 'error'); }
                },

                async deleteVideo(id) {
                    if (!confirm('{{ __("ui.confirm_delete_video") }}')) return;
                    try {
                        const res = await fetch(`{{ url('/settings/content-management/videos') }}/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.fetchVideos();
                            this.showToast(data.message, 'success');
                        } else {
                            this.showToast(data.message, 'error');
                        }
                    } catch (e) { this.showToast('Error deleting video', 'error'); }
                },

                // ==================== HELPERS ====================

                showToast(message, type = 'success') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: type,
                            title: message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }
                }
            };
        }
    </script>
</x-app-layout>

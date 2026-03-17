<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-semibold">{{ __('ui.account_management') }}</h2>
        </div>

        <div id="containerAccount" class="bg-white shadow-md rounded-lg overflow-hidden mt-8">
            <div class="flex justify-between items-center px-6 py-4 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('ui.account_list') }}</h2>
                <div class="flex items-center">
                    <div class="relative">
                        <form method="GET" action="{{ route('users-management') }}" id="searchForm">
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                placeholder="{{ __('ui.search_users') }}"
                                class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 w-48">
                            <div class="absolute left-3 top-2.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="documentTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Updated By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-100 odd:bg-gray-100 even:bg-white">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->role->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $user->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($user->crated_at)->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($user->updated_at)->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div x-data="editModal()">
                                        <button
                                            class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-1 px-3 rounded cursor-pointer"
                                            type="button"
                                            @click.prevent="openEditModal({
                                                            id: '{{ $user->id }}',
                                                            name: '{{ $user->name }}',
                                                            email: '{{ $user->email }}',
                                                            role_id: '{{ $user->role_id }}',
                                                            dep: '{{ $user->dep }}',
                                                            employee_id: '{{ $user->employee_id }}',
                                                            status: '{{ $user->status }}'
                                                        })"
                                            aria-controls="edit-modal-{{ $user->id }}">
                                            Edit
                                        </button>

                                        <!-- Modal backdrop -->
                                        <div class="fixed inset-0 backdrop-blur bg-opacity-30 z-50 transition-opacity"
                                            x-show="modalOpenEdit" x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition ease-out duration-100"
                                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                            aria-hidden="true" x-cloak>
                                        </div>

                                        <!-- Modal dialog -->
                                        <div id="edit-modal-{{ $user->id }}"
                                            class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                                            role="dialog" aria-modal="true" x-show="modalOpenEdit"
                                            x-transition:enter="transition ease-in-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-4"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in-out duration-200"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 translate-y-4" x-cloak>
                                            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6"
                                                @click.outside="closeEditModal()"
                                                @keydown.escape.window="closeEditModal()">
                                                <!-- Modal header -->
                                                <div class="px-5 py-3 border-b border-slate-200">
                                                    <div class="flex justify-between items-center">
                                                        <div class="font-semibold text-slate-800">{{ __('ui.settings_edit_user') }}</div>
                                                        <button type="button"
                                                            class="text-slate-400 hover:text-slate-500"
                                                            @click="closeEditModal()">
                                                            <div class="sr-only">Close</div>
                                                            <svg class="w-4 h-4 fill-current">
                                                                <path
                                                                    d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- Modal content -->
                                                <div class="modal-content text-xs px-5 py-4">
                                                    <form method="POST"
                                                        x-bind:action="'/settings/users/' + currentUser.id"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="grid md:grid-cols-2 md:gap-6">
                                                            <!-- Name -->
                                                            <div class="relative z-0 w-full mb-5 group">
                                                                <input type="text" name="name"
                                                                    x-model="currentUser.name" required
                                                                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                    placeholder=" " />
                                                                <label
                                                                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                                                    Name<span class="text-red-500">*</span>
                                                                </label>
                                                            </div>

                                                            <!-- Email -->
                                                            <div class="relative z-0 w-full mb-5 group">
                                                                <input type="email" name="email"
                                                                    x-model="currentUser.email" required
                                                                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                    placeholder=" " />
                                                                <label
                                                                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                                                    Email<span class="text-red-500">*</span>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <!-- Password (optional for edit) -->
                                                        <div class="relative z-0 w-full mb-5 group">
                                                            <!-- Ganti input password dengan: -->
                                                            <input type="password" name="password"
                                                                :id="'edit_password_' + currentUser.id"
                                                                class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                placeholder=" " />

                                                            <!-- Ganti label dengan: -->
                                                            <label :for="'edit_password_' + currentUser.id"
                                                                class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                                                {{ __('ui.settings_password_leave_blank') }}
                                                            </label>

                                                            <!-- Ganti toggle button dengan: -->
                                                            <button type="button"
                                                                class="absolute right-0 top-0 mt-3 mr-2 text-gray-500 dark:text-gray-400 focus:outline-none toggle-password"
                                                                :data-target="'edit_password_' + currentUser.id">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="h-5 w-5" viewBox="0 0 20 20"
                                                                    fill="currentColor">
                                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                                    <path fill-rule="evenodd"
                                                                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                        </div>

                                                        <div class="grid md:grid-cols-2 md:gap-6">
                                                            <!-- Department -->
                                                            <div class="relative z-0 w-full mb-5 group">
                                                                <select name="department_id" x-model="currentUser.dep"
                                                                    required
                                                                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                                                    <option value="" disabled hidden>{{ __('ui.settings_select_department') }}</option>
                                                                    @foreach ($departments as $dept)
                                                                        <option value="{{ $dept->id }}">
                                                                            {{ $dept->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <label
                                                                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                                                    Department<span class="text-red-500">*</span>
                                                                </label>
                                                            </div>

                                                            <!-- Role -->
                                                            <div class="relative z-0 w-full mb-5 group">
                                                                <select name="role_id" x-model="currentUser.role_id"
                                                                    required
                                                                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                                                    <option value="" disabled hidden>{{ __('ui.settings_select_role') }}</option>
                                                                    @foreach ($roles as $role)
                                                                        <option value="{{ $role->id }}">
                                                                            {{ $role->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <label
                                                                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                                                    Role<span class="text-red-500">*</span>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <!-- Employee ID -->
                                                        <div class="relative z-0 w-full mb-5 group">
                                                            <input type="text" name="employee_id"
                                                                x-model="currentUser.employee_id" required
                                                                class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                placeholder=" " />
                                                            <label
                                                                class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                                                Employee ID<span class="text-red-500">*</span>
                                                            </label>
                                                        </div>

                                                        <!-- Status -->
                                                        <div class="relative z-0 w-full mb-5 group">
                                                            <select name="status" x-model="currentUser.status"
                                                                required
                                                                class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                                                <option value="Active">Active</option>
                                                                <option value="Inactive">Inactive</option>
                                                            </select>
                                                            <label
                                                                class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                                                Status<span class="text-red-500">*</span>
                                                            </label>
                                                        </div>

                                                        <!-- Submit Button -->
                                                        <div
                                                            class="px-5 py-3 border-t border-slate-200 flex justify-end mt-4">
                                                            <button type="submit"
                                                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                                Update
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            <div class="bg-gray-50 rounded p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <form method="GET" action="{{ route('users-management') }}">
                        <div class="flex items-center">
                            <label for="per_page" class="mr-2">Show:</label>
                            <select name="per_page" id="per_page" onchange="this.form.submit()"
                                class="border border-gray-300 rounded px-4 py-2 w-32">
                                <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                                <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="px-6 py-4">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    </div>
    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Toastify({
                    text: "{{ session('success') }}",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)"
                    },
                    stopOnFocus: true,
                }).showToast();
            });
        </script>
    @endif
    <script>
        function editModal() {
            return {
                modalOpenEdit: false,
                currentUser: {
                    id: null,
                    name: '',
                    email: '',
                    role_id: null,
                    dep: null,
                    employee_id: '',
                    status: 'Active'
                },
                openEditModal(user) {
                    this.currentUser = {
                        ...this.currentUser,
                        ...user
                    };
                    this.modalOpenEdit = true;
                },
                closeEditModal() {
                    this.modalOpenEdit = false;
                }
            }
        }
        // Submit search form when user stops typing (with 500ms delay)
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            clearTimeout(this.timer);
            this.timer = setTimeout(function() {
                document.getElementById('searchForm').submit();
            }, 500);
        });

        // Also submit when pressing Enter
        document.getElementById('searchInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('searchForm').submit();
            }
        });

        // Jika menggunakan form submit biasa
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();

            fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Tampilkan toast message
                    showToast(data.toast.type, data.toast.message, data.toast.details);

                    // Jika sukses, redirect
                    if (data.success && data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500); // Beri jeda 1.5 detik untuk melihat toast message
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        // Fungsi untuk menampilkan toast
        function showToast(type, message, details) {
            // Implementasi toast sesuai library yang Anda gunakan
            // Contoh sederhana:
            alert(`${type}: ${message}\n${details}`);
        }
    </script>
</x-app-layout>

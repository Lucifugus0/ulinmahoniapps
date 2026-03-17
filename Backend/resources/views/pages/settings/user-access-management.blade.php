<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between item-start gap-4 mb-8">
            <div>
                <h1
                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                    {{ __('ui.settings_menu_access_management') }}
                </h1>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <select id="userSelect"
                    class="select-custom block w-full md:w-64 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 pl-3 pr-10">
                    <option value="">{{ __('ui.settings_select_user') }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->username }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Permissions Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div
                class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ __('ui.settings_menu_permissions') }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ __('ui.settings_manage_access_desc') }}</p>
                </div>
                <div class="flex gap-2 mt-3 sm:mt-0">
                    <button id="updateAccess"
                        class="btn-primary px-4 py-2 text-sm font-medium rounded-lg shadow-sm flex items-center">
                        <i class="fas fa-save mr-2"></i> {{ __('ui.save_changes') }}
                    </button>
                    <button id="cancelAccess"
                        class="btn-secondary px-4 py-2 text-sm font-medium rounded-lg hidden flex items-center">
                        <i class="fas fa-times mr-2"></i> {{ __('ui.cancel') }}
                    </button>
                </div>
            </div>

            <!-- Permissions Content -->
            <div id="permissionsContainer" class="p-6 hidden animate-fade-in">
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center mb-3 sm:mb-0">
                        <i class="fas fa-filter text-blue-500 mr-2"></i>
                        <label for="mainMenuSelect" class="text-sm font-medium text-gray-700 mr-2">{{ __('ui.settings_filter_main_menu') }}
                            Menu Utama:</label>
                        <select id="mainMenuSelect"
                            class="select-custom border border-gray-300 rounded-lg text-sm px-3 py-2 w-full sm:w-64">
                            <option value="">{{ __('ui.settings_show_all_menus') }}</option>
                            @foreach ($sidebarItems->whereNull('parent_id') as $menu)
                                <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center">
                        <span class="text-xs text-gray-500 mr-2">{{ __('ui.settings_quick_actions') }}</span>
                        <button id="selectAll" class="text-xs text-blue-600 hover:text-blue-800 mr-3">{{ __('ui.settings_select_all') }}
                            Semua</button>
                        <button id="deselectAll" class="text-xs text-gray-600 hover:text-gray-800">{{ __('ui.settings_deselect_all') }}
                            Pilihan</button>
                    </div>
                </div>

                <!-- Permissions Table -->
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                    Akses</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Detail Menu</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipe</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="permissionsTableBody">
                            @foreach ($sidebarItems as $item)
                                @php
                                    $isMainMenu = is_null($item->parent_id);
                                    $isSubMenu = !$isMainMenu && is_null($item->route);
                                    $isListMenu = !$isMainMenu && !is_null($item->route);

                                    $mainMenuId = $isMainMenu
                                        ? $item->id
                                        : ($isSubMenu
                                            ? $item->parent_id
                                            : $sidebarItems->firstWhere('id', $item->parent_id)->parent_id ??
                                                $item->parent_id);

                                    $subMenuId = $isSubMenu ? $item->id : ($isListMenu ? $item->parent_id : null);

                                    // Determine icon and color based on menu type
                                    $icon = 'fas fa-folder';
                                    $color = 'text-blue-500';
                                    $typeClass = 'bg-blue-100 text-blue-800';
                                    $typeText = 'Menu Utama';

                                    if ($isSubMenu) {
                                        $icon = 'fas fa-folder-open';
                                        $color = 'text-purple-500';
                                        $typeClass = 'bg-purple-100 text-purple-800';
                                        $typeText = 'Sub Menu';
                                    } elseif ($isListMenu) {
                                        $icon = 'fas fa-list';
                                        $color = 'text-green-500';
                                        $typeClass = 'bg-green-100 text-green-800';
                                        $typeText = 'List Menu';
                                    }

                                    // Calculate indentation
                                    $indentClass = '';
                                    if ($isSubMenu) {
                                        $indentClass = 'ml-6';
                                    } elseif ($isListMenu) {
                                        $indentClass = 'ml-12';
                                    }
                                @endphp

                                <tr class="menu-item group hover:bg-gray-50 transition-colors duration-150"
                                    data-main-menu="{{ $mainMenuId }}" data-sub-menu="{{ $subMenuId }}"
                                    data-is-main="{{ $isMainMenu ? 'true' : 'false' }}"
                                    data-is-sub="{{ $isSubMenu ? 'true' : 'false' }}"
                                    data-is-list="{{ $isListMenu ? 'true' : 'false' }}">
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <input type="checkbox" class="checkbox-round" value="{{ $item->id }}">
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center {{ $indentClass }}">
                                            <i class="{{ $icon }} {{ $color }} mr-3"></i>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $item->name }}
                                                </div>
                                                @if ($item->route)
                                                    <div class="text-xs text-gray-500">Route: {{ $item->route }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs font-medium {{ $typeClass }} rounded-full">{{ $typeText }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="permission-badge badge-inactive">Tidak Ada Akses</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex items-center text-sm text-gray-500">
                    <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                    <span>Memilih Menu Utama akan otomatis memilih seluruh Sub Menu dan List Menu di bawahnya</span>
                </div>
            </div>
        </div>

        <!-- Empty State (shown when no user is selected) -->
        <div id="emptyState" class="bg-white rounded-xl shadow-sm border border-gray-200 mt-8 p-8 text-center">
            <i class="fas fa-user-lock text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-700">Belum Ada Pengguna Dipilih</h3>
            <p class="text-gray-500 mt-2">Silakan pilih pengguna dari dropdown di atas untuk mengatur izin menu mereka.
            </p>
        </div>
    </div>

    <style>
        .checkbox-normal {
            width: 18px;
            height: 18px;
            border-radius: 3px;
            border: 2px solid #d1d5db;
            appearance: none;
            -webkit-appearance: none;
            outline: none;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
            background-color: white;
        }

        .checkbox-normal:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .checkbox-normal:checked:before {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 12px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .checkbox-normal:indeterminate {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .checkbox-normal:indeterminate:before {
            content: '—';
            position: absolute;
            color: white;
            font-size: 12px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Styling lainnya tetap sama */
        .select-custom {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px;
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .permission-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-active {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .badge-inactive {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.2s ease;
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #4b5563;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }

        /* Additional styling for visual hierarchy */
        .menu-item[data-is-sub="true"] {
            background-color: #fafafa;
        }

        .menu-item[data-is-list="true"] {
            background-color: #f5f5f5;
        }

        .menu-item:hover {
            background-color: #f0f9ff !important;
        }
    </style>

    <script>
        // JavaScript code remains the same as in the original
        const userSelect = document.getElementById('userSelect');
        const updateAccessButton = document.getElementById('updateAccess');
        const cancelAccessButton = document.getElementById('cancelAccess');
        const permissionsContainer = document.getElementById('permissionsContainer');
        const emptyState = document.getElementById('emptyState');
        const mainMenuSelect = document.getElementById('mainMenuSelect');
        const selectAllButton = document.getElementById('selectAll');
        const deselectAllButton = document.getElementById('deselectAll');

        userSelect.addEventListener('change', function() {
            const userId = this.value;

            if (userId) {
                emptyState.classList.add('hidden');
                userSelect.disabled = true;
                cancelAccessButton.classList.remove('hidden');

                fetch(`/user-access/${userId}/permissions`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        permissionsContainer.classList.remove('hidden');

                        // Pastikan data.permissions adalah array
                        const userPermissions = Array.isArray(data.permissions) ? data.permissions : [];

                        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                            checkbox.checked = userPermissions.includes(parseInt(checkbox.value));
                            updateBadgeStatus(checkbox);
                        });

                        // Update menu status based on selections
                        updateAllMenuStatuses();
                    })
                    .catch(error => {
                        console.error('Error fetching permissions:', error);
                        permissionsContainer.classList.add('hidden');
                        Toastify({
                            text: "Gagal memuat izin pengguna!",
                            duration: 3000,
                            close: true,
                            gravity: "bottom",
                            position: "left",
                            style: {
                                background: "#FF5733",
                            },
                        }).showToast();
                    });
            } else {
                userSelect.disabled = false;
                cancelAccessButton.classList.add('hidden');
                permissionsContainer.classList.add('hidden');
                emptyState.classList.remove('hidden');
            }
        });

        updateAccessButton.addEventListener('click', function() {
            const userId = userSelect.value;
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const selectedPermissions = [];

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedPermissions.push(checkbox.value);
                }
            });

            fetch(`/user-access/${userId}/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        permissions: selectedPermissions
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Show success message
                    updateAccessButton.innerHTML = '<i class="fas fa-check mr-2"></i> Saved Successfully!';
                    updateAccessButton.classList.remove('btn-primary');
                    updateAccessButton.classList.add('bg-green-500', 'hover:bg-green-600');

                    setTimeout(() => {
                        updateAccessButton.innerHTML = '<i class="fas fa-save mr-2"></i> Save Changes';
                        updateAccessButton.classList.add('btn-primary');
                        updateAccessButton.classList.remove('bg-green-500', 'hover:bg-green-600');
                    }, 2000);

                    Toastify({
                        text: data.message,
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "left",
                        style: {
                            background: "#4CAF50",
                        },
                        stopOnFocus: true,
                    }).showToast();
                })
                .catch(error => {
                    console.error('Error:', error);
                    Toastify({
                        text: "Gagal memperbarui akses!",
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "left",
                        style: {
                            background: "#FF5733",
                        },
                    }).showToast();
                });
        });

        cancelAccessButton.addEventListener('click', function() {
            userSelect.disabled = false;
            userSelect.value = '';
            cancelAccessButton.classList.add('hidden');
            permissionsContainer.classList.add('hidden');
            emptyState.classList.remove('hidden');

            // Uncheck all boxes
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
                updateBadgeStatus(checkbox);
            });
        });

        mainMenuSelect.addEventListener('change', function() {
            const selectedMainMenu = this.value;
            document.querySelectorAll('.menu-item').forEach(row => {
                const menuGroup = row.getAttribute('data-main-menu');
                row.style.display = (selectedMainMenu === "" || menuGroup === selectedMainMenu) ? "" :
                    "none";
            });
        });

        selectAllButton.addEventListener('click', function() {
            const visibleRows = Array.from(document.querySelectorAll('.menu-item'))
                .filter(row => row.style.display !== 'none');

            visibleRows.forEach(row => {
                const checkbox = row.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = true;
                    updateBadgeStatus(checkbox);
                }
            });

            // Update status semua main menu
            const allMainMenus = new Set();
            document.querySelectorAll('.menu-item[data-is-main="true"]').forEach(row => {
                allMainMenus.add(row.getAttribute('data-main-menu'));
            });

            allMainMenus.forEach(mainMenuId => {
                updateMainMenuStatus(mainMenuId);
            });
        });


        deselectAllButton.addEventListener('click', function() {
            const visibleRows = Array.from(document.querySelectorAll('.menu-item'))
                .filter(row => row.style.display !== 'none');

            visibleRows.forEach(row => {
                const checkbox = row.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = false;
                    updateBadgeStatus(checkbox);
                }
            });

            // Update status semua main menu
            const allMainMenus = new Set();
            document.querySelectorAll('.menu-item[data-is-main="true"]').forEach(row => {
                allMainMenus.add(row.getAttribute('data-main-menu'));
            });

            allMainMenus.forEach(mainMenuId => {
                updateMainMenuStatus(mainMenuId);
            });
        });

        // Enhanced checkbox logic
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateBadgeStatus(this);

                const row = this.closest('tr');
                const isMainMenu = row.getAttribute('data-is-main') === 'true';
                const isSubMenu = row.getAttribute('data-is-sub') === 'true';
                const isListMenu = row.getAttribute('data-is-list') === 'true';
                const menuId = this.value;
                const isChecked = this.checked;

                if (isMainMenu) {
                    // When main menu is checked/unchecked, update all its children
                    const mainMenuId = row.getAttribute('data-main-menu');

                    // Update all sub menus under this main menu
                    document.querySelectorAll(
                        `.menu-item[data-main-menu="${mainMenuId}"]:not([data-is-main="true"]) input[type="checkbox"]`
                    ).forEach(childCheckbox => {
                        childCheckbox.checked = isChecked;
                        updateBadgeStatus(childCheckbox);
                    });
                } else if (isSubMenu || isListMenu) {
                    // For sub menus and list menus, update parent status
                    const mainMenuId = row.getAttribute('data-main-menu');
                    updateMainMenuStatus(mainMenuId);

                    // If this is a sub menu, also update its children
                    if (isSubMenu) {
                        const subMenuId = row.getAttribute('data-sub-menu');
                        document.querySelectorAll(
                            `.menu-item[data-sub-menu="${subMenuId}"] input[type="checkbox"]`
                        ).forEach(listCheckbox => {
                            listCheckbox.checked = isChecked;
                            updateBadgeStatus(listCheckbox);
                        });
                    }
                }
            });
        });

        function updateBadgeStatus(checkbox) {
            const row = checkbox.closest('tr');
            const badge = row.querySelector('.permission-badge');

            if (checkbox.checked) {
                badge.textContent = 'Access Granted';
                badge.classList.remove('badge-inactive');
                badge.classList.add('badge-active');
            } else {
                badge.textContent = 'No Access';
                badge.classList.remove('badge-active');
                badge.classList.add('badge-inactive');
            }
        }

        function updateAllMenuStatuses() {
            // Get all main menu IDs
            const mainMenuIds = new Set();
            document.querySelectorAll('.menu-item[data-is-main="true"]').forEach(row => {
                mainMenuIds.add(row.getAttribute('data-main-menu'));
            });

            // Update status for each main menu and its children
            mainMenuIds.forEach(mainMenuId => {
                updateMainMenuStatus(mainMenuId);

                // Update all sub menus under this main menu
                document.querySelectorAll(
                    `.menu-item[data-main-menu="${mainMenuId}"][data-is-sub="true"]`
                ).forEach(subMenuRow => {
                    const subMenuId = subMenuRow.getAttribute('data-sub-menu');
                    updateSubMenuStatus(subMenuId);
                });
            });
        }

        function updateSubMenuStatus(subMenuId) {
            const subMenuCheckbox = document.querySelector(
                `.menu-item[data-sub-menu="${subMenuId}"][data-is-sub="true"] input[type="checkbox"]`
            );

            if (subMenuCheckbox) {
                // Count checked list menus under this sub menu
                const listMenuCheckboxes = document.querySelectorAll(
                    `.menu-item[data-sub-menu="${subMenuId}"][data-is-list="true"] input[type="checkbox"]`
                );

                const checkedCount = Array.from(listMenuCheckboxes).filter(cb => cb.checked).length;
                const totalCount = listMenuCheckboxes.length;

                // Sub menu should be checked if at least one list menu is checked
                subMenuCheckbox.checked = checkedCount > 0;
                subMenuCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;

                updateBadgeStatus(subMenuCheckbox);
            }
        }

        function updateMainMenuStatus(mainMenuId) {
            const mainMenuCheckbox = document.querySelector(
                `.menu-item[data-main-menu="${mainMenuId}"][data-is-main="true"] input[type="checkbox"]`
            );

            if (mainMenuCheckbox) {
                // Get all direct children of this main menu
                const childCheckboxes = document.querySelectorAll(
                    `.menu-item[data-main-menu="${mainMenuId}"]:not([data-is-main="true"]) input[type="checkbox"]`
                );

                const checkedCount = Array.from(childCheckboxes).filter(cb => cb.checked).length;
                const totalCount = childCheckboxes.length;

                // Main menu should be checked if all children are checked
                mainMenuCheckbox.checked = checkedCount > 0;
                mainMenuCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;

                updateBadgeStatus(mainMenuCheckbox);
            }
        }
    </script>
</x-app-layout>

<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between item-start gap-4 mb-8">
            <div>
                <h1
                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                    {{ __('ui.master_role_management') }}
                </h1>
                <p class="text-sm text-gray-600 mt-2">{{ __('ui.manage_roles_desc') }}</p>
            </div>
            <div>
                <button onclick="openNewRoleModal()"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 shadow-md hover:shadow-lg">
                    <i class="fas fa-plus mr-2"></i>
                    {{ __('ui.new_role') }}
                </button>
            </div>
        </div>

        <!-- Role Assignment Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Search and Filter Section -->
            <div class="p-4 border-b border-gray-200">
                <form id="searchForm" method="GET" action="{{ route('master-role-management') }}">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <!-- Search Input -->
                        <div class="w-full md:w-1/3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <input type="text" name="search" id="searchInput"
                                    value="{{ request('search') }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900"
                                    placeholder="{{ __('ui.search_role_placeholder') }}">
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="flex flex-wrap items-center gap-4">
                            <!-- Per Page -->
                            <div class="flex items-center">
                                <span class="text-sm text-gray-600 mr-2">{{ __('ui.show') }}:</span>
                                <select name="per_page" id="perPageSelect"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm bg-white text-gray-900">
                                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                    <option value="8" {{ $perPage == 8 ? 'selected' : '' }}>8</option>
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>All</option>
                                </select>
                            </div>

                            <!-- Reset Button -->
                            @if(request('search') || request('per_page'))
                                <a href="{{ route('master-role-management') }}"
                                    class="inline-flex items-center px-3 py-2 text-sm text-gray-600 hover:text-gray-800 transition"
                                    title="Reset Filter">
                                    <i class="fas fa-undo mr-1"></i> {{ __('ui.reset') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.user_info') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.email') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.current_role') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.sidebar_access') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.dashboard_widgets') }}</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody" class="bg-white divide-y divide-gray-200">
                        @include('pages.settings.partials.master-role-table', ['adminUsers' => $adminUsers, 'perPage' => $perPage])
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($perPage !== 'all' && $adminUsers->hasPages())
                <div class="bg-gray-50 rounded p-4" id="paginationContainer">
                    {{ $adminUsers->appends(request()->input())->links() }}
                </div>
            @endif

        </div>

        <!-- Information Card -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                <div>
                    <h4 class="text-sm font-semibold text-blue-800 mb-1">{{ __('ui.information') }}</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>{{ __('ui.info_access_rights') }}</li>
                        <li>{{ __('ui.info_one_role') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- New Role Modal -->
    <div id="newRoleModal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-900">
                    {{ __('ui.create_new_role') }}
                </h3>
                <button onclick="closeNewRoleModal()" class="text-gray-400 hover:text-gray-500 transition duration-150">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="newRoleForm" class="mt-4">
                <div class="mb-4">
                    <label for="roleName" class="block text-sm font-medium text-gray-700 mb-2">
                        Role Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="roleName" name="name" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        placeholder="Enter role name">
                    <p class="mt-1 text-xs text-gray-500">Enter a unique name for the role</p>
                </div>

                <div class="flex justify-end items-center gap-3 pt-4 border-t mt-4">
                    <button type="button" onclick="closeNewRoleModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors duration-150">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-150">
                        <i class="fas fa-save mr-2"></i>
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Access Rights Modal -->
    <div id="accessRightsModal"
        class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
        <div class="relative top-4 mx-auto p-6 border w-11/12 max-w-7xl shadow-xl rounded-xl bg-white my-4">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-900">
                    Access Rights - <span id="modalUserName"></span>
                </h3>
                <button onclick="closeAccessRightsModal()"
                    class="text-gray-400 hover:text-gray-500 transition duration-150">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="mt-4">
                <!-- Filter Section -->
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center mb-3 sm:mb-0">
                        <i class="fas fa-filter text-blue-500 mr-2"></i>
                        <label for="modalMainMenuSelect" class="text-sm font-medium text-gray-700 mr-2">Filter by Main
                            Menu:</label>
                        <select id="modalMainMenuSelect"
                            class="select-custom border border-gray-300 rounded-lg text-sm px-3 py-2 w-full sm:w-64">
                            <option value="">Show All Menus</option>
                            @foreach ($sidebarItems->whereNull('parent_id') as $menu)
                                <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center">
                        <span class="text-xs text-gray-500 mr-2">Quick Actions:</span>
                        <button id="modalSelectAll" class="text-xs text-blue-600 hover:text-blue-800 mr-3">Select
                            All</button>
                        <button id="modalDeselectAll" class="text-xs text-gray-600 hover:text-gray-800">Deselect
                            All</button>
                    </div>
                </div>

                <!-- Permissions Table -->
                <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-[60vh]">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                    Access</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Menu Detail</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="modalPermissionsTableBody">
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

                                    $icon = 'fas fa-folder';
                                    $color = 'text-blue-500';
                                    $typeClass = 'bg-blue-100 text-blue-800';
                                    $typeText = 'Main Menu';

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
                                        @if ($item->permission_id)
                                            <input type="checkbox" class="modal-checkbox-round"
                                                value="{{ $item->permission_id }}">
                                        @else
                                            <span class="text-xs text-gray-400 italic">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center {{ $indentClass }}">
                                            <i class="{{ $icon }} {{ $color }} mr-3"></i>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $item->name }}
                                                </div>
                                                @if ($item->route)
                                                    <div class="text-xs text-gray-500">Route: {{ $item->route }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs font-medium {{ $typeClass }} rounded-full">{{ $typeText }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="permission-badge badge-inactive">No Access</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 flex flex-col gap-1 text-sm text-gray-500">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-blue-400 mt-0.5 flex-shrink-0"></i>
                        <span>Mencentang <strong>Main Menu</strong> akan otomatis mencentang semua Sub Menu dan List Menu di bawahnya.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fas fa-exclamation-triangle text-amber-400 mt-0.5 flex-shrink-0"></i>
                        <span>Untuk menonaktifkan akses item tertentu (mis. Parking, Master Properties), centang/hapus centang langsung pada item tersebut — bukan pada Main Menu.</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end items-center gap-3 pt-4 border-t mt-4">
                <button onclick="closeAccessRightsModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors duration-150">
                    Cancel
                </button>
                <button onclick="saveAccessRights()"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-150">
                    <i class="fas fa-save mr-2"></i>
                    Save Access Rights
                </button>
            </div>
        </div>
    </div>

    <!-- Dashboard Widgets Modal -->
    <div id="dashboardWidgetsModal"
        class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
        <div class="relative top-4 mx-auto p-6 border w-11/12 max-w-7xl shadow-xl rounded-xl bg-white my-4">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-900">
                    Dashboard Widgets - Role: <span id="modalRoleName"></span>
                </h3>
                <button onclick="closeDashboardWidgetsModal()"
                    class="text-gray-400 hover:text-gray-500 transition duration-150">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="mt-4">
                <!-- Filter Section -->
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 p-4 bg-purple-50 rounded-lg">
                    <div class="flex items-center mb-3 sm:mb-0">
                        <i class="fas fa-filter text-purple-500 mr-2"></i>
                        <label for="widgetCategoryFilter" class="text-sm font-medium text-gray-700 mr-2">Filter by
                            Category:</label>
                        <select id="widgetCategoryFilter"
                            class="select-custom border border-gray-300 rounded-lg text-sm px-3 py-2 w-full sm:w-64">
                            <option value="">Show All Categories</option>
                            <option value="stats">Stats (Booking)</option>
                            <option value="finance">Finance (Keuangan)</option>
                            <option value="rooms">Rooms (Kamar)</option>
                            <option value="reports">Reports (Laporan)</option>
                            <option value="communication">Communication</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <span class="text-xs text-gray-500 mr-2">Quick Actions:</span>
                        <button id="widgetSelectAll" class="text-xs text-purple-600 hover:text-purple-800 mr-3">Select
                            All</button>
                        <button id="widgetDeselectAll" class="text-xs text-gray-600 hover:text-gray-800">Deselect
                            All</button>
                    </div>
                </div>

                <!-- Widgets Table -->
                <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-96">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                    Access</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Widget Name</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="widgetsTableBody">
                            @foreach ($dashboardWidgets as $widget)
                                @php
                                    $categoryColors = [
                                        'stats' => [
                                            'bg' => 'bg-blue-100',
                                            'text' => 'text-blue-800',
                                            'icon' => 'text-blue-500',
                                        ],
                                        'finance' => [
                                            'bg' => 'bg-emerald-100',
                                            'text' => 'text-emerald-800',
                                            'icon' => 'text-emerald-500',
                                        ],
                                        'rooms' => [
                                            'bg' => 'bg-purple-100',
                                            'text' => 'text-purple-800',
                                            'icon' => 'text-purple-500',
                                        ],
                                        'reports' => [
                                            'bg' => 'bg-orange-100',
                                            'text' => 'text-orange-800',
                                            'icon' => 'text-orange-500',
                                        ],
                                        'communication' => [
                                            'bg' => 'bg-pink-100',
                                            'text' => 'text-pink-800',
                                            'icon' => 'text-pink-500',
                                        ],
                                    ];

                                    $colors = $categoryColors[$widget->category] ?? [
                                        'bg' => 'bg-gray-100',
                                        'text' => 'text-gray-800',
                                        'icon' => 'text-gray-500',
                                    ];
                                @endphp

                                <tr class="widget-item group hover:bg-gray-50 transition-colors duration-150"
                                    data-category="{{ $widget->category }}">
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <input type="checkbox" class="widget-checkbox" value="{{ $widget->id }}">
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="{{ $widget->icon }} {{ $colors['icon'] }} mr-3"></i>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $widget->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">{{ $widget->description }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs font-medium {{ $colors['bg'] }} {{ $colors['text'] }} rounded-full">
                                            {{ ucfirst($widget->category) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="widget-status-badge badge-inactive">No Access</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center text-sm text-gray-500">
                    <i class="fas fa-info-circle text-purple-400 mr-2"></i>
                    <span>Widgets ini akan diterapkan untuk semua user dengan role yang dipilih</span>
                </div>
            </div>

            <div class="flex justify-end items-center gap-3 pt-4 border-t mt-4">
                <button onclick="closeDashboardWidgetsModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors duration-150">
                    Cancel
                </button>
                <button onclick="saveDashboardWidgets()"
                    class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-150">
                    <i class="fas fa-save mr-2"></i>
                    Save Dashboard Widgets
                </button>
            </div>
        </div>
    </div>

    <style>
        .select-custom {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px;
        }

        .btn-access-rights:hover {
            transform: translateY(-1px);
        }

        .modal-checkbox-round {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 2px solid #d1d5db;
            appearance: none;
            -webkit-appearance: none;
            outline: none;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
            background-color: white;
        }

        .modal-checkbox-round:hover {
            border-color: #3b82f6;
        }

        .modal-checkbox-round:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .modal-checkbox-round:checked:after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
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

        /* Dashboard Widgets Styles */
        .btn-widgets:hover {
            transform: translateY(-1px);
        }

        .widget-checkbox {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 2px solid #d1d5db;
            appearance: none;
            -webkit-appearance: none;
            outline: none;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
            background-color: white;
        }

        .widget-checkbox:hover {
            border-color: #a855f7;
        }

        .widget-checkbox:checked {
            background-color: #a855f7;
            border-color: #a855f7;
        }

        .widget-checkbox:checked:after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .widget-status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
    </style>

    <script>
        console.log('=== Master Role Management Script Loaded ===');

        let currentUserId = null;

        // Auto-search with page refresh
        document.addEventListener('DOMContentLoaded', function() {
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const perPageSelect = document.getElementById('perPageSelect');
            const searchForm = document.getElementById('searchForm');

            if (searchInput && searchForm) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        searchForm.submit();
                    }, 500);
                });
            }

            if (perPageSelect && searchForm) {
                perPageSelect.addEventListener('change', function() {
                    searchForm.submit();
                });
            }
        });

        // New Role Modal Functions
        function openNewRoleModal() {
            document.getElementById('newRoleModal').classList.remove('hidden');
            document.getElementById('roleName').value = '';
        }

        function closeNewRoleModal() {
            document.getElementById('newRoleModal').classList.add('hidden');
            document.getElementById('roleName').value = '';
        }

        // Handle New Role Form Submission
        document.getElementById('newRoleForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const roleName = document.getElementById('roleName').value.trim();

            if (!roleName) {
                Toastify({
                    text: "Role name is required!",
                    duration: 3000,
                    close: true,
                    gravity: "bottom",
                    position: "left",
                    style: {
                        background: "#FF5733",
                    },
                }).showToast();
                return;
            }

            const submitButton = e.target.querySelector('button[type="submit"]');
            const originalContent = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...';

            fetch('/master-role/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: roleName
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Toastify({
                            text: data.message,
                            duration: 3000,
                            close: true,
                            gravity: "bottom",
                            position: "left",
                            style: {
                                background: "#4CAF50",
                            },
                        }).showToast();

                        // Add new role to all dropdowns
                        const roleSelects = document.querySelectorAll('.role-select');
                        roleSelects.forEach(select => {
                            const option = document.createElement('option');
                            option.value = data.role.id;
                            option.textContent = data.role.name;
                            select.appendChild(option);
                        });

                        closeNewRoleModal();
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalContent;
                    } else {
                        throw new Error(data.message || 'Failed to create role');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Toastify({
                        text: error.message || "Failed to create role!",
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "left",
                        style: {
                            background: "#FF5733",
                        },
                    }).showToast();

                    submitButton.disabled = false;
                    submitButton.innerHTML = originalContent;
                });
        });

        function manageAccessRights(userId, userName) {
            currentUserId = userId;
            document.getElementById('modalUserName').textContent = userName;
            document.getElementById('accessRightsModal').classList.remove('hidden');

            // Fetch user permissions
            fetch(`/master-role/permissions/${userId}`)
                .then(response => response.json())
                .then(data => {
                    const userPermissions = Array.isArray(data.permissions) ? data.permissions : [];

                    document.querySelectorAll('.modal-checkbox-round').forEach(checkbox => {
                        checkbox.checked = userPermissions.includes(parseInt(checkbox.value));
                        updateModalBadgeStatus(checkbox);
                    });

                    updateAllModalMenuStatuses();
                })
                .catch(error => {
                    console.error('Error fetching permissions:', error);
                    Toastify({
                        text: "Failed to load permissions!",
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "left",
                        style: {
                            background: "#FF5733",
                        },
                    }).showToast();
                });
        }

        function closeAccessRightsModal() {
            document.getElementById('accessRightsModal').classList.add('hidden');
            currentUserId = null;
        }

        function saveAccessRights() {
            if (!currentUserId) return;

            const checkboxes = document.querySelectorAll('.modal-checkbox-round');
            const selectedPermissions = [];

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedPermissions.push(checkbox.value);
                }
            });

            // Remove duplicates from selectedPermissions array
            const uniquePermissions = [...new Set(selectedPermissions)];

            fetch(`/master-role/update-permissions/${currentUserId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        permissions: uniquePermissions
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Toastify({
                            text: data.message,
                            duration: 3000,
                            close: true,
                            gravity: "bottom",
                            position: "left",
                            style: {
                                background: "#4CAF50",
                            },
                        }).showToast();

                        closeAccessRightsModal();
                    } else {
                        Toastify({
                            text: data.message || "Failed to update permissions!",
                            duration: 3000,
                            close: true,
                            gravity: "bottom",
                            position: "left",
                            style: {
                                background: "#FF5733",
                            },
                        }).showToast();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Toastify({
                        text: "Failed to update permissions: " + (error.message || "Unknown error"),
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "left",
                        style: {
                            background: "#FF5733",
                        },
                    }).showToast();
                });
        }

        // Modal filter and selection handlers
        document.getElementById('modalMainMenuSelect').addEventListener('change', function() {
            const selectedMainMenu = this.value;
            document.querySelectorAll('#modalPermissionsTableBody .menu-item').forEach(row => {
                const menuGroup = row.getAttribute('data-main-menu');
                row.style.display = (selectedMainMenu === "" || menuGroup === selectedMainMenu) ? "" :
                    "none";
            });
        });

        document.getElementById('modalSelectAll').addEventListener('click', function() {
            const visibleRows = Array.from(document.querySelectorAll('#modalPermissionsTableBody .menu-item'))
                .filter(row => row.style.display !== 'none');

            visibleRows.forEach(row => {
                const checkbox = row.querySelector('.modal-checkbox-round');
                if (checkbox) {
                    checkbox.checked = true;
                    updateModalBadgeStatus(checkbox);
                }
            });

            updateAllModalMenuStatuses();
        });

        document.getElementById('modalDeselectAll').addEventListener('click', function() {
            const visibleRows = Array.from(document.querySelectorAll('#modalPermissionsTableBody .menu-item'))
                .filter(row => row.style.display !== 'none');

            visibleRows.forEach(row => {
                const checkbox = row.querySelector('.modal-checkbox-round');
                if (checkbox) {
                    checkbox.checked = false;
                    updateModalBadgeStatus(checkbox);
                }
            });

            updateAllModalMenuStatuses();
        });

        // Checkbox change handlers
        document.querySelectorAll('.modal-checkbox-round').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateModalBadgeStatus(this);

                const row = this.closest('tr');
                const isMainMenu = row.getAttribute('data-is-main') === 'true';
                const isSubMenu = row.getAttribute('data-is-sub') === 'true';
                const isChecked = this.checked;

                if (isMainMenu) {
                    const mainMenuId = row.getAttribute('data-main-menu');
                    if (isChecked) {
                        // Checking main menu: cascade check to ALL children (select all)
                        document.querySelectorAll(
                            `#modalPermissionsTableBody .menu-item[data-main-menu="${mainMenuId}"]:not([data-is-main="true"]) .modal-checkbox-round`
                        ).forEach(childCheckbox => {
                            childCheckbox.checked = true;
                            updateModalBadgeStatus(childCheckbox);
                        });
                    }
                    // Unchecking main menu: do NOT cascade to children.
                    // Children keep their current states. Main menu badge already updated above.
                } else if (isSubMenu) {
                    const mainMenuId = row.getAttribute('data-main-menu');
                    updateModalMainMenuStatus(mainMenuId);

                    const subMenuId = row.getAttribute('data-sub-menu');
                    document.querySelectorAll(
                        `#modalPermissionsTableBody .menu-item[data-sub-menu="${subMenuId}"] .modal-checkbox-round`
                    ).forEach(listCheckbox => {
                        listCheckbox.checked = isChecked;
                        updateModalBadgeStatus(listCheckbox);
                    });
                } else {
                    // List item changed → update sub-menu parent, then main menu
                    const mainMenuId = row.getAttribute('data-main-menu');
                    const subMenuId = row.getAttribute('data-sub-menu');

                    if (subMenuId && subMenuId !== '0' && subMenuId !== '') {
                        updateModalSubMenuStatus(subMenuId);
                    }

                    updateModalMainMenuStatus(mainMenuId);
                }
            });
        });

        function updateModalBadgeStatus(checkbox) {
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

        function updateAllModalMenuStatuses() {
            // Update sub-menu statuses first
            const subMenuIds = new Set();
            document.querySelectorAll('#modalPermissionsTableBody .menu-item[data-is-sub="true"]').forEach(row => {
                subMenuIds.add(row.getAttribute('data-sub-menu'));
            });
            subMenuIds.forEach(subMenuId => {
                updateModalSubMenuStatus(subMenuId);
            });

            // Then update main menu statuses
            const mainMenuIds = new Set();
            document.querySelectorAll('#modalPermissionsTableBody .menu-item[data-is-main="true"]').forEach(row => {
                mainMenuIds.add(row.getAttribute('data-main-menu'));
            });
            mainMenuIds.forEach(mainMenuId => {
                updateModalMainMenuStatus(mainMenuId);
            });
        }

        function updateModalSubMenuStatus(subMenuId) {
            const subMenuCheckbox = document.querySelector(
                `#modalPermissionsTableBody .menu-item[data-sub-menu="${subMenuId}"][data-is-sub="true"] .modal-checkbox-round`
            );

            if (subMenuCheckbox) {
                const listCheckboxes = document.querySelectorAll(
                    `#modalPermissionsTableBody .menu-item[data-sub-menu="${subMenuId}"][data-is-list="true"] .modal-checkbox-round`
                );

                const checkedCount = Array.from(listCheckboxes).filter(cb => cb.checked).length;
                subMenuCheckbox.checked = checkedCount > 0;

                updateModalBadgeStatus(subMenuCheckbox);
            }
        }

        function updateModalMainMenuStatus(mainMenuId) {
            const mainMenuCheckbox = document.querySelector(
                `#modalPermissionsTableBody .menu-item[data-main-menu="${mainMenuId}"][data-is-main="true"] .modal-checkbox-round`
            );

            if (mainMenuCheckbox) {
                const childCheckboxes = document.querySelectorAll(
                    `#modalPermissionsTableBody .menu-item[data-main-menu="${mainMenuId}"]:not([data-is-main="true"]) .modal-checkbox-round`
                );

                const checkedCount = Array.from(childCheckboxes).filter(cb => cb.checked).length;
                mainMenuCheckbox.checked = checkedCount > 0;

                updateModalBadgeStatus(mainMenuCheckbox);
            }
        }

        // ========================================
        // Dashboard Widgets Management Functions
        // ========================================
        let currentRoleId = null;

        function manageDashboardWidgets(roleId, roleName) {
            currentRoleId = roleId;
            document.getElementById('modalRoleName').textContent = roleName;
            document.getElementById('dashboardWidgetsModal').classList.remove('hidden');

            // Fetch role's assigned widgets
            fetch(`/role/${roleId}/dashboard-widgets`)
                .then(response => response.json())
                .then(data => {
                    const assignedWidgetIds = data.widget_ids || [];

                    // Update checkboxes based on assigned widgets
                    document.querySelectorAll('.widget-checkbox').forEach(checkbox => {
                        checkbox.checked = assignedWidgetIds.includes(parseInt(checkbox.value));
                        updateWidgetBadgeStatus(checkbox);
                    });
                })
                .catch(error => {
                    console.error('Error fetching role widgets:', error);
                    Toastify({
                        text: "Failed to load dashboard widgets!",
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "left",
                        style: {
                            background: "#FF5733",
                        },
                    }).showToast();
                });
        }

        function closeDashboardWidgetsModal() {
            document.getElementById('dashboardWidgetsModal').classList.add('hidden');
            currentRoleId = null;
        }

        function saveDashboardWidgets() {
            if (!currentRoleId) return;

            const checkboxes = document.querySelectorAll('.widget-checkbox');
            const selectedWidgetIds = [];

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedWidgetIds.push(parseInt(checkbox.value));
                }
            });

            fetch(`/role/${currentRoleId}/dashboard-widgets`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        widget_ids: selectedWidgetIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Toastify({
                        text: data.message,
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "left",
                        style: {
                            background: "#4CAF50",
                        },
                    }).showToast();

                    closeDashboardWidgetsModal();
                })
                .catch(error => {
                    console.error('Error:', error);
                    Toastify({
                        text: "Failed to update dashboard widgets!",
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "left",
                        style: {
                            background: "#FF5733",
                        },
                    }).showToast();
                });
        }

        // Widget category filter
        document.getElementById('widgetCategoryFilter').addEventListener('change', function() {
            const selectedCategory = this.value;
            document.querySelectorAll('#widgetsTableBody .widget-item').forEach(row => {
                const category = row.getAttribute('data-category');
                row.style.display = (selectedCategory === "" || category === selectedCategory) ? "" :
                "none";
            });
        });

        // Widget select all
        document.getElementById('widgetSelectAll').addEventListener('click', function() {
            const visibleRows = Array.from(document.querySelectorAll('#widgetsTableBody .widget-item'))
                .filter(row => row.style.display !== 'none');

            visibleRows.forEach(row => {
                const checkbox = row.querySelector('.widget-checkbox');
                if (checkbox) {
                    checkbox.checked = true;
                    updateWidgetBadgeStatus(checkbox);
                }
            });
        });

        // Widget deselect all
        document.getElementById('widgetDeselectAll').addEventListener('click', function() {
            const visibleRows = Array.from(document.querySelectorAll('#widgetsTableBody .widget-item'))
                .filter(row => row.style.display !== 'none');

            visibleRows.forEach(row => {
                const checkbox = row.querySelector('.widget-checkbox');
                if (checkbox) {
                    checkbox.checked = false;
                    updateWidgetBadgeStatus(checkbox);
                }
            });
        });

        // Widget checkbox change handler
        document.querySelectorAll('.widget-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateWidgetBadgeStatus(this);
            });
        });

        function updateWidgetBadgeStatus(checkbox) {
            const row = checkbox.closest('tr');
            const badge = row.querySelector('.widget-status-badge');

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
    </script>
</x-app-layout>

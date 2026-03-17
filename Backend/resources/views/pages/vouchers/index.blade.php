<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1
                class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-indigo-500 dark:from-blue-400 dark:to-indigo-400">
                {{ __('ui.master_vouchers') }}
            </h1>
            <div class="mt-4 md:mt-0">
                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200"
                    type="button" onclick="openCreateModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ __('ui.add_voucher') }}
                </button>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <!-- Search Input -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('ui.search_voucher') }}
                    </label>
                    <input type="text" id="search" placeholder="{{ __('ui.search_voucher_placeholder') }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('ui.status') }}
                    </label>
                    <select id="status_filter"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('ui.all_status') }}</option>
                        <option value="active">{{ __('ui.active') }}</option>
                        <option value="inactive">{{ __('ui.inactive') }}</option>
                    </select>
                </div>

                <!-- Per Page Selector -->
                <div class="md:col-start-3">
                    <div class="flex items-center justify-end gap-3">
                        <label for="per_page"
                            class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                            {{ __('ui.show') }} :
                        </label>
                        <select id="per_page"
                            class="min-w-[120px] border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            <option value="8">8</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div id="vouchers-table-container">
            @include('pages.vouchers.partials.voucher_table')
        </div>
    </div>

    <!-- Voucher Modal -->
    <div id="voucherModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 hidden" style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 id="modalTitle" class="text-xl font-bold text-gray-800 dark:text-white">{{ __('ui.add_voucher') }}</h3>
                    <button type="button" onclick="closeModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="voucherForm" class="p-6">
                    <input type="hidden" id="voucher_id" name="voucher_id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.voucher_code') }} <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">{{ __('ui.voucher_code_chars') }}</span>
                            </label>
                            <input type="text" id="code" name="code" required minlength="8" maxlength="12"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white uppercase"
                                placeholder="{{ __('ui.example_code') }}"
                                oninput="this.value = this.value.toUpperCase()">
                        </div>

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.voucher_name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="{{ __('ui.example_name') }}">
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.description') }}
                            </label>
                            <textarea id="description" name="description" rows="2"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="{{ __('ui.example_discount_desc') }}"></textarea>
                        </div>

                        <!-- Discount Percentage -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.discount_percent') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="discount_percentage" name="discount_percentage" required
                                min="0" max="100" step="0.01"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="10">
                        </div>

                        <!-- Max Discount Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.max_discount') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="max_discount_amount" name="max_discount_amount" required
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white currency-input"
                                placeholder="100.000">
                            <input type="hidden" id="max_discount_amount_raw" name="max_discount_amount_raw">
                        </div>

                        <!-- Max Total Usage -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.max_total_usage') }} <span class="text-gray-500 text-xs">{{ __('ui.unlimited') }}</span>
                            </label>
                            <input type="number" id="max_total_usage" name="max_total_usage" min="0"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="10" value="0">
                        </div>

                        <!-- Max Usage Per User -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.max_usage_per_user') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="max_usage_per_user" name="max_usage_per_user" required
                                min="1"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="1" value="1">
                        </div>

                        <!-- Date Range Picker -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.valid_period') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="date_range" name="date_range" required
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="{{ __('ui.select_valid_date') }}" readonly>
                            <input type="hidden" id="valid_from" name="valid_from">
                            <input type="hidden" id="valid_to" name="valid_to">
                        </div>

                        <!-- Min Transaction Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.min_transaction') }}
                            </label>
                            <input type="text" id="min_transaction_amount" name="min_transaction_amount"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white currency-input"
                                placeholder="500.000" value="0">
                            <input type="hidden" id="min_transaction_amount_raw" name="min_transaction_amount_raw">
                        </div>

                        <!-- Scope Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.scope_type') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="scope_type" name="scope_type" required onchange="togglePropertySelector()"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="global">{{ __('ui.global') }}</option>
                                <option value="property">{{ __('ui.property') }}</option>
                            </select>
                        </div>

                        <!-- Property Selector (shown when scope_type is property) -->
                        <div id="property_selector_container" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.select_property') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="property_id" name="property_id"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">{{ __('ui.select_property_option') }}</option>
                                @php
                                    $user = auth()->user();
                                    $isSiteUser = $user->isSiteRole() || $user->isSite();
                                @endphp
                                @if($isSiteUser && $user->property_id)
                                    @php
                                        $userProperty = $properties->firstWhere('idrec', $user->property_id);
                                    @endphp
                                    @if($userProperty)
                                        <option value="{{ $userProperty->idrec }}" selected>{{ $userProperty->name }}</option>
                                    @endif
                                @else
                                    @foreach($properties as $property)
                                        <option value="{{ $property->idrec }}">{{ $property->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Status Toggle (Only visible on edit) -->
                        <div id="status_toggle_container" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status
                            </label>
                            <div class="flex items-center space-x-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="status_toggle" class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                </label>
                                <span id="status_label"
                                    class="text-sm font-medium text-gray-900 dark:text-white">Active</span>
                            </div>
                        </div>

                        <!-- Status Hidden Input -->
                        <input type="hidden" id="status" name="status" value="active">
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ __('ui.cancel') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                            {{ __('ui.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Translations object for JavaScript
            const voucherTranslations = {
                addVoucher: '{{ __('ui.add_voucher') }}',
                editVoucher: '{{ __('ui.edit_voucher') }}',
                confirmDelete: '{{ __('ui.confirm_delete') }}',
                confirmDeleteVoucher: '{{ __('ui.confirm_delete_voucher') }}',
                yesDelete: '{{ __('ui.yes_delete') }}',
                cancel: '{{ __('ui.cancel') }}',
                confirm: '{{ __('ui.confirm_action') }}',
                yesChange: '{{ __('ui.yes_change') }}',
                activate: '{{ __('ui.activate_voucher') }}',
                deactivate: '{{ __('ui.deactivate_voucher') }}',
                failedFetchVoucher: '{{ __('ui.failed_fetch_voucher') }}',
                failedSaveVoucher: '{{ __('ui.failed_save_voucher') }}',
                failedDeleteVoucher: '{{ __('ui.failed_delete_voucher') }}',
                failedChangeStatus: '{{ __('ui.failed_change_status') }}',
                statusChangedSuccess: '{{ __('ui.status_changed_success') }}',
                validityPeriodSelected: '{{ __('ui.validity_period_selected') }}',
                today: '{{ __('ui.today') }}',
                tomorrow: '{{ __('ui.tomorrow') }}',
                next7Days: '{{ __('ui.next_7_days') }}',
                next14Days: '{{ __('ui.next_14_days') }}',
                next1Month: '{{ __('ui.next_1_month') }}',
                next3Months: '{{ __('ui.next_3_months') }}',
                next6Months: '{{ __('ui.next_6_months') }}',
                next1Year: '{{ __('ui.next_1_year') }}',
                apply: '{{ __('ui.apply') }}',
                fromLabel: '{{ __('ui.from_label') }}',
                toLabel: '{{ __('ui.to_label') }}',
                chooseDate: '{{ __('ui.choose_date') }}'
            };

            // Toast notification helper
            function showToast(message, type = 'success') {
                const bgColor = type === 'success' ? 'linear-gradient(to right, #00b09b, #96c93d)' :
                    type === 'error' ? 'linear-gradient(to right, #ff5f6d, #ffc371)' :
                    type === 'info' ? 'linear-gradient(to right, #4facfe, #00f2fe)' :
                    'linear-gradient(to right, #f857a6, #ff5858)';

                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: bgColor,
                    },
                    stopOnFocus: true,
                    close: true,
                }).showToast();
            }

            // Initialize date range picker
            function initDateRangePicker() {
                // Check if daterangepicker is available
                if (typeof $.fn.daterangepicker === 'undefined') {
                    console.error('Daterangepicker library not loaded');
                    console.log('Available jQuery plugins:', Object.keys($.fn));
                    return false;
                }

                if (typeof moment === 'undefined') {
                    console.error('Moment.js library not loaded');
                    return false;
                }

                try {
                    $('#date_range').daterangepicker({
                        timePicker: true,
                        timePicker24Hour: true,
                        timePickerIncrement: 30,
                        startDate: moment().startOf('day'),
                        endDate: moment().add(7, 'days').endOf('day'),
                        minDate: moment(),
                        showDropdowns: true,
                        autoApply: false,
                        linkedCalendars: true,
                        showCustomRangeLabel: true,
                        alwaysShowCalendars: true,
                        opens: 'center',
                        drops: 'auto',
                        ranges: {
                            [voucherTranslations.today]: [moment().startOf('day'), moment().endOf('day')],
                            [voucherTranslations.tomorrow]: [moment().add(1, 'days').startOf('day'), moment().add(1, 'days').endOf('day')],
                            [voucherTranslations.next7Days]: [moment().startOf('day'), moment().add(6, 'days').endOf('day')],
                            [voucherTranslations.next14Days]: [moment().startOf('day'), moment().add(13, 'days').endOf('day')],
                            [voucherTranslations.next1Month]: [moment().startOf('day'), moment().add(1, 'months').endOf('day')],
                            [voucherTranslations.next3Months]: [moment().startOf('day'), moment().add(3, 'months').endOf('day')],
                            [voucherTranslations.next6Months]: [moment().startOf('day'), moment().add(6, 'months').endOf('day')],
                            [voucherTranslations.next1Year]: [moment().startOf('day'), moment().add(1, 'years').endOf('day')],
                        },
                        locale: {
                            format: 'DD/MM/YYYY HH:mm',
                            separator: ' - ',
                            applyLabel: voucherTranslations.apply,
                            cancelLabel: voucherTranslations.cancel,
                            fromLabel: voucherTranslations.fromLabel,
                            toLabel: voucherTranslations.toLabel,
                            customRangeLabel: voucherTranslations.chooseDate,
                            weekLabel: 'M',
                            daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                                'September', 'Oktober', 'November', 'Desember'
                            ],
                            firstDay: 1
                        },
                        autoUpdateInput: false
                    }, function(start, end, label) {
                        console.log('Periode dipilih: ' + start.format('DD/MM/YYYY HH:mm') + ' sampai ' + end.format(
                            'DD/MM/YYYY HH:mm'));
                    });

                    $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                        $(this).val(picker.startDate.format('DD/MM/YYYY HH:mm') + ' - ' + picker.endDate.format(
                            'DD/MM/YYYY HH:mm'));
                        $('#valid_from').val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
                        $('#valid_to').val(picker.endDate.format('YYYY-MM-DD HH:mm:ss'));
                        showToast(voucherTranslations.validityPeriodSelected, 'info');
                    });

                    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                        $(this).val('');
                        $('#valid_from').val('');
                        $('#valid_to').val('');
                    });

                    // Show picker on click
                    $('#date_range').on('click', function() {
                        $(this).data('daterangepicker').show();
                    });

                    console.log('Daterangepicker initialized successfully');
                    return true;
                } catch (error) {
                    console.error('Error initializing daterangepicker:', error);
                    return false;
                }
            }

            // Format number with thousand separator (dot)
            function formatNumber(num) {
                if (num === null || num === undefined || num === '') return '';
                // Parse as float first to handle decimal values from server (e.g., "500000.00")
                const parsed = parseFloat(String(num));
                if (isNaN(parsed)) return '';
                // Round to integer then format with dots as thousand separators
                const cleanNum = String(Math.round(parsed));
                return cleanNum.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Parse formatted number back to raw number
            function parseNumber(formattedNum) {
                if (formattedNum === null || formattedNum === undefined || formattedNum === '') return 0;
                // Remove all dots and return as integer
                return parseInt(String(formattedNum).replace(/\./g, ''), 10) || 0;
            }

            // Initialize immediately since scripts are loaded in correct order now
            $(document).ready(function() {
                initDateRangePicker();

                // Currency input formatting
                $('.currency-input').on('input', function() {
                    const cursorPos = this.selectionStart;
                    const oldLength = $(this).val().length;
                    const rawValue = parseNumber($(this).val());
                    const formattedValue = formatNumber(rawValue);
                    $(this).val(formattedValue);

                    // Adjust cursor position after formatting
                    const newLength = formattedValue.length;
                    const diff = newLength - oldLength;
                    this.setSelectionRange(cursorPos + diff, cursorPos + diff);
                });

                // Status toggle handler
                $('#status_toggle').on('change', function() {
                    const isActive = $(this).is(':checked');
                    const status = isActive ? 'active' : 'inactive';
                    $('#status').val(status);
                    $('#status_label').text(isActive ? 'Active' : 'Inactive');
                    $('#status_label').toggleClass('text-blue-600', isActive);
                    $('#status_label').toggleClass('text-red-600', !isActive);
                });
            });

            // Toggle property selector based on scope type
            function togglePropertySelector() {
                const scopeType = $('#scope_type').val();
                if (scopeType === 'property') {
                    $('#property_selector_container').show();
                    $('#property_id').prop('required', true);
                } else {
                    $('#property_selector_container').hide();
                    $('#property_id').prop('required', false);
                    $('#property_id').val('');
                }
            }

            // Filter functionality
            let debounceTimer;
            $('#search, #status_filter, #per_page').on('change keyup', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(filterVouchers, 300);
            });

            function filterVouchers() {
                const search = $('#search').val();
                const status = $('#status_filter').val();
                const perPage = $('#per_page').val();

                $.ajax({
                    url: '{{ route('vouchers.filter') }}',
                    method: 'GET',
                    data: {
                        search: search,
                        status: status,
                        per_page: perPage
                    },
                    success: function(response) {
                        $('#vouchers-table-container').html(response.html);
                    },
                    error: function(xhr) {
                        console.error('Filter error:', xhr);
                    }
                });
            }

            // Reload table only without full page refresh
            function reloadTable() {
                filterVouchers();
            }

            // Modal functions
            function openCreateModal() {
                $('#modalTitle').text(voucherTranslations.addVoucher);
                $('#voucherForm')[0].reset();
                $('#voucher_id').val('');
                $('#date_range').val('');
                $('#valid_from').val('');
                $('#valid_to').val('');
                $('#status').val('active'); // Set default to active
                $('#status_toggle_container').hide(); // Hide status toggle on create
                $('#scope_type').val('global'); // Reset scope type to global
                $('#property_selector_container').hide(); // Hide property selector on create
                $('#property_id').val('');
                // Enable fields that are disabled in edit mode
                $('#code').prop('disabled', false).removeClass('bg-gray-100 cursor-not-allowed opacity-60');
                $('#scope_type').prop('disabled', false).removeClass('bg-gray-100 cursor-not-allowed opacity-60');
                $('#voucherModal').removeClass('hidden').show();
            }

            function openEditModal(id) {
                $.ajax({
                    url: `/vouchers/${id}`,
                    method: 'GET',
                    success: function(response) {
                        const voucher = response.data;
                        $('#modalTitle').text(voucherTranslations.editVoucher);
                        $('#voucher_id').val(voucher.idrec);
                        $('#code').val(voucher.code);
                        $('#name').val(voucher.name);
                        $('#description').val(voucher.description);
                        $('#discount_percentage').val(voucher.discount_percentage);
                        $('#max_discount_amount').val(formatNumber(voucher.max_discount_amount));
                        $('#max_total_usage').val(voucher.max_total_usage);
                        $('#max_usage_per_user').val(voucher.max_usage_per_user);

                        // Set date range picker values
                        const startDate = moment(voucher.valid_from);
                        const endDate = moment(voucher.valid_to);
                        $('#date_range').data('daterangepicker').setStartDate(startDate);
                        $('#date_range').data('daterangepicker').setEndDate(endDate);
                        $('#date_range').val(startDate.format('DD/MM/YYYY HH:mm') + ' - ' + endDate.format(
                            'DD/MM/YYYY HH:mm'));
                        $('#valid_from').val(voucher.valid_from);
                        $('#valid_to').val(voucher.valid_to);

                        $('#min_transaction_amount').val(formatNumber(voucher.min_transaction_amount));
                        $('#scope_type').val(voucher.scope_type);

                        if (voucher.scope_type === 'property') {
                            $('#property_selector_container').show();
                            $('#property_id').val(voucher.property_id);
                        } else {
                            $('#property_selector_container').hide();
                            $('#property_id').val('');
                        }

                        // Set status toggle
                        $('#status').val(voucher.status);
                        const isActive = voucher.status === 'active';
                        $('#status_toggle').prop('checked', isActive);
                        $('#status_label').text(isActive ? 'Active' : 'Inactive');
                        $('#status_label').removeClass('text-blue-600 text-red-600');
                        $('#status_label').addClass(isActive ? 'text-blue-600' : 'text-red-600');
                        $('#status_toggle_container').show(); // Show status toggle on edit

                        // Disable voucher code & scope type on edit
                        $('#code').prop('disabled', true).addClass('bg-gray-100 cursor-not-allowed opacity-60');
                        $('#scope_type').prop('disabled', true).addClass('bg-gray-100 cursor-not-allowed opacity-60');

                        $('#voucherModal').removeClass('hidden').show();
                    },
                    error: function(xhr) {
                        showToast(voucherTranslations.failedFetchVoucher, 'error');
                    }
                });
            }

            function closeModal() {
                $('#voucherModal').addClass('hidden').hide();
            }

            // Form submission
            $('#voucherForm').on('submit', function(e) {
                e.preventDefault();
                const voucherId = $('#voucher_id').val();
                const url = voucherId ? `/vouchers/${voucherId}` : '{{ route('vouchers.store') }}';
                const method = voucherId ? 'PUT' : 'POST';

                const formData = {
                    code: $('#code').val(),
                    name: $('#name').val(),
                    description: $('#description').val(),
                    discount_percentage: $('#discount_percentage').val(),
                    max_discount_amount: parseNumber($('#max_discount_amount').val()),
                    max_total_usage: $('#max_total_usage').val(),
                    max_usage_per_user: $('#max_usage_per_user').val(),
                    valid_from: $('#valid_from').val(),
                    valid_to: $('#valid_to').val(),
                    min_transaction_amount: parseNumber($('#min_transaction_amount').val()),
                    scope_type: $('#scope_type').val(),
                    property_id: $('#property_id').val(),
                    status: $('#status').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            closeModal();
                            showToast(response.message, 'success');
                            setTimeout(() => reloadTable(), 500);
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || voucherTranslations.failedSaveVoucher;
                        showToast(errorMsg, 'error');

                        // Show validation errors if any
                        if (xhr.responseJSON?.errors) {
                            Object.values(xhr.responseJSON.errors).forEach(errors => {
                                errors.forEach(error => showToast(error, 'error'));
                            });
                        }
                    }
                });
            });

            // Delete voucher
            function deleteVoucher(id) {
                Swal.fire({
                    title: voucherTranslations.confirmDelete,
                    text: voucherTranslations.confirmDeleteVoucher,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: voucherTranslations.yesDelete,
                    cancelButtonText: voucherTranslations.cancel
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/vouchers/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    showToast(response.message, 'success');
                                    setTimeout(() => reloadTable(), 500);
                                }
                            },
                            error: function(xhr) {
                                showToast(voucherTranslations.failedDeleteVoucher, 'error');
                            }
                        });
                    }
                });
            }

            // Toggle status
            function toggleStatus(id, currentStatus) {
                const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                const statusText = newStatus === 'active' ? voucherTranslations.activate : voucherTranslations.deactivate;

                Swal.fire({
                    title: voucherTranslations.confirm,
                    text: `{{ __('ui.confirm_status_change') }}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: voucherTranslations.yesChange,
                    cancelButtonText: voucherTranslations.cancel
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('vouchers.toggle-status') }}',
                            method: 'POST',
                            data: {
                                id: id,
                                status: newStatus,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    showToast(response.message || voucherTranslations.statusChangedSuccess, 'success');
                                    setTimeout(() => reloadTable(), 500);
                                }
                            },
                            error: function(xhr) {
                                showToast(voucherTranslations.failedChangeStatus, 'error');
                            }
                        });
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>

<!-- Modal untuk memilih user yang check-in -->
{{-- Modal backdrop: increased opacity for better contrast in dark mode --}}
<div id="checkedInUsersModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border border-gray-200 dark:border-gray-600 w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ __('ui.chat_select_user_title') }}
            </h3>
            <button onclick="closeCheckedInModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="mt-4">
            <!-- Loading State -->
            <div id="modalLoading" class="text-center py-8">
                <svg class="animate-spin h-10 w-10 mx-auto text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Loading...</p>
            </div>

            <!-- User List -->
            <div id="checkedInUsersList" class="hidden">
                <!-- Search Box -->
                <div class="mb-4">
                    <input type="text" id="modalSearch" placeholder="{{ __('ui.chat_search_modal_placeholder') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Users Table -->
                <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    User Info
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Room
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Period
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            <!-- Dynamic content will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="hidden text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="mt-2 text-gray-600">{{ __('ui.chat_no_users_available') }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ __('ui.chat_users_hint') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var checkedInUsersData = [];

// Open modal and load data
async function openCheckedInModal() {
    const modal = document.getElementById('checkedInUsersModal');
    const loading = document.getElementById('modalLoading');
    const usersList = document.getElementById('checkedInUsersList');

    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    usersList.classList.add('hidden');

    try {
        const response = await fetch('{{ route('chat.checked-in-users') }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            checkedInUsersData = data.data;
            renderCheckedInUsers(checkedInUsersData);
            loading.classList.add('hidden');
            usersList.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error loading checked-in users:', error);
        alert('{{ __('ui.chat_failed_load_users') }}');
        closeCheckedInModal();
    }
}

// Close modal
function closeCheckedInModal() {
    document.getElementById('checkedInUsersModal').classList.add('hidden');
    document.getElementById('modalSearch').value = '';
    checkedInUsersData = [];
}

// Render users in table
function renderCheckedInUsers(users) {
    const tbody = document.getElementById('usersTableBody');
    const emptyState = document.getElementById('emptyState');

    if (!users || users.length === 0) {
        tbody.innerHTML = '';
        emptyState.classList.remove('hidden');
        return;
    }

    emptyState.classList.add('hidden');

    tbody.innerHTML = users.map(user => {
        // Format period display
        let periodDisplay = user.check_in_at;
        if (user.check_out_at) {
            periodDisplay = `
                <p class="text-sm text-gray-900">${user.check_in_at}</p>
                <p class="text-xs text-gray-500">to ${user.check_out_at}</p>
            `;
        } else {
            periodDisplay = `
                <p class="text-sm text-gray-900">${user.check_in_at}</p>
                <p class="text-xs text-gray-500">- ongoing</p>
            `;
        }

        // Status badge for display
        let statusBadge = user.status === 'departed'
            ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">Departed</span>`
            : `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Checked-In</span>`;

        // Action button - different for existing conversation vs new
        let actionButton = '';
        if (user.has_conversation) {
            actionButton = `
                <button onclick="handleUserRowClick('${user.order_id}', true)" type="button"
                    class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    Chat
                </button>
            `;
        } else {
            actionButton = `
                <button onclick="handleUserRowClick('${user.order_id}', false)" type="button"
                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Start Chat
                </button>
            `;
        }

        return `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold">
                        ${user.user_name ? user.user_name.charAt(0).toUpperCase() : 'U'}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">${user.user_name || 'N/A'}</p>
                        <p class="text-sm text-gray-500">${user.user_email || 'N/A'}</p>
                        <p class="text-xs text-gray-400">Order: ${user.order_id}</p>
                        ${statusBadge}
                    </div>
                </div>
            </td>
            <td class="px-4 py-3">
                <p class="text-sm text-gray-900">${user.room_name}</p>
                <p class="text-xs text-gray-500">${user.property_name}</p>
            </td>
            <td class="px-4 py-3">
                ${periodDisplay}
            </td>
            <td class="px-4 py-3 text-center">
                ${actionButton}
            </td>
        </tr>
    `}).join('');
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const modalSearch = document.getElementById('modalSearch');
    if (modalSearch) {
        modalSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filtered = checkedInUsersData.filter(user =>
                (user.user_name && user.user_name.toLowerCase().includes(searchTerm)) ||
                (user.user_email && user.user_email.toLowerCase().includes(searchTerm)) ||
                (user.order_id && user.order_id.toLowerCase().includes(searchTerm)) ||
                (user.room_name && user.room_name.toLowerCase().includes(searchTerm)) ||
                (user.status && user.status.toLowerCase().includes(searchTerm))
            );
            renderCheckedInUsers(filtered);
        });
    }
});

// Handle row click - create or open chat
async function handleUserRowClick(orderId, hasConversation) {
    if (hasConversation) {
        // Find and open existing conversation
        await openExistingConversation(orderId);
    } else {
        // Create new conversation
        await createChatForUser(orderId);
    }
}

// Open existing conversation
async function openExistingConversation(orderId) {
    try {
        // Find conversation ID by order_id
        const response = await fetch('{{ route('chat.find-by-order') }}?order_id=' + orderId, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success && data.conversation_id) {
            closeCheckedInModal();
            // Open chat directly using the chatManager
            if (window.openChat) {
                window.openChat(data.conversation_id);
            }
        } else {
            alert('{{ __('ui.chat_conversation_not_found') }}');
        }
    } catch (error) {
        console.error('Error opening conversation:', error);
        alert('{{ __('ui.chat_failed_open') }}');
    }
}

// Create chat for selected user
async function createChatForUser(orderId) {
    // Show SweetAlert confirmation
    const result = await Swal.fire({
        title: '{{ __('ui.chat_start_confirm_title') }}',
        text: '{{ __('ui.chat_start_confirm_text') }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '{{ __('ui.chat_yes_start') }}',
        cancelButtonText: '{{ __('ui.cancel') }}'
    });

    if (!result.isConfirmed) {
        return;
    }

    // Show loading
    Swal.fire({
        title: '{{ __('ui.chat_creating') }}',
        text: '{{ __('ui.chat_please_wait') }}',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await fetch('{{ route('chat.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                order_id: orderId
            })
        });

        const data = await response.json();

        if (data.success && data.conversation_id) {
            // Close the loading alert
            Swal.close();

            // Close modal
            closeCheckedInModal();

            // Open chat directly using the chatManager
            if (window.openChat) {
                window.openChat(data.conversation_id);
            }
        } else {
            Swal.fire({
                title: '{{ __('ui.failed') }}',
                text: data.message || '{{ __('ui.chat_create_failed') }}',
                icon: 'error',
                confirmButtonColor: '#3b82f6'
            });
        }
    } catch (error) {
        console.error('Error creating conversation:', error);
        Swal.fire({
            title: 'Error!',
            text: '{{ __('ui.chat_error_occurred') }}',
            icon: 'error',
            confirmButtonColor: '#3b82f6'
        });
    }
}

// Close modal when clicking outside
document.getElementById('checkedInUsersModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeCheckedInModal();
    }
});
</script>

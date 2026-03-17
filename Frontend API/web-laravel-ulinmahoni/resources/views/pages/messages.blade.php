<x-app-layout>
    <div class="relative flex" x-data="{ msgSidebarOpen: true }" x-init="() => { $refs.contentarea.scrollTop = 99999999 }">

        <!-- Messages sidebar -->
        <x-messages.messages-sidebar />

        <!-- Messages body -->
        <x-messages.messages-body />

    </div>
</x-app-layout>

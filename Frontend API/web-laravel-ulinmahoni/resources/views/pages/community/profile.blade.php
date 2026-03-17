<x-app-layout background="bg-white">
    <div class="relative flex" x-data="{ profileSidebarOpen: false }">

        <!-- Profile sidebar -->
        <x-community.profile-sidebar />

        <!-- Profile body -->
        <x-community.profile-body />

    </div>
</x-app-layout>

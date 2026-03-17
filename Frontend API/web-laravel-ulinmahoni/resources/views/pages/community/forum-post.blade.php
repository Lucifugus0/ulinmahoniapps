<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 md:py-0 w-full max-w-9xl mx-auto">

        <div class="xl:flex">

            <!-- Left + Middle content -->
            <div class="md:flex flex-1">

                <!-- Left content -->
                <x-community.forum-left-content />

                <!-- Middle content -->
                <div class="flex-1 md:ml-8 xl:mx-4 2xl:mx-8">
                    <div class="md:py-8">

                        <!-- Forum entry -->
                        <x-community.forum-entry />

                    </div>
                </div>

            </div>

            <!-- Right content -->
            <x-community.forum-post-right-content />

        </div>

    </div>
</x-app-layout>

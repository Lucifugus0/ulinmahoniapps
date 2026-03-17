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

                        <!-- Buttons group -->
                        <div class="mb-4">
                            <div class="w-full flex flex-wrap -space-x-px">
                                <button class="btn grow bg-white border-slate-200 text-indigo-500 rounded-none first:rounded-l last:rounded-r">Popular</button>
                                <button class="btn grow bg-white border-slate-200 hover:bg-slate-50 text-slate-600 rounded-none first:rounded-l last:rounded-r">Newest</button>
                                <button class="btn grow bg-white border-slate-200 hover:bg-slate-50 text-slate-600 rounded-none first:rounded-l last:rounded-r">Following</button>
                            </div>
                        </div>

                        <!-- Forum entries -->
                        <div class="space-y-2">
                            <x-community.forum-entries />
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 text-right">
                            <nav class="inline-flex" role="navigation" aria-label="Navigation">
                                <ul class="flex justify-center">
                                    <li class="ml-3 first:ml-0">
                                        <a class="btn bg-white border-slate-200 text-slate-300 cursor-not-allowed" href="#0" disabled>&lt;- Previous</a>
                                    </li>
                                    <li class="ml-3 first:ml-0">
                                        <a class="btn bg-white border-slate-200 hover:border-slate-300 text-indigo-500" href="#0">Next -&gt;</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Right content -->
            <x-community.forum-right-content />

        </div>

    </div>
</x-app-layout>

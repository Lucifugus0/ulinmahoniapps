<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-5">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Fintech ✨</h1>
            </div>

            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">

                <!-- Datepicker built with flatpickr -->
                <x-datepicker />

                <!-- Add account button -->
                <button class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"/>
                    </svg>
                    <span class="hidden xs:block ml-2">Add Account</span>
                </button>

            </div>

        </div>

        <!-- Cards -->
        <div class="grid grid-cols-12 gap-6">

            <!-- Page Intro -->                     
            <x-fintech.fintech-intro />

            <!-- Line chart (Portfolio Returns) -->
            <x-fintech.fintech-card-01 />

            <!-- Credit Card -->
            <x-fintech.fintech-card-02 />

            <!-- Bar chart (Cash Flow) -->
            <x-fintech.fintech-card-03 />

            <!-- Horizontal bar chart (Cash Flow by Account) -->
            <x-fintech.fintech-card-04 />

            <!-- Table (Recent Expenses) -->
            <x-fintech.fintech-card-05 />

            <!-- Table (Recent Earnings) -->
            <x-fintech.fintech-card-06 />

            <!-- Line chart (Saving Goals) -->
            <x-fintech.fintech-card-07 />

            <!-- Line chart (Growth Portfolio) -->
            <x-fintech.fintech-card-08 />

            <!-- Pie chart (Portfolio Value) -->
            <x-fintech.fintech-card-09 />

            <!-- Line charts (Stock graphs) -->
            <x-fintech.fintech-card-10 />
            <x-fintech.fintech-card-11 />
            <x-fintech.fintech-card-12 />
            <x-fintech.fintech-card-13 />

            <!-- Table (Market Trends) -->
            <x-fintech.fintech-card-14 />

        </div>

    </div>
</x-app-layout>

<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        
        <!-- Welcome banner -->
        {{-- <x-dashboard.welcome-banner /> --}}

        <div class="relative bg-indigo-200 p-4 sm:p-6 rounded-sm overflow-hidden mb-8">

            <!-- Background illustration -->
            <div class="absolute right-0 top-0 -mt-4 mr-16 pointer-events-none hidden xl:block" aria-hidden="true">
                <svg width="319" height="198" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs>
                        <path id="welcome-a" d="M64 0l64 128-64-20-64 20z" />
                        <path id="welcome-e" d="M40 0l40 80-40-12.5L0 80z" />
                        <path id="welcome-g" d="M40 0l40 80-40-12.5L0 80z" />
                        <linearGradient x1="50%" y1="0%" x2="50%" y2="100%" id="welcome-b">
                            <stop stop-color="#A5B4FC" offset="0%" />
                            <stop stop-color="#818CF8" offset="100%" />
                        </linearGradient>
                        <linearGradient x1="50%" y1="24.537%" x2="50%" y2="100%" id="welcome-c">
                            <stop stop-color="#4338CA" offset="0%" />
                            <stop stop-color="#6366F1" stop-opacity="0" offset="100%" />
                        </linearGradient>
                    </defs>
                    <g fill="none" fill-rule="evenodd">
                        <g transform="rotate(64 36.592 105.604)">
                            <mask id="welcome-d" fill="#fff">
                                <use xlink:href="#welcome-a" />
                            </mask>
                            <use fill="url(#welcome-b)" xlink:href="#welcome-a" />
                            <path fill="url(#welcome-c)" mask="url(#welcome-d)" d="M64-24h80v152H64z" />
                        </g>
                        <g transform="rotate(-51 91.324 -105.372)">
                            <mask id="welcome-f" fill="#fff">
                                <use xlink:href="#welcome-e" />
                            </mask>
                            <use fill="url(#welcome-b)" xlink:href="#welcome-e" />
                            <path fill="url(#welcome-c)" mask="url(#welcome-f)" d="M40.333-15.147h50v95h-50z" />
                        </g>
                        <g transform="rotate(44 61.546 392.623)">
                            <mask id="welcome-h" fill="#fff">
                                <use xlink:href="#welcome-g" />
                            </mask>
                            <use fill="url(#welcome-b)" xlink:href="#welcome-g" />
                            <path fill="url(#welcome-c)" mask="url(#welcome-h)" d="M40.333-15.147h50v95h-50z" />
                        </g>
                    </g>
                </svg>
            </div>
        
            <!-- Content -->
            <div class="relative">
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold mb-1">Welcome to {{ $CRM_ISS->nilai }}, {{ Auth::user()->username }} </h1>
                <p>Responsibility to build Manpower</p>
            </div>
        
        </div>

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Avatars -->
            {{-- <x-dashboard.dashboard-avatars /> --}}
            <label class="bg-slate-100"></label>

            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">

                <!-- Filter button -->
                {{-- <x-dropdown-filter align="left" /> --}}

                <!-- Datepicker built with flatpickr -->
                {{-- <x-datepicker /> --}}
                {{-- <form class="flex items-center mb-3" id="form-filter">
                    <label class="block text-sm font-medium text-lg mb-1" for="form-search">Select Year For Data Chart :</label>
                    <div class="relative ml-2 w-3/4 md:w-1/4">
                        <select id="form-search" name = "year" class="form-input w-60">
                            <option value="2020" {{ date('Y') == '2020' ? 'selected' : '' }}>2020</option>
                            <option value="2021" {{ date('Y') == '2021' ? 'selected' : '' }}>2021</option>
                            <option value="2022" {{ date('Y') == '2022' ? 'selected' : '' }}>2022</option>
                            <option value="2023" {{ date('Y') == '2023' ? 'selected' : '' }}>2023</option>
                            <option value="2024" {{ date('Y') == '2024' ? 'selected' : '' }}>2024</option>
                            <option value="2025" {{ date('Y') == '2025' ? 'selected' : '' }}>2025</option>
                            <option value="2026" {{ date('Y') == '2026' ? 'selected' : '' }}>2026</option>
                            <option value="2027" {{ date('Y') == '2027' ? 'selected' : '' }}>2027</option>
                            <option value="2028" {{ date('Y') == '2028' ? 'selected' : '' }}>2028</option>
                            <option value="2029" {{ date('Y') == '2029' ? 'selected' : '' }}>2029</option>
                            <option value="2030" {{ date('Y') == '2030' ? 'selected' : '' }}>2030</option>
                            <option value="2031" {{ date('Y') == '2031' ? 'selected' : '' }}>2031</option>
                            <option value="2032" {{ date('Y') == '2032' ? 'selected' : '' }}>2032</option>
                            <option value="2033" {{ date('Y') == '2033' ? 'selected' : '' }}>2033</option>
                            <option value="2034" {{ date('Y') == '2034' ? 'selected' : '' }}>2034</option>
                            <option value="2035" {{ date('Y') == '2035' ? 'selected' : '' }}>2035</option>
                        </select>
                    </div>
                </form> --}}
                

                <!-- Add view button -->
                {{-- <button class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    <span class="hidden xs:block ml-2">Add View</span>
                </button> --}}
                
            </div>

        </div>
        
        <!-- Cards -->
        <div class="grid grid-cols-12 gap-6">
            <!-- Bar chart (Direct vs Indirect) -->
            {{-- <x-dashboard.dashboard-card-04 /> --}}

            {{-- <div class="flex flex-col col-span-full sm:col-span-6 bg-white shadow-lg rounded-sm border border-slate-200">
                <header class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Direct VS Indirect</h2>
                </header>
                <div id="dashboard-card-04-legend" class="px-5 py-3">
                    <ul class="flex flex-wrap"></ul>
                </div>
                <div class="grow">
                    <canvas id="dashboard-card-04" width="595" height="248"></canvas>
                </div>
            </div> --}}
            {{-- @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102' || Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203')
            <div class="col-span-full xl:col-span-8 bg-white shadow-lg rounded-sm border border-slate-200 test-1">
                @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102')
                    <header class="px-5 py-4 border-b border-slate-100">
                        <h2 class="font-semibold text-slate-800">Global Sales Achievement</h2>
                    </header>
                @endif
                @if (Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203')
                    <header class="px-5 py-4 border-b border-slate-100">
                        <h2 class="font-semibold text-slate-800">Sales Achievement</h2>
                    </header>
                @endif
                @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102')
                    <div class="flex justify-between flex-col md:flex-row p-3">
                        <label class="flex flex-row text-xs">
                            <p class="flex flex-row text-slate-800 mt-1 text-sm" for="year1">Year :</p>
                                <select id="year1" class="year1 flex flex-row text-xs ml-3" name="year1">
                                    <option value="2020" {{ date('Y') == '2020' ? 'selected' : '' }}>2020</option>
                                    <option value="2021" {{ date('Y') == '2021' ? 'selected' : '' }}>2021</option>
                                    <option value="2022" {{ date('Y') == '2022' ? 'selected' : '' }}>2022</option>
                                    <option value="2023" {{ date('Y') == '2023' ? 'selected' : '' }}>2023</option>
                                    <option value="2024" {{ date('Y') == '2024' ? 'selected' : '' }}>2024</option>
                                    <option value="2025" {{ date('Y') == '2025' ? 'selected' : '' }}>2025</option>
                                    <option value="2026" {{ date('Y') == '2026' ? 'selected' : '' }}>2026</option>
                                    <option value="2027" {{ date('Y') == '2027' ? 'selected' : '' }}>2027</option>
                                    <option value="2028" {{ date('Y') == '2028' ? 'selected' : '' }}>2028</option>
                                    <option value="2029" {{ date('Y') == '2029' ? 'selected' : '' }}>2029</option>
                                    <option value="2030" {{ date('Y') == '2030' ? 'selected' : '' }}>2030</option>
                                    <option value="2031" {{ date('Y') == '2031' ? 'selected' : '' }}>2031</option>
                                    <option value="2032" {{ date('Y') == '2032' ? 'selected' : '' }}>2032</option>
                                    <option value="2033" {{ date('Y') == '2033' ? 'selected' : '' }}>2033</option>
                                    <option value="2034" {{ date('Y') == '2034' ? 'selected' : '' }}>2034</option>
                                    <option value="2035" {{ date('Y') == '2035' ? 'selected' : '' }}>2035</option>
                                </select>
                        </label>
                        
                        <div class="mb-2"></div>
                            
                        <a href="{{route('dashboard.achievsales')}}" target="_blank" class="btn btn-sales bg-indigo-500 hover:bg-indigo-600 text-white">
                            <span class="xl:block ml-5 mr-5">Detail</span>
                        </a>
                    </div>
                @endif
                @if (Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203')
                    <label class="flex flex-row text-xs justify-end mt-2 mr-5">
                        <p class="flex flex-row text-slate-800 mt-1 mr-3 text-sm" for="year1">Year :</p>
                            <select id="year1" class="year1 flex flex-row text-xs" name="year1">
                                <option value="2020" {{ date('Y') == '2020' ? 'selected' : '' }}>2020</option>
                                <option value="2021" {{ date('Y') == '2021' ? 'selected' : '' }}>2021</option>
                                <option value="2022" {{ date('Y') == '2022' ? 'selected' : '' }}>2022</option>
                                <option value="2023" {{ date('Y') == '2023' ? 'selected' : '' }}>2023</option>
                                <option value="2024" {{ date('Y') == '2024' ? 'selected' : '' }}>2024</option>
                                <option value="2025" {{ date('Y') == '2025' ? 'selected' : '' }}>2025</option>
                                <option value="2026" {{ date('Y') == '2026' ? 'selected' : '' }}>2026</option>
                                <option value="2027" {{ date('Y') == '2027' ? 'selected' : '' }}>2027</option>
                                <option value="2028" {{ date('Y') == '2028' ? 'selected' : '' }}>2028</option>
                                <option value="2029" {{ date('Y') == '2029' ? 'selected' : '' }}>2029</option>
                                <option value="2030" {{ date('Y') == '2030' ? 'selected' : '' }}>2030</option>
                                <option value="2031" {{ date('Y') == '2031' ? 'selected' : '' }}>2031</option>
                                <option value="2032" {{ date('Y') == '2032' ? 'selected' : '' }}>2032</option>
                                <option value="2033" {{ date('Y') == '2033' ? 'selected' : '' }}>2033</option>
                                <option value="2034" {{ date('Y') == '2034' ? 'selected' : '' }}>2034</option>
                                <option value="2035" {{ date('Y') == '2035' ? 'selected' : '' }}>2035</option>
                            </select>
                    </label>
                @endif
                @if (Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203')
                    <div class="p-3">
                        <div class="table-responsive">
                            <table id="percent1" class="table table-striped table-bordered text-xs" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Periode</th>
                                        <th class="text-center">Sales Name</th>
                                        <th class="text-center">Realized</th>
                                        <th class="text-center">Budget</th>
                                        <th class="text-center">Achievement %</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endif
                @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102')
                    <div class="p-3">
                        <div class="table-responsive">
                            <table id="globalsales" class="table table-striped table-bordered text-xs" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Month</th>
                                        <th class="text-center">Realized</th>
                                        <th class="text-center">Budget</th>
                                        <th class="text-center">Achievement %</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endif
            </div>     
            @endif --}}

            {{-- @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102' || Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203')
                <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
                    <header class="px-5 py-4 border-b border-slate-100">
                        @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102' || Auth::user()->role == '200' || Auth::user()->role == '201')
                        <h2 class="font-semibold text-slate-800">Global Sales Achievement Chart</h2>
                        @endif
                        @if (Auth::user()->role == '202' || Auth::user()->role == '203')
                        <h2 class="font-semibold text-slate-800">Sales Achievement Chart</h2>
                        @endif
                    </header>
                    <form class="flex justify-end mb-3 mt-3" id="form-achiev">
                        <label class="block text-sm font-medium text-lg mt-1" for="form-search">Year :</label>
                        <div class="relative ml-2 w-3/4 md:w-1/4">
                            <select id="achiev-search" name = "year" class="getAchiev form-input w-20">
                                <option value="2020" {{ date('Y') == '2020' ? 'selected' : '' }}>2020</option>
                                <option value="2021" {{ date('Y') == '2021' ? 'selected' : '' }}>2021</option>
                                <option value="2022" {{ date('Y') == '2022' ? 'selected' : '' }}>2022</option>
                                <option value="2023" {{ date('Y') == '2023' ? 'selected' : '' }}>2023</option>
                                <option value="2024" {{ date('Y') == '2024' ? 'selected' : '' }}>2024</option>
                                <option value="2025" {{ date('Y') == '2025' ? 'selected' : '' }}>2025</option>
                                <option value="2026" {{ date('Y') == '2026' ? 'selected' : '' }}>2026</option>
                                <option value="2027" {{ date('Y') == '2027' ? 'selected' : '' }}>2027</option>
                                <option value="2028" {{ date('Y') == '2028' ? 'selected' : '' }}>2028</option>
                                <option value="2029" {{ date('Y') == '2029' ? 'selected' : '' }}>2029</option>
                                <option value="2030" {{ date('Y') == '2030' ? 'selected' : '' }}>2030</option>
                                <option value="2031" {{ date('Y') == '2031' ? 'selected' : '' }}>2031</option>
                                <option value="2032" {{ date('Y') == '2032' ? 'selected' : '' }}>2032</option>
                                <option value="2033" {{ date('Y') == '2033' ? 'selected' : '' }}>2033</option>
                                <option value="2034" {{ date('Y') == '2034' ? 'selected' : '' }}>2034</option>
                                <option value="2035" {{ date('Y') == '2035' ? 'selected' : '' }}>2035</option>
                            </select>
                        </div>
                    </form>
                    <div id="sales-achiev-legend" class="px-5 py-3">
                        <ul class="flex flex-wrap"></ul>
                    </div>
                    <div class="grow">
                        <canvas id="sales-achiev-chart" width="300" height="100"></canvas>
                    </div>
                </div>
            @endif --}}

            {{-- @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102')
            <div class="col-span-full xl:col-span-8 bg-white shadow-lg rounded-sm border border-slate-200">
                <header class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Order By Principal</h2>
                </header>
                <div class="flex justify-between flex-col md:flex-row">
                        <label class="flex flex-row text-xs justify-start mt-2 ml-5">
                            <p class="flex flex-row text-slate-800 mt-1 mr-3 text-sm" for="year2">Year :</p>
                                <select id="year2" class="year2 flex flex-row text-xs" name="year2">
                                    <option value="2020" {{ date('Y') == '2020' ? 'selected' : '' }}>2020</option>
                                    <option value="2021" {{ date('Y') == '2021' ? 'selected' : '' }}>2021</option>
                                    <option value="2022" {{ date('Y') == '2022' ? 'selected' : '' }}>2022</option>
                                    <option value="2023" {{ date('Y') == '2023' ? 'selected' : '' }}>2023</option>
                                    <option value="2024" {{ date('Y') == '2024' ? 'selected' : '' }}>2024</option>
                                    <option value="2025" {{ date('Y') == '2025' ? 'selected' : '' }}>2025</option>
                                    <option value="2026" {{ date('Y') == '2026' ? 'selected' : '' }}>2026</option>
                                    <option value="2027" {{ date('Y') == '2027' ? 'selected' : '' }}>2027</option>
                                    <option value="2028" {{ date('Y') == '2028' ? 'selected' : '' }}>2028</option>
                                    <option value="2029" {{ date('Y') == '2029' ? 'selected' : '' }}>2029</option>
                                    <option value="2030" {{ date('Y') == '2030' ? 'selected' : '' }}>2030</option>
                                    <option value="2031" {{ date('Y') == '2031' ? 'selected' : '' }}>2031</option>
                                    <option value="2032" {{ date('Y') == '2032' ? 'selected' : '' }}>2032</option>
                                    <option value="2033" {{ date('Y') == '2033' ? 'selected' : '' }}>2033</option>
                                    <option value="2034" {{ date('Y') == '2034' ? 'selected' : '' }}>2034</option>
                                    <option value="2035" {{ date('Y') == '2035' ? 'selected' : '' }}>2035</option>
                                </select>
                        </label>
                </div>
                <div class="p-3">
                    <div class="table-responsive">
                        <table id="orderPrincipal" class="table table-striped table-bordered text-xs" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Principal</th>
                                    <th class="text-center"># Orders</th>
                                    <th class="text-center">Currency</th>
                                    <th class="text-center">Grand Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div> 
            @endif --}}

            {{-- @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102')
                <div class="col-span-full xl:col-span-8 bg-white shadow-lg rounded-sm border border-slate-200">
                    <header class="px-5 py-4 border-b border-slate-100">
                        <h2 class="font-semibold text-slate-800">Ongoing Orders</h2>
                    </header>

                    <div class="p-3">
                        <div class="overflow-x-auto">
                            <table id="ongoingOrders" class="table-auto w-full ongoingOrders">
                                <thead class="text-xs uppercase text-slate-400 bg-slate-50 rounded-sm">
                                    <tr>
                                        <th class="p-2">
                                            <div class="font-semibold text-left">Sales Orders</div>
                                        </th>
                                        <th class="p-2">
                                            <div class="font-semibold text-center">Total SO</div>
                                        </th>
                                        <th class="p-2">
                                            <div class="font-semibold text-center">Total DPP</div>
                                        </th>
                                    </tr>
                                </thead>
                                <!-- Table body -->
                                <tbody class="text-sm font-medium divide-y divide-slate-100">
                                    <!-- Row -->
                                        <tr>
                                            <td class="p-2">
                                                <div class="flex items-center">
                                                    <div class="text-slate-800"></div>
                                                </div>
                                            </td>
                                            <td class="p-2">
                                                <div class="text-center"></div>
                                            </td>
                                            <td class="p-2">
                                                <div class="text-right text-emerald-500"></div>
                                            </td>
                                        </tr>  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> 
            @endif --}}

            {{-- @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102')
            <div>
            </div>
            @endif

            @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102' || Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203')
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
                <header class="px-5 py-4 border-b border-slate-100">
                    @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102' || Auth::user()->role == '200' || Auth::user()->role == '201')
                    <h2 class="font-semibold text-slate-800">Global New Customer</h2>
                    @endif
                    @if (Auth::user()->role == '202' || Auth::user()->role == '203')
                    <h2 class="font-semibold text-slate-800">New Customer</h2>
                    @endif
                </header>
                <form class="flex justify-end mb-3 mt-3" id="form-cust">
                    <label class="block text-sm font-medium text-lg mt-1" for="form-search">Year :</label>
                    <div class="relative ml-2 w-3/4 md:w-1/4">
                        <select id="cust-search" name = "year" class="getAchiev form-input w-20">
                            <option value="2020" {{ date('Y') == '2020' ? 'selected' : '' }}>2020</option>
                            <option value="2021" {{ date('Y') == '2021' ? 'selected' : '' }}>2021</option>
                            <option value="2022" {{ date('Y') == '2022' ? 'selected' : '' }}>2022</option>
                            <option value="2023" {{ date('Y') == '2023' ? 'selected' : '' }}>2023</option>
                            <option value="2024" {{ date('Y') == '2024' ? 'selected' : '' }}>2024</option>
                            <option value="2025" {{ date('Y') == '2025' ? 'selected' : '' }}>2025</option>
                            <option value="2026" {{ date('Y') == '2026' ? 'selected' : '' }}>2026</option>
                            <option value="2027" {{ date('Y') == '2027' ? 'selected' : '' }}>2027</option>
                            <option value="2028" {{ date('Y') == '2028' ? 'selected' : '' }}>2028</option>
                            <option value="2029" {{ date('Y') == '2029' ? 'selected' : '' }}>2029</option>
                            <option value="2030" {{ date('Y') == '2030' ? 'selected' : '' }}>2030</option>
                            <option value="2031" {{ date('Y') == '2031' ? 'selected' : '' }}>2031</option>
                            <option value="2032" {{ date('Y') == '2032' ? 'selected' : '' }}>2032</option>
                            <option value="2033" {{ date('Y') == '2033' ? 'selected' : '' }}>2033</option>
                            <option value="2034" {{ date('Y') == '2034' ? 'selected' : '' }}>2034</option>
                            <option value="2035" {{ date('Y') == '2035' ? 'selected' : '' }}>2035</option>
                        </select>
                    </div>
                </form>
                <div id="new-customer-legend" class="px-5 py-3">
                    <ul class="flex flex-wrap"></ul>
                </div>
                <div class="grow">
                    <canvas id="new-customer-chart" width="300" height="100"></canvas>
                </div>
            </div>
            @endif
            @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102' || Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203')
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
            <header class="px-5 py-4 border-b border-slate-100">
                @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102' || Auth::user()->role == '200' || Auth::user()->role == '201')
                <h2 class="font-semibold text-slate-800">Global New Product</h2>
                @endif
                @if (Auth::user()->role == '202' || Auth::user()->role == '203')
                <h2 class="font-semibold text-slate-800">New Product</h2>
                @endif
            </header>
            <form class="flex justify-end mb-3 mt-3" id="form-prod">
                <label class="block text-sm font-medium text-lg mt-1" for="form-search">Year :</label>
                <div class="relative ml-2 w-3/4 md:w-1/4">
                    <select id="prod-search" name = "year" class="getAchiev form-input w-20">
                        <option value="2020" {{ date('Y') == '2020' ? 'selected' : '' }}>2020</option>
                        <option value="2021" {{ date('Y') == '2021' ? 'selected' : '' }}>2021</option>
                        <option value="2022" {{ date('Y') == '2022' ? 'selected' : '' }}>2022</option>
                        <option value="2023" {{ date('Y') == '2023' ? 'selected' : '' }}>2023</option>
                        <option value="2024" {{ date('Y') == '2024' ? 'selected' : '' }}>2024</option>
                        <option value="2025" {{ date('Y') == '2025' ? 'selected' : '' }}>2025</option>
                        <option value="2026" {{ date('Y') == '2026' ? 'selected' : '' }}>2026</option>
                        <option value="2027" {{ date('Y') == '2027' ? 'selected' : '' }}>2027</option>
                        <option value="2028" {{ date('Y') == '2028' ? 'selected' : '' }}>2028</option>
                        <option value="2029" {{ date('Y') == '2029' ? 'selected' : '' }}>2029</option>
                        <option value="2030" {{ date('Y') == '2030' ? 'selected' : '' }}>2030</option>
                        <option value="2031" {{ date('Y') == '2031' ? 'selected' : '' }}>2031</option>
                        <option value="2032" {{ date('Y') == '2032' ? 'selected' : '' }}>2032</option>
                        <option value="2033" {{ date('Y') == '2033' ? 'selected' : '' }}>2033</option>
                        <option value="2034" {{ date('Y') == '2034' ? 'selected' : '' }}>2034</option>
                        <option value="2035" {{ date('Y') == '2035' ? 'selected' : '' }}>2035</option>
                    </select>
                </div>
            </form>
            <div id="new-product-legend" class="px-5 py-3">
                <ul class="flex flex-wrap"></ul>
            </div>
            <div class="grow">
                <canvas id="new-product-chart" width="300" height="100"></canvas>
            </div>
            </div>
            @endif
            @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102' || Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203')
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
            <header class="px-5 py-4 border-b border-slate-100">
                @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102' || Auth::user()->role == '200' || Auth::user()->role == '201')
                <h2 class="font-semibold text-slate-800">Global Offering</h2>
                @endif
                @if (Auth::user()->role == '202' || Auth::user()->role == '203')
                <h2 class="font-semibold text-slate-800">Offering</h2>
                @endif
            </header>
            <form class="flex justify-end mb-3 mt-3" id="form-offer">
                <label class="block text-sm font-medium text-lg mt-1" for="form-search">Year :</label>
                <div class="relative ml-2 w-3/4 md:w-1/4">
                    <select id="offer-search" name = "year" class="getAchiev form-input w-20">
                        <option value="2020" {{ date('Y') == '2020' ? 'selected' : '' }}>2020</option>
                        <option value="2021" {{ date('Y') == '2021' ? 'selected' : '' }}>2021</option>
                        <option value="2022" {{ date('Y') == '2022' ? 'selected' : '' }}>2022</option>
                        <option value="2023" {{ date('Y') == '2023' ? 'selected' : '' }}>2023</option>
                        <option value="2024" {{ date('Y') == '2024' ? 'selected' : '' }}>2024</option>
                        <option value="2025" {{ date('Y') == '2025' ? 'selected' : '' }}>2025</option>
                        <option value="2026" {{ date('Y') == '2026' ? 'selected' : '' }}>2026</option>
                        <option value="2027" {{ date('Y') == '2027' ? 'selected' : '' }}>2027</option>
                        <option value="2028" {{ date('Y') == '2028' ? 'selected' : '' }}>2028</option>
                        <option value="2029" {{ date('Y') == '2029' ? 'selected' : '' }}>2029</option>
                        <option value="2030" {{ date('Y') == '2030' ? 'selected' : '' }}>2030</option>
                        <option value="2031" {{ date('Y') == '2031' ? 'selected' : '' }}>2031</option>
                        <option value="2032" {{ date('Y') == '2032' ? 'selected' : '' }}>2032</option>
                        <option value="2033" {{ date('Y') == '2033' ? 'selected' : '' }}>2033</option>
                        <option value="2034" {{ date('Y') == '2034' ? 'selected' : '' }}>2034</option>
                        <option value="2035" {{ date('Y') == '2035' ? 'selected' : '' }}>2035</option>
                    </select>
                </div>
            </form>
            <div id="offering-chart-legend" class="px-5 py-3">
                <ul class="flex flex-wrap"></ul>
            </div>
            <div class="grow">
                <canvas id="offering-chart" width="300" height="100"></canvas>
            </div>
            </div>
            @endif

            @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102' || Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203'
            || Auth::user()->role == '800' || Auth::user()->role == '801' || Auth::user()->role == '802' || Auth::user()->role == '803' || Auth::user()->role == '900' || Auth::user()->role == '901'
            || Auth::user()->role == '902' || Auth::user()->role == '903')
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
            <header class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">Total AR Days - All Customers</h2>
            </header>
            <form class="flex justify-end mb-3 mt-3" id="form-ar">
                <label class="block text-sm font-medium text-lg mt-1" for="form-search">Year :</label>
                <div class="relative ml-2 w-3/4 md:w-1/4">
                    <select id="ar-search" name = "year" class="getAchiev form-input w-20">
                        <option value="2020" {{ date('Y') == '2020' ? 'selected' : '' }}>2020</option>
                        <option value="2021" {{ date('Y') == '2021' ? 'selected' : '' }}>2021</option>
                        <option value="2022" {{ date('Y') == '2022' ? 'selected' : '' }}>2022</option>
                        <option value="2023" {{ date('Y') == '2023' ? 'selected' : '' }}>2023</option>
                        <option value="2024" {{ date('Y') == '2024' ? 'selected' : '' }}>2024</option>
                        <option value="2025" {{ date('Y') == '2025' ? 'selected' : '' }}>2025</option>
                        <option value="2026" {{ date('Y') == '2026' ? 'selected' : '' }}>2026</option>
                        <option value="2027" {{ date('Y') == '2027' ? 'selected' : '' }}>2027</option>
                        <option value="2028" {{ date('Y') == '2028' ? 'selected' : '' }}>2028</option>
                        <option value="2029" {{ date('Y') == '2029' ? 'selected' : '' }}>2029</option>
                        <option value="2030" {{ date('Y') == '2030' ? 'selected' : '' }}>2030</option>
                        <option value="2031" {{ date('Y') == '2031' ? 'selected' : '' }}>2031</option>
                        <option value="2032" {{ date('Y') == '2032' ? 'selected' : '' }}>2032</option>
                        <option value="2033" {{ date('Y') == '2033' ? 'selected' : '' }}>2033</option>
                        <option value="2034" {{ date('Y') == '2034' ? 'selected' : '' }}>2034</option>
                        <option value="2035" {{ date('Y') == '2035' ? 'selected' : '' }}>2035</option>
                    </select>
                </div>
            </form>
            <div id="ar-days-legend" class="px-5 py-3">
                <ul class="flex flex-wrap"></ul>
            </div>
            <div class="grow">
                <canvas id="ar-days-chart" width="300" height="100"></canvas>
            </div>
            </div>
            @endif --}}
{{-- @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102')
<div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
    <header class="px-5 py-4 border-b border-slate-100">
        @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102' || Auth::user()->role == '200' || Auth::user()->role == '201')
        <h2 class="font-semibold text-slate-800">Global Year Sales</h2>
        @endif
        @if (Auth::user()->role == '202' || Auth::user()->role == '203')
        <h2 class="font-semibold text-slate-800">This Year Sales</h2>
        @endif
    </header>
    <div id="dashboard-sales-chart-legend" class="px-5 py-3">
        <ul class="flex flex-wrap"></ul>
    </div>
    <div class="grow">
        <canvas id="dashboard-sales-chart" width="300" height="100"></canvas>
    </div>
</div>
@endif --}}

{{-- @if (Auth::user()->role == '100' || Auth::user()->role == '101' || Auth::user()->role == '102')
        <div class="col-span-full xl:col-span-8 bg-white shadow-lg rounded-sm border border-slate-200">
            <header class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">Purchase Orders Report</h2>
            </header>
            <div class="p-3">
                <div class="table-responsive">
                     <table id="" class="table table-striped table-bordered text-xs" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">Periode</th>
                                <th class="text-center">Realized</th>
                                <th class="text-center">Budget</th>
                                <th class="text-center">Achievement %</th>
                             </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>   
@endif   --}}


            <!-- Line chart (Real Time Value) -->
            {{-- <x-dashboard.dashboard-card-05 />

            <!-- Doughnut chart (Top Countries) -->
            <x-dashboard.dashboard-card-06 /> --}}
            
            <!-- Table (Top Channels) -->
            {{-- <x-dashboard.dashboard-card-07 /> --}}

            <!-- Line chart (Sales Over Time)  -->
            {{-- <x-dashboard.dashboard-card-08 />

            <!-- Stacked bar chart (Sales VS Refunds) -->
            <x-dashboard.dashboard-card-09 /> --}}

            <!-- Card (Recent Activity) -->
            {{-- <x-dashboard.dashboard-card-10 /> --}}
            {{-- @if (Auth::user()->role == '100' || Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203')
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200" >
                <header class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Upcoming Product Offering</h2>
                </header>
                <div class="p-3">
            
                    <!-- Card content -->
                    <!-- "Today" group -->
                    <div class="grow" width="100" height="100">
                        <header class="text-xs uppercase text-slate-400 bg-slate-50 rounded-sm font-semibold p-2">Today</header>
                        <ul class="my-1">
                            <!-- Item -->
                            @if (count($dataOffering['today']) > 0)
                                @foreach ($dataOffering['today'] as $today)    
                                <li class="flex px-2">
                                    <div class="grow flex items-center border-b border-slate-100 text-sm py-2">
                                        <div class="w-9 h-9 rounded-full shrink-0 {{$today->value_color}}"></div>
                                        <div class="grow flex justify-between">
                                            <div class="self-center"><a class="font-medium text-slate-800 hover:text-slate-900" href="#0">&nbsp;{{$today->color_tag}}</a> {{$today->company_id}} - {{$today->name}}, at {{date('Y-m-d H;i', strtotime($today->start_time))}}</div>
                                            <div class="shrink-0 self-end ml-2">
                                                <a href="{{route('productoffering')}}" class="font-medium text-indigo-500 hover:text-indigo-600" href="#0">View<span class="hidden sm:inline"> -&gt;</span></a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            @else
                                <li class="flex px-2 justify-center mb-2 text-sm">No Activity Found</li>
                            @endif
                        </ul>
                    </div>
                    <!-- "Yesterday" group -->
                    <div>
                        <header class="text-xs uppercase text-slate-400 bg-slate-50 rounded-sm font-semibold p-2">Tomorrow</header>
                        <ul class="my-1">
                            <!-- Item -->
                            @if (count($dataOffering['tomorrow']) > 0)
                                @foreach ($dataOffering['tomorrow'] as $tomorrow)    
                                <li class="flex px-2">
                                    <div class="grow flex items-center border-b border-slate-100 text-sm py-2">
                                        <div class="w-9 h-9 rounded-full shrink-0 {{$tomorrow->value_color}}"></div>
                                        <div class="grow flex justify-between">
                                            <div class="self-center"><a class="font-medium text-slate-800 hover:text-slate-900" href="#0">&nbsp;{{$tomorrow->color_tag}}</a> {{$tomorrow->company_id}} - {{$tomorrow->name}}, at {{date('Y-m-d H:i', strtotime($tomorrow->start_time))}}</div>
                                            <div class="shrink-0 self-end ml-2">
                                                <a href="{{route('productoffering')}}" class="font-medium text-indigo-500 hover:text-indigo-600" href="#0">View<span class="hidden sm:inline"> -&gt;</span></a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            @else
                                <li class="flex px-2 justify-center mb-2 text-sm">No Activity Found</li>
                            @endif
                        </ul>
                    </div>
            
                </div>
            </div>

            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
                <header class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Project Proposal</h2>
                </header>
                <div class="p-3">
                    @if (Auth::user()->role == '100')
                    <div class="table-responsive">
                         <table id="proyek1" class="table table-striped table-bordered text-xs" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Project</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Project Status</th>
                                    <th class="text-center">Action</th>
                                 </tr>
                            </thead>
                        </table>
                    </div>
                    @endif
                    @if (Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203')
                    <div class="table-responsive">
                         <table id="proyek" class="table table-striped table-bordered text-xs" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Project</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Project Status</th>
                                    <th class="text-center">Action</th>
                                 </tr>
                            </thead>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif --}}

            {{-- @if (Auth::user()->role == '100' || Auth::user()->role == '500' || Auth::user()->role == '501' || Auth::user()->role == '502' || Auth::user()->role == '503')
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
                <header class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Purchase Order</h2>
                </header>
                <div class="p-3">
                    <div class="table-responsive">
                         <table id="purchase-order" class="table table-striped table-bordered text-xs" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Purchase Order #</th>
                                    <th class="text-center">Created By</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                 </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            @endif --}}

            {{-- @if (Auth::user()->role == '100' || Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203'
            || Auth::user()->role == '300' || Auth::user()->role == '301' || Auth::user()->role == '302' || Auth::user()->role == '303')
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
                <header class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Today's Order</h2>
                </header>
                <div class="p-3">
                    <div class="table-responsive">
                         <table id="sales-order" class="table table-striped table-bordered text-xs" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Created Date</th>
                                    <th class="text-center">Invoice Number</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Action</th>
                                 </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            @endif --}}

            {{-- @if (Auth::user()->role == '100' || Auth::user()->role == '600' || Auth::user()->role == '601' || Auth::user()->role == '602' || Auth::user()->role == '603')
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
                <header class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Today's Attendance List</h2>
                </header>
                <div class="p-3">
                    <div class="table-responsive">
                         <table id="attendance-all" class="table table-striped table-bordered text-xs" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Employee</th>
                                    <th class="text-center">Clock In</th>
                                 </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            @endif --}}

            {{-- @if (Auth::user()->role == '100' || Auth::user()->role == '1000' || Auth::user()->role == '1001' || Auth::user()->role == '1002' || Auth::user()->role == '1003')
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
                <header class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Asset Inventory Request List</h2>
                </header>
                <div class="p-3">
                    <div class="table-responsive">
                         <table id="asset-request" class="table table-striped table-bordered text-xs" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Purchase Request #</th>
                                    <th class="text-center">Request Level</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                 </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            @endif --}}

            {{-- @if (Auth::user()->role == '100' || Auth::user()->role == '1000' || Auth::user()->role == '1001' || Auth::user()->role == '1002' || Auth::user()->role == '1003')
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
                <header class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Reimburse Request List</h2>
                </header>
                <div class="p-3">
                    <div class="table-responsive">
                         <table id="reimburse-request" class="table table-striped table-bordered text-xs" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Reimburse Request #</th>
                                    <th class="text-center">Applicant</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                 </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            @endif --}}

            {{-- <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200" width="100" height="100">
                    <header class="px-5 py-4 border-b border-slate-100">
                        <h2 class="font-semibold text-slate-800">Upcoming Calendar</h2>
                    </header>
                    <div class="p-3">
                
                        <!-- Card content -->
                        <!-- "Today" group -->
                        <div class="grow" width="100" height="100">
                            <header class="text-xs uppercase text-slate-400 bg-slate-50 rounded-sm font-semibold p-2">Today</header>
                            <ul class="my-1">
                                <!-- Item -->
                                @if (count($dataCalendars['today']) > 0)
                                    @foreach ($dataCalendars['today'] as $today)    
                                    <li class="flex px-2">
                                        <div class="grow flex items-center border-b border-slate-100 text-sm py-2">
                                            <div class="w-9 h-9 rounded-full shrink-0 {{$today->value_color}}"></div>
                                            <div class="grow flex justify-between">
                                                <div class="self-center"><a class="font-medium text-slate-800 hover:text-slate-900" href="#0">&nbsp;{{$today->color_tag}}</a> {{$today->calendar_name}}, at {{date('Y-m-d', strtotime($today->start_time))}}</div>
                                                <div class="shrink-0 self-end ml-2">
                                                    <a href="{{route('calendar')}}" class="font-medium text-indigo-500 hover:text-indigo-600" href="#0">View<span class="hidden sm:inline"> -&gt;</span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                @else
                                    <li class="flex px-2 justify-center mb-2 text-sm">No Activity Found</li>
                                @endif
                            </ul>
                        </div>
                        <!-- "Yesterday" group -->
                        <div>
                            <header class="text-xs uppercase text-slate-400 bg-slate-50 rounded-sm font-semibold p-2">Tomorrow</header>
                            <ul class="my-1">
                                <!-- Item -->
                                @if (count($dataCalendars['tomorrow']) > 0)
                                    @foreach ($dataCalendars['tomorrow'] as $tomorrow)    
                                    <li class="flex px-2">
                                        <div class="grow flex items-center border-b border-slate-100 text-sm py-2">
                                            <div class="w-9 h-9 rounded-full shrink-0 {{$tomorrow->value_color}}"></div>
                                            <div class="grow flex justify-between">
                                                <div class="self-center"><a class="font-medium text-slate-800 hover:text-slate-900" href="#0">&nbsp;{{$tomorrow->color_tag}}</a> {{$tomorrow->calendar_name}}, at {{date('Y-m-d', strtotime($tomorrow->start_time))}}</div>
                                                <div class="shrink-0 self-end ml-2">
                                                    <a href="{{route('calendar')}}" class="font-medium text-indigo-500 hover:text-indigo-600" href="#0">View<span class="hidden sm:inline"> -&gt;</span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                @else
                                    <li class="flex px-2 justify-center mb-2 text-sm">No Activity Found</li>
                                @endif
                            </ul>
                        </div>
                
                    </div>
            </div>  --}}

            {{-- @if (Auth::user()->role == '100' || Auth::user()->role == '200' || Auth::user()->role == '201' || Auth::user()->role == '202' || Auth::user()->role == '203' || Auth::user()->role == '300'
            || Auth::user()->role == '301' || Auth::user()->role == '302' || Auth::user()->role == '303' || Auth::user()->role == '500' || Auth::user()->role == '501' || Auth::user()->role == '502' || Auth::user()->role == '503'
            || Auth::user()->role == '600' || Auth::user()->role == '601' || Auth::user()->role == '602' || Auth::user()->role == '603' || Auth::user()->role == '700' || Auth::user()->role == '701' || Auth::user()->role == '702' || Auth::user()->role == '703'
            || Auth::user()->role == '800' || Auth::user()->role == '801' || Auth::user()->role == '802' || Auth::user()->role == '803' || Auth::user()->role == '900' || Auth::user()->role == '901' || Auth::user()->role == '902' || Auth::user()->role == '903'
            || Auth::user()->role == '1000' || Auth::user()->role == '1001' || Auth::user()->role == '1002' || Auth::user()->role == '1003' || Auth::user()->role == '1100' || Auth::user()->role == '1101' || Auth::user()->role == '1102' || Auth::user()->role == '1103')
                <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
                    <header class="px-5 py-4 border-b border-slate-100">
                        <h2 class="font-semibold text-slate-800">Leave Request</h2>
                    </header>
                    <div class="p-3">
                        <div class="table-responsive">
                            <table id="leave-request" class="table table-striped table-bordered text-xs" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Leave Request #</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            @endif --}}

            <!-- Card (Income/Expenses) -->
            {{-- <x-dashboard.dashboard-card-11 /> --}}

        </div>

    </div>
    @section('js-page')
    <script>
    
    </script>
    @endsection
</x-app-layout>
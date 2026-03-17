{{-- <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
    <header class="px-5 py-4 border-b border-slate-100 flex items-center">
        <h2 class="font-semibold text-slate-800">Portfolio Value</h2>
    </header>
    <div class="px-5 py-3">
        <div class="text-sm italic mb-2">Hey Mark, here is the value of your portfolio:</div>
        <div class="text-3xl font-bold text-slate-800">$224,807.27</div>
    </div>
    <div class="grow flex flex-col justify-center">
        <div>
            <canvas id="fintech-card-09" width="389" height="220"></canvas>
        </div>
        <div id="fintech-card-09-legend" class="px-5 py-4">
            <ul class="flex flex-wrap justify-center -m-1"></ul>
        </div>
    </div>
</div> --}}

<div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
    <header class="px-5 py-4 border-b border-slate-100 flex items-center">
        <h2 class="font-semibold text-slate-800">KPI Sales -> {{ Auth::user()->username }}</h2>
    </header>
    <div class="px-5 py-3">
        <div class="text-3xl font-bold text-slate-800">IDR 224.8K</div>
    </div>
    <div class="grow flex flex-col justify-center">
        <div>
            <canvas id="fintech-card-09" width="389" height="220"></canvas>
        </div>
        <div id="fintech-card-09-legend" class="px-5 py-4">
            <ul class="flex flex-wrap justify-center -m-1"></ul>
        </div>
    </div>
</div>
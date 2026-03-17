<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full">

        <!-- Page content -->
        <div class="max-w-5xl mx-auto flex flex-col lg:flex-row lg:space-x-8 xl:space-x-16">

            <!-- Cart items -->
            <div class="mb-6 lg:mb-0">
                <div class="mb-3">
                    <div class="flex text-sm font-medium text-slate-400 space-x-2">
                        <span class="text-indigo-500">Review</span>
                        <span>-&gt;</span>
                        <span class="text-slate-500">Payment</span>
                        <span>-&gt;</span>
                        <span class="text-slate-500">Confirm</span>
                    </div>
                </div>
                <header class="mb-2">
                    <!-- Title -->
                    <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Shopping Cart (3) ✨</h1>
                </header>
                <x-ecommerce.cart-items />
            </div>

            <!-- Sidebar -->
            <div class="max-w-sm mx-auto lg:max-w-none">
                <div class="bg-white p-5 shadow-lg rounded-sm border border-slate-200 lg:w-72 xl:w-80">
                    <div class="text-slate-800 font-semibold mb-2">Order Summary</div>
                    <!-- Order details -->
                    <ul class="mb-4">
                        <li class="text-sm w-full flex justify-between py-3 border-b border-slate-200">
                            <div>Products & Subscriptions</div>
                            <div class="font-medium text-slate-800">$205</div>
                        </li>
                        <li class="text-sm w-full flex justify-between py-3 border-b border-slate-200">
                            <div>Shipping</div>
                            <div class="font-medium text-slate-800">-</div>
                        </li>
                        <li class="text-sm w-full flex justify-between py-3 border-b border-slate-200">
                            <div>Taxes</div>
                            <div class="font-medium text-slate-800">$48</div>
                        </li>
                        <li class="text-sm w-full flex justify-between py-3 border-b border-slate-200">
                            <div>Total due (including taxes)</div>
                            <div class="font-medium text-emerald-600">$253</div>
                        </li>
                    </ul>
                    <!-- Promo box -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium mb-1" for="promo">Promo Code</label>
                            <div class="text-sm text-slate-400 italic">optional</div>
                        </div>
                        <input id="promo" class="form-input w-full mb-2" type="text" />
                        <button class="btn w-full bg-indigo-500 hover:bg-indigo-600 text-white disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed shadow-none" disabled>Apply Code</button>
                    </div>
                    <div class="mb-4">
                        <button class="btn w-full bg-indigo-500 hover:bg-indigo-600 text-white" href="#0">Buy Now - $253.00</button>
                    </div>
                    <div class="text-xs text-slate-500 italic text-center">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do <a class="underline hover:no-underline" href="#0">Terms</a>.</div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>

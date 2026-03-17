<x-app-layout background="bg-white">
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Create New Sales Order 📝</h1>
        </div>

        <form action="{{ route('sales-order.create') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="border-t border-slate-200">

                <!-- Components -->
                <div class="space-y-8">

                    <!-- Input Types -->
                    <div class="px-5 py-4">
                        <div class="space-y-3">
                            <div class="flex flex-col md:flex-row">
                                <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="date">Sales Name :
                                </label>
                                <input id="date" name="date" class="date form-input px-2 py-1 read-only:bg-slate-200" type="text" value="{{Auth::user()->username}}" required readonly/>
                            </div>
                            <div class="flex flex-col md:flex-row">
                                <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="date">Delivery Date :
                                </label>
                                <input id="date" name="date" class="date form-input px-2 py-1" type="date" required/>
                            </div>
                            <div class="flex flex-col md:flex-row">
                                <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="customer1">Customer Name :
                                </label>
                                <input id="customer1" name="customer1"
                                    class="customer1 form-input md:w-1/2 px-2 py-1 read-only:bg-slate-200 "
                                    type="text" readonly required></input>
                                <div x-data="{ modalOpen: false }">
                                    <button type="button" class="ml-2 btn bg-indigo-500 hover:bg-indigo-600 text-white"
                                        @click.prevent="modalOpen = true" aria-controls="feedback-modal">
                                        <svg class="w-4 h-4" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                            <path class="fill-current text-slate-200"
                                                d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                                            <path class="fill-current text-slate-200"
                                                d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                                        </svg>
                                        <span></span>
                                    </button>
                                    <!-- Modal backdrop -->
                                    <div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity"
                                        x-show="modalOpen" x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-out duration-100"
                                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                        aria-hidden="true" x-cloak></div>
                                    <!-- Modal dialog -->
                                    <div id="feedback-modal"
                                        class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                                        role="dialog" aria-modal="true" x-show="modalOpen"
                                        x-transition:enter="transition ease-in-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-4"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in-out duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 translate-y-4" x-cloak>

                                        <div class="bg-white rounded shadow-lg overflow-auto w-3/4 max-h-full"
                                            @click.outside="modalOpen = false"
                                            @keydown.escape.window="modalOpen = false">
                                            <!-- Modal header -->
                                            <div class="px-5 py-3 border-b border-slate-200">
                                                <div class="flex justify-between items-center">
                                                    <div class="font-semibold text-slate-800">Search Customer</div>
                                                    <button type="button" class="text-slate-400 hover:text-slate-500"
                                                        @click="modalOpen = false">
                                                        <div class="sr-only">Close</div>
                                                        <svg class="w-4 h-4 fill-current">
                                                            <path
                                                                d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Modal content -->
                                            <div class="modal-content text-xs px-5 py-4">
                                                <div class="table-responsive">
                                                    <table id="customer"
                                                        class="table table-striped table-bordered text-xs"
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">Customer ID</th>
                                                                <th class="text-center">Customer Name</th>
                                                                <th class="text-center">Address</th>
                                                                <th class="text-center">City</th>
                                                                <th class="text-center">Country</th>
                                                                <th class="text-center">Phone Number</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>

                                                <div class="space-y-3">
                                                </div>
                                            </div>
                                            <!-- Modal footer -->
                                            <div class="px-5 py-4 border-t border-slate-200">
                                                <div class="flex flex-wrap justify-end space-x-2">
                                                    <button type="button"
                                                        class="btn-sm border-slate-200 hover:border-slate-300 text-slate-600"
                                                        @click="modalOpen = false">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input id="customerId" name="customerId"
                                class="customerId form-input w-full md:w-72 px-2 py-1 read-only:bg-slate-200" type="text"
                                readonly required hidden/>
                            <div class="flex justify-between flex-col md:flex-row">
                                
                            </div>
                            <div class="flex flex-col justify-between md:flex-row">
                                <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="notes">Note :
                                </label>
                                <textarea id="notes" name="notes" class="notes form-input w-full md:w-3/4 px-2 py-1"
                                    rows="3" required></textarea>
                            </div>
                            <div class="flex flex-row md:flex-row">
                                <label class="block text-sm font-medium mb-1" for="task_id">Products :
                                </label>
                                <div x-data="{ modalOpen: false }">
                                    <button class="ml-2 btn bg-indigo-500 hover:bg-indigo-600 text-white" type="button"
                                        @click.prevent="modalOpen = true" aria-controls="feedback-modal">
                                        <svg class="w-4 h-4 fill-current  text-slate-200" viewBox="0 0 16 16">
                                            <path
                                                d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                                        </svg>
                                        <span></span>
                                    </button>
                                    <!-- Modal backdrop -->
                                    <div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity"
                                        x-show="modalOpen" x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-out duration-100"
                                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                        aria-hidden="true" x-cloak></div>
                                    <!-- Modal dialog -->
                                    <div id="feedback-modal"
                                        class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                                        role="dialog" aria-modal="true" x-show="modalOpen"
                                        x-transition:enter="transition ease-in-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-4"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in-out duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 translate-y-4" x-cloak>

                                        <div class="bg-white rounded shadow-lg overflow-auto w-3/4 max-h-full"
                                            @click.outside="modalOpen = false"
                                            @keydown.escape.window="modalOpen = false">
                                            <!-- Modal header -->
                                            <div class="px-5 py-3 border-b border-slate-200">
                                                <div class="flex justify-between items-center">
                                                    <div class="font-semibold text-slate-800">Add Product</div>
                                                    <button type="button" class="text-slate-400 hover:text-slate-500"
                                                        @click="modalOpen = false">
                                                        <div class="sr-only">Close</div>
                                                        <svg class="w-4 h-4 fill-current">
                                                            <path
                                                                d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Modal content -->
                                            <div class="modal-content text-xs px-5 py-4">
                                                <div class="flex justify-between flex-col md:flex-row mb-3">
                                                    <label class="block w-full md:w-1/4 text-sm font-medium mb-1"
                                                        for="nama_product">Product Name :
                                                    </label>
                                                    <input id="nama_product" name="nama_product"
                                                        class="nama_product form-input w-full md:w-3/4 px-2 py-1 read-only:bg-slate-200"
                                                        type="text" readonly required />
                                                    <div x-data="{ modalOpen: false }">
                                                        <button type="button"
                                                            class="ml-2 btn bg-indigo-500 hover:bg-indigo-600 text-white"
                                                            @click.prevent="modalOpen = true"
                                                            aria-controls="feedback-modal">
                                                            <svg class="w-4 h-4" viewBox="0 0 16 16"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path class="fill-current text-slate-200"
                                                                    d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                                                                <path class="fill-current text-slate-200"
                                                                    d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                                                            </svg>
                                                            <span></span>
                                                        </button>
                                                        <!-- Modal backdrop -->
                                                        <div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity"
                                                            x-show="modalOpen"
                                                            x-transition:enter="transition ease-out duration-200"
                                                            x-transition:enter-start="opacity-0"
                                                            x-transition:enter-end="opacity-100"
                                                            x-transition:leave="transition ease-out duration-100"
                                                            x-transition:leave-start="opacity-100"
                                                            x-transition:leave-end="opacity-0" aria-hidden="true"
                                                            x-cloak>
                                                        </div>
                                                        <!-- Modal dialog -->
                                                        <div id="feedback-modal"
                                                            class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                                                            role="dialog" aria-modal="true" x-show="modalOpen"
                                                            x-transition:enter="transition ease-in-out duration-200"
                                                            x-transition:enter-start="opacity-0 translate-y-4"
                                                            x-transition:enter-end="opacity-100 translate-y-0"
                                                            x-transition:leave="transition ease-in-out duration-200"
                                                            x-transition:leave-start="opacity-100 translate-y-0"
                                                            x-transition:leave-end="opacity-0 translate-y-4" x-cloak>

                                                            <div class="bg-white rounded shadow-lg overflow-auto w-3/4 max-h-full"
                                                                @click.outside="modalOpen = false"
                                                                @keydown.escape.window="modalOpen = false">
                                                                <!-- Modal header -->
                                                                <div class="px-5 py-3 border-b border-slate-200">
                                                                    <div class="flex justify-between items-center">
                                                                        <div class="font-semibold text-slate-800">Search
                                                                            Product</div>
                                                                        <button type="button"
                                                                            class="text-slate-400 hover:text-slate-500"
                                                                            @click="modalOpen = false">
                                                                            <div class="sr-only">Close</div>
                                                                            <svg class="w-4 h-4 fill-current">
                                                                                <path
                                                                                    d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <!-- Modal content -->
                                                                <div class="modal-content text-xs px-5 py-4">
                                                                    <div class="table-responsive">
                                                                        <table id="product"
                                                                            class="table table-striped table-bordered text-xs"
                                                                            style="width:100%">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th class="text-center">Product id
                                                                                    </th>
                                                                                    <th class="text-center">Product Code
                                                                                    </th>
                                                                                    <th class="text-center">Product Name
                                                                                    </th>
                                                                                    <th class="text-center">Nett Stock</th>
                                                                                    <th class="text-center">Unit</th>
                                                                                    <th class="text-center">Action</th>
                                                                                </tr>
                                                                            </thead>
                                                                        </table>
                                                                    </div>

                                                                    <div class="space-y-3">
                                                                    </div>
                                                                </div>
                                                                <!-- Modal footer -->
                                                                <div class="px-5 py-4 border-t border-slate-200">
                                                                    <div class="flex flex-wrap justify-end space-x-2">
                                                                        <button type="button"
                                                                            class="btn-sm border-slate-200 hover:border-slate-300 text-slate-600"
                                                                            @click="modalOpen = false">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input id="productId" name="productId"
                                                        class="productId form-input w-full md:w-72 px-2 py-1 read-only:bg-slate-200"
                                                        type="text" readonly required hidden/>
                                                </div>
                                                <div class="flex justify-between flex-col md:flex-row mb-3">
                                                    <label class="block w-full md:w-1/4 text-sm font-medium mb-1"
                                                        for="product_price">Product Price :
                                                    </label>
                                                    <input id="product_price" name="product_price"
                                                        class="product_price form-input w-full md:w-3/4 px-2 py-1 read-only:bg-slate-200"
                                                        type="number" />
                                                </div>
                                                <div class="flex justify-between flex-col md:flex-row mb-3">
                                                    <label class="block w-full md:w-1/4 text-sm font-medium mb-1"
                                                        for="order_quantity">Order Qty :
                                                    </label>
                                                    <input id="order_quantity" name="order_quantity"
                                                        class="order_quantity form-input w-full md:w-3/4 px-2 py-1 read-only:bg-slate-200"
                                                        type="number" />
                                                </div>
                                                <div class="flex justify-between flex-col md:flex-row mb-3">
                                                    <label class="block w-full md:w-1/4 text-sm font-medium mb-1"
                                                        for="order_quantity">Discount (%) :
                                                    </label>
                                                    <input id="discount" name="discount"
                                                        class="discount form-input w-full md:w-3/4 px-2 py-1 read-only:bg-slate-200"
                                                        type="number" />
                                                </div>

                                                <div class="space-y-3">
                                                </div>

                                                <!-- Modal footer -->
                                                <div class="px-5 py-4 border-t border-slate-200">
                                                    <div class="flex flex-wrap justify-end space-x-2">
                                                        <button type="button"
                                                            class="btn-sm border-slate-200 hover:border-slate-300 text-slate-600"
                                                            @click="modalOpen = false">Close</button>
                                                        <button type="button"
                                                            class="btn btn-sm btn-addProduct bg-indigo-500 hover:bg-indigo-600 text-white"
                                                            @click="modalOpen = false" id="addProduct"
                                                            data-id_product="' . $dataProduct->id . '"
                                                            data-nama_product="' . $dataProduct->name . '"
                                                            data-task-id="' . $dataProduct->task_id . '"
                                                            data-task="' . $dataProduct->task . '"
                                                            data-product_price="' . $dataProduct->price . '"
                                                            data-currency="' . $dataProduct->m_currency . '"
                                                            data-minimum_quantity_order="' . $dataProduct->minimum_order_qty . '"
                                                            data-order_quantity="' . $dataProduct->order_qty . '">Add
                                                            Product</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row md:flex-row">
                                <table class="tableProductAddBody table table-striped table-bordered mt-3"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-sm text-center">Product Name</th>
                                            <th class="text-sm text-center">Price</th>
                                            <th class="text-sm text-center">Order Qty</th>
                                            <th class="text-sm text-center">Discount (%)</th>
                                            <th class="text-sm text-center">Sub Total</th>
                                            {{-- <th class="text-sm text-center">Action</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody class="tableProductAddBody" id="tableProductAddBody">

                                    </tbody>
                                </table>
                            </div>
                            <div class="flex flex-row">
                                <label class="md:w-1/4 text-sm font-medium mb-1" for="subtotal">Subtotal :
                                </label>
                                <input type="text" class="bg-white border-white md:w-1/4 px-2 py-1" disabled/>
                                <label class="md:w-1/6 text-sm font-medium mb-1 ml-5 text-white" for="discount2idr">Discount 1 (IDR) :
                                </label>
                                <input id="subtotal" name="subtotal" type="text" class="subtotal form-input md:w-1/3 px-2 py-1 read-only:bg-slate-200 text-right ml-5" required readonly/>
                            </div>
                            <div class="flex flex-row">
                                <label class="md:w-1/4 text-sm font-medium mb-1" for="discount1">Discount 1 (%) :
                                </label>
                                <input id="discount1" name="discount1" class="discount1 form-input md:w-1/4 px-2 py-1" onchange="calculateDisc()" type="text"/>
                                <label class="md:w-1/6 text-sm font-medium mb-1 ml-5" for="discount2idr">Discount 1 (IDR) :
                                </label>
                                <input id="discount1idr" name="discount1idr" class="discount1idr form-input md:w-1/3 px-2 py-1 read-only:bg-slate-200 text-right ml-5" onchange="calculateDisc()" type="text" readonly/>
                            </div>
                            <div class="flex flex-row">
                                <label class="md:w-1/4 text-sm font-medium mb-1" for="discount2">Discount 2 (%) :
                                </label>
                                <input id="discount2" name="discount2" class="discount2 form-input md:w-1/4 px-2 py-1" onchange="calculateDisc()" type="text"/>
                                <label class="md:w-1/6 text-sm font-medium mb-1 ml-5" for="discount2idr">Discount 2 (IDR) :
                                </label>
                                <input id="discount2idr" name="discount2idr" class="discount2idr form-input md:w-1/3 px-2 py-1 read-only:bg-slate-200 text-right ml-5" onchange="calculateDisc()" type="text" readonly/>
                            </div>
                            <div class="flex flex-row">
                                <label class="md:w-1/4 text-sm font-medium mb-1" for="discount3">Discount 3 (%) :
                                </label>
                                <input id="discount3" name="discount3" class="discount3 form-input md:w-1/4 px-2 py-1" onchange="calculateDisc()" type="text"/>
                                <label class="md:w-1/6 text-sm font-medium mb-1 ml-5" for="discount3idr">Discount 3 (IDR) :
                                </label>
                                <input id="discount3idr" name="discount3idr" class="discount3idr form-input md:w-1/3 px-2 py-1 read-only:bg-slate-200 text-right ml-5" onchange="calculateDisc()" type="text" readonly/>
                            </div>
                            <div class="flex flex-row">
                                <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="total">Total :
                                </label>
                                <input type="text" class="bg-white border-white md:w-1/4 px-2 py-1" disabled/>
                                <label class="md:w-1/6 text-sm font-medium mb-1 ml-5 text-white" for="discount2idr">Discount 1 (IDR) :
                                </label>
                                <input id="total" name="total" type="text" class="total form-input md:w-1/3 px-2 py-1 read-only:bg-slate-200 text-right ml-6" required readonly/>
                            </div>
                            <div class="flex flex-row">
                                <label class="md:w-1/4 text-sm font-medium mb-1" for="ppn">PPN (%) :
                                </label>
                                <input id="ppn" name="ppn" class="ppn form-input md:w-1/4 px-2 py-1" onchange="calculateDisc()" type="text"/>
                                <label class="md:w-1/6 text-sm font-medium mb-1 ml-5" for="ppnidr">PPN (IDR) :
                                </label>
                                <label class="md:w-1/6 text-sm font-medium mb-1 ml-5 text-white" for="discount2idr">Discou
                                </label>
                                <input id="ppnidr" name="ppnidr" class="ppnidr form-input md:w-1/3 px-2 py-1 read-only:bg-slate-200 text-right" onchange="calculateDisc()" type="text" readonly/>
                            </div>
                            <div class="flex flex-row">
                                <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="totalAmount">Total Amount :
                                </label>
                                <input type="text" class="bg-white border-white md:w-1/4 px-2 py-1" disabled/>
                                <label class="md:w-1/6 text-sm font-medium mb-1 ml-5 text-white" for="discount2idr">Discount 1 (IDR) :
                                </label>
                                <input id="totalAmount" name="totalAmount" class="totalAmount form-input md:w-1/3 px-2 py-1 read-only:bg-slate-200 text-right ml-6" type="text" readonly/>
                                <input id="totalAmount1" name="totalAmount1" class="totalAmount1 form-input w-full md:w-3/4 px-2 py-1 read-only:bg-slate-200" type="text" readonly hidden/>
                            </div>
                            <center><button class="btn bg-indigo-500 hover:bg-indigo-600 text-white mt-5" type="submit">
                                    <span class="xs:block ml-5 mr-5">Create Sales Order</span>
                                </button></center>
                            <div class="space-y-3">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @section('js-page')
    <script>
        // data Companies
        $(document).ready(function () {
            $('#customer').DataTable({
                responsive: true,
                processing: true,
                serverSide: false,
                stateServe: true,
                order: [[1, 'asc']],
                language: {
                    search: "Search Customer Name: "
                },
                ajax: {
                    url: "{{ route('create.getcustomer') }}"
                },
                columns: [
                    {
                        data: "customer_id",
                        name: "customer_id"
                    },
                    {
                        data: "customer",
                        name: "customer"
                    },
                    {
                        data: "address",
                        name: "address"
                    },
                    {
                        data: "city",
                        name: "city"
                    },
                    {
                        data: "country",
                        name: "country"
                    },
                    {
                        data: "phone_number",
                        name: "phone_number"
                    },
                    {
                        data: "action",
                        name: "action"
                    },
                ],
                columnDefs: [
                    { className: 'text-center', targets: [0, 1, 2, 6] },
                    {
                        target: 0,
                        visible: false,
                        searchable: false,
                    }
                ], lengthMenu: [[30, 50, 100, -1], [30, 50, 100, 'All']]
            });

            $('#customer').on("click", ".btn-select", function () {
                const customerId = $(this).data("id");
                const customer1 = $(this).data("nama");

                $.ajax({
                    type: "GET",
                    url: `/sales/sales-order/getcustomer/${customerId}`,
                    success: function (response) {
                        $(".customerId").val(customerId);
                        $(".customer1").val(customer1);
                    }
                });
            });
        });
        let custlist = '';
            $(".btn-new").click(function () {
                var customer = $('#customerName').val(); 
                var address = $('#address').val(); 
                var city = $('#city').val(); 
                var country = $('#country').val(); 
                var phone = $('#phone').val(); 

                custlist += `${customer} (${address} / ${city} / ${country} / ${phone})`;

                $(".customer1").val(custlist);

            });

        // data product
        $(document).ready(function () {
            $('#product').DataTable({
                responsive: true,
                processing: true,
                serverSide: false,
                stateServe: true,
                order: [[2, 'asc']],
                language: {
                    search: "Search Product Name: "
                },
                ajax: {
                    url: "{{ route('create.getproduct') }}"
                },
                columns: [
                    {
                        data: "id",
                        name: "id"
                    },
                    {
                        data: "code",
                        name: "code"
                    },
                    {
                        data: "name",
                        name: "name"
                    },
                    {
                        data: "nett_stock",
                        name: "nett_stock"
                    },
                    {
                        data: "unit",
                        name: "unit"
                    },
                    {
                        data: "action",
                        name: "action"
                    },
                ],
                columnDefs: [
                    { className: 'text-center', targets: [0, 1, 3, 4, 5] },
                ], lengthMenu: [[30, 50, 100, -1], [30, 50, 100, 'All']]
            });

            $('#product').on("click", ".btn-select", function () {
                const productId = $(this).data("id_product");
                const name = $(this).data("nama_product");


                $(".productId").val(productId);
                $(".nama_product").val(name);
            });
        });

        function deleteDataProduct(positionTableRow, dataFromDatabase) {
            const positionTableRowVariable = positionTableRow

                $(`#tableProductAddBody tr[id="row-${positionTableRow}"]`).remove();

            console.info('positionTableRowVariable: ', positionTableRowVariable)
        }

        function calculateDisc() {
            let finalAmount = totalAmount;
            if($('#discount1').val()){
                disc1 = ($('#discount1').val()/100*finalAmount);
                $('#discount1idr').val(`${divider(disc1)}`);
                finalAmount -= (finalAmount*$('#discount1').val()/100);
            }
            if($('#discount2').val()){
                disc2 = ($('#discount2').val()/100*finalAmount);
                $('#discount2idr').val(`${divider(disc2)}`);
                finalAmount -= (finalAmount*$('#discount2').val()/100);
            }
            if($('#discount3').val()){
                disc3 = ($('#discount3').val()/100*finalAmount);
                $('#discount3idr').val(`${divider(disc3)}`);
                finalAmount -= (finalAmount*$('#discount3').val()/100);
                $('#total').val(`${divider(finalAmount)}`);
            }
            if($('#ppn').val()){
                ppn = ($('#ppn').val()/100*finalAmount);
                $('#ppnidr').val(`${divider(ppn)}`);
                finalAmount += (finalAmount*$('#ppn').val()/100);
            }
            $('#totalAmount').val(`${divider(finalAmount)}`);
            $('#totalAmount1').val(finalAmount);
        }

            let productIdx = 0;
            let totalAmount = 0;
            $("#addProduct").click(
            function addProduct() {
                if(!($('#productId').val())||!($('#product_price').val())||!($('#discount').val())||!($('#order_quantity').val())){
                    alert('Please Fill the Empty Field !')
                }else{
                    var id = $('#productId').val();
                    var name = $('#nama_product').val();
                    var price = $('#product_price').val();
                    var oq = $('#order_quantity').val();
                    var disc = $('#discount').val();
                    var subtotal = price * oq - (price * oq * disc/100);
                    totalAmount += subtotal;


                
                    var tr = "<tr id=\"row-" + productIdx + "\">\n" +
                        "                                    <td class=\"text-left\">" + name + "<input type=\"hidden\" name = \"rows[" + productIdx + "][ids]\"value =" + id + "></td>\n" +
                        "                                    <td class=\"text-center\">" + `${divider(price)}` + "<input type=\"hidden\" name = \"rows[" + productIdx + "][prices]\" value =" + price + "></td>\n" +
                        "                                    <td class=\"text-center\">" + oq + "<input type=\"hidden\" name = \"rows[" + productIdx + "][oqs]\" value =" + oq + "></td>\n" +
                        "                                    <td class=\"text-center\">" + disc + "<input type=\"hidden\" name = \"rows[" + productIdx + "][discs]\" value =" + disc + "></td>\n" +
                        "                                    <td class=\"text-center\">" + `${divider(subtotal)}` + "<input type=\"hidden\" id = \"rows[" + productIdx + "][subtotals]\" name = \"rows[" + productIdx + "][subtotals]\" value =" + subtotal + "></td>\n" +
                        // "   <td class=\"text-center\">" + "<button type=\"button\" onclick=\"deleteDataProduct(" + productIdx + "," + null + ")\" class=\"remove_button btn border-slate-200 hover:border-slate-300\" > <svg class=\"w-4 h-4 fill-current text-rose-500 shrink-0\" viewBox=\"0 0 16 16\"> <path d=\"M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z\" /> </svg> </button>" + "</td>"
                    "                                </tr>";

                    $("#tableProductAddBody").append(tr);
                    function tableProductAddBody(tableProductAddBody) {
                        $('#tableProductAddBody').remove();
                    }

                    $('#productId').val('');
                    $('#nama_product').val('');
                    $('#product_price').val('');
                    $('#order_quantity').val('');
                    $('#discount').val('');
                    $('#subtotal').val(`${divider(totalAmount)}`);
                    productIdx++;
                    calculateDisc();
                }
            });

    </script>
    @endsection
</x-app-layout>
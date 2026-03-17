<x-app-layout background="bg-white">
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Update Proposal 📝</h1>
        </div>
        <form action="{{ route('proposal.update', ['projectId' => $viewUpdate->projectId]) }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="px-5 py-4">
                <div class="space-y-3">
                    <div class="flex justify-between flex-col md:flex-row">
                        <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="sales">Sales Representative :
                        </label>
                        <input id="projectId" name="projectId" class=" projectId form-select w-full md:w-3/4 px-2 py-1"
                            value="{{ $viewUpdate->projectId }}" disabled hidden />
                        </label>
                        <input id="tasksId" name="tasksId" class=" tasksId form-select w-full md:w-3/4 px-2 py-1"
                            value="{{ $viewUpdate->tasksId }}" disabled hidden />
                        </label>
                        <input id="sales" name="sales" class=" sales form-select w-full md:w-3/4 px-2 py-1"
                            value="{{ $viewUpdate->salesname }}" disabled />
                    </div>
                    <div class="flex justify-between flex-col md:flex-row">
                        <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="company">Company : </label>
                        <input id="company" name="company"
                            class="company form-input w-full md:w-3/4 px-2 py-1 read-only:bg-slate-200" type="text"
                            value="{{ $viewUpdate->companyname }}" readonly disabled />
                    </div>

                    <div class="flex justify-between flex-col md:flex-row">
                        <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="pic">Company PIC : </label>
                        <input value="{{ $viewUpdate->pic}}" id="pic" name="pic"
                            class="pic form-select w-full md:w-3/4 px-2 py-1" disabled />
                    </div>
                    <div class="flex justify-between flex-col md:flex-row">
                        <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="project_name">Project Name :
                        </label>
                        <input id="project_name" name="project_name" value="{{ $viewUpdate->name}}"
                            class="project-name form-input w-full md:w-3/4 px-2 py-1" type="text" />
                    </div>
                    <div class="flex flex-col justify-between md:flex-row">
                        <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="desc">Description : </label>
                        <textarea id="desc" name="desc" class="description form-input w-full md:w-3/4 px-2 py-1"
                            rows="3">{{ $viewUpdate->description }} </textarea>
                    </div>
                    <div class="flex justify-between flex-col md:flex-row">
                        <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="file">File Upload (PDF) :
                        </label>
                        <input id="file" name="file" class="form-input w-full md:w-3/4 px-2 py-1" type="file"
                            accept="application/pdf" />
                    </div>
                    <div class="flex justify-between flex-col md:flex-row">
                        <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for=""></label>
                        <input id="company" name="file"
                            class="file form-input w-full md:w-3/4 px-2 py-1 read-only:bg-slate-200" type="text"
                            readonly disabled value="{{ $viewUpdate->file_upload}}" />
                    </div>
                    <div class="flex flex-row md:flex-row">
                        <label class="block text-sm font-medium mb-1" for="task_id">Product Offered :
                        </label>
                        <div x-data="{ modalOpen: false }">
                            <button class="ml-24 btn bg-indigo-500 hover:bg-indigo-600 text-white" type="button"
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
                                    @click.outside="modalOpen = false" @keydown.escape.window="modalOpen = false">
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
                                                    @click.prevent="modalOpen = true" aria-controls="feedback-modal">
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
                                                    x-transition:leave-end="opacity-0" aria-hidden="true" x-cloak>
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
                                                                <table id="proyek"
                                                                    class="table table-striped table-bordered text-xs"
                                                                    style="width:100%">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-center">Product Code
                                                                            </th>
                                                                            <th class="text-center">Product Name
                                                                            </th>
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
                                                type="text" readonly required hidden />
                                        </div>
                                        <div class="flex justify-between flex-col md:flex-row mb-3">
                                            <label class="block w-full md:w-1/4 text-sm font-medium mb-1"
                                                for="currency">Currency :
                                            </label>
                                            <select id="currency" name="currency"
                                                class="currency form-select w-full md:w-3/4 px-2 py-1">
                                                <option selected hidden>Set Currency</option>
                                                <option value="IDR">IDR</option>
                                                <option value="USD">USD</option>
                                            </select>
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
                                                for="minimum_quantity_order">Minimum Order Qty :
                                            </label>
                                            <input id="minimum_quantity_order" name="minimum_quantity_order"
                                                class="minimum_quantity_order form-input w-full md:w-3/4 px-2 py-1 read-only:bg-slate-200"
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
                                                for="status_product">Status :
                                            </label>
                                            <select id="status_product" name="status_product" class="status_product form-select w-full md:w-3/4 px-2 py-1">
                                                <option selected value="Open">Open</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Lost">Lost</option>
                                                <option value="Won">Won</option>
                                            </select>
                                        </div>
                                        <div class="flex justify-between flex-col md:flex-row mb-3">
                                            <label class="block w-full md:w-1/4 text-sm font-medium mb-1"
                                                for="product_note">Notes :
                                            </label>
                                            <textarea id="product_note" name="product_note"
                                                class="product_note form-input w-full md:w-3/4 px-2 py-1 read-only:bg-slate-200"
                                                type="text" rows="2"></textarea>
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
                                                    data-id_product="' . $productData->id . '"
                                                    data-nama_product="' . $productData->name . '"
                                                    data-task-id="' . $productData->task_id . '"
                                                    data-task="' . $productData->task . '"
                                                    data-product_price="' . $productData->price . '"
                                                    data-currency="' . $productData->m_currency . '"
                                                    data-minimum_quantity_order="' . $productData->minimum_order_qty . '"
                                                    data-order_quantity="' . $productData->order_qty . '">Add
                                                    Product</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-row md:flex-row">
                        <table class="tableProductAddBody table table-striped table-bordered mt-3" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-sm text-center">Product Name</th>
                                    <th class="text-sm text-center">Currency</th>
                                    <th class="text-sm text-center">Price</th>
                                    <th class="text-sm text-center">Minimun Order Qty</th>
                                    <th class="text-sm text-center">Quantity</th>
                                    <th class="text-sm text-center">Status</th>
                                    <th class="text-sm text-center">Notes</th>
                                    <th class="text-sm text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="tableProductAddBody" id="tableProductAddBody">

                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-between flex-col md:flex-row">
                        <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="status">Status :
                        </label>
                        <select id="status" name="status" class="status form-select w-full md:w-3/4 px-2 py-1">
                            <option value="0" {{ $viewUpdate->status == 0 ? ' selected' : '' }}>Pending</option>
                            <option value="1" {{ $viewUpdate->status == 1 ? ' selected' : '' }}>Open</option>
                            <option value="2" {{ $viewUpdate->status == 2 ? ' selected' : '' }}>Lost</option>
                            <option value="3" {{ $viewUpdate->status == 3 ? ' selected' : '' }}>Won</option>
                        </select>
                    </div>
                    <div class="flex flex-col justify-between md:flex-row">
                        <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="botes">Date : </label>
                        <input type="date" id="date" name="date"
                            class="date form-input w-full md:w-3/4 px-2 py-1" rows="3"
                            value="{{ date('Y-m-d', strtotime($viewUpdate->date)) }}"></input>
                    </div>
                    <div class="flex justify-between flex-col md:flex-row">
                        <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="stage">Project
                            Stage :
                        </label>
                        <select id="stage" name="stage" class="stage form-select w-full md:w-3/4 px-2 py-1" required>
                            <option value="0" selected disabled hidden>Select Stage</option>
                            <option value="1" {{ $viewUpdate->stage == 1 ? ' selected' : '' }}>1. DOCUMENTATION /
                                SAMPLING &
                                QUOTATION </option>
                            <option value="2" {{ $viewUpdate->stage == 2 ? ' selected' : '' }}>2. FORMULATION IN
                                PROGRESS
                            </option>
                            <option value="3" {{ $viewUpdate->stage == 3 ? ' selected' : '' }}>3. STABLE FORMULA &
                                PENDING
                                APPROVAL</option>
                            <option value="4" {{ $viewUpdate->stage == 4 ? ' selected' : '' }}>4. PILOT</option>
                            <option value="5" {{ $viewUpdate->stage == 5 ? ' selected' : '' }}>5. MARKETING APPROVED
                            </option>
                            <option value="6" {{ $viewUpdate->stage == 6 ? ' selected' : '' }}>6. INDUSTRIAL BATCHES
                            </option>
                            <option value="7" {{ $viewUpdate->stage == 7 ? ' selected' : '' }}>7. LAUNCHING</option>
                        </select>
                    </div>
                    <div class="flex flex-col justify-between md:flex-row">
                        <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="botes">Proposal Note :
                        </label>
                        <textarea id="notes" name="notes" class="notes form-input w-full md:w-3/4 px-2 py-1"
                            rows="3">{{ $viewUpdate->notes}} </textarea>
                    </div>
                    <div class="flex flex-col justify-between md:flex-row">
                        <label class="block w-full md:w-1/4 text-sm font-medium mb-1" for="success">Success Rate :
                        </label>
                        <select id="success" name="success" class="success form-select w-full md:w-3/4 px-2 py-1">
                            <option value="0" {{ $viewUpdate->success == 0 ? ' selected' : '' }}>0</option>
                            <option value="10" {{ $viewUpdate->success == 10 ? ' selected' : '' }}>10</option>
                            <option value="25" {{ $viewUpdate->success == 25 ? ' selected' : '' }}>25</option>
                            <option value="50" {{ $viewUpdate->success == 50 ? ' selected' : '' }}>50</option>
                            <option value="60" {{ $viewUpdate->success == 60 ? ' selected' : '' }}>60</option>
                            <option value="80" {{ $viewUpdate->success == 80 ? ' selected' : '' }}>80</option>
                            <option value="90" {{ $viewUpdate->success == 90 ? ' selected' : '' }}>90</option>
                            <option value="100" {{ $viewUpdate->success == 100 ? ' selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                </div>

                <!-- Modal footer -->
                <div class="px-5 py-4 border-t border-slate-200">
                    <div class="flex flex-wrap justify-end space-x-2">
                        <button class="btn-sm text- bg-indigo-500 hover:bg-indigo-600 text-white"
                            type="submit">Update</button>
                        <div x-data="{ modalOpen: false }">
                            <button type="button" class="ml-2 btn bg-indigo-500 hover:bg-indigo-600 text-white"
                                @click.prevent="modalOpen = true" aria-controls="feedback-modal">
                                <span>Task History</span>
                            </button>
                            <!-- Modal backdrop -->
                            <div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity" x-show="modalOpen"
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-out duration-100"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" aria-hidden="true"
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
                                    @click.outside="modalOpen = false" @keydown.escape.window="modalOpen = false">
                                    <!-- Modal header -->
                                    <div class="px-5 py-3 border-b border-slate-200">
                                        <div class="flex justify-between items-center">
                                            <div class="font-semibold text-slate-800">Task History</div>
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
                                            <table id="proyek1" class="table table-striped table-bordered text-xs"
                                                style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Date</th>
                                                        <th class="text-center">Stage</th>
                                                        <th class="text-center">Project Status</th>
                                                        <th class="text-center">Notes</th>
                                                        <th class="text-center">Success Rate</th>
                                                        <th class="text-center" hidden>Project ID</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($histories as $item)
                                                        <tr>
                                                            <td class="text-center">{{ $item->date }}</td>
                                                            <td>{{ $item->name }}</td>
                                                            <td class="text-center">{{ $item->status_name }}</td>
                                                            <td>{{ $item->notes }}</td>
                                                            <td class="text-center">{{ $item->success_rate }}</td>
                                                            <td hidden>{{ $item->project_id }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
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
                </div>
        </form>
    </div>

    @section('js-page')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        const dataProducts = JSON.parse('<?= json_encode($dataTasksProducts) ?>')
        let tableRow = '';
        let productIdx = 0;
        
        for (const value of dataProducts) {
            console.info(value)
            tableRow += "<tr id=\"row-" + productIdx + "\">\n" +
                "   <td class=\"text-left\">" + value.product_name + "</td>\n" +
                "   <td class=\"text-center\">" + value.m_currency + "<input type=\"hidden\" name = \"rows[" + productIdx + "][m_currency]\" value =" + value.m_currency + "></td>\n" +
                "   <td class=\"text-right\">" + `${divider(value.price)}` + "<input type=\"hidden\" name = \"rows[" + productIdx + "][price]\" value =" + value.price + "></td>\n" +
                "   <td class=\"text-center\">" + value.moq + "<input type=\"hidden\" name = \"rows[" + productIdx + "][minimum_order_qty]\" value =" + value.moq + "></td>\n" +
                "   <td class=\"text-center\">" + value.oq + "<input type=\"hidden\" name = \"rows[" + productIdx + "][order_qty]\" value =" + value.oq + ">" + "<input type=\"hidden\" name = \"rows[" + productIdx + "][product_id]\" value =" + value.product_id + "></td>\n" +
                "   <td class=\"text-center\">" + value.status + "<input type=\"hidden\" name = \"rows[" + productIdx + "][status]\" value =" + value.status + ">" + "<input type=\"hidden\" name = \"rows[" + productIdx + "][product_id]\" value =" + value.product_id + "></td>\n" +
                "   <td class=\"text-center\">" + value.notes + "<input type=\"hidden\" name = \"rows[" + productIdx + "][notes]\" value =" + value.notes + ">" + "<input type=\"hidden\" name = \"rows[" + productIdx + "][product_id]\" value =" + value.product_id + "></td>\n" +
                "   <td class=\"text-center\">" + "<button type=\"button\" onclick=\"deleteDataProduct(" + productIdx + "," + value.id + ")\" class=\"btn border-slate-200 hover:border-slate-300\" > <svg class=\"w-4 h-4 fill-current text-rose-500 shrink-0\" viewBox=\"0 0 16 16\"> <path d=\"M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z\" /> </svg> </button>" + " </td>"
            "</tr>";
            productIdx++;

        }
        $("#tableProductAddBody").append(tableRow);

        function deleteDataProduct(positionTableRow, dataFromDatabase) {
            const positionTableRowVariable = positionTableRow
            const dataFromDatabaseVariable = dataFromDatabase

            if (dataFromDatabaseVariable !== null) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: `/tasks/proyek/proyek-single/deleteproduct/${dataFromDatabaseVariable}`,
                            success: function (response) {
                                console.info("response: ", response)
                                const { status, message } = response;
                                if (status == 1) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        message
                                    )
                                    window.location.reload(true);
                                }
                            },
                            error: function (data) {
                                console.info("error: ", data)
                            }
                        })

                    }
                })

            } else if (dataFromDatabaseVariable === null) {

                $(`#tableProductAddBody tr[id="row-${positionTableRow}"]`).remove();

            }

            console.info('positionTableRowVariable: ', positionTableRowVariable)
            console.info('dataFromDatabaseVariable: ', dataFromDatabaseVariable)
        }

        $(document).ready(function () {

            $('#proyek').DataTable({
                responsive: true,
                processing: true,
                serverSide: false,
                stateServe: true,
                language: {
                    search: "Search Product Name: "
                },
                ajax: {
                    url: "{{ route('proyek.updateproduct') }}"
                },
                columns: [
                    {
                        data: "code",
                        name: "code"
                    },
                    {
                        data: "name",
                        name: "name"
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
                    { className: 'text-center', targets: [0, 2, 3] },
                ], lengthMenu: [[30, 50, 100, -1], [30, 50, 100, 'All']]
            });
            $('#proyek').on("click", ".btn-select", function () {
                const productId = $(this).data("id_product");
                const name = $(this).data("nama_product");


                $(".productId").val(productId);
                $(".nama_product").val(name);
            });

            $("#addProduct").click(function () {
                var id = $('#productId').val();
                var name = $('#nama_product').val();
                var currency = $('#currency').val();
                var price = $('#product_price').val();
                var moq = $('#minimum_quantity_order').val();
                var oq = $('#order_quantity').val();
                var status = $('#status_product').val();
                var notes = $('#product_note').val();

                var tr = " <tr id=\"row-" + productIdx + "\">\n" +
                    "   <td class=\"text-left\">" + name + "</td>\n" +
                    "   <td class=\"text-center\">" + currency + "<input type=\"hidden\" name = \"rows[" + productIdx + "][m_currency]\" value =" + currency + "></td>\n" +
                    "   <td class=\"text-right\">" + `${divider(price)}` + "<input type=\"hidden\" name = \"rows[" + productIdx + "][price]\" value =" + price + "></td>\n" +
                    "   <td class=\"text-center\">" + moq + "<input type=\"hidden\" name = \"rows[" + productIdx + "][minimum_order_qty]\" value =" + moq + "></td>\n" +
                    "   <td class=\"text-center\">" + oq + "<input type=\"hidden\" name = \"rows[" + productIdx + "][order_qty]\" value =" + oq + ">" + "<input type=\"hidden\" name = \"rows[" + productIdx + "][product_id]\" value =" + id + "></td>\n" +
                    "   <td class=\"text-center\">" + status + "<input type=\"hidden\" name = \"rows[" + productIdx + "][status]\" value =" + status + ">" + "<input type=\"hidden\" name = \"rows[" + productIdx + "][product_id]\" value =" + id + "></td>\n" +
                    "   <td class=\"text-center\">" + notes + "<input type=\"hidden\" name = \"rows[" + productIdx + "][notes]\" value =" + notes + ">" + "<input type=\"hidden\" name = \"rows[" + productIdx + "][product_id]\" value =" + id + "></td>\n" +
                    "   <td class=\"text-center\">" + "<button onclick=\"deleteDataProduct(" + productIdx + "," + null + ")\" class=\"btn border-slate-200 hover:border-slate-300\" > <svg class=\"w-4 h-4 fill-current text-rose-500 shrink-0\" viewBox=\"0 0 16 16\"> <path d=\"M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z\" /> </svg> </button>" + " </td>"
                "</tr>";

                $("#tableProductAddBody").append(tr);

                $('#id_product').val('');
                $('#nama_product').val('');
                $('#currency').val('');
                $('#product_price').val('');
                $('#minimum_quantity_order').val('');
                $('#order_quantity').val('');
                $('#status_product').val('');
                $('#product_note').val('');
                productIdx++;
            });

        });

        
    </script>
    @endsection
</x-app-layout>
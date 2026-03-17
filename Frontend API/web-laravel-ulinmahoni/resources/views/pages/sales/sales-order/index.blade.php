<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">
                    Sales Orders List 🗄️
                </h1>
            </div>
        </div>

        <!-- label -->
        <div class="flex flex-row text-xs">
            <label class="flex flex-row text-xs">
                <p class="flex flex-row text-slate-800 mb-3 text-sm mt-2" for="status">Sales Order Status :</p>
                <select id="status" class="status flex flex-row ml-3 mb-3 text-xs" name="status">
                    <option value="">All</option>
                    <option value="0">Pending</option>
                    <option value="1">Close</option>
                    <option value="2">Cancel</option>
                </select>
                <p class="flex flex-row text-sm text-slate-800 mb-3 mt-2 ml-3" for="date">Delivery Date :</p>
                <input id="date" class="date flex flex-row ml-3 mb-3" type="date" />
                <a class="ml-20 btn bg-indigo-500 hover:bg-indigo-600 text-white text-xs @if(Route::is('proyek.form')){{ '!text-indigo-500' }}@endif mb-3"
                    href="{{ route('sales-order.form') }}">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path
                            d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    <span class="ml-2 text-xs">Create Sales Order</span>
                </a>
        </div>
        <div class="table-responsive">
            <table id="sales_order" class="table table-striped table-bordered text-xs" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-center">Delivery Date</th>
                        <th class="text-center">SO Number</th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Created at</th>
                        <th class="text-center">Last Updated at</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @section('js-page')
    <script>
        $(document).ready(function () {
            $('#sales_order').DataTable({
                responsive: true,
                processing: true,
                serverSide: false,
                stateServe: true,
                "order": [[ 6, "desc" ]],
                language: {
                    search: "Search Customer Name: "
                },
                ajax: {
                    url: "{{ route('sales-order.getdata') }}",
                    data:function(d){
                        d.status = $("#status").val()
                        d.date = $("#date").val()
                    }
                },
                columns: [
                    {
                        data: "label",
                        name: "label"
                    },
                    {
                        data: "delivery_date",
                        name: "delivery_date"
                    },
                    {
                        data: "so_number",
                        name: "so_number"
                    },
                    {
                        data: "customername",
                        name: "customername"
                    },
                    {
                        data: "total",
                        name: "total"
                    },
                    {
                        data: "status_name",
                        name: "status_name"
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "updated_at",
                        name: "updated_at"
                    },
                    {
                        data: "action",
                        name: "action"
                    },
                ],
                columnDefs: [
                    { className: 'text-center', targets: [0, 1, 2, 4, 5, 6, 7] },
                    { className: 'flex justify-center', targets: [8] },
                ], lengthMenu: [[30, 50, 100, -1], [30, 50, 100, 'All']]
            }); 
        });

        $(".status").on('change', function (e) {
            $('#sales_order').DataTable().ajax.reload();
        })

        $(".date").on('change', function (e) {
            $('#sales_order').DataTable().ajax.reload();
        })      

        $('#sales_order').on("click", ".btn-modal", function () {
                const id = $(this).data('id');
                const number = $(this).data('number');
                const date = $(this).data('date');
                const by = $(this).data('by');
                const cust = $(this).data('cust');
                const status = $(this).data('status');
                const notes = $(this).data('notes');
                const disc1 = $(this).data('disc1');
                const disc2 = $(this).data('disc2');
                const disc3 = $(this).data('disc3');
                const ppn = $(this).data('ppn');
                const total = $(this).data('total');

                $.ajax({
                    type: "GET",
                    url: `/sales/sales-order/getdetail/${id}`,

                    success: function (response) {

                        $(".modal-content").html(`
                                <div class="px-5">
                                    <div class="grid md:grid-cols-3 gap-3 mt-3">
                                        <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${id}" disabled readonly hidden/>
                                        <div>
                                            <label class="text-sm font-medium mb-1">SO Number</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${number}" disabled readonly />
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium mb-1">Customer Name</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${cust}" disabled readonly />
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium mb-1">Delivery Date</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${date}" disabled readonly />
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium mb-1">Created By</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${by}" disabled readonly />
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium mb-1">Status</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${status}" disabled readonly />
                                        </div>
                                    </div>
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium mb-1 text-left" for="address">Notes</label>
                                            <textarea rows="4" id="address" class="address form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" disabled readonly>${notes}</textarea>
                                        </div>
                                        <div class="table-responsive mt-4">
                                            <label class="block text-sm font-medium text-center mb-1" for="address">Sales Order Product</label>
                                            <table class="table table-striped table-bordered detail-sales-orders"
                                                style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-sm text-center">Product Name</th>
                                                        <th class="text-sm text-center">Price</th>
                                                        <th class="text-sm text-center">Quantity</th>
                                                        <th class="text-sm text-center">Discount</th>
                                                        <th class="text-sm text-center">Sub Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="grid md:grid-cols-3 gap-3 mt-3">
                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">Discount 1</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${disc1}" disabled readonly />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">Discount 2</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${disc2}" disabled readonly />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">Discount 3</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${disc3}" disabled readonly />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">PPN</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${ppn}" disabled readonly />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">Total Amount</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${divider(total)}" disabled readonly />
                                            </div>
                                        </div>

                                    <center>
                                        <a href="/sales/sales-order/print/${id}" target="_blank" class="btn bg-indigo-500 hover:bg-indigo-600 text-white mt-5 mb-5" type="button">
                                            <span class="xs:block ml-5 mr-5">Print Out</span></a>
                                    </center>
                                    
                                </div>
                        `); 
                        let tableRow = '';
                        for (const value of response) {
                            tableRow += `<tr>
                                            <td>${value.product_name}</td>
                                            <td class="text-center">${divider(value.price)}</td>
                                            <td class="text-center">${value.qty}</td>
                                            <td class="text-center">${value.discount}</td>
                                            <td class="text-center">${divider(value.sub_total)}</td>
                                        </tr>`;
                        }

                        $(".detail-sales-orders").find('tbody').append(tableRow);
                    },
                });
        });

        $('#sales_order').on("click", ".btn-update", function () {
                const id = $(this).data('id');
                const number = $(this).data('number');
                const date = $(this).data('date');
                const by = $(this).data('by');
                const cust = $(this).data('cust');
                const status = $(this).data('status');
                const notes = $(this).data('notes');
                const disc1 = $(this).data('disc1');
                const disc2 = $(this).data('disc2');
                const disc3 = $(this).data('disc3');
                const ppn = $(this).data('ppn');
                const total = $(this).data('total');

                $.ajax({
                    type: "GET",
                    url: `/sales/sales-order/getdetail/${id}`,
                    success: function (response) {
                        const csrf_token = $('meta[name="csrf-token"]').attr('content');

                        $(".modal-content").html(`
                                <div class="px-5">
                                    <div class="grid md:grid-cols-3 gap-3 mt-3">
                                        <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${id}" disabled readonly hidden/>
                                        <div>
                                            <label class="text-sm font-medium mb-1">SO Number</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${number}" disabled readonly />
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium mb-1">Customer Name</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${cust}" disabled readonly />
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium mb-1">Delivery Date</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${date}" disabled readonly />
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium mb-1">Created By</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${by}" disabled readonly />
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium mb-1">Status</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${status}" disabled readonly />
                                        </div>
                                    </div>
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium mb-1 text-left" for="address">Notes</label>
                                            <textarea rows="4" id="address" class="address form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" disabled readonly>${notes}</textarea>
                                        </div>
                                        <div class="table-responsive mt-4">
                                            <label class="block text-sm font-medium text-center mb-1" for="address">Sales Order Product</label>
                                            <table class="table table-striped table-bordered detail-sales-orders"
                                                style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-sm text-center">Product Name</th>
                                                        <th class="text-sm text-center">Price</th>
                                                        <th class="text-sm text-center">Quantity</th>
                                                        <th class="text-sm text-center">Discount</th>
                                                        <th class="text-sm text-center">Sub Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="grid md:grid-cols-3 gap-3 mt-3">
                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">Discount 1</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${disc1}" disabled readonly />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">Discount 2</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${disc2}" disabled readonly />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">Discount 3</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${disc3}" disabled readonly />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">PPN</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${ppn}" disabled readonly />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">Total Amount</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${divider(total)}" disabled readonly />
                                            </div>
                                        </div>
                                        <form method="post" class="form_do_update" enctype="multipart/form-data" action="/sales/sales-order/update/${id}">
                                            <input type="hidden" name="_token" value="${csrf_token}" />
                                            <div class="grid md:grid-cols-3 gap-3 mt-5">
                                                <input type="submit" value="Close" name="status" class="btn-sm bg-indigo-500 hover:bg-indigo-600 text-white" />
                                                <div></div>
                                                <input type="submit" value="Cancel" name="status" class="btn-sm bg-red-400 border-slate-200 hover:bg-red-500 text-white" />
                                            </div>
                                        </form>
                                    
                                </div>
                        `); 
                        let tableRow = '';
                        for (const value of response) {
                            tableRow += `<tr>
                                            <td>${value.product_name}</td>
                                            <td class="text-center">${divider(value.price)}</td>
                                            <td class="text-center">${value.qty}</td>
                                            <td class="text-center">${value.discount}</td>
                                            <td class="text-center">${divider(value.sub_total)}</td>
                                        </tr>`;
                        }

                        $(".detail-sales-orders").find('tbody').append(tableRow);
                    },
                });
        });

        $('#sales_order').on("click", ".btn-delete", function () {
                const id = $(this).data('id');
                const number = $(this).data('number');
                const date = $(this).data('date');
                const by = $(this).data('by');
                const cust = $(this).data('cust');
                const status = $(this).data('status');
                const notes = $(this).data('notes');
                const disc1 = $(this).data('disc1');
                const disc2 = $(this).data('disc2');
                const disc3 = $(this).data('disc3');
                const ppn = $(this).data('ppn');
                const total = $(this).data('total');

                $.ajax({
                    type: "GET",
                    url: `/sales/sales-order/getdetail/${id}`,
                    success: function (response) {
                        const csrf_token = $('meta[name="csrf-token"]').attr('content');

                        $(".modal-content").html(`
                                <div class="px-5">
                                    <div class="grid md:grid-cols-3 gap-3 mt-3">
                                        <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${id}" disabled readonly hidden/>
                                        <div>
                                            <label class="text-sm font-medium mb-1">SO Number</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${number}" disabled readonly />
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium mb-1">Customer Name</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${cust}" disabled readonly />
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium mb-1">Delivery Date</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${date}" disabled readonly />
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium mb-1">Created By</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${by}" disabled readonly />
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium mb-1">Status</label>
                                            <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" value="${status}" disabled readonly />
                                        </div>
                                    </div>
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium mb-1 text-left" for="address">Notes</label>
                                            <textarea rows="4" id="address" class="address form-input w-full px-2 py-1 bg-slate-100"
                                            type="text" disabled readonly>${notes}</textarea>
                                        </div>
                                        <div class="table-responsive mt-4">
                                            <label class="block text-sm font-medium text-center mb-1" for="address">Sales Order Product</label>
                                            <table class="table table-striped table-bordered detail-sales-orders"
                                                style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-sm text-center">Product Name</th>
                                                        <th class="text-sm text-center">Price</th>
                                                        <th class="text-sm text-center">Quantity</th>
                                                        <th class="text-sm text-center">Discount</th>
                                                        <th class="text-sm text-center">Sub Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="grid md:grid-cols-3 gap-3 mt-3">
                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">Discount 1</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${disc1}" disabled readonly />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">Discount 2</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${disc2}" disabled readonly />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">Discount 3</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${disc3}" disabled readonly />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">PPN</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${ppn}" disabled readonly />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-left" for="address">Total Amount</label>
                                                <input id="id" class="id form-input w-full px-2 py-1 bg-slate-100"
                                                type="text" value="${divider(total)}" disabled readonly />
                                            </div>
                                        </div>
                                        <form method="post" class="form_sales_update" enctype="multipart/form-data" action="/sales/sales-order/update/${id}">
                                            <input type="hidden" name="_token" value="${csrf_token}" />
                                            <div class="grid md:grid-cols-3 gap-3 mt-5">
                                                <div></div>
                                                <input type="submit" value="Delete" name="status" class="btn-sm bg-red-400 border-slate-200 hover:bg-red-500 text-white" />
                                                <div></div>
                                            </div>
                                        </form>   
                                </div>
                        `); 
                        let tableRow = '';
                        for (const value of response) {
                            tableRow += `<tr>
                                            <td>${value.product_name}</td>
                                            <td class="text-center">${divider(value.price)}</td>
                                            <td class="text-center">${value.qty}</td>
                                            <td class="text-center">${value.discount}</td>
                                            <td class="text-center">${divider(value.sub_total)}</td>
                                        </tr>`;
                        }

                        $(".detail-sales-orders").find('tbody').append(tableRow);
                    },
                });
        });
    </script>
    @endsection
</x-app-layout>
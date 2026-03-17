<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">
                    Inventory List 📋
                </h1>
            </div>
        </div>

        {{-- color --}}
        <div class="flex flex-row mb-3">
            <div class="flex flex-row rounded-full bg-green-700 h-5 w-5"></div>
            <p class="flex flex-row ml-1">Active</p>
            <div class="rounded-full bg-black md:break-after-column h-5 w-5 ml-5"></div>
            <p class="flex flex-row ml-1">Not Active</p>
        </div>

         <!-- label -->
         <div class="flex flex-row text-xs">
            <label class="flex flex-row text-xs">
                <p class="flex flex-row text-slate-800 mb-3 text-sm mt-2" for="status">Project Status :</p>
                <select id="status" class="status flex flex-row ml-3 mb-3 text-xs" name="status">
                    <option value="">All</option>
                    <option value="1">Active</option>
                    <option value="0">Not Active</option>
                </select>
                <p class="flex flex-row text-slate-800 mb-3 text-sm mt-2 ml-3" for="hidden_zero">Show 0 (zero) Stock :</p>
                <select id="hidden_zero" class="hidden_zero flex flex-row ml-3 mb-3 text-xs" name="hidden_zero">
                    <option value="0">Yes</option>
                    <option value="1" selected>No</option>
                </select>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table id="invlist" class="table table-striped table-bordered text-xs" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th class="text-center">Inventory Code</th>
                        <th class="text-center">Inventory Name</th>
                        <th class="text-center">Unit</th>
                        <th class="text-center">Ready Goods</th>
                        <th class="text-center">Purchase Order (in Transit)</th>
                        <th class="text-center">Sales Order (awaiting Delivery)</th>
                        <th class="text-center">Nett Goods</th>
                        <th class="text-center">Damage/Quarantine Goods</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @section('js-page')
    <script>
        $(document).ready(function () {
            $('#invlist').DataTable({
                responsive: true,
                processing: true,
                serverSide: false,
                stateServe: true,
                "order": [[ 2, "asc" ]],
                language: {
                    search: "Search Inventory Name: "
                },
                ajax: {
                    url: "{{ route('invlist.getdata') }}",
                    data:function(d){
                        d.status = $("#status").val()
                        d.stock = $("#hidden_zero").val()
                    }
                },
                columns: [
                    {
                        data: "label",
                        name: "label"
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
                        data: "unit",
                        name: "unit"
                    },
                    {
                        data: "global_stock",
                        name: "global_stock"
                    },
                    {
                        data: "purchased",
                        name: "purchased"
                    },
                    {
                        data: "reserved_so",
                        name: "reserved_so"
                    },
                    {
                        data: "nett_stock",
                        name: "nett_stock"
                    },
                    {
                        data: "broken_stock",
                        name: "broken_stock"
                    },
                    {
                        data: "action",
                        name: "action"
                    },
                ],
                columnDefs: [
                    { className: 'text-center', targets: [1, 3, 4, 5, 6, 7, 8] },
                    { className: 'flex justify-center', targets: [9] },
                ], lengthMenu: [[30, 50, 100, -1], [30, 50, 100, 'All']]
            });
        });

        $(".status").on('change', function (e) {
                e.preventDefault();
                $('#invlist').DataTable().ajax.reload();
            })

        $(".hidden_zero").on('change', function (e) {
            $('#invlist').DataTable().ajax.reload();
        })

        $('#invlist').on("click", ".btn-modal", function () {
                const id = $(this).data('id');
                const code = $(this).data("code");
                const file1 = $(this).data("file1");
                const file2 = $(this).data("file2");
                const file3 = $(this).data("file3");
                const file1_name = $(this).data("file1_name");
                const file2_name = $(this).data("file2_name");
                const file3_name = $(this).data("file3_name");

                $.ajax({
                    type: "GET",
                    url: `/inventory/invlist/getdetail/${code}`,
                    success: function (response) {

                        $(".modal-content").html(`
                                <div class="px-5">
                                    <div class="grid md:grid-cols-3 gap-3 mt-3">
                                        <div></div>
                                        <div class="${file3 == 1 ? '' : 'hidden'}">
                                            <label class="text-sm font-medium mb-1">Image Not Uploaded Yet</label>
                                        </div>
                                        <div></div>
                                    </div>
                                    <div class="grid md:grid-cols-3 gap-3 mt-3">
                                        <div class="${file1 == 1 ? 'hidden' : ''}">
                                            <label class="text-sm font-medium mb-1">AWB DO :</label>
                                            <a href="/inventory/invlist/file1/${code}" target="_blank" class="text-sm font-medium ml-5">View Image</a>
                                            <img class="w-full mt-3" src="http://nnb.integrated-os.cloud/${file1_name}" width="259" height="142" alt="Product Image" />
                                        </div>
                                        <div class="${file2 == 1 ? 'hidden' : ''}">
                                            <label class="text-sm font-medium mb-1">Signed By :</label>
                                            <a href="/inventory/invlist/file2/${code}" target="_blank" class="text-sm font-medium ml-5">View Image</a>
                                            <img class="w-full mt-3" src="http://nnb.integrated-os.cloud/${file2_name}" width="259" height="142" alt="Product Image" />
                                        </div>
                                        <div class="${file3 == 1 ? 'hidden' : ''}">
                                            <label class="text-sm font-medium mb-1">Signed By :</label>
                                            <a href="/inventory/invlist/file3/${code}" target="_blank" class="text-sm font-medium ml-5">View Image</a>
                                            <img class="w-full mt-3" src="http://nnb.integrated-os.cloud/${file3_name}" width="259" height="142" alt="Product Image" />
                                        </div>
                                    </div>
                                </div>
                        `); 
                    },
                });
            });
    </script>
    @endsection
</x-app-layout>
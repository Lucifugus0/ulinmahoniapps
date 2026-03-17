<?php

namespace App\Http\Controllers\salesorder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

use function Ramsey\Uuid\v1;

class SalesOrderController extends Controller
{
    protected $saveImageUrl;
    protected $baseImageUrl;
    
    public function __construct()
    {
        $this->saveImageUrl = config('app.save_url_file');
        $this->baseImageUrl = config('app.base_url_file');
    }

    public function index()
    {
        $dataSales = DB::table('sales_orders')->select('sales_orders.id')->get();
        return view('pages.sales.sales-order.index', compact('dataSales'));
    }

    public function form()
    {
        $dataCust = DB::table('customers')->select('*')->get();
        $dataProduct = DB::table('customers')->select('*')->get();
        return view('pages.sales.sales-order.create-order', compact('dataCust', 'dataProduct'));
    }

    public function getData(Request $request)
    {
        $userRole = Auth::user()->role;
        $salesId = Auth::user()->sales_id;
        $salesDate = $request->input('date');
        $dataSalesOrders = DB::table('sales_orders')
            ->leftJoin('customers', 'sales_orders.customer_id', 'customers.id')
            ->leftJoin('users', 'sales_orders.created_by', 'users.id')
            ->select(
                'sales_orders.id',
                'sales_orders.so_number',
                'sales_orders.delivery_date',
                'sales_orders.customer_id',
                'sales_orders.sales_id',
                'sales_orders.discount_1',
                'sales_orders.discount_2',
                'sales_orders.discount_3',
                'sales_orders.ppn',
                'sales_orders.total',
                'sales_orders.status',
                'sales_orders.created_at',
                'sales_orders.updated_at',
                'sales_orders.created_by',
                'sales_orders.updated_by',
                'sales_orders.notes',
                'customers.name as customername',
                'users.username as user',
                DB::raw("
                case
                    when sales_orders.status = 0 then 'Pending'
                    when sales_orders.status = 1 then 'Close'
                    when sales_orders.status = 2 then 'Cancel'
                    when sales_orders.status = 3 then 'Delete'
                    else 'unknown status'
                end as status_name
                ")
            )->where('sales_orders.status', '!=', 3)->orderBy('sales_orders.created_at', 'desc');

        if ($userRole == '201' || $userRole == '202' || $userRole == '203'){
            $dataSalesOrders->where('sales_orders.sales_id', $salesId);
        }

        if ($request->input('status') != null) {
                $dataSalesOrders = $dataSalesOrders->where('sales_orders.status', $request->status);
        }

        if ($salesDate != null) {
                $dataSalesOrders->whereDate('sales_orders.delivery_date', $request->date);
        }

        if ($request->ajax()) {
            return DataTables::of($dataSalesOrders)
                ->editColumn('total', function ($dataSalesOrders) {
                    return number_format($dataSalesOrders->total, 0, ',', '.')."";
                })
                ->editColumn('created_at', function ($dataSalesOrders) {
                    return date('Y-m-d', strtotime($dataSalesOrders->created_at));
                })
                ->editColumn('updated_at', function ($dataSalesOrders) {
                    return date('Y-m-d', strtotime($dataSalesOrders->updated_at));
                })
                ->editColumn('delivery_date', function ($dataSalesOrders) {
                    return date('Y-m-d', strtotime($dataSalesOrders->delivery_date));
                })
                ->addColumn('label', function ($dataSalesOrders) {

                    $status = ($dataSalesOrders->status);
                    $color = "color";

                    if ($status == '0') {
                        $color = "yellow";
                    } else if ($status == '1') {
                        $color = "black";
                    } else if ($status == '2') {
                        $color = "red";
                    } else if ($status == '3') {
                        $color = "grey";
                    }
                    return '<div class="h-4 w-4 rounded-full" style="background-color: ' . $color . '"></div>';
                })
                ->addColumn('action', function ($dataSalesOrders) {
                    return '<div class="flex flex-row">
                            <div x-data="{ modalOpen: false }">
                                <button  class="btn btn-sm btn-modal text-sm bg-indigo-500 hover:bg-indigo-600 text-white ml-1" 
                                    @click.prevent="modalOpen = true" aria-controls="scrollbar-modal" data-id="'.$dataSalesOrders->id.'" data-number="'.$dataSalesOrders->so_number.'" data-date="'.$dataSalesOrders->delivery_date.'"
                                    data-by="'.$dataSalesOrders->user.'" data-cust="'.$dataSalesOrders->customername.'" data-status="'.$dataSalesOrders->status_name.'" data-notes="'.$dataSalesOrders->notes.'" data-disc1="'.$dataSalesOrders->discount_1.'"
                                    data-disc2="'.$dataSalesOrders->discount_2.'" data-disc3="'.$dataSalesOrders->discount_3.'" data-ppn="'.$dataSalesOrders->ppn.'" data-total="'.$dataSalesOrders->total.'"
                                >View Detail</button>
                                
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
                                    <div class="bg-white rounded shadow-lg overflow-auto w-full max-h-full"
                                        @click.outside="modalOpen = false" @keydown.escape.window="modalOpen = false">
                                        <!-- Modal header -->
                                        <div class="px-5 py-3 border-b border-slate-200">
                                            <div class="flex justify-between items-center">
                                                <div class="font-semibold text-slate-800">Sales Order Detail</div>
                                                <button class="text-slate-400 hover:text-slate-500"
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
                                        <div class="modal-content">
                                            
                                        </div>
                                        <!-- Modal footer -->
                                        <div class="px-5 py-4 border-t border-slate-200">
                                            <div class="flex flex-wrap justify-end space-x-2">
                                                <button class="btn-sm border-slate-200 hover:border-slate-300 text-slate-600" @click="modalOpen = false">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div x-data="{ modalOpen: false }">
                                <button  class="btn btn-sm btn-update text-sm bg-emerald-500 hover:bg-emerald-600 text-white ml-1" 
                                    @click.prevent="modalOpen = true" aria-controls="scrollbar-modal" data-id="'.$dataSalesOrders->id.'" data-number="'.$dataSalesOrders->so_number.'" data-date="'.$dataSalesOrders->delivery_date.'"
                                    data-by="'.$dataSalesOrders->user.'" data-cust="'.$dataSalesOrders->customername.'" data-status="'.$dataSalesOrders->status_name.'" data-notes="'.$dataSalesOrders->notes.'" data-disc1="'.$dataSalesOrders->discount_1.'"
                                    data-disc2="'.$dataSalesOrders->discount_2.'" data-disc3="'.$dataSalesOrders->discount_3.'" data-ppn="'.$dataSalesOrders->ppn.'" data-total="'.$dataSalesOrders->total.'"
                                >Update Status</button>
                                
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
                                    <div class="bg-white rounded shadow-lg overflow-auto w-full max-h-full"
                                        @click.outside="modalOpen = false" @keydown.escape.window="modalOpen = false">
                                        <!-- Modal header -->
                                        <div class="px-5 py-3 border-b border-slate-200">
                                            <div class="flex justify-between items-center">
                                                <div class="font-semibold text-slate-800">Updat Status Sales Order</div>
                                                <button class="text-slate-400 hover:text-slate-500"
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
                                        <div class="modal-content">
                                            
                                        </div>
                                        <!-- Modal footer -->
                                        <div class="px-5 py-4 border-t border-slate-200">
                                            <div class="flex flex-wrap justify-end space-x-2">
                                                <button class="btn-sm border-slate-200 hover:border-slate-300 text-slate-600" @click="modalOpen = false">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div x-data="{ modalOpen: false }">
                                <button  class="btn btn-sm btn-delete text-sm bg-red-600 hover:bg-red-600 text-white ml-1" 
                                    @click.prevent="modalOpen = true" aria-controls="scrollbar-modal" data-id="'.$dataSalesOrders->id.'" data-number="'.$dataSalesOrders->so_number.'" data-date="'.$dataSalesOrders->delivery_date.'"
                                    data-by="'.$dataSalesOrders->user.'" data-cust="'.$dataSalesOrders->customername.'" data-status="'.$dataSalesOrders->status_name.'" data-notes="'.$dataSalesOrders->notes.'" data-disc1="'.$dataSalesOrders->discount_1.'"
                                    data-disc2="'.$dataSalesOrders->discount_2.'" data-disc3="'.$dataSalesOrders->discount_3.'" data-ppn="'.$dataSalesOrders->ppn.'" data-total="'.$dataSalesOrders->total.'"
                                >Delete</button>
                                
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
                                    <div class="bg-white rounded shadow-lg overflow-auto w-full max-h-full"
                                        @click.outside="modalOpen = false" @keydown.escape.window="modalOpen = false">
                                        <!-- Modal header -->
                                        <div class="px-5 py-3 border-b border-slate-200">
                                            <div class="flex justify-between items-center">
                                                <div class="font-semibold text-slate-800">Delete Sales Order</div>
                                                <button class="text-slate-400 hover:text-slate-500"
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
                                        <div class="modal-content">
                                            
                                        </div>
                                        <!-- Modal footer -->
                                        <div class="px-5 py-4 border-t border-slate-200">
                                            <div class="flex flex-wrap justify-end space-x-2">
                                                <button class="btn-sm border-slate-200 hover:border-slate-300 text-slate-600" @click="modalOpen = false">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>';
                })
                ->rawColumns(['label', 'action'])
                ->make();
        }
    }

    public function getDetail($salesId)
    {
        $dataDetailSo = DB::table('sales_orders')
            ->leftJoin('sales_order_items', 'sales_orders.id', 'sales_order_items.sales_order_id')
            ->join('products', 'sales_order_items.product_id', 'products.id')
            ->select(
            'sales_orders.id',
            'sales_orders.so_number',
            'sales_order_items.product_id', 'sales_order_items.qty', 'sales_order_items.price',
            'sales_order_items.discount', 'sales_order_items.sub_total', 'products.name as product_name')
            ->where('sales_orders.id', $salesId)
            ->get()->toArray();

        return $dataDetailSo;
    }

    public function getCustomer(Request $request)
    {
        $dataCompany = DB::table('customers')
            ->select(
                'customers.id as customer_id',
                'customers.name as customer',
                'customers.address',
                'customers.city',
                'customers.country',
                'customers.phone_number'
            );
        if ($request->ajax()) {
            return DataTables::of($dataCompany)
                ->addColumn('action', function ($dataCompany) {
                    return '<button
                    type="button" class="btn btn-xs btn-select text-sm bg-indigo-500 hover:bg-indigo-600 text-white" @click="modalOpen = false"  data-id="' . $dataCompany->customer_id . '"
                    data-nama="' . $dataCompany->customer . '" id="select"
                >Select</button>';
                })
                ->rawColumns(['action'])
                ->make();
        }
    }

    public function selectCustomer($customerId)
    {
        $customerData = DB::table('company_pics')
            ->where('company_id', $customerId)
            ->select('id', 'name', 'phone_number_1', 'phone_number_2', 'email')
            ->get()->toArray();

        return response()->json([
            'listCompanyPic' => $customerData
        ]);
    }

    public function getProduct(Request $request)
    {
        $dataProduct = DB::table('products')
            ->select(
                'products.id',
                'products.code',
                'products.name',
                'products.unit',
                'products.global_stock',
                'products.nett_stock'
            )->where('global_stock', '>', 0);
        if ($request->ajax()) {
            return DataTables::of($dataProduct)
                ->addColumn('action', function ($dataProduct) {

                    return '<button
                    type="button" class="btn btn-xs btn-select text-sm bg-indigo-500 hover:bg-indigo-600 text-white" @click="modalOpen = false"  data-id_product="' . $dataProduct->id . '"
                    data-nama_product="' . $dataProduct->name . '" data-code="' . $dataProduct->code . '" data-unit="' . $dataProduct->unit . '" data-stock="' . $dataProduct->global_stock . '" id="select"
                >Select</button>';
                })
                ->rawColumns(['action'])
                ->make();
        }
    }
    public function create(Request $request)
    {
        // return $request->all();

        $yearNow = date('Y');
        $maxId = DB::table('sales_orders')
            ->max('so_number');

        $yearNowSubstring = substr($yearNow, -2);
        $maxIdSubstring = substr($maxId, 0, 2);
        
        if (is_null($maxId)) {
            $soNumber = $yearNowSubstring . 'MMP-SO001';
        } else {
            if ($maxIdSubstring == $yearNowSubstring) {
                $runningNumber = substr($maxId, -3);
                $newRunningNumber = $runningNumber + 1;
                $soNumber = $yearNowSubstring . 'MMP-SO' . str_pad($newRunningNumber, 3, '0', STR_PAD_LEFT);
            } else {
                $soNumber = $yearNowSubstring . 'MMP-SO001';
            }
        }
        
        $rowsProducts = $request->get('rows');
        $cust = $request->input('customer1');

       if ($rowsProducts && $cust != null) {
            if ($request->input('customerId') == null) {
                $customer = DB::table('customers')->insertGetId([
                    'name' => $request->input('customerName'),
                    'address' => $request->input('address'),
                    'city' => $request->input('city'),
                    'country' => $request->input('country'),
                    'phone_number' => $request->input('phone')
                ]);

                $id = DB::table('sales_orders')->insertGetId([
                    'so_number' => $soNumber,
                    'delivery_date' => $request->input('date'),
                    'customer_id' => $customer,
                    'sales_id' => Auth::user()->sales_id,
                    'discount_1' => $request->input('discount1'),
                    'discount_2' => $request->input('discount2'),
                    'discount_3' => $request->input('discount3'),
                    'discount1_idr' => $request->input('discount1idr'),
                    'discount2_idr' => $request->input('discount2idr'),
                    'discount3_idr' => $request->input('discount3idr'),
                    'total_idr' => $request->input('total'),
                    'ppn_idr' => $request->input('ppnidr'),
                    'ppn' => $request->input('ppn'),
                    'total' => $request->input('totalAmount1'),
                    'status' => '0',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'notes' => $request->input('notes')
                ]);
                $salesOrderId = $id;
    
                    foreach ($rowsProducts as $key) {
                        DB::table('sales_order_items')->insert([
                            'sales_order_id' => $salesOrderId,
                            'product_id' => $key['ids'],
                            'qty' => $key['oqs'],
                            'price' => $key['prices'],
                            'discount' => $key['discs'],
                            'sub_total' => $key['subtotals'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
            } else {
                $id = DB::table('sales_orders')->insertGetId([
                    'so_number' => $soNumber,
                    'delivery_date' => $request->input('date'),
                    'customer_id' => $request->input('customerId'),
                    'sales_id' => Auth::user()->sales_id,
                    'discount_1' => $request->input('discount1'),
                    'discount_2' => $request->input('discount2'),
                    'discount_3' => $request->input('discount3'),
                    'discount1_idr' => $request->input('discount1idr'),
                    'discount2_idr' => $request->input('discount2idr'),
                    'discount3_idr' => $request->input('discount3idr'),
                    'ppn' => $request->input('ppn'),
                    'total' => $request->input('totalAmount1'),
                    'status' => '0',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'notes' => $request->input('notes')
                ]);
                $salesOrderId = $id;
    
                    foreach ($rowsProducts as $key) {
                        DB::table('sales_order_items')->insert([
                            'sales_order_id' => $salesOrderId,
                            'product_id' => $key['ids'],
                            'qty' => $key['oqs'],
                            'price' => $key['prices'],
                            'discount' => $key['discs'],
                            'sub_total' => $key['subtotals'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
            }
            alert()->success('Sukses', 'Sales Order Has Been Created');
            return to_route('sales-order');
        } else {
            alert()->error('Gagal', 'Error Product or Customer not Fill');
            return to_route('sales-order');
        }
    }

    public function updateSO(Request $request, $salesId)
    {  
        $status = $request->input('status');

        if ($status == 'Delete') {
            $statusUpdate = "3";
        } else if ($status == 'Close') {
            $statusUpdate = "1";
        } else if ($status == 'Cancel') {
            $statusUpdate = "2";
        }

        $updateOrders = DB::table('sales_orders')
        ->where('id', $salesId)
        ->update([
            'status' => $statusUpdate,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
            
        if ($updateOrders) {
            alert()->success('Sukses', 'Sales Orders Status Has Been Updated');
            return to_route('sales-order');
        } else {
            alert()->error('Gagal', 'Error Occurred');
            return to_route('sales-order');
        }   
    }

    public function print(Request $request, $salesId)
    {  
        $dataSalesOrders = DB::table('sales_orders')
            ->leftJoin('sales_order_items', 'sales_orders.id', 'sales_order_items.sales_order_id')
            ->leftJoin('customers', 'sales_orders.customer_id', 'customers.id')
            ->leftJoin('users', 'sales_orders.sales_id', 'users.sales_id')
            ->select(
                'sales_orders.id',
                'sales_orders.so_number',
                'sales_orders.delivery_date',
                'sales_orders.customer_id',
                'sales_orders.sales_id',
                'sales_orders.discount_1',
                'sales_orders.discount_2',
                'sales_orders.discount_3',
                'sales_orders.discount1_idr',
                'sales_orders.discount2_idr',
                'sales_orders.discount3_idr',
                'sales_orders.total_idr',
                'sales_orders.ppn_idr',
                'sales_orders.ppn',
                'sales_orders.total',
                'sales_orders.status',
                'sales_orders.created_at',
                'sales_orders.updated_at',
                'sales_orders.created_by',
                'sales_orders.updated_by',
                'sales_orders.notes',
                'sales_order_items.sub_total',
                'customers.name as customername',
                'users.username as user',
                DB::raw("
                case
                    when sales_orders.status = 0 then 'Pending'
                    when sales_orders.status = 1 then 'Close'
                    when sales_orders.status = 2 then 'Cancel'
                    when sales_orders.status = 3 then 'Delete'
                    else 'unknown status'
                end as status_name
                ")
            )->where('sales_orders.id', $salesId)->first();

        $dataDetailSo = DB::table('sales_order_items')
            ->leftJoin('sales_orders', 'sales_order_items.sales_order_id', 'sales_orders.id')
            ->join('products', 'sales_order_items.product_id', 'products.id')
            ->select(
            'sales_orders.id',
            'sales_orders.so_number',
            'sales_order_items.product_id', 'sales_order_items.qty', 'sales_order_items.price',
            'sales_order_items.discount', 'sales_order_items.sub_total', 'products.name as product_name')
            ->where('sales_order_items.sales_order_id', $salesId)->get();

       return view('pages.sales.sales-order.printso', compact('dataSalesOrders', 'dataDetailSo'));
    }
    
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use finfo;
use Illuminate\Support\Facades\Response;

class SearchProductController extends Controller
{
    public function index()
    {
        return view('pages.inventory.search-product');
    }

    public function getData(Request $request)
    {
        $hiddenzero = $request->input('stock');
        
        $dataInvListQuery = DB::table('products')
            ->select('products.id', 'products.code', 'products.name', 'products.global_stock', 'products.broken_stock', 'products.purchased', 'products.reserved_so', 'products.nett_stock', 'products.status', 'products.unit', 'products.file1_name', 'products.file2_name', 'products.file3_name',
            DB::raw("ISNULL(products.file_1) as file1"),
            DB::raw("ISNULL(products.file_2) as file2"),
            DB::raw("ISNULL(products.file_3) as file3"));

            if ($request->input('status') != null) {
                $dataInvListQuery = $dataInvListQuery->where('products.status', $request->status);
            }

            if ($hiddenzero == 1){
                $dataInvListQuery->where('products.global_stock', '>', 0);
            }

            $dataInvList = $dataInvListQuery->orderBy('products.name', 'asc');

        if ($request->ajax()) {
            return DataTables::of($dataInvList)
            ->addColumn('label', function ($dataInvList) {

                $status = ($dataInvList->status);
                    $color = "color";

                    if ($status == '1') {
                        $color = "green";
                    } else if ($status == '0') {
                        $color = "black";
                    }
                    return '<div class="h-4 w-4 rounded-full" style="background-color: ' . $color . '">';
                })
            ->addColumn('action', function ($dataInvList) {
                return '
                <div class="flex flex-row">
                    <div x-data="{ modalOpen: false }">
                        <button  class="btn btn-sm btn-modal text-sm bg-indigo-500 hover:bg-indigo-600 text-white ml-1" 
                            @click.prevent="modalOpen = true" aria-controls="scrollbar-modal" data-id="'.$dataInvList->id.'" data-code = "' . $dataInvList->code . '" data-name = "' . $dataInvList->name . '" data-stock ="'.$dataInvList->global_stock.'" data-stat = "' . $dataInvList->status . '"
                            data-file1 = "' . $dataInvList->file1 . '" data-file2 = "' . $dataInvList->file2 . '" data-file3 = "' . $dataInvList->file3 . '" data-file1_name = "' . $dataInvList->file1_name . '" data-file2_name = "' . $dataInvList->file2_name . '" data-file3_name = "' . $dataInvList->file3_name . '"

                        >View Image</button>
                        
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
                                        <div class="font-semibold text-slate-800">Product Image</div>
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
                                <div class="modal-content text-xs">
                                    
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

    public function getDetail($code)
    {
        $dataDetailInv = DB::table('products')
        ->select('products.id', 'products.code', 'products.name', 'products.global_stock', 'products.status', 'products.unit')
        ->where('products.code', $code)->get()->toArray();

        return $dataDetailInv;
    }

    public function viewPhoto1($code)
    {
        $data = DB::table('products')->where('products.code', $code)->select('file_1')->first();

        return Response::make($data->file_1, 200, [
            'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($data->file_1)
        ]);
    }

    public function viewPhoto2($code)
    {
        $data = DB::table('products')->where('products.code', $code)->select('file_2')->first();

        return Response::make($data->file_2, 200, [
            'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($data->file_2)
        ]);
    }

    public function viewPhoto3($code)
    {
        $data = DB::table('products')->where('products.code', $code)->select('file_3')->first();

        return Response::make($data->file_3, 200, [
            'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($data->file_3)
        ]);
    }
}

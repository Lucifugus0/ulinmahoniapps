<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::paginate(10);
        $invoices_count = Invoice::all()->count();
        return view('pages/ecommerce/invoices', compact('invoices', 'invoices_count'));  
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::simplePaginate(10);
        $customers_count = Customer::all()->count();
        return view('pages/ecommerce/customers', compact('customers', 'customers_count'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index01()
    {
        $transactions = Transaction::simplePaginate(10);
        return view('pages/finance/transactions', compact('transactions'));  
    }

    public function index02()
    {
        $transactions = Transaction::simplePaginate(10);
        return view('pages/finance/transaction-details', compact('transactions'));  
    }    
}

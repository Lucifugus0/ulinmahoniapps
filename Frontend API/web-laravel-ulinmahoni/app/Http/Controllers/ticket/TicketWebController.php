<?php

namespace App\Http\Controllers\ticket;

use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use Illuminate\Http\Request;

/**
 * TicketWebController — serves the dedicated /tickets page for web portal customers.
 * The ticket data is loaded via AJAX calls to the existing API endpoints,
 * so this controller only renders the initial Blade view.
 */
class TicketWebController extends Controller
{
    /**
     * Display the tickets page for authenticated customers.
     * The page uses Alpine.js + fetch() to interact with /api/v1/tickets endpoints.
     */
    public function index(Request $request)
    {
        /** Get ticket categories for the create-ticket modal */
        $categories = TicketCategory::active()->get()->groupBy('ticket_type');

        return view('pages.tickets.index', compact('categories'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::paginate(9);
        return view('pages/campaigns', compact('campaigns'));  
    }
}

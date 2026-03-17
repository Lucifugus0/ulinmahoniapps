<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::paginate(10);
        return view('pages/job/job-listing', compact('jobs'));  
    }
}

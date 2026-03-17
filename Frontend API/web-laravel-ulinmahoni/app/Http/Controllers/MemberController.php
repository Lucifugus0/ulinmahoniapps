<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller
{
    public function indexTabs()
    {
        $members = Member::paginate(8);
        return view('pages/community/users-tabs', compact('members'));  
    }

    public function indexTiles()
    {
        $members = Member::paginate(9);
        return view('pages/community/users-tiles', compact('members'));  
    }    
}

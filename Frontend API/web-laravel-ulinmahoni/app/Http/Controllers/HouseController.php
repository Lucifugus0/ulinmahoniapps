<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class HouseController extends Controller
{
    public function show($id)
    {
        $house = Property::findOrFail($id);
        return view('pages.house.show', compact('house'));
    }
} 
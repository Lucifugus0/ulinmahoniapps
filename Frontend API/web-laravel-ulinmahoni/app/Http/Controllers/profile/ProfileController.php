<?php

namespace App\Http\Controllers\profile;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    { 
        $user = DB::table('users')
        ->select('*');
        return view('resources.views.profile', compact('user'));
    }

    public function update(Request $request)
    {
        // return $request->all();
        $data = $request->all();

        $userId = Auth::user()->id;
        $dataGcal = [
            'g_cal' => $data['g_cal']
        ];
        

        try {
            DB::transaction(function () use ($userId, $dataGcal) {
                DB::table('users')->where('id', $userId)->update($dataGcal);
            });
            alert()->success('Sukses', 'Google Calendar successfully added');
            return to_route('google-calendar');
        } catch (\Throwable $th) {
            dd($th->getMessage());
            alert()->error('Gagal', 'Error Occurred');
            return to_route('profile.show');
        }
    }
}

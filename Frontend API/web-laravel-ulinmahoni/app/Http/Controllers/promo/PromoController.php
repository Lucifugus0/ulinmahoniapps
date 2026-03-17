<?php

namespace App\Http\Controllers\promo;

use App\Http\Controllers\Controller;
use App\Models\PromoBanner;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promos = PromoBanner::where('status', 1)
            ->with(['primaryImage', 'images'])
            ->orderByDesc('idrec')
            ->get()
            ->map(function ($promo) {
                // Get primary image or first image from images relationship
                $image = null;
                if ($promo->primaryImage) {
                    $image = $promo->primaryImage->image;
                } elseif ($promo->images->isNotEmpty()) {
                    $image = $promo->images->first()->image;
                }

                return [
                    'id' => $promo->idrec,
                    'title' => $promo->title,
                    'image' => $image,
                    'badge' => 'Promo',
                    'description' => $promo->descriptions,
                    'promo_code' => $promo->promo_code,
                ];
            });

        return view('components.homepage.promos', compact('promos'));
    }

    public function show($id)
    {
        $promo = PromoBanner::where('status', 1)
            ->with(['primaryImage', 'images'])
            ->where('idrec', $id)
            ->first();

        if (!$promo) {
            abort(404);
        }

        // Get primary image or first image from images relationship
        $image = null;
        if ($promo->primaryImage) {
            $image = $promo->primaryImage->image;
        } elseif ($promo->images->isNotEmpty()) {
            $image = $promo->images->first()->image;
        }

        // Get all images for gallery
        $allImages = $promo->images->map(function ($img) {
            return [
                'id' => $img->idrec,
                'image' => $img->image,
                'thumbnail' => $img->thumbnail,
                'caption' => $img->caption,
            ];
        })->toArray();

        $promoArr = [
            'id' => $promo->idrec,
            'title' => $promo->title,
            'image' => $image,
            'images' => $allImages,
            'badge' => 'Promo',
            'description' => $promo->descriptions,
            'promo_code' => $promo->promo_code,
        ];

        // Add additional details for the promo page
        $promoArr['terms_conditions'] = [
            'Syarat dan ketentuan berlaku',
            'Tidak dapat digabung dengan promo lain',
            'Periode promo terbatas',
            'Hanya berlaku untuk pengguna secara terbatas'
        ];

        $promoArr['how_to_claim'] = $promo->how_to_claim ?? [
            'Pilih properti yang diinginkan',
            'Lalu Pilih Kamar yang diinginkan',
            'Masukkan Kode Voucher saat checkout',
            'Lakukan Checkout',
            'Nikmati promo/diskon yang didapatkan'
        ];

        return view('promos.show', ['promo' => $promoArr]);
    }
}

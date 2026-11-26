<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WilayahService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function provinsi(WilayahService $wilayah): JsonResponse
    {
        return response()->json([
            'data' => $wilayah->daftarProvinsi(),
        ]);
    }

    public function kabupaten(Request $permintaan, WilayahService $wilayah): JsonResponse
    {
        $valid = $permintaan->validate([
            'provinsi_id' => ['required', 'string', 'max:10', 'regex:/^\d+$/'],
        ]);

        return response()->json([
            'data' => $wilayah->daftarKabupaten($valid['provinsi_id']),
        ]);
    }
}

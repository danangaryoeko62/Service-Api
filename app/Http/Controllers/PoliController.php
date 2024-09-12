<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use App\Services\SatuSehatService;
use Illuminate\Http\Request;

class PoliController extends Controller
{
    protected $satuSehatService;

    public function __construct(SatuSehatService $satuSehatService)
    {
        $this->satuSehatService = $satuSehatService;
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|string|email|max:255',
            'url' => 'required|string|max:255',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ]);

        // Simpan data ke database
        $poli = Poli::create($validatedData);

        // Kirim data ke API SatuSehat
        $response = $this->satuSehatService->sendPoliData($poli);

        if ($response['status'] === 'success') {
            return response()->json([
                'message' => 'Data Poli berhasil disimpan dan dikirim ke SatuSehat',
                'poli' => $poli,
                'response' => $response['data'],
            ], 201);
        } else {
            return response()->json([
                'message' => 'Data Poli berhasil disimpan tetapi gagal dikirim ke SatuSehat',
                'poli' => $poli,
                'error' => $response['error'],
            ], 500);
        }
    }
}

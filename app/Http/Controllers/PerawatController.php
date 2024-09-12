<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perawat;
use App\Models\Poli;

class PerawatController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:perawats',
            'id_poli' => 'required|exists:polis,id',
        ]);

        $perawat = Perawat::create($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => $perawat,
        ], 201);
    }

    public function index()
    {
        $perawats = Perawat::with('poli')->get();

        return response()->json([
            'status' => 'success',
            'data' => $perawats,
        ]);
    }
}

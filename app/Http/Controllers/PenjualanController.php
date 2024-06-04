<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function createAccount(Request $request)
    {
        // Validasi input sebelum membuat penjualan
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }

        // Konversi format tanggal

        // Buat penjualan baru
        $penjualan = Penjualan::create([
            'nama_produk' => $request->nama_produk,
            'jumlah' => $request->jumlah,
            'harga' => $request->harga,
        ]);

        return response()->json(['status' => true, 'message' => 'Penjualan berhasil dibuat', 'penjualan' => $penjualan], 201);
    }

    public function show()
    {
        $penjualan = Penjualan::all();
        return response()->json($penjualan);
    }

    public function update(Request $request, $penjualanId)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'sometimes|required|string|max:255',
            'jumlah' => 'sometimes|required|integer|min:1',
            'harga' => 'sometimes|required|numeric|min:0',
            'tanggal' => 'sometimes|required|date_format:d-m-Y', // Pastikan validasi format tanggal sesuai dengan input
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }

        $penjualan = Penjualan::find($penjualanId);

        if (is_null($penjualan)) {
            return response()->json(['message' => 'Maaf, penjualan tidak ditemukan'], 404);
        }

        // Konversi format tanggal jika ada
        $input = $request->all();
        if ($request->has('tanggal')) {
            $input['tanggal'] = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
        }

        $penjualan->update($input);

        return response()->json(['status' => true, 'message' => 'Penjualan berhasil diupdate', 'penjualan' => $penjualan]);
    }
}

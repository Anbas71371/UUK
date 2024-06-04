<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Auth;

class BuatAkunController extends Controller
{
    public function createAccount(Request $request)
    {
        // Validasi input sebelum membuat akun
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }

        // Buat akun user baru
        $pelanggan = User::create([
            'nama' => $request->nama,
            'password' => Hash::make($request->password),
            'type'=>$request->type
        ]);

        return response()->json(['status' => true, 'message' => 'Akun  berhasil dibuat', 'user' => $pelanggan], 201);
    }
    public function show()
    {
        $pelanggan = User::all();
        return response()->json($pelanggan);
    } 


    public function update(Request $request, $pelangganid)
    {
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_telepon' => 'required',
        ]);
    
        $pelanggan = User::find($pelangganid);
    
        if (is_null($pelanggan)) {
            return response()->json(['message' => 'Maaf, akun tidak ditemukan'], 404);
        }
    
        $pelanggan->update($request->all());
    
        return response()->json($pelanggan);
    }
    
}

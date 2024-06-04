<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pinjaman; // Import model Pinjaman
use Illuminate\Support\Facades\Validator;
use Hash;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        // Validate input before saving new user data
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed: ', $validator->errors()->toArray());
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Save the photo to the public/photos directory

        // Encrypt the password before saving the new user
        $user = User::create([
            'nama' => $request->nama,
            'password' => Hash::make($request->password),

        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registration Berhasil',
            'user' => $user
        ], 201);
    }

    // Fungsi untuk menerima pinjaman
    // public function terimaPinjaman($id)
    // {
    //     $pinjaman = Pinjaman::find($id);

    //     if (!$pinjaman) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Pinjaman tidak ditemukan'
    //         ], 404);
    //     }

    //     $pinjaman->status_id = 3; // Ubah status menjadi 'diterima'
    //     $pinjaman->save();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Pinjaman telah diterima',
    //         'pinjaman' => $pinjaman
    //     ], 200);
    // }

    // Fungsi untuk menolak pinjaman
    // public function tolakPinjaman($id)
    // {
    //     $pinjaman = Pinjaman::find($id);

    //     if (!$pinjaman) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Pinjaman tidak ditemukan'
    //         ], 404);
    //     }

    //     $pinjaman->status_id = 2; // Ubah status menjadi 'ditolak'
    //     $pinjaman->save();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Pinjaman telah ditolak',
    //         'pinjaman' => $pinjaman
    //     ], 200);
    // }
}

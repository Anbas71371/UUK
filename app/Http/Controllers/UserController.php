<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // Pastikan untuk mengimpor Validator
use Illuminate\Support\Facades\Hash; // Tambahkan ini
use Illuminate\Support\Facades\Storage; // Tambahkan ini untuk penggunaan Storage

use App\Models\Pinjaman;
use Illuminate\Support\Facades\Log;
use App\Models\User;


class UserController extends Controller
{
    // Fungsi untuk mendapatkan data pengguna berdasarkan ID
    public function getUserById($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['user' => $user], 200);
    }

    // Fungsi untuk mendapatkan data pengguna yang sedang login
    public function getLoggedInUser(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['user' => $user], 200);
    }


    // Fungsi untuk memperbarui data pengguna
    // Fungsi untuk memperbarui data pengguna
    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        // Validasi input
        $this->validate($request, [
            'nama' => 'sometimes|required|string|max:255',
            'nip' => 'sometimes|required|string|max:255',
            'no_hp' => 'sometimes|required|string|max:255',
            'no_rekening' => 'sometimes|required|string|max:255',
            'alamat' => 'sometimes|required|string|max:255',
            'foto' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Periksa apakah foto diupload
        if ($request->hasFile('foto')) {

            // Upload foto baru
            // Upload foto baru
            $foto = $request->file('foto');
            
            
            // Dapatkan URL publik foto
            
            // Hapus foto lama jika ada
            if ($user->foto) {
                Storage::delete($user->foto);
            }
            
            $foto->storeAs('public/photos', $foto->getClientOriginalName()); // Simpan foto baru dengan nama asli
            $fotopath = "/photos/".$foto->getClientOriginalName();
            // Update pengguna dengan foto baru dan URL yang benar
            $user->update([
                'foto' => $fotopath, // Update path foto
                'nama' => $request->input('nama', $user->nama),
                'nip' => $request->input('nip', $user->nip),
                'no_hp' => $request->input('no_hp', $user->no_hp),
                'no_rekening' => $request->input('no_rekening', $user->no_rekening),
                'alamat' => $request->input('alamat', $user->alamat)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Pengguna berhasil diupdate',
                'user' => $user,
                'foto_url' => $fotopath // Sertakan URL foto dalam respons
            ], 200);


        } else {
            // Update pengguna tanpa foto baru
            $user->update([
                'nama' => $request->input('nama', $user->nama),
                'nip' => $request->input('nip', $user->nip),
                'no_hp' => $request->input('no_hp', $user->no_hp),
                'no_rekening' => $request->input('no_rekening', $user->no_rekening),
                'alamat' => $request->input('alamat', $user->alamat)
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Pengguna berhasil diupdate',
            'user' => $user
        ], 200);
    }

    // Fungsi untuk mendapatkan profil pengguna
    public function getProfile()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'nama' => $user->nama,
            'nip' => $user->nip,
            'no_hp' => $user->no_hp,
            'no_rekening' => $user->no_rekening,
            'alamat' => $user->alamat,
        ]);
    }

    // Fungsi untuk mendapatkan semua data pengguna beserta total pinjaman
    public function getAllUsers()
    {
        // Mengambil pengguna beserta pinjaman yang diterima
        $users = User::with([
            'pinjamans' => function ($query) {
                $query->where('status', 'pending');
            }
        ])->get();

        // Debug: Memeriksa apakah pengguna diambil dengan benar
        if ($users->isEmpty()) {
            return response()->json(['error' => 'No users found'], 404);
        }

        // Debug: Menampilkan data pengguna dan pinjaman yang diambil
        foreach ($users as $user) {
            \Log::info('User:', ['id' => $user->id, 'name' => $user->name, 'pinjamans' => $user->pinjamans]);
        }

        // Menghitung total pinjaman yang diterima untuk setiap pengguna
        foreach ($users as $user) {
            $totalPinjaman = 0;

            foreach ($user->pinjamans as $pinjaman) {
                $totalPinjaman += $pinjaman->total;
            }

            $user->total_pinjaman = $totalPinjaman;
        }

        return response()->json(['users' => $users], 200);
    }

    public function getAllUsersForAdmin()
    {
        // Mengambil semua pengguna beserta semua pinjaman mereka
        $users = User::with('pinjamans')->get();

        // Debug: Memeriksa apakah pengguna diambil dengan benar
        if ($users->isEmpty()) {
            return response()->json(['error' => 'No users found'], 404);
        }

        // Debug: Menampilkan data pengguna dan pinjaman yang diambil
        foreach ($users as $user) {
            \Log::info('User:', ['id' => $user->id, 'name' => $user->name, 'pinjamans' => $user->pinjamans]);
        }

        // Menghitung total pinjaman untuk setiap pengguna
        foreach ($users as $user) {
            $totalPinjaman = 0;

            foreach ($user->pinjamans as $pinjaman) {
                $totalPinjaman += $pinjaman->total;
            }

            $user->total_pinjaman = $totalPinjaman;
        }

        return response()->json(['users' => $users], 200);
    }


    public function acceptPinjaman($pinjamanId)
    {
        $pinjaman = Pinjaman::find($pinjamanId);

        if (!$pinjaman) {
            return response()->json(['error' => 'Pinjaman not found'], 404);
        }

        // Set status pinjaman ke 'accepted'
        $pinjaman->status = 'accepted';
        $pinjaman->save();

        return response()->json(['message' => 'Pinjaman accepted successfully'], 200);
    }

    public function rejectPinjaman($pinjamanId)
    {
        $pinjaman = Pinjaman::find($pinjamanId);

        if (!$pinjaman) {
            return response()->json(['error' => 'Pinjaman not found'], 404);
        }

        // Hapus pinjaman dari database
        $pinjaman->delete();

        return response()->json(['message' => 'Pinjaman rejected and deleted successfully'], 200);
    }
    public function searchByNomor($nomor)
    {
        $user = User::with('pinjamans')->where('id', $nomor)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Calculate total pinjaman for the user if there are associated pinjamans
        $totalPinjaman = $user->pinjamans->sum('total');
        $user->total_pinjaman = $totalPinjaman;

        return response()->json(['user' => $user], 200);
    }

    public function changePasswordSave(Request $request)
    {
        // Pastikan pengguna terautentikasi
        if (!Auth::check()) {
            return response()->json(['error' => 'You need to be logged in to change your password.'], 401);
        }

        // Validasi input
        $this->validate($request, [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        $auth = Auth::user();

        // Memastikan password saat ini cocok
        if (!Hash::check($request->current_password, $auth->password)) {
            return response()->json(['error' => 'Current Password is Invalid'], 400);
        }

        // Memastikan password baru tidak sama dengan password saat ini
        if (strcmp($request->current_password, $request->new_password) == 0) {
            return response()->json(['error' => 'New Password cannot be the same as your current password.'], 400);
        }

        // Mengubah password
        $auth->password = Hash::make($request->new_password);
        $auth->save();

        return response()->json(['success' => 'Password Changed Successfully'], 200);
    }
    public function approve ($id){
        $user = User::findOrFail($id);
        $user -> approve = true ;
        $user -> save();
        return response()->json(['success' => 'Berhasil di acc'], 200);

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Penjualan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input sebelum membuat produk baru
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|string|max:255',
            'stok' => 'required|string|max:20'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }
    
        // Buat produk baru
        $product = Product::create([
            'nama_produk' => $request->nama_produk,
            'harga' => $request->harga,
            'stok' => $request->stok
        ]);
    
        return response()->json(['status' => true, 'message' => 'Produk berhasil dibuat', 'product' => $product], 201);
    }
    
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }
    
        $product = Product::where('nama_produk', $request->nama_produk)->first();
    
        if (is_null($product)) {
            return response()->json(['status' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }
    
        $harga = $product->harga * $request->jumlah;
    
        // Kurangi stok produk
        if ($product->stok < $request->jumlah) {
            return response()->json(['status' => false, 'message' => 'Stok tidak mencukupi'], 400);
        }
        $product->stok -= $request->jumlah;
        $product->save();
    
        // Simpan data penjualan
        $penjualan = Penjualan::create([
            'nama_produk' => $request->nama_produk,
            'jumlah' => $request->jumlah,
            'harga' => $harga
        ]);
    
        return response()->json(['status' => true, 'message' => 'Penjualan berhasil', 'penjualan' => $penjualan], 201);
    }
    

    public function show()
    {
        $product = Product::all();
        return response()->json($product);
    } 

    public function update(Request $request, $id)
    {
        $request->validate([
            'stok' => 'required|integer|min:1',
        ]);

        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $product->increment('stok', $request->stok);

        return response()->json(['status' => true, 'message' => 'Stok berhasil ditambahkan', 'product' => $product]);
    }

    public function delete($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $product->delete();

        return response()->json(['status' => true, 'message' => 'Produk berhasil dihapus']);
    }

    public function updates(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|string|max:255',
            'stok' => 'required|string|max:20'
        ]);

        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $product->update([
            'nama_produk' => $request->nama_produk,
            'harga' => $request->harga,
            'stok' => $request->stok
        ]);

        return response()->json(['status' => true, 'message' => 'Produk berhasil diperbarui', 'product' => $product]);
    }
    // Tambahkan metode untuk menambah penjualan di ProductController atau buat PenjualanController baru
public function addPenjualan(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'nama_produk' => 'required|string|max:255',
        'jumlah' => 'required|integer|min:1'
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
    }

    // Cari produk berdasarkan nama_produk
    $product = Product::where('nama_produk', $request->nama_produk)->first();

    if (is_null($product)) {
        return response()->json(['status' => false, 'message' => 'Produk tidak ditemukan'], 404);
    }

    // Hitung total harga
    $total_harga = $product->harga * $request->jumlah;

    // Buat penjualan (di sini Anda perlu membuat model dan tabel Penjualan jika belum ada)
    $penjualan = Penjualan::create([
        'nama_produk' => $request->nama_produk,
        'jumlah' => $request->jumlah,
        'harga' => $total_harga,
    ]);

    // Kurangi stok produk
    $product->decrement('stok', $request->jumlah);

    return response()->json(['status' => true, 'message' => 'Penjualan berhasil', 'penjualan' => $penjualan], 201);
}

}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role->name), ['owner', 'manager', 'warehouse admin'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $products = Product::orderBy('name')->get();

        return view('products.index', [
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role->name), ['owner', 'manager', 'warehouse admin'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'price_buy' => 'required|numeric|min:0',
            'price_sell' => 'required|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product)
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role->name), ['owner', 'manager', 'warehouse admin'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price_buy' => $product->price_buy,
            'price_sell' => $product->price_sell,
            'min_stock' => $product->min_stock,
            'status' => $product->status,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role->name), ['owner', 'manager', 'warehouse admin'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'price_buy' => 'required|numeric|min:0',
            'price_sell' => 'required|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role->name), ['owner', 'manager', 'warehouse admin'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('products.index')->with('success', "Produk '$productName' berhasil dihapus.");
    }
}

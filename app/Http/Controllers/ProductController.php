<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = \App\Models\Product::with(['category', 'unit', 'vendor'])->latest()->get();

        $categories = \App\Models\Category::all();
        $units = \App\Models\Unit::all();
        $vendors = \App\Models\Vendor::all();

        return view('products.index', compact('products', 'categories', 'units', 'vendors'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'purchase_price' => str_replace('.', '', $request->purchase_price),
            'selling_price'  => str_replace('.', '', $request->selling_price),
        ]);

        $request->validate([
            'code'           => 'nullable|string|max:50|unique:products,code',
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'unit_id'        => 'required|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
        ]);

        try {
            Product::create($request->all());
            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambah produk: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'purchase_price' => str_replace('.', '', $request->purchase_price),
            'selling_price'  => str_replace('.', '', $request->selling_price),
        ]);

        $request->validate([
            'code'           => 'nullable|string|max:50|unique:products,code,' . $id,
            'name'           => 'required|string|max:255',
            'category_id'    => 'required',
            'unit_id'        => 'required',
            'purchase_price' => 'required|numeric',
            'selling_price'  => 'required|numeric',
        ]);

        try {
            $product = Product::findOrFail($id);
            $product->update($request->all());
            return redirect()->route('products.index')->with('success', 'Data produk berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data produk.');
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus produk.');
        }
    }
}

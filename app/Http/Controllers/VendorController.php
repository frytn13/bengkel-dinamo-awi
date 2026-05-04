<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::orderBy('name', 'asc')->get();
        return view('vendors.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:vendors,name',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string'
        ]);

        try {
            Vendor::create($request->all());
            return redirect()->route('vendors.index')->with('success', 'Vendor baru berhasil didaftarkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambah vendor: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:vendors,name,' . $id,
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string'
        ]);

        try {
            $vendor = Vendor::findOrFail($id);
            $vendor->update($request->all());
            return redirect()->route('vendors.index')->with('success', 'Data vendor berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data vendor.');
        }
    }

    public function destroy($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            $vendor->delete();
            return redirect()->route('vendors.index')->with('success', 'Vendor berhasil dihapus.');
        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'Vendor tidak bisa dihapus karena memiliki riwayat transaksi.');
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem database.');
        }
    }
}

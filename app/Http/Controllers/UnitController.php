<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::orderBy('name', 'asc')->get();
        return view('units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:units,name'
        ]);

        try {
            Unit::create($request->all());
            return redirect()->route('units.index')->with('success', 'Satuan baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambah satuan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:units,name,' . $id
        ]);

        try {
            $unit = Unit::findOrFail($id);
            $unit->update($request->all());
            return redirect()->route('units.index')->with('success', 'Satuan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui satuan.');
        }
    }

    public function destroy($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            $unit->delete();
            return redirect()->route('units.index')->with('success', 'Satuan berhasil dihapus.');
        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'Satuan tidak bisa dihapus karena masih digunakan oleh beberapa produk.');
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}

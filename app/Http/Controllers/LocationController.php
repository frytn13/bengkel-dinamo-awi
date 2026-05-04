<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('name', 'asc')->get();
        return view('locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name'
        ]);

        try {
            Location::create($request->all());
            return redirect()->route('locations.index')->with('success', 'Lokasi baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambah lokasi: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $id
        ]);

        try {
            $location = Location::findOrFail($id);
            $location->update($request->all());
            return redirect()->route('locations.index')->with('success', 'Lokasi berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui lokasi.');
        }
    }

    public function destroy($id)
    {
        try {
            $location = Location::findOrFail($id);
            $location->delete();
            return redirect()->route('locations.index')->with('success', 'Lokasi berhasil dihapus.');
        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'Lokasi tidak bisa dihapus karena masih digunakan sebagai tempat penyimpanan produk.');
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}

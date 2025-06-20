<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{

    public function index()
    {
        $vendors = Vendor::paginate(20);
        return view('vendor.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:vendors',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        Vendor::create($request->only('name', 'email', 'phone', 'address'));

        return redirect()->route('vendor.index')->with('success', 'Vendor created successfully.');
    }

    public function edit(Vendor $vendor)
    {
        return view('vendor.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:vendors,email,' . $vendor->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $vendor->update($request->only('name', 'email', 'phone', 'address'));

        return redirect()->route('vendor.index')->with('success', 'Vendor updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendor.index')->with('success', 'Vendor deleted successfully.');
    }

    public function show(Vendor $vendor)
    {
        $vendor->load('users');
        return view('vendor.show', compact('vendor'));
    }
}
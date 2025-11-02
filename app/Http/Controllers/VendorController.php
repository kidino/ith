<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{

    public function index()
    {
        $vendors = Vendor::orderBy('id')->paginate(10);
        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:vendors',
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'person_in_charge' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
        ]);

        Vendor::create($request->only('code', 'name', 'phone_number', 'address', 'person_in_charge', 'email'));

        return redirect()->route('vendors.index')->with('success', 'Vendor created successfully.');
    }

    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:vendors,code,' . $vendor->id,
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'person_in_charge' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
        ]);

        $vendor->update($request->only('code', 'name', 'phone_number', 'address', 'person_in_charge', 'email'));

        return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully.');
    }

    public function show(Vendor $vendor)
    {
        $vendor->load('users');
        return view('vendors.show', compact('vendor'));
    }
}
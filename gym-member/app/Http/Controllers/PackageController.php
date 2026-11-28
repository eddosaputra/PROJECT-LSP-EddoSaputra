<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $this->admin();

        return view('admin.packages.index', [
            'packages' => Package::latest()->paginate(10),
        ]);
    }

    public function store(Request $request)
    {
        $this->admin();

        $validated = $this->validatePackage($request);
        $validated['is_active'] = $request->boolean('is_active');
        Package::create($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Membership package added successfully.');
    }

    public function edit(Package $package)
    {
        $this->admin();

        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $this->admin();

        $validated = $this->validatePackage($request);
        $validated['is_active'] = $request->boolean('is_active');
        $package->update($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Membership package updated successfully.');
    }

    public function toggleStatus(Package $package)
    {
        $this->admin();
        $package->update(['is_active' => ! $package->is_active]);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package availability updated successfully.');
    }

    private function validatePackage(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }

    private function admin(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403);
    }
}

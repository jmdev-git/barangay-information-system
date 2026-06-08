<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\Resident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HouseholdController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $households = Household::with(['head', 'residents.user'])
            ->withCount('residents')
            ->when($search, fn ($q) =>
                $q->where('address', 'like', "%{$search}%")
                  ->orWhere('purok',  'like', "%{$search}%")
                  ->orWhere('household_head_name', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.households.index', compact('households', 'search'));
    }

    public function create(): View
    {
        return view('admin.households.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address'             => 'required|string|max:500',
            'purok'               => 'nullable|string|max:100',
            'barangay'            => 'required|string|max:100',
            'household_head_name' => 'nullable|string|max:255',
        ]);

        Household::create($validated);

        return redirect()->route('households.index')
            ->with('success', 'Household created successfully.');
    }

    public function show(Household $household): View
    {
        // Req 4 AC4 — list all residents with full_name, gender, computed age, account status
        $household->load(['head', 'residents.user']);
        return view('admin.households.show', compact('household'));
    }

    public function edit(Household $household): View
    {
        $residents = Resident::orderBy('first_name')->get();
        return view('admin.households.edit', compact('household', 'residents'));
    }

    public function update(Request $request, Household $household): RedirectResponse
    {
        $validated = $request->validate([
            'address'             => 'required|string|max:500',
            'purok'               => 'nullable|string|max:100',
            'barangay'            => 'required|string|max:100',
            'household_head_name' => 'nullable|string|max:255',
            'household_head_id'   => 'nullable|exists:residents,id',
        ]);

        $household->update($validated);

        // Sync all residents' address if household address changed (Req 4 AC5)
        if ($household->wasChanged('address')) {
            $household->residents()->update(['address' => $validated['address']]);
        }

        return redirect()->route('households.show', $household)
            ->with('success', 'Household updated successfully.');
    }

    public function destroy(Household $household): RedirectResponse
    {
        $household->delete();
        return redirect()->route('households.index')
            ->with('success', 'Household deleted successfully.');
    }
}

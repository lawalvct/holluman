<?php

namespace App\Http\Controllers;

use App\Models\Sim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimController extends Controller
{
    public function index()
    {
        $sims = Auth::user()->sims()->latest()->get();
        return view('sims.index', compact('sims'));
    }

    public function create()
    {
        return view('sims.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sim_number' => 'required|unique:sims,sim_number',
            'camera_name' => 'required|string|max:255',
            'camera_location' => 'required|string|max:255',
        ]);

        Auth::user()->sims()->create($request->only(['sim_number', 'camera_name', 'camera_location']));

        return redirect()->route('sims')->with('success', 'SIM added successfully.');
    }

    public function edit(Sim $sim)
    {
        //$this->authorize('update', $sim);
        return view('sims.edit', compact('sim'));
    }

    public function update(Request $request, Sim $sim)
    {
       // $this->authorize('update', $sim);

        $request->validate([
            'sim_number' => 'required|unique:sims,sim_number,' . $sim->id,
            'camera_name' => 'required|string|max:255',
            'camera_location' => 'required|string|max:255',
        ]);

        $sim->update($request->only(['sim_number', 'camera_name', 'camera_location']));

        return redirect()->route('sims')->with('success', 'SIM updated successfully.');
    }

    public function destroy(Sim $sim)
    {
        $this->authorize('delete', $sim);
        $sim->delete();
        return redirect()->route('sims')->with('success', 'SIM deleted.');
    }
}

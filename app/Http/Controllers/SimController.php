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
        try {
            $request->validate([
                'sim_number' => 'required|string|unique:sims,sim_number',
                'camera_name' => 'required|string|max:255',
                'camera_location' => 'required|string|max:255',
            ]);

            $sim = Sim::create([
                'user_id' => auth()->id(),
                'sim_number' => $request->sim_number,
                'camera_name' => $request->camera_name,
                'camera_location' => $request->camera_location,
            ]);

            // Check if request is AJAX (for modal submission)
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'SIM added successfully.',
                    'sim' => $sim
                ]);
            }

            return redirect()->route('sims')->with('success', 'SIM added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->validator->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while adding the SIM.'
                ], 500);
            }
            throw $e;
        }
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

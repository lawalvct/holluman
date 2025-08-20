<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->latest()->paginate(10);
        $stats = [
            'total_plans' => SubscriptionPlan::count(),
            'active_plans' => SubscriptionPlan::where('is_active', true)->count(),
            'total_subscriptions' => \App\Models\Subscription::count(),
            'total_revenue' => \App\Models\Payment::where('status', 'successful')->sum('amount'),
        ];
        return view('admin.plans.index', compact('plans', 'stats'));
    }

    /**
     * Display the specified resource.
     */
    public function show(SubscriptionPlan $plan)
    {
        return view('admin.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'data_limit_in_gb' => 'required|integer|min:1',
            'speed_limit_in_mbps' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        $plan->update($request->all());

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\HostProfile;
use App\Models\User;
use Illuminate\Http\Request;

class HostVerificationController extends Controller
{
    /**
     * List all host profiles pending verification
     */
    public function index(Request $request)
    {
        $query = HostProfile::with('user');

        // Filter by verification status
        if ($request->status) {
            $query->where('verification_status', $request->status);
        } else {
            $query->where('verification_status', 'pending');
        }

        // Filter by host type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        $hosts = $query->latest()->paginate(20);

        return view('backend.verification.hosts', compact('hosts'));
    }

    /**
     * Show host profile details for verification
     */
    public function show($id)
    {
        $host = HostProfile::with(['user', 'properties'])->findOrFail($id);
        return view('backend.verification.host-details', compact('host'));
    }

    /**
     * Verify a host
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $host = HostProfile::findOrFail($id);

        $host->update([
            'verification_status' => $request->status,
            'verified_at' => $request->status === 'verified' ? now() : null,
            'verification_notes' => $request->notes,
        ]);

        // If verified, update verified_at timestamp
        if ($request->status === 'verified') {
            $host->update(['verified_at' => now()]);
        }

        $message = $request->status === 'verified' 
            ? 'Host verified successfully!' 
            : 'Host verification rejected.';

        return redirect()->route('admin.verification.hosts')
            ->with('success', $message);
    }

    /**
     * Toggle superhost status
     */
    public function toggleSuperhost($id)
    {
        $host = HostProfile::findOrFail($id);

        if ($host->verification_status !== 'verified') {
            return back()->with('error', 'Host must be verified before becoming a superhost.');
        }

        $host->update([
            'is_superhost' => !$host->is_superhost,
            'superhost_since' => !$host->is_superhost ? now() : null,
        ]);

        $message = $host->is_superhost 
            ? 'Superhost status granted!' 
            : 'Superhost status removed.';

        return back()->with('success', $message);
    }

    /**
     * Bulk verify hosts
     */
    public function bulkVerify(Request $request)
    {
        $request->validate([
            'host_ids' => 'required|array',
            'host_ids.*' => 'exists:host_profiles,id',
            'status' => 'required|in:verified,rejected',
        ]);

        HostProfile::whereIn('id', $request->host_ids)->update([
            'verification_status' => $request->status,
            'verified_at' => $request->status === 'verified' ? now() : null,
        ]);

        $count = count($request->host_ids);
        $message = $request->status === 'verified'
            ? "{$count} hosts verified successfully!"
            : "{$count} hosts rejected.";

        return back()->with('success', $message);
    }
}


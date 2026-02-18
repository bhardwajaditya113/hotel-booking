<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\HostProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyVerificationController extends Controller
{
    /**
     * List all properties pending verification
     */
    public function index(Request $request)
    {
        $query = Property::with(['type', 'host.hostProfile']);

        // Filter by verification status
        if ($request->status) {
            $query->where('verification_status', $request->status);
        } else {
            $query->where('verification_status', 'pending');
        }

        // Filter by listing type
        if ($request->listing_type) {
            $query->where('listing_type', $request->listing_type);
        }

        $properties = $query->latest()->paginate(20);

        return view('backend.verification.properties', compact('properties'));
    }

    /**
     * Show property details for verification
     */
    public function show($id)
    {
        $property = Property::with(['type', 'host.hostProfile', 'activeRooms'])->findOrFail($id);
        return view('backend.verification.property-details', compact('property'));
    }

    /**
     * Verify a property
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $property = Property::findOrFail($id);

        $property->update([
            'verification_status' => $request->status,
            'verified_at' => $request->status === 'verified' ? now() : null,
            'verification_notes' => $request->notes,
        ]);

        // If verified, also activate the property
        if ($request->status === 'verified' && $property->status === 'pending') {
            $property->update(['status' => 'active']);
        }

        $message = $request->status === 'verified' 
            ? 'Property verified successfully!' 
            : 'Property verification rejected.';

        return redirect()->route('admin.verification.properties')
            ->with('success', $message);
    }

    /**
     * Bulk verify properties
     */
    public function bulkVerify(Request $request)
    {
        $request->validate([
            'property_ids' => 'required|array',
            'property_ids.*' => 'exists:properties,id',
            'status' => 'required|in:verified,rejected',
        ]);

        Property::whereIn('id', $request->property_ids)->update([
            'verification_status' => $request->status,
            'verified_at' => $request->status === 'verified' ? now() : null,
        ]);

        $count = count($request->property_ids);
        $message = $request->status === 'verified'
            ? "{$count} properties verified successfully!"
            : "{$count} properties rejected.";

        return back()->with('success', $message);
    }
}



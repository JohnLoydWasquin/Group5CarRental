<?php

namespace App\Http\Controllers;

use App\Models\KycSubmission;
use Illuminate\Http\Request;

class AdminKycController extends Controller
{
    public function index(Request $request)
    {
        $query = KycSubmission::with('user');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $submissions = $query
            ->orderByRaw("FIELD(status, 'Pending', 'Rejected', 'Approved')")
            ->orderBy('created_at', 'desc')
            ->paginate(3)
            ->withQueryString();

        return view('layouts.authorities.accountVerify', compact('submissions'));
    }

    public function show(KycSubmission $submission)
    {
        $submission->load('user');
        return view('layouts.authorities.showVerify', compact('submission'));
    }

    public function approve(Request $request, KycSubmission $submission)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $submission->update([
            'status' => 'Approved',
            'admin_notes' => $request->admin_notes,
        ]);

        $submission->user->update(['kyc_status' => 'Approved']);

        return redirect()
            ->route('admin.kyc.show', $submission->id)
            ->with('success', 'Account verification has been approved.');
    }

    public function reject(Request $request, KycSubmission $submission)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:2000',
        ]);

        $submission->update([
            'status' => 'Rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        $submission->user->update(['kyc_status' => 'Rejected']);

        return redirect()
            ->route('admin.kyc.show', $submission->id)
            ->with('success', 'Account verification has been rejected.');
    }
}

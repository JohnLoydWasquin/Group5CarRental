<?php

namespace App\Http\Controllers;

use App\Models\KycSubmission;
use Illuminate\Http\Request;

class AdminKycController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'role:admin']);
    // }

    public function index()
    {
        $submissions = KycSubmission::with('user')
            ->orderByRaw("FIELD(status, 'Pending', 'Rejected', 'Approved')")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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

        $submission->status = 'Approved';
        $submission->admin_notes = $request->admin_notes;
        $submission->save();

        $submission->user->kyc_status = 'Approved';
        $submission->user->save();

        return redirect()
            ->route('admin.kyc.show', $submission->id)
            ->with('success', 'Account verification has been approved.');
    }

    public function reject(Request $request, KycSubmission $submission)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:2000',
        ]);

        $submission->status = 'Rejected';
        $submission->admin_notes = $request->admin_notes;
        $submission->save();

        $submission->user->kyc_status = 'Rejected';
        $submission->user->save();

        return redirect()
            ->route('admin.kyc.show', $submission->id)
            ->with('success', 'Account verification has been rejected.');
    }
}

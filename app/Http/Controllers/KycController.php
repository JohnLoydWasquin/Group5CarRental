<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KycSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        // Fetch the user's last KYC submission (if any)
        // Note: The relationship should be defined on the User model as 'kycSubmission'
        $kyc = $user->kycSubmission; 

        return view('layouts.pages.verifyForm', compact('kyc', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if the verification is already Approved. If so, prevent resubmission.
        if ($user->kyc_status === 'Approved') {
             // Redirect back to profile with a warning, or abort. Redirecting is usually better UX.
             return redirect()->route('profile')->with('error', 'Your verification is already approved and cannot be modified.');
        }

        $data = $request->validate([
            'full_name'    => 'required|string|max:255',
            'sex' => 'required|in:Male,Female,Prefer not to say',
            'birthdate'    => 'required|date|before:today',
            'address_line' => 'required|string|max:255',
            'city'         => 'required|string|max:100',
            'province'     => 'required|string|max:100',
            'postal_code' => 'required|digits:4',
            'id_type'      => 'required|string|max:100',
            'id_number' => [
                'required',
                'string',
                function ($attr, $value, $fail) use ($request) {
                    $rules = [
                        'Passport' => 9,
                        'Driver License' => 11,
                        'National ID' => 12,
                    ];
                    if (isset($rules[$request->id_type]) &&
                        strlen($value) !== $rules[$request->id_type]) {
                        $fail("{$request->id_type} must be exactly {$rules[$request->id_type]} characters.");
                    }
                }
            ],
            // Only require file uploads if a previous KYC submission doesn't exist
            // OR if the user is actively resubmitting (the fields are present and not empty)
            'id_image'     => ($user->kycSubmission && $user->kycSubmission->status !== 'Rejected') ? 'nullable|image|mimes:jpg,jpeg,png|max:20480' : 'required|image|mimes:jpg,jpeg,png|max:20480',
            'selfie_image' => ($user->kycSubmission && $user->kycSubmission->status !== 'Rejected') ? 'nullable|image|mimes:jpg,jpeg,png|max:20480' : 'required|image|mimes:jpg,jpeg,png|max:20480',
        ]);

        $payload = [
            'user_id'      => $user->id,
            'full_name'    => $data['full_name'],
            'sex'          => $data['sex'], // Add the new 'sex' field
            'birthdate'    => $data['birthdate'] ?? null,
            'address_line' => $data['address_line'],
            'city'         => $data['city'],
            'province'     => $data['province'],
            'postal_code'  => $data['postal_code'] ?? null,
            'id_type'      => $data['id_type'],
            'id_number'    => $data['id_number'],
            'status'       => 'Pending', // New submissions/resubmissions are always Pending
        ];
        
        // Handle file uploads for new or resubmitted files.
        // We only overwrite the path if a new file is uploaded.
        if ($request->hasFile('id_image')) {
            $payload['id_image_path'] = $request->file('id_image')->store('kyc/id_images', 'public');
        }

        if ($request->hasFile('selfie_image')) {
            $payload['selfie_image_path'] = $request->file('selfie_image')->store('kyc/selfies', 'public');
        }

        $kyc = KycSubmission::updateOrCreate(
            ['user_id' => $user->id],
            $payload
        );

        /** @var \App\Models\User $user */
        $user->kyc_status = 'Pending';
        $user->save();
        
        $message = ($request->has('full_name') && $user->kycSubmission) 
                   ? 'Your KYC details were successfully updated and resubmitted for verification.' 
                   : 'Your KYC was submitted. Please wait for verification.';

        return redirect()
            ->route('profile')
            ->with('success', $message);
    }
}
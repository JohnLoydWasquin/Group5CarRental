<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KycSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @method void middleware(string|array $middleware, array $options = [])
 */
class KycController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $kyc  = $user->kycSubmission;

        return view('layouts.pages.verifyForm', compact('kyc', 'user'));
    }

    public function store(Request $request)
{

    $user = Auth::user();

    $data = $request->validate([
        'full_name'    => 'required|string|max:255',
        'sex' => 'required|in:Male,Female,Prefer not to say',
        'birthdate'    => 'required|date|before:today',
        'address_line' => 'required|string|max:255',
        'city'         => 'required|string|max:100',
        'province'     => 'required|string|max:100',
        'postal_code'  => 'nullable|string|max:20',
        'id_type'      => 'required|string|max:100',
        'id_number'    => 'required|string|max:100',
        'id_image'     => 'required|image|mimes:jpg,jpeg,png|max:20480',
        'selfie_image' => 'required|image|mimes:jpg,jpeg,png|max:20480',
    ]);

    $payload = [
        'user_id'      => $user->id,
        'full_name'    => $data['full_name'],
        'birthdate'    => $data['birthdate'] ?? null,
        'address_line' => $data['address_line'],
        'city'         => $data['city'],
        'province'     => $data['province'],
        'postal_code'  => $data['postal_code'] ?? null,
        'id_type'      => $data['id_type'],
        'id_number'    => $data['id_number'],
        'status'       => 'Pending',
    ];

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

    return redirect()
        ->route('profile')
        ->with('success', 'Your KYC was submitted. Please wait for verification.');
}
}

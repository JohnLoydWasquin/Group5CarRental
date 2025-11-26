<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $bookings = $user->bookings()->with('vehicle')->get();

        $totalBookings = $bookings->count();
        $totalSpent = $bookings->sum('total_amount');
        $averageRating = null;

        $kyc = $user->kycSubmission;

        return view('layouts.userProfile.profile', compact(
        'bookings',
        'totalBookings',
        'totalSpent',
        'averageRating',
        'user',
        'kyc'
    ));
}

    public function upload(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|max:2048'
        ]);

        $path = $request->file('profile_image')->store('profile_images', 'public');

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->profile_image = $path;
        $user->save();

        return back()->with('success', 'Profile image updated!');
    }

    public function update(Request $request)
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            // 'email'   => ['required','email','max:255','unique:users,email,'.$user->id],
        ]);

        $user->update($data);

        return back()->with('profile_success', 'Profile updated successfully.');
    }

}

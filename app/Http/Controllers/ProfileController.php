<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\KycSubmission;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $bookings = $user->bookings()->with('vehicle')->get();

        $bookingsQuery = $user->bookings()->with('vehicle')->latest();
        $bookings = $bookingsQuery->paginate(3);

        $totalBookings = $bookings->count();
        $totalSpent    = $bookings->sum('total_amount');

        $averageRating = Review::where('user_id', $user->id)
            ->where('status', 'approved')   
            ->avg('rating');

        $averageRating = $averageRating ? round($averageRating, 1) : null;

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

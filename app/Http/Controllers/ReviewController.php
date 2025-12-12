<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $averageRating = Review::where('status', 'approved')->avg('rating') ?? 0;
        $totalReviews  = Review::where('status', 'approved')->count();

        $reviews = Review::with('user')
            ->where('status', 'approved')
            ->latest()
            ->paginate(6);

        // Default null if user is not logged in
        $userReview = null;

        if ($user) {
            $userReview = Review::where('user_id', $user->id)->latest()->first();
        }

        return view('layouts.pages.rateus', compact(
            'averageRating',
            'totalReviews',
            'reviews',
            'userReview'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'rating'  => $data['rating'],
                'comment' => $data['comment'] ?? null,
                'status'  => 'pending',
            ]
        );

        return back()->with('success', 'Thank you for your feedback! Your review is pending approval.');
    }
}

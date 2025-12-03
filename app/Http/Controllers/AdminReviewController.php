<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with('user')
            ->latest()
            ->paginate(15);

        $averageRating = Review::where('status', 'approved')->avg('rating');
        $totalReviews  = Review::where('status', 'approved')->count();

        $approvedCount = Review::where('status', 'approved')->count();
        $pendingCount  = Review::where('status', 'pending')->count();

        return view('layouts.authorities.reviewIndex', compact(
            'reviews',
            'averageRating',
            'totalReviews',
            'approvedCount',
            'pendingCount'
        ));
    }

    public function approve(Review $review)
    {
        $review->status = 'approved';
        $review->save();

        return back()->with('success', 'Review approved.');
    }

    public function reject(Review $review)
    {
        $review->status = 'rejected';
        $review->save();

        return back()->with('success', 'Review rejected.');
    }
}

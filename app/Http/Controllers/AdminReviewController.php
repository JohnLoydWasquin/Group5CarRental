<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with('user')->latest()->paginate(15);

        return view('layouts.authorities.reviewIndex', compact('reviews'));
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
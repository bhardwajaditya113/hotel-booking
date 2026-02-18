<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * How It Works page
     */
    public function howItWorks()
    {
        return view('frontend.pages.how-it-works');
    }

    /**
     * How It Works for Hosts page
     */
    public function howItWorksHost()
    {
        return view('frontend.pages.how-it-works-host');
    }

    /**
     * Features page
     */
    public function features()
    {
        return view('frontend.pages.features');
    }

    /**
     * Pricing page
     */
    public function pricing()
    {
        return view('frontend.pages.pricing');
    }

    /**
     * User Journey page
     */
    public function userJourney()
    {
        return view('frontend.pages.user-journey');
    }
}


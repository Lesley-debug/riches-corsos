<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\HomecomingPhoto;
use App\Models\Puppy;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicSiteController extends Controller
{
    public function home()
    {
        return Inertia::render('Home', [
            'featuredPuppies' => Puppy::with('images')->where('status', 'available')->latest()->take(3)->get(),
            'recentPosts' => BlogPost::published()->latest('published_at')->take(3)->get(),
            'testimonials' => Testimonial::where('is_featured', true)->take(3)->get(),
            'heroImage' => SiteSetting::current()->hero_image,
            'homecomingPhotos' => HomecomingPhoto::orderBy('sort_order')->get(),
        ]);
    }

    public function puppyIndex()
    {
        return Inertia::render('Puppies/Index', [
            'puppies' => Puppy::with('images')
                ->whereIn('status', ['available', 'pending'])
                ->latest()
                ->get(),
        ]);
    }

    public function puppyShow(Puppy $puppy)
    {
        $puppy->load('images');

        $isWishlisted = auth()->check()
            ? auth()->user()->wishlists()->where('puppy_id', $puppy->id)->exists()
            : false;

        return Inertia::render('Puppies/Show', [
            'puppy' => $puppy,
            'isWishlisted' => $isWishlisted,
        ]);
    }

    public function blogIndex()
    {
        return Inertia::render('Blog/Index', [
            'posts' => BlogPost::published()->latest('published_at')->paginate(9),
        ]);
    }

    public function blogShow(BlogPost $blogPost)
    {
        return Inertia::render('Blog/Show', [
            'post' => $blogPost,
        ]);
    }

    public function about()
    {
        return Inertia::render('About');
    }

    public function faqs()
    {
        return Inertia::render('Faqs');
    }

    public function contactShow()
    {
        return Inertia::render('Contact');
    }

    public function contactStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create($validated);

        return back()->with('success', "Thanks — we'll get back to you shortly.");
    }
}

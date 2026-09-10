<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\HomecomingPhoto;
use App\Models\Puppy;
use App\Models\PuppyDocument;
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
                ->whereIn('status', ['available', 'pending', 'reserved'])
                ->where('visibility', 'published')
                ->latest()
                ->get(),
        ]);
    }

    public function puppyShow(Puppy $puppy)
    {
        abort_unless($puppy->visibility === 'published', 404);

        $puppy->load([
            'images',
            'videos',
            'documents' => fn ($q) => $q
                ->where('visibility', 'public')
                ->whereIn('status', [PuppyDocument::STATUS_GENERATED, PuppyDocument::STATUS_UPLOADED])
                ->latest(),
            'parents.images',
            'parents.videos',
        ]);

        $puppy->documents->each(fn (PuppyDocument $document) => $document->append(['type_label', 'status_label']));

        $related = Puppy::with('images')
            ->where('id', '!=', $puppy->id)
            ->where('visibility', 'published')
            ->whereIn('status', ['available', 'pending', 'reserved'])
            ->where('breed', $puppy->breed)
            ->latest()
            ->take(4)
            ->get();

        if ($related->count() < 4) {
            $ids = $related->pluck('id')->push($puppy->id);
            $extra = Puppy::with('images')
                ->whereNotIn('id', $ids)
                ->where('visibility', 'published')
                ->whereIn('status', ['available', 'pending', 'reserved'])
                ->latest()
                ->take(4 - $related->count())
                ->get();
            $related = $related->concat($extra);
        }
        $related = $related->values();

        $isWishlisted = auth()->check()
            ? auth()->user()->wishlists()->where('puppy_id', $puppy->id)->exists()
            : false;

        return Inertia::render('Puppies/Show', [
            'puppy'        => $puppy,
            'sire'         => $puppy->parents->firstWhere('pivot.role', 'sire'),
            'dam'          => $puppy->parents->firstWhere('pivot.role', 'dam'),
            'isWishlisted' => $isWishlisted,
            'related'      => $related,
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

    public function testimonials()
    {
        return Inertia::render('Testimonials');
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

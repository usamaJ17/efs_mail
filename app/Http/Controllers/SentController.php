<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $emails = Email::whereFrom(auth()->id())
            ->with(
                [
                    'messages' => fn($query) => $query->latest()->take(3),
                    'receivers.user' => fn($query) => $query->take(5),
                ]
            )
            ->where('owner_id', auth()->id())
            ->latest()
            ->paginate(20);


        return Inertia::render('Emails/Index', compact('emails'));
    }
}

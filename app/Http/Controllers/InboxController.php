<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InboxController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $emails = Email::whereHas('receivers', fn($query) => $query->where('receiver_id', auth()->id()))
            ->with(
                [
                    'messages' => fn($query) => $query->latest()->take(3),
                    'receivers.user' => fn($query) => $query->take(5),
                ]
            )
            ->where('owner_id', auth()->id())
            ->whereHas('messages', function ($query) {
                $query->where('sender_id', '!=', auth()->id());
            })
            ->latest()
            ->paginate(20);

        $totalUnreadEmails = Email::whereHas('receivers', fn($query) => $query->where('receiver_id', auth()->id()))
            ->where('owner_id', auth()->id())
            ->whereHas('messages', function ($query) {
                $query->where('sender_id', '!=', auth()->id());
            })
            ->where('is_read', false)
            ->latest()
            ->count();


        return Inertia::render('Emails/Index', compact(['emails', 'totalUnreadEmails']));
    }
}

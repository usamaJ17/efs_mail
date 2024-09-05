<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarkReadAllEmailRequest;
use App\Http\Requests\StoreEmailReplyRequest;
use App\Models\Email;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Email $email)
    {
        if ($email->owner_id != auth()->id()) {
            abort('400', 'Its not your email!');
        }

        $email->update([
            'is_read' => true,
        ]);

        $email->load([
            'from',
            'receivers' => ['user'],
            'messages' => [
                'attachments',
                'sender',  //message sender
                'replyMessage' => ['sender'] //receiver or reply to
            ],
        ]);

        return Inertia::render('Emails/Show', [
            'email' => $email,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Email $email)
    {
        $email->delete();

        return back()->with([
            'message' => 'Email deleted successfully!',
            'type' => 'success',
        ]);
    }

    public function permanentDestroy(string $id)
    {
        $email = Email::withTrashed()->with('messages.attachments')->findOrFail($id);

        $email->messages->map(function ($message) {
            $message->attachments->map(function ($attachment) {
                $attachment->delete();
            });
        });

        $email->forceDelete();

        return back()->with([
            'message' => 'Email permanently deleted successfully!',
            'type' => 'success',
        ]);
    }

    public function restore(string $id)
    {
        $email = Email::withTrashed()->findOrFail($id);

        $email->restore();

        return back()->with(['message' => 'Email restored successfully!']);
    }

    public function storeReply(Email $email, StoreEmailReplyRequest $request)
    {
        $sameGroupEmails = Email::where('group_id', $email->group_id)->get();

        $sameGroupEmails->map(function ($email) use ($request) {
            $email->update(['is_read' => false]);
            $message = $email->messages()->create($request->safe()->except('files'));
            collect($request->validated('files'))->map(function ($file) use ($message) {
                $message->copyMedia($file['file'])->toMediaCollection('attachments');
            });
        });

        return back()->with(['message' => 'Reply send successfully!.']);
    }

    public function search(Request $request)
    {
        $q = $request->input('q');

        $emails = Email::where('owner_id', auth()->id())
            ->where(function ($query) use ($q) {
                $query->where('subject', 'like', "%$q%")
                    ->orWhere(function ($query) use ($q) {
                        $query->whereHas('receivers', function ($query) use ($q) {
                            $query->whereHas('user', function ($query) use ($q) {
                                $query->where('email', 'like', "%$q%");
                            });
                        });
                    })
                    ->orWhere(function ($query) use ($q) {
                        $query->whereHas('messages', function ($query) use ($q) {
                            $query->where('content', 'like', "%$q%");
                        });
                    });
            })
            ->with(['from', 'receivers.user', 'messages' => fn($query) => $query->latest()->take(1)])
            ->take(5)
            ->get();

        return response()->json($emails);
    }


    public function markReadAll(MarkReadAllEmailRequest $request)
    {
        if (count($request->emails) > 0) {
            Email::whereIn('id', $request->emails)->update(['is_read' => true]);
            return back()->with(['message' => 'Emails marked as read successfully!.']);
        }

        return back()->with(['message' => 'No emails selected!.', 'type' => 'error']);
    }

    public function deleteMany(MarkReadAllEmailRequest $request)
    {
        if (count($request->emails) > 0) {
            Email::whereIn('id', $request->emails)->delete();
            return back()->with(['message' => 'Emails deleted successfully!.']);
        }

        return back()->with(['message' => 'No emails selected!.', 'type' => 'error']);
    }
}

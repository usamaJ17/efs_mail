<?php

namespace App\Http\Controllers;

use App\Events\ReceiveEmailBroadcastEvent;
use App\Http\Requests\StoreComposerMessageRequest;
use App\Jobs\ExportPdfDataJob;
use App\Models\Email;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ComposeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreComposerMessageRequest $request)
    {
        $groupId = Str::random();

        collect($request->validated('receivers'))
            ->push(auth()->user()->email)
            ->map(function ($owner) use ($request, $groupId) {

                $user = User::where('email', $owner)->firstOrFail();

                $email = Email::create([
                    'subject' => $request->validated('subject'),
                    'from' => auth()->id(),
                    'is_read' => false,
                    'is_sent' => true,
                    'is_important' => false,
                    'owner_id' => $user->id,
                    'group_id' => $groupId,
                ]);

                $message = $email->messages()->create([
                    'content' => $request->validated('message'),
                    'sender_id' => auth()->id(),
                ]);

                $receivers = collect($request->validated('receivers'))->map(function ($receiver) {
                    $user = User::where('email', $receiver)->firstOrFail();
                    return ['receiver_id' => $user->id];
                })->push(['receiver_id' => auth()->id()]);

                $email->receivers()->createMany($receivers);

                collect($request->validated('files'))->map(function ($file) use ($message) {
                    $message->copyMedia($file['file'])->toMediaCollection('attachments');
                });

                event(new ReceiveEmailBroadcastEvent($user->id));
            });

        return back()->with(['message' => 'Email sent successfully!']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComposerMessageRequest;
use App\Models\Email;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForwardEmailController extends Controller
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

                $email = Email::create([
                    'subject' => $request->validated('subject'),
                    'from' => auth()->id(),
                    'is_read' => false,
                    'is_sent' => true,
                    'is_important' => false,
                    'owner_id' => User::where('email', $owner)->firstOrFail()->id,
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

                return redirect()->route('inbox')->with(['message' => 'Email forwarded successfully!']);
            });
    }
}

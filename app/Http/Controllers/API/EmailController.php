<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComposerMessageRequest;
use App\Models\Email;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $emails = Email::with(['receivers', 'messages', 'from'])->paginate(20);

        return response()->json($emails);
    }


    public function send(StoreComposerMessageRequest $request)
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
            });

        return response()->json(['message' => 'Email sent successfully!']);
    }
}

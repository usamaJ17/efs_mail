<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class JWTLoginController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->has('t')) {

            $tks = explode('.', $request->t);
            list($headb64, $bodyb64, $cryptob64) = $tks;
            $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64));

            $user = User::find($payload->sub);
            if ($user) {
                auth()->login($user);
                return redirect()->route('inbox');
            }

            abort(403, 'User Not Found');

        }

        abort(403, 'Token Not Found');
    }
}

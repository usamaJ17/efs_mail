<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;

class ToggleAsStarredEmailController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Email $email)
    {
        if ($email->is_starred) {
            $email->update([
                'is_starred' => false,
            ]);
        } else {
            $email->update([
                'is_starred' => true,
            ]);
        }

        return redirect()->back();
    }
}

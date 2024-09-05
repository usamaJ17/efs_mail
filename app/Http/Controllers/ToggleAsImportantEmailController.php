<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;

class ToggleAsImportantEmailController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Email $email)
    {
        if ($email->is_important) {
            $email->update([
                'is_important' => false,
            ]);
        } else {
            $email->update([
                'is_important' => true,
            ]);
        }

        return redirect()->back();
    }
}

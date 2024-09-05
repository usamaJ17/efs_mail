<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;

class ToggleAsReadEmailController extends Controller
{
    public function __invoke(Email $email)
    {
        if ($email->is_read) {
            $email->update([
                'is_read' => false
            ]);
        } else {
            $email->update([
                'is_read' => true
            ]);
        }

        return back();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Models\User;

class LocaleSwitchController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, ['id', 'en']), 404);

        /** @var User $user */
        $user = $request->user();
        $user->update(['preferred_locale' => $locale]);

        return redirect()->back();
    }
}

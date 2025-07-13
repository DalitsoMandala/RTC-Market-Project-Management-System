<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Helpers\RoleBasedRedirectHelper;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        $redirectUrl = RoleBasedRedirectHelper::getDashboardRoute($user);
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended($redirectUrl)
            : view('auth.verify-email');
    }
}

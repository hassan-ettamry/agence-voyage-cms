<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SecurityEventLogger;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function __construct(private SecurityEventLogger $securityLogger) {}

    public function notice(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectAfterVerification($request);
        }

        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        $this->securityLogger->record('auth.email_verified', $request->user(), $request);

        return $this->redirectAfterVerification($request)
            ->with('status', 'Votre adresse email a été vérifiée.');
    }

    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectAfterVerification($request);
        }

        $request->user()->sendEmailVerificationNotification();
        $this->securityLogger->record('auth.email_verification_resent', $request->user(), $request);

        return back()->with('status', 'verification-link-sent');
    }

    private function redirectAfterVerification(Request $request)
    {
        if ($request->user()->agency?->shouldAutoStartOnboarding()) {
            return redirect()->route('onboarding.profile');
        }

        return redirect()->route('dashboard');
    }
}

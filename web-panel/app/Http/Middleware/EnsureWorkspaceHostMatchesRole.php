<?php

namespace App\Http\Middleware;

use App\CPU\Helpers;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceHostMatchesRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('admin')->check()) {
            return $next($request);
        }

        $host = strtolower((string) $request->getHost());
        $isGuestDemo = Helpers::returns_user_is_guest_demo();

        if (Helpers::dossentry_is_public_demo_host($host) && !$isGuestDemo) {
            return $this->redirectToInternalWorkspace($request);
        }

        if (Helpers::dossentry_is_internal_admin_host($host) && $isGuestDemo) {
            return $this->redirectToGuestDemo($request);
        }

        return $next($request);
    }

    protected function redirectToInternalWorkspace(Request $request): RedirectResponse
    {
        auth('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->away(
            Helpers::dossentry_internal_admin_login_url() . '?notice=internal-workspace-only'
        );
    }

    protected function redirectToGuestDemo(Request $request): RedirectResponse
    {
        auth('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->away(
            Helpers::dossentry_guest_demo_login_url() . '?notice=guest-demo-only'
        );
    }
}

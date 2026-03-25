<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $userRole = auth()->user()->role?->name;
        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized. Required role: ' . implode(' or ', $roles));
        }
        return $next($request);
    }
}

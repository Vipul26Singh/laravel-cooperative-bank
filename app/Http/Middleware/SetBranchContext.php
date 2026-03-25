<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetBranchContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->branch_id) {
            session(['branch_id' => auth()->user()->branch_id]);
            view()->share('currentBranch', auth()->user()->branch);
        }
        return $next($request);
    }
}

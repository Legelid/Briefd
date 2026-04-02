<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! session('current_workspace_id')) {
            $workspace = $user->workspaces()->first();

            if (! $workspace) {
                return redirect()->route('workspaces')->with('message', 'Please create a workspace to get started.');
            }

            session(['current_workspace_id' => $workspace->id]);
        }

        return $next($request);
    }
}

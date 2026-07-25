<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Ensure the authenticated user has one of the given roles.
     *
     * Usage: ->middleware('role:admin') or 'role:admin,streamer'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $role = $user?->role instanceof \App\Enums\Role ? $user->role->value : $user?->role;

        if (! $user || ! in_array($role, $roles, true)) {
            abort(403, 'ທ່ານບໍ່ມີສິດເຂົ້າເຖິງສ່ວນນີ້');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HandleEditHash
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (session()->has('edit_hash')) {
                session()->forget('edit_hash');
            }
        } else {
            // when creating a bin, generate edit_hash
            if (
                $request->route()->getName() === 'bin.edit' ||
                $request->route()->getName() === 'bin.store'
            ) {
                if (!session()->has('edit_hash')) {
                    session()->put('edit_hash', Str::random(80));
                }
            }

            // if guest user navigates outside the edit page, forget edit hash
            if (!Str::startsWith($request->route()->getName(), ['bin.', 'image.'])) {
                if (session()->has('edit_hash')) {
                    session()->forget('edit_hash');
                }
            }
        }

        return $next($request);
    }
}

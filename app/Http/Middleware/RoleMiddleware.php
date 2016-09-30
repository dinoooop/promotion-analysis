<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class RoleMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        if (!User::hasrole('sup_admin_cap')) {
            return Response::make(View::make('errors.404', ['page_404' => true]), 404);
        }

        return $next($request);
    }

}

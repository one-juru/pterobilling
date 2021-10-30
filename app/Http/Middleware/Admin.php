<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class Admin
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next, $type = null)
  {
    if ($request->user()->is_admin) {
      return $next($request);
    } else {
      if ($type) {
        if ($type == 'api') {
          return response('', 401);
        }
      }

      return redirect('/');
    }
  }
}

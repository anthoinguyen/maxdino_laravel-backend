<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Http\Controllers\BaseApiController;

class CheckActive extends BaseApiController
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    try {
      if(!$request->user->active)
      {
        //return
        return $this->responseErrorCustom('account_deactive', 403);
      }
    } catch (Exception $exception) {
      //return
      return $this->responseErrorException($exception->getMessage(),99999, 500);
    }
    return $next($request);
  }
}

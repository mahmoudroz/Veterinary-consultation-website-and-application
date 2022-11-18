<?php

namespace App\Http\Middleware;
use App\Traits\HandleApiJsonResponseTrait;
use Closure;

class ClinicAuth
{
    use HandleApiJsonResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next , $guard = null )
    {
        if ($guard != null) {
            auth()->shouldUse($guard);
        }
        $token = $request->header('token');
        $request->headers->set('token', (string) $token, true);
        $request->headers->set('Authorization', 'Bearer ' . $token, true);

        if (auth($guard)->check()  && auth($guard)->user()->type === 1 ) {

            if( auth($guard)->user()->active === 1){

                return $next($request);

            }else if( auth($guard)->user()->active === 0 ){
                return $this->error( __('clinic.not Active') );
            }else if( auth($guard)->user()->active === -1 ){
                return $this->error( __('clinic.you are Blocked') );
            }
        } else {
            return $this->error( __('clinic.Unauthorized') );
        }
    }
}

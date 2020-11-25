<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\User;
use App\Mail\Email\Noinsertarcomentario;
use Illuminate\Support\Facades\Mail;
class ValidacionAdmin
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
        if($request->user()->tokenCan('admin:admin'))
        {
            return $next($request);
        }
        $mail = Mail::to($equi)->send(new Noinsertarcomentario($usuario, $hi));
        return abort(401, "Scope Invalido, Token sin Permiso");
        
    }
}

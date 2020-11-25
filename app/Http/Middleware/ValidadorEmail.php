<?php

namespace App\Http\Middleware;
use App\Modelos\Producto;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\Email\Noinsertarcomentario;
use Illuminate\Support\Facades\Mail;

class ValidadorEmail
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
        $results = DB::select('select * from users where email_verified_at = :email_verified_at', ['email_verified_at' => 'verificado']);
        if($results)
        {
            if('verificado' == $results[0]->abilities)
            {
                return response()->json(['messeage' => 'Ya existe un administrador....'],200); 
            }   
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Email\Prueba;

class EmailController extends Controller
{
    public function MandarCorreo()
    {
        $correo = Mail::to('fernando03082000@gmail.com')->send(new Prueba());
        return response()->json(["correo"=>"CORREO ENVIADO"],200);
    }
}

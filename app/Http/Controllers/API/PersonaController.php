<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modelos\Persona;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\User;

use App\Mail\Email\Agregarpersona;


class PersonaController extends Controller
{
    public function index($id = null)
    {
        if($id)
        return response()->json(["persona"=>Persona::find($id)],200);
        return response()->json(["personas"=>Persona::all()],200);
    }
    public function guardar(Request $request)
    {
        $id = Auth::id();
        $hola = New User();
        $hola = User::find($id);
        $usuario = $hola->email;

        $hi = $request->nombre;
        $persona = new Persona();
        $persona->nombre = $request->nombre;
        $persona->apellido_pat = $request->apellido_pat;
        $persona->apellido_mat = $request->apellido_mat;

        if($persona->save())
        $request->merge(['usuario'=>$hola->email]);
        $mail = Mail::to($usuario)->send(new Agregarpersona($usuario, $hi));
        return response()->json(['messeage' => 'Registro guardado correctamente'],200);
        return response()->json(null,400);
    }
    public function destroy($id)
    {
       Persona::destroy($id);
       return response()->json(['res' => true, 'messeage' => 'Registro eliminado correctamente'],200);
    }
    public function update(Request $request, $id)
    {
        $update = new Persona();
        $update = Persona::find($id);
        $update -> nombre = $request->get('nombre');
        $update -> apellido_pat = $request->get('apellido_pat');
        $update -> apellido_mat = $request->get('apellido_mat');
        $update->save();
        return response()->json(['res' => true, 'messeage' => 'Registro modificado correctamente'],200);
    }
}

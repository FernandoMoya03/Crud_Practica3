<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modelos\Comentario;
use Illuminate\Support\Facades\DB;
use App\Mail\Email\Prueba;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\User;
use App\Admin;
use App\addmiinn;

//VISTAS
use App\Mail\Email\Agregarcomentario;
use App\Mail\Email\Eliminarcomentario;
use App\Mail\Email\Actualizarcomentario;
use App\Mail\Email\Agregarcomentarioadmin;
use App\Mail\Email\Actualizarcomentarioadmin;
use App\Mail\Email\Eliminarcomentarioadmin;
use App\Mail\Email\Noeliminarcomentario;
use App\Mail\Email\Noinsertarcomentario;
use App\Mail\Email\Noactualizarcomentario;
class ComentarioController extends Controller
{
    public function index($id = null)
    {
        if($id)
        return response()->json(["comentario"=>Comentario::find($id)],200);
        return response()->json(["comentarios"=>Comentario::all()],200);
    }
    public function guardar(Request $request)
    {
         $id = Auth::id();
         $hola = New User();
         $hola = User::find($id);
         $usuario = $hola->email;
         
         $oye = New Admin();
         $oye = Admin::find(1);
         $equi = $oye->admin;
         $hi = $request->comentario;
        if(($request->user()->tokenCan('admin:admin')) || $request->user()->tokenCan('user:permiso'))
        {
        $comentario = new Comentario();
        $comentario->comentario = $request->comentario;
        $comentario->producto_id = $request->producto_id;
        $comentario->persona_id = $request->persona_id;
        if($comentario->save())
        {
            $request->merge(['usuario'=>$hola->email]);
            $mail = Mail::to($usuario)->send(new Agregarcomentario($hi));
            $mail = Mail::to($equi)->send(new Agregarcomentarioadmin($usuario, $hi));
            return response()->json(['messeage' => 'Registro guardado correctamente'],200);
        }
       else{
        $mail = Mail::to($equi)->send(new Noinsertarcomentario($usuario, $hi));
        return response()->json(null,400);
       }
        }          
        $mail = Mail::to($equi)->send(new Noinsertarcomentario($usuario, $hi));
        return abort(401, "Scope Invalido, Token sin permiso");
    }
    public function destroy(Request $request, $id)
    {
       $id = Auth::id();
       $hola = New User();
       $hola = User::find($id);
       $usuario = $hola->email;
       $oye = New Admin();
       $oye = Admin::find(1);
       $equi = $oye->admin;
       if(($request->user()->tokenCan('admin:admin')) || $request->user()->tokenCan('user:permiso'))
       {
        Comentario::destroy($id);
        $request->merge(['usuario'=>$hola->email]);
        $mail = Mail::to($usuario)->send(new Eliminarcomentario());
        $mail = Mail::to($equi)->send(new Eliminarcomentarioadmin($usuario));
        return response()->json(['res' => true, 'messeage' => 'Registro eliminado correctamente'],200);
       }          
       $mail = Mail::to($equi)->send(new Noeliminarcomentario($usuario));
       return abort(401, "Scope Invalido, Token sin permiso");
   
    }
    public function update(Request $request, $id)
    {
       $idad = Auth::id();
       $hola = New User();
       $hola = User::find($idad);
       $usuario = $hola->email;
       $oye = New Admin();
       $oye = Admin::find(1);
       $equi = $oye->admin;
       $hi = $request->comentario;
       if(($request->user()->tokenCan('admin:admin')) || $request->user()->tokenCan('user:permiso'))
        {
        $update = new Comentario();
        $update = Comentario::find($id);
        $update -> comentario = $request->get('comentario');
        $update -> persona_id = $request->get('persona_id');
        $update -> producto_id = $request->get('producto_id');
        if($update->save())
        {
        $mail = Mail::to($usuario)->send(new Actualizarcomentario($hi));
         $mail = Mail::to($equi)->send(new Actualizarcomentarioadmin($usuario, $hi));
         return response()->json(['res' => true, 'messeage' => 'Registro modificado correctamente'],200);
        }
       }          
       $mail = Mail::to($equi)->send(new Noactualizarcomentario($usuario,$hi));
       return abort(401, "Scope Invalido, Token sin permiso");
    }
}

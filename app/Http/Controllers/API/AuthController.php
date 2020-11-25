<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Http\Controllers\API\PerfilController;
use App\User;
use App\Codigos;
//vistas.....
use App\Mail\Email\Prueba;
use App\Mail\Email\eliminar;
use App\Mail\Email\eliminaruser;
use App\Mail\Email\mandaradmin;


class AuthController extends Controller
{
    public function index(Request $request) //INDEX CON LOS 3 TIPOS DE PERMISO ESTABLECIDOS (USA SCOPE)
    {
        if(($request->user()->tokenCan('admin:admin')) || $request->user()->tokenCan('user:permiso'))
        {
            return response()->json(["users" => User::all()],200);
        }
        else if ($request->user()->tokenCan('user:info'))
        {
            return response()->json(["perfil" => $request->user()],200);
        }            
        return abort(401, "Scope Invalido");
    }
    public function logOut(Request $request) //BORRA TOKENS DE LA BD
    {
        return response()->json(["afectos"=>$request->user()->tokens()->delete()],200);
    }
    public function logIn(Request $request) //SE LOGEA USUARIO CREANDO UN TOKEN USER:INFO
    {
        $hi = $request->codigos;
        $results = DB::select('select * from codigo where codigos = :codigos', ['codigos' => $hi]);
        Log::info($results);
        if($results)
        {
            if($hi == $results[0]->codigos)
            {
                $request->validate([
                    'email' => 'required|email',
                    'password' => 'required',
                ]);
                $user = User::where('email', $request->email)->first();
                if(! $user || ! Hash::check($request->password, $user->password))
                {
                    throw ValidationException::withMessages([
                        'email|password' => ['Credenciales incorrectas...']
                    ]);
                }
                $token = $user ->createToken($request->email,['user:info'])->plainTextToken;
                return response()->json(["token"=>$token],201);
            }    
        }
        return response()->json(['messeage' => 'Codigo incorrecto....'],400); 
    }
    public function otorgarPermiso(Request $request) //OTORGA PERMISO USER:PERMISO SIEMPRE Y CUANDO EL TOKEN SEA ADMIN:ADMIN
    {
        if($request->user()->tokenCan('admin:admin'))
        {
            $request->validate(['email'=>'required|email']);
        }
        $user = User::where('email', $request->email)->first();
        if(!$user)
        {
            throw ValidationException::withMessages([
                'email' => ['Usuario incorrecto...']
            ]);
        }
        $token = $user ->createToken($request->email, ['user:permiso'])->plainTextToken;
        return response()->json(["token" => $token],201);
    }
    public function registro (Request $request) //REGISTRO DE USUARIO 
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'required'
        ]);
        $hi = $request->email;
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        if ($user->save())
        {
            $correo = Mail::to($hi)->send(new Prueba());
            return response()->json(["correo"=>"Cuenta creada, Verificar tu codigo de activacion..."],200);
        }
        return abor(400, "Error al general el registro");
    }
    public function delete(Request $request, $id) //SOLO PERMITE BORRAR CON TOKEN ADMIN:ADMIN (USA SCOPE)
    {
        $idadmin = Auth::id();
        $hola = New User();
        $hola = User::find($idadmin);
        $correo = $hola->email;
            $equis = Mail::to($correo)->send(new eliminaruser());
            User::destroy($id);
            return response()->json(['messeage' => 'Usuario eliminado correctamente'],200);
    }
    public function cambiocontraseña(Request $request, $id) //SOLO PERMITE CAMBIOS CON TOKENS USER:PERMISO USER:ADMIN (USA SCOPE)
    {
            $update = new User();
            $update = User::find($id);
            $update ->password = Hash::make($request->password);
            $update->save();
            return response()->json([true, 'messeage' => 'Registro modificado correctamente'],200);
    }
    public function crearadmin(Request $request) //SOLO PERMITE UN ADMINISTRADOR 
    {
        $hi = $request->email;
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();
        if(! $user || ! Hash::check($request->password, $user->password))
        {
            throw ValidationException::withMessages([
                'email|password' => ['Credenciales incorrectas...']
            ]);
        }
        DB::insert('insert into admin (admin) values (?)', [$hi]);
        $correo = Mail::to($hi)->send(new mandaradmin());
        $token = $user ->createToken($request->email,['admin:admin'])->plainTextToken;
        return response()->json(["token"=>$token],201);
    }
    public function validar()
    {
        $code = rand(100,1000);
        DB::insert('insert into codigo (codigos) values (?)', [$code]);
        return response()->json(["Tu cuenta ha sido validada, tu codigo de validacion es "=>$code],200);
    }
    public function actualizarPerfil(Request $request)
    {
            $id = Auth::id();
            $response = new Response();
            if($request->hasFile('archivo'))
            {
                $response = app('App\Http\Controllers\API\PerfilController')->subirfoto($request)->getOriginalContent();
            }
            if(($request->user()->tokenCan('admin:admin')) || $request->user()->tokenCan('user:info'))
            {
            $update = new User();
            $update = User::find($id);
            $update -> file = $response['path'];
            $update->save();
            } 
            return response()->json(["Tu foto de perfil se ha subido con exito "],200);    
    }
    
}
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\User;
class PerfilController extends Controller
{
    public function subirfoto(Request $request)
    {
        
        $id = Auth::id();
        $user = new User();
        $user = User::find($id);

        $nombre = $user->name;
        
        if($request->hasFile('archivo'))
        {
            $path = Storage::disk('public')->put($nombre, $request->archivo);
            return response()->json(["path"=>$path],201);
        }
        
    }
    
}

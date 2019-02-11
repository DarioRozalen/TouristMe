<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Usuario;
use \Firebase\JWT\JWT;

class userController extends Controller
{
    public function index()
    {
        if ($this->checkLogin()) 
        { 
            $usuariosCSave = Usuario::all();

            if (count($usuariosCSave) < 1)
            {
                return $this->success('No hay usuarios todavia');
            }

            return $this->success('Lista usuarios creados: ', $usuariosCSave);
        }
        else
        {
            return $this->error(400, "Acceso denegado en el index");
        }    
    }
    
    public function store(Request $request)
    {

        if ($this->checkLogin()) 
        { 
            if (!$request->filled("usuarioNombre") or !$request->filled("email") or !$request->filled("contrasena"))
            {
                return $this-> error(400, "Campos vacios");
            }

            $usuarioData = $this->getUsuarioData();
            $this->isUsedUsuarioNombre($request->usuarioNombre,$usuarioData->id);
            $usuario = new Usuario();
            $usuario->nombre = $request->usuarioNombre;
            $usuario->email = $request->email;
            $usuario->contrasena = $request->contrasena;
            $usuario->id_rol = $usuarioData->id;
            $usuario->save();
            return $this->success('usuario creado', $request->usuarioNombre);
        }
        else
        {
            return $this->error(400, "adios");
        }    
    }
    public function crearUsuario(Request $request)
    {

        if ($this->checkLogin()) 
        { 
            if (!$request->filled("usuarioNombre") or !$request->filled("email") or !$request->filled("contrasena"))
            {
                return $this-> error(400, "Campos vacios");
            }

            $usuarioData = $this->getUsuarioData();
            $this->isUsedUsuarioNombre($request->usuarioNombre,$usuarioData->id);
            $usuario = new Usuario();
            $usuario->nombre = $request->usuarioNombre;
            $usuario->email = $request->email;
            $usuario->contrasena = $request->contrasena;
            $usuario->id_rol = $usuarioData->id;
            $usuario->save();
            return $this->success('usuario creado', $request->usuarioNombre);
        }
        else
        {
            return $this->error(400, "adios");
        }    
    }
    public function show($usuarioNombre)
    {
        if ($this->checkLogin()) 
        {
            if(is_null($usuarioNombre))
            {
                return $this->error(400, "El nombre es necesario");
            }
            $usuarioData = $this->getUsuarioData();
            $usuarioSave = $this->oneUsuarioOfUsuario($usuarioData->id,$usuarioNombre);
            return $this->success('La categoria selecionada', $usuarioSave);
        }
        else
        {
            return $this->error(400, "Acceso denegado");
        } 
    }   

    public function update(Request $request, $id)
    {
        if ($this->checkLogin()) 
        { 
            $infoToken = $this->getUsuarioData();

            $newName = $request->newName;
            $newEmail = $request->newEmail;
            $newContrasena = $request->newContrasena;

            $usuarioSave = Usuario::where('id',$id)->first();

            if(!is_null($newName))
            {
                $usuarioSave->nombre = $newName;
            }
            if(!is_null($newEmail))
            {
                $usuarioSave->email = $newEmail;
            }
            if(!is_null($newContrasena))
            {
                $usuarioSave->contrasena = $newContrasena;
            }

            $usuarioSave->save();
            
            return $this->success('Usuario modificada', $usuarioSave);
        }
        else
        {
            return $this->error(400, "Acceso denegado");
        }
    }
    
    public function destroy($id)
    {
        if ($this->checkLogin()) 
        { 
            $usuarioNombre = $id;
            $usuarioSave = Usuario::where('id',$usuarioNombre)->first();
            $usuarioSave->delete();
            return $this->success('eliminado usuario', "");
        }
        else
        {
            return $this->error(400, "Acceso denegado");
        }       
    }
    
    private function isUsedUsuarioNombre($usuarioNombre,$id_rol)
    {
        $usuariosCSave = $this->allUsuariosOneUsuario($id_rol);
        foreach ($usuariosCSave as $Usuario => $UsuarioSave) 
        {
            if($UsuarioSave->email == $usuarioNombre)
            {
                exit($this->error(400,'El nombre del usuario ya existe'));
            }  
        }
    }

    private function allUsuariosOneUsuario($id)
    {
        return Usuario::where('id_rol', $id)->get();
    }
    private function oneusuarioOfUsuario($id,$usuarionombre)
    {
        $usuariosCSave = $this->allUsuariosOneUsuario($id);
        foreach ($usuariosCSave as $usuariosC => $categorie)
        {
            if($usuarionombre == $categorie->nombre)
            {
                return $categorie;
            }
        }
        exit($this->error(400,'Este usuario no existe, introduce otro que ya exista'));
    }
}

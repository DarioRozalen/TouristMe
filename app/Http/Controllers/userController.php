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
            return $this->error(400, "Acceso denegado");
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
            if($this->checkPassword($request->contrasena))
            {
                return $this->error(415,'La contraseña tiene que ser superior a 8 carecteres');
            }
            if($this->checkEmail($request->email))
            {
                return $this->error(415,'El email no es valido');
            }
            if($this->checkUsuarioExist($request->email))
            {
                return $this->error(415,'El usuario ya existe');
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
            return $this->error(400, "Acceso denegado");
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
            
            if($this->checkEmail($request->email))
            {
                return $this->error(415,'El email no es valido');
            }

            if($this->checkUsuarioExist($request->email))
            {
                return $this->error(415,'El usuario ya existe');
            }

            if($this->checkPassword($request->contrasena))
            {
                return $this->error(415,'La password tiene que ser superior a 8 carecteres');
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
            return $this->error(400, "Acceso denegado");
        }    
    }
    public function show($usuarioNombre)
    {
        if ($this->checkLogin()) 
        {
            if(is_null($request->email))
            {
                return $this->error(400, "El nombre del usuario tiene que estar rellenado");
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
            // if(empty($_POST['newName']) && empty($_POST['newEmail']) && empty($_POST['newContrasena']))
            // {
            //     return $this->error(400, 'Rellene almenos un campo');
            // }
            
            $usuarioSave = Usuario::where('id',$id)->first();
            

            if(!is_null($request->newName))
            {
                $usuarioSave->nombre = $request->newName;
            }
            if(!is_null($request->newEmail))
            {
                if($this->checkEmail($request->newEmail))
                {
                    return $this->error(415,'El email no es valido');
                } 

                if($this->checkUsuarioExist($request->newEmail))
                {
                    return $this->error(415,'El usuario ya existe');
                }  
                $usuarioSave->email = $request->newEmail;
            }
            if(!is_null($request->newContrasena))
            {
                if($this->checkPassword($request->newContrasena))
                {
                    return $this->error(415,'La password tiene que ser superior a 8 carecteres');
                }
                $usuarioSave->contrasena = $request->newContrasena;
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
                exit($this->error(400,'El nombre del email ya existe'));
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
        exit($this->error(400,'Este usuario no existe, introduce una que ya exista'));
    }
}

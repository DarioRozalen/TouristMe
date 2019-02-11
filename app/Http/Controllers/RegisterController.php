<?php
namespace App\Http\Controllers;
use \Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\Usuario;

class RegisterController extends Controller
{
    public function register (Request $request)
    {
        if (!isset($_POST['nombre']) or !isset($_POST['email']) or !isset($_POST['contrasena'])) 
        {
            return $this->error(400, $request);
        }

        $usuario = $this->deleteSpace($_POST['nombre']); 
        $email = $_POST['email'];
        $contrasena = $_POST['contrasena'];
        if($this->checkPassword($contrasena))
        {
            return $this->error(415,'La contraseña tiene que ser superior a 8 carecteres');
        }
        if($this->checkEmail($email))
        {
            return $this->error(415,'El email no es valido');
        }
        if($this->checkUsuarioExist($email))
        {
            return $this->error(415,'El usuario ya existe');
        }
        
        if (!empty($usuario) && !empty($email) && !empty($contrasena))
        {
            $usuarios = new Usuario();
            $usuarios->nombre = $usuario;
            $usuarios->contrasena = $contrasena;
            $usuarios->email = $email;
            $usuarios->save();
            return $this->success('Usuario registrado',"");                 
        }
        else
        {
            return $this->error(400,'No puede haber campos vacios');
        }    
    }
    public function checkPassword($contrasena)
    {
        if(strlen($contrasena) < 8)
        {
            return true;
        }
        return false;
    }
    public function checkEmail($email)
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return true;
        }
        return false;
    }
    public function checkUsuarioExist($email)
    {
        $usuarioData = Usuario::where('email',$email)->first();
        if(!is_null($usuarioData))
        {
            return true;
        }
        return false;
    }

    public function destroy($nombre)
    {
        if ($this->checkLogin()) 
        { 
            $usuarioNombre = $nombre;
            $usuarioSave = Lugares::where('nombre',$usuarioNombre)->first();
            $usuarioSave->delete();
            return $this->success('Cuenta eliminada', "");
        }
        else
        {
            return $this->error(400, "Acceso denegado");
        }       
    }
}
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Usuario;//Para poder acceder al modelo de User
class LoginController extends Controller
{
    public function login(Request $req)
    {
        if (!isset($_POST['email']) or !isset($_POST['contrasena'])) 
        {
            return $this->error(400, 'Campos incompletos');
        }

        if (empty($_POST['email']) and empty($_POST['contrasena']))
        {

            return $this->error(400,'No puede haber campos vacios');
        }

        if($this->checkEmail($_POST['email']))
        {
            return $this->error(415,'El email no es valido');
        }

        if($this->checkPassword($_POST['contrasena']))
        {
            return $this->error(415,'La password tiene que ser superior a 8 carecteres');
        }

        $email = $_POST['email'];
        $contrasena = $_POST['contrasena'];

        if ($this->checkExist($email,$contrasena))
        {
            $usuarioSave = Usuario::where('email', $email)->first();
            $usuarioData = array(
                'id' => $usuarioSave->id,
                'nombre' => $usuarioSave->nombre,
                'email' => $usuarioSave->email,
                'contrasena' => $usuarioSave->contrasena,
                'id_rol' => $usuarioSave->id_rol,
            );
            $token = JWT::encode($usuarioData, $this->key);
            return $this->success("Usuario logueado",$token);
        }
        else
        {
            return $this->error(400, 'No tienes permisos');
        }
    }
    public function checkExist($email,$contrasena)
    {   
        $usuarioSave = Usuario::where('email', $email)->first();
        
        if(!is_null($usuarioSave) && $usuarioSave->contrasena == $contrasena && $usuarioSave->id_rol == 0)
        {
            return true;
        }
        return false;
    }
}
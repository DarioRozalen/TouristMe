<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use \Firebase\JWT\JWT;
use App\Usuario;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected $key = '^fg?4xtyDXcjb5c__aXWb$J?2wn#9jBB4Wbc68d4YUDsB*ZuQ$p4b!rj';
    //La uso a la hora de hacer return e identificar el error
    protected function error($code, $message)
    {
        $json = ['message' => $message];
        $json = json_encode($json);
        return  response($json, $code)->header('Access-Control-Allow-Origin', '*');
    }
    //La uso a la hora de hacer return
    protected function success($message, $data = [])
    {
        $json = ['message' => $message, 'data' => $data];
        $json = json_encode($json);
        return  response($json, 200)->header('Access-Control-Allow-Origin', '*');
    }
    //Me devuelve el header deseado
    protected function getOneHeader($header)
    {
        $headers = getallheaders();
        if(isset($headers[$header]))
        {
            $header = $headers[$header];
            return $header;
        }
        return null;    
    }
    //Me comprueba si hay o no un token
    private function getToken()
    {
        $token = $this->getOneHeader("Authorization");
        if(is_null($token))
        {
            return $this->error(400, "Debes logearte");
        }
        return $token;
    }
    //Me permite decodificar el token y obtener toda la info que tenga este
    protected function getUsuarioData()
    {
        try 
        {
            $usuarioData = JWT::decode($this->getToken(), $this->key, array('HS256'));
            return $usuarioData;      
        } 
        catch (\Exception $e) 
        {
            return null;
        }   
    }
    //Me comprueba el login
    protected function checkLogin()
    {
        $usuarioData = $this->getUsuarioData();
        if(is_null($usuarioData))
        {
            return false;
        }
        $usuarioSave = Usuario::where('email', $usuarioData->email)->first();
        
        if(!is_null($usuarioSave) && $usuarioSave->contrasena == $usuarioData->contrasena)
        {
            return true;
        }
        return false;
    } 
    //Me borra los espacios en caso de introducir alguno
    public function deleteSpace($string)
    {
        $string = str_replace(' ', '', $string);
        return $string;
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
        if(!strpos($email,"@") || !strpos($email, "."))
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
} 
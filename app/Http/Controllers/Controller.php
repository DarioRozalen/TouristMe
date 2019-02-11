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
    protected function error($code, $message)
    {
        $json = ['message' => $message];
        $json = json_encode($json);
        return  response($json, $code)->header('Access-Control-Allow-Origin', '*');
    }
    protected function success($message, $data = [])
    {
    	$json = ['message' => $message, 'data' => $data];
        $json = json_encode($json);
        return  response($json, 200)->header('Access-Control-Allow-Origin', '*');
    }
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
    private function getToken()
    {
    	$token = $this->getOneHeader("Authorization");
    	if(is_null($token))
    	{
    		return $this->error(400, "Debes logearte");
    	}
    	return $token;
    }
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

    protected function checkLogin()
    {
    	$usuarioData = $this->getUsuarioData();
        if(is_null($usuarioData))
        {
            return false;
        }

        $usuarioSave = Usuario::where('email', $usuarioData->email)->first();
        
        if(!is_null($usuarioSave) && $usuarioSave->contrasena == $usuarioData->contrasena && $usuarioSave->id_rol == 0)
        {
            return true;
        }if(!is_null($usuarioSave) && $usuarioSave->contrasena == $usuarioData->contrasena && $usuarioSave->id_rol == 1)
        {
            return false;
        }
    } 


    protected function deleteSpace($string)
    {
        $string = str_replace(' ', '', $string);
        return $string;
    }

} 
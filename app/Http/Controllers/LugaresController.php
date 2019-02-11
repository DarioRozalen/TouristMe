<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Lugares;
use \Firebase\JWT\JWT;

class LugaresController extends Controller
{
    public function index()
    {
        if ($this->checkLogin()) 
        { 
            $usuarioData = $this->getUsuarioData();
            $lugaresSave = $this->allLugaresOneUsuario($usuarioData->id);

            if (count($lugaresSave) < 1)
            {
            	return $this->success('No hay lugares todavia');
            }

            return $this->success('Lista lugares creados: ', $lugaresSave);
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

        	if (!$request->filled("lugarNombre") or !$request->filled("fechaInicio") or !$request->filled("fechaFin"))
         {
          return $this-> error(400, "Campos vacios");
      }

      $usuarioData = $this->getUsuarioData();
      $this->isUsedLugarNombre($request->lugarNombre,$usuarioData->id);
      $lugar = new Lugares();
      $lugar->nombre = $request->lugarNombre;
      $lugar->descripcion = $request->descripcion;
      $lugar->fechaInicio = $request->fechaInicio;
      $lugar->fechaFin = $request->fechaFin;
      $lugar->coordenadasX = $request->coordenadasX;
      $lugar->coordenadasY = $request->coordenadasY;
      $lugar->id_usuario = $usuarioData->id;
      $lugar->save();
      return $this->success('Lugar creado', $request->lugarNombre);
  }
  else
  {
    return $this->error(400, "Acceso denegado");
}    
}

public function show($lugarNombre)
{
    if ($this->checkLogin()) 
    {
        if(is_null($lugarNombre))
        {
            return $this->error(400, "El nombre del lugar tiene que estar rellenado");
        }
        $usuarioData = $this->getUsuarioData();
        $lugarSave = $this->oneLugarOfUsuario($usuarioData->id,$lugarNombre);
        return $this->success('La categoria selecionada', $lugarSave);
    }
    else
    {
        return $this->error(400, "Acceso denegado");
    } 
}   

public function update(Request $request, $lugar)
{
    if ($this->checkLogin()) 
    { 
        if(is_null($lugar))
        {
            return $this->error(400, "El nombre del lugar tiene que estar rellenado");
        }
        if(!$request->filled("newLugarNombre"))
        {
            return $this->error(400, "El nombre del nuevo lugar tiene que estar rellenado");
        }
        if(is_null($lugar))
        {
            return $this->error(400, "El nombre del lugar que quieres cambiar debe estar rellenado");
        }
        $newNombre = $request->newLugarNombre;
        $lugarNombre = $lugar;
        $usuarioData = $this->getUsuarioData();
        $this->isUsedLugarNombre($usuarioData->id,$newNombre);
        $lugarSave = $this->oneLugarOfUsuario($usuarioData->id,$lugar);
        $lugarSave->nombre = $newNombre;
        $lugarSave->save();
        return $this->success('Lugar modificada', $lugarSave);
    }
    else
    {
        return $this->error(400, "Acceso denegado");
    }
}

public function destroy($lugar)
{
    if ($this->checkLogin()) 
    { 
        $lugarNombre = $lugar;
        $lugarSave = Lugares::where('nombre',$lugarNombre)->first();
        $lugarSave->delete();
        return $this->success('eliminado lugar', "");
    }
    else
    {
        return $this->error(400, "Acceso denegado");
    }       
}

private function isUsedLugarNombre($lugarNombre,$id_usuario)
{
    $lugaresSave = $this->allLugaresOneUsuario($id_usuario);
    foreach ($lugaresSave as $Lugar => $LugarSave) 
    {
        if($LugarSave->nombre == $lugarNombre)
        {
            exit($this->error(400,'El nombre del lugar ya existe'));
        }  
    }
}

private function allLugaresOneUsuario($id)
{
    return Lugares::where('id_usuario', $id)->get();
}
private function onelugarOfUsuario($id,$lugarnombre)
{
    $lugaresSave = $this->allLugaresOneUsuario($id);
    foreach ($lugaresSave as $lugares => $categorie)
    {
        if($lugarnombre == $categorie->nombre)
        {
            return $categorie;
        }
    }
    exit($this->error(400,'Este lugar no existe, introduce una que ya exista'));
}
}

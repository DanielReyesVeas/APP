<?php

class Aporte extends Eloquent {
    
    protected $table = 'aportes_cuentas';
    
    public function cuenta($cuentas=null){
        $nombre = "";
        $idCuenta = "";

        if($this->cuenta_id){
            $bool = true;
            $idCuenta = $this->cuenta_id;
            if(!$cuentas){
                $cuentas = Cuenta::listaCuentas();
            }
            foreach($cuentas as $cuenta){
                if($cuenta['id']==$idCuenta){
                    $nombre = $cuenta['nombre'];
                }
            }
        }
        $datos = array(
            'id' => $idCuenta,
            'nombre' => $nombre
        );
        
        return $datos;
	}
    
    static function aportes()
    {
        $aportes = Aporte::all();
        
        return $aportes;
    }
    
    static function isCuentas()
    {
        $aportes = Aporte::all();
        $bool = true;
        foreach($aportes as $aporte){
            if(!$aporte->cuenta_id){
                $bool = false;
            }
        }
        
        return $bool;
    }
    
    public function afp(){
        $afp = Glosa::find($this->nombre);
        
        return $afp->glosa;
	}
    
    public function exCaja(){
        $exCaja = Glosa::find($this->nombre);
        
        return $exCaja->glosa;
	}
    
    static function errores($datos){
         
        $rules = array(
            'nombre' => 'required'
        );

        $message = array(
            'aporte.required' => 'Obligatorio!'
        );

        $verifier = App::make('validation.presence');
        $verifier->setConnection("principal");

        $validation = Validator::make($datos, $rules, $message);
        $validation->setPresenceVerifier($verifier);

        if($validation->fails()){
            return $validation->messages();
        }else{
            return false;
        }
    }
}
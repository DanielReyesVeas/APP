<?php

class TipoHaber extends Eloquent {
    
    protected $table = 'tipos_haber';
    
    public function haberes(){
        return $this->hasMany('Haber','tipo_haber_id');
    }
    
    public function cuenta($cuentas=null){
        $nombre = "";
        $idCuenta = "";
        
        $isCME = \Session::get('empresa')->isCME;
        if($isCME){
            if($this->cuenta_id){
                $bool = true;
                $idCuenta = $this->cuenta_id;
                if(!$cuentas){
                    $cuentas = Cuenta::listaCuentas();
                }
                if(count($cuentas)){
                    foreach($cuentas as $cuenta){
                        if($cuenta['id']==$idCuenta){
                            $nombre = $cuenta['nombre'];
                        }
                    }
                }
            }
        }
        
        $datos = array(
            'id' => $idCuenta,
            'nombre' => $nombre
        );
        
        return $datos;
	}
    
    static function isCuentas()
    {
        $tiposHaber = TipoHaber::all();
        $bool = true;
        foreach($tiposHaber as $tipoHaber){
            if(!$tipoHaber->cuenta_id){
                $bool = false;
            }
        }
        
        return $bool;
    }
    
    static function listaTiposHaber(){
    	$listaTiposHaber = array();
    	$tiposHaber = TipoHaber::orderBy('nombre', 'ASC')->get();
    	if( $tiposHaber->count() ){
            foreach( $tiposHaber as $tipoHaber ){
                if($tipoHaber->id>15){
                    $listaTiposHaber[]=array(
                        'id' => $tipoHaber->id,
                        'imponible' => $tipoHaber->imponible ? true : false,
                        'nombre' => $tipoHaber->nombre
                    );
                }
            }
    	}
    	return $listaTiposHaber;
    }
    
    public function misHaberes(){
        
        $idTipoHaber = $this->id;
        $listaHaberes = array();
        $idMes = \Session::get('mesActivo')->id;
        $mes = \Session::get('mesActivo')->mes;
        $misHaberes = Haber::where('tipo_haber_id', $idTipoHaber)->where('mes_id', $idMes)->orWhere('permanente', 1)->orWhere('rango_meses', 1)->where('desde', '<=', $mes)->where('hasta', '>=', $mes)->get();
        
        if( $misHaberes->count() ){
            foreach($misHaberes as $haber){
                $listaHaberes[] = array(
                    'id' => $haber->id,
                    'sid' => $haber->sid,
                    'moneda' => $haber->moneda,
                    'monto' => $haber->monto,
                    'trabajador' => $haber->trabajadorHaber(),
                    'fechaIngreso' => $haber->created_at
                );
            }
        }
        return $listaHaberes;
    }
    
    static function errores($datos){
         
        $rules = array(
            'codigo' => 'required',
            'nombre' => 'required'
        );

        $message = array(
            'tipoHaber.required' => 'Obligatorio!'
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
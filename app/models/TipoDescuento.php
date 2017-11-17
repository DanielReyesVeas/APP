<?php

class TipoDescuento extends Eloquent {
    
    protected $table = 'tipos_descuento';
    
    public function descuentos(){
        return $this->hasMany('Descuento','tipo_descuento_id');
    }
    
    public function afp(){
        return $this->belongsTo('Glosa', 'afp_id');
    }
    
    public function formaPago(){
        return $this->belongsTo('Glosa', 'forma_pago');
    }
    
    public function estructuraDescuento(){
        return $this->belongsTo('EstructuraDescuento','estructura_descuento_id');
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
    
    public function nombreAfp(){
        $afp = Glosa::find($this->nombre);
        
        return $afp->glosa;
	}
    
    public function nombreIsapre(){
        $isapre = Glosa::find($this->nombre);
        
        return $isapre->glosa;
	}
    
    static function isCuentas()
    {
        $tiposDescuento = TipoDescuento::all();
        $bool = true;
        foreach($tiposDescuento as $tipoDescuento){
            if(!$tipoDescuento->cuenta_id){
                $bool = false;
            }
        }
        
        return $bool;
    }
    
    static function listaTiposDescuento(){
    	$listaTiposDescuento = array();
    	$tiposDescuento = TipoDescuento::orderBy('id', 'ASC')->get();
    	if( $tiposDescuento->count() ){
            foreach( $tiposDescuento as $tipoDescuento ){
                if($tipoDescuento->id!=1 && $tipoDescuento->id!=3 && $tipoDescuento->estructura_descuento_id!=4 && $tipoDescuento->estructura_descuento_id!=5 && $tipoDescuento->estructura_descuento_id!=9){
                    if($tipoDescuento->estructuraDescuento->id==3){
                        $nombre = 'APVC AFP ' . $tipoDescuento->nombreAfp();
                    }else if($tipoDescuento->estructuraDescuento->id==7){
                        $nombre = 'Cuenta de Ahorro AFP ' . $tipoDescuento->nombreAfp();
                    }else{
                        $nombre = $tipoDescuento->nombre;
                    } 
                    $listaTiposDescuento[]=array(
                        'id' => $tipoDescuento->id,
                        'nombre' => $nombre
                    );
                }
            }
    	}
    	return $listaTiposDescuento;
    }
    
    public function misDescuentos(){
        
        $idTipoDescuento = $this->id;
        $listaDescuentos = array();
        $idMes = \Session::get('mesActivo')->id;
        $mes = \Session::get('mesActivo')->mes;
        $misDescuentos = Descuento::where('tipo_descuento_id', $idTipoDescuento)->where('mes_id', $idMes)->orWhere('permanente', 1)->orWhere('rango_meses', 1)->where('desde', '<=', $mes)->where('hasta', '>=', $mes)->get();
        
        if( $misDescuentos->count() ){
            foreach($misDescuentos as $descuento){
                $listaDescuentos[] = array(
                    'id' => $descuento->id,
                    'sid' => $descuento->sid,
                    'moneda' => $descuento->moneda,
                    'monto' => $descuento->monto,
                    'trabajador' => $descuento->trabajadorDescuento(),
                    'fechaIngreso' => $descuento->created_at
                );
            }
        }
        return $listaDescuentos;
    }
    
    static function errores($datos){
         
        $rules = array(
            'codigo' => 'required',
            'nombre' => 'required'
        );

        $message = array(
            'tipoDescuento.required' => 'Obligatorio!'
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
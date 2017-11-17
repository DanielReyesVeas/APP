<?php

class SeguroDeCesantia extends Eloquent {
    
    protected $table = 'seguro_de_cesantia';
    protected $connection = "principal";
    
    static function listaSeguroDeCesantia($mes=null){
        
        if(!$mes){
            $mes = \Session::get('mesActivo')->mes;
        }
        
    	$listaSeguroDeCesantia = array();
    	$segurosDeCesantias = SeguroDeCesantia::where('mes', $mes)->orderBy('id', 'ASC')->get();
    	if( $segurosDeCesantias->count() ){
            foreach( $segurosDeCesantias as $seguroDeCesantia ){
                $listaSeguroDeCesantia[]=array(
                    'id' => $seguroDeCesantia->id,
                    'tipoContrato' => $seguroDeCesantia->tipo_contrato,
                    'financiamientoEmpleador' => $seguroDeCesantia->financiamiento_empleador,
                    'financiamientoTrabajador' => $seguroDeCesantia->financiamiento_trabajador
                );
            }
    	}
    	return $listaSeguroDeCesantia;
    }
    
    static function errores($datos){
         
        $rules = array(
            'valor' => 'required',
            'nombre' => 'required'
        );

        $message = array(
            'seguroDeCesantia.required' => 'Obligatorio!'
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
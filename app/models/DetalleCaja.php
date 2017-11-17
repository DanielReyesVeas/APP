<?php

class DetalleCaja extends Eloquent {
    
    protected $table = 'detalles_caja';
    
    public function liquidacion(){
        return $this->belongsTo('Liquidacion','liquidacion_id');
    }
    
    public function codigoCaja()
    {        
        $caja = $this->caja_id;
        $codigo = Codigo::find($caja)->codigo;
        
        return $codigo;
    }
    
    static function errores($datos){
         
        $rules = array(

        );

        $message = array(
            'detalleCaja.required' => 'Obligatorio!'
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

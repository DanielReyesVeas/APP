<?php

class DetalleIpsIslFonasa extends Eloquent {
    
    protected $table = 'detalles_ips_isl_fonasa';
    
    public function liquidacion(){
        return $this->belongsTo('Liquidacion','liquidacion_id');
    }
    
    static function errores($datos){
         
        $rules = array(

        );

        $message = array(
            'detalleIpsIslFonasa.required' => 'Obligatorio!'
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

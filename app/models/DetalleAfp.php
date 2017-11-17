<?php

class DetalleAfp extends Eloquent {
    
    protected $table = 'detalles_afp';
    
    public function liquidacion(){
        return $this->belongsTo('Liquidacion','liquidacion_id');
    }
    
    public function codigoAfp()
    {
        $afp = $this->afp_id;
        $codigo = Codigo::find($afp)->codigo;
        
        return $codigo;
    }
    
    static function errores($datos){
         
        $rules = array(

        );

        $message = array(
            'detalleAfp.required' => 'Obligatorio!'
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

<?php

class DetalleMutual extends Eloquent {
    
    protected $table = 'detalles_mutual';
    
    public function liquidacion(){
        return $this->belongsTo('Liquidacion','liquidacion_id');
    }
    
    public function codigoMutual()
    {        
        $mutual = $this->mutual_id;
        $codigo = Codigo::find($mutual)->codigo;
        
        return $codigo;
    }
    
    static function errores($datos){
         
        $rules = array(

        );

        $message = array(
            'detalleMutual.required' => 'Obligatorio!'
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

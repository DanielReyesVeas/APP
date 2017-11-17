<?php

class DetalleSalud extends Eloquent {
    
    protected $table = 'detalles_salud';
    
    public function liquidacion(){
        return $this->belongsTo('Liquidacion','liquidacion_id');
    }
    
    public function codigoSalud()
    {        
        $salud = $this->salud_id;
        $codigo = Codigo::find($salud)->codigo;
        
        return $codigo;
    }
    
    static function errores($datos){
         
        $rules = array(

        );

        $message = array(
            'detalleSalud.required' => 'Obligatorio!'
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

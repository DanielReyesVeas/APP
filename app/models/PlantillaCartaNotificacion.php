<?php

class PlantillaCartaNotificacion extends Eloquent {
    
    protected $table = 'plantillas_cartas_notificacion';
        
    public function cartaNotificacion(){
        return $this->hasMany('CartaNotificacion','plantilla_carta_id');
    }
    
    static function errores($datos){
         
        $rules = array(
            'nombre' => 'required',
            'cuerpo' => 'required'
        );

        $message = array(
            'plantillaCartaNotificacion.required' => 'Obligatorio!'
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
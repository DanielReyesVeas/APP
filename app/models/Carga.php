<?php

class Carga extends Eloquent {
    
    protected $table = 'cargas_familiares';
    
    public function trabajador(){
        return $this->belongsTo('FichaTrabajador','ficha_trabajador_id');
    }
    
    public function tipoCarga(){
        return $this->belongsTo('TipoCarga','tipo_carga_id');
    }
    
    public function trabajadorCarga(){        
        if( $this->trabajador ){
            $trabajador = $this->trabajador;
            $datosTrabajador = array(
                'id' => $trabajador->id,
                'sid' => $trabajador->sid,
                'nombreCompleto' => $trabajador->nombres . " " . $trabajador->apellidos,
                'rutFormato' => Funciones::formatear_rut($trabajador->rut)
            );        
        }
        return $datosTrabajador;
    }
    
    static function errores($datos){
         
        $rules = array(
            /*'trabajador_id' => 'required',
            'parentesco' => 'required',
            'rut' => 'required',
            'nombres' => 'required',
            'apellidos' => 'required',
            'fecha_nacimiento' => 'required',
            'sexo' => 'required'*/
        );

        $message = array(
            'carga.required' => 'Obligatorio!'
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
<?php

class Vacaciones extends Eloquent {
    
    protected $table = 'vacaciones';

    public function trabajador(){
        return $this->belongsTo('Trabajador','trabajador_id');
    }
    
    public function mes(){
        return $this->belongsTo('MesDeTrabajo','mes_id');
    }
    
    static function calcularVacaciones($trabajador, $empleado, $mesActual)
    {
        $idTrabajador = $trabajador->id;        
        $i = 0;
        do{
            $i = ($i + 1);
            if(!$mesActual){
                $mesActual = \Session::get('mesActivo');
            }
            $mes = $mesActual->mes;
            $idMesActual = $mesActual->id;
            $fecha = date('Y-m-d', strtotime('-' . $i . ' month', strtotime($mes)));
            $idMesAnterior = MesDeTrabajo::where('mes', $fecha)->first()->id;

            $misVacaciones = Vacaciones::where('trabajador_id', $idTrabajador)->where('mes_id', $idMesAnterior)->first();
            
        }while(!$misVacaciones && $idMesAnterior!=1);
        
        if($misVacaciones){
            $vacas = ($misVacaciones->dias + (1.25 * $i));
        }else{
            $vacas = 0;
        }
        
        $vacaciones = new Vacaciones();
        $vacaciones->sid = Funciones::generarSID();;
        $vacaciones->trabajador_id = $idTrabajador;
        $vacaciones->mes_id = $idMesActual;
        $vacaciones->dias = $vacas;
        $vacaciones->save(); 
        
        return;
    }
    
    static function errores($datos){
         
        $rules = array(

        );

        $message = array(
            'vacaciones.required' => 'Obligatorio!'
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
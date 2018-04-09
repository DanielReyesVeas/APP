<?php

class Prestamo extends Eloquent {
    
    protected $table = 'prestamos';
    
    public function trabajador(){
        return $this->belongsTo('Trabajador','trabajador_id');
    }
    
    public function cuotas(){
        return $this->hasMany('Cuota','prestamo_id');
    }
    
    public function trabajadorPrestamo()
    {        
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
    
    public function cuotasPrestamo()
    {   
        $idPrestamo = $this->id;
        $cuotas = Cuota::where('prestamo_id', $idPrestamo)->orderBy('mes')->get();
        $datosCuotas = array();
        if($cuotas){            
            foreach($cuotas as $cuota){
                $datosCuotas[] = array(
                    'id' => $cuota->id,
                    'sid' => $cuota->sid,
                    'numero' => $cuota->numero,
                    'monto' => $cuota->monto,
                    'mes' => $cuota->mes
                );     
            }
        }
        return $datosCuotas;
    }
    
    public function cuotaPagar()
    {
        $idPrestamo = $this->id;
        $cuota = new stdClass();
        $mes = \Session::get('mesActivo')->mes;
        $cuotas = Cuota::where('prestamo_id', $idPrestamo)->where('mes', $mes)->first();

        $cuota->id = $cuotas->id;
        $cuota->sid = $cuotas->sid;
        $cuota->numero = $cuotas->numero;
        $cuota->monto = $cuotas->monto;
        
        return $cuota;
    }
    
    public function eliminarPrestamo()
    {
        $idPrestamo = $this->id;
        $cuotas = Cuota::where('prestamo_id', $idPrestamo)->get();
        if($cuotas->count()){
            foreach($cuotas as $cuota){
                $cuota->delete();
            }
        }
        $this->delete();
    }
    
    static function errores($datos){
         
        $rules = array(
            'trabajador_id' => 'required',
            //'fecha' => 'required',
            'glosa' => 'required',
            'nombre_liquidacion' => 'required',
            'moneda' => 'required',
            'monto' => 'required',
            'cuotas' => 'required',
            'primera_cuota' => 'required',
            'ultima_cuota' => 'required'
        );

        $message = array(
            'prestamo.required' => 'Obligatorio!'
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
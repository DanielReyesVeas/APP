<?php

class MesDeTrabajo extends Eloquent {
    
    protected $table = 'meses_de_trabajo';
    
    public function anioRemuneracion(){
        return $this->belongsTo('AnioRemuneracion','anio_id');
    }
    
    public function haberes(){
        return $this->hasMany('Haber','mes_id');
    }
    
    public function descuentos(){
        return $this->hasMany('Descuento','mes_id');
    }
    
    public function estado(){
        $bool = false;
        $mes = ValorIndicador::where('fecha', $this->fecha_remuneracion)->get();
        if($mes->count()){
            $bool = true;
        }
        return $bool;
    }
    
    static function listaMesesDeTrabajo()
    {
    	$listaMesesDeTrabajo = array();
        $idMes = \Session::get('mesActivo')->id;
    	$mesesDeTrabajo = MesDeTrabajo::orderBy('mes', 'DESC')->get();
    	if( $mesesDeTrabajo->count() ){
            foreach( $mesesDeTrabajo as $mesDeTrabajo ){
                if($mesDeTrabajo->id!=$idMes){
                    $listaMesesDeTrabajo[]=array(
                        'id' => $mesDeTrabajo->id,
                        'mes' => $mesDeTrabajo->mes,
                        'nombre' => $mesDeTrabajo->nombre,
                        'mesActivo' => $mesDeTrabajo->nombre . ' ' . $mesDeTrabajo->anioRemuneracion->anio,
                        'anio' => $mesDeTrabajo->anioRemuneracion->anio,
                        'idAnio' => $mesDeTrabajo->anio_id,
                        'fechaRemuneracion' => $mesDeTrabajo->fecha_remuneracion,
                        'isIngresado' => $mesDeTrabajo->estado()
                    );
                }
            }
    	}
    	return $listaMesesDeTrabajo;
    }
    
    static function semanas()
    {
        $mes = \Session::get('mesActivo');
        $fechaInicial = $mes->mes;
        $fechaFinal = $mes->fechaRemuneracion;
        $beg = (int) date('W', strtotime(date($fechaInicial)));
        $end = (int) date('W', strtotime(date($fechaFinal)));
        
        return (($end - $beg) + 1);
    }
    
    static function selectMes($id=null)
    {
        $datosMesDeTrabajo = new stdClass();
        $uf = NULL;
        $utm = NULL;
        $uta = NULL;
        $indicadores = array();
        
        if($id){
            $mesDeTrabajo = MesDeTrabajo::find($id);
        }else{            
            $mesDeTrabajo = MesDeTrabajo::orderBy('mes', 'DESC')->first();
        }
        
        if($mesDeTrabajo){
            $indicadores = ValorIndicador::where('mes', $mesDeTrabajo->mes)->orderBy('indicador_id', 'ASC')->get();
            
            if(count($indicadores)){
                $uf = $indicadores[0]->valor;
                $utm = $indicadores[1]->valor;
                $uta = $indicadores[2]->valor;
            }
            $datosMesDeTrabajo->id = $mesDeTrabajo->id;
            $datosMesDeTrabajo->mes = $mesDeTrabajo->mes;
            $datosMesDeTrabajo->mesActivo = $mesDeTrabajo->nombre . ' ' . $mesDeTrabajo->anioRemuneracion->anio;
            $datosMesDeTrabajo->fecha = date('d-m-Y');
            $datosMesDeTrabajo->fechaPalabras = Funciones::obtenerFechaTexto();
            $datosMesDeTrabajo->nombre = $mesDeTrabajo->nombre;
            $datosMesDeTrabajo->idAnio = $mesDeTrabajo->anio_id;
            $datosMesDeTrabajo->anio = $mesDeTrabajo->anioRemuneracion->anio;
            $datosMesDeTrabajo->fechaRemuneracion = $mesDeTrabajo->fecha_remuneracion;
            $datosMesDeTrabajo->uf = $uf;
            $datosMesDeTrabajo->utm = $utm;
            $datosMesDeTrabajo->uta = $uta;
        }
        
        return $datosMesDeTrabajo;
    }
    
    static function errores($datos){
         
        $rules = array(
            'mes' => 'required',
            'nombre' => 'required',
            'fecha_remuneracion' => 'required',
            'anio_id' => 'required'
        );

        $message = array(
            'mesDeTrabajo.required' => 'Obligatorio!'
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
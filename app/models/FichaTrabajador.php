<?php

class FichaTrabajador extends Eloquent {
    
    protected $table = 'fichas_trabajadores';
    
    public function trabajador(){
		return $this->belongsTo('Trabajador', 'trabajador_id');
	}
    
    public function afp(){
        return $this->belongsTo('Glosa', 'afp_id');
    }
    
    public function prevision(){
        return $this->belongsTo('Glosa', 'prevision_id');
    }
    
    public function afpApv(){
        return $this->belongsTo('Glosa', 'afp_id');
    }
    
    public function cargo(){
		return $this->belongsTo('Cargo', 'cargo_id');
	}
    
    public function tipoContrato(){
		return $this->belongsTo('TipoContrato', 'tipo_contrato_id');
	}
    
    public function nacionalidad(){
		return $this->belongsTo('Glosa', 'nacionalidad_id');
	}
    
    public function estadoCivil(){
		return $this->belongsTo('EstadoCivil', 'estado_civil_id');
	}
    
    public function comuna(){
        return $this->belongsTo('Comuna', 'comuna_id');
    }  
    
    public function seccion(){
		return $this->belongsTo('Seccion', 'seccion_id');
	}
    
    public function titulo(){
		return $this->belongsTo('Titulo', 'titulo_id');
	}
    
    public function tienda(){
		return $this->belongsTo('Tienda', 'tienda_id');
	}
    
    public function centroCosto(){
		return $this->belongsTo('CentroCosto', 'centro_costo_id');
	}
    
    public function banco(){
		return $this->belongsTo('Banco', 'banco_id');
	}
    
    public function tipoCuenta(){
		return $this->belongsTo('TipoCuenta', 'tipo_cuenta_id');
	}
    
    public function tipoJornada(){
		return $this->belongsTo('Jornada', 'tipo_jornada_id');
	}
    
    public function isapre(){
		return $this->belongsTo('Glosa', 'isapre_id');
	}
    
    public function tipo(){
		return $this->belongsTo('Glosa', 'tipo_id');
	}
    
    public function afpSeguro(){
        return $this->belongsTo('Glosa', 'afp_seguro_id');
    }
    
    public function apvs(){
        return $this->hasMany('Apv','ficha_trabajador_id');
    }
    
    public function cargas(){
        return $this->hasMany('Carga','ficha_trabajador_id');
    }
    
    public function tramo(){
		return $this->belongsTo('AsignacionFamiliar', 'tramo_id');
	}              
    
    static function isGratificacionAnual()
    {
        $empresa = \Session::get('empresa');
        if($empresa->gratificacion=='e'){
            if($empresa->tipo_gratificacion=='a'){
                return true;
            }
        }else{
            $trabajadores = FichaTrabajador::where('gratificacion', 'a')->get();
            if($trabajadores->count()){
                return true;
            }
        }
        return false;
    }
    
    public function totalCargasFamiliares()
    {        
        $totalCargasFamiliares = 0;
        $cargas = $this->cargas;
        if($cargas->count()){
            foreach($cargas as $carga){
                if($carga->es_carga){
                    $totalCargasFamiliares++;
                }
            }
        }
        
        return $totalCargasFamiliares;
    }
    
    public function totalCargasAutorizadas()
    {        
        $totalCargasFamiliares = 0;
        $cargas = $this->cargas;
        
        if($cargas->count()){
            foreach($cargas as $carga){
                if($carga->es_carga && $carga->es_autorizada==1){
                    $totalCargasFamiliares++;
                }
            }
        }
        
        return $totalCargasFamiliares;
    }
    
    public function numeroCargasAutorizadas()
    {        
        $totalCargasFamiliares = $this->totalCargasAutorizadas();
        
        if($totalCargasFamiliares==0){
            return '';
        }
        
        return $totalCargasFamiliares;
    }
    
    public function totalCargasPagar()
    {        
        $cargasFamiliares = array();
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;
        
        if($this->cargas->count()){
            foreach($this->cargas as $carga){
                if($carga->es_carga && $carga->es_autorizada==1){
                    $cargasFamiliares[] = $carga;
                }
            }
        }
        
        return $cargasFamiliares;
    }
    
    public function totalGrupoFamiliar()
    {        
        $totalGrupoFamiliar = 0;
        $cargas = $this->cargas;
        
        if($cargas->count()){
            $totalGrupoFamiliar = $cargas->count();
        }
        
        return $totalGrupoFamiliar;
    }
    
    public function comprobarApvs($apvs)
    {
        $idFicha = $this->id;
        $misApvs = $this->misApvs();
        $update = array();
        $create = array();
        $destroy = array();
        
        if($misApvs){
            foreach($apvs as $apv)
            {
                $isUpdate = false;

                if(isset($apv['id'])){  
                    foreach($misApvs as $miApv)
                    {
                        if($apv['id'] == $miApv['id']){
                            $update[] = array(
                                'id' => $apv['id'],
                                'sid' => $apv['sid'],
                                'afp_id' => $apv['afp']['id'],
                                'forma_pago' => $apv['formaPago']['id'],
                                'moneda' => $apv['moneda'],
                                'regimen' => $apv['regimen'],
                                'monto' => $apv['monto']
                            );
                            $isUpdate = true;
                        }                    
                        if($isUpdate){
                            break;
                        }
                    }
                }else{
                    $create[] = array(
                        'afp_id' => $apv['afp']['id'],
                        'forma_pago' => $apv['formaPago']['id'],
                        'moneda' => $apv['moneda'],                        
                        'regimen' => $apv['regimen'],
                        'monto' => $apv['monto']
                    );
                }
            }

            foreach($misApvs as $miApv)
            {
                $isApv = false;
                foreach($apvs as $apv)
                {
                    if(isset($apv['id'])){
                        if($miApv['id'] == $apv['id']){
                            $isApv = true;                        
                        }
                    }
                }
                if(!$isApv){
                    $destroy[] = array(
                        'id' => $miApv['id'],
                        'sid' => $miApv['sid']
                    );
                }
            }
        }else{
            foreach($apvs as $apv){
                $create[] = array(
                    'afp_id' => $apv['afp']['id'],
                    'forma_pago' => $apv['formaPago']['id'],
                    'moneda' => $apv['moneda'],
                    'regimen' => $apv['regimen'],
                    'monto' => $apv['monto']
                );
            }                
        }
        
        
        $datos = array(
            'create' => $create,
            'update' => $update,
            'destroy' => $destroy
        );
        
        return $datos;
    }
    
    public function totalApv()
    {
        $idFicha = $this->id;
        $apvs = $this->misApvs();
        $monto = 0;
        
        if($apvs){
            foreach($apvs as $apv){
                $monto += $apv['montoPesos'];
            }
        }
        
        return $monto;
    }
    
    public function misCargas()
    {        
        $misCargas = Carga::where('ficha_trabajador_id', $this->id)->get();
        $listaCargas = array();
        
        if( $misCargas ){
            foreach($misCargas as $carga){
                $listaCargas[] = array(
                    'id' => $carga->id,
                    'sid' => $carga->sid,
                    'created_at' => $carga->created_at,
                    'parentesco' => $carga->parentesco,
                    'tipo' => $carga->tipoCarga,
                    'esCarga' => $carga->es_carga ? true : false,
                    'esAutorizada' => $carga->es_autorizada ? true : false,
                    'rut' => $carga->rut,
                    'rutFormato' => Funciones::formatear_rut($carga->rut),
                    'nombreCompleto' => $carga->nombre_completo,
                    'fechaNacimiento' => $carga->fecha_nacimiento,
                    'fechaAutorizacion' => $carga->fecha_autorizacion,
                    'sexo' => $carga->sexo
                );
            }
        }
        
        return $listaCargas;
    }
    
    public function cargasFamiliares()
    {        
        $cargas = $this->totalCargasPagar();
        $mes = \Session::get('mesActivo')->mes;
        $monto = 0;
        $cargasSimples = 0;
        $cargasMaternales = 0;
        $cargasInvalidas = 0;
        $isCargas = false;
        $idTramo = $this->tramo_id;
        if($idTramo && $cargas){
            $tramo = AsignacionFamiliar::where('tramo', $idTramo)->where('mes', $mes)->first();
            $monto = $tramo->monto;
            $monto = ( count($cargas) * $monto );
            $isCargas = true;
        }
        foreach($cargas as $carga){
            if($carga->tipo_carga_id==3){
                $cargasInvalidas++;
            }else if($carga->tipo_carga_id==2){
                $cargasMaternales++;
            }else if($carga->tipo_carga_id==1){
                $cargasSimples++;
            }
        }
        
        $cargasFamiliares = array(
            'cantidad' => count($cargas),
            'cantidadSimples' => $cargasSimples,
            'cantidadMaternales' => $cargasMaternales,
            'cantidadInvalidas' => $cargasInvalidas,
            'monto' => $monto,
            'isCargas' => $isCargas
        );
        
        return $cargasFamiliares;
    }
    
    public function asignacionRetroactiva()
    {
        $monto = 0;
        $mes = \Session::get('mesActivo')->id;
        $id = $this->trabajador_id;
        $asignacionesRetroactivas = Haber::where('trabajador_id', $id)->where('tipo_haber_id', 3)->where('mes_id', $mes)->get();
        
        if($asignacionesRetroactivas->count()){
            foreach($asignacionesRetroactivas as $asignacionRetroactiva){
                $monto = ($monto + Funciones::convertir($asignacionRetroactiva->monto, $asignacionRetroactiva->moneda));
            }
        }
        
        return $monto;
    }
    
    public function reintegroCargasFamiliares()
    {
        return '';
    }
    
    public function solicitudTrabajadorJoven()
    {
        return 'N';
    }
    
    public function asignacionFamiliar()
    {
        $monto = $this->cargasFamiliares()['monto'];
        if($monto==0){
            return '';
        }
        
        return $monto;
    }
    
    public function isCargas()
    {
        $isCargas = false;
        $idTrabajador = $this->id;
        $cargas = $this->misCargas();
        if($cargas){
            $isCargas = true;
        }
        
        return $isCargas;
    }        
    
    public function vigenciaContrato()
    {
        $inicio = Funciones::obtenerFechaTexto($this->fecha_reconocimiento);
        if($this->tipo_contrato_id==1){
            $vigencia = "desde el día " . $inicio;
        }else{            
            $fin = Funciones::obtenerFechaTexto($this->fecha_vencimiento); 
            $vigencia = "desde el día " . $inicio . " hasta el día " . $fin;
        }
        
        return $vigencia;
    }
    
    public function tramoAsignacionFamiliar()
    {
        $tramo = $this->tramo_id ? $this->tramo_id : 'd';
        
        return strtoupper($tramo);
    }
    
    static function calcularTramo($monto)
    {
        $miTramo = null;
        $mes = \Session::get('mesActivo')->mes;
        $tramos = AsignacionFamiliar::where('mes', $mes)->orderBy('tramo')->get();
        foreach($tramos as $tramo){
            if($monto > $tramo['renta_menor'] && $monto <= $tramo['renta_mayor']){
                $miTramo = $tramo['tramo'];
                break;
            }
        }
        return $miTramo;
    }
    
    public function miGrupoFamiliar()
    {        
        $idFicha = $this->id;
        $misCargas = Carga::where('ficha_trabajador_id', $idFicha)->orderBy('es_carga', 'DESC')->get();
        $listaCargas = array();
        
        if( $misCargas->count() ){
            foreach($misCargas as $carga){
                $listaCargas[] = array(
                    'id' => $carga->id,
                    'sid' => $carga->sid,
                    'created_at' => $carga->created_at,
                    'parentesco' => $carga->parentesco,
                    'esCarga' => $carga->es_carga ? true : false,
                    'esAutorizada' => $carga->es_autorizada ? true : false,
                    'rut' => $carga->rut,
                    'rutFormato' => Funciones::formatear_rut($carga->rut),
                    'nombreCompleto' => $carga->nombre_completo,
                    'fechaNacimiento' => $carga->fecha_nacimiento,
                    'sexo' => $carga->sexo
                );
            }
        }
        
        return $listaCargas;
    }
    
    
    
    public function comprobarCargas($cargas)
    {
        $idFicha = $this->id;
        $misCargas = $this->miGrupoFamiliar($idFicha);
        $update = array();
        $create = array();
        $destroy = array();
        
        if($misCargas){
            foreach($cargas as $carga)
            {
                $isUpdate = false;

                if(isset($carga['id'])){  
                    foreach($misCargas as $miCarga)
                    {
                        if($carga['id'] == $miCarga['id']){
                            $update[] = array(
                                'id' => $carga['id'],
                                'sid' => $carga['sid'],
                                'tipo' => $carga['tipo']['id'],
                                'esCarga' => $carga['esCarga'],
                                'rut' => $carga['rut'],
                                'nombreCompleto' => $carga['nombreCompleto'],
                                'sexo' => $carga['sexo'],
                                'fechaNacimiento' => $carga['fechaNacimiento'],
                                'parentesco' => $carga['parentesco']
                            );
                            $isUpdate = true;
                        }                    
                        if($isUpdate){
                            break;
                        }
                    }
                }else{
                    $create[] = array(
                        'esCarga' => $carga['esCarga'],
                        'rut' => $carga['rut'],
                        'tipo' => $carga['tipo']['id'],
                        'nombreCompleto' => $carga['nombreCompleto'],
                        'fechaNacimiento' => $carga['fechaNacimiento'],
                        'sexo' => $carga['sexo'],
                        'parentesco' => $carga['parentesco']
                    );
                }
            }

            foreach($misCargas as $miCarga)
            {
                $isCarga = false;
                foreach($cargas as $carga)
                {
                    if(isset($carga['id'])){
                        if($miCarga['id'] == $carga['id']){
                            $isCarga = true;                        
                        }
                    }
                }
                if(!$isCarga){
                    $destroy[] = array(
                        'id' => $miCarga['id'],
                        'sid' => $miCarga['sid']
                    );
                }
            }
        }else{
            foreach($cargas as $carga){
                $create[] = array(
                    'esCarga' => $carga['esCarga'],
                    'rut' => $carga['rut'],
                    'tipo' => $carga['tipo']['id'],
                    'nombreCompleto' => $carga['nombreCompleto'],
                    'fechaNacimiento' => $carga['fechaNacimiento'],
                    'sexo' => $carga['sexo'],
                    'parentesco' => $carga['parentesco']
                );
            }
        }
        
        
        $datos = array(
            'create' => $create,
            'update' => $update,
            'destroy' => $destroy
        );
        
        return $datos;
    }
    
    public function misApvs()
    {        
        $misApvs = Apv::where('ficha_trabajador_id', $this->id)->get();
        $listaApvs = array();
        
        if( $misApvs ){
            foreach($misApvs as $apv){
                $listaApvs[] = array(
                    'id' => $apv->id,
                    'sid' => $apv->sid,
                    'moneda' => $apv->moneda,
                    'numeroContrato' => $apv->numero_contrato,
                    'monto' => $apv->monto,
                    'regimen' => strtoupper($apv->regimen),
                    'montoPesos' => Funciones::convertir($apv->monto, $apv->moneda),
                    'afp' => array(
                        'id' => $apv->afp->id,
                        'nombre' => $apv->afp->glosa
                    ),
                    'formaPago' => array(
                        'id' => $apv->formaPago->id,
                        'nombre' => $apv->formaPago->glosa
                    )
                );
            }
        }
        
        return $listaApvs;
    }
    
    public function nombreCompleto()
    {
        $nombres = $this->nombres;
        $apellidos = $this->apellidos;
        
        $nombreCompleto = $nombres . " " . $apellidos;
        
        return $nombreCompleto;
    }
    
    public function apellidoPaterno()
    {
        $apellidos = $this->apellidos;                
        $ape = explode(" ", $apellidos);

        return $ape[0];
    }
    
    public function apellidoMaterno()
    {
        $apellidos = $this->apellidos;                
        $ape = explode(" ", $apellidos);
        
        if(isset($ape[1])){
            return $ape[1];
        }else{
            return "";
        }
    }
    
    public function codigoNacionalidad()
    {
        $nacionalidad = $this->nacionalidad->id;                
        
        if($nacionalidad==3){
            $codigo = 0;
        }else if($nacionalidad==4){
            $codigo = 1;            
        }else{
            $codigo = "";            
        }
        
        return $codigo;
    }        
    
    public function tipoTrabajador($recaudador=null)
    {        
        if(!$recaudador){
            $recaudador = 1;
        }
        $tipo = $this->tipo_id;
        $codigo = Glosa::find($tipo)->codigo($recaudador)['codigo'];
        
        return $codigo;
    }                
    
    public function mesesAntiguedad()
    {
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;
        $fechaIngreso = new DateTime($this->fecha_reconocimiento);
        $fecha = new DateTime($finMes);
        $diff = $fechaIngreso->diff($fecha);
        $meses = (($diff->y * 12) + $diff->m);
        
        return $meses;
    }
    
    public function domicilio()
    {
        $direccion = $this->direccion;
        $comuna = $this->comuna->comuna;
        $provincia = $this->comuna->provincia->provincia;
        $domicilio = $direccion . ', comuna de ' . $comuna . ', de la ciudad de ' . $provincia;
        
        return $domicilio;
    }

    static function errores($datos){
         
        $rules = array(

            
        );

        $message = array(
            'fichaTrabajador.required' => 'Obligatorio!'
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
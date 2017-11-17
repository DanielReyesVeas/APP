<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Trabajador extends Eloquent {
    
    use SoftDeletingTrait;
    
    protected $table = 'trabajadores';
    protected $dates = array('deleted_at');

    public function contrato(){
        return $this->hasMany('Contrato', 'trabajador_id');
    }        
    
    public function rut_formato(){
        return Funciones::formatear_rut($this->rut);
    }
    
    public function rut_sin_digito(){
        return Funciones::formatear_rut_sin_digito($this->rut);
    }
    
    public function rut_digito(){
        return Funciones::formatear_rut_digito($this->rut);
    }
    
    public function inasistencias(){
        return $this->hasMany('Inasistencia','trabajador_id');
    }
    
    public function licencias(){
        return $this->hasMany('Licencia','trabajador_id');
    }
    
    public function horasExtra(){
        return $this->hasMany('HoraExtra','trabajador_id');
    }
        
    public function vacaciones(){
        return $this->hasMany('Vacaciones','trabajador_id');
    }
    
    public function haberes(){
        return $this->hasMany('Haber','trabajador_id');
    }
    
    public function descuentos(){
        return $this->hasMany('Descuento','trabajador_id');
    }
    
    public function prestamos(){
        return $this->hasMany('Prestamo','trabajador_id');
    }
    
    public function documentos(){
        return $this->hasMany('Documento','trabajador_id');
    }        
    
    public function secciones(){
        return $this->hasMany('Seccion','encargado_id');
    }
    
    public function liquidaciones(){
        return $this->hasMany('Liquidacion','trabajador_id');
    }
    
    public function cartasNotificacion(){
        return $this->hasMany('CartaNotificacion','trabajador_id');
    }
    
    public function certificados(){
        return $this->hasMany('Certificado','trabajador_id');
    }
    
    public function finiquito(){
        return $this->hasMany('Finiquito','trabajador_id');
    }
    
    public function fichaTrabajador()
    {
        return $this->hasMany('FichaTrabajador', 'trabajador_id');
    }
    
    static function centralizar($fecha, $empresa_id)
    {
        $empresa = Empresa::find($empresa_id);
        Config::set('database.default', $empresa->base_datos);
        $mes = MesDeTrabajo::where('fecha_remuneracion', '2017/09/30')->first();
        $liquidaciones = Liquidacion::where('mes', $mes['mes'])->orderBy('trabajador_apellidos')->get();
        $lista = array();
        $listaCuentas = array();
        $cuentas = Cuenta::all();
        
        if($cuentas->count()){
            foreach($cuentas as $cuenta){
                $listaCuentas[] = array(
                    'id' => $cuenta->id,    
                    'codigo' => $cuenta->codigo,    
                    'nombre' => $cuenta->nombre,    
                    'comportamiento' => $cuenta->comportamiento
                );
            }
        }
        
        foreach($liquidaciones as $liquidacion){
            $descuentos = array();
            $detalles = $liquidacion->detallesLiquidacion($empresa->base_datos);

            $lista[] = array(
                'idTrabajador' => $liquidacion->trabajador_id,
                'rut' => $liquidacion->trabajador_rut,
                'nombreCompleto' => $liquidacion->trabajador_nombres . ' ' . $liquidacion->trabajador_apellidos,
                'sueldoBase' => $liquidacion->sueldo_base,
                'sueldo' => $liquidacion->sueldo,
                'rentaImponible' => $liquidacion->renta_imponible,
                'sueldoLiquido' => $liquidacion->sueldo_liquido,
                'haberes' => $detalles['haberes'],
                'descuentos' => $detalles['descuentos'],
                'aportes' => $detalles['aportes'],
                'pais' => 'CHILE',
                'canal' => $liquidacion->trabajador_seccion,
                'tienda' => 'STGO CENTRO'
            );
        }
        
        $datos = array(
            'general' => array(
                'rut' => '112223334',
                'nombre' => 'Usuario Admin',
                'periodo' => '2017-09-01',
                'cuentas' => $listaCuentas
            ),
            'detalle' => $lista                
        );
        
        return $datos;
    }
    
    public function ficha()
    {
        $idMes = \Session::get('mesActivo')->id;
        $mes = \Session::get('mesActivo')->mes;
        $idTrabajador = $this->id;
        $ficha = FichaTrabajador::where('trabajador_id', $idTrabajador)->where('mes_id', $idMes)->first();
        if(!$ficha){
            $ficha = FichaTrabajador::where('trabajador_id', $idTrabajador)->where('fecha', '<=', $mes)->orderBy('fecha', 'DESC')->first();
        }
        if(!$ficha){
            $ficha = null;
        }
        
        return $ficha;
    }       
    
    public function crearUser($estado)
    {
        $usuario = new User();
        $usuario->sid = Funciones::generarSID();
        $usuario->tipo = 2;
        $usuario->funcionario_id = $this->id;
        $usuario->username = $this->rut;
        $usuario->password = Hash::make('1234');
        $usuario->estado = $estado;
        $usuario->perfil_id = 2;
        $usuario->save();
        
        $empresa = \Session::get('empresa');
        
        $usuarioEmpresa = new UsuarioEmpresa();
        $usuarioEmpresa->usuario_id = $usuario->id;
        $usuarioEmpresa->empresa_id = $empresa->id;
        $usuarioEmpresa->activo = $estado;
        $usuarioEmpresa->documentos_empresa = true;
        $usuarioEmpresa->cartas_notificacion = true;
        $usuarioEmpresa->certificados = true;
        $usuarioEmpresa->liquidaciones = true;
        $usuarioEmpresa->solicitudes = true;
        $usuarioEmpresa->save();
        
        return;
    }    
    
    public function semanaCorrida()
    {
        $mes = \Session::get('mesActivo')->mes;
        $semanaCorrida = SemanaCorrida::where('trabajador_id', $this->id)->where('mes', $mes)->first();
        $semanas = MesDeTrabajo::semanas();
        $datos = array();
        $datos[] = array('semana' => '1°', 'alias' => 'semana_1', 'comision' => $semanaCorrida->semana_1);
        $datos[] = array('semana' => '2°', 'alias' => 'semana_2', 'comision' => $semanaCorrida->semana_2);
        $datos[] = array('semana' => '3°', 'alias' => 'semana_3', 'comision' => $semanaCorrida->semana_3);
        $datos[] = array('semana' => '4°', 'alias' => 'semana_4', 'comision' => $semanaCorrida->semana_4);
        
        if($semanas>4){
            $datos[] = array('semana' => '5°', 'alias' => 'semana_5', 'comision' => $semanaCorrida->semana_5);
        }
        
        $semana = array(
            'id' => $semanaCorrida->id,
            'semanas' => $datos
        );

        return $semana;
    }
    
    public function misHaberes()
    {
        $idTrabajador = $this->id;
        $listaHaberes = array();
        $idMes = \Session::get('mesActivo')->id;
        $mes = \Session::get('mesActivo')->mes;
        $misHaberes = Haber::where('trabajador_id', $idTrabajador)->where('mes_id', $idMes)->orWhere('permanente', 1)->where('trabajador_id', $idTrabajador)->orWhere('hasta', '>=', $mes)->where('trabajador_id', $idTrabajador)->get();
        $diasTrabajados = $this->diasTrabajados();
        
        if( $misHaberes->count() ){
            if($diasTrabajados<30){
                foreach($misHaberes as $haber){
                    $monto = Funciones::convertir($haber->monto, $haber->moneda);
                    
                    
                    if($haber->tipoHaber->id==2){
                        if($diasTrabajados<25){
                            $monto = round(($monto / 30) * $diasTrabajados);                            
                        }
                    }else{
                        if($haber->tipoHaber->proporcional_dias_trabajados){
                            $monto = round(($monto / 30) * $diasTrabajados);                            
                        }
                    }
                    
                    $listaHaberes[] = array(
                        'id' => $haber->id,
                        'sid' => $haber->sid,
                        'moneda' => $haber->moneda,
                        'monto' => $haber->monto,
                        'montoPesos' => $monto,
                        'mes' => array(
                            'id' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->id : "",
                            'nombre' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->nombre : ""
                        ),
                        'fechaIngreso' => $haber->created_at,
                        'tipo' => array(
                            'id' => $haber->tipoHaber->id,
                            'nombre' => $haber->tipoHaber->nombre,
                            'gratificacion' => $haber->tipoHaber->gratificacion ? true : false,
                            'imponible' => $haber->tipoHaber->imponible ? true : false,
                            'tributable' => $haber->tipoHaber->tributable ? true : false,
                            'proporcional' => $haber->tipoHaber->proporcional_dias_trabajados ? true : false,
                            'horasExtra' => $haber->tipoHaber->calcula_horas_extra ? true : false,
                            'semanaCorrida' => $haber->tipoHaber->calcula_semana_corrida ? true : false,
                            'idCuenta' => $haber->tipoHaber->cuenta_id
                        ),
                        'desde' => $haber->desde,
                        'hasta' => $haber->hasta,
                        'porMes' => $haber->por_mes ? true : false,
                        'rangoMeses' => $haber->rango_meses ? true : false,
                        'permanente' => $haber->permanente ? true : false,
                        'todosAnios' => $haber->todos_anios ? true : false
                    );
                }
            }else{
                foreach($misHaberes as $haber){
                    $listaHaberes[] = array(
                        'id' => $haber->id,
                        'sid' => $haber->sid,
                        'moneda' => $haber->moneda,
                        'monto' => $haber->monto,
                        'montoPesos' => Funciones::convertir($haber->monto, $haber->moneda),
                        'mes' => array(
                            'id' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->id : "",
                            'nombre' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->nombre : ""
                        ),
                        'fechaIngreso' => $haber->created_at,
                        'tipo' => array(
                            'id' => $haber->tipoHaber->id,
                            'nombre' => $haber->tipoHaber->nombre,
                            'gratificacion' => $haber->tipoHaber->gratificacion ? true : false,
                            'imponible' => $haber->tipoHaber->imponible ? true : false,
                            'tributable' => $haber->tipoHaber->tributable ? true : false,
                            'proporcional' => $haber->tipoHaber->proporcional_dias_trabajados ? true : false,
                            'horasExtra' => $haber->tipoHaber->calcula_horas_extra ? true : false,
                            'semanaCorrida' => $haber->tipoHaber->calcula_semana_corrida ? true : false,
                            'idCuenta' => $haber->tipoHaber->cuenta_id
                        ),
                        'desde' => $haber->desde,
                        'hasta' => $haber->hasta,
                        'porMes' => $haber->por_mes ? true : false,
                        'rangoMeses' => $haber->rango_meses ? true : false,
                        'permanente' => $haber->permanente ? true : false,
                        'todosAnios' => $haber->todos_anios ? true : false
                    );
                }
            }
        }
        
        return $listaHaberes;
    }
  
    public function haberesImponibles()
    {
        $idTrabajador = $this->id;
        $listaHaberes = array();
        $idMes = \Session::get('mesActivo')->id;
        $mes = \Session::get('mesActivo')->mes;
        $misHaberes = Haber::where('trabajador_id', $idTrabajador)->where('mes_id', $idMes)->orWhere('permanente', 1)->where('trabajador_id', $idTrabajador)->orWhere('hasta', '>=', $mes)->where('trabajador_id', $idTrabajador)->get();
        $diasTrabajados = $this->diasTrabajados();
        
        if( $misHaberes->count() ){
            if($diasTrabajados<30){
                foreach($misHaberes as $haber){
					if($haber->tipoHaber->imponible){
						$monto = Funciones::convertir($haber->monto, $haber->moneda);

						if($haber->tipoHaber->proporcional_dias_trabajados){
							$monto = round(($monto / 30) * $diasTrabajados);
						}

						$listaHaberes[] = array(
							'id' => $haber->id,
							'sid' => $haber->sid,
							'moneda' => $haber->moneda,
							'monto' => $haber->monto,
							'montoPesos' => $monto,
							'mes' => array(
								'id' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->id : "",
								'nombre' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->nombre : ""
							),
							'fechaIngreso' => $haber->created_at,
							'tipo' => array(
                                'id' => $haber->tipoHaber->id,
                                'nombre' => $haber->tipoHaber->nombre,
                                'gratificacion' => $haber->tipoHaber->gratificacion ? true : false,
                                'imponible' => $haber->tipoHaber->imponible ? true : false,
                                'tributable' => $haber->tipoHaber->tributable ? true : false,
                                'proporcional' => $haber->tipoHaber->proporcional_dias_trabajados ? true : false,
                                'horasExtra' => $haber->tipoHaber->calcula_horas_extra ? true : false,
                                'semanaCorrida' => $haber->tipoHaber->calcula_semana_corrida ? true : false
							),
							'desde' => $haber->desde,
							'hasta' => $haber->hasta,
							'porMes' => $haber->por_mes ? true : false,
							'rangoMeses' => $haber->rango_meses ? true : false,
							'permanente' => $haber->permanente ? true : false,
							'todosAnios' => $haber->todos_anios ? true : false
						);
					}
                }
            }else{
                foreach($misHaberes as $haber){
				  	if($haber->tipoHaber->imponible){
						$listaHaberes[] = array(
							'id' => $haber->id,
							'sid' => $haber->sid,
							'moneda' => $haber->moneda,
							'monto' => $haber->monto,
							'montoPesos' => Funciones::convertir($haber->monto, $haber->moneda),
							'mes' => array(
								'id' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->id : "",
								'nombre' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->nombre : ""
							),
							'fechaIngreso' => $haber->created_at,
							'tipo' => array(
                                'id' => $haber->tipoHaber->id,
                                'nombre' => $haber->tipoHaber->nombre,
                                'gratificacion' => $haber->tipoHaber->gratificacion ? true : false,
                                'imponible' => $haber->tipoHaber->imponible ? true : false,
                                'tributable' => $haber->tipoHaber->tributable ? true : false,
                                'proporcional' => $haber->tipoHaber->proporcional_dias_trabajados ? true : false,
                                'horasExtra' => $haber->tipoHaber->calcula_horas_extra ? true : false,
                                'semanaCorrida' => $haber->tipoHaber->calcula_semana_corrida ? true : false
							),
							'desde' => $haber->desde,
							'hasta' => $haber->hasta,
							'porMes' => $haber->por_mes ? true : false,
							'rangoMeses' => $haber->rango_meses ? true : false,
							'permanente' => $haber->permanente ? true : false,
							'todosAnios' => $haber->todos_anios ? true : false
						);
					}
                }
            }
        }
        
        return $listaHaberes;
    }
  
  	public function haberesNoImponibles()
    {
        $idTrabajador = $this->id;
        $listaHaberes = array();
        $idMes = \Session::get('mesActivo')->id;
        $mes = \Session::get('mesActivo')->mes;
        $misHaberes = Haber::where('trabajador_id', $idTrabajador)->where('mes_id', $idMes)->orWhere('permanente', 1)->where('trabajador_id', $idTrabajador)->orWhere('hasta', '>=', $mes)->where('trabajador_id', $idTrabajador)->get();
        $diasTrabajados = $this->diasTrabajados();
        
        if( $misHaberes->count() ){
            if($diasTrabajados<30){
                foreach($misHaberes as $haber){
					if(!$haber->tipoHaber->imponible){
						$monto = Funciones::convertir($haber->monto, $haber->moneda);

						if($haber->tipoHaber->proporcional_dias_trabajados){
							$monto = round(($monto / 30) * $diasTrabajados);
						}

						$listaHaberes[] = array(
							'id' => $haber->id,
							'sid' => $haber->sid,
							'moneda' => $haber->moneda,
							'monto' => $haber->monto,
							'montoPesos' => $monto,
							'mes' => array(
								'id' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->id : "",
								'nombre' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->nombre : ""
							),
							'fechaIngreso' => $haber->created_at,
							'tipo' => array(
                                'id' => $haber->tipoHaber->id,
                                'nombre' => $haber->tipoHaber->nombre,
                                'gratificacion' => $haber->tipoHaber->gratificacion ? true : false,
                                'imponible' => $haber->tipoHaber->imponible ? true : false,
                                'tributable' => $haber->tipoHaber->tributable ? true : false,
                                'proporcional' => $haber->tipoHaber->proporcional_dias_trabajados ? true : false,
                                'horasExtra' => $haber->tipoHaber->calcula_horas_extra ? true : false,
                                'semanaCorrida' => $haber->tipoHaber->calcula_semana_corrida ? true : false
							),
							'desde' => $haber->desde,
							'hasta' => $haber->hasta,
							'porMes' => $haber->por_mes ? true : false,
							'rangoMeses' => $haber->rango_meses ? true : false,
							'permanente' => $haber->permanente ? true : false,
							'todosAnios' => $haber->todos_anios ? true : false
						);
					}
                }
            }else{
                foreach($misHaberes as $haber){
				  	if(!$haber->tipoHaber->imponible){
						$listaHaberes[] = array(
							'id' => $haber->id,
							'sid' => $haber->sid,
							'moneda' => $haber->moneda,
							'monto' => $haber->monto,
							'montoPesos' => Funciones::convertir($haber->monto, $haber->moneda),
							'mes' => array(
								'id' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->id : "",
								'nombre' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->nombre : ""
							),
							'fechaIngreso' => $haber->created_at,
							'tipo' => array(
                                'id' => $haber->tipoHaber->id,
                                'nombre' => $haber->tipoHaber->nombre,
                                'gratificacion' => $haber->tipoHaber->gratificacion ? true : false,
                                'imponible' => $haber->tipoHaber->imponible ? true : false,
                                'tributable' => $haber->tipoHaber->tributable ? true : false,
                                'proporcional' => $haber->tipoHaber->proporcional_dias_trabajados ? true : false,
                                'horasExtra' => $haber->tipoHaber->calcula_horas_extra ? true : false,
                                'semanaCorrida' => $haber->tipoHaber->calcula_semana_corrida ? true : false
							),
							'desde' => $haber->desde,
							'hasta' => $haber->hasta,
							'porMes' => $haber->por_mes ? true : false,
							'rangoMeses' => $haber->rango_meses ? true : false,
							'permanente' => $haber->permanente ? true : false,
							'todosAnios' => $haber->todos_anios ? true : false
						);
					}
                }
            }
        }
        
        return $listaHaberes;
    }
    
    public function misHaberesPermanentes()
    {
        $listaHaberes = array();
        $misHaberes = Haber::where('trabajador_id', $this->id)->where('permanente', 1)->get();
        
        if( $misHaberes->count() ){
            foreach($misHaberes as $haber){
                $listaHaberes[] = array(
                    'id' => $haber->id,
                    'sid' => $haber->sid,
                    'moneda' => $haber->moneda,
                    'monto' => $haber->monto,
                    'montoPesos' => Funciones::convertir($haber->monto, $haber->moneda),
                    'tipo' => array(
                        'id' => $haber->tipoHaber->id,
                        'nombre' => $haber->tipoHaber->nombre,
                        'gratificacion' => $haber->tipoHaber->gratificacion ? true : false,
                        'imponible' => $haber->tipoHaber->imponible ? true : false,
                        'tributable' => $haber->tipoHaber->tributable ? true : false,
                        'proporcional' => $haber->tipoHaber->proporcional_dias_trabajados ? true : false,
                        'horasExtra' => $haber->tipoHaber->calcula_horas_extra ? true : false,
                        'semanaCorrida' => $haber->tipoHaber->calcula_semana_corrida ? true : false
                    )
                );
            }
        }
        
        return $listaHaberes;
    }
    
    public function misVacaciones()
    {
        $idMes = \Session::get('mesActivo')->id;
        $vacaciones = Vacaciones::where('trabajador_id', $this->id)->where('mes_id', $idMes)->first();
        
        if($vacaciones){
            return $vacaciones->dias;
        }else{
            return null;
        }
    }
    
    public function mesActualVacaciones()
    {
        $idMes = \Session::get('mesActivo')->id;
        $vacaciones = Vacaciones::where('trabajador_id', $this->id)->where('mes_id', $idMes)->first();
        $detalleVacaciones = array(
            'id' => $vacaciones->id,
            'sid' => $vacaciones->sid,
            'dias' => $vacaciones->dias,
            'mes' => $vacaciones->mes->nombre . " " . $vacaciones->mes->anioRemuneracion->anio
        );
        
        return $detalleVacaciones;
    }
    
    public function miHistorialVacaciones()
    {
        $listaVacaciones = array();
        $vacaciones = Vacaciones::where('trabajador_id', $this->id)->get();
        $mes = \Session::get('mesActivo')->mes;
        
        if($vacaciones->count()){
            foreach($vacaciones as $vacacion){
                if($vacacion->mes->mes < $mes){
                    $listaVacaciones[] = array(
                        'id' => $vacacion->id,
                        'sid' => $vacacion->sid,
                        'mes' => array(
                            'id' => $vacacion->mes->id,
                            'mes' => $vacacion->mes->mes,
                            'nombre' => $vacacion->mes->nombre . " " . $vacacion->mes->anioRemuneracion->anio
                        ),
                        'dias' => $vacacion->dias
                    );
                }
            }
        }
       
        return $listaVacaciones;
    }
    
    static function calcularVacaciones($mes)
    {
        $trabajadores = Trabajador::all();
        if(!$mes){
            $mes = \Session::get('mesActivo');
            $finMes = $mes->fechaRemuneracion;
        }else{
            $finMes = $mes->fecha_remuneracion;
        }
        
        if( $trabajadores->count() ){
            foreach( $trabajadores as $trabajador ){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        Vacaciones::calcularVacaciones($trabajador, $empleado, $mes);   
                    }
                }
            }
        }
        
        return;
    }
    
    static function crearSemanasCorridas($mes)
    {
        $trabajadores = Trabajador::all();
        if(!$mes){
            $mes = \Session::get('mesActivo');
            $finMes = $mes->fechaRemuneracion;
        }else{
            $finMes = $mes->fecha_remuneracion;
        }
        
        if( $trabajadores->count() ){
            foreach( $trabajadores as $trabajador ){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $trabajador->crearSemanaCorrida($mes);   
                    }
                }
            }
        }
        
        return;
    }
    
    public function crearSemanaCorrida($mes=null)
    {
        $id = $this->id;
        if(!$mes){
            $mes = \Session::get('mesActivo');
        }
        $semanaCorrida = SemanaCorrida::where('trabajador_id', $id)->where('mes', $mes->mes)->first();
        if(!$semanaCorrida){
            $semanaCorrida = new SemanaCorrida();
            $semanaCorrida->sid = Funciones::generarSID();
            $semanaCorrida->trabajador_id = $id;
            $semanaCorrida->mes = $mes->mes;
            $semanaCorrida->semana_1 = 0;
            $semanaCorrida->semana_2 = 0;
            $semanaCorrida->semana_3 = 0;
            $semanaCorrida->semana_4 = 0;
            $semanaCorrida->semana_5 = 0;
            $semanaCorrida->save();
        }
        
        return;
    }

    public function misDescuentos()
    {        
        $idTrabajador = $this->id;
        $listaDescuentos = array();
        $idMes = \Session::get('mesActivo')->id;
        $mes = \Session::get('mesActivo')->mes;
        $misDescuentos = Descuento::where('trabajador_id', $idTrabajador)->where('mes_id', $idMes)->orWhere('desde', '<=', $mes)->where('hasta', '>=', $mes)->where('trabajador_id', $idTrabajador)->orWhere('permanente', 1)->where('trabajador_id', $idTrabajador)->get();
        
        //$misDescuentos = Descuento::where('trabajador_id', $idTrabajador)->get();
        
        if( $misDescuentos->count() ){
            foreach($misDescuentos as $descuento){
                //if($descuento->permanente==1 || $descuento->mes_id == $idMes){
                    //if($descuento->desde <= $mes && $descuento->hasta >= $mes){
                if($descuento->tipoDescuento->estructuraDescuento->id==3){                        
                    $nombre = 'APVC AFP ' . $descuento->tipoDescuento->nombreAfp();
                }else if($descuento->tipoDescuento->estructuraDescuento->id==7){                        
                    $nombre = 'Cuenta de Ahorro AFP ' . $descuento->tipoDescuento->nombreAfp();
                }else{                    
                    $nombre = $descuento->tipoDescuento->nombre;
                }
                $listaDescuentos[] = array(
                    'id' => $descuento->id,
                    'sid' => $descuento->sid,
                    'moneda' => $descuento->moneda,
                    'monto' => $descuento->monto,
                    'montoPesos' => Funciones::convertir($descuento->monto, $descuento->moneda),
                    'fechaIngreso' => $descuento->created_at,
                    'tipo' => array(
                        'id' => $descuento->tipoDescuento->id,
                        'nombre' => $nombre,
                        'idCuenta' => $descuento->tipoDescuento->cuenta_id,
                        'estructura' => array(
                            'id' => $descuento->tipoDescuento->estructuraDescuento->id,
                            'nombre' => $descuento->tipoDescuento->estructuraDescuento->nombre
                        ),
                        'afp' => array(
                            'id' => $descuento->tipoDescuento->afp_id ? $descuento->tipoDescuento->afp->id : '',
                            'nombre' => $descuento->tipoDescuento->afp_id ? $descuento->tipoDescuento->afp->glosa : ''
                        ),
                        'formaPago' => array(
                            'id' => $descuento->tipoDescuento->forma_pago ? $descuento->tipoDescuento->formaPago->id : '',
                            'nombre' => $descuento->tipoDescuento->forma_pago ? $descuento->tipoDescuento->formaPago->glosa : ''
                        )
                    )
                );
                   // }
                //}
            }
        }
        
        return $listaDescuentos;
    }
    
    public function misCuotasPrestamo()
    {        
        $idTrabajador = $this->id;
        $listaPrestamos = array();
        $mes = \Session::get('mesActivo')->mes;
        $misPrestamos = Prestamo::where('trabajador_id', $idTrabajador)->where('primera_cuota', '<=', $mes)->where('ultima_cuota', '>=', $mes)->get();
        
        if( $misPrestamos->count() ){
            foreach($misPrestamos as $prestamo){
                $listaPrestamos[] = array(
                    'id' => $prestamo->id,
                    'sid' => $prestamo->sid,
                    'moneda' => $prestamo->moneda,
                    'monto' => $prestamo->monto,
                    'glosa' => $prestamo->glosa,
                    'nombreLiquidacion' => $prestamo->nombre_liquidacion,
                    'cuotas' => $prestamo->cuotas,
                    'primeraCuota' => $prestamo->primera_cuota,
                    'ultimaCuota' => $prestamo->ultima_cuota,
                    'cuotaPagar' => $prestamo->cuotaPagar()
                );
            }
        }
        
        return $listaPrestamos;
    }
    
    public function misDescuentosPermanentes()
    {        
        $listaDescuentos = array();
        $misDescuentos = Descuento::where('trabajador_id', $this->id)->where('permanente', 1)->get();
        
        if( $misDescuentos->count() ){
            foreach($misDescuentos as $descuento){
                $listaDescuentos[] = array(
                    'id' => $descuento->id,
                    'sid' => $descuento->sid,
                    'moneda' => $descuento->moneda,
                    'monto' => $descuento->monto,
                    'montoPesos' => Funciones::convertir($descuento->monto, $descuento->moneda),
                    'fechaIngreso' => $descuento->created_at,
                    'tipo' => array(
                        'id' => $descuento->tipoDescuento->id,
                        'nombre' => $descuento->tipoDescuento->nombre
                    ) 
                );
            }
        }
        
        return $listaDescuentos;
    }
    
    public function comprobarHaberes($haberes)
    {
        $idTrabajador = $this->id;
        $misHaberes = $this->misHaberesPermanentes($idTrabajador);
        $update = array();
        $create = array();
        $destroy = array();
        
        if($misHaberes){
            foreach($haberes as $haber)
            {
                $isUpdate = false;
                
                if(isset($haber['id'])){    
                    foreach($misHaberes as $miHaber)
                    {

                        if($haber['id'] == $miHaber['id']){
                            $update[] = array(
                                'id' => $haber['id'],
                                'sid' => $haber['sid'],
                                'tipo_haber_id' => $haber['tipo']['id'],
                                'moneda' => $haber['moneda'],
                                'monto' => $haber['monto']
                            );
                            $isUpdate = true;
                        }                        
                        if($isUpdate){
                            break;
                        }
                    }
                }else{
                    $create[] = array(
                        'tipo_haber_id' => $haber['tipo']['id'],
                        'moneda' => $haber['moneda'],
                        'monto' => $haber['monto']
                    );
                }
            }

            foreach($misHaberes as $miHaber)
            {
                $isHaber = false;
                foreach($haberes as $haber)
                {
                    if(isset($haber['id'])){
                        if($miHaber['id'] == $haber['id']){
                            $isHaber = true;                        
                        }
                    }
                }
                if(!$isHaber){
                    $destroy[] = array(
                        'id' => $miHaber['id'],
                        'sid' => $miHaber['sid']
                    );
                }
            }
        }else{
            foreach($haberes as $haber){
                $create[] = array(
                    'tipo_haber_id' => $haber['tipo']['id'],
                    'moneda' => $haber['moneda'],
                    'monto' => $haber['monto']
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
    
    public function comprobarDescuentos($descuentos)
    {
        $idTrabajador = $this->id;
        $misDescuentos = $this->misDescuentosPermanentes($idTrabajador);
        $update = array();
        $create = array();
        $destroy = array();
        
        if($misDescuentos){
            foreach($descuentos as $descuento)
            {
                $isUpdate = false;

                if(isset($descuento['id'])){  
                    foreach($misDescuentos as $miDescuento)
                    {
                        if($descuento['id'] == $miDescuento['id']){
                            $update[] = array(
                                'id' => $descuento['id'],
                                'tipo_descuento_id' => $descuento['tipo']['id'],
                                'sid' => $descuento['sid'],
                                'moneda' => $descuento['moneda'],
                                'monto' => $descuento['monto']
                            );
                            $isUpdate = true;
                        }                    
                        if($isUpdate){
                            break;
                        }
                    }
                }else{
                    $create[] = array(
                        'tipo_descuento_id' => $descuento['tipo']['id'],
                        'moneda' => $descuento['moneda'],
                        'monto' => $descuento['monto']
                    );
                }
            }

            foreach($misDescuentos as $miDescuento)
            {
                $isDescuento = false;
                foreach($descuentos as $descuento)
                {
                    if(isset($descuento['id'])){
                        if($miDescuento['id'] == $descuento['id']){
                            $isDescuento = true;                        
                        }
                    }
                }
                if(!$isDescuento){
                    $destroy[] = array(
                        'id' => $miDescuento['id'],
                        'sid' => $miDescuento['sid']
                    );
                }
            }
        }else{
            foreach($descuentos as $descuento){
                $create[] = array(
                    'tipo_descuento_id' => $descuento['tipo']['id'],
                    'moneda' => $descuento['moneda'],
                    'monto' => $descuento['monto']
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
    
    public function misPrestamos()
    {        
        $misPrestamos = $this->prestamos;
        $listaPrestamos = array();
        
        if( $misPrestamos ){
            foreach($misPrestamos as $prestamo){
                $listaPrestamos[] = array(
                    'id' => $prestamo->id,
                    'created_at' => $prestamo->created_at,
                    'glosa' => $prestamo->glosa,
                    'nombreLiquidacion' => $prestamo->nombre_liquidacion,
                    'monto' => $prestamo->monto,
                    'cuotas' => $prestamo->cuotas
                );
            }
        }
        
        return $listaPrestamos;
    }        

    public function totalInasistencias()
    {        
        $totalDias = 0;
        $idMes = \Session::get('mesActivo')->id;
        $inasistencias = Inasistencia::where('trabajador_id', $this->id)->where('mes_id', $idMes)->get();
        
        if($inasistencias->count()){
            foreach($inasistencias as $inasistencia){
                $totalDias = $totalDias + $inasistencia->dias;
            }
        }
        
        return $totalDias;
    }
    
    public function misInasistencias()
    {        
        $idTrabajador = $this->id;
        $idMes = \Session::get('mesActivo')->id;
        $inasistencias = Inasistencia::where('trabajador_id', $idTrabajador)->where('mes_id', $idMes)->get();
        $listaInasistencias = array();
        
        if($inasistencias->count()){
            foreach($inasistencias as $inasistencia){
                $listaInasistencias[] = array(
                    'id' => $inasistencia->id,
                    'sid' => $inasistencia->sid,
                    'idTrabajador' => $inasistencia->trabajador_id,
                    'idMes' => $inasistencia->mes_id,
                    'fechaIngreso' => $inasistencia->created_at->format('d M Y'),
                    'desde' => $inasistencia->desde,
                    'hasta' => $inasistencia->hasta,
                    'dias' => $inasistencia->dias,
                    'motivo' => $inasistencia->motivo,
                    'observacion' => $inasistencia->observacion
                );
            }
        }
        
        return $listaInasistencias;
    }
    
    public function totalLicencias()
    {
        $totalLicencias = 0;
        $idMes = \Session::get('mesActivo')->id;
        $licencias = Licencia::where('trabajador_id', $this->id)->where('mes_id', $idMes)->get();
        
        if($licencias->count()){
            $totalLicencias = $licencias->count();
        }
        
        return $totalLicencias;
    }
    
    public function totalDiasLicencias()
    {
        $totalDiasLicencias = 0;
        $idMes = \Session::get('mesActivo')->id;
        $licencias = Licencia::where('trabajador_id', $this->id)->where('mes_id', $idMes)->get();
        
        if($licencias->count()){
            foreach($licencias as $licencia){
                $totalDiasLicencias += $licencia->dias;
            }
        }
        
        return $totalDiasLicencias;
    }
    
    public function misLicencias()
    {        
        $idTrabajador = $this->id;
        $idMes = \Session::get('mesActivo')->id;
        $licencias = Licencia::where('trabajador_id', $idTrabajador)->where('mes_id', $idMes)->get();
        $listaLicencias = array();
        
        if($licencias->count()){
            foreach($licencias as $licencia){
                $listaLicencias[] = array(
                    'id' => $licencia->id,
                    'sid' => $licencia->sid,
                    'idTrabajador' => $licencia->trabajador_id,
                    'idMes' => $licencia->mes_id,
                    'fechaIngreso' => $licencia->created_at->format('d M Y'),
                    'desde' => $licencia->desde,
                    'hasta' => $licencia->hasta,
                    'dias' => $licencia->dias,
                    'codigo' => $licencia->codigo,
                    'observacion' => $licencia->observacion
                );
            }
        }
        
        return $listaLicencias;
    }
    
    public function totalPrestamos()
    {
        $prestamos = $this->prestamos;
        $totalPrestamos = 0;
        if($prestamos->count()){
            $totalPrestamos = $prestamos->count();
        }
        return $totalPrestamos;
    }
    
    public function totalCuotasPrestamos()
    {
        $cuotas = $this->misCuotasPrestamo();
        $totalCuotasPrestamos = 0;
        if($cuotas){
            foreach($cuotas as $cuota){
                $totalCuotasPrestamos += $cuota['cuotaPagar']->monto;
            }
        }
        
        return $totalCuotasPrestamos;
    }
    
    public function totalHaberes()
    {
        $totalHaberes = 0.00;
        $totalHaberes = ($this->totalImponibles() + $this->noImponibles());
        
        return $totalHaberes;
    }
    
    public function totalHorasExtra()
    {
        $totalHorasExtra = 0.00;
        $idMes = \Session::get('mesActivo')->id;
        $horasExtra = HoraExtra::where('trabajador_id', $this->id)->where('mes_id', $idMes)->get();
        
        if($horasExtra->count()){
            foreach($horasExtra as $horaExtra){
                $totalHorasExtra += $horaExtra->cantidad;
            }
        }
        
        return $totalHorasExtra;
    }
    
    public function sueldoCalcularHorasExtra()
    {
        $sueldo = $this->sueldo();
        $haberes = $this->haberesCalculaHorasExtra();
        
        return ($sueldo + $haberes);
    }
    
    public function haberesCalculaHorasExtra()
    {
        $calculaHorasExtra = 0;
        $misHaberes = $this->misHaberes();
        
        if( $misHaberes){
            foreach($misHaberes as $haber){
                if($haber['tipo']['horasExtra']){
                    $monto = Funciones::convertir($haber['monto'], $haber['moneda']);                 
                    $calculaHorasExtra += $monto;
                }
            }
        }
        
        return $calculaHorasExtra;
    }
    
    public function horasExtraPagar()
    {
        $idTrabajador = $this->id;
        $cantidad = $this->totalHorasExtra($idTrabajador);
        $factor = $this->ficha()->tipoJornada->tramoHoraExtra->factor;
        $sueldo = $this->sueldoCalcularHorasExtra();
        $total = round(($sueldo * $factor) * $cantidad);
        
        $datos = array(
            'cantidad' => $cantidad,
            'factor' => $factor,
            'total' => $total
        );
        
        return $datos;
    }
    
    public function misHorasExtra()
    {        
        $idTrabajador = $this->id;
        $idMes = \Session::get('mesActivo')->id;
        $horasExtra = HoraExtra::where('trabajador_id', $idTrabajador)->where('mes_id', $idMes)->get();
        $listaHorasExtra = array();
        
        if($horasExtra->count())
        {
            foreach($horasExtra as $horaExtra)
            {
                $listaHorasExtra[] = array(
                    'id' => $horaExtra->id,
                    'sid' => $horaExtra->sid,
                    'idTrabajador' => $horaExtra->trabajador_id,
                    'idMes' => $horaExtra->mes_id,
                    'fechaIngreso' => $horaExtra->created_at->format('Y-m-d'),
                    'fecha' => $horaExtra->fecha,
                    'jornada' => $horaExtra->jornada,
                    'cantidad' => $horaExtra->cantidad,
                    'observacion' => $horaExtra->observacion
                );
            }
        }
        
        return $listaHorasExtra;
    }        
    
    public function totalCartasNotificacion()
    {
        $totalCartasNotificacion = 0;
        $cartasNotificacion = $this->cartasNotificacion;
        
        if($cartasNotificacion->count()){
            $totalCartasNotificacion = $cartasNotificacion->count();
        }
        
        return $totalCartasNotificacion;
    }
    
    public function totalDocumentos()
    {
        $totalDocumentos = 0;
        $documentos = $this->documentos;
        
        if($documentos){
            $totalDocumentos = $documentos->count();
        }
        
        return $totalDocumentos;
    }
    
    public function totalCertificados()
    {
        $totalCertificados = 0;
        $certificados = $this->certificados;
        
        if($certificados->count()){
            $totalCertificados = $certificados->count();
        }
        
        return $totalCertificados;
    }
    
    public function misCertificados()
    {        
        $idTrabajador = $this->id;
        $certificados = Certificado::where('trabajador_id', $idTrabajador)->get();
        $listaCertificados = array();
        
        if($certificados->count()){
            foreach($certificados as $certificado){
                $listaCertificados[] = array(
                    'id' => $certificado->id,
                    'sid' => $certificado->sid,
                    'documentoSid' => $certificado->documento->sid,
                    'fecha' => $certificado->fecha,
                    'nombre' => $certificado->documento->nombre,
                    'tipo' => array(
                        'id' => $certificado->plantillaCertificado->id,
                        'nombre' => $certificado->plantillaCertificado->nombre
                    )
                );
            }
        }
        
        return $listaCertificados;
    }
    
    public function misCartasNotificacion()
    {        
        $idTrabajador = $this->id;
        $cartasNotificacion = CartaNotificacion::where('trabajador_id', $idTrabajador)->get();
        $listaCartasNotificacion = array();
        
        if($cartasNotificacion->count()){
            foreach($cartasNotificacion as $cartaNotificacion){
                $listaCartasNotificacion[] = array(
                    'id' => $cartaNotificacion->id,
                    'sid' => $cartaNotificacion->sid,
                    'documentoSid' => $cartaNotificacion->documento,
                    'fecha' => $cartaNotificacion->fecha,
                    'documento' => array(
                        'id' => $cartaNotificacion->documento->id,
                        'sid' => $cartaNotificacion->documento->sid
                    ),
                    'tipo' => array(
                        'id' => $cartaNotificacion->plantillaCartaNotificacion->id,
                        'nombre' => $cartaNotificacion->plantillaCartaNotificacion->nombre
                    )
                );
            }
        }
        
        return $listaCartasNotificacion;
    }
    
    public function misContratos()
    {        
        $idTrabajador = $this->id;
        $contratos = Documento::where('trabajador_id', $idTrabajador)->where('tipo_documento_id', 1)->get();
        $listaContratos = array();
        
        if($contratos->count()){
            foreach($contratos as $contrato){
                $listaContratos[] = array(
                    'id' => $contrato->id,
                    'sid' => $contrato->sid,
                    'nombre' => $contrato->nombre,
                    'alias' => $contrato->alias,
                    'descripcion' => $contrato->descripcion ? $contrato->descripcion : "",
                    'fecha' => $contrato->created_at,
                    'tipo' => array(
                        'id' => $contrato->tipoDocumento->id,
                        'sid' => $contrato->tipoDocumento->sid,
                        'nombre' => $contrato->tipoDocumento->nombre
                    )
                );
            }
        }
        
        return $listaContratos;
    }
    
    public function misDocumentos()
    {        
        $idTrabajador = $this->id;
        $documentos = Documento::where('trabajador_id', $idTrabajador)->get();
        $listaDocumentos = array();
        
        if($documentos->count()){
            foreach($documentos as $documento){
                $listaDocumentos[] = array(
                    'id' => $documento->id,
                    'sid' => $documento->sid,
                    'nombre' => $documento->nombre,
                    'alias' => $documento->alias,
                    'descripcion' => $documento->descripcion ? $documento->descripcion : "",
                    'fecha' => $documento->created_at,
                    'tipo' => array(
                        'id' => $documento->tipoDocumento->id,
                        'sid' => $documento->tipoDocumento->sid,
                        'nombre' => $documento->tipoDocumento->nombre
                    )
                );
            }
        }
        
        return $listaDocumentos;
    }
    
    public function calcularMisVacaciones($fechaReconocimiento)
    {
        $idTrabajador = $this->id;
        $mes = \Session::get('mesActivo');
        $finMes = $mes->fechaRemuneracion;
        $idMes = $mes->id;
        $fechaReconocimiento = new DateTime($fechaReconocimiento);
        $fecha = new DateTime($finMes);
        $diff = $fechaReconocimiento->diff($fecha);
        $meses = (($diff->y * 12) + $diff->m);
        $vacas = ($meses * 1.25);
        
        $vacaciones = new Vacaciones();
        $vacaciones->sid = Funciones::generarSID();
        $vacaciones->trabajador_id = $idTrabajador;
        $vacaciones->mes_id = $idMes;
        $vacaciones->dias = $vacas;
        $vacaciones->save(); 

        $respuesta=array(
            'success' => true,
            'mensaje' => "La Información fue almacenada correctamente",
            'vacaciones' => $vacas
        );
        
        return Response::json($respuesta);
    }
    
    public function asignarVacaciones($dias=0)
    {
        $idTrabajador = $this->id;
        $idMes = \Session::get('mesActivo')->id;        
        
        $vacaciones = new Vacaciones();
        $vacaciones->sid = Funciones::generarSID();
        $vacaciones->trabajador_id = $idTrabajador;
        $vacaciones->mes_id = $idMes;
        $vacaciones->dias = $dias;
        $vacaciones->save(); 

        $respuesta=array(
            'success' => true,
            'mensaje' => "La Información fue almacenada correctamente",
            'vacaciones' => $vacaciones
        );
        
        return Response::json($respuesta);
    }
    
    public function misFiniquitos()
    {        
        $idTrabajador = $this->id;
        $finiquitos = Finiquito::where('trabajador_id', $idTrabajador)->get();
        $listaFiniquitos = array();
        
        if($finiquitos->count()){
            foreach($finiquitos as $finiquito){
                $listaFiniquitos[] = array(
                    'id' => $finiquito->id,
                    'sid' => $finiquito->sid,
                    'fecha' => $finiquito->fecha,
                    'causal' => array(
                        'id' => $finiquito->causalFiniquito->id,
                        'sid' => $finiquito->causalFiniquito->sid,
                        'codigo' => $finiquito->causalFiniquito->codigo,
                        'articulo' => $finiquito->causalFiniquito->articulo,
                        'nombre' => $finiquito->causalFiniquito->nombre
                    ),
                    'documento' => array(
                        'id' => $finiquito->documento->id,
                        'sid' => $finiquito->documento->sid
                    ),
                    'vacaciones' => $finiquito->vacaciones ? true : false,            
                    'sueldo_normal' => $finiquito->sueldo_normal ? true : false,            
                    'sueldo_variable' => $finiquito->sueldo_variable ? true : false,            
                    'mes_aviso' => $finiquito->mes_aviso ? true : false,            
                    'indemnizacion' => $finiquito->indemnizacion ? true : false,
                    'recibido' => $finiquito->recibido ? true : false
                );
            }
        }
        
        return $listaFiniquitos;
    }
    
    public function finiquitar($fecha)
    {
        $mesActual = \Session::get('mesActivo');  
        $idMes = $mesActual->id;  
        $mes = $mesActual->mes;  
        $ficha = $this->ficha();
        
        if($ficha->mes_id!=$idMes){
            $id = (FichaTrabajador::orderBy('id', 'DESC')->first()->id + 1);
            $nuevaFicha = new FichaTrabajador();
            $nuevaFicha = $ficha->replicate();
            $nuevaFicha->id = $id;
            $nuevaFicha->mes_id = $idMes;
            $nuevaFicha->fecha = $mes;
            $nuevaFicha->fecha_finiquito = $fecha;
            $nuevaFicha->estado = 'Finiquitado';
            $nuevaFicha->save(); 
        }else{
            $ficha->fecha_finiquito = $fecha;
            $ficha->estado = 'Finiquitado';
            $ficha->save(); 
        }
        
        return true;
    }
    
    public function isContrato()
    {
        return true;
    }
    //  Liquidación de Sueldo Trabajadores
    
    public function topeGratificacion()
    {    
        $mes = \Session::get('mesActivo')->mes;
        $empresa = \Session::get('empresa');
        $factorIMM = $empresa->tope_gratificacion;
        $rmi = RentaMinimaImponible::where('mes', $mes)->where('nombre', 'Trab. Dependientes e Independientes')->first()->valor;
        $tope = (( $factorIMM * $rmi ) / 12 );
        
        return round($tope);
    }
    
    public function diasTrabajados()
    {        
        $mes = \Session::get('mesActivo');
        $empleado = $this->ficha();
        $diasTrabajados = 30;
        $inasistencias = $this->totalInasistencias();
        $licencias = $this->totalDiasLicencias();
        
        if($empleado->fecha_ingreso>$mes->mes){
            $diaIngreso = (int) date('d', strtotime($empleado->fecha_ingreso));
            $diasTrabajados = $diasTrabajados - ($diaIngreso - 1);
        }
        
        if($empleado->fecha_finiquito){
            $diasTrabajados = (int) date('d', strtotime($empleado->fecha_finiquito));
        }
        
        if($inasistencias>30){
            $inasistencias = 30;
        }
        if($licencias>0){
            $diasTrabajados = (int) date('d', strtotime($mes->fechaRemuneracion));
        }
        
        $diasTrabajados = ($diasTrabajados - $inasistencias - $licencias);
        
        return $diasTrabajados;
    }
    
    public function misDiasTrabajados()
    {        
        $mes = \Session::get('mesActivo');
        $empleado = $this->ficha();
        $diasTrabajados = (int) date('d', strtotime($mes->fechaRemuneracion));
        
        if($empleado->fecha_ingreso>$mes->mes){
            $diaIngreso = (int) date('d', strtotime($empleado->fecha_ingreso));
            $diasTrabajados = $diasTrabajados - ($diaIngreso - 1);
        }
        
        if($empleado->fecha_finiquito){
            $diasTrabajados = (int) date('d', strtotime($empleado->fecha_finiquito));
        }
        
        $diasTrabajados = ($diasTrabajados - $this->totalInasistencias() - $this->totalDiasLicencias());
        
        return $diasTrabajados;
    }
    
    public function diasDescontados()
    {
        $diasTrabajados = $this->diasTrabajados();
        $dias = 0;
        $diasCalendario = 0;
        $monto = 0;
        
        if($diasTrabajados<30){
            $mes = \Session::get('mesActivo');
            $diasMes = (int) date('d', strtotime($mes->fechaRemuneracion));
            $sueldoDiario = $this->sueldoDiario();
            $dias = (30 - $diasTrabajados);
            $diasCalendario = ($diasMes - $diasTrabajados);
            $monto = ($sueldoDiario * $dias);
        }
        
        $datos = array(
            'dias' => $dias,
            'diasCalendario' => $diasCalendario,
            'monto' => $monto
        );
        
        return $datos;
    }
    
    public function sueldoDiario()
    {        
        $sueldoBase = Funciones::convertir($this->ficha()->sueldo_base, $this->ficha()->moneda_sueldo);
        $sueldo_diario = ($sueldoBase / 30);
        
        return $sueldo_diario;
    }

    public function sueldo()
    {        
        $dias_trabajados = $this->diasTrabajados();
        $sueldo = ($this->sueldoDiario() * $dias_trabajados);
        
        return round($sueldo);
    }
    
    public function miSemanaCorrida()
    {        
        $semanaCorrida = $this->ficha()->semana_corrida ? true : false;
        $total = 0;
        if($semanaCorrida){
            $total = $this->totalSemanaCorrida();
        }
        
        return $total;
    }
    
    public function miSemanaCorridas()
    {        
        $semanaCorrida = $this->ficha()->semana_corrida ? true : false;
        $total = 0;
        if($semanaCorrida){
            $total = $this->totalSemanaCorridas();
        }
        
        return $total;
    }
    
    public function totalSemanaCorrida()
    {        
        $semanaCorrida = $this->semanaCorrida();
        $id = $this->id;
        $mes = \Session::get('mesActivo');
        $idMes = $mes->id;
        $inasistencias = Inasistencia::where('trabajador_id', $id)->where('mes_id', $idMes)->get();
        $feriados = Feriado::feriados($mes->mes, $mes->fechaRemuneracion);
        $licencias = Licencia::where('trabajador_id', $id)->where('mes_id', $idMes)->get();
        $fechasFeriados = $this->totalFeriados($feriados);
        $fechasInasistencias = $this->totalFaltas($inasistencias);
        $fechasLicencias = $this->totalFaltas($licencias);
        
        $montoSemana1 = ($semanaCorrida['semanas'][0]['comision'] / (5 - ($fechasLicencias->semana_1 + $fechasFeriados->semana_1)));
        $montoSemana2 = ($semanaCorrida['semanas'][1]['comision'] / (5 - ($fechasLicencias->semana_2 + $fechasFeriados->semana_2)));
        $montoSemana3 = ($semanaCorrida['semanas'][2]['comision'] / (5 - ($fechasLicencias->semana_3 + $fechasFeriados->semana_3)));
        $montoSemana4 = ($semanaCorrida['semanas'][3]['comision'] / (5 - ($fechasLicencias->semana_4 + $fechasFeriados->semana_4)));
        $montoSemana5 = ($semanaCorrida['semanas'][4]['comision'] / (5 - ($fechasLicencias->semana_5 + $fechasFeriados->semana_5)));
        
        $semana1 = ($montoSemana1 + ($montoSemana1 * $fechasFeriados->semana_1) + $semanaCorrida['semanas'][0]['comision']);
        $semana2 = ($montoSemana1 + ($montoSemana2 * $fechasFeriados->semana_2) + $semanaCorrida['semanas'][1]['comision']);
        $semana3 = ($montoSemana1 + ($montoSemana3 * $fechasFeriados->semana_3) + $semanaCorrida['semanas'][2]['comision']);
        $semana4 = ($montoSemana1 + ($montoSemana4 * $fechasFeriados->semana_4) + $semanaCorrida['semanas'][3]['comision']);
        $semana5 = ($montoSemana1 + ($montoSemana5 * $fechasFeriados->semana_5) + $semanaCorrida['semanas'][4]['comision']);

        $total = ($semana1 + $semana2 + $semana3 + $semana4 + $semana5);
        
        return round($total);
    }
    
    public function totalSemanaCorridas()
    {        
        $semanaCorrida = $this->semanaCorrida();
        $id = $this->id;
        $mes = \Session::get('mesActivo');
        $idMes = $mes->id;
        $inasistencias = Inasistencia::where('trabajador_id', $id)->where('mes_id', $idMes)->get();
        $feriados = Feriado::feriados($mes->mes, $mes->fechaRemuneracion);
        $licencias = Licencia::where('trabajador_id', $id)->where('mes_id', $idMes)->get();
        $fechasFeriados = $this->totalFeriados($feriados);
        $fechasInasistencias = $this->totalFaltas($inasistencias);
        $fechasLicencias = $this->totalFaltas($licencias);
        
        $montoSemana1 = ($semanaCorrida['semanas'][0]['comision'] / (5 - ($fechasLicencias->semana_1 + $fechasFeriados->semana_1)));
        $montoSemana2 = ($semanaCorrida['semanas'][1]['comision'] / (5 - ($fechasLicencias->semana_2 + $fechasFeriados->semana_2)));
        $montoSemana3 = ($semanaCorrida['semanas'][2]['comision'] / (5 - ($fechasLicencias->semana_3 + $fechasFeriados->semana_3)));
        $montoSemana4 = ($semanaCorrida['semanas'][3]['comision'] / (5 - ($fechasLicencias->semana_4 + $fechasFeriados->semana_4)));
        $montoSemana5 = ($semanaCorrida['semanas'][4]['comision'] / (5 - ($fechasLicencias->semana_5 + $fechasFeriados->semana_5)));
        
        $semana1 = ($montoSemana1 + ($montoSemana1 * $fechasFeriados->semana_1) + $semanaCorrida['semanas'][0]['comision']);
        $semana2 = ($montoSemana1 + ($montoSemana2 * $fechasFeriados->semana_2) + $semanaCorrida['semanas'][1]['comision']);
        $semana3 = ($montoSemana1 + ($montoSemana3 * $fechasFeriados->semana_3) + $semanaCorrida['semanas'][2]['comision']);
        $semana4 = ($montoSemana1 + ($montoSemana4 * $fechasFeriados->semana_4) + $semanaCorrida['semanas'][3]['comision']);
        $semana5 = ($montoSemana1 + ($montoSemana5 * $fechasFeriados->semana_5) + $semanaCorrida['semanas'][4]['comision']);

        $total = ($semana1 + $semana2 + $semana3 + $semana4 + $semana5);
        
        $datos = array(
            'semana1' => $semana1,
            'semana2' => $semana2,
            'semana3' => $semana3,
            'semana4' => $semana4,
            'semana5' => $semana5,
            'feriados' => $fechasFeriados,
            'licencias' => $fechasLicencias
        );
        
        return $datos;
    }
    
    public function totalFaltas($faltas)
    {
        $fechas = new stdClass();
        $fechas->semana_1 = 0;
        $fechas->semana_2 = 0;
        $fechas->semana_3 = 0;
        $fechas->semana_4 = 0;
        $fechas->semana_5 = 0;
        
        if($faltas->count()){
            $semanaAnterior = 0;
            $cont = 0;
            $mes = \Session::get('mesActivo');
            foreach($faltas as $falta){
                $desde = $falta['desde'];
                $hasta = $falta['hasta'];
                $inicial = (int) date('W', strtotime($mes->mes));
                $final = (int) date('W', strtotime($mes->fechaRemuneracion));
                $diaDesde = (int) date('j', strtotime($desde));
                $diaHasta = (int) date('j', strtotime($hasta));
                $resto = ($diaHasta - $diaDesde);
                for($i=0; $i<=$resto; $i++){
                    $fecha = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($desde)));
                    $semanaActual = (int) date('W', strtotime($fecha));     
                    $n = 'semana_' . (($semanaActual - $inicial) + 1);
                    if($semanaAnterior==$semanaActual){
                        $cont++;
                    }else{
                        $semanaAnterior = $semanaActual;
                        $cont = 1;
                    }
                    $fechas->$n = $cont;                
                }
            }
        }
        
        return $fechas;
    }
    
    public function totalFeriados($feriados)
    {
        $fechas = new stdClass();
        $fechas->semana_1 = 0;
        $fechas->semana_2 = 0;
        $fechas->semana_3 = 0;
        $fechas->semana_4 = 0;
        $fechas->semana_5 = 0;
        
        if(count($feriados)){
            $semanaAnterior = 0;
            $cont = 0;
            $mes = \Session::get('mesActivo');
            $inicial = (int) date('W', strtotime($mes->mes));
            $final = (int) date('W', strtotime($mes->fechaRemuneracion));
            foreach($feriados as $feriado){                
                $semanaActual = (int) date('W', strtotime($feriado));                
                $n = 'semana_' . (($semanaActual - $inicial) + 1);
                if($semanaAnterior==$semanaActual){
                    $cont++;
                }else{
                    $semanaAnterior = $semanaActual;
                    $cont = 1;
                }
                $fechas->$n = $cont;                
            }
        }
        
        return $fechas;
    }
    
    public function imponibles()
    {        
        $imponibles = 0;
        $misHaberes = $this->misHaberes();
        
        if( $misHaberes){
            foreach($misHaberes as $haber){
                if($haber['tipo']['imponible']){
                    $monto = Funciones::convertir($haber['monto'], $haber['moneda']);                 
                    $imponibles += $monto;
                }
            }
        }
        
        return $imponibles;
    }
    
    public function sumaNoImponibles()
    {        
        $misHaberes = $this->misHaberes();
        $noImponibles = 0;
        
        if( $misHaberes){
            foreach($misHaberes as $haber){
                if(!$haber['tipo']['imponible']){
                    $monto = Funciones::convertir($haber['monto'], $haber['moneda']);
                    $noImponibles += $monto;
                }
            }
        }
        
        return $noImponibles;
    }
    
    public function sumaImponibles()
    {        
        $gratificacion = $this->gratificacion();
        $imponibles = $this->imponibles();
        $sueldo = $this->sueldo();
        $semanaCorrida = $this->miSemanaCorrida();
        $horasExtra = $this->horasExtraPagar()['total'];
        
        return ($imponibles + $gratificacion + $sueldo + $semanaCorrida + $horasExtra);
    }
    
    public function rentaImponible()
    {        
        $mes = \Session::get('mesActivo')->mes;
        $empleado = $this->ficha();
        $rentaImponible = $this->totalImponibles();
        
        if($empleado->prevision_id!=10){
            if($empleado->prevision_id==8){
                $topeImponible = RentaTopeImponible::where('mes', $mes)->where('nombre', 'Para afiliados a una AFP')->first()->valor;
            }else if($empleado->prevision_id==9){
                $topeImponible = RentaTopeImponible::where('mes', $mes)->where('nombre', 'Para afiliados al IPS (ex INP)')->first()->valor;
            }
            $valorTope = Funciones::convertirUF($topeImponible);
            
            if($rentaImponible > $valorTope){
                $rentaImponible = $valorTope;
            }
        }
        
        return round($rentaImponible);
    }
    
    public function haberesTributables()
    {    
        $tributables = 0;
        $misHaberes = $this->haberesImponibles();
        
        if( $misHaberes){
            foreach($misHaberes as $haber){
                if($haber['tipo']['tributable']){
                    $monto = Funciones::convertir($haber['monto'], $haber['moneda']);                 
                    $tributables += $monto;
                }
            }
        }
        
        return $tributables;
    }
    
    public function rentaImponibleTributable()
    {        
        $haberesTributables = $this->haberesTributables();
        $gratificacion = $this->gratificacion();
        $sueldo = $this->sueldo();
        $horasExtra = $this->horasExtraPagar()['total'];
        $semanaCorrida = $this->miSemanaCorrida();
                
        $rentaImponibleTributable = ($sueldo + $haberesTributables + $gratificacion + $horasExtra + $semanaCorrida);

        return $rentaImponibleTributable;
    }
    
    public function tasaAfp()
    {        
        $mes = \Session::get('mesActivo')->mes;
        $empleado = $this->ficha();
        $idAfp = $empleado->afp_id;
        $empresa = \Session::get('empresa');
        $sis = $empresa->sis ? true : false;
        $tasaTrabajador = 0;
        $tasa = 0;
        $tasaSis = 0;
        $tasaEmpleador = 0;
        
        if($empleado->prevision_id==8){
            $tasa = TasaCotizacionObligatorioAfp::where('afp_id', $idAfp)->where('mes', $mes)->first()['tasa_afp'];
            $tasaTrabajador = $tasa;
            $tasaSis = TasaCotizacionObligatorioAfp::where('afp_id', $idAfp)->where('mes', $mes)->first()['sis'];
            if($sis){
                $tasaEmpleador = $tasaSis;
            }else{
                $tasaTrabajador += $tasaSis;
            }
        }else if($empleado->prevision_id==9){
            $mes = '2017-01-01';
            $tasa = TasaCajasExRegimen::where('caja_id', 1)->where('mes', $mes)->first()['tasa'];
            $tasaTrabajador = $tasa;
        }
        
        $datos = array(
            'tasaTrabajador' => $tasaTrabajador,
            'tasaEmpleador' => $tasaEmpleador,
            'tasaObligatoria' => $tasa,
            'tasaSis' => $tasaSis
        );
        
        return $datos;
    }
    
    public function cuentaAhorroVoluntario()
    {        
        $descuentos = $this->misDescuentos();
        if(count($descuentos)){
            foreach($descuentos as $descuento){
                if($descuento['tipo']['estructura']['id']==7){
                    $total = $descuento['montoPesos'];
                    return $total;
                }
            }
        }
        
        return 0;
    }
    
    public function descuentosCaja()
    {        
        $descuentos = $this->misDescuentos();
        $creditosPersonales = 0;
        $descuentoDental = 0;
        $descuentosLeasing = 0;
        $descuentosSeguro = 0;
        $otrosDescuentos = 0;
        $descuentoCargas = 0;
        
        if(count($descuentos)){
            foreach($descuentos as $descuento){
                if($descuento['tipo']['estructura']['id']==6){
                    if($descuento['tipo']['id']==6){
                        $creditosPersonales += $descuento['montoPesos'];
                    }else if($descuento['tipo']['id']==7){
                        $descuentoDental += $descuento['montoPesos'];
                    }else if($descuento['tipo']['id']==8){
                        $descuentosLeasing += $descuento['montoPesos'];
                    }else if($descuento['tipo']['id']==9){
                        $descuentosSeguro += $descuento['montoPesos'];
                    }else if($descuento['tipo']['id']==6){
                        $otrosDescuentos += $descuento['montoPesos'];
                    }else if($descuento['tipo']['id']==10){
                        $descuentoCargas += $descuento['montoPesos'];
                    } 
                }
            }
        }
        $datos = array(
            'creditosPersonales' => $creditosPersonales,
            'descuentoDental' => $descuentoDental,
            'descuentosLeasing' => $descuentosLeasing,
            'descuentosSeguro' => $descuentosSeguro,
            'otrosDescuentos' => $otrosDescuentos,
            'descuentoCargas' => $descuentoCargas
        );
        return $datos;
    }
    
    public function apvc()
    {        
        $descuentos = $this->misDescuentos();
        if(count($descuentos)){
            foreach($descuentos as $descuento){
                if($descuento['tipo']['estructura']['id']==3){
                    $idAfp = TipoDescuento::find($descuento['tipo']['id'])->nombre;
                    $datos = array(
                        'monto' => $descuento['monto'],
                        'moneda' => $descuento['moneda'],
                        'cotizacionTrabajador' => $descuento['montoPesos'],
                        'cotizacionEmpleador' => 0,
                        'numeroContrato' => '',
                        'idAfp' => $idAfp,
                        'idFormaPago' => 102
                    );
                    return $datos; 
                }
            }
        }
        
        return null;
    }
    
    public function totalAfp()
    {        
        $diasTrabajados = $this->diasTrabajados(); 
        $tasa = $this->tasaAfp();
        $rentaImponible = $this->rentaImponible();
        $totalTrabajador = (($tasa['tasaTrabajador'] * $rentaImponible ) / 100);
        $totalEmpleador = (( $tasa['tasaEmpleador'] * $rentaImponible ) / 100);
        $cotizacion = (( $tasa['tasaObligatoria'] * $rentaImponible ) / 100);
        $sis = (( $tasa['tasaSis'] * $rentaImponible ) / 100);
        
        $datos = array(
            'totalTrabajador' => round($totalTrabajador),
            'totalEmpleador' => round($totalEmpleador),
            'cotizacion' => round($cotizacion),
            'sis' => round($sis),
            'cuentaAhorroVoluntario' => $this->cuentaAhorroVoluntario()
        );
        
        return $datos;
    }
    
    public function movimientoPersonal()
    {        
        $mesActual = \Session::get('mesActivo');
        $mes = $mesActual->mes;
        $fechaRemuneracion = $mesActual->fechaRemuneracion;
        $codigo = 0;
        $fechaDesde = null;
        $fechaHasta = null;
        $empleado = $this->ficha();
        $fechaReconocimiento = $empleado->fecha_reconocimiento;
        $fechaFiniquito = $empleado->fecha_finiquito;
        $tipoContrato = $empleado->tipo_contrato_id;
        $rut = '';
        $digito = '';
        $licencias = $this->misLicencias();
        
        if($fechaReconocimiento>=$mes && $fechaReconocimiento<=$fechaRemuneracion){
            if($tipoContrato==1){
                $codigo = 1;
            }else if($tipoContrato==2){
                $codigo = 7;
            }
            $fechaDesde = $fechaReconocimiento;
        }else if($empleado->estado=='Finiquitado'){
            if($fechaFiniquito>=$mes && $fechaFiniquito<=$fechaRemuneracion){
                $codigo = 2;
                $fechaHasta = $fechaFiniquito;                
            }
        }else if(count($licencias)){
            $codigo = 3;
            $fechaDesde = $licencias[0]['desde'];
            $fechaHasta = $licencias[0]['hasta'];
        }
        
        $datos = array(
            'codigo' => $codigo,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'rut' => $rut,
            'digito' => $digito
        );
        
        return $datos;
    }
    
    public function totalColacion()
    {        
        $ficha = $this->ficha();
        $monto = $ficha->monto_colacion;
        $totalColacion = 0;
        
        if($monto){
            $totalColacion = Funciones::convertir($monto, $ficha->moneda_colacion);
            $diasTrabajados = $this->diasTrabajados();
            if($diasTrabajados < 30){
                $totalColacion = (($totalColacion / 30) * $diasTrabajados);
            }
        }        
        
        return round($totalColacion);
    }
    
    public function totalMovilizacion()
    {        
        $ficha = $this->ficha();
        $monto = $ficha->monto_movilizacion;
        $totalMovilizacion = 0;
        
        if($monto){
            $totalMovilizacion = Funciones::convertir($monto, $ficha->moneda_movilizacion);
            $diasTrabajados = $this->diasTrabajados();
            if($diasTrabajados < 30){
                $totalMovilizacion = (($totalMovilizacion / 30) * $diasTrabajados);
            }
        }        
        
        return round($totalMovilizacion);
    }
    
    public function totalViatico()
    {        
        $ficha = $this->ficha();
        $monto = $ficha->monto_viatico;
        $totalViatico = 0;
        
        if($monto){
            $totalViatico = Funciones::convertir($monto, $ficha->moneda_viatico);
            $diasTrabajados = $this->diasTrabajados();
            if($diasTrabajados < 30){
                $totalViatico = (($totalViatico / 30) * $diasTrabajados);
            }
        }        
        
        return round($totalViatico);
    }
    
    public function totalMutual()
    {   
        $empresa = \Session::get('empresa');
        $rentaImponible = $this->rentaImponible();
        $totalMutual = 0;        
        
        $fijo = $empresa->tasa_fija_mutual;
        $adicional = $empresa->tasa_adicional_mutual;
        $totalMutual = round((($rentaImponible * $fijo) + ($rentaImponible * $adicional)) / 100);
        
        return $totalMutual;
    }
    
    public function totalSalud()
    {        
        $idSalud = $this->ficha()->isapre->id;
        $rentaImponible = $this->rentaImponible();
        $adicional = 0;
        $montoSalud = ( $rentaImponible * 0.07 );
        $excedente = 0;
        $montoCaja = 0;
        $montoFonasa = 0;
        $empresa = \Session::get('empresa');
        $bool = false;        
        
        if($this->sueldo()>0 && $idSalud!=240){
            $diasTrabajados = $this->diasTrabajados(); 
            if($idSalud!=246){
                $cotizacionSalud = $this->ficha()->cotizacion_isapre; 

                if($cotizacionSalud == 'UF'){
                    $totalSalud = Funciones::convertirUF($this->ficha()->monto_isapre);
                    $adicional = ($totalSalud - $montoSalud);
                }else if($cotizacionSalud == '7%'){
                    $uf = Funciones::convertirUF($this->ficha()->monto_isapre);
                    $totalSalud = ( $rentaImponible * 0.07 );
                    $adicional = ($totalSalud - $montoSalud);
                }else if($cotizacionSalud == '7% + UF'){
                    $uf = Funciones::convertirUF($this->ficha()->monto_isapre);
                    $base = ( $rentaImponible * 0.07 );
                    $totalSalud = ($base + $uf);
                    $adicional = ($totalSalud - $montoSalud);
                }else{
                    $totalSalud = $this->ficha()->monto_isapre;
                    $adicional = ($totalSalud - $montoSalud);
                }            
                if($adicional<0){
                    $excedente = ($adicional * -1);
                    $adicional = 0;
                    $totalSalud = $montoSalud;
                }
            }else{
                $totalSalud = $montoSalud;  
                if($empresa->caja_id!=257){
                    $montoFonasa = ( $rentaImponible * 0.064 );
                    $montoCaja = ( $rentaImponible * 0.006 );
                }else{
                    $montoFonasa = ( $rentaImponible * 0.07 );                    
                }  
            }                        
        }else{
            $montoSalud = 0;
            $adicional = 0;
            $excedente = 0;
            $totalSalud = 0;
        }
        
        $datos = array(
            'obligatorio' => round($montoSalud),
            'montoFonasa' => round($montoFonasa),
            'montoCaja' => round($montoCaja),
            'adicional' => round($adicional),
            'excedente' => round($excedente),
            'total' => round($totalSalud)
        );
        
        return $datos;
    }
  
    public function antiguedad()
    {
      $mes = \Session::get('mesActivo');
      $finMes = $mes->fechaRemuneracion;
      $empleado = $this->ficha();
      $fechaReconocimiento = $empleado->fecha_reconocimiento;
      $fechaReconocimiento = new DateTime($fechaReconocimiento);
      $fecha = new DateTime($finMes);
      $diff = $fechaReconocimiento->diff($fecha);
      $anios = $diff->y;
      
      return $anios;
    }
    
    public function antiguedadMeses()
    {
      $mes = \Session::get('mesActivo');
      $finMes = $mes->fechaRemuneracion;
      $empleado = $this->ficha();
      $fechaReconocimiento = $empleado->fecha_reconocimiento;
      $fechaReconocimiento = new DateTime($fechaReconocimiento);
      $fecha = new DateTime($finMes);
      $diff = $fechaReconocimiento->diff($fecha);
      $meses = (($diff->y * 12) + $diff->m);
      
      return $meses;
    }
    
    public function totalSeguroCesantia()
    {        
        $mes = \Session::get('mesActivo')->mes;
        $totalSeguroCesantiaTrabajador = 0;
        $totalSeguroCesantiaEmpleador = 0;
        $empleado = $this->ficha();
        $afcTrabajador = 0;
        $afcEmpleador = 0;
        
        if($empleado->seguro_desempleo){
            $diasTrabajados = $this->diasTrabajados();            
            if($empleado->tipoContrato['id']==2){
              $indefinido = false;
            }else{
              $indefinido = true;
            }  
            
            $rentaImponible = $this->sumaImponibles();
            $topeSeguro = RentaTopeImponible::where('mes', $mes)->where('nombre', 'Para Seguro de Cesantía')->first()->valor;
            $topeSeguroPesos = Funciones::convertirUF($topeSeguro);        

            if($rentaImponible > $topeSeguroPesos){
              $rentaImponible = $topeSeguroPesos;
            }
            
            if($this->antiguedad()<11){                          
              
              if($rentaImponible > 0){
                  if($indefinido){
                      $afcTrabajador = SeguroDeCesantia::where('mes', $mes)->where('tipo_contrato', 'Contrato Plazo Indefinido')->first()->financiamiento_trabajador;
                      $afcEmpleador = SeguroDeCesantia::where('mes', $mes)->where('tipo_contrato', 'Contrato Plazo Indefinido')->first()->financiamiento_empleador;
                      $totalSeguroCesantiaTrabajador = round((( $afcTrabajador * $rentaImponible ) / 100 ));
                      $totalSeguroCesantiaEmpleador = round((( $afcEmpleador * $rentaImponible ) / 100 ));
                  }else{
                      $afcTrabajador = SeguroDeCesantia::where('mes', $mes)->where('tipo_contrato', 'Contrato Plazo Fijo')->first()->financiamiento_trabajador;
                      $afcEmpleador = SeguroDeCesantia::where('mes', $mes)->where('tipo_contrato', 'Contrato Plazo Fijo')->first()->financiamiento_empleador;
                      $totalSeguroCesantiaTrabajador = round((( $afcTrabajador * $rentaImponible ) / 100 ));
                      $totalSeguroCesantiaEmpleador = round((( $afcEmpleador * $rentaImponible ) / 100 ));
                  }
              }
            }else{
                if($rentaImponible > 0){
                  if($indefinido){
                      $afcTrabajador = SeguroDeCesantia::where('mes', $mes)->where('tipo_contrato', 'Contrato Plazo Indefinido 11 años o más ')->first()->financiamiento_trabajador;
                      $afcEmpleador = SeguroDeCesantia::where('mes', $mes)->where('tipo_contrato', 'Contrato Plazo Indefinido 11 años o más ')->first()->financiamiento_empleador;
                      $totalSeguroCesantiaTrabajador = round((( $afcTrabajador * $rentaImponible ) / 100 ));
                      $totalSeguroCesantiaEmpleador = round((( $afcEmpleador * $rentaImponible ) / 100 ));
                  }else{
                      $afcTrabajador = SeguroDeCesantia::where('mes', $mes)->where('tipo_contrato', 'Contrato Plazo Fijo')->first()->financiamiento_trabajador;
                      $afcEmpleador = SeguroDeCesantia::where('mes', $mes)->where('tipo_contrato', 'Contrato Plazo Fijo')->first()->financiamiento_empleador;
                      $totalSeguroCesantiaTrabajador = round((( $afcTrabajador * $rentaImponible ) / 100 ));
                      $totalSeguroCesantiaEmpleador = round((( $afcEmpleador * $rentaImponible ) / 100 ));
                  }
              }
            }
        }
      
        $datos = array(
            'afc' => $afcTrabajador,
            'afcEmpleador' => $afcEmpleador,
            'total' => $totalSeguroCesantiaTrabajador,
            'totalEmpleador' => $totalSeguroCesantiaEmpleador
        );
        
        return $datos;
    }
    
    public function isLiquidacion($mes=null)
    {
        if(!$mes){
            $mes = \Session::get('mesActivo')->mes;
        }
        $isLiquidacion = false;
        $liquidaciones = Liquidacion::where('trabajador_id', $this->id)->where('mes', $mes)->get();
        
        if($liquidaciones->count()){
            $isLiquidacion = true;
        }
        
        return $isLiquidacion;
    }
    
    public function totalDescuentosPrevisionales()
    {                           
        $totalAfp = $this->totalAfp()['totalTrabajador'];
        $totalSalud = $this->totalSalud();
        $totalSeguroCesantia = $this->totalSeguroCesantia();        
        $totalDescuentos = ( $totalAfp + $totalSalud['total'] + $totalSeguroCesantia['total']);
        
        return $totalDescuentos;
    }
    
    public function totalOtrosDescuentos()
    {        
        $id = $this->id;
        $empleado = $this->ficha();
        $mes = \Session::get('mesActivo')->mes;
        $descuentos = $this->misDescuentos();
        $cuotas = $this->totalCuotasPrestamos();
        $apvs = $empleado->totalApv();
        $sumaDescuentos = 0;
        
        if($descuentos){
            foreach($descuentos as $desc){
                $monto = Funciones::convertir($desc['monto'], $desc['moneda']);
                $sumaDescuentos += $monto;
            }    
        }
        
        $sumaDescuentos = $sumaDescuentos + $apvs + $cuotas;
              
        return $sumaDescuentos;
    } 
    
    public function totalAnticipos()
    {        
        $id = $this->id;
        $empleado = $this->ficha();
        $mes = \Session::get('mesActivo')->mes;
        $descuentos = $this->misDescuentos();        
        $sumaAnticipos = 0;
        
        if($descuentos){
            foreach($descuentos as $desc){
                if($desc['tipo']['id']==4){
                    $sumaAnticipos += $desc['montoPesos'];
                }
            }
        }
                      
        return $sumaAnticipos;
    }        
    
    public function baseImpuestoUnico()
    {        
        $rentaImponibleTributable = $this->rentaImponibleTributable();
        $baseImpuestoUnico = 0;
        $mes = \Session::get('mesActivo')->mes;
        $zona = \Session::get('empresa')->zona;
        
        if($rentaImponibleTributable > 0){
            $topeImponible = Funciones::convertirUF(RentaTopeImponible::where('mes', $mes)->where('nombre', 'Para afiliados a una AFP')->first()->valor);
            $topeImponible = ($topeImponible * 0.07);
            $salud = $this->totalSalud()['total'];
            $totalDescuentosPrevisionales = $this->totalDescuentosPrevisionales();
            $apvsRegimenB = $this->totalApvsRegimenB();
            if($salud>$topeImponible){
                $resto = ($salud - $topeImponible);
                $totalDescuentosPrevisionales = ($totalDescuentosPrevisionales - $resto);
            }

            $baseImpuestoUnico = ($rentaImponibleTributable - $totalDescuentosPrevisionales - $apvsRegimenB);
            $baseImpuestoUnico = round($baseImpuestoUnico - ($baseImpuestoUnico * ($zona / 100)));
        }else{
            $baseImpuestoUnico = 1;
        }
        
        return $baseImpuestoUnico;
    }
    
    public function tramoImpuesto()
    {        
        $mes = \Session::get('mesActivo')->mes;
        $tramos = TablaImpuestoUnico::where('mes', $mes)->get();
        $factor = 0;
        foreach($tramos as $tramo){
            $desde = Funciones::convertirUTM($tramo->imponible_mensual_desde, false);
            $hasta = Funciones::convertirUTM($tramo->imponible_mensual_hasta, false);
            $baseImpuestoUnico = $this->baseImpuestoUnico();

            if($baseImpuestoUnico > $desde && $baseImpuestoUnico <= $hasta){
                $factor = $tramo;
                break;
            }
        }     
        
        return $factor;
    }
    
    public function totalApvsRegimenB()
    {
        $empleado = $this->ficha();
        $apvs = $empleado->misApvs();
        $totalRebajar = 0;
        if($apvs){
            foreach($apvs as $apv){
                if(strtoupper($apv['regimen'])=='B'){
                    $totalRebajar += Funciones::convertir($apv['monto'], $apv['moneda']);
                }
            }
        }
        
        return $totalRebajar;
    }
    
    static function isAllLiquidados($idMes)
    {
        $mes = MesDeTrabajo::find($idMes);
        $finMes = $mes['fecha_remuneracion'];
        $trabajadores = Trabajador::all();
        $bool = true;
        
        if($trabajadores){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        if(!$trabajador->isLiquidacion($mes['mes'])){
                        $trabajadore[] = $trabajador;
                            $bool = false;
                            break;
                        }
                    }
                }
            }
        }
        
        return $bool;
    }
    
    public function impuestoDeterminado()
    {                        
        $tramo = $this->tramoImpuesto();
        $factor = $tramo->factor;
        $cantidadARebajar = Funciones::convertirUTM($tramo->cantidad_a_rebajar, false);
        $baseImpuestoUnico = $this->baseImpuestoUnico();        
        
        $impuestoDeterminado = round((($factor / 100) * $baseImpuestoUnico) - $cantidadARebajar);
        
        return $impuestoDeterminado;
    }
    
    public function noImponibles()
    {        
        $empleado = $this->ficha();
        $noImponibles = $this->sumaNoImponibles();
        $permanentes = ($empleado->monto_colacion + $empleado->monto_viatico + $empleado->monto_movilizacion);
        $cargasFamiliares = $empleado->cargasFamiliares()['monto'];
        $total = ($noImponibles + $permanentes + $cargasFamiliares);
        
        return $total;
    }
    
    public function totalImponibles()
    {        
        $gratificacion = $this->gratificacion();
        $imponibles = $this->imponibles();
        $sueldo = $this->sueldo();
        $horasExtra = $this->horasExtraPagar()['total'];
        $semanaCorrida = $this->miSemanaCorrida();
        
        $total = ($imponibles + $sueldo + $gratificacion + $horasExtra + $semanaCorrida);
        
        return $total;
    }    
    
    public function remuneracionAnualTrabajador()
    {
        $sum = 0;
        $mesActual = \Session::get('mesActivo')->mes;
        $mesAnterior = date('Y-m-d', strtotime('-' . 1 . ' year', strtotime($mesActual)));
        $liquidaciones = Liquidacion::where('trabajador_id', $this->id)->whereBetween('mes', [$mesAnterior, $mesActual])->get();
        if($liquidaciones->count()){
            $sum = $liquidaciones->sum('sueldo_liquido');
        }
        
        return $sum;
    }
    
    public function haberesGratificacion()
    {    
        $haberesGratificacion = 0;
        $misHaberes = $this->misHaberes();
        
        if( $misHaberes){
            foreach($misHaberes as $haber){
                if($haber['tipo']['gratificacion']){
                    $monto = Funciones::convertir($haber['monto'], $haber['moneda']);                 
                    $haberesGratificacion += $monto;
                }
            }
        }
        
        return $haberesGratificacion;
    }
    
    public function baseGratificacion()
    {
        $haberesGratificacion = $this->haberesGratificacion();
        $sueldo = $this->sueldo();
        
        return ($sueldo + $haberesGratificacion);
    }
    
    public function tipoGratificacion()
    {
        $empresa = \Session::get('empresa');
        $tipoGratificacion = '';
        
        if($empresa->gratificacion=='e'){
            $tipoGratificacion = $empresa->tipo_gratificacion;
        }else{
            $empleado = $this->ficha();
            $tipoGratificacion = $empleado->gratificacion;
        }
        
        return $tipoGratificacion;
    }
    
    public function gratificacion()
    {
        $gratificacion = 0;
        $baseGratificacion = $this->baseGratificacion();
        $diasTrabajados = $this->diasTrabajados();
        $tipoGratificacion = $this->tipoGratificacion();        
            
        if($baseGratificacion > 0){
            if($tipoGratificacion=='m'){
                $gratificacion = (($baseGratificacion) * 0.25);
                $topeGratificacion = $this->topeGratificacion();
                if($gratificacion > $topeGratificacion){
                    $gratificacion = $topeGratificacion;
                }  
                if($diasTrabajados<30){
                    $gratificacion = (($gratificacion / 30) * $diasTrabajados);
                }
            }else{
                $mes = \Session::get('mesActivo');
                $mesActual = $mes->mes;
                $anio = AnioRemuneracion::find($mes->idAnio);
                $gratificacion = 1;
                if($anio->gratificacion==$mesActual){
                    $gratificacion = 2;
                    $utilidad = $anio->utilidad;
                    $antiguedad = $this->antiguedadMeses();
                    if($utilidad){
                        $remuneracionAnualDevengada = Liquidacion::remuneracionAnualDevengada();
                        $remuneracionAnualTrabajador = $this->remuneracionAnualTrabajador();
                        $factor = ($utilidad / $remuneracionAnualDevengada);
                        $gratificacion = ($factor * $remuneracionAnualTrabajador);
                        if($antiguedad<12){
                            $gratificacion = (($gratificacion / 12) * $antiguedad);
                        }
                    }
                }
            }
        }                      
        
        return round($gratificacion);
    }
    
    public function sueldoLiquido()
    {        
        $imponibles = $this->totalImponibles();
        $noImponibles = $this->noImponibles();
        $descuentosPrevisionales = $this->totalDescuentosPrevisionales();
        $descuentosTributarios = $this->impuestoDeterminado();
        $otrosDescuentos = $this->totalOtrosDescuentos();
        $sueldoLiquido = 0;
        
        if($this->sueldo()){
            $sueldoLiquido = ( $imponibles + $noImponibles - $descuentosPrevisionales - $descuentosTributarios - $otrosDescuentos );
        }
        
        return round($sueldoLiquido);
    }
    
    public function eliminarDatos()
    {
        $idTrabajador = $this->id;
        $fichas = FichaTrabajador::where('trabajador_id', $idTrabajador)->get();
        if($fichas->count()){
            foreach($fichas as $ficha){
                $apvs = $ficha->apvs;
                if($apvs->count()){
                    foreach($apvs as $apv){
                        $apv->delete();
                    }
                }
                $cargas = $ficha->cargas;
                if($cargas->count()){
                    foreach($cargas as $carga){
                        $carga->delete();
                    }
                }
                $ficha->delete();
            }
        }
        $haberes = Haber::where('trabajador_id', $idTrabajador)->get();
        if($haberes->count()){
            foreach($haberes as $haber){
                $haber->delete();
            }
        }
        $descuentos = Descuento::where('trabajador_id', $idTrabajador)->get();
        if($descuentos->count()){
            foreach($descuentos as $descuento){
                $descuento->delete();
            }
        }
        $horasExtra = HoraExtra::where('trabajador_id', $idTrabajador)->get();
        if($horasExtra->count()){
            foreach($horasExtra as $horaExtra){
                $horaExtra->delete();
            }
        }
        $inasistencias = Inasistencia::where('trabajador_id', $idTrabajador)->get();
        if($inasistencias->count()){
            foreach($inasistencias as $inasistencia){
                $inasistencia->delete();
            }
        }
        $licencias = Licencia::where('trabajador_id', $idTrabajador)->get();
        if($licencias->count()){
            foreach($licencias as $licencia){
                $licencia->delete();
            }
        }
        $semanas = SemanaCorrida::where('trabajador_id', $idTrabajador)->get();
        if($semanas->count()){
            foreach($semanas as $semana){
                $semana->delete();
            }
        }
        $vacaciones = Vacaciones::where('trabajador_id', $idTrabajador)->get();
        if($vacaciones->count()){
            foreach($vacaciones as $vacacion){
                $vacacion->delete();
            }
        }
        $prestamos = Prestamo::where('trabajador_id', $idTrabajador)->get();
        if($prestamos->count()){
            foreach($prestamos as $prestamo){
                $prestamos->eliminarPrestamo();
            }
        }
        $documentos = Documento::where('trabajador_id', $idTrabajador)->get();
        if($documentos->count()){
            foreach($documentos as $documento){
                $documento->eliminarDocumento();
            }
        }
    }

    static function errores($datos)
    {         
        $rules = array(
            /*'rut' => 'required',
            'nombres' => 'required',
            'apellidos' => 'required',
            'nacionalidad' => 'required',
            'sexo' => 'required',
            'estado_civil' => 'required',
            'fecha_nacimiento' => 'required',
            'direccion' => 'required',
            'comuna_id' => 'required',
            'celular' => 'required',
            'email' => 'required',
            'cargo' => 'required',
            'seccion_id' => 'required',
            'tipo_cuenta' => 'required',
            'banco' => 'required',
            'numero_cuenta' => 'required',
            'fecha_ingreso' => 'required',
            'fecha_reconocimiento' => 'required',
            'tipo_contrato' => 'required',
            'tipo_jornada' => 'required',
            'semana_corrida' => 'required',
            'moneda_sueldo' => 'required',
            'sueldo_base' => 'required',
            'tipo_trabajador' => 'required',
            'gratificacion_mensual' => 'required',
            'gratificacion_anual' => 'required',
            'moneda_colacion' => 'required',
            'colacion' => 'required',
            'moneda_movilizacion' => 'required',
            'movilizacion' => 'required',
            'moneda_viatico' => 'required',
            'viatico' => 'required',
            'afp' => 'required',
            'seguro_desempleo' => 'required',
            'isapre' => 'required',
            'cotizacion_isapre' => 'required',
            'monto_isapre' => 'required',
            'sindicato' => 'required',
            'moneda_sindicato' => 'required',
            'monto_sindicato' => 'required'
            */    
        );

        $message = array(
            'trabajador.required' => 'Obligatorio!'
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
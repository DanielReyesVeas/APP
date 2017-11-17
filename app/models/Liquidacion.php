<?php

class Liquidacion extends Eloquent {
    
    protected $table = 'liquidaciones';
    
    public function detalles(){
        return $this->hasMany('DetalleLiquidacion','liquidacion_id');
    }
    
    public function detalleAfiliadoVoluntario(){
        return $this->hasMany('DetalleAfiliadoVoluntario','liquidacion_id');
    }
    
    public function detalleAfp(){
        return $this->hasOne('DetalleAfp','liquidacion_id');
    }
    
    public function detalleApvc(){
        return $this->hasMany('DetalleApvc','liquidacion_id');
    }
    
    public function detalleApvi(){
        return $this->hasMany('DetalleApvi','liquidacion_id');
    }
    
    public function detalleCaja(){
        return $this->hasOne('DetalleCaja','liquidacion_id');
    }
    
    public function detalleIpsIslFonasa(){
        return $this->hasOne('DetalleIpsIslFonasa','liquidacion_id');
    }
    
    public function detalleMutual(){
        return $this->hasOne('DetalleMutual','liquidacion_id');
    }
    
    public function detalleSalud(){
        return $this->hasOne('DetalleSalud','liquidacion_id');
    }
    
    public function detalleSeguroCesantia(){
        return $this->hasOne('DetalleSeguroCesantia','liquidacion_id');
    }
    
    public function detallePagadorSubsidio(){
        return $this->hasOne('DetallePagadorSubsidio','liquidacion_id');
    }
    
    public function trabajador(){
        return $this->belongsTo('Trabajador','trabajador_id');
    }
  
    public function documento(){
        return $this->belongsTo('Documento', 'documento_id');
    }        
    
    public function cotizacionSalud()
    {
        if($this->cotizacion_salud=='$'){
            return 1;
        }else if($this->cotizacion_salud=='UF'){
            return 2;
        }else{
            return '';
        }
    }
    
    public function cotizacionFonasa()
    {        
        $idSalud = $this->id_salud;
        $montoFonasa = '';
        
        if($idSalud==246){
            $montoFonasa = $this->total_salud;
        }
        
        return $montoFonasa;
    }
    
    public function regimenPrevisional()
    {
        $prevision = $this->prevision_id;                
        
        if($prevision==8){
            $codigo = 'AFP';                        
        }else if($prevision==9){
            $codigo = 'INP';            
        }else{
            $codigo = 'SIP';
        }
        
        return $codigo;
    }
    
    public function miDetalleAfp()
    {
        $detalleAfp = $this->detalleAfp;
        $codigoAfp = '';
        $rentaImponible = 0;
        $cotizacionAfp = 0;
        $sis = 0;
        $cuentaAhorroVoluntario = 0;
        $rentaSustitutiva = 0;
        $tasaSustitutiva = 0;
        $aporteSustitutiva = 0;
        $numeroPeriodos = 0;
        $periodoDesdeSustit = 0;
        $periodoHastaSustit = 0;
        $puestoTrabajoPesado = '';
        $porcentajeTrabajoPesado = 0;
        $cotizacionTrabajoPesado = 0;
        
        if($detalleAfp){
            $codigoAfp = $detalleAfp->afp_id ? $detalleAfp->codigoAfp(1) : '';
            $rentaImponible = $detalleAfp->renta_imponible;
            $cotizacionAfp = $detalleAfp->cotizacion;
            $sis = $detalleAfp->sis;
            $cuentaAhorroVoluntario = $detalleAfp->cuenta_ahorro_voluntario;
            $rentaSustitutiva = $detalleAfp->renta_sustitutiva;
            $tasaSustitutiva = $detalleAfp->tasa_sustitutiva;
            $aporteSustitutiva = $detalleAfp->aporte_sustitutiva;
            $numeroPeriodos = $detalleAfp->numero_periodos;
            $periodoDesdeSustit = $detalleAfp->periodo_desde;
            $periodoHastaSustit = $detalleAfp->periodo_hasta;
            $puestoTrabajoPesado = $detalleAfp->puesto_trabajo_pesado ? $detalleAfp->puesto_trabajo_pesado : '';
            $porcentajeTrabajoPesado = $detalleAfp->porcentaje_trabajo_pesado;
            $cotizacionTrabajoPesado = $detalleAfp->cotizacion_trabajo_pesado;
        }
        
        $datos = array(
            'codigoAfp' => $codigoAfp,
            'rentaImponible' => $rentaImponible,
            'cotizacionAfp' => $cotizacionAfp,
            'sis' => $sis,
            'cuentaAhorroVoluntario' => $cuentaAhorroVoluntario,
            'rentaSustitutiva' => $rentaSustitutiva,
            'tasaSustitutiva' => $tasaSustitutiva,
            'aporteSustitutiva' => $aporteSustitutiva,
            'numeroPeriodos' => $numeroPeriodos,
            'periodoDesdeSustit' => $periodoDesdeSustit,
            'periodoHastaSustit' => $periodoHastaSustit,
            'puestoTrabajoPesado' => $puestoTrabajoPesado,
            'porcentajeTrabajoPesado' => $porcentajeTrabajoPesado,
            'cotizacionTrabajoPesado' => $cotizacionTrabajoPesado
        );
        
        return $datos;
    }
    
    public function miDetalleSalud()
    {
        $detalleSalud = $this->detalleSalud;
        $codigoSalud = '';
        $numeroFun = 0;
        $rentaImponible = 0;
        $moneda = '';
        $cotizacionPactada = 0;
        $cotizacionObligatoria = 0;
        $cotizacionAdicional = 0;
        $ges = 0;
        
        if($detalleSalud){
            $codigoSalud = $detalleSalud->codigoSalud(1);
            $rentaImponible = $detalleSalud->renta_imponible;
            if($codigoSalud!=7){
                $numeroFun = $detalleSalud->numero_fun;
                $moneda = $detalleSalud->moneda ? $detalleSalud->moneda : '';
                $cotizacionPactada = $detalleSalud->cotizacion_pactada;
                $cotizacionObligatoria = $detalleSalud->cotizacion_obligatoria;
                $cotizacionAdicional = $detalleSalud->cotizacion_adicional;
                $ges = $detalleSalud->ges;  
            }
        }
        
        $datos = array(
            'codigoSalud' => $codigoSalud,
            'numeroFun' => $numeroFun,
            'rentaImponible' => $rentaImponible,
            'moneda' => $moneda,
            'cotizacionPactada' => $cotizacionPactada,
            'cotizacionObligatoria' => $cotizacionObligatoria,
            'cotizacionAdicional' => $cotizacionAdicional,
            'ges' => $ges
        );
        
        return $datos;
    }
    
    public function miDetalleCaja()
    {
        $detalleCaja = $this->detalleCaja;
        $codigoCaja = '00';
        $rentaImponible = 0;
        $creditosPersonales = 0;
        $descuentoDental = 0;
        $descuentosLeasing = 0;
        $descuentosSeguro = 0;
        $otrosDescuentos = 0;
        $cotizacion = 0;
        $descuentoCargas = 0;
        $otrosDescuentos1 = 0;
        $otrosDescuentos2 = 0;
        $bonosGobierno = 0;
        $codigoSucursal = '';
        
        if($detalleCaja){
            $codigoCaja = $detalleCaja->codigoCaja(1);
            $rentaImponible = $detalleCaja->renta_imponible;
            $creditosPersonales = $detalleCaja->creditos_personales;
            $descuentoDental = $detalleCaja->descuento_dental;
            $descuentosLeasing = $detalleCaja->descuentos_leasing;
            $descuentosSeguro = $detalleCaja->descuentos_seguro;
            $otrosDescuentos = $detalleCaja->otros_descuentos;
            $cotizacion = $detalleCaja->cotizacion;            
            $descuentoCargas = $detalleCaja->descuentos_cargas;            
            $otrosDescuentos1 = $detalleCaja->otros_descuentos_1;            
            $otrosDescuentos2 = $detalleCaja->otros_descuentos_2;            
            $bonosGobierno = $detalleCaja->bonos_gobierno;            
            $codigoSucursal = $detalleCaja->codigo_sucursal;            
        }
        
        $datos = array(
            'codigoCaja' => $codigoCaja,
            'rentaImponible' => $rentaImponible,
            'creditosPersonales' => $creditosPersonales,
            'descuentoDental' => $descuentoDental,
            'descuentosLeasing' => $descuentosLeasing,
            'descuentosSeguro' => $descuentosSeguro,
            'otrosDescuentos' => $otrosDescuentos,
            'cotizacion' => $cotizacion,
            'descuentoCargas' => $descuentoCargas,
            'otrosDescuentos1' => $otrosDescuentos1,
            'otrosDescuentos2' => $otrosDescuentos2,
            'bonosGobierno' => $bonosGobierno,
            'codigoSucursal' => $codigoSucursal
        );
        
        return $datos;
    }
    
    public function miDetalleMutual()
    {
        $detalleMutual = $this->detalleMutual;
        $codigoMutual = '00';
        $rentaImponible = 0;
        $cotizacionAccidentes = 0;
        $codigoSucursal = '';
        
        if($detalleMutual){
            $codigoMutual = $detalleMutual->codigoMutual(1);
            $rentaImponible = $detalleMutual->renta_imponible;
            $cotizacionAccidentes = $detalleMutual->cotizacion_accidentes;
            $codigoSucursal = $detalleMutual->codigo_sucursal;         
        }
        
        $datos = array(
            'codigoMutual' => $codigoMutual,
            'rentaImponible' => $rentaImponible,
            'cotizacionAccidentes' => $cotizacionAccidentes,
            'codigoSucursal' => $codigoSucursal
        );
        
        return $datos;
    }
    
    public function miDetalleSeguroCesantia()
    {
        $detalleSeguroCesantia = $this->detalleSeguroCesantia;
        $rentaImponible = 0;
        $aporteTrabajador = 0;
        $aporteEmpleador = 0;
        
        if($detalleSeguroCesantia){
            $rentaImponible = $detalleSeguroCesantia->renta_imponible;
            $aporteTrabajador = $detalleSeguroCesantia->aporte_trabajador;
            $aporteEmpleador = $detalleSeguroCesantia->aporte_empleador;         
        }
        
        $datos = array(
            'rentaImponible' => $rentaImponible,
            'aporteTrabajador' => $aporteTrabajador,
            'aporteEmpleador' => $aporteEmpleador
        );
        
        return $datos;
    }
    
    public function miDetallePagadorSubsidio()
    {
        $detallePagadorSubsidio = $this->detallePagadorSubsidio;
        $rut = '';
        $digito = '';
        
        if($detallePagadorSubsidio){
            $rut = $detallePagadorSubsidio->rut;
            $digito = $detallePagadorSubsidio->digito;
        }
        
        $datos = array(
            'rut' => $rut,
            'digito' => $digito
        );
        
        return $datos;
    }
    
    public function miDetalleIpsIslFonasa()
    {
        $detalleIpsIslFonasa = $this->detalleIpsIslFonasa;
        $codigoExCaja = '';
        $tasa = 0;
        $rentaImponible = 0;
        $cotizacionObligatoria = 0;
        $rentaImponibleDesahucio = 0;
        $codigoExCajaDesahucio = 0;
        $tasaDesahucio = 0;
        $cotizacionDesahucio = 0;
        $cotizacionFonasa = 0;
        $cotizacionIsl = 0;
        $bonificacion = 0;
        $descuentoCargasIsl = 0;
        $bonosGobierno = 0;
        
        if($detalleIpsIslFonasa){
            $codigoExCaja = $detalleIpsIslFonasa->ex_caja_id ? $detalleIpsIslFonasa->ex_caja_id : '';
            $tasa = $detalleIpsIslFonasa->tasa_cotizacion;
            $rentaImponible = $detalleIpsIslFonasa->renta_imponible;
            $cotizacionObligatoria = $detalleIpsIslFonasa->cotizacion_obligatoria;
            $rentaImponibleDesahucio = $detalleIpsIslFonasa->renta_imponible_desahucio;
            $codigoExCajaDesahucio = $detalleIpsIslFonasa->ex_caja_desahucio_id;
            $tasaDesahucio = $detalleIpsIslFonasa->tasa_desahucio;
            $cotizacionDesahucio = $detalleIpsIslFonasa->cotizacion_desahucio;
            $cotizacionFonasa = $detalleIpsIslFonasa->cotizacion_fonasa;
            $cotizacionIsl = $detalleIpsIslFonasa->cotizacion_isl;
            $bonificacion = $detalleIpsIslFonasa->bonificacion;
            $descuentoCargasIsl = $detalleIpsIslFonasa->descuento_cargas_isl;
            $bonosGobierno = $detalleIpsIslFonasa->bonos_gobierno;
        }
        
        $datos = array(
            'codigoExCaja' => $codigoExCaja,
            'tasa' => $tasa,
            'rentaImponible' => $rentaImponible,
            'cotizacionObligatoria' => $cotizacionObligatoria,
            'rentaImponibleDesahucio' => $rentaImponibleDesahucio,
            'codigoExCajaDesahucio' => $codigoExCajaDesahucio,
            'tasaDesahucio' => $tasaDesahucio,
            'cotizacionDesahucio' => $cotizacionDesahucio,
            'cotizacionFonasa' => $cotizacionFonasa,
            'cotizacionIsl' => $cotizacionIsl,
            'bonificacion' => $bonificacion,
            'descuentoCargasIsl' => $descuentoCargasIsl,
            'bonosGobierno' => $bonosGobierno
        );
        
        return $datos;
    }
    
    public function miDetalleApvi(&$lineaAdicional)
    {
        $detalle = $this->detalleApvi;
        $codigo = 0;
        $numeroContrato = '';
        $formaPago = 0;
        $cotizacion = 0;
        $cotizacionDepositosConvenidos = 0;

        if(count($detalle)){
            $codigo = $detalle[0]->afp_id;
            $numeroContrato = $detalle[0]->numero_contrato ? $detalle->numero_contrato : '';
            $formaPago = $detalle[0]->forma_pago_id;
            $cotizacion = $detalle[0]->cotizacion;
            $cotizacionDepositosConvenidos = $detalle[0]->cotizacion_depositos_convenidos;
            if(count($detalle)>1){
                $lineaAdicional = true;
            }
        }
        
        $datos = array(
            'codigo' => $codigo,
            'numeroContrato' => $numeroContrato,
            'formaPago' => $formaPago,
            'cotizacion' => $cotizacion,
            'cotizacionDepositosConvenidos' => $cotizacionDepositosConvenidos
        );
        
        return $datos;
    }
    
    public function miDetalleApvc(&$lineaAdicional)
    {
        $detalle = $this->detalleApvc;
        $codigo = 0;
        $numeroContrato = '';
        $formaPago = 0;
        $cotizacionTrabajador = 0;
        $cotizacionEmpleador = 0;

        if(count($detalle)){
            $codigo = $detalle[0]->afp_id;
            $numeroContrato = $detalle[0]->numero_contrato;
            $formaPago = $detalle[0]->forma_pago_id;
            $cotizacionTrabajador = $detalle[0]->cotizacion_trabajador;
            $cotizacionEmpleador = $detalle[0]->cotizacion_empleador;
            if(count($detalle)>1){
                $lineaAdicional = true;
            }
        }
        
        $datos = array(
            'codigo' => $codigo,
            'numeroContrato' => $numeroContrato,
            'formaPago' => $formaPago,
            'cotizacionTrabajador' => $cotizacionTrabajador,
            'cotizacionEmpleador' => $cotizacionEmpleador
        );
        
        return $datos;
    }
    
    public function detallesLiquidacion($bd)
    {
        $detalles = $this->detalles;
        $detalleAfp = $this->detalleAfp;
        $detalleSeguroCesantia = $this->detalleSeguroCesantia;
        $detalleIpsIslFonasa = $this->detalleIpsIslFonasa;
        $listaHaberes = array();
        $listaDescuentos = array();
        
        if($detalleAfp){
            Config::set('database.default', 'principal' );
            $afp = DB::table('tipos_estructura_glosa')->where('id', $detalleAfp->afp_id)->first();
            $idAfp = (int) $detalleAfp->afp_id;
            Config::set('database.default', $bd );
            $ap = DB::table('aportes_cuentas')->where('nombre', 40)->where('tipo_aporte', 4)->first();
            $listaDescuentos[] = array(
                'nombre' => 'AFP ' . $afp->glosa,
                'monto' => $detalleAfp->cotizacion,
                'idCuenta' => $ap->cuenta_id
            );
        }
        
        if($detalleSeguroCesantia){
            Config::set('database.default', $bd );
            $seguro = DB::table('aportes_cuentas')->where('id', 3)->first();
            $listaDescuentos[] = array(
                'nombre' => 'Seguro Cesantía',
                'monto' => $detalleSeguroCesantia->aporte_trabajador,
                'idCuenta' => $seguro->cuenta_id
            );
        }
        
        if($detalleIpsIslFonasa){
            Config::set('database.default', $bd );
            $fonasa = DB::table('aportes_cuentas')->where('id', 2)->first();
            $listaDescuentos[] = array(
                'nombre' => 'Fonasa',
                'monto' => $detalleIpsIslFonasa->cotizacion_fonasa,
                'idCuenta' => $fonasa->cuenta_id
            );
        }
        
        if($detalles->count()){
            foreach($detalles as $detalle){
                if($detalle->tipo_id==1){
                    $listaHaberes[] = array(
                        'nombre' => $detalle->nombre,
                        'monto' => $detalle->valor,
                        'idCuenta' => $detalle->cuenta_id
                    );
                }else if($detalle->tipo_id==1){
                    $listaDescuentos[] = array(
                        'nombre' => $detalle->nombre,
                        'monto' => $detalle->valor,
                        'idCuenta' => $detalle->cuenta_id
                    );                  
                }
            }
        }
        
        /*$aportes = Aporte::aportes();
        $ap = array();
        $ap[] = array(
            'nombre' => 'Caja de Compensación',
            'monto' => 54125,
            'idCuenta' => 43
        );
         $ap[] = array(
            'nombre' => 'Fonasa',
            'monto' => 45001,
            'idCuenta' => 49
        );
         $ap[] = array(
            'nombre' => 'Mutual',
            'monto' => 512,
            'idCuenta' => 43
        );
         $ap[] = array(
            'nombre' => 'SIS AFP',
            'monto' => 55105,
            'idCuenta' => 42
        );*/
        
        $datos = array(
            'haberes' => $listaHaberes,
            'descuentos' => $listaDescuentos,
            'aportes' => $ap
        );
        
        return $datos;
    }
    
    static function comprobarCuentas($liquidaciones)
    {
        $listaAportes = array();
        $listaAfps = array();
        $listaExCajas = array();
        $listaApvs = array();
        $listaApvcs = array();
        $listaCCAF = array();
        $listaSalud = array();
        $listaHaberes = array();
        $listaDescuentos = array();
        if($liquidaciones->count()){
            foreach($liquidaciones as $liquidacion){
                $detalleAfp = $liquidacion->detalleAfp;
                if($detalleAfp){
                    $listaAfps[] = $detalleAfp->afp_id;
                }
                $detallesAPVC = $liquidacion->detalleApvc;
                if($detallesAPVC->count()){
                    foreach($detallesAPVC as $detalleAPVC){
                        $listaApvcs[] = $detalleAPVC->afp_id;   
                    }
                }
                $detallesAPVI = $liquidacion->detalleApvi;
                if($detallesAPVI->count()){
                    foreach($detallesAPVI as $detalleAPVI){
                        $listaApvs[] = $detalleAPVI->afp_id;   
                    }
                }
                $detalleCaja = $liquidacion->detalleCaja;
                if($detalleCaja){
                    $listaCCAF[] = $detalleCaja->caja_id;   
                }
                $detalleIpsIslFonasa = $liquidacion->detalleIpsIslFonasa;
                if($detalleIpsIslFonasa){
                    if($detalleIpsIslFonasa->cotizacion_fonasa>0){
                        $listaAportes[] = $detalleIpsIslFonasa->cotizacion_fonasa;   
                    }
                    if($detalleIpsIslFonasa->tasa_cotizacion>0){
                        $listaExCajas[] = $detalleIpsIslFonasa->ex_caja_id;   
                    }
                    if($detalleIpsIslFonasa->cotizacion_isl>0){
                        $listaAportes[] = $detalleIpsIslFonasa->cotizacion_isl;   
                    }
                }
                $detalleSalud = $liquidacion->detalleSalud;
                if($detalleSalud){
                    $listaSalud[] = $detalleSalud->salud_id;   
                }
                $detalleSeguroCesantia = $liquidacion->detalleSeguroCesantia;
                if($detalleSeguroCesantia){
                    if($detalleSeguroCesantia->aporte_empleador>0){
                        $listaAportes[] = $detalleSeguroCesantia->aporte_empleador; 
                    }
                    if($detalleSeguroCesantia->aporte_trabajador>0){
                        $listaAportes[] = $detalleSeguroCesantia->aporte_trabajador; 
                    }
                }
                $detalles = $liquidacion->detalles;
                if($detalles->count()){
                    foreach($detalles as $detalle){
                        if($detalle->tipo_id==1){
                            $listaHaberes[] = $detalle->detalle_id;   
                        }else if($detalle->tipo_id==2){
                            $listaDescuentos[] = $detalle->detalle_id;   
                        }
                    }
                }
                if($liquidacion->total_horas_extra>0){
                    $listaHaberes[] = 7; 
                }
            }   
        }
        $datos = array(
            'aportes' => $listaAportes,
            'afps' => $listaAfps,
            'exCajas' => $listaExCajas,
            'apvs' => $listaApvs,
            'apvcs' => $listaApvcs,
            'ccaf' => $listaCCAF,
            'salud' => $listaSalud,
            'haberes' => $listaHaberes,
            'descuentos' => $listaDescuentos
        );

        return $datos;    
    }
    
    static function remuneracionAnualDevengada()
    {
        $sum = 0;
        $mesActual = \Session::get('mesActivo')->mes;
        $mesAnterior = date('Y-m-d', strtotime('-' . 1 . ' year', strtotime($mesActual)));
        $liquidaciones = Liquidacion::whereBetween('mes', [$mesAnterior, $mesActual])->get();
        if($liquidaciones->count()){
            $sum = $liquidaciones->sum('sueldo_liquido');
        }
        
        return $sum;
    }
    
    public function misHaberes()
    {
        $idLiquidacion = $this->id;
        $listaHaberes = array();
        $misHaberes = DetalleLiquidacion::where('liquidacion_id', $idLiquidacion)->where('tipo_id', 1)->get();
        
        if( $misHaberes->count() ){
            foreach($misHaberes as $haber){
                $listaHaberes[] = array(
                    'id' => $haber->id,
                    'sid' => $haber->sid,
                    'moneda' => $haber->valor_3,
                    'monto' => $haber->valor_2,
                    'montoPesos' => $haber->valor,
                    'tipo' => array(
                        'nombre' => $haber->nombre,
                        'imponible' => $haber->tipo ? true : false
                    )
                );
            }
        }
        
        return $listaHaberes;
    }
    
    public function apv($recaudador=null)
    {
        $idLiquidacion = $this->id;
        $apv = DetalleLiquidacion::where('liquidacion_id', $idLiquidacion)->where('tipo_id', 3)->first();
        $codigoAfp = '';
        $contrato = '';
        $formaPago = '';
        $monto = '';
        $depositosConvenidos = '';        
        
        if($apv){
            if(!$recaudador){
                $recaudador = 1;
            }
            $idAfp = $apv->valor_4;
            $idFormaPago = $apv->valor_5;
            if($idAfp!=0){
                $codigoAfp = Glosa::find($idAfp)->codigo($recaudador)['codigo'];
                $formaPago = Glosa::find($idFormaPago)->codigo($recaudador)['codigo'];
            }
            $contrato = '1';
            $monto = $apv->valor;
            $depositosConvenidos = '1';
        }        
        
        $detallesApv = array(
            'codigo' => $codigoAfp,
            'contrato' => $contrato,
            'formaPago' => $formaPago,
            'monto' => $monto,
            'depositosConvenidos' => $depositosConvenidos
        );
        
        return $detallesApv;
    }
    
    public function apvc($recaudador=null)
    {
        $idLiquidacion = $this->id;
        $apvc = DetalleLiquidacion::where('liquidacion_id', $idLiquidacion)->where('valor_4', 2)->first();
        $codigoAfp = '';
        $contrato = '';
        $formaPago = '';
        $montoTrabajador = '';
        $montoEmpleador = '';
        
        if($apvc){
            if(!$recaudador){
                $recaudador = 1;
            }
            $idAfp = $apvc->valor_6;
            $idFormaPago = $apvc->valor_5;
            
            $codigoAfp = Glosa::find($idAfp)->codigo($recaudador)['codigo'];
            $formaPago = Glosa::find($idFormaPago)->codigo($recaudador)['codigo'];
            
            $contrato = '65461';
            $montoTrabajador = $apvc->valor;
            $montoEmpleador = 0;
        }        
        
        $detallesApvc = array(
            'codigo' => $codigoAfp,
            'contrato' => $contrato,
            'formaPago' => $formaPago,
            'montoTrabajador' => $montoTrabajador,
            'montoEmpleador' => $montoEmpleador
        );
        
        return $detallesApvc;
    }
    
    public function misPrestamos()
    {
        $idLiquidacion = $this->id;
        $listaPrestamos = array();
        $misPrestamos = DetalleLiquidacion::where('liquidacion_id', $idLiquidacion)->where('tipo_id', 4)->get();
        
        if( $misPrestamos->count() ){
            foreach($misPrestamos as $prestamo){
                $listaPrestamos[] = array(
                    'moneda' => $prestamo->valor_3,
                    'monto' => $prestamo->valor_2,
                    'nombreLiquidacion' => $prestamo->nombre,
                    'cuotas' => $prestamo->valor_5,
                    'cuotaPagar' => array(
                        'monto' => $prestamo->valor,
                        'numero' => $prestamo->valor_4
                    )
                );
            }
        }
        
        return $listaPrestamos;
    }
    
    public function misApvs()
    {
        $idLiquidacion = $this->id;
        $listaApvs = array();
        $misApvs = DetalleLiquidacion::where('liquidacion_id', $idLiquidacion)->where('tipo_id', 3)->get();
        
        if( $misApvs->count() ){
            foreach($misApvs as $apv){
                $listaApvs[] = array(
                    'moneda' => $apv->valor_3,
                    'monto' => $apv->valor_2,
                    'montoPesos' => $apv->valor,
                    'afp' => array(
                        'id' => $apv->valor_4,
                        'nombre' => $apv->nombre
                    )
                );
            }
        }
        
        return $listaApvs;
    }
    
    public function totalApvs()
    {
        $totalApvs = 0;
        $apvs = $this->detalleApvi;
        
        if($apvs){
            foreach($apvs as $apv){
                $totalApvs += ($apv['aporte_trabajador'] + $apv['aporte_empleador']);
            }
        }
        
        return $totalApvs;
    }
    
    public function totalSalud()
    {
        $totalSalud = 0;
        $fonasa = $this->detalleIpsIslFonasa;
        
        if($fonasa->cotizacion_fonasa > 0){
            $totalSalud = $fonasa->cotizacion_fonasa;
        }else{
            $totalSalud = $this->detalleSalud->cotizacion_obligatoria;
        }
        
        return $totalSalud;
    }
    
    public function totalAnticipos()
    {
        $totalAnticipos = 0;
        $descuentos = $this->misDescuentos();
        
        if($descuentos){
            foreach($descuentos as $descuento){
                if($descuento['anticipo']=='1'){
                    $totalAnticipos += $descuento['montoPesos'];
                }
            }
        }
        
        return $totalAnticipos;
    }
    
    public function misCargas()
    {
        $cantidad = $this->cantidad_cargas;
        $monto = $this->total_cargas;
        $isCargas = false;
        
        if($cantidad>0){
            $isCargas = true;
        }
        
        $cargasFamiliares = array(
            'cantidad' => $cantidad,
            'monto' => $monto,
            'isCargas' => $isCargas
        );
        
        return $cargasFamiliares;   
    }
    
    public function misDescuentos()
    {        
        $idLiquidacion = $this->id;
        $listaDescuentos = array();
        $misDescuentos = DetalleLiquidacion::where('liquidacion_id', $idLiquidacion)->where('tipo_id', 2)->get();
        
        if( $misDescuentos->count() ){
            foreach($misDescuentos as $descuento){
                $listaDescuentos[] = array(
                    'id' => $descuento->id,
                    'sid' => $descuento->sid,
                    'moneda' => $descuento->valor_3,
                    'monto' => $descuento->valor_2,
                    'montoPesos' => $descuento->valor,
                    'anticipo' => $descuento->valor_4,
                    'tipo' => array(
                        'nombre' => $descuento->nombre
                    ) 
                );
            }
        }
        
        return $listaDescuentos;
    }
    
    public function inasistenciasAtrasos()
    {
        $diasTrabajados = $this->dias_trabajados;
        $inasistenciasAtrasos = (30 - $diasTrabajados);
        
        return $inasistenciasAtrasos;
    }
    
    public function totalHaberes()
    {
        $imponibles = $this->imponibles;
        $noImponibles = $this->no_imponibles;
        $totalHaberes = ($imponibles + $noImponibles);
        
        return $totalHaberes;
    }
    
    static function errores($datos){
         
        $rules = array(

        );

        $message = array(
            'liquidacion.required' => 'Obligatorio!'
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

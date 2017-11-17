<?php
class TrabajadoresController extends \BaseController {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    
    public function descargarLibroExcel($name)
    {
        $destination = public_path() . '/stories/' . $name . '.xls';
        return Response::make(file_get_contents($destination), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="Libro.xls"'
        ]);    
    }        
    
    public function descargarNominaExcel($name)
    {
        $destination = public_path() . '/stories/' . $name . '.xls';
        return Response::make(file_get_contents($destination), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="Nomina.xls"'
        ]);    
    }
    
    public function descargarPlanillaExcel($name)
    {
        $destination = public_path() . '/stories/' . $name . '.xls';
        return Response::make(file_get_contents($destination), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="PlanillaCostoEmpresa.xls"'
        ]);    
    }
    
    public function generarLibroExcel()
    {
        $datos = Input::all();
        $trabajadores = (array) $datos['trabajadores'];
        $conceptos = $datos['conceptos'];
        $libroRemuneraciones = array();
        $empresa = \Session::get('empresa');
        $empresa->domicilio = $empresa->domicilio();
        $empresa->rutFormato = $empresa->rut_formato();
        
        $sumaSueldoBase = 0;
        $sumaInasistenciasAtrasos = 0;
        $sumaDiasTrabajados = 0;
        $sumaHorasExtra = 0;
        $sumaSueldo = 0;
        $sumaSalud = 0;
        $sumaAfp = 0;
        $sumaApv = 0;
        $sumaGratificacion = 0;
        $sumaMutual = 0;
        $sumaImpuestoRenta = 0;
        $sumaAnticipos = 0;
        $sumaAsignacionFamiliar = 0;
        $sumaNoImponibles = 0;
        $sumaHaberes = 0;
        $sumaImponibles = 0;
        $sumaTotalImponibles = 0;
        $sumaSeguroCesantia = 0;
        $sumaTotalDescuentos = 0;
        $sumaOtrosDescuentos = 0;
        $sumaSueldoLiquido = 0;
        
        $mes = \Session::get('mesActivo');
        $liquidaciones = Liquidacion::where('mes', $mes->mes)->get();
        
        
        foreach($liquidaciones as $liquidacion){            
            if(in_array($liquidacion->trabajador_id, $trabajadores)){
                $sis = 0;
                if($liquidacion->detalleIpsIslFonasa->cotizacion_fonasa>0){
                    $cotizacionSalud = $liquidacion->detalleIpsIslFonasa->cotizacion_fonasa;
                }else{
                    $cotizacionSalud = ($liquidacion->detalleSalud->cotizacion_obligatoria + $liquidacion->detalleSalud->cotizacion_adicional);
                }
                $liquidacion->totalApvs = $liquidacion->totalApvs();
                $liquidacion->totalSalud = $cotizacionSalud;
                if(!$empresa->sis){
                    $sis = $liquidacion->detalleAfp->sis;
                }
                $liquidacion->totalAfp = ($liquidacion->detalleAfp->cotizacion + $sis);
                $liquidacion->totalSeguroCesantia = $liquidacion->detalleSeguroCesantia ? $liquidacion->detalleSeguroCesantia->aporte_trabajador : 0;
                
                $libroRemuneraciones[] = $liquidacion;
                
                $sumaSueldoBase += $liquidacion->sueldo_base;
                $sumaDiasTrabajados += $liquidacion->dias_trabajados;
                $sumaInasistenciasAtrasos += $liquidacion->inasistencias;
                $sumaHorasExtra += $liquidacion->horas_extra;
                $sumaSueldo += $liquidacion->sueldo;
                $sumaSalud += $liquidacion->totalSalud;
                $sumaAfp += ($liquidacion->detalleAfp->cotizacion + $liquidacion->detalleAfp->sis);
                $sumaApv += $liquidacion->totalApvs();
                $sumaGratificacion += $liquidacion->gratificacion;
                $sumaMutual += $liquidacion->total_mutual;
                $sumaImpuestoRenta += $liquidacion->impuesto_determinado;
                $sumaAnticipos += $liquidacion->total_anticipos;
                $sumaAsignacionFamiliar += $liquidacion->total_cargas;
                $sumaNoImponibles += $liquidacion->no_imponibles;
                $sumaHaberes += $liquidacion->total_haberes;
                $sumaImponibles += $liquidacion->imponibles;
                $sumaTotalImponibles += $liquidacion->renta_imponible;
                $sumaSeguroCesantia += $liquidacion->detalleSeguroCesantia ? ($liquidacion->detalleSeguroCesantia->aporte_trabajador + $liquidacion->detalleSeguroCesantia->aporte_empleador) : 0;;
                $sumaTotalDescuentos += $liquidacion->total_descuentos;
                $sumaOtrosDescuentos += $liquidacion->total_otros_descuentos;
                $sumaSueldoLiquido += $liquidacion->sueldo_liquido;
            }
        }
        $totales = array(
            'totalSueldoBase' => $sumaSueldoBase,
            //'totalMutual' => $sumaMutual,
            'totalDiasTrabajados' => $sumaDiasTrabajados,
            'totalInasistenciasAtrasos' => $sumaInasistenciasAtrasos,
            'totalHorasExtra' => $sumaHorasExtra,
            'totalSueldo' => $sumaSueldo,
            'totalSalud' => $sumaSalud,
            'totalAfp' => $sumaAfp,
            'totalApv' => $sumaApv,
            'totalGratificacion' => $sumaGratificacion,
            'totalImpuestoRenta' => $sumaImpuestoRenta,
            'totalAnticipos' => $sumaAnticipos,
            'totalAsignacionFamiliar' => $sumaAsignacionFamiliar,
            'totalSeguroCesantia' => $sumaSeguroCesantia,
            'totalNoImponibles' => $sumaNoImponibles,
            'totalHaberes' => $sumaHaberes,
            'totalImponibles' => $sumaImponibles,
            'totalTotalImponibles' => $sumaTotalImponibles,
            'totalOtrosDescuentos' => $sumaOtrosDescuentos,
            'totalTotalDescuentos' => $sumaTotalDescuentos,
            'totalSueldoLiquido' => $sumaSueldoLiquido
        );
        
        
        
        $datos = new stdClass();
        $datos->liquidaciones = $libroRemuneraciones;
        $datos->conceptos = $conceptos;
        $datos->totales = $totales;
        $datos->empresa = $empresa;
        $datos->mes = strtoupper($mes->mesActivo);
        
        $filename = date("d-m-Y-H-i-s") . "_Libro_" . $mes->nombre . "_" . $mes->anio . ".xls";
        
        Excel::create("Libro", function($excel) use($datos) {
            $excel->sheet("Libro", function($sheet) use($datos) {
                $sheet->loadView('excel.libro')->with('datos', $datos);
            });
        })->store('xls', public_path('stories'));
        
        $destination = public_path('stories/' . $filename);

        
        $datos = array(
            'success' => true,
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $filename,
            'nombre' => 'Libro',
            'libro' => $libroRemuneraciones
        );
        
        return Response::json($datos);
    }
    
    public function generarNominaExcel()
    {
        $datos = Input::all();
        $ids = (array) $datos['trabajadores'];
        $trabajadores = array();
        $mes = \Session::get('mesActivo');
        
        foreach($ids as $id){
            $trabajador = Trabajador::find($id);
            $empleado = $trabajador->ficha();
            $liquidacion = Liquidacion::where('trabajador_id', $trabajador->id)->where('mes', $mes->mes)->first();
            $trabajadores[] = array(
                'rut' => $trabajador->rut_formato(),
                'nombreCompleto' => $empleado->nombreCompleto(),
                'cargo' => $empleado->cargo->nombre,
                'codigoBanco' => $empleado->banco ? $empleado->banco->codigo : "",
                'tipoCuenta' => $empleado->tipoCuenta ? $empleado->tipoCuenta->nombre : "",
                'numeroCuenta' => $empleado->numero_cuenta ? $empleado->numero_cuenta : "",
                'monto' => $liquidacion->sueldo_liquido
            );
        }
        
        $filename = date("d-m-Y-H-i-s") . "_Nomina_" . $mes->nombre . "_" . $mes->anio . ".xls";
        
        Excel::create("Nomina", function($excel) use($trabajadores) {
            $excel->sheet("Nomina", function($sheet) use($trabajadores) {
                $sheet->loadView('excel.nomina')->with('trabajadores', $trabajadores)->getStyle('A1')->getAlignment();
            });
        })->store('xls', public_path('stories'));
        
        $destination = public_path('stories/' . $filename);
        
        $datos = array(
            'success' => true,
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $filename,
            'nombre' => 'Nomina',
            'nomina' => $trabajadores
        );
        
        return Response::json($datos);
    }
    
    /*public function generarNominaExcel()
    {
        $mes = \Session::get('mesActivo');
        
        $comunas = Glosa::where('tipo_estructura_id', 18)->orderBy('id', 'ASC')->get();
        $datos = array();
        foreach($comunas as $comuna){
            $datos[] = array(
                'id' => $comuna->id,
                'nombre' => $comuna->glosa
            );
        }
        
        $filename = date("d-m-Y-H-i-s") . "_Nomina_" . $mes->nombre . "_" . $mes->anio . ".xls";
        
        Excel::create("Nomina", function($excel) use($datos) {
            $excel->sheet("Nomina", function($sheet) use($datos) {
                $sheet->loadView('excel.comuna')->with('datos', $datos)->getStyle('A1')->getAlignment();
            });
        })->store('xls', public_path('stories'));
        
        $destination = public_path('stories/' . $filename);
        
        $datos = array(
            'success' => true,
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $filename,
            'nombre' => 'Nomina',
            'nomina' => $datos
        );
        
        return Response::json($datos);
    }*/
    
    public function descargarPlantillaTrabajadores(){
        
        $listaActivos = array();
        
        $destination = public_path('planillas/trabajadores.xlsx');
        
        Excel::load($destination, function($reader) {
            $reader->sheet('Códigos', function($sheet) {
                //Nacionalidades
                $i = 1;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 1'); 
                $i++;       
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Nacionalidades');
                $i++;                
                $nacionalidades = Glosa::codigosNacionalidades();
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');
                $i++;                 
                foreach($nacionalidades as $nacionalidad){                
                    $sheet->setCellValue('A'.$i, $nacionalidad['codigo']);
                    $sheet->setCellValue('B'.$i, $nacionalidad['glosa']);
                    $i++;
                }
                
                //Sexos
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 2'); 
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Sexos');
                $i++;                
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');                
                $i++;  
                $sheet->setCellValue('A'.$i, 'F');
                $sheet->setCellValue('B'.$i, 'Femenino');
                $i++;  
                $sheet->setCellValue('A'.$i, 'M');
                $sheet->setCellValue('B'.$i, 'Masculino');
                $i++;
                
                //Estados Civiles
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 3'); 
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Estados Civiles');
                $i++;                
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');
                $i++;  
                $sheet->setCellValue('A'.$i, 1);
                $sheet->setCellValue('B'.$i, 'Soltero/a');
                $i++;  
                $sheet->setCellValue('A'.$i, 2);
                $sheet->setCellValue('B'.$i, 'Casado/a');
                $i++;  
                $sheet->setCellValue('A'.$i, 3);
                $sheet->setCellValue('B'.$i, 'Divorciado/a');
                $i++;  
                $sheet->setCellValue('A'.$i, 4);
                $sheet->setCellValue('B'.$i, 'Viudo/a');
                $i++;  
                
                //Tipos Empleado
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 4'); 
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Tipos de Empleado');
                $tipos = Glosa::codigosTiposEmpleado();
                $i++;                
                foreach($tipos as $tipo){                
                    $sheet->setCellValue('A'.$i, $tipo['codigo']);
                    $sheet->setCellValue('B'.$i, $tipo['glosa']);
                    $i++;
                }
                
                //Cargos
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 5'); 
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Cargos');
                $cargos = Cargo::codigosCargos();
                $i++;                
                foreach($cargos as $cargo){                
                    $sheet->setCellValue('A'.$i, $cargo['codigo']);
                    $sheet->setCellValue('B'.$i, $cargo['glosa']);
                    $i++;
                }
                
                //Títulos
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 6'); 
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Títulos');
                $titulos = Titulo::codigosTitulos();
                $i++;                
                foreach($titulos as $titulo){                
                    $sheet->setCellValue('A'.$i, $titulo['codigo']);
                    $sheet->setCellValue('B'.$i, $titulo['glosa']);
                    $i++;
                }
                
                //Secciones
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 7'); 
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Secciones');
                $secciones = Seccion::codigosSecciones();
                $i++;                
                foreach($secciones as $seccion){                
                    $sheet->setCellValue('A'.$i, $seccion['codigo']);
                    $sheet->setCellValue('B'.$i, $seccion['glosa']);
                    $i++;
                }
                
                //Tipos de Cuenta
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 8'); 
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Tipos de Cuenta');
                $i++;                
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');
                $i++;  
                $sheet->setCellValue('A'.$i, 1);
                $sheet->setCellValue('B'.$i, 'Cuenta Corriente');
                $i++;  
                $sheet->setCellValue('A'.$i, 2);
                $sheet->setCellValue('B'.$i, 'Cuenta Vista');
                $i++;  
                $sheet->setCellValue('A'.$i, 3);
                $sheet->setCellValue('B'.$i, 'Cuenta Ahorro');
                $i++;  
                $sheet->setCellValue('A'.$i, 4);
                $sheet->setCellValue('B'.$i, 'CuentaRut');
                $i++;  
                $sheet->setCellValue('A'.$i, 5);
                $sheet->setCellValue('B'.$i, 'Chequera Electróica');
                $i++;  
                $sheet->setCellValue('A'.$i, 6);
                $sheet->setCellValue('B'.$i, 'Cuenta Gastos');
                $i++;  
                
                //Bancos
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 9'); 
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Bancos');
                $i++;                
                $bancos = Banco::codigosBancos();
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');
                $i++;                 
                foreach($bancos as $banco){                
                    $sheet->setCellValue('A'.$i, $banco['codigo']);
                    $sheet->setCellValue('B'.$i, $banco['glosa']);
                    $i++;
                }
                
                //Tipos de Contrato
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 10');
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Tipos de Contrato');
                $i++;                
                $contratos = TipoContrato::codigosTiposContrato();
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');
                $i++;                 
                foreach($contratos as $contrato){                
                    $sheet->setCellValue('A'.$i, $contrato['codigo']);
                    $sheet->setCellValue('B'.$i, $contrato['glosa']);
                    $i++;
                }
                
                //Tipos de Jornada
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 11');
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Tipos de Jornada');
                $i++;                
                $jornadas = Jornada::codigosTiposJornada();
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');
                $i++;                 
                foreach($jornadas as $jornada){                
                    $sheet->setCellValue('A'.$i, $jornada['codigo']);
                    $sheet->setCellValue('B'.$i, $jornada['glosa']);
                    $i++;
                }
                
                //Semana Corrida
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 12');
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Semana Corrida');
                $i++;                
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');
                $i++;  
                $sheet->setCellValue('A'.$i, 0);
                $sheet->setCellValue('B'.$i, 'No');
                $i++;  
                $sheet->setCellValue('A'.$i, 1);
                $sheet->setCellValue('B'.$i, 'Sí');
                $i++;
                
                //Tipos de Moneda
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 13');
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Tipos de Moneda');
                $i++;                
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');
                $i++;  
                $sheet->setCellValue('A'.$i, '$');
                $sheet->setCellValue('B'.$i, 'Pesos');
                $i++;  
                $sheet->setCellValue('A'.$i, 'UF');
                $sheet->setCellValue('B'.$i, 'UF');
                $i++;  
                $sheet->setCellValue('A'.$i, 'UTM');
                $sheet->setCellValue('B'.$i, 'UTM');
                $i++;
                
                //Tipos de Trabajador
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 14');
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Tipos de Trabajador');
                $i++;                
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');
                $i++;  
                $sheet->setCellValue('A'.$i, 'Normal');
                $sheet->setCellValue('B'.$i, 'Normal');
                $i++;  
                $sheet->setCellValue('A'.$i, 'Socio');
                $sheet->setCellValue('B'.$i, 'Socio');
                $i++;
                
                //Exceso de Retiro
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 15');
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Exceso de Retiro');
                $i++;                
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');
                $i++;  
                $sheet->setCellValue('A'.$i, 0);
                $sheet->setCellValue('B'.$i, 'No');
                $i++;  
                $sheet->setCellValue('A'.$i, 1);
                $sheet->setCellValue('B'.$i, 'Sí');
                $i++;
                
                //Proporcional Colación, Movilización y Viático
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 16');
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Proporcional Colación, Movilización y Viático');
                $i++;                
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');
                $i++;  
                $sheet->setCellValue('A'.$i, 0);
                $sheet->setCellValue('B'.$i, 'No');
                $i++;  
                $sheet->setCellValue('A'.$i, 1);
                $sheet->setCellValue('B'.$i, 'Sí');
                $i++;
                
                //Previsión
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 17');
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Previsión');
                $previsiones = Glosa::codigosPrevision();
                $i++;                
                foreach($previsiones as $prevision){                
                    $sheet->setCellValue('A'.$i, $prevision['codigo']);
                    $sheet->setCellValue('B'.$i, $prevision['glosa']);
                    $i++;
                }
                
                //AFPs
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 18');
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de AFPs');
                $afps = Glosa::codigosAfps();
                $i++;                
                foreach($afps as $afp){                
                    $sheet->setCellValue('A'.$i, $afp['codigo']);
                    $sheet->setCellValue('B'.$i, $afp['glosa']);
                    $i++;
                }
                
                //ExCajas
                $i=1;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Tabla N° 19');
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Códigos de Ex Cajas');
                $exCajas = Glosa::codigosExCajas();
                $i++;                
                foreach($exCajas as $exCaja){                
                    $sheet->setCellValue('D'.$i, $exCaja['codigo']);
                    $sheet->setCellValue('E'.$i, $exCaja['glosa']);
                    $i++;
                }
            
                //Seguro de Cesantía
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Tabla N° 20');
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Códigos de Seguro de Cesantía');
                $i++;                
                $sheet->setCellValue('D'.$i, 'Código');
                $sheet->setCellValue('E'.$i, 'Glosa');
                $i++;  
                $sheet->setCellValue('D'.$i, 0);
                $sheet->setCellValue('E'.$i, 'No');
                $i++;  
                $sheet->setCellValue('D'.$i, 1);
                $sheet->setCellValue('E'.$i, 'Sí');
                $i++;
                
                //AFPs Seguro de Cesantía
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Tabla N° 21');
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Códigos de AFPs Seguro de Cesantía');
                $afps = Glosa::codigosAfpsSeguro();
                $i++;                
                foreach($afps as $afp){                
                    $sheet->setCellValue('D'.$i, $afp['codigo']);
                    $sheet->setCellValue('E'.$i, $afp['glosa']);
                    $i++;
                }
                
                //Isapres
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Tabla N° 22');
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Códigos de Isapres');
                $isapres = Glosa::codigosIsapres();
                $i++;                
                foreach($isapres as $isapre){                
                    $sheet->setCellValue('D'.$i, $isapre['codigo']);
                    $sheet->setCellValue('E'.$i, $isapre['glosa']);
                    $i++;
                }
                
                //Cotización Isapre
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Tabla N° 23');
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Códigos de Cotización Isapre (Sólo si es Isapre, no Fonasa)');
                $i++;                
                $sheet->setCellValue('D'.$i, 'Código');
                $sheet->setCellValue('E'.$i, 'Glosa');
                $i++;  
                $sheet->setCellValue('D'.$i, '$');
                $sheet->setCellValue('E'.$i, 'Pesos');
                $i++;  
                $sheet->setCellValue('D'.$i, 'UF');
                $sheet->setCellValue('E'.$i, 'UF');
                $i++;
                
                //Sindicato
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Tabla N° 24');
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Códigos de Sindicato');
                $i++;                
                $sheet->setCellValue('D'.$i, 'Código');
                $sheet->setCellValue('E'.$i, 'Glosa');
                $i++;  
                $sheet->setCellValue('D'.$i, 0);
                $sheet->setCellValue('E'.$i, 'No');
                $i++;  
                $sheet->setCellValue('D'.$i, 1);
                $sheet->setCellValue('E'.$i, 'Sí');
                $i++;
                
                //Tramos Asignación Familiar
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Tabla N° 25');
                $i++;
                $sheet->mergeCells('D'.$i.':E'.$i);
                $sheet->setCellValue('D'.$i, 'Códigos de Tramo Asignación Familiar');
                $i++;                
                $sheet->setCellValue('D'.$i, 'Código');
                $sheet->setCellValue('E'.$i, 'Glosa');
                $i++;  
                $sheet->setCellValue('D'.$i, 'a');
                $sheet->setCellValue('E'.$i, 'Primer Tramo');
                $i++;
                $sheet->setCellValue('D'.$i, 'b');
                $sheet->setCellValue('E'.$i, 'Segundo Tramo');
                $i++;
                $sheet->setCellValue('D'.$i, 'c');
                $sheet->setCellValue('E'.$i, 'Tercer Tramo');
                $i++;
                $sheet->setCellValue('D'.$i, 'd');
                $sheet->setCellValue('E'.$i, 'Sin Derecho');
                $i++;       
                                     
            });  
            
            
            $reader->sheet('Comunas', function($sheet) {
                //Comunas
                $i = 1;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Tabla N° 26');                
                $i++;
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->setCellValue('A'.$i, 'Códigos de Comunas');
                $comunas = Comuna::codigosComunas();
                $i++;
                $sheet->setCellValue('A'.$i, 'Código');
                $sheet->setCellValue('B'.$i, 'Glosa');
                $i++;                
                foreach($comunas as $comuna){                
                    $sheet->setCellValue('A'.$i, $comuna['id']);
                    $sheet->setCellValue('B'.$i, $comuna['glosa']);
                    $i++;
                }                                

            });
            
            
            
        })->setFilename('planilla-trabajadores')->export('xlsx');

        
        return Response::make(file_get_contents($destination), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="planilla-trabajadores.xlsx"'
        ]);    
    }
    
    public function descargarPlantilla($tipo){
        
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;
        $mes = \Session::get('mesActivo')->mes;
        $trabajadores = Trabajador::all();
        
        $listaActivos = array();
        if($trabajadores){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $listaActivos[] = array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'rut' => $trabajador->rut
                        );
                    }
                }
            }
        }
        
        $destination = public_path('planillas/planilla.xlsx');
        
        Excel::load($destination, function($reader) {
            $i = 2;
            $trabajadores = Trabajador::all();
            $finMes = \Session::get('mesActivo')->fechaRemuneracion;
            $mes = \Session::get('mesActivo')->mes;
            $listaActivos = array();
            if($trabajadores){
                foreach($trabajadores as $trabajador){
                    $empleado = $trabajador->ficha();
                    if($empleado){
                        if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                            $sheet = $reader->getActiveSheet();
                            $a = 'A' . $i;
                            $sheet->setCellValue($a, $trabajador['rut']);
                            $i++;
                        }
                    }
                }
            }

        })->setFilename('planilla-' . $tipo)->export('xlsx');
        
        return Response::make(file_get_contents($destination), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="planillas.xlsx"'
        ]);    
    }
    
    public function generarPlanillaExcel()
    {
        $datos = Input::all();
        $trabajadores = (array) $datos['trabajadores'];
        $planilla = array();
        
        $mes = \Session::get('mesActivo');
        $liquidaciones = Liquidacion::where('mes', $mes->mes)->get();
        
        foreach($liquidaciones as $liquidacion){
            if(in_array($liquidacion->trabajador_id, $trabajadores)){
                $planilla[] = $liquidacion;
            }
        }
        
        $filename = date("d-m-Y-H-i-s") . "_Planilla_" . $mes->nombre . "_" . $mes->anio . ".xls";
        
        Excel::create("Planilla", function($excel) use($planilla) {
            $excel->sheet("Planilla", function($sheet) use($planilla) {
                $sheet->loadView('excel.planilla')->with('planilla', $planilla)->getStyle('A1')->getAlignment();
            });
        })->store('xls', public_path('stories'));
        
        $destination = public_path('stories/' . $filename);
        
        $datos = array(
            'success' => true,
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $filename,
            'nombre' => 'Planilla',
            'nomina' => $planilla
        );
        
        return Response::json($datos);
    }
    
    public function importarPlanilla()
    {
        $insert = array();
        $val;
        if(Input::hasFile('file')){            
            $file = Input::file('file')->getRealPath();
            $data = Excel::selectSheets('Trabajadores')->load($file, function($reader){                
            })->get();
            $val = $data;
            if(!empty($data) && $data->count()){
                foreach($data as $key => $value){
                    if(isset($value->rut)){
                        $insert[] = array(
                            'rut' => $value->rut,
                            'nombres' => $value->nombres,
                            'apellidos' => $value->apellidos,
                            'nacionalidad' => $value->nacionalidad,
                            'sexo' => strtolower($value->sexo),
                            'estadoCivil' => $value->estado_civil,
                            'fechaNacimiento' => $value->fecha_nacimiento,
                            'direccion' => $value->direccion,
                            'comuna' => $value->comuna,
                            'telefonoFijo' => $value->telefono_fijo,
                            'celular' => $value->celular,
                            'celularEmpresa' => $value->celular_empresa,
                            'email' => $value->email,
                            'emailEmpresa' => $value->email_empresa,
                            'cargo' => $value->cargo,
                            'titulo' => $value->titulo,
                            'seccion' => $value->seccion,
                            'tipoCuenta' => $value->tipo_cuenta,
                            'banco' => $value->banco,
                            'numeroCuenta' => $value->numero_cuenta,
                            'fechaIngreso' => $value->fecha_ingreso,
                            'fechaReconocimiento' => $value->fecha_reconocimiento,
                            'tipoContrato' => $value->tipo_contrato,
                            'fechaVencimiento' => $value->fecha_vencimiento,
                            'fechaFiniquito' => $value->fecha_finiquito,
                            'tipoJornada' => $value->tipo_jornada,
                            'semanaCorrida' => $value->semana_corrida,
                            'monedaSueldo' => $value->moneda_sueldo,
                            'sueldoBase' => $value->sueldo_base,
                            'tipoTrabajador' => $value->tipo_trabajador,
                            'tipo' => $value->tipo_empleado,
                            'excesoRetiro' => $value->exceso_retiro,
                            'monedaColacion' => $value->moneda_colacion,
                            'proporcionalColacion' => $value->proporcional_colacion,
                            'montoColacion' => $value->monto_colacion,
                            'monedaMovilizacion' => $value->moneda_movilizacion,
                            'proporcionalMovilizacion' => $value->proporcional_movilizacion,
                            'montoMovilizacion' => $value->monto_movilizacion,
                            'monedaViatico' => $value->moneda_viatico,
                            'proporcionalViatico' => $value->proporcional_viatico,
                            'montoViatico' => $value->monto_viatico,
                            'prevision' => $value->prevision,
                            'afp_ips' => $value->afpips,
                            'seguroCesantia' => $value->seguro_cesantia,
                            'afpSeguroCesantia' => $value->afp_seguro_cesantia,
                            'isapre' => $value->isapre,
                            'cotizacionIsapre' => $value->cotizacion_isapre,
                            'planIsapre' => $value->plan_isapre,
                            'sindicato' => $value->sindicato,
                            'monedaSindicato' => $value->moneda_sindicato,
                            'montoSindicato' => $value->monto_sindicato,
                            'vacaciones' => $value->vacaciones,
                            'tramo' => $value->tramo
                        );
                    }else{
                        $errores = array();
                        $errores[] = $value;
                    }
                }
            }
        }
        
        if(!isset($errores)){
            $errores = $this->comprobarErrores($insert);
        }
        
        if(!$errores){            
            $tabla = array();
            foreach($insert as $dato){
                if($dato['rut']){
                    $tabla[] = array(
                        'trabajador' => array(
                            'rut' => Funciones::formatear_rut($dato['rut']),
                            'nombreCompleto' => $dato['nombres'] . ' ' . $dato['apellidos'],
                            'fechaIngreso' => Funciones::formatoFecha($dato['fechaIngreso']),
                            'sueldoBase' => $dato['sueldoBase'],
                            'monedaSueldo' => strtoupper($dato['monedaSueldo'])
                        )                        
                    );
                }
            }
            
            $respuesta=array(
                'success' => true,
                'mensaje' => "La Información fue almacenada correctamente",
                'datos' => $tabla,
                'trabajadores' => $insert,
                'val' => $val
            );
        }else{
            $respuesta=array(
                'success' => false,
                'mensaje' => "La acción no pudo ser completada debido a errores en la información ingresada",
                'errores' => $errores
            );
        }
        
        
        return Response::json($respuesta);
    }
    
    public function generarIngresoMasivo()
    {
        $datos = Input::get();
        $idMes = \Session::get('mesActivo')->id;  
        
        
        foreach($datos as $dato){
            if($dato['rut']){
                $trabajador = new Trabajador();
                $trabajador->sid = Funciones::generarSID();
                $trabajador->rut = $dato['rut'];                        
                $trabajador->save();                                

                if($dato['fechaFiniquito']){
                    $estado = 'Finiquitado';
                }else{
                    $estado = 'Ingresado';
                    $dato['fechaFiniquito'] = NULL;
                }

                $ficha = new FichaTrabajador();
                $ficha->trabajador_id = $trabajador->id;
                $ficha->mes_id = $idMes;            
                $ficha->nombres = $dato['nombres'];
                $ficha->apellidos = $dato['apellidos'];
                $ficha->nacionalidad_id = $dato['nacionalidad'];
                $ficha->sexo = $dato['sexo'];
                $ficha->estado_civil_id = $dato['estadoCivil'];
                $ficha->fecha_nacimiento = date('Y-m-d', strtotime($dato['fechaNacimiento']));
                $ficha->direccion = $dato['direccion'];
                $ficha->comuna_id = $dato['comuna'];
                $ficha->telefono = $dato['telefonoFijo'];
                $ficha->celular = $dato['celular'];
                $ficha->celular_empresa = $dato['celularEmpresa'];
                $ficha->email = $dato['email'];
                $ficha->email_empresa = $dato['emailEmpresa'];
                $ficha->tipo_id = $dato['tipo'];
                $ficha->cargo_id = $dato['cargo'];
                $ficha->titulo_id = $dato['titulo'];
                $ficha->seccion_id = $dato['seccion'];
                $ficha->tipo_cuenta_id = $dato['tipoCuenta'];
                $ficha->banco_id = $dato['banco'];
                $ficha->numero_cuenta = $dato['numeroCuenta'];
                $ficha->fecha_ingreso = date('Y-m-d', strtotime($dato['fechaIngreso']));
                $ficha->fecha_reconocimiento = date('Y-m-d', strtotime($dato['fechaReconocimiento']));
                $ficha->tipo_contrato_id = $dato['tipoContrato'];
                $ficha->fecha_vencimiento = $dato['fechaVencimiento'] ? date('Y-m-d', strtotime($dato['fechaVencimiento'])) : NULL;
                $ficha->fecha_finiquito = $dato['fechaFiniquito'] ? date('Y-m-d', strtotime($dato['fechaFiniquito'])) : NULL;
                $ficha->tipo_jornada_id = $dato['tipoJornada'];
                $ficha->semana_corrida = $dato['semanaCorrida'];
                $ficha->moneda_sueldo = strtoupper($dato['monedaSueldo']);
                $ficha->sueldo_base = $dato['sueldoBase'];
                $ficha->tipo_trabajador = $dato['tipoTrabajador'];
                $ficha->gratificacion = 'm';
                $ficha->exceso_retiro = $dato['excesoRetiro'];
                $ficha->moneda_colacion = strtoupper($dato['monedaColacion']);
                $ficha->proporcional_colacion = $dato['proporcionalColacion'];
                $ficha->monto_colacion = $dato['montoColacion'];
                $ficha->moneda_movilizacion = strtoupper($dato['monedaMovilizacion']);
                $ficha->proporcional_movilizacion = $dato['proporcionalMovilizacion'];
                $ficha->monto_movilizacion = $dato['montoMovilizacion'];
                $ficha->moneda_viatico = strtoupper($dato['monedaViatico']);
                $ficha->proporcional_viatico = $dato['proporcionalViatico'];
                $ficha->monto_viatico = $dato['montoViatico'];
                $ficha->prevision_id = $dato['prevision'];
                $ficha->afp_id = $dato['afp_ips'];
                $ficha->seguro_desempleo = $dato['seguroCesantia'];
                $ficha->afp_seguro_id = $dato['afpSeguroCesantia'];
                $ficha->isapre_id = $dato['isapre'];
                if($ficha->isapre_id==246){
                    $dato['cotizacionIsapre'] = '%';
                    $dato['planIsapre'] = 7;
                }
                $ficha->cotizacion_isapre = strtoupper($dato['cotizacionIsapre']);
                $ficha->monto_isapre = $dato['planIsapre'];
                $ficha->sindicato = $dato['sindicato'];
                $ficha->moneda_sindicato = strtoupper($dato['monedaSindicato']);
                $ficha->monto_sindicato = $dato['montoSindicato'];
                $ficha->tramo_id = $dato['tramo'];
                $ficha->estado = $estado;

                if($ficha->estado=='Ingresado'){
                    $ficha->tramo_id = FichaTrabajador::calcularTramo(Funciones::convertir($dato['sueldoBase'], $dato['monedaSueldo']));
                    if($dato['vacaciones']){
                        $trabajador->asignarVacaciones($dato['vacaciones']);
                    }else{
                        $trabajador->asignarVacaciones(0);                        
                    }
                    if($ficha->semana_corrida==1){
                        $trabajador->crearSemanaCorrida();
                    }
                }
                $trabajador->save();
                $trabajador->crearUser(false);
                $ficha->save();
            }

        }
        
        $respuesta=array(
            'success' => true,
            'mensaje' => "La Información fue almacenada correctamente",
            'trabajadores' => $datos
        );
        
        return Response::json($respuesta);
    }
    
    public function comprobarErrores($datos)
    {
        $lista = array();    
        $listaSecciones=array();
        Seccion::listaSecciones($listaSecciones, 0, 1);
        
        $trabajadores = Trabajador::all()->lists('rut');
        $estadosCiviles = EstadoCivil::all()->lists('id');
        $tiposCuentas = TipoCuenta::all()->lists('id');
        $bancos = Banco::all()->lists('id');
        $tiposContratos = TipoContrato::all()->lists('id');
        $tiposTrabajador = Glosa::where('tipo_estructura_id', 5)->orderBy('id', 'ASC')->get()->lists('id');
        $tiposJornadas = Jornada::all()->lists('id');
        $previsiones = Glosa::where('tipo_estructura_id', 4)->orderBy('id', 'ASC')->get()->lists('id');
        $afps = Glosa::where('tipo_estructura_id', 9)->orderBy('id', 'ASC')->get()->lists('id');
        $exCajas = Glosa::where('tipo_estructura_id', 13)->orderBy('id', 'ASC')->get()->lists('id');
        $isapres = Glosa::where('tipo_estructura_id', 15)->orderBy('id', 'ASC')->get()->lists('id');
        $afpsSeguro = Glosa::where('tipo_estructura_id', 9)->orderBy('id', 'ASC')->get()->lists('id');
        $tiposEmpleado = Glosa::where('tipo_estructura_id', 5)->orderBy('id', 'ASC')->get()->lists('id');
        $comunas = Comuna::all()->lists('id');
        $cargos = Cargo::all()->lists('id');
        $titulos = Titulo::all()->lists('id');
        $secciones = Seccion::all()->lists('id');
        $i = 1;
        
        foreach($datos as $dato){
            if($dato){
                if($dato['rut']){
                    $isError = false;
                    $listaErrores = array();
                
                    if($dato['rut']){
                        if(!Funciones::comprobarRut($dato['rut'])){
                            $listaErrores[] = 'El RUT ' . Funciones::formatear_rut($dato['rut']) . ' es inválido.';
                            $isError = true;
                        }                
                        if(in_array($dato['rut'], $trabajadores)){
                            $listaErrores[] = 'El RUT ' . Funciones::formatear_rut($dato['rut']) . ' ya se encuentra registrado.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo RUT es obligatorio.';
                        $isError = true;
                    }
                    if(!$dato['nombres']){
                        $listaErrores[] = 'El campo Nombres es obligatorio.';
                        $isError = true;
                    }
                    if(!$dato['apellidos']){
                        $listaErrores[] = 'El campo Apellidos es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['nacionalidad'])){
                        if($dato['nacionalidad']!=3 && $dato['nacionalidad']!=4){
                            $listaErrores[] = 'El código de Nacionalidad "' . $dato['nacionalidad'] . '" no existe.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Nacionalidad es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['sexo'])){
                        if(strtoupper($dato['sexo'])!='F' && strtoupper($dato['sexo'])!='M'){
                            $listaErrores[] = 'El código de Sexo "' . $dato['sexo'] . '" es incorrecto, recuerda que los códigos son "F" o "M".';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Sexo es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['estadoCivil'])){
                        if(!in_array($dato['estadoCivil'], $estadosCiviles)){
                            $listaErrores[] = 'El código de Estado Civíl "' . $dato['estadoCivil'] . '" no existe.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Estado Civil es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['fechaNacimiento'])){
                        if(!Funciones::comprobarFecha($dato['fechaNacimiento'], 'd-m-Y')){
                           $listaErrores[] = 'El formato de Fecha de Nacimiento "' . $dato['fechaNacimiento'] . '" es incorrecto, recuerda que el formato es "DD-MM-AAAA".';
                            $isError = true; 
                        }
                    }else{
                        $listaErrores[] = 'El campo Fecha de Nacimiento es obligatorio.';
                        $isError = true;
                    }
                    if(!isset($dato['direccion'])){
                        $listaErrores[] = 'El campo Direcci? es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['comuna'])){
                        if(!in_array($dato['comuna'], $comunas)){
                            $listaErrores[] = 'El código de Comuna "' . $dato['comuna'] . '" no existe.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Comuna es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['tipo'])){
                        if(!in_array($dato['tipo'], $tiposEmpleado)){
                            $listaErrores[] = 'El Tipo de Empleado "' . $dato['tipo'] . '" no existe.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Tipo de Empleado es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['cargo'])){
                        if(!in_array($dato['cargo'], $cargos)){
                            $listaErrores[] = 'El Cargo "' . $dato['cargo'] . '" no existe.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Cargo es obligatorio.';
                        $isError = true;
                    }                
                    if(isset($dato['titulo'])){
                        if(!in_array($dato['titulo'], $titulos)){
                            $listaErrores[] = 'El Título "' . $dato['titulo'] . '" no existe.';
                            $isError = true;
                        }
                    }
                    if(isset($dato['seccion'])){
                        if(!in_array($dato['seccion'], $secciones)){
                            $listaErrores[] = 'La Secci? "' . $dato['seccion'] . '" no existe.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Secci? es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['telefonoFijo'])){
                        if(!is_numeric($dato['telefonoFijo'])){
                            $listaErrores[] =  'El formato de Teléfono Fijo "' . $dato['telefonoFijo'] . '" es incorrecto, recuerda deben ser sólo valores núméricos.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Teléfono Fijo es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['celular'])){
                        if(!is_numeric($dato['celular'])){
                            $listaErrores[] =  'El formato de Celular "' . $dato['celular'] . '" es incorrecto, recuerda deben ser sólo valores núméricos.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Celular es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['celularEmpresa']) && !is_numeric($dato['celularEmpresa'])){
                        $listaErrores[] =  'El formato de Celular Empresa "' . $dato['celularEmpresa'] . '" es incorrecto, recuerda deben ser sólo valores núméricos.';
                        $isError = true;
                    }
                    if(isset($dato['email'])){
                        if(!filter_var($dato['email'], FILTER_VALIDATE_EMAIL)) {
                            /*$listaErrores[] =  'El formato de Email "' . $dato['email'] . '" es incorrecto, recuerda que el formato es "nombre@sitio.com".';
                            $isError = false;*/
                        }
                    }else{
                        $listaErrores[] = 'El campo Email es obligatorio.';
                        $isError = true;                    
                    }
                    if(isset($dato['emailEmpresa'])){
                        if(!filter_var($dato['emailEmpresa'], FILTER_VALIDATE_EMAIL)) {
                            $listaErrores[] =  'El formato de Email Empresa "' . $dato['emailEmpresa'] . '" es incorrecto, recuerda que el formato es "nombre@sitio.com".';
                            $isError = true;
                        }
                    }
                    if(isset($dato['tipoCuenta'])){
                        if(!in_array($dato['tipoCuenta'], $tiposCuentas)){
                            $listaErrores[] = 'El código de Tipo de Cuenta "' . $dato['tipoCuenta'] . '" no existe.';
                            $isError = true;
                        }
                    }else{
                        /*$listaErrores[] = 'El campo Tipo de Cuenta es obligatorio.';
                        $isError = true;*/
                    }
                    if(isset($dato['banco'])){
                        if(!in_array($dato['banco'], $bancos)){
                            $listaErrores[] = 'El código de Banco "' . $dato['banco'] . '" no existe.';
                            $isError = true;
                        }
                    }else{
                        /*$listaErrores[] = 'El campo Banco es obligatorio.';
                        $isError = true;*/
                    }
                    if(!isset($dato['numeroCuenta'])){
                        /*$listaErrores[] = 'El campo Número de Cuenta es obligatorio.';
                        $isError = true;*/
                    }
                    if(isset($dato['fechaIngreso'])){
                        if(!Funciones::comprobarFecha($dato['fechaIngreso'], 'd-m-Y')){
                           $listaErrores[] = 'El formato de Fecha de Ingreso "' . $dato['fechaIngreso'] . '" es incorrecto, recuerda que el formato es "DD-MM-AAAA".';
                            $isError = true; 
                        }
                    }else{
                        $listaErrores[] = 'El campo Fecha de Ingreso es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['fechaReconocimiento'])){
                        if(!Funciones::comprobarFecha($dato['fechaReconocimiento'], 'd-m-Y')){
                           $listaErrores[] = 'El formato de Fecha de Reconocimiento "' . $dato['fechaReconocimiento'] . '" es incorrecto, recuerda que el formato es "DD-MM-AAAA".';
                            $isError = true; 
                        }
                    }else{
                        $listaErrores[] = 'El campo Fecha de Reconocimiento es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['tipoContrato'])){
                        if(!in_array($dato['tipoContrato'], $tiposContratos)){
                            $listaErrores[] = 'El código de Tipo de Contrato "' . $dato['tipoContrato'] . '" no existe.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Tipo de Contrato es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['fechaVencimiento'])){
                        if(!Funciones::comprobarFecha($dato['fechaVencimiento'], 'd-m-Y')){
                           $listaErrores[] = 'El formato de Fecha de Vencimiento "' . $dato['fechaVencimiento'] . '" es incorrecto, recuerda que el formato es "DD-MM-AAAA".';
                            $isError = true; 
                        }
                    }
                    if(isset($dato['fechaFiniquito'])){
                        if(!Funciones::comprobarFecha($dato['fechaFiniquito'], 'd-m-Y')){
                           $listaErrores[] = 'El formato de Fecha de Finiquito "' . $dato['fechaFiniquito'] . '" es incorrecto, recuerda que el formato es "DD-MM-AAAA".';
                            $isError = true; 
                        }
                    }
                    if(isset($dato['tipoJornada'])){
                        if(!in_array($dato['tipoJornada'], $tiposJornadas)){
                            $listaErrores[] = 'El código de Tipo de Jornada "' . $dato['tipoJornada'] . '" no existe.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Tipo de Jornada es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['semanaCorrida'])){
                        if($dato['semanaCorrida']!=0 && $dato['semanaCorrida']!=1){
                            $listaErrores[] = 'El código de Semana Corrida "' . $dato['nacionalidad'] . '" es incorrecto, recuerda que los códigos son 0 o 1.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Semana Corrida es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['monedaSueldo'])){
                        if($dato['monedaSueldo']!='$' && strtoupper($dato['monedaSueldo'])!='UF' && strtoupper($dato['monedaSueldo'])!='UTM'){
                            $listaErrores[] = 'El formato de Moneda de Sueldo Base "' . $dato['monedaSueldo'] . '" es incorrecto, recuerda que los formatos son "$", "UF" o "UTM".';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Moneda Sueldo Base es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['sueldoBase'])){
                        if(!is_numeric($dato['sueldoBase'])){
                            $listaErrores[] = 'El formato del Monto Sueldo Base"' . $dato['sueldoBase'] . '" es incorrecto, recuerda que este campo acepta sólo valores numéricos.';
                            $isError = true;
                        }
                    }else{
                        $listaErrores[] = 'El campo Sueldo Base es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['tipoTrabajador'])){
                        if(strtoupper($dato['tipoTrabajador'])!='NORMAL' && strtoupper($dato['tipoTrabajador'])!='SOCIO'){
                            $listaErrores[] = 'El código de Tipo de Trabajador "' . $dato['tipoTrabajador'] . '" es incorrecto, recuerda que los códigos son "Normal" o "Socio".';
                            $isError = true;
                        }else{
                            if(strtoupper($dato['tipoTrabajador'])=='SOCIO'){
                                if(isset($dato['excesoRetiro'])){
                                    if($dato['excesoRetiro']!=0 && $dato['excesoRetiro']!=1){
                                        $listaErrores[] = 'El código de Exceso de Retiro "' . $dato['excesoRetiro'] . '" es incorrecto, recuerda que los códigos son 0 o 1.';
                                        $isError = true;
                                    }
                                }else{
                                    $listaErrores[] = 'El campo Exceso de Retiro es obligatorio si el Tipo de Trabajador es "Socio".';
                                    $isError = true;
                                }
                            }
                        }
                    }else{
                        $listaErrores[] = 'El campo Tipo de Trabajador es obligatorio.';
                        $isError = true;
                    }
                    if(isset($dato['monedaColacion']) || isset($dato['montoColacion']) || isset($dato['proporcionalColacion'])){
                        if(isset($dato['monedaColacion'])){
                            if($dato['monedaColacion']!='$' && strtoupper($dato['monedaColacion'])!='UF' && strtoupper($dato['monedaColacion'])!='UTM'){
                                $listaErrores[] = 'El formato de Moneda Colación "' . $dato['monedaColacion'] . '" es incorrecto, recuerda que los formatos son "$", "UF" o "UTM".';
                                $isError = true;
                            }
                        }else{
                            $listaErrores[] = 'El campo Moneda Colación es obligatorio si el trabajador posee Colación.';
                            $isError = true;
                        }
                        if(isset($dato['montoColacion'])){
                            if(!is_numeric($dato['montoColacion'])){
                                $listaErrores[] = 'El formato de Moneda de Colación "' . $dato['montoColacion'] . '" es incorrecto, recuerda que este campo acepta sólo valores numéricos.';
                                $isError = true;
                            }
                        }else{
                            $listaErrores[] = 'El campo Monto Colación es obligatorio si el trabajador posee Colación.';
                            $isError = true;
                        }
                        if(isset($dato['proporcionalColacion'])){
                            if($dato['proporcionalColacion']!=0 && $dato['proporcionalColacion']!=1){
                                $listaErrores[] = 'El código de Proporcional Colación "' . $dato['proporcionalColacion'] . '" es incorrecto, recuerda que los códigos son 0 o 1.';
                                $isError = true;
                            }
                        }else{
                            $listaErrores[] = 'El campo Proporcional Colación es obligatorio si el trabajador posee Colación.';
                            $isError = true;
                        }
                    }
                    if(isset($dato['monedaMovilizacion']) || isset($dato['montoMovilizacion']) || isset($dato['proporcionalMovilizacion'])){
                        if(isset($dato['monedaMovilizacion'])){
                            if($dato['monedaMovilizacion']!='$' && strtoupper($dato['monedaMovilizacion'])!='UF' && strtoupper($dato['monedaMovilizacion'])!='UTM'){
                                $listaErrores[] = 'El formato de Moneda Movilización "' . $dato['monedaMovilizacion'] . '" es incorrecto, recuerda que los formatos son "$", "UF" o "UTM".';
                                $isError = true;
                            }
                        }else{
                            $listaErrores[] = 'El campo Moneda Movilización es obligatorio si el trabajador posee Movilización.';
                            $isError = true;
                        }
                        if(isset($dato['montoMovilizacion'])){
                            if(!is_numeric($dato['montoMovilizacion'])){
                                $listaErrores[] = 'El formato de Moneda de Movilización "' . $dato['montoMovilizacion'] . '" es incorrecto, recuerda que este campo acepta sólo valores numéricos.';
                                $isError = true;
                            }
                        }else{
                            $listaErrores[] = 'El campo Monto Movilización es obligatorio si el trabajador posee Movilización.';
                            $isError = true;
                        }
                        if(isset($dato['proporcionalMovilizacion'])){
                            if($dato['proporcionalMovilizacion']!=0 && $dato['proporcionalMovilizacion']!=1){
                                $listaErrores[] = 'El código de Proporcional Movilización "' . $dato['proporcionalMovilizacion'] . '" es incorrecto, recuerda que los códigos son 0 o 1.';
                                $isError = true;
                            }
                        }else{
                            $listaErrores[] = 'El campo Proporcional Movilización es obligatorio si el trabajador posee Movilización.';
                            $isError = true;
                        }
                    }
                    if(isset($dato['monedaViatico']) || isset($dato['montoViatico']) || isset($dato['proporcionalViatico'])){
                        if(isset($dato['monedaViatico'])){
                            if($dato['monedaViatico']!='$' && strtoupper($dato['monedaViatico'])!='UF' && strtoupper($dato['monedaViatico'])!='UTM'){
                                $listaErrores[] = 'El formato de Moneda Viático "' . $dato['monedaViatico'] . '" es incorrecto, recuerda que los formatos son "$", "UF" o "UTM".';
                                $isError = true;
                            }
                        }else{
                            $listaErrores[] = 'El campo Moneda Viático es obligatorio si el trabajador posee Viático.';
                            $isError = true;
                        }
                        if(isset($dato['montoViatico'])){
                            if(!is_numeric($dato['montoViatico'])){
                                $listaErrores[] = 'El formato de Moneda de Viático "' . $dato['montoViatico'] . '" es incorrecto, recuerda que este campo acepta sólo valores numéricos.';
                                $isError = true;
                            }
                        }else{
                            $listaErrores[] = 'El campo Monto Viático es obligatorio si el trabajador posee Viático.';
                            $isError = true;
                        }
                        if(isset($dato['proporcionalViatico'])){
                            if($dato['proporcionalViatico']!=0 && $dato['proporcionalViatico']!=1){
                                $listaErrores[] = 'El código de Proporcional Viático "' . $dato['proporcionalViatico'] . '" es incorrecto, recuerda que los códigos son 0 o 1.';
                                $isError = true;
                            }
                        }else{
                            $listaErrores[] = 'El campo Proporcional Viático es obligatorio si el trabajador posee Viático.';
                            $isError = true;
                        }
                    }
                    if(isset($dato['prevision'])){
                        if(!in_array($dato['prevision'], $previsiones)){
                            $listaErrores[] = 'El código de Previsión "' . $dato['prevision'] . '" no existe.';
                            $isError = true;
                        }else{
                            if($dato['prevision']==8){
                                if(isset($dato['afp_ips'])){
                                    if(!in_array($dato['afp_ips'], $afps)){
                                        $listaErrores[] = 'El código de AFP "' . $dato['afp_ips'] . '" no existe.';
                                        $isError = true;
                                    }
                                }else{
                                    $listaErrores[] = 'El campo AFP es obligatorio si la previsión es AFP.';
                                    $isError = true;
                                }  
                            }else if($dato['prevision']==9){
                                if(isset($dato['afp_ips'])){
                                    if(!in_array($dato['afp_ips'], $exCajas)){
                                        $listaErrores[] = 'El código de IPS "' . $dato['afp_ips'] . '" no existe.';
                                        $isError = true;
                                    }
                                }else{
                                    $listaErrores[] = 'El campo IPS es obligatorio si la previsión es IPS.';
                                    $isError = true;
                                }  
                            }
                        }
                    }else{
                        $listaErrores[] = 'El campo Previsión es obligatorio.';
                        $isError = true;
                    }                           
                    if(isset($dato['seguroCesantia'])){
                        if($dato['seguroCesantia']!=0 && $dato['seguroCesantia']!=1){
                            $listaErrores[] = 'El código de Seguro de Cesantía "' . $dato['seguroCesantia'] . '" es incorrecto, recuerda que los códigos son 0 o 1.';
                            $isError = true;
                        }else{
                            if($dato['seguroCesantia']==1){
                                if(isset($dato['afpSeguroCesantia'])){
                                    if(!in_array($dato['afpSeguroCesantia'], $afpsSeguro)){
                                        $listaErrores[] = 'El código de AFP Seguro de Cesantía "' . $dato['afpSeguroCesantia'] . '" no existe.';
                                        $isError = true;
                                    }
                                }else{
                                    $listaErrores[] = 'El campo AFP Seguro de Cesantía es obligatorio si el trabajador posee Seguro de Cesantía.';
                                    $isError = true;
                                }
                            }
                        }
                    }else{
                        $listaErrores[] = 'El campo Seguro de Cesantía es obligatorio.';
                        $isError = true;
                    }      
                    if(isset($dato['isapre'])){
                        if(!in_array($dato['isapre'], $isapres)){
                            $listaErrores[] = 'El código de Isapre "' . $dato['isapre'] . '" no existe.';
                            $isError = true;                    
                        }else{
                            if($dato['isapre']!=240 && $dato['isapre']!=246){
                                if(isset($dato['cotizacionIsapre'])){
                                    if($dato['cotizacionIsapre']!='$' && strtoupper($dato['cotizacionIsapre'])!='UF'){
                                        $listaErrores[] = 'El código de Cotización de Isapre "' . $dato['cotizacionIsapre'] . '" es incorrecto, recuerda que los códigos son "$" o "UF".';
                                        $isError = true;
                                    }
                                }else{
                                    $listaErrores[] = 'El campo Cotización Isapre es obligatorio si el trabajador posee Isapre.';
                                    $isError = true;
                                }
                                if(isset($dato['planIsapre'])){
                                    if(!is_numeric($dato['planIsapre'])){
                                        $listaErrores[] =  'El formato de Plan Isapre "' . $dato['planIsapre'] . '" es incorrecto, recuerda deben ser sólo valores núméricos.';
                                        $isError = true;
                                    }
                                }else{
                                    $listaErrores[] = 'El campo Plan Isapre es obligatorio si el trabajador posee Isapre.';
                                    $isError = true;
                                }
                            }
                        }
                    }else{
                        $listaErrores[] = 'El campo Isapre es obligatorio.';
                        $isError = true;
                    }     
                    if(isset($dato['sindicato'])){
                        if($dato['sindicato']!=0 && $dato['sindicato']!=1){
                            $listaErrores[] = 'El código de Sindicato "' . $dato['sindicato'] . '" es incorrecto, recuerda que los códigos son 0 o 1.';
                            $isError = true;
                        }else{
                            if($dato['sindicato']==1){
                                if(isset($dato['monedaSindicato'])){
                                    if($dato['monedaSindicato']!='$' && strtoupper($dato['monedaSindicato'])!='UF' && strtoupper($dato['monedaSindicato'])!='UTM'){
                                        $listaErrores[] = 'El formato de Moneda de Sindicato "' . $dato['monedaSindicato'] . '" es incorrecto, recuerda que los formatos son "$", "UF" o "UTM".';
                                        $isError = true;
                                    }
                                }else{
                                    $listaErrores[] = 'El campo Moneda Sindicato es obligatorio si el trabajador es parte del Sindicato.';
                                    $isError = true;
                                } 
                                if(isset($dato['montoSindicato'])){
                                    if(!is_numeric($dato['montoSindicato'])){
                                        $listaErrores[] = 'El formato del Monto Sindicato "' . $dato['montoSindicato'] . '" es incorrecto, recuerda que este campo acepta sólo valores numéricos.';
                                        $isError = true;
                                    }
                                }else{
                                    $listaErrores[] = 'El campo Monto Sindicato es obligatorio si el trabajador es parte del Sindicato.';
                                    $isError = true;
                                } 
                            }
                        }
                    }else{
                        /*$listaErrores[] = 'El campo Sindicato es obligatorio.';
                        $isError = true;*/
                    }                                
                    if(isset($dato['vacaciones'])){
                        if(!is_numeric($dato['vacaciones'])){
                            $listaErrores[] = 'El formato de Vacaciones "' . $dato['vacaciones'] . '" es incorrecto, recuerda que este campo acepta sólo valores numéricos.';
                            $isError = true;
                        }
                    }else{
                        /*$listaErrores[] = 'El campo Vacaciones es obligatorio.';
                        $isError = true;*/
                    }
                    if(isset($dato['tramo'])){
                        if(strtoupper($dato['tramo'])!='A' && strtoupper($dato['tramo'])!='B' && strtoupper($dato['tramo'])!='C' && strtoupper($dato['tramo'])!='D'){
                            $listaErrores[] = 'El código de Tramo de Asignación Familiar "' . $dato['tramo'] . '" es incorrecto, recuerda que los códigos son "a", "b", "c" o "d".';
                            $isError = true;
                        }
                    }
                    if($isError){
                        $lista[] = array(
                            'fila' => $i,
                            'trabajador' => $dato['rut'] ? Funciones::formatear_rut($dato['rut']) : '',
                            'errores' => $listaErrores
                        );
                    }
                    $i++;
                }
            }
        }
        
        return $lista;
    }
    
    public function descargarArchivoPreviredExcel()
    {
        $mes = \Session::get('mesActivo');
        $finMes = $mes->fechaRemuneracion;
        $trabajadores = Trabajador::all();        
        $listaTrabajadores = array();
        $empresa = Session::get('empresa');
        if($trabajadores){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes || $empleado->estado=='Finiquitado' && $empleado->fecha_finiquito >= $mes->mes && $empleado->fecha_ingreso<=$finMes){
                        $liquidacion = Liquidacion::where('trabajador_id', $trabajador->id)->where('mes', $mes->mes)->first();
                        
                        $movimientoPersonal = $liquidacion->movimiento_personal;
                        $detallesAfiliadoVoluntario = $liquidacion->detalleAfiliadoVoluntario;
                        $detallesAfp = $liquidacion->miDetalleAfp();
                        $detallesApvc = $liquidacion->miDetalleApvc($lineaAdicional);
                        $detallesApvi = $liquidacion->miDetalleApvi($lineaAdicional);
                        $detallesCaja = $liquidacion->miDetalleCaja();
                        $detallesIpsIslFonasa = $liquidacion->miDetalleIpsIslFonasa();
                        $detallesSalud = $liquidacion->miDetalleSalud();
                        $detallesMutual = $liquidacion->miDetalleMutual();
                        $detallesSeguroCesantia = $liquidacion->miDetalleSeguroCesantia();
                        $detallesPagadorSubsidio = $liquidacion->miDetallePagadorSubsidio();
                        
                        $listaTrabajadores[] = array(
                            /*'id' => $trabajador->id,
                            'sid' => $trabajador->sid,*/
                            
                            //Datos del Trabajador
                            'rutSinDigito' => $trabajador->rut_sin_digito(),
                            'rutDigito' => $trabajador->rut_digito(),
                            'apellidoPaterno' => $empleado->apellidoPaterno(),
                            'apellidoMaterno' => $empleado->apellidoMaterno(),
                            'nombres' => $liquidacion->trabajador_nombres,
                            'sexo' => strtoupper($empleado->sexo),
                            'nacionalidad' => $empleado->codigoNacionalidad(),
                            'tipoPago' => '01',
                            'periodoDesde' => Funciones::obtenerMes($mes->nombre) . $mes->anio, 
                            'periodoHasta' => 0, 
                            'regimenPrevisional' => $liquidacion->regimenPrevisional(), 
                            'tipoTrabajador' => $empleado->tipoTrabajador(), 
                            'diasTrabajados' => $liquidacion->dias_trabajados, 
                            'tipoLinea' => '00', 
                            'movimientoPersonal' => $liquidacion->movimiento_personal, 
                            'movimientoPersonalDesde' => $liquidacion->fecha_desde ? $liquidacion->fecha_desde : '',
                            'movimientoPersonalHasta' => $liquidacion->fecha_hasta ? $liquidacion->fecha_hasta : '',
                            'tramo' => strtoupper($empleado->tramo_id), 
                            'cargasSimples' => $liquidacion->cantidad_cargas_simples, 
                            'cargasMaternales' => $liquidacion->cantidad_cargas_maternales, 
                            'cargasInvalidas' => $liquidacion->cantidad_cargas_invalidas,
                            'asignacionFamiliar' => $liquidacion->total_cargas,
                            'asignacionFamiliarRetroactiva' => $liquidacion->asignacion_retroactiva,
                            'reintegroCargasFamiliares' => $liquidacion->reintegro_cargas,
                            'solicitudTrabajadorJoven' => $empleado->solicitudTrabajadorJoven(),
                            
                            //Datos de la AFP
                            'codigoAfp' => $detallesAfp['codigoAfp'],
                            'rentaImponible' => $detallesAfp['rentaImponible'],
                            'cotizacionAfp' => $detallesAfp['cotizacionAfp'],
                            'sis' => $detallesAfp['sis'],
                            'cuentaAhorroVoluntario' => $detallesAfp['cuentaAhorroVoluntario'],
                            'rentaSustitutiva' => $detallesAfp['rentaSustitutiva'],
                            'tasaSustitutiva' => $detallesAfp['tasaSustitutiva'],
                            'aporteSustitutiva' => $detallesAfp['aporteSustitutiva'],
                            'numeroPeriodos' => $detallesAfp['numeroPeriodos'],
                            'periodoDesdeSustit' => $detallesAfp['periodoDesdeSustit'],
                            'periodoHastaSustit' => $detallesAfp['periodoHastaSustit'],
                            'puestoTrabajoPesado' => $detallesAfp['puestoTrabajoPesado'],
                            'porcentajeTrabajoPesado' => $detallesAfp['porcentajeTrabajoPesado'],
                            'cotizacionTrabajoPesado' => $detallesAfp['cotizacionTrabajoPesado'],
                            
                            //Datos Ahorro Previsional Voluntario Individual    
                            'codigoAPVI' => $detallesApvi['codigo'],
                            'numeroContratoAPVI' => $detallesApvi['numeroContrato'],
                            'formaPagoAPVI' => $detallesApvi['formaPago'],
                            'cotizacionAPVI' => $detallesApvi['cotizacion'],
                            'cotizacionDepositosConvenidos' => $detallesApvi['cotizacionDepositosConvenidos'],
                            
                            //Datos Ahorro Previsional Voluntario Colectivo
                            'codigoAPVC' => $detallesApvc['codigo'],
                            'numeroContratoAPVC' => $detallesApvc['numeroContrato'],
                            'formaPagoAPVC' => $detallesApvc['formaPago'],
                            'cotizacionTrabajadorAPVC' => $detallesApvc['cotizacionTrabajador'],
                            'cotizacionEmpleadorAPVC' => $detallesApvc['cotizacionEmpleador'],
                            
                            
                            //Datos Afiliado Voluntario
                            'rutAfiliadoVoluntario' => '',
                            'dvAfiliadoVoluntario' => '',
                            'apellidoPaternoAfiliadoVoluntario' => '',
                            'apellidoMaternoAfiliadoVoluntario' => '',
                            'nombresAfiliadoVoluntario' => '',
                            'codigoMovimientoPersonalAfiliadoVoluntario' => '0',
                            'fechaDesdeAfiliadoVoluntario' => '',
                            'fechaHastaAfiliadoVoluntario' => '',
                            'codigoAfpAfiliadoVoluntario' => '',
                            'montoCapitalizacionVoluntaria' => 0,
                            'montoAhorroVoluntario' => 0,
                            'numeroPeriodosCotizacion' => 0,
                            
                            //Datos IPS-ISL-FONASA
                            'codigoExCaja' => $detallesIpsIslFonasa['codigoExCaja'],
                            'tasaCotizacionExCaja' => $detallesIpsIslFonasa['tasa'],
                            'rentaImponibleIps' => $detallesIpsIslFonasa['rentaImponible'],
                            'cotizacionObligatoriaIps' => $detallesIpsIslFonasa['cotizacionObligatoria'],
                            'rentaImponibleDesahucio' => $detallesIpsIslFonasa['rentaImponibleDesahucio'],
                            'codigoExCajaDesahucio' => $detallesIpsIslFonasa['codigoExCajaDesahucio'],
                            'tasaDesahucio' => $detallesIpsIslFonasa['tasaDesahucio'],
                            'cotizacionDesahucio' => $detallesIpsIslFonasa['cotizacionDesahucio'],
                            'cotizacionFonasa' => $detallesIpsIslFonasa['cotizacionFonasa'],
                            'cotizacionIsl' => $detallesIpsIslFonasa['cotizacionIsl'],
                            'bonificacion15386' => $detallesIpsIslFonasa['bonificacion'],
                            'descuentoCargasIsl' => $detallesIpsIslFonasa['descuentoCargasIsl'],
                            'bonosGobierno' => $detallesIpsIslFonasa['bonosGobierno'],                            
                            
                            //Datos Salud
                            'codigoInstitucionSalud' => $detallesSalud['codigoSalud'],
                            'numeroFun' => '',
                            'rentaImponibleIsapre' => $detallesSalud['rentaImponible'],
                            'monedaPlanIsapre' => $detallesSalud['moneda'],
                            'cotizacionPactada' => $detallesSalud['cotizacionPactada'],
                            'cotizacionObligatoria' => $detallesSalud['cotizacionObligatoria'],
                            'cotizacionAdicional' => $detallesSalud['cotizacionAdicional'],
                            'montoGarantiaExplicita' => $detallesSalud['ges'],
                            
                            //Datos Caja de Compensación
                            'codigoCcaf' => $detallesCaja['codigoCaja'],
                            'rentaImponibleCcaf' => $detallesCaja['rentaImponible'],
                            'creditosPersonalesCcaf' => $detallesCaja['creditosPersonales'],
                            'descuentoDentalCcaf' => $detallesCaja['descuentoDental'],
                            'descuentosLeasing' => $detallesCaja['descuentosLeasing'],
                            'descuentosSeguroCcaf' => $detallesCaja['descuentosSeguro'],
                            'otrosDescuentosCcaf' => $detallesCaja['otrosDescuentos'],
                            'cotizacionCcafNoAfiliadosIsapre' => $detallesCaja['cotizacion'],
                            'descuentoCargasFamiliaresCcaf' => $detallesCaja['descuentoCargas'],
                            'otrosDescuentosCcaf1' => $detallesCaja['otrosDescuentos1'],
                            'otrosDescuentosCcaf2' => $detallesCaja['otrosDescuentos2'],
                            'bonosGobiernoSalud' => $detallesCaja['bonosGobierno'],
                            'codigoSucursalSalud' => $detallesCaja['codigoSucursal'],
                            
                            //Datos Mutualidad
                            'codigoMutualidad' => $detallesMutual['codigoMutual'],
                            'rentaImponibleMutual' => $detallesMutual['rentaImponible'],
                            'cotizacionAccidenteTrabajo' => $detallesMutual['cotizacionAccidentes'],
                            'sucursalPagoMutual' => $detallesMutual['codigoSucursal'],
                            
                            //Datos Administradora de Seguro de Cesantía
                            'rentaImponibleSeguroCesantia' => $detallesSeguroCesantia['rentaImponible'],
                            'aporteTrabajadorSeguroCesantia' => $detallesSeguroCesantia['aporteTrabajador'],
                            'aporteEmpleadorSeguroCesantia' => $detallesSeguroCesantia['aporteEmpleador'],
                            
                            //Datos Pagador de Subsidios
                            'rutPagadoraSubsidio' => $detallesPagadorSubsidio['rut'],
                            'dvPagadoraSubsidio' => $detallesPagadorSubsidio['digito'],
                            
                            //Otros Datos de la Empresa
                            'centroCosto' => $liquidacion->centro_costo_id                  
                            
                            //'liquidacion' => $liquidacion                           
                        );
                        //$lineaAdicional
                        if(false){
                            if($liquidacion->movimiento_personal==3){
                                $licencia = $trabajador->fechasLicencia();
                                $fechaDesdeMovimiento = $licencia['desde'];
                                $fechaHastaMovimiento = $licencia['hasta'];
                            }else{
                                $fechaDesdeMovimiento = '';
                                $fechaHastaMovimiento = '';
                            }
                            $listaTrabajadores[] = array(

                                //Datos del Trabajador
                                'rutSinDigito' => '',
                                'rutDigito' => '',
                                'apellidoPaterno' => '',
                                'apellidoMaterno' => '',
                                'nombres' => '',
                                'sexo' => '',
                                'nacionalidad' => '',
                                'tipoPago' => '',
                                'periodoDesde' => '', 
                                'periodoHasta' => '', 
                                'regimenPrevisional' => '', 
                                'tipoTrabajador' => '', 
                                'diasTrabajados' => '', 
                                'tipoLinea' => '01', 
                                'movimientoPersonal' => '', 
                                'movimientoPersonalDesde' => $fechaDesdeMovimiento,
                                'movimientoPersonalHasta' => $fechaHastaMovimiento,
                                'tramo' => '', 
                                'cargasSimples' => '', 
                                'cargasMaternales' => '', 
                                'cargasInvalidas' => '',
                                'asignacionFamiliar' => '',
                                'asignacionFamiliarRetroactiva' => '',
                                'reintegroCargasFamiliares' => '',
                                'solicitudTrabajadorJoven' => '',

                                //Datos de la AFP
                                'codigoAfp' => '',
                                'rentaImponible' => '',
                                'cotizacionAfp' => '',
                                'sis' => '',
                                'cuentaAhorroVoluntario' => '',
                                'rentaSustitutiva' => '',
                                'tasaSustitutiva' => '',
                                'aporteSustitutiva' => '',
                                'numeroPeriodos' => '',
                                'periodoDesdeSustit' => '',
                                'periodoHastaSustit' => '',
                                'puestoTrabajoPesado' => '',
                                'porcentajeTrabajoPesado' => '',
                                'cotizacionTrabajoPesado' => '',

                                //Datos Ahorro Previsional Voluntario Individual    
                                'codigoAPVI' => '',
                                'numeroContratoAPVI' => '',
                                'formaPagoAPVI' => '',
                                'cotizacionAPVI' => '',
                                'cotizacionDepositosConvenidos' => '',

                                //Datos Ahorro Previsional Voluntario Colectivo
                                'codigoAPVC' => '',
                                'numeroContratoAPVC' => '',
                                'formaPagoAPVC' => '',
                                'cotizacionTrabajadorAPVC' => '',
                                'cotizacionEmpleadorAPVC' => '',


                                //Datos Afiliado Voluntario
                                'rutAfiliadoVoluntario' => '',
                                'dvAfiliadoVoluntario' => '',
                                'apellidoPaternoAfiliadoVoluntario' => '',
                                'apellidoMaternoAfiliadoVoluntario' => '',
                                'nombresAfiliadoVoluntario' => '',
                                'codigoMovimientoPersonalAfiliadoVoluntario' => '0',
                                'fechaDesdeAfiliadoVoluntario' => '',
                                'fechaHastaAfiliadoVoluntario' => '',
                                'codigoAfpAfiliadoVoluntario' => '',
                                'montoCapitalizacionVoluntaria' => '',
                                'montoAhorroVoluntario' => '',
                                'numeroPeriodosCotizacion' => '',

                                //Datos IPS-ISL-FONASA
                                'codigoExCaja' => '',
                                'tasaCotizacionExCaja' => '',
                                'rentaImponibleIps' => '',
                                'cotizacionObligatoriaIps' => '',
                                'rentaImponibleDesahucio' => '',
                                'codigoExCajaDesahucio' => '',
                                'tasaDesahucio' => '',
                                'cotizacionDesahucio' => '',
                                'cotizacionFonasa' => '',
                                'cotizacionIsl' => '',
                                'bonificacion15386' => '',
                                'descuentoCargasIsl' => '',
                                'bonosGobierno' => '',                          

                                //Datos Salud
                                'codigoInstitucionSalud' => '',
                                'numeroFun' => '',
                                'rentaImponibleIsapre' => '',
                                'monedaPlanIsapre' => '',
                                'cotizacionPactada' => '',
                                'cotizacionObligatoria' => '',
                                'cotizacionAdicional' => '',
                                'montoGarantiaExplicita' => '',

                                //Datos Caja de Compensación
                                'codigoCcaf' => '',
                                'rentaImponibleCcaf' => '',
                                'creditosPersonalesCcaf' => '',
                                'descuentoDentalCcaf' => '',
                                'descuentosLeasing' => '',
                                'descuentosSeguroCcaf' => '',
                                'otrosDescuentosCcaf' => '',
                                'cotizacionCcafNoAfiliadosIsapre' => '',
                                'descuentoCargasFamiliaresCcaf' => '',
                                'otrosDescuentosCcaf1' => '',
                                'otrosDescuentosCcaf2' => '',
                                'bonosGobierno' => '',
                                'codigoSucursal' => '',

                                //Datos Mutualidad
                                'codigoMutualidad' => '',
                                'rentaImponibleMutual' => '',
                                'cotizacionAccidenteTrabajo' => '',
                                'sucursalPagoMutual' => '',

                                //Datos Administradora de Seguro de Cesantía
                                'rentaImponibleSeguroCesantia' => '',
                                'aporteTrabajadorSeguroCesantia' => '',
                                'aporteEmpleadorSeguroCesantia' => '',

                                //Datos Pagador de Subsidios
                                'rutPagadoraSubsidio' => '',
                                'dvPagadoraSubsidio' => '',

                                //Otros Datos de la Empresa
                                'centroCosto' => ''                  
                            );
                        }
                    }
                }
            }
        }
        
        $filename = "ArchivoPrevired.xls";
        
        
        
        $destination = public_path('stories/' . $filename);
        
        $fp = fopen($destination, "w+");
        if($fp){
            if(count($listaTrabajadores)){
                foreach($listaTrabajadores as $index => $trab){
                    //fputcsv($fp, $trab, ";");
                    fputs($fp, utf8_decode(implode(";", $trab))."\r\n", 2048);
                }
            }
            fclose($fp);
        }
        
        /*
        
        Excel::create("ArchivoPrevired", function($excel) use($listaTrabajadores) {
            $excel->sheet("ArchivoPrevired", function($sheet) use($listaTrabajadores) {
                //$sheet->loadView('excel.previred')->with('listaTrabajadores', $listaTrabajadores)->getStyle('A1')->getAlignment();
                $sheet->fromArray($listaTrabajadores);
            });
        })->store('csv', public_path('stories'), true);
        */
        
        
        
        return Response::make(file_get_contents($destination), 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="ArchivoPrevired.csv"'
        ]);   
    }
    
    public function index()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#trabajadores');
        $mes = \Session::get('mesActivo')->mes;
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;
        /*$trabajadores = FichaTrabajador::orderBy('fecha', 'DESC')->groupby('trabajador_id')->distinct()->with('Trabajador')->where('fecha', '<=', $mes)->get();
        $trabajadores = Trabajador::with(array('FichaTrabajador' => function($query){
            $query->where('estado', '=', 'Ingresado')->where('fecha_reconocimiento', '<=', '2017-02-01')->orderBy('fecha', 'DESC')->first();
        }))->get();*/
        $trabajadores = Trabajador::all();
        $listaTrabajadores=array();
        if( $trabajadores->count() ){
            foreach( $trabajadores as $trabajador ){
                $empleado = $trabajador->ficha();
                if($empleado){
                    //if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes || $empleado->estado=='En Creación' || $empleado->estado=='Finiquitado' && $empleado->fecha_finiquito >= $mes){
                        $listaTrabajadores[]=array(    
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'nombreCompleto' => $empleado->nombreCompleto(),      
                            'cargo' => array(
                                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : "",
                            ),             
                            'fechaIngreso' => $empleado->fecha_ingreso,
                            'monedaSueldo' => $empleado->moneda_sueldo,
                            'sueldoBase' => $empleado->sueldo_base,
                            'tipoContrato' => array(
                                'id' => $empleado->tipoContrato ? $empleado->tipoContrato->id : "",
                                'nombre' => $empleado->tipoContrato ? $empleado->tipoContrato->nombre : ""
                            ),
                            'estado' => $empleado->estado,
                            'isContrato' => $trabajador->isContrato()
                        );
                    //}
                }
            }
        }
        
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaTrabajadores
        );
        
        return Response::json($datos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    
    public function input()
    {
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;
        $trabajadores = Trabajador::all();
        
        $listaTrabajadores=array();
        if( $trabajadores->count() ){
            foreach( $trabajadores as $trabajador ){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $listaTrabajadores[]=array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'nombreCompleto' => $empleado->nombreCompleto()
                        );
                    }
                }
            }
        }
        
        
        $datos = array(
            'datos' => $listaTrabajadores
        );
        
        return Response::json($datos);
        
    }
    
    /*public function seccion($sid = null)
    {
        if($sid){
            $seccion = Seccion::whereSid($sid)->first();
            $trabajadores = Trabajador::where('seccion_id', $seccion->id)->where('estado', 'Ingresado')->orderBy('apellidos')->get();
        }else{
            $trabajadores = Trabajador::where('estado', 'Ingresado')->orderBy('apellidos')->get();
        }
        $listaTrabajadores=array();
        if( $trabajadores->count() ){
            foreach( $trabajadores as $trabajador ){
                $listaTrabajadores[]=array(
                    'id' => $trabajador->id,
                    'sid' => $trabajador->sid,
                    'rutFormato' => $trabajador->rut_formato(),
                    'nombreCompleto' => $trabajador->ficha()->nombreCompleto(),
                    'seccion' => array(
                        'id' => $trabajador->ficha()->seccion->id,
                        'sid' => $trabajador->ficha()->seccion->sid,
                        'nombre' => $trabajador->ficha()->seccion->nombre
                    )
                );
            }
        }
        
        
        $datos = array(
            'datos' => $listaTrabajadores
        );
        
        return Response::json($datos);
    }*/
    
    public function ingresados()
    {
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;    
        $trabajadores = Trabajador::all();
        
        $listaTrabajadores=array();
        if( $trabajadores->count() ){
            foreach( $trabajadores as $trabajador ){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $listaTrabajadores[]=array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'celular' => $empleado->celular,
                            'email' => $empleado->email,
                            'cargo' => array(
                                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : "",
                            ),                     
                            'fechaIngreso' => $empleado->fecha_ingreso,
                            'monedaSueldo' => $empleado->moneda_sueldo,
                            'sueldoBase' => $empleado->sueldo_base,
                            'sueldoBasePesos' => Funciones::convertir($empleado->sueldo_base, $empleado->moneda_sueldo),
                            'afp' => array(
                                'id' => $empleado->afp ? $empleado->afp->id : "",
                                'nombre' => $empleado->afp ? $empleado->afp->glosa : ""
                            ),
                            'tipoContrato' => array(
                                'id' => $empleado->tipo_contrato ? $empleado->tipo_contrato->id : "",
                                'nombre' => $empleado->tipo_contrato ? $empleado->tipo_contrato->nombre : ""
                            ),
                            'estado' => $empleado->estado
                        );
                    }
                }
            }
        }
        
        
        $datos = array(
            'datos' => $listaTrabajadores
        );
        
        return Response::json($datos);
    }
    
    public function trabajadoresVacaciones()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;    
        $trabajadores = Trabajador::all();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#trabajadores-vacaciones');
        
        $listaTrabajadores=array();
        if( $trabajadores->count() ){
            foreach( $trabajadores as $trabajador ){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $listaTrabajadores[]=array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'celular' => $empleado->celular,
                            'email' => $empleado->email,
                            'cargo' => array(
                                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : "",
                            ),                     
                            'fechaIngreso' => $empleado->fecha_ingreso,
                            'monedaSueldo' => $empleado->moneda_sueldo,
                            'sueldoBase' => $empleado->sueldo_base,
                            'sueldoBasePesos' => Funciones::convertir($empleado->sueldo_base, $empleado->moneda_sueldo),
                            'afp' => array(
                                'id' => $empleado->afp ? $empleado->afp->id : "",
                                'nombre' => $empleado->afp ? $empleado->afp->glosa : ""
                            ),
                            'tipoContrato' => array(
                                'id' => $empleado->tipo_contrato ? $empleado->tipo_contrato->id : "",
                                'nombre' => $empleado->tipo_contrato ? $empleado->tipo_contrato->nombre : ""
                            ),
                            'estado' => $empleado->estado,
                            'vacaciones' => $trabajador->misVacaciones()
                        );
                    }
                }
            }
        }
        
        
        $datos = array(
            'datos' => $listaTrabajadores,
            'accesos' => $permisos
        );
        
        return Response::json($datos);
    }
    
    public function trabajadoresSemanaCorrida()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#semana-corrida');
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;    
        $trabajadores = Trabajador::all();
        $semanas = MesDeTrabajo::semanas();        
        
        $listaTrabajadores=array();
        if( $trabajadores->count() ){
            foreach( $trabajadores as $trabajador ){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes && $empleado->semana_corrida==1){
                        $listaTrabajadores[]=array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'semanaCorrida' => $trabajador->semanaCorrida()
                        );
                    }
                }
            }
        }        
        
        $datos = array(
            'datos' => $listaTrabajadores,
            'semanas' => $semanas,
            'accesos' => $permisos
        );
        
        return Response::json($datos);
    }
    
    public function updateSemanaCorrida()
    {
        $datos = Input::all();
        
        $id = $datos['id'];
        $semanas = $datos['semanas'];
        $semanaCorrida = SemanaCorrida::find($id);
        
        foreach($semanas as $semana){
            $nombre = $semana['alias'];
            $semanaCorrida->$nombre = $semana['comision'];
        }
        $semanaCorrida->save();
        
        $respuesta = array(
            'success' => true,
            'mensaje' => "La Información fue actualizada correctamente",
            'sid' => $semanaCorrida->sid
        );

        return Response::json($respuesta);
    }
    
    public function trabajadorVacaciones($sid)
    {        
        $trabajador = Trabajador::whereSid($sid)->first();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#trabajadores-vacaciones');
        $mes = \Session::get('mesActivo');
        
        $trabajadorVacaciones = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'rut' => $trabajador->rut,
            'nombreCompleto' => $trabajador->ficha()->nombreCompleto(),
            'vacacionesMesActual' => $trabajador->mesActualVacaciones(),
            'vacaciones' => $trabajador->miHistorialVacaciones()
        );
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorVacaciones
        );
        
        return Response::json($datos);     
    }
    
    public function trabajadoresDocumentos()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#asociar-documentos');
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;    
        $trabajadores = Trabajador::all();
        
        $listaTrabajadores=array();
        if( $trabajadores->count() ){
            foreach( $trabajadores as $trabajador ){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $listaTrabajadores[]=array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'nombreCompleto' => $empleado->nombreCompleto(),                        
                            'cargo' => array(
                                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : "",
                            ),     
                            'totalDocumentos' => $trabajador->totalDocumentos()
                        );
                    }
                }
            }
        }
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaTrabajadores
        );
        
        return Response::json($datos);
    }
    
    public function trabajadorDocumentos($sid)
    {        
        $trabajador = Trabajador::whereSid($sid)->first();
        $empleado = $trabajador->ficha();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#asociar-documentos');
        
        $trabajadorDocumentos = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'nombreCompleto' => $empleado->nombreCompleto(),
            'documentos' => $trabajador->misDocumentos()
        );
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorDocumentos
        );
        
        return Response::json($datos);     
    }
    
    public function trabajadoresCartasNotificacion()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#cartas-de-notificacion');
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;     
        $trabajadores = Trabajador::all();
        
        $listaTrabajadores = array();
        if($trabajadores->count()){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $idTrabajador = $trabajador->id;
                        $listaTrabajadores[]=array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'celular' => $empleado->celular,
                            'email' => $empleado->email,
                            'cargo' => array(
                                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : "",
                            ),                     
                            'fechaIngreso' => $empleado->fecha_ingreso,
                            'monedaSueldo' => $empleado->moneda_sueldo,
                            'sueldoBase' => $empleado->sueldo_base,
                            'sueldoBasePesos' => Funciones::convertir($empleado->sueldo_base, $empleado->moneda_sueldo),
                            'afp' => array(
                                'id' => $empleado->afp ? $empleado->afp->id : "",
                                'nombre' => $empleado->afp ? $empleado->afp->glosa : ""
                            ),
                            'tipoContrato' => array(
                                'id' => $empleado->tipo_contrato ? $empleado->tipo_contrato->id : "",
                                'nombre' => $empleado->tipo_contrato ? $empleado->tipo_contrato->nombre : ""
                            ),
                            'estado' => $empleado->estado,
                            'totalCartasNotificacion' => $trabajador->totalCartasNotificacion()

                        );
                    }
                }
            }
        }
        
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaTrabajadores
        );
        
        return Response::json($datos);
    }
    
    public function trabajadoresCertificados()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#certificados');
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;     
        $trabajadores = Trabajador::all();
        
        $listaTrabajadores = array();
        if($trabajadores->count()){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $idTrabajador = $trabajador->id;
                        $listaTrabajadores[]=array(
                            'id' => $empleado->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'celular' => $empleado->celular,
                            'email' => $empleado->email,
                            'cargo' => array(
                                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : "",
                            ),                     
                            'fechaIngreso' => $empleado->fecha_ingreso,
                            'monedaSueldo' => $empleado->moneda_sueldo,
                            'sueldoBase' => $empleado->sueldo_base,
                            'sueldoBasePesos' => Funciones::convertir($empleado->sueldo_base, $empleado->moneda_sueldo),
                            'afp' => array(
                                'id' => $empleado->afp ? $empleado->afp->glosa : "",
                                'nombre' => $empleado->afp ? $empleado->afp->nombre : ""
                            ),
                            'tipoContrato' => array(
                                'id' => $empleado->tipo_contrato ? $empleado->tipo_contrato->id : "",
                                'nombre' => $empleado->tipo_contrato ? $empleado->tipo_contrato->nombre : ""
                            ),
                            'estado' => $empleado->estado,
                            'totalCertificados' => $trabajador->totalCertificados()
                        );
                    }
                }
            }
        }
        
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaTrabajadores
        );
        
        return Response::json($datos);
    }
    
    public function trabajadorCertificados($sid)
    {        
        $trabajador = Trabajador::whereSid($sid)->first();
        $empleado = $trabajador->ficha();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#certificados');
        
        $trabajadorCertificados = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'nombreCompleto' => $empleado->nombreCompleto(),
            'celular' => $empleado->celular,
            'email' => $empleado->email,
            'cargo' => array(
                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : "",
            ),                     
            'fechaIngreso' => $empleado->fecha_ingreso,
            'monedaSueldo' => $empleado->moneda_sueldo,
            'sueldoBase' => $empleado->sueldo_base,
            'sueldoBasePesos' => Funciones::convertir($empleado->sueldo_base, $empleado->moneda_sueldo),
            'afp' => array(
                'id' => $empleado->afp ? $empleado->afp->id : "",
                'nombre' => $empleado->afp ? $empleado->afp->glosa : ""
            ),
            'tipoContrato' => array(
                'id' => $empleado->tipo_contrato ? $empleado->tipo_contrato->id : "",
                'nombre' => $empleado->tipo_contrato ? $empleado->tipo_contrato->nombre : ""
            ),
            'estado' => $empleado->estado,
            'certificados' => $trabajador->misCertificados()
        );
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorCertificados
        );
        
        return Response::json($datos);     
    }
    
    public function trabajadorCartasNotificacion($sid)
    {        
        $trabajador = Trabajador::whereSid($sid)->first();
        $empleado = $trabajador->ficha();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#cartas-de-notificacion');
        
        $trabajadorCartasNotificacion = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'nombreCompleto' => $empleado->nombreCompleto(),
            'celular' => $empleado->celular,
            'email' => $empleado->email,
            'cargo' => array(
                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : "",
            ),                     
            'fechaIngreso' => $empleado->fecha_ingreso,
            'monedaSueldo' => $empleado->moneda_sueldo,
            'sueldoBase' => $empleado->sueldo_base,
            'sueldoBasePesos' => Funciones::convertir($empleado->sueldo_base, $empleado->moneda_sueldo),
            'afp' => array(
                'id' => $empleado->afp ? $empleado->afp->id : "",
                'nombre' => $empleado->afp ? $empleado->afp->glosa : ""
            ),
            'tipoContrato' => array(
                'id' => $empleado->tipo_contrato ? $empleado->tipo_contrato->id : "",
                'nombre' => $empleado->tipo_contrato ? $empleado->tipo_contrato->nombre : ""
            ),
            'estado' => $empleado->estado,
            'cartasNotificacion' => $trabajador->misCartasNotificacion()
        );
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorCartasNotificacion
        );
        
        return Response::json($datos);     
    }   
    
    public function trabajadorContratos($sid)
    {        
        $trabajador = Trabajador::whereSid($sid)->first();
        $empleado = $trabajador->ficha();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#trabajadores');
        
        $trabajadorContratos = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'nombreCompleto' => $empleado->nombreCompleto(),
            'contratos' => $trabajador->misContratos()
        );
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorContratos
        );
        
        return Response::json($datos);     
    }    
    
    public function trabajadoresLiquidaciones()
    {   
        if(!\Session::get('empresa')){
            return Response::json(array('sinLiquidacion' => array(), 'conLiquidacion' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#liquidaciones-de-sueldo');
        $mes = \Session::get('mesActivo')->mes;
        $finMes = \Session::get('mesActivo')->fechaRemuneracion; 
        $trabajadores = Trabajador::all();
        $liquidaciones = Liquidacion::where('mes', $mes)->orderBy('trabajador_apellidos')->get();
        
        $listaTrabajadores = array();
        $listaLiquidaciones = array();
        
        if($trabajadores->count()){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes || $empleado->estado=='Finiquitado' && $empleado->fecha_finiquito >= $mes){
                        if(!$trabajador->isLiquidacion()){
                            $listaTrabajadores[]=array(
                                'id' => $empleado->id,
                                'sidTrabajador' => $trabajador->sid,
                                'rutFormato' => $trabajador->rut_formato(),
                                'nombreCompleto' => $empleado->nombreCompleto(),
                                'cargo' => $empleado->cargo->nombre,
                                'sueldoBasePesos' => Funciones::convertir($empleado->sueldo_base, $empleado->moneda_sueldo)
                            );
                        }
                    }
                }
            }
        }
        
        if( $liquidaciones->count() ){
            foreach( $liquidaciones as $liquidacion ){
                $listaLiquidaciones[]=array(
                    'id' => $liquidacion->trabajador_id,
                    'sid' => $liquidacion->sid,
                    'sidDocumento' => $liquidacion->documento->sid,
                    'sidTrabajador' => $liquidacion->trabajador->sid,
                    'rutFormato' => $liquidacion->trabajador->rut_formato(),
                    'nombreCompleto' => $liquidacion->trabajador_nombres . ' ' . $liquidacion->trabajador_apellidos,
                    'cargo' => $liquidacion->trabajador_cargo,              
                    'sueldoBasePesos' => $liquidacion->sueldo_base,
                    'sueldoLiquido' => $liquidacion->sueldo_liquido
                );
            }
        }
        
        
        $datos = array(
            'accesos' => $permisos,
            'sinLiquidacion' => $listaTrabajadores,
            'conLiquidacion' => $listaLiquidaciones,
            'cuentas' => Liquidacion::comprobarCuentas($liquidaciones)
        );
        
        return Response::json($datos);
    }
    
    public function planillaCostoEmpresa()
    {   
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        $mes = \Session::get('mesActivo')->mes;
        $liquidaciones = Liquidacion::where('mes', $mes)->orderBy('trabajador_apellidos')->get();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#planilla-costo-empresa');
        $listaTrabajadores = array();
        
        if($liquidaciones->count()){
            foreach( $liquidaciones as $liquidacion ){
                $listaTrabajadores[]=array(
                    'id' => $liquidacion->id,
                    'idTrabajador' => $liquidacion->trabajador_id,
                    'sid' => $liquidacion->sid,
                    'rutFormato' => $liquidacion->trabajador->rut_formato(),
                    'nombreCompleto' => $liquidacion->trabajador->ficha()->nombreCompleto(),
                    'sueldoBasePesos' => $liquidacion->sueldo,
                    'sueldoLiquido' => $liquidacion->sueldo_liquido,
                    'imponibles' => $liquidacion->renta_imponible,
                    'noImponibles' => $liquidacion->no_imponibles,
                    'mutual' => $liquidacion->total_mutual,
                    'seguroCesantia' => $liquidacion->total_seguro_cesantia_empleador,
                    'sis' => $liquidacion->total_afp_empleador,
                    'caja' => $liquidacion->total_salud_caja,
                    'fonasa' => $liquidacion->total_salud_fonasa,
                    'aportes' => $liquidacion->total_aportes
                );
            }
        }
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaTrabajadores
        );
        
        return Response::json($datos);
    }
    
    public function cartaNotificacion()
    {
        $datos = Input::all();
        $sidTrabajador = $datos['sidTrabajador'];
        $sidPlantilla = $datos['sidPlantilla']['sid'];
        $carta = PlantillaCartaNotificacion::whereSid($sidPlantilla)->first();  
        $clausulas = array();
        $trabajador = Trabajador::whereSid($sidTrabajador)->first();
        $empleado = $trabajador->ficha();
        $idEmpresa = \Session::get('empresa')->id;
        $empresa = Empresa::find($idEmpresa);
        
        if($datos['inasistencias']){            
            $inasistencias = $datos['inasistencias'];
        }
                
        $idEmpresa = \Session::get('empresa')->id;
        $empresa = Empresa::find($idEmpresa);
        $comunaEmpresa = $empresa->comuna->comuna;
        $fechaPalabras = Funciones::obtenerFechaTexto();
        $nombreTrabajador = $empleado->nombreCompleto();
        $rutTrabajador = $trabajador->rut_formato();
        $direccionTrabajador = $empleado->direccion;
        $comunaTrabajador =$empleado->comuna->comuna;
        $ciudadTrabajador = $empleado->comuna->provincia->provincia;
        $faltas = "";
        $faltasLineal = "";
        $nombreEmpresa = $empresa->razon_social;
        $rutEmpresa = $empresa->rut_formato();
        
        if($datos['inasistencias']){  
            foreach($inasistencias as $inasistencia){
                $faltas = $faltas . "\n" . Funciones::obtenerFechasTexto($inasistencia);
                $faltasLineal = Funciones::obtenerFechasTextoLineal($inasistencia);
            }
        }
        
        $carta = $this->reemplazarCampos($trabajador, $empleado, $empresa, $carta, $faltas, $faltasLineal);
        $datosTrabajador = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'rut' => $trabajador->rut,
            'nombreCompleto' => $empleado->nombreCompleto(),
            'sexo' => $empleado->sexo,
            'direccion' => $empleado->direccion,
            'comuna' => array(
                'id' => $empleado->comuna->id,
                'nombre' => $empleado->comuna->localidad(),
                'comuna' => $empleado->comuna->comuna,
                'provincia' => $empleado->comuna->provincia->provincia
            ), 
            'telefono' => $empleado->telefono,
            'celular' => $empleado->celular,
            'celularEmpresa' => $empleado->celular_empresa,
            'email' => $empleado->email,
            'emailEmpresa' => $empleado->email_empresa,
            'cargo' => array(
                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
            ),
            'seccion' => array(
                'id' => $empleado->seccion ? $empleado->seccion->id : "",
                'nombre' => $empleado->seccion ? $empleado->seccion->nombre : ""
            ),
            'fechaIngreso' => $empleado->fecha_ingreso

        );
        
        $datos = array(
            'datos' => $carta,
            'trabajador' => $datosTrabajador
        );
        
        return Response::json($datos);
    }
    
    public function contrato()
    {
        $datos = Input::all();
        $sidTrabajador = $datos['sidTrabajador'];
        $sidPlantilla = $datos['sidPlantilla'];
        $clausulas = $datos['clausulas'];
        $trabajador = Trabajador::whereSid($sidTrabajador)->first();
        $empleado = $trabajador->ficha();
        $idTrabajador = $trabajador->id;
        $idEmpresa = \Session::get('empresa')->id;
        $empresa = Empresa::find($idEmpresa);
        
        $contrato = $this->reemplazar($trabajador, $empleado, $empresa, $clausulas, $sidPlantilla);
        
        $datosRepresentante = array(
            'rut' => $empresa->representante_rut,
            'nombreCompleto' => $empresa->representante_nombre,
            'domicilio' => $empresa->representante_direccion . ', comuna de ' . $empresa->comunaRepresentante->comuna . ', de la ciudad de ' . $empresa->comunaRepresentante->provincia->provincia
        );
        
        $datosTrabajador = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'rut' => $trabajador->rut,
            'nombreCompleto' => $empleado->nombreCompleto(),
            'nacionalidad' => array(
                'id' => $empleado->nacionalidad ? $empleado->nacionalidad->id : "",
                'nombre' => $empleado->nacionalidad ? $empleado->nacionalidad->glosa : ""
            ),
            'sexo' => $trabajador->sexo,
            'estadoCivil' => array(
                'id' => $empleado->estadoCivil ? $empleado->estadoCivil->id : "",
                'nombre' => $empleado->estadoCivil ? $empleado->estadoCivil->nombre : ""
            ),
            'fechaNacimiento' => $empleado->fecha_nacimiento,
            'direccion' => $empleado->direccion,
            'domicilio' => $empleado->domicilio(),
            'comuna' => array(
                'id' => $empleado->comuna ? $empleado->comuna->id : "",
                'nombre' => $empleado->comuna ? $empleado->comuna->localidad() : "",
                'comuna' => $empleado->comuna ? $empleado->comuna->comuna : "",
                'provincia' => $empleado->comuna ? $empleado->comuna->provincia->provincia : ""
            ), 
            'telefono' => $empleado->telefono,
            'celular' => $empleado->celular,
            'celularEmpresa' => $empleado->celular_empresa,
            'email' => $empleado->email,
            'emailEmpresa' => $empleado->email_empresa,
            'cargo' => array(
                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
            ),
            'titulo' => array(
                'id' => $empleado->titulo ? $empleado->titulo->id : "",
                'nombre' => $empleado->titulo ? $empleado->titulo->nombre : ""
            ),
            'seccion' => array(
                'id' => $empleado->seccion ? $empleado->seccion->id : "",
                'nombre' => $empleado->seccion ? $empleado->seccion->nombre : ""
            ),
            'tipoCuenta' => array(
                'id' => $empleado->tipoCuenta ? $empleado->tipoCuenta->id : "",
                'nombre' => $empleado->tipoCuenta ? $empleado->tipoCuenta->nombre : ""
            ),
            'banco' => array(
                'id' => $empleado->banco ? $empleado->banco->id : "",
                'nombre' => $empleado->banco ? $empleado->banco->nombre : ""
            ),
            'numeroCuenta' => $empleado->numero_cuenta,
            'fechaIngreso' => $empleado->fecha_ingreso,
            'fechaReconocimiento' => $empleado->fecha_reconocimiento,
            'tipoContrato' => array(
                'id' => $empleado->tipoContrato ? $empleado->tipoContrato->id : "",
                'nombre' => $empleado->tipoContrato ? $empleado->tipoContrato->nombre : ""
            ),
            'fechaVencimiento' => $empleado->fecha_vencimiento ? $empleado->fecha_vencimiento : "",
            'tipoJornada' => array(
                'id' => $empleado->tipoJornada ? $empleado->tipoJornada->id : "",
                'nombre' => $empleado->tipoJornada ? $empleado->tipoJornada->nombre : ""
            ),
            'semanaCorrida' => $empleado->semana_corrida ? true : false,
            'monedaSueldo' => $empleado->moneda_sueldo,
            'sueldoBase' => $empleado->sueldo_base,
            'tipoTrabajador' => $empleado->tipo_trabajador,
            'excesoRetiro' => $empleado->exceso_retiro,
            'monedaColacion' => $empleado->moneda_colacion,
            'montoColacion' => $empleado->monto_colacion,
            'monedaMovilizacion' => $empleado->moneda_movilizacion,
            'montoMovilizacion' => $empleado->monto_movilizacion,
            'monedaViatico' => $empleado->moneda_viatico,
            'montoViatico' => $empleado->monto_viatico,
            'afp' => array(
                'id' => $empleado->afp ? $empleado->afp->id : "",
                'nombre' => $empleado->afp ? $empleado->afp->glosa : ""
            ),
            'seguroDesempleo' => $empleado->seguro_desempleo ? true : false,
            'afpSeguro' => array(
                'id' => $empleado->afpSeguro ? $empleado->afpSeguro->id : "",
                'nombre' => $empleado->afpSeguro ? $empleado->afpSeguro->glosa : ""
            ),
            'isapre' => array(
                'id' => $empleado->isapre ? $empleado->isapre->id : "",
                'nombre' => $empleado->isapre ? $empleado->isapre->glosa : ""
            ),
            'cotizacionIsapre' => $empleado->cotizacion_isapre,
            'montoIsapre' => $empleado->monto_isapre,
            'sindicato' => $empleado->sindicato ? true : false,
            'monedaSindicato' => $empleado->moneda_sindicato,
            'montoSindicato' => $empleado->monto_sindicato,
            'estado' => $empleado->estado,
            'apvs' => $empleado->misApvs(),
            'haberes' => $trabajador->misHaberes(),
            'descuentos' => $trabajador->misDescuentos(),
            'prestamos' => $trabajador->misPrestamos(),
            'cargas' => $empleado->misCargas()

        );
        
        $datos = array(
            'datos' => $contrato,
            'trabajador' => $datosTrabajador,
            'representante' => $datosRepresentante,
            'empresa' => array(
                'domicilio' => $empresa->domicilio()
            )
        );
        
        return Response::json($datos);
    }
    
    public function finiquito()
    {
        $datos = Input::all();
        $sidTrabajador = $datos['sidTrabajador'];
        $sidPlantilla = $datos['sidPlantilla'];
        $plantilla = PlantillaFiniquito::whereSid($sidPlantilla)->first();
        $idCausal = $datos['idCausal'];
        $totalFiniquito = $datos['totalFiniquito'];
        $causal = CausalFiniquito::find($idCausal);
        $fechaFiniquito = $datos['fecha'];
        $clausulas = $datos['clausulas'];
        $trabajador = Trabajador::whereSid($sidTrabajador)->first();
        $empleado = $trabajador->ficha();
        $idTrabajador = $trabajador->id;
        $idEmpresa = \Session::get('empresa')->id;
        $empresa = Empresa::find($idEmpresa);
        
        $detalleFiniquito = $this->detalleFiniquito($datos);
        $finiquito = $this->reemplazarFiniquito($trabajador, $empleado, $empresa, $clausulas, $plantilla, $causal, $fechaFiniquito, $totalFiniquito, $detalleFiniquito);
        $finiquito->cuerpo = '<html><head><style>table {width: 100%; border-collapse: collapse;} th {height: 50px;} td {padding: 8px;} tr:nth-child(even) {background-color: #f2f2f2} </style></head><body>' . $finiquito->cuerpo . '</body></html>';
        
        $datosRepresentante = array(
            'rut' => $empresa->representante_rut,
            'nombreCompleto' => $empresa->representante_nombre,
            'domicilio' => $empresa->representante_direccion . ', comuna de ' . $empresa->comunaRepresentante->comuna . ', de la ciudad de ' . $empresa->comunaRepresentante->provincia->provincia
        );
        
        $datosTrabajador = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'rut' => $trabajador->rut,
            'nombreCompleto' => $empleado->nombreCompleto(),
            'nacionalidad' => array(
                'id' => $empleado->nacionalidad ? $empleado->nacionalidad->id : "",
                'nombre' => $empleado->nacionalidad ? $empleado->nacionalidad->glosa : ""
            ),
            'sexo' => $trabajador->sexo,
            'estadoCivil' => array(
                'id' => $empleado->estadoCivil ? $empleado->estadoCivil->id : "",
                'nombre' => $empleado->estadoCivil ? $empleado->estadoCivil->nombre : ""
            ),
            'fechaNacimiento' => $empleado->fecha_nacimiento,
            'direccion' => $empleado->direccion,
            'domicilio' => $empleado->domicilio(),
            'comuna' => array(
                'id' => $empleado->comuna ? $empleado->comuna->id : "",
                'nombre' => $empleado->comuna ? $empleado->comuna->localidad() : "",
                'comuna' => $empleado->comuna ? $empleado->comuna->comuna : "",
                'provincia' => $empleado->comuna ? $empleado->comuna->provincia->provincia : ""
            ), 
            'telefono' => $empleado->telefono,
            'celular' => $empleado->celular,
            'celularEmpresa' => $empleado->celular_empresa,
            'email' => $empleado->email,
            'emailEmpresa' => $empleado->email_empresa,
            'cargo' => array(
                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
            ),
            'titulo' => array(
                'id' => $empleado->titulo ? $empleado->titulo->id : "",
                'nombre' => $empleado->titulo ? $empleado->titulo->nombre : ""
            ),
            'seccion' => array(
                'id' => $empleado->seccion ? $empleado->seccion->id : "",
                'nombre' => $empleado->seccion ? $empleado->seccion->nombre : ""
            ),
            'tipoCuenta' => array(
                'id' => $empleado->tipoCuenta ? $empleado->tipoCuenta->id : "",
                'nombre' => $empleado->tipoCuenta ? $empleado->tipoCuenta->nombre : ""
            ),
            'banco' => array(
                'id' => $empleado->banco ? $empleado->banco->id : "",
                'nombre' => $empleado->banco ? $empleado->banco->nombre : ""
            ),
            'numeroCuenta' => $empleado->numero_cuenta,
            'fechaIngreso' => $empleado->fecha_ingreso,
            'fechaReconocimiento' => $empleado->fecha_reconocimiento,
            'tipoContrato' => array(
                'id' => $empleado->tipoContrato ? $empleado->tipoContrato->id : "",
                'nombre' => $empleado->tipoContrato ? $empleado->tipoContrato->nombre : ""
            ),
            'fechaVencimiento' => $empleado->fecha_vencimiento ? $empleado->fecha_vencimiento : "",
            'tipoJornada' => array(
                'id' => $empleado->tipoJornada ? $empleado->tipoJornada->id : "",
                'nombre' => $empleado->tipoJornada ? $empleado->tipoJornada->nombre : ""
            ),
            'semanaCorrida' => $empleado->semana_corrida ? true : false,
            'monedaSueldo' => $empleado->moneda_sueldo,
            'sueldoBase' => $empleado->sueldo_base,
            'tipoTrabajador' => $empleado->tipo_trabajador,
            'excesoRetiro' => $empleado->exceso_retiro,
            'monedaColacion' => $empleado->moneda_colacion,
            'montoColacion' => $empleado->monto_colacion,
            'monedaMovilizacion' => $empleado->moneda_movilizacion,
            'montoMovilizacion' => $empleado->monto_movilizacion,
            'monedaViatico' => $empleado->moneda_viatico,
            'montoViatico' => $empleado->monto_viatico,
            'afp' => array(
                'id' => $empleado->afp ? $empleado->afp->id : "",
                'nombre' => $empleado->afp ? $empleado->afp->glosa : ""
            ),
            'seguroDesempleo' => $empleado->seguro_desempleo ? true : false,
            'afpSeguro' => array(
                'id' => $empleado->afpSeguro ? $empleado->afpSeguro->id : "",
                'nombre' => $empleado->afpSeguro ? $empleado->afpSeguro->glosa : ""
            ),
            'isapre' => array(
                'id' => $empleado->isapre ? $empleado->isapre->id : "",
                'nombre' => $empleado->isapre ? $empleado->isapre->glosa : ""
            ),
            'cotizacionIsapre' => $empleado->cotizacion_isapre,
            'montoIsapre' => $empleado->monto_isapre,
            'sindicato' => $empleado->sindicato ? true : false,
            'monedaSindicato' => $empleado->moneda_sindicato,
            'montoSindicato' => $empleado->monto_sindicato,
            'estado' => $empleado->estado,
            'apvs' => $empleado->misApvs(),
            'haberes' => $trabajador->misHaberes(),
            'descuentos' => $trabajador->misDescuentos(),
            'prestamos' => $trabajador->misPrestamos(),
            'cargas' => $empleado->misCargas()

        );
        
        $datos = array(
            'datos' => $finiquito,
            'vacaciones' => $datos['vacaciones'],
            'indemnizacion' => $datos['indemnizacion'],
            'mesAviso' => $datos['mesAviso'],
            'sueldoNormal' => $datos['sueldoNormal'],
            'sueldoVariable' => $datos['sueldoVariable'],
            'totalFiniquito' => $totalFiniquito,
            'causal' =>array(
                'id' => $causal->id,
                'sid' => $causal->sid,
                'nombre' => $causal->nombre
            ),
            'fecha' => $fechaFiniquito,
            'plantilla' => $plantilla,
            'trabajador' => $datosTrabajador,
            'representante' => $datosRepresentante,
            'empresa' => array(
                'domicilio' => $empresa->domicilio()
            )
        );
        
        return Response::json($datos);
    }
    
    public function detalleFiniquito($datos)
    {            
        $table = '<div class="mceNonEditable">';
        $table .= '<table>';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th>CONCEPTO</th>';
        $table .= '<th>MONTO</th>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
        
        if($datos['mesAviso']['mesAviso']){
            
            if($datos['sueldoVariable']){
                $mesAviso = '(promedio Renta Imponible últimos ' . $datos['mesAviso']['meses'] . ' meses)';
            }else{
                $mesAviso = '(Renta Imponible ' . $datos['detalle'][0]['mes'] . ')';
            }
            
            $table .= '<tr><td>Mes de Aviso ' . $mesAviso . '</td><td>' . Funciones::formatoPesos($datos['mesAviso']['imponibles']['suma']) . '</td></tr>';
        }
        
        if($datos['noImponibles']){
            
            if($datos['sueldoVariable']){
                $noImponibles = '(promedio últimos ' . $datos['mesAviso']['meses'] . ' meses)';
            }else{
                $noImponibles = '(' . $datos['detalle'][0]['mes'] . ')';
            }
            
            $table .= '<tr><td>No Imponibles ' . $noImponibles . '</td><td>' . Funciones::formatoPesos($datos['mesAviso']['noImponibles']['suma']) . '</td></tr>';
        }
        
        if($datos['indemnizacion']['indemnizacion']){
            $table .= '<tr><td>Indemización Años de Servicio (' . $datos['indemnizacion']['anios'] . ' años)</td><td>' . Funciones::formatoPesos($datos['indemnizacion']['monto']) . '</td></tr>';
        }
        if($datos['vacaciones']['vacaciones']){
            $table .= '<tr><td>Vacaciones disponibles (' . $datos['vacaciones']['dias'] . ' días)</td><td>' . Funciones::formatoPesos($datos['vacaciones']['monto']) . '</td></tr>';
        }
        
        if($datos['otros']){
            
            foreach($datos['otros'] as $otro){
                $table .= '<tr><td colspan="2">' . $otro['nombre'] . '</td></tr>';
                foreach($otro['detalles'] as $detalle){
                    $table .= '<tr><td>' . $detalle['nombre'] . '</td><td>' . Funciones::formatoPesos(Funciones::convertir($detalle['monto'], $detalle['moneda'])) . '</td></tr>';
                }
            }
            
            /*if($datos['sueldoVariable']){
                $noImponibles = '(promedio últimos ' . $datos['mesAviso']['meses'] . ' meses)';
            }else{
                $noImponibles = '(' . $datos['detalle'][0]['mes'] . ')';
            }
            
            $table .= '<tr><td>No Imponibles ' . $noImponibles . '</td><td>' . Funciones::formatoPesos($datos['mesAviso']['noImponibles']['suma']) . '</td></tr>';*/
        }
        
        $table .= '</tbody>';
        $table .= '<tfoot>';
        $table .= '<tr>';
        $table .= '<td><b>Total: </b></td>';
        $table .= '<td><b>' . Funciones::formatoPesos($datos['totalFiniquito']) . '</b></td>';
        $table .= '</tr>';
        $table .= '</tfoot>';
        $table .= '</table>';
        $table .= '</div>';
        
        return $table;
    }
    
    public function certificado()
    {
        $datos = Input::all();
        $sidTrabajador = $datos['sidTrabajador'];
        $sidPlantilla = $datos['sidPlantilla']['sid'];
        $certificado = PlantillaCertificado::whereSid($sidPlantilla)->first();  
        $tipo = $certificado->nombre;
        $clausulas = array();
        $trabajador = Trabajador::whereSid($sidTrabajador)->first();
        $empleado = $trabajador->ficha();
        $idEmpresa = \Session::get('empresa')->id;
        $empresa = Empresa::find($idEmpresa);
        
        $idEmpresa = \Session::get('empresa')->id;
        $empresa = Empresa::find($idEmpresa);
        $comunaEmpresa = $empresa->comuna->comuna;
        $fechaPalabras = Funciones::obtenerFechaTexto();
        $nombreTrabajador = $empleado->nombreCompleto();
        $rutTrabajador = $trabajador->rut_formato();
        $direccionTrabajador = $empleado->direccion;
        $comunaTrabajador = $empleado->comuna->comuna;
        $ciudadTrabajador = $empleado->comuna->provincia->provincia;
        $faltas = "";
        $faltasLineal = "";
        $nombreEmpresa = $empresa->razon_social;
        $rutEmpresa = $empresa->rut_formato();
        
        
        $certificado = $this->reemplazarCampos($trabajador, $empleado, $empresa, $certificado, $faltas, $faltasLineal);
        $certificado->tipo = $tipo;
        
        $datosTrabajador = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'rut' => $trabajador->rut,
            'nombreCompleto' => $empleado->nombreCompleto(),
            'sexo' => $empleado->sexo,
            'direccion' => $empleado->direccion,
            'comuna' => array(
                'id' => $empleado->comuna->id,
                'nombre' => $empleado->comuna->localidad(),
                'comuna' => $empleado->comuna->comuna,
                'provincia' => $empleado->comuna->provincia->provincia
            ), 
            'telefono' => $empleado->telefono,
            'celular' => $empleado->celular,
            'celularEmpresa' => $empleado->celular_empresa,
            'email' => $empleado->email,
            'emailEmpresa' => $empleado->email_empresa,
            'cargo' => array(
                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
            ),
            'seccion' => array(
                'id' => $empleado->seccion ? $empleado->seccion->id : "",
                'nombre' => $empleado->seccion ? $empleado->seccion->nombre : ""
            ),
            'fechaIngreso' => $empleado->fecha_ingreso

        );
        
        $datos = array(
            'datos' => $certificado,
            'trabajador' => $datosTrabajador
        );
        
        return Response::json($datos);
    }
    
    public function reemplazar($trabajador, $ficha, $empresa, $clausulas, $sidPlantilla)
    {        
        $contrato = PlantillaContrato::whereSid($sidPlantilla)->first();        
        $textoClausulas = "";
        
        $comunaEmpresa = $empresa->comuna->comuna;
        $fechaPalabras = Funciones::obtenerFechaTexto();
        $fechaActual = Funciones::obtenerFechaActual();
        $nombreEmpresa = $empresa->razon_social;
        $rutEmpresa = $empresa->rut_formato();
        $nombreRepresentante = $empresa->representante_nombre;
        $rutRepresentante = Funciones::formatear_rut($empresa->representante_rut);
        $domicilioRepresentante = $empresa->representante_direccion . ', comuna de ' . $empresa->comunaRepresentante->comuna . ', de la ciudad de ' . $empresa->comunaRepresentante->provincia->provincia;
        $domicilioEmpresa = $empresa->domicilio();
        $nombreTrabajador = $ficha->nombreCompleto();
        $nacionalidadTrabajador = $ficha->nacionalidad->glosa;
        $rutTrabajador = $trabajador->rut_formato();
        $estadoCivilTrabajador = $ficha->estadoCivil->nombre;
        $fechaNacimientoPalabrasTrabajador = Funciones::obtenerFechaTexto($ficha->fecha_nacimiento);
        $cargoTrabajador = $ficha->cargo->nombre;
        $domicilioTrabajador = $ficha->domicilio();
        $trabajadorAfp = $ficha->afp->glosa;
        $trabajadorIsapre = $ficha->isapre->glosa;
        
        $sb = Funciones::convertir($ficha->sueldo_base, $ficha->moneda_sueldo);
        $sueldoBase = Funciones::formatoPesos($sb);
        $sueldoBasePalabras = Funciones::convertirPalabras($sb);
        $col = Funciones::convertir($ficha->monto_colacion, $ficha->moneda_colacion);
        $colacion = Funciones::formatoPesos($col);
        $colacionPalabras = Funciones::convertirPalabras($col);
        $mov = Funciones::convertir($ficha->monto_movilizacion, $ficha->moneda_movilizacion);
        $movilizacion = Funciones::formatoPesos($mov);
        $movilizacionPalabras = Funciones::convertirPalabras($mov);
        $via = Funciones::convertir($ficha->monto_viatico, $ficha->moneda_viatico);
        $viatico = Funciones::formatoPesos($via);
        $viaticoPalabras = Funciones::convertirPalabras($via);
        $fechaInicial = $ficha->fecha_reconocimiento;
        $fechaInicialPalabras = Funciones::obtenerFechaTexto($fechaInicial);
        $fechaTermino = $ficha->fecha_termino ? $ficha->fecha_termino : "";
        $fechaTerminoPalabras = $ficha->fecha_termino ? Funciones::obtenerFechaTexto($fechaTermino) : "";
        
        $direccionTrabajador = $ficha->direccion;
        $comunaTrabajador = $ficha->comuna->comuna;
        $ciudadTrabajador = $ficha->comuna->provincia->provincia;        
        $ciudadEmpresa = $empresa->comuna->provincia->provincia;        
        $contratoTrabajador = $ficha->tipoContrato->nombre;
        $vigenciaContrato = $ficha->vigenciaContrato();
        
        $index = 1;
        foreach($clausulas as $clausula){
            $textoClausulas =  $textoClausulas . '<br /><b>' . Funciones::obtenerOrdinalTexto($index) . "." . $clausula['nombre'] . ".</b>" . $clausula['clausula'] . "<br />";
            $index++;
        }
        
        $contrato->cuerpo = str_replace('${clausulas}', $textoClausulas, $contrato->cuerpo);
        
        $var = array('${comunaEmpresa}', '${fechaActual}', '${fechaPalabras}', '${nombreEmpresa}', '${rutEmpresa}', '${nombreRepresentante}', '${rutRepresentante}', '${domicilioRepresentante}', '${domicilioEmpresa}', '${nombreTrabajador}', '${nacionalidadTrabajador}', '${rutTrabajador}', '${estadoCivilTrabajador}', '${fechaNacimientoPalabrasTrabajador}', '${cargoTrabajador}', '${domicilioTrabajador}', '${trabajadorAfp}', '${trabajadorIsapre}', '${sueldoBase}', '${sueldoBasePalabras}', '${colacion}', '${colacionPalabras}', '${movilizacion}', '${movilizacionPalabras}','${viatico}', '${viaticoPalabras}', '${fechaInicial}', '${fechaInicialPalabras}', '${fechaTermino}', '${fechaTerminoPalabras}', '${direccionTrabajador}', '${comunaTrabajador}', '${ciudadTrabajador}', '${contratoTrabajador}', '${vigenciaContrato}', '${ciudadEmpresa}');
        
        $replace = array($comunaEmpresa, $fechaActual, $fechaPalabras, $nombreEmpresa, $rutEmpresa, $nombreRepresentante, $rutRepresentante, $domicilioRepresentante, $domicilioEmpresa, $nombreTrabajador, $nacionalidadTrabajador, $rutTrabajador, $estadoCivilTrabajador, $fechaNacimientoPalabrasTrabajador, $cargoTrabajador, $domicilioTrabajador, $trabajadorAfp, $trabajadorIsapre, $sueldoBase, $sueldoBasePalabras, $colacion, $colacionPalabras, $movilizacion, $movilizacionPalabras, $viatico, $viaticoPalabras, $fechaInicial, $fechaInicialPalabras, $fechaTermino, $fechaTerminoPalabras, $direccionTrabajador, $comunaTrabajador, $ciudadTrabajador, $contratoTrabajador, $vigenciaContrato, $ciudadEmpresa);
        
        $contrato->cuerpo = str_replace($var, $replace, $contrato->cuerpo);    
        
        return $contrato;
    }
    
    public function reemplazarFiniquito($trabajador, $ficha, $empresa, $clausulas, $plantilla, $causal, $fechaFiniquito, $totalFiniquito, $detalleFiniquito)
    {        
        $finiquito = $plantilla;     
        $textoClausulas = "";
        
        $causalFiniquito = $causal->nombre;
        $numeroArticulo = $causal->articulo;
        $numeroCodigo = $causal->codigo;
        $comunaEmpresa = $empresa->comuna->comuna;
        $fechaPalabras = Funciones::obtenerFechaTexto();
        $totalFiniquitoPalabras = Funciones::convertirPalabras($totalFiniquito);
        $totalFiniquito = Funciones::formatoPesos($totalFiniquito);
        $nombreEmpresa = $empresa->razon_social;
        $rutEmpresa = $empresa->rut_formato();
        $nombreRepresentante = $empresa->representante_nombre;
        $rutRepresentante = Funciones::formatear_rut($empresa->representante_rut);
        $domicilioRepresentante = $empresa->representante_direccion . ', comuna de ' . $empresa->comunaRepresentante->comuna . ', de la ciudad de ' . $empresa->comunaRepresentante->provincia->provincia;
        $domicilioEmpresa = $empresa->domicilio();
        $nombreTrabajador = $ficha->nombreCompleto();
        $nacionalidadTrabajador = $ficha->nacionalidad->glosa;
        $rutTrabajador = $trabajador->rut_formato();
        $estadoCivilTrabajador = $ficha->estadoCivil->nombre;
        $fechaNacimientoPalabrasTrabajador = Funciones::obtenerFechaTexto($ficha->fecha_nacimiento);
        $fechaFiniquitoPalabras = Funciones::obtenerFechaTexto($fechaFiniquito);
        $cargoTrabajador = $ficha->cargo->nombre;
        $domicilioTrabajador = $ficha->domicilio();
        $trabajadorAfp = $ficha->afp->glosa;
        $trabajadorIsapre = $ficha->isapre->glosa;
        
        $sb = Funciones::convertir($ficha->sueldo_base, $ficha->moneda_sueldo);
        $sueldoBase = Funciones::formatoPesos($sb);
        $sueldoBasePalabras = Funciones::convertirPalabras($sb);
        $col = Funciones::convertir($ficha->monto_colacion, $ficha->moneda_colacion);
        $colacion = Funciones::formatoPesos($col);
        $colacionPalabras = Funciones::convertirPalabras($col);
        $mov = Funciones::convertir($ficha->monto_movilizacion, $ficha->moneda_movilizacion);
        $movilizacion = Funciones::formatoPesos($mov);
        $movilizacionPalabras = Funciones::convertirPalabras($mov);
        $via = Funciones::convertir($ficha->monto_viatico, $ficha->moneda_viatico);
        $viatico = Funciones::formatoPesos($via);
        $viaticoPalabras = Funciones::convertirPalabras($via);
        $fechaInicial = $ficha->fecha_reconocimiento;
        $fechaInicialPalabras = Funciones::obtenerFechaTexto($fechaInicial);
        $fechaTermino = $ficha->fecha_termino ? $ficha->fecha_termino : "";
        $fechaTerminoPalabras = $ficha->fecha_termino ? Funciones::obtenerFechaTexto($fechaTermino) : "";
        
        $direccionTrabajador = $ficha->direccion;
        $comunaTrabajador = $ficha->comuna->comuna;
        $ciudadTrabajador = $ficha->comuna->provincia->provincia;        
        $ciudadEmpresa = $empresa->comuna->provincia->provincia;        
        $contratoTrabajador = $ficha->tipoContrato->nombre;
        
        $index = 1;
        foreach($clausulas as $clausula){
            $textoClausulas =  $textoClausulas . '<br /><b>' . Funciones::obtenerOrdinalTexto($index) . "." . $clausula['nombre'] . ".</b>" . $clausula['clausula'] . "<br />";
            $index++;
        }
        
        $finiquito->cuerpo = str_replace('${clausulas}', $textoClausulas, $finiquito->cuerpo);        
        $var = array('${comunaEmpresa}', '${fechaPalabras}', '${nombreEmpresa}', '${rutEmpresa}', '${nombreRepresentante}', '${rutRepresentante}', '${domicilioRepresentante}', '${domicilioEmpresa}', '${nombreTrabajador}', '${nacionalidadTrabajador}', '${rutTrabajador}', '${estadoCivilTrabajador}', '${fechaNacimientoPalabrasTrabajador}', '${cargoTrabajador}', '${domicilioTrabajador}', '${trabajadorAfp}', '${trabajadorIsapre}', '${sueldoBase}', '${sueldoBasePalabras}', '${colacion}', '${colacionPalabras}', '${movilizacion}', '${movilizacionPalabras}','${viatico}', '${viaticoPalabras}', '${fechaInicial}', '${fechaInicialPalabras}', '${fechaTermino}', '${fechaTerminoPalabras}', '${direccionTrabajador}', '${comunaTrabajador}', '${ciudadTrabajador}', '${contratoTrabajador}', '${ciudadEmpresa}', '${fechaFiniquito}', '${fechaFiniquitoPalabras}', '${causalFiniquito}','${numeroArticulo}', '${numeroCodigo}', '${totalFiniquito}', '${totalFiniquitoPalabras}', '${detalleFiniquito}');
        
        $replace = array($comunaEmpresa, $fechaPalabras, $nombreEmpresa, $rutEmpresa, $nombreRepresentante, $rutRepresentante, $domicilioRepresentante, $domicilioEmpresa, $nombreTrabajador, $nacionalidadTrabajador, $rutTrabajador, $estadoCivilTrabajador, $fechaNacimientoPalabrasTrabajador, $cargoTrabajador, $domicilioTrabajador, $trabajadorAfp, $trabajadorIsapre, $sueldoBase, $sueldoBasePalabras, $colacion, $colacionPalabras, $movilizacion, $movilizacionPalabras, $viatico, $viaticoPalabras, $fechaInicial, $fechaInicialPalabras, $fechaTermino, $fechaTerminoPalabras, $direccionTrabajador, $comunaTrabajador, $ciudadTrabajador, $contratoTrabajador, $ciudadEmpresa, $fechaFiniquito, $fechaFiniquitoPalabras, $causalFiniquito, $numeroArticulo, $numeroCodigo, $totalFiniquito, $totalFiniquitoPalabras, $detalleFiniquito);
        
        $finiquito->cuerpo = str_replace($var, $replace, $finiquito->cuerpo);    
        
        return $finiquito;
    }
    
    public function reemplazarCampos($trabajador, $ficha, $empresa, $documento, $faltas, $faltasLineal)
    {                              
        $comunaEmpresa = $empresa->comuna->comuna;
        $fechaPalabras = Funciones::obtenerFechaTexto();
        $nombreEmpresa = $empresa->razon_social;
        $rutEmpresa = $empresa->rut_formato();
        $nombreRepresentante = $empresa->representante_nombre;
        $rutRepresentante = Funciones::formatear_rut($empresa->representante_rut);
        $domicilioRepresentante = $empresa->representante_direccion . ', comuna de ' . $empresa->comunaRepresentante->comuna . ', de la ciudad de ' . $empresa->comunaRepresentante->provincia->provincia;
        $domicilioEmpresa = $empresa->domicilio();
        $nombreTrabajador = $ficha->nombreCompleto();
        $nacionalidadTrabajador = $ficha->nacionalidad->glosa;
        $rutTrabajador = $trabajador->rut_formato();
        $estadoCivilTrabajador = $ficha->estadoCivil->nombre;
        $fechaNacimientoPalabrasTrabajador = Funciones::obtenerFechaTexto($ficha->fecha_nacimiento);
        $cargoTrabajador = $ficha->cargo->nombre;
        $domicilioTrabajador = $ficha->domicilio();
        $trabajadorAfp = $ficha->afp->glosa;
        $trabajadorIsapre = $ficha->isapre->glosa;
        
        $sb = Funciones::convertir($ficha->sueldo_base, $ficha->moneda_sueldo);
        $sueldoBase = Funciones::formatoPesos($sb);
        $sueldoBasePalabras = Funciones::convertirPalabras($sb);
        $col = Funciones::convertir($ficha->monto_colacion, $ficha->moneda_colacion);
        $colacion = Funciones::formatoPesos($col);
        $colacionPalabras = Funciones::convertirPalabras($col);
        $mov = Funciones::convertir($ficha->monto_movilizacion, $ficha->moneda_movilizacion);
        $movilizacion = Funciones::formatoPesos($mov);
        $movilizacionPalabras = Funciones::convertirPalabras($mov);
        $via = Funciones::convertir($ficha->monto_viatico, $ficha->moneda_viatico);
        $viatico = Funciones::formatoPesos($via);
        $viaticoPalabras = Funciones::convertirPalabras($via);
        $fechaInicial = $ficha->fecha_reconocimiento;
        $fechaInicialPalabras = Funciones::obtenerFechaTexto($fechaInicial);
        $fechaTermino = $ficha->fecha_termino ? $ficha->fecha_termino : "";
        $fechaTerminoPalabras = $ficha->fecha_termino ? Funciones::obtenerFechaTexto($fechaTermino) : "";
        $direccionTrabajador = $ficha->direccion;
        $comunaTrabajador = $ficha->comuna->comuna;
        $ciudadTrabajador = $ficha->comuna->provincia->provincia;
        $ciudadEmpresa = $empresa->comuna->provincia->provincia;
        $contratoTrabajador = $ficha->tipoContrato->nombre;
                
        $var = array('${comunaEmpresa}', '${fechaPalabras}', '${nombreEmpresa}', '${rutEmpresa}', '${nombreRepresentante}', '${rutRepresentante}', '${domicilioRepresentante}', '${domicilioEmpresa}', '${nombreTrabajador}', '${nacionalidadTrabajador}', '${rutTrabajador}', '${estadoCivilTrabajador}', '${fechaNacimientoPalabrasTrabajador}', '${cargoTrabajador}', '${domicilioTrabajador}', '${trabajadorAfp}', '${trabajadorIsapre}', '${sueldoBase}', '${sueldoBasePalabras}', '${colacion}', '${colacionPalabras}', '${movilizacion}', '${movilizacionPalabras}','${viatico}', '${viaticoPalabras}', '${fechaInicial}', '${fechaInicialPalabras}', '${fechaTermino}', '${fechaTerminoPalabras}', '${direccionTrabajador}', '${comunaTrabajador}', '${ciudadTrabajador}', '${faltas}', '${faltasLineal}', '${contratoTrabajador}', '${ciudadEmpresa}');
        
        $replace = array($comunaEmpresa, $fechaPalabras, $nombreEmpresa, $rutEmpresa, $nombreRepresentante, $rutRepresentante, $domicilioRepresentante, $domicilioEmpresa, $nombreTrabajador, $nacionalidadTrabajador, $rutTrabajador, $estadoCivilTrabajador, $fechaNacimientoPalabrasTrabajador, $cargoTrabajador, $domicilioTrabajador, $trabajadorAfp, $trabajadorIsapre, $sueldoBase, $sueldoBasePalabras, $colacion, $colacionPalabras, $movilizacion, $movilizacionPalabras, $viatico, $viaticoPalabras, $fechaInicial, $fechaInicialPalabras, $fechaTermino, $fechaTerminoPalabras, $direccionTrabajador, $comunaTrabajador, $ciudadTrabajador, $faltas, $faltasLineal, $contratoTrabajador, $ciudadEmpresa);
        
        $documento->cuerpo = str_replace($var, $replace, $documento->cuerpo);    
        
        return $documento;
    }
    
    public function reajuste()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array(), 'rmi' => null));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#reajuste-global');
        $mesActual = \Session::get('mesActivo');
        $finMes = $mesActual->fechaRemuneracion;
        $mes = $mesActual->mes;
        
        $rmi = RentaMinimaImponible::where('mes', $mes)->where('nombre', 'Trab. Dependientes e Independientes')->first()->valor;
        $trabajadores = Trabajador::all();
        $listaTrabajadores=array();
        
        if( $trabajadores->count() ){
            foreach( $trabajadores as $trabajador ){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes && Funciones::convertir($empleado->sueldo_base, $empleado->moneda_sueldo)<$rmi){
                        $listaTrabajadores[]=array(
                            'id' => $empleado->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'celular' => $empleado->celular,
                            'email' => $empleado->email,
                            'cargo' => array(
                                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : "",
                            ),                    
                            'fechaIngreso' => $empleado->fecha_ingreso,
                            'monedaSueldo' => $empleado->moneda_sueldo,
                            'sueldoBase' => $empleado->sueldo_base,
                            'afp' => array(
                                'id' => $empleado->afp ? $empleado->afp->id : "",
                                'nombre' => $empleado->afp ? $empleado->afp->glosa : ""
                            ),
                            'estado' => $empleado->estado
                        );
                    }
                }
            }
        }
        
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaTrabajadores,
            'rmi' => $rmi
        );
        
        return Response::json($datos);
    }
    
    public function reajustarRMI()
    {
        $datos = Input::all();
        $mesActual = \Session::get('mesActivo');
        $mes = $mesActual->mes;  
        $idMes = $mesActual->id;  
        
        $rmi = RentaMinimaImponible::where('mes', $mes)->where('nombre', 'Trab. Dependientes e Independientes')->first()->valor;
        
        foreach($datos['trabajadores'] as $trab){
            $ficha = FichaTrabajador::find($trab['id']);

            if($ficha->mes_id!=$idMes){
                $id = (FichaTrabajador::orderBy('id', 'DESC')->first()->id + 1);
                $nuevaFicha = new FichaTrabajador();
                $nuevaFicha = $ficha->replicate();
                $nuevaFicha->id = $id;
                $nuevaFicha->mes_id = $idMes;
                $nuevaFicha->fecha = $mes;
                $nuevaFicha->sueldo_base = $rmi;
                $nuevaFicha->save(); 
            }else{
                $ficha->sueldo_base = $rmi;
                $ficha->save(); 
            }                
        }
        
        $respuesta=array(
            'success' => true,
            'mensaje' => "La Información fue actualizada correctamente"
        );
        
        return Response::json($respuesta);
    }    
    
    public function create()
    {
        //
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $datos = $this->get_datos_formulario();
        $errores = Trabajador::errores($datos);  
        $idMes = \Session::get('mesActivo')->id;  
        
        if(!$errores){
            $trabajador = new Trabajador();
            $trabajador->sid = Funciones::generarSID();
            $trabajador->rut = $datos['rut'];            
            $trabajador->save();
            
            $ficha = new FichaTrabajador();
            $ficha->trabajador_id = $trabajador->id;
            $ficha->mes_id = $idMes;            
            $ficha->nombres = $datos['nombres'];
            $ficha->apellidos = $datos['apellidos'];
            $ficha->nacionalidad_id = $datos['nacionalidad_id'];
            $ficha->sexo = $datos['sexo'];
            $ficha->gratificacion = $datos['gratificacion'];
            $ficha->estado_civil_id = $datos['estado_civil_id'];
            $ficha->fecha_nacimiento = $datos['fecha_nacimiento'];
            $ficha->direccion = $datos['direccion'];
            $ficha->comuna_id = $datos['comuna_id'];
            $ficha->telefono = $datos['telefono'];
            $ficha->celular = $datos['celular'];
            $ficha->celular_empresa = $datos['celular_empresa'];
            $ficha->email = $datos['email'];
            $ficha->email_empresa = $datos['email_empresa'];
            $ficha->tipo_id = $datos['tipo_id'];
            $ficha->cargo_id = $datos['cargo_id'];
            $ficha->titulo_id = $datos['titulo_id'];
            $ficha->seccion_id = $datos['seccion_id'];
            $ficha->tienda_id = $datos['tienda_id'];
            $ficha->centro_costo_id = $datos['centro_costo_id'];
            $ficha->tipo_cuenta_id = $datos['tipo_cuenta_id'];
            $ficha->banco_id = $datos['banco_id'];
            $ficha->numero_cuenta = $datos['numero_cuenta'];
            $ficha->fecha_ingreso = $datos['fecha_ingreso'];
            $ficha->fecha_reconocimiento = $datos['fecha_reconocimiento'];
            $ficha->tipo_contrato_id = $datos['tipo_contrato_id'];
            $ficha->fecha_vencimiento = $datos['fecha_vencimiento'];
            $ficha->tipo_jornada_id = $datos['tipo_jornada_id'];
            $ficha->semana_corrida = $datos['semana_corrida'];
            $ficha->moneda_sueldo = $datos['moneda_sueldo'];
            $ficha->sueldo_base = $datos['sueldo_base'];
            $ficha->tipo_trabajador = $datos['tipo_trabajador'];
            $ficha->exceso_retiro = $datos['exceso_retiro'];
            $ficha->moneda_colacion = $datos['moneda_colacion'];
            $ficha->proporcional_colacion = $datos['proporcional_colacion'];
            $ficha->monto_colacion = $datos['monto_colacion'];
            $ficha->moneda_movilizacion = $datos['moneda_movilizacion'];
            $ficha->proporcional_movilizacion = $datos['proporcional_movilizacion'];
            $ficha->monto_movilizacion = $datos['monto_movilizacion'];
            $ficha->moneda_viatico = $datos['moneda_viatico'];
            $ficha->proporcional_viatico = $datos['proporcional_viatico'];
            $ficha->monto_viatico = $datos['monto_viatico'];
            $ficha->prevision_id = $datos['prevision_id'];
            $ficha->afp_id = $datos['afp_id'];
            $ficha->seguro_desempleo = $datos['seguro_desempleo'];
            $ficha->afp_seguro_id = $datos['afp_seguro_id'];
            $ficha->isapre_id = $datos['isapre_id'];
            $ficha->cotizacion_isapre = $datos['cotizacion_isapre'];
            $ficha->monto_isapre = $datos['monto_isapre'];
            $ficha->sindicato = $datos['sindicato'];
            $ficha->moneda_sindicato = $datos['moneda_sindicato'];
            $ficha->monto_sindicato = $datos['monto_sindicato'];
            $ficha->estado = $datos['estado'];
            
            if($ficha->estado=='Ingresado'){
                $ficha->tramo_id = FichaTrabajador::calcularTramo(Funciones::convertir($datos['sueldo_base'], $datos['moneda_sueldo']));
                $trabajador->crearUser($datos['estadoUser']);
                if($ficha->semana_corrida==1){
                    $trabajador->crearSemanaCorrida();
                }
            }
            
            $ficha->save();
            
            if($ficha->fecha_reconocimiento){
                $trabajador->calcularMisVacaciones($ficha->fecha_reconocimiento);
            }
            
            $respuesta=array(
                'success' => true,
                'mensaje' => "La Información fue almacenada correctamente",
                'sid' => $trabajador->sid
            );
            
            if($datos['apvs']){    
                
                $apvs = $datos['apvs'];
                foreach($apvs as $apv){
                    $afp = $apv['afp'];
                    $formaPago = $apv['forma_pago'];
                    $errores = Apv::errores($apv);
                    if(!$errores){
                        $nuevoApv = new Apv();
                        $nuevoApv->sid = Funciones::generarSID();
                        $nuevoApv->ficha_trabajador_id = $ficha->id;
                        $nuevoApv->afp_id = $afp['id'];
                        $nuevoApv->forma_pago = $formaPago['id'];
                        $nuevoApv->regimen = $apv['regimen'];
                        $nuevoApv->moneda = $apv['moneda'];
                        $nuevoApv->monto = $apv['monto'];
                        $nuevoApv->save();                                                
                    }else{
                        $respuesta=array(
                            'success' => false,
                            'mensaje' => "La acción no pudo ser completada debido a errores en la información ingresada",
                            'errores' => $errores
                        );
                    }
                }
            }
            if($datos['descuentos']){    
                
                $descuentos = $datos['descuentos'];
                foreach($descuentos as $descuento){
                    $tipo = $descuento['tipo'];
                    $errores = Descuento::errores($descuento);
                    if(!$errores){
                        $nuevoDescuento = new Descuento();
                        $nuevoDescuento->sid = Funciones::generarSID();
                        $nuevoDescuento->trabajador_id = $trabajador->id;
                        $nuevoDescuento->tipo_descuento_id = $tipo['id'];
                        $nuevoDescuento->mes_id = null;
                        $nuevoDescuento->por_mes = 0;
                        $nuevoDescuento->rango_meses = 0;
                        $nuevoDescuento->permanente = 1;
                        $nuevoDescuento->todos_anios = 0;
                        $nuevoDescuento->mes = null;
                        $nuevoDescuento->desde = null;
                        $nuevoDescuento->hasta = null;
                        $nuevoDescuento->moneda = $descuento['moneda'];
                        $nuevoDescuento->monto = $descuento['monto'];
                        $nuevoDescuento->save();                                                
                    }else{
                        $respuesta=array(
                            'success' => false,
                            'mensaje' => "La acción no pudo ser completada debido a errores en la información ingresada",
                            'errores' => $errores
                        );
                    }
                }
            }
            if($datos['haberes']){    
                
                $haberes = $datos['haberes'];
                foreach($haberes as $haber){
                    $tipo = $haber['tipo'];
                    $errores = Haber::errores($haber);
                    if(!$errores){
                        $nuevoHaber = new Haber();
                        $nuevoHaber->sid = Funciones::generarSID();
                        $nuevoHaber->trabajador_id = $trabajador->id;
                        $nuevoHaber->tipo_haber_id = $tipo['id'];
                        $nuevoHaber->mes_id = null;
                        $nuevoHaber->por_mes = 0;
                        $nuevoHaber->rango_meses = 0;
                        $nuevoHaber->permanente = 1;
                        $nuevoHaber->todos_anios = 0;
                        $nuevoHaber->mes = null;
                        $nuevoHaber->desde = null;
                        $nuevoHaber->hasta = null;
                        $nuevoHaber->moneda = $haber['moneda'];
                        $nuevoHaber->monto = $haber['monto'];
                        $nuevoHaber->save();                                                
                    }else{
                        $respuesta=array(
                            'success' => false,
                            'mensaje' => "La acción no pudo ser completada debido a errores en la información ingresada",
                            'errores' => $errores
                        );
                    }
                }
            }
            if($datos['cargas']){    
                
                $cargas = $datos['cargas'];
                foreach($cargas as $carga){
                    $tipo = $carga['tipo'];
                    $errores = Carga::errores($carga);
                    if(!$errores){
                        $nuevaCarga = new Carga();
                        $nuevaCarga->sid = Funciones::generarSID();
                        $nuevaCarga->ficha_trabajador_id = $ficha->id;
                        $nuevaCarga->parentesco = $carga['parentesco'];
                        $nuevaCarga->tipo_carga_id = $tipo['id'];
                        $nuevaCarga->rut = $carga['rut'];
                        $nuevaCarga->nombre_completo = $carga['nombreCompleto'];
                        $nuevaCarga->fecha_nacimiento = $carga['fechaNacimiento'];
                        $nuevaCarga->sexo = $carga['sexo'];
                        $nuevaCarga->es_carga = $carga['esCarga'];
                        $nuevaCarga->save();                                                
                    }else{
                        $respuesta=array(
                            'success' => false,
                            'mensaje' => "La acción no pudo ser completada debido a errores en la información ingresada",
                            'errores' => $errores
                        );
                    }
                }
            }
        }else{
            $respuesta=array(
                'success' => false,
                'mensaje' => "La acción no pudo ser completada debido a errores en la información ingresada",
                'errores' => $errores
            );
        }

        return Response::json($respuesta);
    }    
    
    public function listaAfps()
    {        
        $datos=array(
            'datos' => Glosa::listaAfps()
        );
        
        return Response::json($datos);
    }
    
    public function seccionesFormulario()
    {
        $listaSecciones=array();
        Seccion::listaSecciones($listaSecciones, 0, 1);
        
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;    
        $trabajadores = Trabajador::all();
        
        $listaTrabajadores=array();
        if( $trabajadores->count() ){
            foreach( $trabajadores as $trabajador ){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $listaTrabajadores[]=array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'seccion' => array(
                                'id' => $empleado->seccion->id,
                                'sid' => $empleado->seccion->sid,
                                'nombre' => $empleado->seccion->nombre
                            )
                        );
                    }
                }
            }
        }
        
		$datos=array(
			'secciones' => $listaSecciones,
			'trabajadores' => $listaTrabajadores
		);
        
		return Response::json($datos);
	}
    
    public function trabajadoresInasistencias()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-inasistencias');
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;     
        $trabajadores = Trabajador::all();
        
        $listaTrabajadores = array();
        if($trabajadores->count()){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $idTrabajador = $trabajador->id;
                        $listaTrabajadores[] = array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'rut' => $trabajador->rut,
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'totalInasistencias' => $trabajador->totalInasistencias()
                        );
                    }
                }
            }
        }
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaTrabajadores
        );
        
        return Response::json($datos);     
    }
    
    public function trabajadorInasistencias($sid)
    {        
        $trabajador = Trabajador::whereSid($sid)->first();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-inasistencias');
        
        $trabajadorInasistencias = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'rut' => $trabajador->rut,
            'nombreCompleto' => $trabajador->ficha()->nombreCompleto(),
            'inasistencias' => $trabajador->misInasistencias()
        );
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorInasistencias
        );
        return Response::json($datos);     
    }
    
    public function trabajadoresLicencias()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-licencias');
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;     
        $trabajadores = Trabajador::all();
        
        $listaTrabajadores = array();
        if($trabajadores->count()){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $idTrabajador = $trabajador->id;
                        $listaTrabajadores[] = array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'rut' => $trabajador->rut,
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'totalLicencias' => $trabajador->totalLicencias(),
                            'totalDiasLicencias' => $trabajador->totalDiasLicencias()
                        );
                    }
                }
            }
        }
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaTrabajadores
        );
        
        return Response::json($datos);     
    }
    
    public function trabajadorLicencias($sid)
    {        
        $trabajador = Trabajador::whereSid($sid)->first();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-licencias');
        
        $trabajadorLicencias = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'rut' => $trabajador->rut,
            'nombreCompleto' => $trabajador->ficha()->nombreCompleto(),
            'licencias' => $trabajador->misLicencias()
        );
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorLicencias
        );
        
        return Response::json($datos);     
    }
    
    public function trabajadoresHorasExtra()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-horas-extra');
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;     
        $trabajadores = Trabajador::all();
        
        $listaTrabajadores = array();
        if($trabajadores->count()){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $idTrabajador = $trabajador->id;
                        $listaTrabajadores[] = array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'rut' => $trabajador->rut,
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'totalHorasExtra' => $trabajador->totalHorasExtra()
                        );
                    }
                }
            }
        }
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaTrabajadores
        );
        
        return Response::json($datos);     
    }    
    
    public function trabajadorHorasExtra($sid)
    {        
        $trabajador = Trabajador::whereSid($sid)->first();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-horas-extra');
        
        $trabajadorHorasExtra = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => Funciones::formatear_rut($trabajador->rut),
            'rut' => $trabajador->rut,
            'nombreCompleto' => $trabajador->ficha()->nombreCompleto(),
            'horasExtra' => $trabajador->misHorasExtra()
        );
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorHorasExtra
        );
        
        return Response::json($datos);     
    }            
    
    public function trabajadoresPrestamos()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-prestamos');
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;     
        $trabajadores = Trabajador::all();
        
        $listaTrabajadores = array();
        if($trabajadores->count()){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $idTrabajador = $trabajador->id;
                        $listaTrabajadores[] = array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'rut' => $trabajador->rut,
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'totalPrestamos' => $trabajador->totalPrestamos()
                        );
                    }
                }
            }
        }
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaTrabajadores
        );
        
        return Response::json($datos);     
    }
    
    public function trabajadorPrestamos($sid)
    {        
        $trabajador = Trabajador::whereSid($sid)->first();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-prestamos');
        
        $trabajadorPrestamos = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'rut' => $trabajador->rut,
            'nombreCompleto' => $trabajador->ficha()->nombreCompleto(),
            'prestamos' => $trabajador->prestamos
        );
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorPrestamos
        );
        
        return Response::json($datos);       
    }
    
    public function trabajadoresCargas()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#cargas-familiares');
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;     
        $trabajadores = Trabajador::all();
        $tiposCargas = TipoCarga::listaTiposCarga();
        
        $listaTrabajadores = array();
        if($trabajadores->count()){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $idTrabajador = $trabajador->id;
                        $listaTrabajadores[] = array(
                            'id' => $trabajador->id,
                            'idFicha' => $empleado->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'rut' => $trabajador->rut,
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'cargasFamiliares' => $empleado->totalCargasFamiliares(),
                            'grupoFamiliar' => $empleado->totalGrupoFamiliar(),
                            'cargasAutorizadas' => $empleado->totalCargasAutorizadas(),
                        );
                    }
                }
            }
        }
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaTrabajadores,
            'tiposCargas' => $tiposCargas
        );
        
        return Response::json($datos);     
    }
    
    public function trabajadorCargas($sid)
    {        
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#cargas-familiares');
        $trabajador = Trabajador::whereSid($sid)->first();
        $idTrabajador = $trabajador->id;
        $empleado = $trabajador->ficha();
        $tiposCargas = TipoCarga::listaTiposCarga();
        
        $trabajadorCargas = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'rut' => $trabajador->rut,
            'esAutorizadas' => $empleado->tramo_id ? true : false,
            'esCargas' => $empleado->isCargas($idTrabajador),
            'nombreCompleto' => $empleado->nombreCompleto(),
            'cargas' => $empleado->miGrupoFamiliar($idTrabajador)
            
        );
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorCargas,
            'tiposCargas' => $tiposCargas
        );
        
        return Response::json($datos);       
    }
    
    public function trabajadorCargasAutorizar($sid)
    {        
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#cargas-familiares');
        $trabajador = Trabajador::whereSid($sid)->first();
        $listaTramos = AsignacionFamiliar::listaAsignacionFamiliar();
        $empleado = $trabajador->ficha();
        
        $trabajadorCargas = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'rut' => $trabajador->rut,
            'nombreCompleto' => $empleado->nombreCompleto(),
            'cargas' => $empleado->misCargas()    ,
            'tramo' => array(
                'id' => $empleado->tramo_id ? $empleado->tramo_id : "",
                'sid' => $empleado->tramo_id ? $empleado->tramo->sid : "",
                'tramo' => $empleado->tramo_id ? $empleado->tramo->tramo : ""
            )
        );
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorCargas,
            'tramos' => $listaTramos
        );
        
        return Response::json($datos);       
    }
    
    public function trabajadorAutorizarCargas()
    {        
        
        $datos = Input::all();
        $sidTrabajador = $datos['sidTrabajador'];
        $trabajador = Trabajador::whereSid($sidTrabajador)->first();
        $cargas = $datos['cargas'];
        $idTramo = $datos['tramo'];
        $empleado = $trabajador->ficha();
        
        $empleado->tramo_id = $idTramo; 
        $empleado->save();
        
        if($cargas){
            foreach($cargas as $carga){
                $cargaFamiliar = Carga::whereSid($carga['sid'])->first();
                $cargaFamiliar->es_autorizada = 1;
                $cargaFamiliar->fecha_autorizacion = $carga['fecha'];
                $cargaFamiliar->save();
            }    
        }
        
        $respuesta = array(
            'success' => true,
            'mensaje' => "La Información fue actualizada correctamente",
            'sid' => $trabajador->sid
        );
        
        return Response::json($respuesta);       
    }
    
    public function haberes($sid)
    {
        $trabajador = Trabajador::whereSid($sid)->first();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-haberes');
        
        $trabajadorHaberes = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'rut' => $trabajador->rut,
            'nombreCompleto' => $trabajador->ficha()->nombreCompleto(),
            'haberes' => $trabajador->misHaberes()
        );
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorHaberes
        );
        
        return Response::json($datos);     
    }
    
    public function descuentos($sid)
    {
        $trabajador = Trabajador::whereSid($sid)->first();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-descuentos');
        
        $trabajadorDescuentos = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'rut' => $trabajador->rut,
            'nombreCompleto' => $trabajador->ficha()->nombreCompleto(),
            'descuentos' => $trabajador->misDescuentos()
        );
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorDescuentos
        );
        return Response::json($datos);     
    }    
    
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($sid)
    {
        
        $listaSecciones=array();
        Seccion::listaSecciones($listaSecciones, 0, 1);
        $datosTrabajador = array();
        
		$datosFormulario=array(
			'nacionalidades' => Glosa::listaNacionalidades(),
			'estadosCiviles' => EstadoCivil::listaEstadosCiviles(),
			'cargos' => Cargo::listaCargos(),
			'tiendas' => Tienda::listaTiendas(),
			'centros' => CentroCosto::listaCentrosCosto(),
			'secciones' => $listaSecciones,
			'titulos' => Titulo::listaTitulos(),
			'tipos' => Glosa::listaTiposTrabajador(),
			'tiposCuentas' => TipoCuenta::listaTiposCuenta(),
			'tiposCargas' => TipoCarga::listaTiposCarga(),
			'bancos' => Banco::listaBancos(),
			'tiposContratos' => TipoContrato::listaTiposContrato(),
			'tiposJornadas' => Jornada::listaJornadas(),			
			'previsiones' => Glosa::listaPrevisiones(),
			'exCajas' => Glosa::listaExCajas(),
			'afps' => Glosa::listaAfps(),
			'afpsSeguro' => Glosa::listaAfpsSeguro(),
			'afpsApvs' => Glosa::listaAfpsApvs(),
			'formasPago' => Glosa::listaFormasPago(),
			'isapres' => Glosa::listaIsapres(),
			'tiposDescuento' => TipoDescuento::listaTiposDescuento(),
			'tiposHaber' => TipoHaber::listaTiposHaber(),
            'rmi' => RentaMinimaImponible::rmi(),
            'rti' => RentaTopeImponible::rti(),
            'tasasSeguroCesantia' => SeguroDeCesantia::listaSeguroDeCesantia(),
            'rentasTopesImponibles' => RentaTopeImponible::listaRentasTopeImponibles(),
            'tablaImpuestoUnico' => TablaImpuestoUnico::tabla()
		);
        
        if($sid){
            $trabajador = Trabajador::whereSid($sid)->first();
            $idTrabajador = $trabajador->id;
            $mes = \Session::get('mesActivo')->mes;
            $idMes = \Session::get('mesActivo')->id;   
            $empleado = $trabajador->ficha();

            $datosTrabajador = array(
                'id' => $empleado->id,
                'sid' => $trabajador->sid,
                'rutFormato' => $trabajador->rut_formato(),
                'rut' => $trabajador->rut,
                'nombres' => $empleado->nombres,
                'apellidos' => $empleado->apellidos,
                'nombreCompleto' => $empleado->nombreCompleto(),
                'nacionalidad' => array(
                    'id' => $empleado->nacionalidad ? $empleado->nacionalidad->id : "",
                    'nombre' => $empleado->nacionalidad ? $empleado->nacionalidad->glosa : ""
                ),
                'sexo' => $empleado->sexo,
                'estadoCivil' => array(
                    'id' => $empleado->estadoCivil ? $empleado->estadoCivil->id : "",
                    'nombre' => $empleado->estadoCivil ? $empleado->estadoCivil->nombre : ""
                ),
                'fechaNacimiento' => $empleado->fecha_nacimiento,
                'direccion' => $empleado->direccion,
                //'domicilio' => $trabajador->domicilio(),
                'comuna' => array(
                    'id' => $empleado->comuna ? $empleado->comuna->id : "",
                    'nombre' => $empleado->comuna ? $empleado->comuna->localidad() : "",
                    'comuna' => $empleado->comuna ? $empleado->comuna->comuna : "",
                    'provincia' => $empleado->comuna ? $empleado->comuna->provincia->provincia : ""
                ), 
                'telefono' => $empleado->telefono,
                'celular' => $empleado->celular,
                'celularEmpresa' => $empleado->celular_empresa,
                'email' => $empleado->email,
                'emailEmpresa' => $empleado->email_empresa,
                'tipo' => array(
                    'id' => $empleado->tipo_id,
                    'nombre' => $empleado->tipo ? $empleado->tipo->nombre : ""
                ),
                'cargo' => array(
                    'id' => $empleado->cargo ? $empleado->cargo->id : "",
                    'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
                ),
                'titulo' => array(
                    'id' => $empleado->titulo ? $empleado->titulo->id : "",
                    'nombre' => $empleado->titulo ? $empleado->titulo->nombre : ""
                ),
                'seccion' => array(
                    'id' => $empleado->seccion ? $empleado->seccion->id : "",
                    'nombre' => $empleado->seccion ? $empleado->seccion->nombre : ""
                ),
                'tienda' => array(
                    'id' => $empleado->tienda ? $empleado->tienda->id : "",
                    'nombre' => $empleado->tienda ? $empleado->tienda->nombre : ""
                ),
                'centroCosto' => array(
                    'id' => $empleado->centroCosto ? $empleado->centroCosto->id : "",
                    'nombre' => $empleado->centroCosto ? $empleado->centroCosto->nombre : ""
                ),
                'tipoCuenta' => array(
                    'id' => $empleado->tipoCuenta ? $empleado->tipoCuenta->id : "",
                    'nombre' => $empleado->tipoCuenta ? $empleado->tipoCuenta->nombre : ""
                ),
                'banco' => array(
                    'id' => $empleado->banco ? $empleado->banco->id : "",
                    'nombre' => $empleado->banco ? $empleado->banco->nombre : ""
                ),
                'numeroCuenta' => $empleado->numero_cuenta,                
                'fechaIngreso' => $empleado->fecha_ingreso,
                'fechaReconocimiento' => $empleado->fecha_reconocimiento,
                'fechaFiniquito' => $empleado->fecha_finiquito,
                'tipoContrato' => array(
                    'id' => $empleado->tipoContrato ? $empleado->tipoContrato->id : "",
                    'nombre' => $empleado->tipoContrato ? $empleado->tipoContrato->nombre : ""
                ),
                'fechaVencimiento' => $empleado->fecha_vencimiento ? $empleado->fecha_vencimiento : "",
                'tipoJornada' => array(
                    'id' => $empleado->tipoJornada ? $empleado->tipoJornada->id : "",
                    'nombre' => $empleado->tipoJornada ? $empleado->tipoJornada->nombre : ""
                ),
                'semanaCorrida' => $empleado->semana_corrida ? true : false,
                'monedaSueldo' => $empleado->moneda_sueldo,
                'gratificacion' => $empleado->gratificacion,
                'sueldoBase' => $empleado->sueldo_base,
                'tipoTrabajador' => $empleado->tipo_trabajador,
                'excesoRetiro' => $empleado->exceso_retiro,
                'proporcionalColacion' => $empleado->proporcional_colacion ? true : false,
                'monedaColacion' => $empleado->moneda_colacion,
                'montoColacion' => $empleado->monto_colacion,
                'proporcionalMovilizacion' => $empleado->proporcional_movilizacion ? true : false,
                'monedaMovilizacion' => $empleado->moneda_movilizacion,
                'montoMovilizacion' => $empleado->monto_movilizacion,
                'proporcionalViatico' => $empleado->proporcional_viatico ? true : false,
                'monedaViatico' => $empleado->moneda_viatico,
                'montoViatico' => $empleado->monto_viatico,
                'prevision' => array(
                    'id' => $empleado->prevision ? $empleado->prevision->id : "",
                    'nombre' => $empleado->prevision ? $empleado->prevision->glosa : ""
                ),
                'afp' => array(
                    'id' => $empleado->afp ? $empleado->afp->id : "",
                    'nombre' => $empleado->afp ? $empleado->afp->glosa : ""
                ),
                'seguroDesempleo' => $empleado->seguro_desempleo ? true : false,
                'afpSeguro' => array(
                    'id' => $empleado->afpSeguro ? $empleado->afpSeguro->id : "",
                    'nombre' => $empleado->afpSeguro ? $empleado->afpSeguro->glosa : ""
                ),
                'isapre' => array(
                    'id' => $empleado->isapre ? $empleado->isapre->id : "",
                    'nombre' => $empleado->isapre ? $empleado->isapre->glosa : ""
                ),
                'cotizacionIsapre' => $empleado->cotizacion_isapre,
                'montoIsapre' => $empleado->monto_isapre,
                'sindicato' => $empleado->sindicato ? true : false,
                'monedaSindicato' => $empleado->moneda_sindicato,
                'montoSindicato' => $empleado->monto_sindicato,
                'estado' => $empleado->estado,
                'apvs' => $empleado->misApvs(),
                'haberes' => $trabajador->misHaberesPermanentes(),
                'descuentos' => $trabajador->misDescuentosPermanentes(),
                'prestamos' => $trabajador->misPrestamos(),
                'cargas' => $empleado->misCargas()

            );
        }
        
        $datos = array(
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'trabajador' => $datosTrabajador,
            'formulario' => $datosFormulario
        );
        
        return Response::json($datos);
    }    
    
    public function vigentes()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('activos' => array(), 'inactivos' => array(), 'permisos' => array()));
        }
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;
        $mes = \Session::get('mesActivo')->mes;
        /*$activos = Trabajador::whereFicha('estado', 'Ingresado')->orWhereFicha('estado', 'Finiquitado')->whereFicha('fecha_finiquito', '>=', $mes)->get();*/
        /*$activos = Trabajador::with('FichaTrabajador')->WhereIn('fichaTrabajador.estado', 'Ingresado')->get();*/
        /*$activos = DB::table('trabajadores')
            ->join('fichas_trabajadores', function($join)
                   {
                        $join->on('trabajadores.id', '=', 'fichas_trabajadores.trabajador_id')
                            ->where('fichas_trabajadores.estado', '=', 'Ingresado');
                    })
            ->get();*/
        
        $trabajadores = Trabajador::all();
        
        
        $listaActivos = array();
        if($trabajadores){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $listaActivos[] = array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'rut' => $trabajador->rut,
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'fechaIngreso' => $empleado->fecha_ingreso,
                            'cargo' => array(
                                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
                            ),
                            'tipoContrato' => array(
                                'id' => $empleado->tipoContrato ? $empleado->tipoContrato->id : "",
                                'nombre' => $empleado->tipoContrato ? $empleado->tipoContrato->nombre : ""
                            ),
                            'monedaSueldo' => $empleado->moneda_sueldo,
                            'sueldoBase' => $empleado->sueldo_base
                        );
                    }
                }
            }
        }
        
        //$inactivos = Trabajador::where('estado', '<>', 'Ingresado')->orderBy('apellidos')->get();
        //$inactivos = FichaTrabajador::with('Trabajador')->where('mes_id', $idMes)->where('estado', '<>', 'Ingresado')->orWhere('estado', 'Finiquitado')->where('fecha_finiquito', '<=', $mes)->get();
        
        $listaInactivos = array();
        if($trabajadores){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='En Creación' || $empleado->estado=='Finiquitado'){
                        $listaInactivos[] = array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'rut' => $trabajador->rut,
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'fechaIngreso' => $empleado->fecha_ingreso,
                            'cargo' => array(
                                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
                            ),
                            'tipoContrato' => array(
                                'id' => $empleado->tipoContrato ? $empleado->tipoContrato->id : "",
                                'nombre' => $empleado->tipoContrato ?$empleado->tipoContrato->nombre : ""
                            ),
                            'monedaSueldo' => $empleado->moneda_sueldo,
                            'estado' => $empleado->estado,
                            'sueldoBase' => $empleado->sueldo_base
                        );
                    }
                }
            }
        }
        
        $datos = array(
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'activos' => $listaActivos,
            'inactivos' => $listaInactivos
        );
        
        return Response::json($datos); 
    }
    
    public function archivoPrevired()
    {
        
        if(!\Session::get('empresa')){
            return Response::json(array('activos' => array(), 'conLiquidacion' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#archivo-previred');
        $mesActual = \Session::get('mesActivo');
        $finMes = $mesActual->fechaRemuneracion;
        $mes = $mesActual->mes;
        $trabajadores = Trabajador::all();
        
        $listaActivos = array();
        
        if($trabajadores){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes || $empleado->estado=='Finiquitado' && $empleado->fecha_finiquito >= $mes && $empleado->fecha_ingreso<=$finMes){
                        $listaActivos[] = array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'rut' => $trabajador->rut,
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'fechaIngreso' => $empleado->fecha_ingreso,
                            'cargo' => array(
                                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
                            ),
                            'tipoContrato' => array(
                                'id' => $empleado->tipoContrato ? $empleado->tipoContrato->id : "",
                                'nombre' => $empleado->tipoContrato ? $empleado->tipoContrato->nombre : ""
                            ),
                            'monedaSueldo' => $empleado->moneda_sueldo,
                            'sueldoBase' => $empleado->sueldo_base,        
                            'estado' => $empleado->estado,
                            'isLiquidacion' => $trabajador->isLiquidacion()
                        );
                    }
                }
            }
        }
                                    
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaActivos
        );
        
        return Response::json($datos); 
    }
    
    public function trabajadoresFiniquitos()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('activos' => array(), 'finiquitados' => array(), 'permisos' => array()));
        }
        
        $finMes = \Session::get('mesActivo')->fechaRemuneracion;
        $mes = \Session::get('mesActivo')->mes;
        $trabajadores = Trabajador::all();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#finiquitar-trabajador');
        
        $listaActivos = array();
        if($trabajadores){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Ingresado' && $empleado->fecha_ingreso<=$finMes){
                        $listaActivos[] = array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'rut' => $trabajador->rut,
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'fechaIngreso' => $empleado->fecha_ingreso,
                            'cargo' => array(
                                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
                            ),
                            'tipoContrato' => array(
                                'id' => $empleado->tipoContrato ? $empleado->tipoContrato->id : "",
                                'nombre' => $empleado->tipoContrato ? $empleado->tipoContrato->nombre : ""
                            ),
                            'monedaSueldo' => $empleado->moneda_sueldo,
                            'sueldoBase' => $empleado->sueldo_base,
                            'mesesAntiguedad' => $empleado->mesesAntiguedad()
                        );
                    }
                }
            }
        }

        $listaInactivos = array();
        if($trabajadores){
            foreach($trabajadores as $trabajador){
                $empleado = $trabajador->ficha();
                if($empleado){
                    if($empleado->estado=='Finiquitado'){
                        $listaInactivos[] = array(
                            'id' => $trabajador->id,
                            'sid' => $trabajador->sid,
                            'rutFormato' => $trabajador->rut_formato(),
                            'rut' => $trabajador->rut,
                            'nombreCompleto' => $empleado->nombreCompleto(),
                            'fechaIngreso' => $empleado->fecha_ingreso,
                            'fechaFiniquito' => $empleado->fecha_finiquito,
                            'cargo' => array(
                                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
                            ),
                            'tipoContrato' => array(
                                'id' => $empleado->tipoContrato ? $empleado->tipoContrato->id : "",
                                'nombre' => $empleado->tipoContrato ?$empleado->tipoContrato->nombre : ""
                            ),
                            'monedaSueldo' => $empleado->moneda_sueldo,
                            'sueldoBase' => $empleado->sueldo_base
                        );
                    }
                }
            }
        }
        
        $datos = array(
            'accesos' => $permisos,
            'activos' => $listaActivos,
            'finiquitados' => $listaInactivos
        );
        
        return Response::json($datos); 
    }
    
    public function trabajadorFiniquitos($sid)
    {        
        $trabajador = Trabajador::whereSid($sid)->first();
        $empleado = $trabajador->ficha();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#finiquitar-trabajador');
        
        $trabajadorFiniquitos = array(
            'id' => $trabajador->id,
            'sid' => $trabajador->sid,
            'rutFormato' => $trabajador->rut_formato(),
            'nombreCompleto' => $empleado->nombreCompleto(),
            'celular' => $empleado->celular,
            'email' => $empleado->email,
            'cargo' => array(
                'id' => $empleado->cargo ? $empleado->cargo->id : "",
                'nombre' => $empleado->cargo ? $empleado->cargo->nombre : "",
            ),                     
            'fechaIngreso' => $empleado->fecha_ingreso,
            'monedaSueldo' => $empleado->moneda_sueldo,
            'sueldoBase' => $empleado->sueldo_base,
            'sueldoBasePesos' => Funciones::convertir($empleado->sueldo_base, $empleado->moneda_sueldo),
            'afp' => array(
                'id' => $empleado->afp ? $empleado->afp->id : "",
                'nombre' => $empleado->afp ? $empleado->afp->glosa : ""
            ),
            'tipoContrato' => array(
                'id' => $empleado->tipo_contrato ? $empleado->tipo_contrato->id : "",
                'nombre' => $empleado->tipo_contrato ? $empleado->tipo_contrato->nombre : ""
            ),
            'estado' => $empleado->estado,
            'finiquitos' => $trabajador->misFiniquitos()
        );
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $trabajadorFiniquitos
        );
        
        return Response::json($datos);     
    }
    
    public function liquidacion()
    {
        $datos = Input::all();
        $liquidaciones = array();
        foreach($datos['trabajadores'] as $sid){
            
            $trabajador = Trabajador::whereSid($sid['sid'])->first();   
            $empleado = $trabajador->ficha();
            $idTrabajador = $trabajador->id;
            $liquidaciones[] = array(
                'id' => $trabajador->id,
                'sid' => $trabajador->sid,
                'rutFormato' => $trabajador->rut_formato(),
                'rut' => $trabajador->rut,
                'nombres' => $empleado->nombres,
                'apellidos' => $empleado->apellidos,
                'nombreCompleto' => $empleado->nombreCompleto(),
                'cargo' => array(
                    'id' => $empleado->cargo ? $empleado->cargo->id : "",
                    'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
                ),
                'seccion' => array(
                    'id' => $empleado->seccion ? $empleado->seccion->id : "",
                    'nombre' => $empleado->seccion ? $empleado->seccion->nombre : ""
                ),
                'fechaIngreso' => $empleado->fecha_ingreso,
                'tipoContrato' => array(
                    'id' => $empleado->tipoContrato ? $empleado->tipoContrato->id : "",
                    'nombre' => $empleado->tipoContrato ? $empleado->tipoContrato->nombre : ""
                ),
                'monedaSueldo' => $empleado->moneda_sueldo,
                'sueldoBase' => $empleado->sueldo_base,
                'colacion' => array(
                    'moneda' => $empleado->moneda_colacion,
                    'monto' => $empleado->monto_colacion,
                    'montoPesos' => $trabajador->totalColacion(),
                    'proporcional' => $empleado->proporcional_colacion ? true : false
                ),
                'movilizacion' => array(
                    'moneda' => $empleado->moneda_movilizacion,
                    'monto' => $empleado->monto_movilizacion,
                    'montoPesos' => $trabajador->totalMovilizacion(),
                    'proporcional' => $empleado->proporcional_movilizacion ? true : false
                ),
                'viatico' => array(
                    'moneda' => $empleado->moneda_viatico,
                    'monto' => $empleado->monto_viatico,
                    'montoPesos' => $trabajador->totalViatico(),
                    'proporcional' => $empleado->proporcional_viatico ? true : false
                ),
                'afp' => array(
                    'id' => $empleado->afp ? $empleado->afp->id : "",
                    'nombre' => $empleado->afp ? $empleado->afp->glosa : ""
                ),
                'seguroDesempleo' => $empleado->seguro_desempleo ? true : false,
                'afpSeguro' => array(
                    'id' => $empleado->afpSeguro ? $empleado->afpSeguro->id : "",
                    'nombre' => $empleado->afpSeguro ? $empleado->afpSeguro->glosa : ""
                ),
                'isapre' => array(
                    'id' => $empleado->isapre ? $empleado->isapre->id : "",
                    'nombre' => $empleado->isapre ? $empleado->isapre->glosa : ""
                ),
                'cotizacionIsapre' => $empleado->cotizacion_isapre,
                'montoIsapre' => $empleado->monto_isapre,
                'sindicato' => $empleado->sindicato ? true : false,
                'monedaSindicato' => $empleado->moneda_sindicato,
                'montoSindicato' => $empleado->monto_sindicato,
                'estado' => $empleado->estado,
                'diasTrabajados' => $trabajador->diasTrabajados(),
                'sueldoDiario' => $trabajador->sueldoDiario(),
                'sueldo' => $trabajador->sueldo(),
                'gratificacion' => $trabajador->gratificacion(),
                'horasExtra' => $trabajador->horasExtraPagar(),
                'imponibles' => $trabajador->sumaImponibles(),
                'cargasFamiliares' => $empleado->cargasFamiliares(),
                'noImponibles' => $trabajador->noImponibles(),
                'rentaImponible' => $trabajador->rentaImponible(),
                'tasaAfp' => $trabajador->tasaAfp(),
                'totalAfp' => $trabajador->totalAfp(),
                'totalSalud' => $trabajador->totalSalud(),
                'totalSeguroCesantia' => $trabajador->totalSeguroCesantia(),
                'totalDescuentosPrevisionales' => $trabajador->totalDescuentosPrevisionales(),
                'baseImpuestoUnico' => $trabajador->baseImpuestoUnico(),
                'tramoImpuesto' => $trabajador->tramoImpuesto()->factor,
                'impuestoDeterminado' => $trabajador->impuestoDeterminado(),
                'totalOtrosDescuentos' => $trabajador->totalOtrosDescuentos(),
                'totalAnticipos' => $trabajador->totalAnticipos(),
                'totalHaberes' => $trabajador->totalHaberes(),
                'apvs' => $empleado->misApvs(),
                'haberes' => $trabajador->misHaberes(),
                'descuentos' => $trabajador->misDescuentos(),
                'prestamos' => $trabajador->misCuotasPrestamo(),
                'sueldoLiquido' => $trabajador->sueldoLiquido()
            );
        }
        
        $data = array(
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $liquidaciones
        );
        
        return Response::json($data);   
    }
  
  	public function comprobarLiquidaciones($trabajadores)
    {
        $mes = \Session::get('mesActivo')->mes;
        $trabajadores = (array) $trabajadores;
        $sid = Trabajador::whereIn('sid', $trabajadores)->lists('id');
        $liquidaciones = Liquidacion::where('mes', $mes)->whereIn('trabajador_id', $sid)->where('mes', $mes)->get();
        foreach($liquidaciones as $liquidacion){
            $documento = $liquidacion->documento;
            if($documento){
                $documento->eliminarDocumento();                
            }
        }
        
        return $liquidaciones;
    }
    
    public function miLiquidacion()
    {
        $datos = Input::all();
        $sid = (array) $datos['trabajadores'];
        $isComprobar = $datos['comprobar'];
        $comprobar = false;
        
        if($isComprobar){
            $comprobar = $this->comprobarLiquidaciones($datos['trabajadores']);
        }
        
        $mes = \Session::get('mesActivo');
        $trabajadores = Trabajador::whereIn('sid', $sid)->get();
        $listaPDF = array();
		$empresa = \Session::get('empresa');
		$rutEmpresa = Funciones::formatear_rut($empresa->rut);
        $liquidaciones = array();
        
        foreach($trabajadores as $trabajador){
            $empleado = $trabajador->ficha();
            $apvs = $empleado->misApvs();
            $apvc = $trabajador->apvc();
            $diasTrabajados = $trabajador->misDiasTrabajados();
            $horasExtra = $trabajador->horasExtraPagar();
            $totalAfp = $trabajador->totalAfp();
            $tasaAfp = $trabajador->tasaAfp();
            $totalSalud = $trabajador->totalSalud();
            $totalSeguroCesantia = $trabajador->totalSeguroCesantia();
            $baseImpuestoUnico = $trabajador->baseImpuestoUnico();
            $impuestoDeterminado = $trabajador->impuestoDeterminado();
            $tramoImpuesto = $trabajador->tramoImpuesto()->factor;
            $sumaImponibles = $trabajador->sumaImponibles();
            $noImponibles = $trabajador->noImponibles();
            $totalOtrosDescuentos = $trabajador->totalOtrosDescuentos();
            $totalAnticipos = $trabajador->totalAnticipos();
            $totalDescuentosPrevisionales = $trabajador->totalDescuentosPrevisionales();
            $rentaImponible = $trabajador->rentaImponible();
            $totalHaberes = $trabajador->totalHaberes();
            $cargasFamiliares = $empleado->cargasFamiliares();
            $asignacionRetroactiva = $empleado->asignacionRetroactiva();
            $sueldo = $trabajador->sueldo();
            $diasDescontados = $trabajador->diasDescontados();
            $sueldoDiario = $trabajador->sueldoDiario();
            $sueldoLiquido = $trabajador->sueldoLiquido();
            $gratificacion = $trabajador->gratificacion();
            $totalColacion = $trabajador->totalColacion();
            $totalMovilizacion = $trabajador->totalMovilizacion();
            $totalViatico = $trabajador->totalViatico();
            $totalMutual = $trabajador->totalMutual();        
            $semanaCorrida = $trabajador->miSemanaCorrida();
            $semanaCorridas = $trabajador->miSemanaCorridas();
            $descuentos = $trabajador->misDescuentos();
            $haberes = $trabajador->misHaberes();
            $prestamos = $trabajador->misCuotasPrestamo();
            $totalAportes = ($totalMutual + $totalSeguroCesantia['totalEmpleador'] + $totalSalud['montoCaja'] + $totalSalud['montoFonasa']);
            $movimientoPersonal = $trabajador->movimientoPersonal();
            $miLiquidacion = array(
                'mes' => $mes->nombre . ' del ' . $mes->anio,
                'empresa' => $empresa,
                'rutEmpresa' => $rutEmpresa,
                'id' => $trabajador->id,
                'sid' => $trabajador->sid,
                'rutFormato' => $trabajador->rut_formato(),
                'rut' => $trabajador->rut,
                'nombres' => $empleado->nombres,
                'apellidos' => $empleado->apellidos,
                'nombreCompleto' => $empleado->nombreCompleto(),
                'cargo' => array(
                    'id' => $empleado->cargo ? $empleado->cargo->id : "",
                    'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
                ),
                'seccion' => array(
                    'id' => $empleado->seccion ? $empleado->seccion->id : "",
                    'nombre' => $empleado->seccion ? $empleado->seccion->nombre : ""
                ),
                'fechaIngreso' => $empleado->fecha_ingreso,
                'tipoContrato' => array(
                    'id' => $empleado->tipoContrato ? $empleado->tipoContrato->id : "",
                    'nombre' => $empleado->tipoContrato ? $empleado->tipoContrato->nombre : ""
                ),
                'monedaSueldo' => $empleado->moneda_sueldo,
                'sueldoBase' => Funciones::convertir($empleado->sueldo_base, $empleado->moneda_sueldo),
                'colacion' => array(
                    'monto' => $totalColacion
                ),
                'movilizacion' => array(
                    'monto' => $totalMovilizacion
                ),
                'viatico' => array(
                    'monto' => $totalViatico
                ),
                'afp' => array(
                    'id' => $empleado->afp ? $empleado->afp->id : "",
                    'nombre' => $empleado->afp ? strtoupper($empleado->afp->glosa) : ""
                ),
                'seguroDesempleo' => $empleado->seguro_desempleo ? true : false,
                'afpSeguro' => array(
                    'id' => $empleado->afpSeguro ? $empleado->afpSeguro->id : "",
                    'nombre' => $empleado->afpSeguro ? strtoupper($empleado->afpSeguro->glosa) : ""
                ),
                'isapre' => array(
                    'id' => $empleado->isapre ? $empleado->isapre->id : "",
                    'nombre' => $empleado->isapre ? $empleado->isapre->glosa : ""
                ),
                'cotizacionIsapre' => $empleado->cotizacion_isapre,
                'montoIsapre' => $empleado->monto_isapre,
                'sindicato' => $empleado->sindicato ? true : false,
                'monedaSindicato' => $empleado->moneda_sindicato,
                'montoSindicato' => $empleado->monto_sindicato,
                'estado' => $empleado->estado,
                'diasTrabajados' => $diasTrabajados,
                'diasDescontados' => $diasDescontados,
                'sueldoDiario' => $sueldoDiario,
                'sueldo' => $sueldo,
                'gratificacion' => $gratificacion,
                'horasExtra' => $horasExtra,
                'imponibles' => $sumaImponibles,
                'totalHaberes' => $totalHaberes,
                'cargasFamiliares' => $cargasFamiliares,
                'asignacionRetroactiva' => $asignacionRetroactiva,
                'noImponibles' => $noImponibles,
                'semanaCorrida' => $semanaCorrida,
                'semanaCorridas' => $semanaCorridas,
                'isSemanaCorrida' => $empleado->semana_corrida ? true : false,
                'rentaImponible' => $rentaImponible,
                'tasaAfp' => $tasaAfp['tasaTrabajador'],
                'tasaEmpleador' => $tasaAfp['tasaEmpleador'],
                'totalMutual' => $totalMutual,
                'totalAfp' => $totalAfp['totalTrabajador'],
                'totalAfpEmpleador' => $totalAfp['totalEmpleador'],
                'totalSalud' => $totalSalud,
                'totalSeguroCesantia' => $totalSeguroCesantia,
                'totalDescuentosPrevisionales' => $totalDescuentosPrevisionales,
                'totalDescuentos' => ($totalDescuentosPrevisionales + $totalOtrosDescuentos + $impuestoDeterminado),
                'baseImpuestoUnico' => $baseImpuestoUnico,
                'tramoImpuesto' => $tramoImpuesto,
                'impuestoDeterminado' => $impuestoDeterminado,
                'totalOtrosDescuentos' => $totalOtrosDescuentos,
                'totalAportes' => $totalAportes,
                'apvs' => $apvs,
                'haberes' => $haberes,
                'haberesImponibles' => $trabajador->haberesImponibles(),
                'haberesNoImponibles' => $trabajador->haberesNoImponibles(),
                'descuentos' => $descuentos,
                'prestamos' => $prestamos,
                'sueldoLiquidoPalabras' => strtoupper(Funciones::convertirPalabras($sueldoLiquido)),
                'sueldoLiquido' => $sueldoLiquido,
                'banco' => $empleado->banco ? $empleado->banco->nombre : "",
                'cuenta' => $empleado->numero_cuenta ? $empleado->numero_cuenta : ""
            );
            
            $filename = date("m-Y")."_Liquidacion_".$trabajador->rut. '.pdf';
            $documento = new Documento();
            $documento->sid = Funciones::generarSID();
            $documento->trabajador_id = $trabajador->id;
            $documento->tipo_documento_id = 4;
            $documento->nombre = $filename;
            $documento->alias = 'Liquidación de Sueldo ' . $empleado->nombreCompleto() . $mes->nombre . ' del ' . $mes->anio . '.pdf';
            $documento->descripcion = 'Liquidación de Sueldo de ' . $empleado->nombreCompleto() . ' del mes de ' . $mes->nombre . ' del ' . $mes->anio;
            $documento->save();
          
            $totalApv = 0;
          
            if($apvs){                    
                foreach($apvs as $apv){
                    $totalApv = ($totalApv + $apv['montoPesos']);
                }
            }
            
            $liquidacion = new Liquidacion();
            $liquidacion->sid = Funciones::generarSID();
            $liquidacion->trabajador_id = $trabajador->id;
            $liquidacion->documento_id = $documento->id;
            $liquidacion->empresa_id = $empresa->id;
            $liquidacion->empresa_razon_social = $empresa->razon_social;
            $liquidacion->empresa_rut = $empresa->rut;
            $liquidacion->empresa_direccion = $empresa->direccion;
            $liquidacion->inasistencias = $trabajador->totalInasistencias();
            $liquidacion->encargado_id = $trabajador->id;
            $liquidacion->mes = $mes->mes;
            $liquidacion->folio = 45646548;
            $liquidacion->estado = 1;
            $liquidacion->trabajador_rut = $trabajador->rut;
            $liquidacion->trabajador_nombres = $empleado->nombres;
            $liquidacion->trabajador_apellidos = $empleado->apellidos;
            $liquidacion->trabajador_cargo = $empleado->cargo->nombre;
            $liquidacion->trabajador_seccion = $empleado->seccion->nombre;
            $liquidacion->trabajador_fecha_ingreso = $empleado->fecha_ingreso;
            $liquidacion->uf = $mes->uf;
            $liquidacion->utm = $mes->utm;
            $liquidacion->dias_trabajados = $trabajador->diasTrabajados();
            $liquidacion->horas_extra = $horasExtra['cantidad'];
            $liquidacion->total_horas_extra = $horasExtra['total'];
            $liquidacion->tipo_contrato = $empleado->tipoContrato->nombre;
            $liquidacion->sueldo_base = $empleado->sueldo_base;
            $liquidacion->seguro_cesantia = $empleado->seguro_desempleo;
            $liquidacion->base_impuesto_unico = $baseImpuestoUnico;
            $liquidacion->impuesto_determinado = $impuestoDeterminado;
            $liquidacion->tramo_impuesto = $tramoImpuesto;
            $liquidacion->imponibles = $sumaImponibles;
            $liquidacion->no_imponibles = $noImponibles;
            $liquidacion->total_otros_descuentos = $totalOtrosDescuentos;
            $liquidacion->total_anticipos = $totalAnticipos;
            $liquidacion->total_descuentos_previsionales = $totalDescuentosPrevisionales;
            $liquidacion->total_descuentos = ($totalDescuentosPrevisionales + $totalOtrosDescuentos);
            $liquidacion->total_aportes = $totalAportes;
            $liquidacion->renta_imponible = $rentaImponible;
            $liquidacion->total_haberes = $totalHaberes;
            $liquidacion->total_cargas = $cargasFamiliares['monto'];
            $liquidacion->cantidad_cargas = $cargasFamiliares['cantidad'];
            $liquidacion->cantidad_cargas_invalidas = $cargasFamiliares['cantidadInvalidas'];
            $liquidacion->cantidad_cargas_simples = $cargasFamiliares['cantidadSimples'];
            $liquidacion->cantidad_cargas_maternales = $cargasFamiliares['cantidadMaternales'];
            $liquidacion->asignacion_retroactiva = $asignacionRetroactiva;
            $liquidacion->reintegro_cargas = 0;
            $liquidacion->sueldo = $sueldo;
            $liquidacion->sueldo_diario = $sueldoDiario;
            $liquidacion->sueldo_liquido = $sueldoLiquido;
            $liquidacion->gratificacion = $gratificacion;
            $liquidacion->colacion = $totalColacion;
            $liquidacion->movilizacion = $totalMovilizacion;
            $liquidacion->viatico = $totalViatico;
            $liquidacion->centro_costo_id = '';
            $liquidacion->movimiento_personal = $movimientoPersonal['codigo'];
            $liquidacion->fecha_desde = $movimientoPersonal['fechaDesde'];
            $liquidacion->fecha_hasta = $movimientoPersonal['fechaHasta'];
            $liquidacion->tramo_id = $empleado->tramo_id;
            $liquidacion->prevision_id = $empleado->prevision_id;
            
            $liquidacion->save();
            
            if($liquidacion->prevision_id==8){
                $detalleAfp = new DetalleAfp();
                $detalleAfp->liquidacion_id = $liquidacion->id;
                $detalleAfp->afp_id = $empleado->afp_id;
                $detalleAfp->renta_imponible = $rentaImponible;
                $detalleAfp->cotizacion = $totalAfp['cotizacion'];
                $detalleAfp->sis = $totalAfp['sis'];
                $detalleAfp->cuenta_ahorro_voluntario = $totalAfp['cuentaAhorroVoluntario'];
                $detalleAfp->renta_sustitutiva = 0;
                $detalleAfp->tasa_sustitutiva = 0;
                $detalleAfp->aporte_sustitutiva = 0;
                $detalleAfp->numero_periodos = 0;
                $detalleAfp->periodo_desde = null;
                $detalleAfp->periodo_hasta = null;
                $detalleAfp->puesto_trabajo_pesado = null;
                $detalleAfp->porcentaje_trabajo_pesado = 0;
                $detalleAfp->cotizacion_trabajo_pesado = 0;
                $detalleAfp->save();
            }
            
            if($totalColacion>0){                
                $detalleLiquidacion = new DetalleLiquidacion();
                $detalleLiquidacion->sid = Funciones::generarSID();
                $detalleLiquidacion->liquidacion_id = $liquidacion->id;
                $detalleLiquidacion->nombre = 'Colación';
                $detalleLiquidacion->tipo = 'no imponible';
                $detalleLiquidacion->tipo_id = 1;
                $detalleLiquidacion->valor = $totalColacion;
                $detalleLiquidacion->valor_2 = $empleado->monto_colacion;
                $detalleLiquidacion->valor_3 = $empleado->moneda_colacion;
                $detalleLiquidacion->valor_4 = null;
                $detalleLiquidacion->valor_5 = null;
                $detalleLiquidacion->valor_6 = null;
                $detalleLiquidacion->detalle_id = 4;
                $detalleLiquidacion->save(); 
            }
            if($totalMovilizacion>0){                
                $detalleLiquidacion = new DetalleLiquidacion();
                $detalleLiquidacion->sid = Funciones::generarSID();
                $detalleLiquidacion->liquidacion_id = $liquidacion->id;
                $detalleLiquidacion->nombre = 'Movilización';
                $detalleLiquidacion->tipo = 'no imponible';
                $detalleLiquidacion->tipo_id = 1;
                $detalleLiquidacion->valor = $totalColacion;
                $detalleLiquidacion->valor_2 = $empleado->monto_colacion;
                $detalleLiquidacion->valor_3 = $empleado->moneda_colacion;
                $detalleLiquidacion->valor_4 = null;
                $detalleLiquidacion->valor_5 = null;
                $detalleLiquidacion->valor_6 = null;
                $detalleLiquidacion->detalle_id = 4;
                $detalleLiquidacion->save(); 
            }
            if($totalViatico>0){                
                $detalleLiquidacion = new DetalleLiquidacion();
                $detalleLiquidacion->sid = Funciones::generarSID();
                $detalleLiquidacion->liquidacion_id = $liquidacion->id;
                $detalleLiquidacion->nombre = 'Viático';
                $detalleLiquidacion->tipo = 'no imponible';
                $detalleLiquidacion->tipo_id = 1;
                $detalleLiquidacion->valor = $totalColacion;
                $detalleLiquidacion->valor_2 = $empleado->monto_colacion;
                $detalleLiquidacion->valor_3 = $empleado->moneda_colacion;
                $detalleLiquidacion->valor_4 = null;
                $detalleLiquidacion->valor_5 = null;
                $detalleLiquidacion->valor_6 = null;
                $detalleLiquidacion->detalle_id = 5;
                $detalleLiquidacion->save(); 
            }            
            if($haberes){                
                foreach($haberes as $haber)
                {
                    $detalleLiquidacion = new DetalleLiquidacion();
                    $detalleLiquidacion->sid = Funciones::generarSID();
                    $detalleLiquidacion->liquidacion_id = $liquidacion->id;
                    $detalleLiquidacion->nombre = $haber['tipo']['nombre'];
                    $detalleLiquidacion->tipo = $haber['tipo']['imponible'] ? 'imponible' : 'no imponible';
                    $detalleLiquidacion->tipo_id = 1;
                    $detalleLiquidacion->valor = $haber['montoPesos'];
                    $detalleLiquidacion->valor_2 = $haber['monto'];
                    $detalleLiquidacion->valor_3 = $haber['moneda'];
                    $detalleLiquidacion->valor_4 = null;
                    $detalleLiquidacion->valor_5 = null;
                    $detalleLiquidacion->valor_6 = null;
                    $detalleLiquidacion->detalle_id = $haber['tipo']['id'];
                    $detalleLiquidacion->save(); 
                }
            }
            
            if($descuentos){                    
                foreach($descuentos as $descuento)
                {
                    $detalleLiquidacion = new DetalleLiquidacion();
                    $detalleLiquidacion->sid = Funciones::generarSID();
                    $detalleLiquidacion->liquidacion_id = $liquidacion->id;
                    $detalleLiquidacion->nombre = $descuento['tipo']['nombre'];
                    $detalleLiquidacion->tipo = 'descuento';
                    $detalleLiquidacion->tipo_id = 2;
                    $detalleLiquidacion->valor = $descuento['montoPesos'];
                    $detalleLiquidacion->valor_2 = $descuento['monto'];
                    $detalleLiquidacion->valor_3 = $descuento['moneda'];
                    if($descuento['tipo']['estructura']['id']==2){
                        $detalleLiquidacion->valor_4 = 1;   
                        $detalleLiquidacion->valor_5 = null;
                        $detalleLiquidacion->valor_6 = null;
                    }else if($descuento['tipo']['estructura']['id']==3){
                        $detalleLiquidacion->valor_4 = 2;   
                        $detalleLiquidacion->valor_5 = $descuento['tipo']['formaPago']['id'];
                        $detalleLiquidacion->valor_6 = $descuento['tipo']['afp']['id'];
                    }else{
                        if($descuento['tipo']['id']==2){                            
                            $detalleLiquidacion->valor_4 = 3;                              
                        }else{
                            $detalleLiquidacion->valor_4 = null;                              
                        }
                        $detalleLiquidacion->valor_5 = null;
                        $detalleLiquidacion->valor_6 = null;
                    }
                    $detalleLiquidacion->detalle_id = $descuento['tipo']['id'];
                    
                    $detalleLiquidacion->save(); 
                }
            }
            
            if($apvs){                    
                foreach($apvs as $apv)
                {                    
                    $detalleApvi = new DetalleApvi();
                    $detalleApvi->liquidacion_id = $liquidacion->id;
                    $detalleApvi->afp_id = $apv['afp']['id'];
                    $detalleApvi->numero_contrato = $apv['numeroContrato'];
                    $detalleApvi->forma_pago_id = $apv['formaPago']['id'];
                    $detalleApvi->monto = $apv['monto'];
                    $detalleApvi->moneda = $apv['moneda'];
                    $detalleApvi->cotizacion = $apv['montoPesos'];
                    $detalleApvi->cotizacion_depositos_convenidos = 0;
                    $detalleApvi->save();
                }
            }
            
            if($prestamos){                    
                foreach($prestamos as $prestamo)
                {
                    $detalleLiquidacion = new DetalleLiquidacion();
                    $detalleLiquidacion->sid = Funciones::generarSID();
                    $detalleLiquidacion->liquidacion_id = $liquidacion->id;
                    $detalleLiquidacion->nombre = $prestamo['nombreLiquidacion'];
                    $detalleLiquidacion->tipo = 'prestamo';
                    $detalleLiquidacion->tipo_id = 4;
                    $detalleLiquidacion->valor = $prestamo['cuotaPagar']['monto'];
                    $detalleLiquidacion->valor_2 = $prestamo['monto'];
                    $detalleLiquidacion->valor_3 = $prestamo['moneda'];
                    $detalleLiquidacion->valor_4 = $prestamo['cuotaPagar']['numero'];
                    $detalleLiquidacion->valor_5 = $prestamo['cuotas'];
                    $detalleLiquidacion->valor_6 = null;
                    $detalleLiquidacion->detalle_id = null;
                    $detalleLiquidacion->save(); 
                }
            }
            
            if($apvc){
                $detalleApvc = new DetalleApvc();
                $detalleApvc->liquidacion_id = $liquidacion->id;
                $detalleApvc->afp_id = $apvc['idAfp'];
                $detalleApvc->numero_contrato = $apvc['numeroContrato'];
                $detalleApvc->forma_pago_id = $apvc['idFormaPago'];
                $detalleApvc->monto = $apvc['monto'];
                $detalleApvc->moneda = $apvc['moneda'];
                $detalleApvc->cotizacion_trabajador = $apvc['cotizacionTrabajador'];
                $detalleApvc->cotizacion_empleador = $apvc['cotizacionEmpleador'];
                $detalleApvc->save();
            }
        
            $detalleIpsIslFonasa = new DetalleIpsIslFonasa();
            $detalleIpsIslFonasa->liquidacion_id = $liquidacion->id;
            if($empleado->prevision_id==9){
                $detalleIpsIslFonasa->ex_caja_id = $empleado->afp_id;
                $detalleIpsIslFonasa->tasa_cotizacion = $tasaAfp['tasaTrabajador'];
                $detalleIpsIslFonasa->cotizacion_obligatoria = $totalAfp['totalTrabajador'];
            }else{
                $detalleIpsIslFonasa->ex_caja_id = 0;
                $detalleIpsIslFonasa->tasa_cotizacion = 0;
                $detalleIpsIslFonasa->cotizacion_obligatoria = 0;
            }
            $detalleIpsIslFonasa->renta_imponible = $rentaImponible;
            $detalleIpsIslFonasa->renta_imponible_desahucio = 0;
            $detalleIpsIslFonasa->ex_caja_desahucio_id = 0;
            $detalleIpsIslFonasa->tasa_desahucio = 0;
            $detalleIpsIslFonasa->cotizacion_desahucio = 0;
            if($empleado->isapre_id==246){
                $detalleIpsIslFonasa->cotizacion_fonasa = $totalSalud['montoFonasa'];
            }else{
                $detalleIpsIslFonasa->cotizacion_fonasa = 0;
            }
            $detalleIpsIslFonasa->cotizacion_isl = $totalMutual;
            $detalleIpsIslFonasa->bonificacion = 0;
            $detalleIpsIslFonasa->descuento_cargas_isl = 0;
            $detalleIpsIslFonasa->bonos_gobierno = 0;
            $detalleIpsIslFonasa->save();      
            
            $detalleSalud = new DetalleSalud();
            $detalleSalud->liquidacion_id = $liquidacion->id;
            $detalleSalud->salud_id = $empleado->isapre_id;
            if($empleado->salud_id!=240 & $empleado->salud_id!=246){
                $detalleSalud->numero_fun = 145652;
                $detalleSalud->renta_imponible = $rentaImponible;
                $detalleSalud->moneda = $empleado->cotizacion_isapre;
                $detalleSalud->cotizacion_pactada = $empleado->monto_isapre;
                $detalleSalud->cotizacion_obligatoria = $totalSalud['obligatorio'];
                $detalleSalud->cotizacion_adicional = $totalSalud['adicional'];
                $detalleSalud->ges = 0;
            }else{
                $detalleSalud->numero_fun = 0;
                $detalleSalud->renta_imponible = $rentaImponible;
                $detalleSalud->moneda = null;
                $detalleSalud->cotizacion_pactada = 0;
                $detalleSalud->cotizacion_obligatoria = 0;
                $detalleSalud->cotizacion_adicional = 0;
                $detalleSalud->ges = 0;
            }
            $detalleSalud->save(); 
            
            if($empresa->caja_id!=257){
                $detalleCaja = new DetalleCaja();
                $detalleCaja->liquidacion_id = $liquidacion->id;
                $detalleCaja->caja_id = $empresa->caja_id;
                $detalleCaja->renta_imponible = $rentaImponible;
                $detalleCaja->creditos_personales = 0;
                $detalleCaja->descuento_dental = 0;
                $detalleCaja->descuentos_leasing = 0;
                $detalleCaja->descuentos_seguro = 0;
                $detalleCaja->otros_descuentos = 0;
                $detalleCaja->cotizacion = $totalSalud['montoCaja'];
                $detalleCaja->descuento_cargas = 0;
                $detalleCaja->otros_descuentos_1 = 0;
                $detalleCaja->otros_descuentos_2 = 0;
                $detalleCaja->bonos_gobierno = 0;
                $detalleCaja->codigo_sucursal = '';
                $detalleCaja->save();
            }
            
            if($empresa->mutual_id!=263){
                $detalleMutual = new DetalleMutual();
                $detalleMutual->liquidacion_id = $liquidacion->id;
                $detalleMutual->mutual_id = $empresa->mutual_id;
                $detalleMutual->renta_imponible = $rentaImponible;
                $detalleMutual->cotizacion_accidentes = $totalMutual;
                $detalleMutual->codigo_sucursal = 0;
                $detalleMutual->save();
            }
            
            if($empleado->seguro_desempleo==1){
                $detalleSeguroCesantia = new DetalleSeguroCesantia();
                $detalleSeguroCesantia->liquidacion_id = $liquidacion->id;
                $detalleSeguroCesantia->renta_imponible = $rentaImponible;
                $detalleSeguroCesantia->aporte_trabajador = $totalSeguroCesantia['total'];
                $detalleSeguroCesantia->afc_trabajador = $totalSeguroCesantia['afc'];
                $detalleSeguroCesantia->aporte_empleador = $totalSeguroCesantia['totalEmpleador'];
                $detalleSeguroCesantia->afc_empleador = $totalSeguroCesantia['afcEmpleador'];
                $detalleSeguroCesantia->save();
            }
            
            if($liquidacion->movimiento_personal==3){
                $detallePagadorSubsidio = new DetallePagadorSubsidio();
                $detallePagadorSubsidio->liquidacion_id = $liquidacion->id;
                $detallePagadorSubsidio->rut = 0;
                $detallePagadorSubsidio->digito = 0;
                $detallePagadorSubsidio->save();
            }
            
                                  
            $liquidaciones[] = $miLiquidacion;
            $listaPDF[] = array(
                'liquidacion' => $miLiquidacion,
                'sidDocumento' => $documento->sid
            );
            $destination = public_path() . '/stories/' . $filename;
            //$dompdf->set_option('isHtml5ParserEnabled', true);
            //$html = View::make('pdf.liquidacion', array('liquidaciones' => $liquidaciones))->render();

            //File::put($destination, PDF::load(utf8_decode($html), 'A4', 'portrait')->output());
            $pdf = new \Thujohn\Pdf\Pdf();
            $content = $pdf->load(View::make('pdf.liquidacion', array('liquidacion' => $miLiquidacion)))->output();
            File::put($destination, $content);
        }
        
        PDF::clear();
        
        
        /*$filename = date("d-m-Y-H-i-s") . "_Liquidacion_" . $mes->nombre . "_" . $mes->anio . ".xls";
        
        Excel::create("Liquidacion", function($excel) use($liquidacion) {
            $excel->sheet("Liquidacion", function($sheet) use($liquidacion) {
                $sheet->loadView('pdf.liquidacion')->with('liquidacion', $liquidacion);
            });
        })->store('xls', public_path('stories'));
        
        $destination = public_path('stories/' . $filename);*/
        if(count($liquidaciones)>1){
            $mensaje = "Las Liquidaciones de Sueldo fueron ingresadas correctamente";
        }else{
            $mensaje = "La Liquidación de Sueldo fue ingresada correctamente";
        }

        $datos = array(
            'success' => true,
            'mensaje' => $mensaje,
            'datos' => $listaPDF,
            'trabajadores' => $trabajadores
        );
        
        return Response::json($datos);
        /*
            $trabajador = Trabajador::whereSid($sid)->first();   
            $empleado = $trabajador->ficha();
            $idTrabajador = $trabajador->id;
            $liquidacion = array(
                'id' => $trabajador->id,
                'sid' => $trabajador->sid,
                'rutFormato' => $trabajador->rut_formato(),
                'rut' => $trabajador->rut,
                'nombres' => $empleado->nombres,
                'apellidos' => $empleado->apellidos,
                'nombreCompleto' => $empleado->nombreCompleto(),
                'cargo' => array(
                    'id' => $empleado->cargo ? $empleado->cargo->id : "",
                    'nombre' => $empleado->cargo ? $empleado->cargo->nombre : ""
                ),
                'seccion' => array(
                    'id' => $empleado->seccion ? $empleado->seccion->id : "",
                    'nombre' => $empleado->seccion ? $empleado->seccion->nombre : ""
                ),
                'fechaIngreso' => $empleado->fecha_ingreso,
                'tipoContrato' => array(
                    'id' => $empleado->tipoContrato ? $empleado->tipoContrato->id : "",
                    'nombre' => $empleado->tipoContrato ? $empleado->tipoContrato->nombre : ""
                ),
                'monedaSueldo' => $empleado->moneda_sueldo,
                'sueldoBase' => $empleado->sueldo_base,
                'colacion' => array(
                    'moneda' => $empleado->moneda_colacion,
                    'monto' => $empleado->monto_colacion,
                    'montoPesos' => $trabajador->totalColacion(),
                    'proporcional' => $empleado->proporcional_colacion ? true : false
                ),
                'movilizacion' => array(
                    'moneda' => $empleado->moneda_movilizacion,
                    'monto' => $empleado->monto_movilizacion,
                    'montoPesos' => $trabajador->totalMovilizacion(),
                    'proporcional' => $empleado->proporcional_movilizacion ? true : false
                ),
                'viatico' => array(
                    'moneda' => $empleado->moneda_viatico,
                    'monto' => $empleado->monto_viatico,
                    'montoPesos' => $trabajador->totalViatico(),
                    'proporcional' => $empleado->proporcional_viatico ? true : false
                ),
                'afp' => array(
                    'id' => $empleado->afp ? $empleado->afp->id : "",
                    'nombre' => $empleado->afp ? $empleado->afp->glosa : ""
                ),
                'seguroDesempleo' => $empleado->seguro_desempleo ? true : false,
                'afpSeguro' => array(
                    'id' => $empleado->afpSeguro ? $empleado->afpSeguro->id : "",
                    'nombre' => $empleado->afpSeguro ? $empleado->afpSeguro->glosa : ""
                ),
                'isapre' => array(
                    'id' => $empleado->isapre ? $empleado->isapre->id : "",
                    'nombre' => $empleado->isapre ? $empleado->isapre->glosa : ""
                ),
                'cotizacionIsapre' => $empleado->cotizacion_isapre,
                'montoIsapre' => $empleado->monto_isapre,
                'sindicato' => $empleado->sindicato ? true : false,
                'monedaSindicato' => $empleado->moneda_sindicato,
                'montoSindicato' => $empleado->monto_sindicato,
                'estado' => $empleado->estado,
                'diasTrabajados' => $trabajador->diasTrabajados(),
                'sueldoDiario' => $trabajador->sueldoDiario(),
                'sueldo' => $trabajador->sueldo(),
                'gratificacion' => $trabajador->gratificacion(),
                'horasExtra' => $trabajador->horasExtraPagar(),
                'imponibles' => $trabajador->sumaImponibles(),
                'cargasFamiliares' => $empleado->cargasFamiliares(),
                'noImponibles' => $trabajador->noImponibles(),
                'rentaImponible' => $trabajador->rentaImponible(),
                'tasaAfp' => $trabajador->tasaAfp(),
                'totalAfp' => $trabajador->totalAfp(),
                'totalSalud' => $trabajador->totalSalud(),
                'totalSeguroCesantia' => $trabajador->totalSeguroCesantia(),
                'totalDescuentosPrevisionales' => $trabajador->totalDescuentosPrevisionales(),
                'baseImpuestoUnico' => $trabajador->baseImpuestoUnico(),
                'tramoImpuesto' => $trabajador->tramoImpuesto()->factor,
                'impuestoDeterminado' => $trabajador->impuestoDeterminado(),
                'totalOtrosDescuentos' => $trabajador->totalOtrosDescuentos(),
                'apvs' => $empleado->misApvs(),
                'haberes' => $trabajador->misHaberes(),
                'descuentos' => $trabajador->misDescuentos(),
                'prestamos' => $trabajador->misCuotasPrestamo(),
                'sueldoLiquido' => $trabajador->sueldoLiquido()
            );
        
        $data = array(
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $liquidacion
        );
        
        return View::make('liquidacion', ['trabajador' => $liquidacion]);
        //return Response::json($data);  
        */ 
    }
    
    
    public function liquidacionPDF($sid)
    {
        $name = Documento::whereSid($sid)->first()['nombre'];
        
        $destination = public_path() . '/stories/' . $name;
      
        return Response::make(file_get_contents($destination), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$name.'"'
        ]);      
		
    }

    /**
     * Show the form for editing the specified resource. 
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($sid)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($sid)
    {
        $trabajador = Trabajador::whereSid($sid)->first();
        $datos = $this->get_datos_formulario();
        $idFicha = $datos['ficha_id'];
        $idMes = \Session::get('mesActivo')->id;
        $mes = \Session::get('mesActivo')->mes;
        $ficha = FichaTrabajador::find($idFicha);
        $errores = Trabajador::errores($datos);              
        $ficha = FichaTrabajador::find($idFicha);    
        if($datos['nueva_ficha'] && $ficha->mes_id!=$idMes){
            $ficha = new FichaTrabajador();
            $ficha->trabajador_id = $trabajador->id;            
            $ficha->fecha = $mes;
            $ficha->mes_id = $idMes;
        }
        if($ficha->estado==='Finiquitado' && $ficha->fecha_finiquito){
            $ficha->fecha_finiquito = null;            
        }
        if($datos['estado']=='Ingresado'){   
            $ficha->tramo_id = FichaTrabajador::calcularTramo(Funciones::convertir($datos['sueldo_base'], $datos['moneda_sueldo']));
            if($ficha->estado==='En Creación'){
                $trabajador->calcularMisVacaciones($ficha->fecha_reconocimiento);
                $trabajador->crearUser($datos['estadoUser']);
                $ficha->tramo_id = FichaTrabajador::calcularTramo(Funciones::convertir($datos['sueldo_base'], $datos['moneda_sueldo']));         
                if($ficha->semana_corrida==1){
                    $trabajador->crearSemanaCorrida();
                }
            }
        }
        
        if(!$errores and $trabajador){
            $trabajador->rut = $datos['rut'];
            $trabajador->save();   
            
            $ficha->nombres = $datos['nombres'];
            $ficha->apellidos = $datos['apellidos'];
            $ficha->nacionalidad_id = $datos['nacionalidad_id'];
            $ficha->sexo = $datos['sexo'];
            $ficha->estado_civil_id = $datos['estado_civil_id'];
            $ficha->fecha_nacimiento = $datos['fecha_nacimiento'];
            $ficha->direccion = $datos['direccion'];
            $ficha->comuna_id =  $datos['comuna_id'];
            $ficha->telefono =  $datos['telefono'];
            $ficha->celular =  $datos['celular'];
            $ficha->celular_empresa =  $datos['celular_empresa'];
            $ficha->email =  $datos['email'];
            $ficha->email_empresa =  $datos['email_empresa'];
            $ficha->tipo_id =  $datos['tipo_id'];
            $ficha->cargo_id = $datos['cargo_id'];
            $ficha->titulo_id = $datos['titulo_id'];
            $ficha->gratificacion = $datos['gratificacion'];
            $ficha->tienda_id = $datos['tienda_id'];
            $ficha->centro_costo_id = $datos['centro_costo_id'];
            $ficha->seccion_id = $datos['seccion_id'];
            $ficha->tipo_cuenta_id = $datos['tipo_cuenta_id'];
            $ficha->banco_id = $datos['banco_id'];
            $ficha->numero_cuenta = $datos['numero_cuenta'];
            $ficha->fecha_ingreso = $datos['fecha_ingreso'];
            $ficha->fecha_reconocimiento = $datos['fecha_reconocimiento'];
            $ficha->tipo_contrato_id = $datos['tipo_contrato_id'];
            $ficha->fecha_vencimiento = $datos['fecha_vencimiento'];
            $ficha->tipo_jornada_id = $datos['tipo_jornada_id'];
            $ficha->semana_corrida = $datos['semana_corrida'];
            $ficha->moneda_sueldo = $datos['moneda_sueldo'];
            $ficha->sueldo_base = $datos['sueldo_base'];
            $ficha->tipo_trabajador = $datos['tipo_trabajador'];
            $ficha->exceso_retiro = $datos['exceso_retiro'];
            $ficha->moneda_colacion = $datos['moneda_colacion'];
            $ficha->proporcional_colacion = $datos['proporcional_colacion'];
            $ficha->monto_colacion = $datos['monto_colacion'];
            $ficha->moneda_movilizacion = $datos['moneda_movilizacion'];
            $ficha->proporcional_movilizacion = $datos['proporcional_movilizacion'];
            $ficha->monto_movilizacion = $datos['monto_movilizacion'];
            $ficha->moneda_viatico = $datos['moneda_viatico'];
            $ficha->proporcional_viatico = $datos['proporcional_viatico'];
            $ficha->monto_viatico = $datos['monto_viatico'];
            $ficha->prevision_id = $datos['prevision_id'];
            $ficha->afp_id = $datos['afp_id'];
            $ficha->seguro_desempleo = $datos['seguro_desempleo'];
            $ficha->afp_seguro_id = $datos['afp_seguro_id'];
            $ficha->isapre_id = $datos['isapre_id'];
            $ficha->cotizacion_isapre = $datos['cotizacion_isapre'];
            $ficha->monto_isapre = $datos['monto_isapre'];
            $ficha->sindicato = $datos['sindicato'];
            $ficha->moneda_sindicato = $datos['moneda_sindicato'];
            $ficha->monto_sindicato = $datos['monto_sindicato'];
            $ficha->estado = $datos['estado'];            
            $ficha->save();            
            
            $misHaberes = $trabajador->comprobarHaberes($datos['haberes']);
            $misDescuentos = $trabajador->comprobarDescuentos($datos['descuentos']);
            $misApvs = $ficha->comprobarApvs($datos['apvs']);
            $misCargas = $ficha->comprobarCargas($datos['cargas']);
            
            if($misHaberes['create']){
                foreach($misHaberes['create'] as $haber){
                    $nuevoHaber = new Haber();
                    $nuevoHaber->sid = Funciones::generarSID();
                    $nuevoHaber->trabajador_id = $trabajador->id;
                    $nuevoHaber->tipo_haber_id = $haber['tipo_haber_id'];
                    $nuevoHaber->mes_id = null;
                    $nuevoHaber->por_mes = 0;
                    $nuevoHaber->rango_meses = 0;
                    $nuevoHaber->permanente = 1;
                    $nuevoHaber->todos_anios = 0;
                    $nuevoHaber->mes = null;
                    $nuevoHaber->desde = null;
                    $nuevoHaber->hasta = null;
                    $nuevoHaber->moneda = $haber['moneda'];
                    $nuevoHaber->monto = $haber['monto'];
                    $nuevoHaber->save();  
                }
            }elseif($misHaberes['destroy']){
                foreach($misHaberes['destroy'] as $haber){
                    $id = $haber['id'];
                    Haber::find($id)->delete();
                }
            }elseif($misHaberes['update']){
                foreach($misHaberes['update'] as $haber){
                    $id = $haber['id'];
                    $nuevoHaber = Haber::find($id);
                    $nuevoHaber->tipo_haber_id = $haber['tipo_haber_id'];
                    $nuevoHaber->mes_id = null;
                    $nuevoHaber->por_mes = 0;
                    $nuevoHaber->rango_meses = 0;
                    $nuevoHaber->permanente = 1;
                    $nuevoHaber->todos_anios = 0;
                    $nuevoHaber->mes = null;
                    $nuevoHaber->desde = null;
                    $nuevoHaber->hasta = null;
                    $nuevoHaber->moneda = $haber['moneda'];
                    $nuevoHaber->monto = $haber['monto'];
                    $nuevoHaber->save();  
                }
            }
            
            if($misDescuentos['create']){
                foreach($misDescuentos['create'] as $descuento){
                    $nuevoDescuento = new Descuento();
                    $nuevoDescuento->sid = Funciones::generarSID();
                    $nuevoDescuento->trabajador_id = $trabajador->id;
                    $nuevoDescuento->tipo_descuento_id = $descuento['tipo_descuento_id'];
                    $nuevoDescuento->mes_id = null;
                    $nuevoDescuento->por_mes = 0;
                    $nuevoDescuento->rango_meses = 0;
                    $nuevoDescuento->permanente = 1;
                    $nuevoDescuento->todos_anios = 0;
                    $nuevoDescuento->mes = null;
                    $nuevoDescuento->desde = null;
                    $nuevoDescuento->hasta = null;
                    $nuevoDescuento->moneda = $descuento['moneda'];
                    $nuevoDescuento->monto = $descuento['monto'];
                    $nuevoDescuento->save();  
                }
            }elseif($misDescuentos['destroy']){
                foreach($misDescuentos['destroy'] as $descuento){
                    $id = $descuento['id'];
                    Descuento::find($id)->delete();
                }
            }elseif($misDescuentos['update']){
                foreach($misDescuentos['update'] as $descuento){
                    $id = $descuento['id'];
                    $nuevoDescuento = Descuento::find($id);
                    $nuevoDescuento->tipo_descuento_id = $descuento['tipo_descuento_id'];
                    $nuevoDescuento->mes_id = null;
                    $nuevoDescuento->por_mes = 0;
                    $nuevoDescuento->rango_meses = 0;
                    $nuevoDescuento->permanente = 1;
                    $nuevoDescuento->todos_anios = 0;
                    $nuevoDescuento->mes = null;
                    $nuevoDescuento->desde = null;
                    $nuevoDescuento->hasta = null;
                    $nuevoDescuento->moneda = $descuento['moneda'];
                    $nuevoDescuento->monto = $descuento['monto'];
                    $nuevoDescuento->save(); 
                }
            }
            
            if($misApvs['create']){
                foreach($misApvs['create'] as $apv){
                    $nuevoApv = new Apv();
                    $nuevoApv->sid = Funciones::generarSID();
                    $nuevoApv->ficha_trabajador_id = $ficha->id;
                    $nuevoApv->afp_id = $apv['afp_id'];
                    $nuevoApv->forma_pago = $apv['forma_pago'];
                    $nuevoApv->moneda = $apv['moneda'];
                    $nuevoApv->regimen = strtolower($apv['regimen']);
                    $nuevoApv->monto = $apv['monto'];
                    $nuevoApv->save();  
                }
            }elseif($misApvs['destroy']){
                foreach($misApvs['destroy'] as $apv){
                    $id = $apv['id'];
                    Apv::find($id)->delete();
                }
            }elseif($misApvs['update']){
                foreach($misApvs['update'] as $apv){
                    $id = $apv['id'];
                    $nuevoApv = Apv::find($id);
                    $nuevoApv->afp_id = $apv['afp_id'];
                    $nuevoApv->forma_pago = $apv['forma_pago'];
                    $nuevoApv->moneda = $apv['moneda'];
                    $nuevoApv->regimen = strtolower($apv['regimen']);
                    $nuevoApv->monto = $apv['monto'];
                    $nuevoApv->save(); 
                }
            }
            
            if($misCargas['create']){
                foreach($misCargas['create'] as $carga){
                    $nuevaCarga = new Carga();
                    $nuevaCarga->sid = Funciones::generarSID();
                    $nuevaCarga->ficha_trabajador_id = $ficha->id;
                    $nuevaCarga->tipo_carga_id = $carga['tipo'];
                    $nuevaCarga->parentesco = $carga['parentesco'];
                    $nuevaCarga->es_carga = $carga['esCarga'];
                    $nuevaCarga->nombre_completo = $carga['nombreCompleto'];
                    $nuevaCarga->rut = $carga['rut'];
                    $nuevaCarga->fecha_nacimiento = $carga['fechaNacimiento'];
                    $nuevaCarga->sexo = $carga['sexo'];
                    $nuevaCarga->save();  
                }
            }elseif($misCargas['destroy']){
                foreach($misCargas['destroy'] as $carga){
                    $id = $carga['id'];
                    Carga::find($id)->delete();
                }
            }elseif($misCargas['update']){
                foreach($misCargas['update'] as $carga){
                    $id = $carga['id'];
                    $nuevaCarga = Carga::find($id);
                    $nuevaCarga->parentesco = $carga['parentesco'];
                    $nuevaCarga->es_carga = $carga['esCarga'];
                    $nuevaCarga->tipo_carga_id = $carga['tipo'];
                    $nuevaCarga->nombre_completo = $carga['nombreCompleto'];
                    $nuevaCarga->rut = $carga['rut'];
                    $nuevaCarga->fecha_nacimiento = $carga['fechaNacimiento'];
                    $nuevaCarga->sexo = $carga['sexo'];
                    $nuevaCarga->save();  
                }
            }                        
            
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue actualizada correctamente",
                'sid' => $trabajador->sid
            );
        }else{
            $respuesta = array(
                'success' => false,
                'mensaje' => "La acción no pudo ser completada debido a errores en la información ingresada",
                'errores' => $errores
            );
        }
        
        return Response::json($respuesta);
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($sid)
    {
        $mensaje="La Información fue eliminada correctamente";
        $trabajador = Trabajador::whereSid($sid)->first();
        $trabajador->eliminarDatos();
        $trabajador->delete();        
        
        return Response::json(array('success' => true, 'mensaje' => $mensaje));
    }
    
    public function get_datos_formulario()
    {
        $datos = array(
            'rut' => Input::get('rut'),
            'nueva_ficha' => Input::get('nuevaFicha'),
            'ficha_id' => Input::get('id'),
            'nombres' => Input::get('nombres'),
            'apellidos' => Input::get('apellidos'),
            'nacionalidad_id' => Input::get('nacionalidad')['id'],
            'sexo' => Input::get('sexo'),
            'estado_civil_id' => Input::get('estadoCivil')['id'],
            'fecha_nacimiento' => Input::get('fechaNacimiento'),
            'direccion' => Input::get('direccion'),
            'comuna_id' => Input::get('comuna')['id'],
            'telefono' => Input::get('telefono'),
            'celular' => Input::get('celular'),
            'celular_empresa' => Input::get('celularEmpresa'),
            'email' => Input::get('email'),
            'email_empresa' => Input::get('emailEmpresa'),
            'tipo_id' => Input::get('tipo')['id'],
            'cargo_id' => Input::get('cargo')['id'],
            'titulo_id' => Input::get('titulo')['id'],
            'seccion_id' => Input::get('seccion')['id'],
            'tipo_cuenta_id' => Input::get('tipoCuenta')['id'],
            'banco_id' => Input::get('banco')['id'],
            'numero_cuenta' => Input::get('numeroCuenta'),
            'fecha_ingreso' => Input::get('fechaIngreso'),
            'fecha_reconocimiento' => Input::get('fechaReconocimiento'),
            'tipo_contrato_id' => Input::get('tipoContrato')['id'],
            'fecha_vencimiento' => Input::get('fechaVencimiento'),
            'tipo_jornada_id' => Input::get('tipoJornada')['id'],
            'semana_corrida' => Input::get('semanaCorrida'),
            'moneda_sueldo' => Input::get('monedaSueldo'),
            'sueldo_base' => Input::get('sueldoBase'),
            'tipo_trabajador' => Input::get('tipoTrabajador'),
            'exceso_retiro' => Input::get('excesoRetiro'),
            'moneda_colacion' => Input::get('monedaColacion'),
            'proporcional_colacion' => Input::get('proporcionalColacion'),
            'monto_colacion' => Input::get('montoColacion'),
            'moneda_movilizacion' => Input::get('monedaMovilizacion'),
            'proporcional_movilizacion' => Input::get('proporcionalMovilizacion'),
            'monto_movilizacion' => Input::get('montoMovilizacion'),
            'moneda_viatico' => Input::get('monedaViatico'),
            'proporcional_viatico' => Input::get('proporcionalViatico'),
            'monto_viatico' => Input::get('montoViatico'),
            'prevision_id' => Input::get('prevision')['id'],
            'afp_id' => Input::get('afp')['id'],
            'seguro_desempleo' => Input::get('seguroDesempleo'),
            'afp_seguro_id' => Input::get('afpSeguro')['id'],
            'tienda_id' => Input::get('tienda')['id'],
            'centro_costo_id' => Input::get('centroCosto')['id'],
            'isapre_id' => Input::get('isapre')['id'],
            'cotizacion_isapre' => Input::get('cotizacionIsapre'),
            'monto_isapre' => Input::get('montoIsapre'),
            'sindicato' => Input::get('sindicato'),
            'moneda_sindicato' => Input::get('monedaSindicato'),
            'monto_sindicato' => Input::get('montoSindicato'),
            'gratificacion' => Input::get('gratificacion'),
            'estado' => Input::get('estado'),
            'apvs' => Input::get('apvs'),
            'descuentos' => Input::get('descuentos'),
            'haberes' => Input::get('haberes'),
            'estadoUser' => Input::get('estadoUser'),
            'cargas' => Input::get('cargas')
        );
        return $datos;
    }

}
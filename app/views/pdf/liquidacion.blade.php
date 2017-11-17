<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>{{ $liquidacion['nombreCompleto'] }}</title>
  
  <style type="text/css">     
    
    html, body {
      font-family: sans-serif;
      font-size: 12px;  
      padding: 0;
      height: 100%;
      margin: 0;
    }
    .page {
      margin: 20px;
      border: 2px solid black;
    }
    .contenedor{  
      padding: 10px;
      border-bottom: 1px solid black;
    }
    .contenedor2{  
      padding: 10px;
    }
    .pie{  
      border: 1px solid black;
      padding: 5px;
      margin-top: 20px;
    }
    .encabezado{
      width: 100%;
      margin-top: 10px;
    }
    .encabezado td{
      padding-top: 5px;      
    }
    .resumen{
      border: 1px solid black;
      border-collapse: collapse;
      width: 100%;
      margin-top: 20px;
      font-size: 11px;
    }
    table.resumen td{
      border: 1px solid black;
    }
    tr.valores td{
      border: 1px solid black;
      font-weight: 800;
      text-align: right;
      padding-top: 15px;
      padding-bottom: 5px;
      padding-right: 5px;
      font-size: 12px;
    }  
    .contenido{
      border-collapse: collapse;
      margin: 0px;
      font-size: 11px;
    }
    .contenido2{
      border-collapse: collapse;
      margin: 0px;
      font-size: 11px;
    }
    .cont1{
      width: 100%;
      border-collapse: collapse;
    }
    .cont1 tr td{
      border-right: 1px solid black;
      border-collapse: collapse;
      width: 70%;
    }
    .cont2 tr td{
      border-right: 1px solid black;
      border-collapse: collapse;
      width: 70%;
    }
    .cont2{
      width: 100%;
      border-collapse: collapse;
    }
    .col{
      position: absolute;
      border-collapse: collapse;
      margin: 0;
      width: 100%;
      border: 0;
    }
    .totalesContenido{
      border-collapse: collapse;
      width: 100%;
      margin: 0px;
      font-size: 11px;
    }
    .totalesContenido td{
      border-collapse: collapse;
      border: 1px solid black;
    }
    .liquidoContenido{
      border-collapse: collapse;
      width: 100%;
      margin: 0px;
      font-size: 11px;
    }
    .liquidoContenido td{
      border-collapse: collapse;
      border: 1px solid black;
    }
    .contenidoContenedor{
      border-collapse: collapse;
      height: 360px;
      border-top: 1px solid black;
      border-left: 1px solid black;
      border-right: 1px solid black;
      margin: 0px;
      margin-top: 10px;
      padding: 0;
    }
    .totalesContenedor{
      border-collapse: collapse;
      margin: 0px;
      padding: 0;
    }
    .liquidoContenedor{
      padding: 0;
      border-collapse: collapse;
      margin: 0;
    }
    .tablaContenidos{
      padding: 0;
      border-collapse: collapse;
      width: 100%;
    }
    .tablaContenidos tr td{

      border-collapse: collapse;
      width: 100%;
    }
    .fila{
      border-collapse: collapse;
      float: left;
      border-right: 1px solid black;     
      width: 276px;
      margin: 0;
      height: 100px;
    }
    .fila:nth-child(even){
      width: 86px;
    }
    table.contenido td{
      text-align: left;
    }
    table.contenido2 td{
      text-align: left;
    }
    .resumen tr.titulos td{
      border: 1px solid black;
      padding-top: 5px;
      text-align: center;
      font-size: 13px;
    }
    /*.contenido tr.titulos td{
      border-right: 1px solid black;
      width: 32%;
      padding-top: 5px;
      font-size: 13px;
      text-align: center;
    }
    .contenido2 tr.titulos2 td{
      width: 32%;
      padding-top: 5px;
      border-right: 1px solid black;
      font-size: 13px;
      text-align: center;
    }*/
    tr.detalle td{
      width: 32%;
      padding-top: 5px;
      padding-left: 5px;
      text-align: left;
      border-right: 1px solid black;
    }
    tr.detalle2 td{
      width: 32%;
      padding-top: 5px;
      padding-left: 5px;
      text-align: left;
    }
    tr.totales td{
      width: 32%;
      padding-top: 5px;
      padding-left: 5px;
      text-align: right;
      border-right: 1px solid black;
    }    
    tr.liquido td{
      width: 32%;
      padding-top: 5px;
      padding-left: 5px;
      text-align: right;
      border-right: 1px solid black;
    }
    .final{
      margin-top: 10px;  
      border-collapse: collapse;
      width: 100%;
      font-size: 11px;
    }
    .title td{
      padding-top: 8px;
      padding-bottom: 8px;
      border-right: 1px solid black;
      border-bottom: 1px solid black;
      text-align: center;
      font-size: 13px;
    }
    .conforme{
      margin-top: 20px;
      font-size: 13px;
    }
    .firma{
      margin-top: 85px;
      margin-bottom: 25px;
      margin-right: 10px;
    }
    table.final td{
      border: 1px solid black;
      padding-top: 5px;
      padding-left: 5px;
      text-align: right;
    }
    tr.detalle td:nth-child(even){
      font-weight: 800;
      text-align: right;
    }
    tr.detalle2 td:nth-child(even){
      font-weight: 800;
      text-align: right;
    }
    .contenido tr.titulos td:nth-child(even){
      text-align: right;
    }
    .contenido2 tr.titulos2 td:nth-child(even){
      text-align: right;
    }
    .liquidoContenido tr.liquido td:nth-child(even){
      font-weight: 800;
    }
    .totalesContenido tr.totales td:nth-child(even){
      font-weight: 800;
    }  
    .cont1 tr td:nth-child(even){
      text-align: right;
      font-weight: 800;
      width: 30%;
    }   
    .cont2 tr td:nth-child(even){
      text-align: right;
      border-right: 0;
      font-weight: 800;
      width: 30%;
    }    
    .tablaFirma{
      width: 100%;
    }
    .tablaFirma td{
      padding: 5px;
      text-align: center;
      font-size: 13px;
    }
    
  </style>
  
</head>
  <body>
    <div class="page">

      <div class="contenedor">

        <div>

          <table class="encabezado">
            <tbody>
              <tr>
                <td><b>{{ $liquidacion['empresa']['razon_social'] }}</b></td>
                <td>RUT : <b>{{ $liquidacion['rutEmpresa'] }}</b></td>
              </tr>
              <tr>
                <td>Liquidación de Sueldo del mes de {{ $liquidacion['mes'] }}</td>
                <td>Fecha de Ingreso : <b>{{ $liquidacion['fechaIngreso'] }}</b></td>                
              </tr>
              <tr>
                <td>Trabajador : <b>{{ $liquidacion['nombreCompleto'] }}</b></td>
                <td>RUT : <b>{{ $liquidacion['rutFormato'] }}</b></td>
              </tr>
              <tr>
                <td>
                    @if($liquidacion['seccion']['nombre'])
                        Sección :  <b>{{ $liquidacion['seccion']['nombre'] }}</b>
                    @endif
                </td>
                <td>
                    @if($liquidacion['cargo']['nombre'])
                        Cargo :  <b>{{ $liquidacion['cargo']['nombre'] }}</b>
                    @endif
                </td>                
              </tr>
            </tbody>
          </table>
        </div>

        <div>

          <table class="resumen">
            <tbody>
              <tr class="titulos">
                <td>Tot. Imponible</td>
                <td>No Imponible</td>
                <td>Renta Imponible</td>
                <td>Afec. Impuesto</td>
                <td>Pacto Isapre</td>
                <td>DL 889</td>
                <td>Días Trabajados</td>
              </tr>
              <tr class="valores">
                <td>{{ Funciones::formatoPesos($liquidacion['imponibles']) }}</td>
                <td>{{ Funciones::formatoPesos($liquidacion['noImponibles']) }}</td>
                <td>{{ Funciones::formatoPesos($liquidacion['rentaImponible']) }}</td>
                <td>
                  @if($liquidacion['impuestoDeterminado']>0)
                    {{ Funciones::formatoPesos($liquidacion['baseImpuestoUnico']) }}
                  @else
                    {{ Funciones::formatoPesos(0) }} 
                  @endif
                </td>
                <td>
                    @if($liquidacion['isapre']['id']!=246 && $liquidacion['isapre']['id']!=240)
                        {{ Funciones::formatoPesos($liquidacion['totalSalud']['total']) }}
                    @else
                        0 
                    @endif
                </td>
                <td>DL 889</td>
                <td>{{ $liquidacion['diasTrabajados'] }}</td>
              </tr>

            </tbody>
          </table>

        </div>

        <div class="contenidoContenedor">

          <table class="tablaContenidos">
            <tbody>
              <tr>
                <td valign="top">
                  <table class="cont1">
                    <tbody>
                      <tr class="title">
                        <td>HABERES</td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>Sueldo Base</td>
                        <td>{{ Funciones::formatoPesos($liquidacion['sueldoBase']) }}</td>
                      </tr>
                      @if($liquidacion['gratificacion']>0)
                        <tr>
                          <td>Gratificación Legal</td>
                          <td>{{ Funciones::formatoPesos($liquidacion['gratificacion']) }}</td>
                        </tr>
                      @endif
                      @if($liquidacion['horasExtra']['cantidad']>0)
                        <tr>
                          <td>Horas Extra ({{ $liquidacion['horasExtra']['cantidad'] }})</td>
                          <td>{{ Funciones::formatoPesos($liquidacion['horasExtra']['total']) }}</td>
                        </tr>
                      @endif
                      @if($liquidacion['semanaCorrida']>0 && $liquidacion['isSemanaCorrida'])
                        <tr>
                          <td>Semana Corrida</td>
                          <td>{{ Funciones::formatoPesos($liquidacion['semanaCorrida']) }}</td>
                        </tr>
                      @endif
                      @if($liquidacion['diasDescontados']['monto']>0)
                        <tr>
                          <td>{{ $liquidacion['diasDescontados']['diasCalendario'] }} Días Descontados (-)</td>
                          <td>{{ Funciones::formatoPesos($liquidacion['diasDescontados']['monto']) }}</td>
                        </tr>
                      @endif
                      @if(count($liquidacion['haberesImponibles'])>0)
                        <tr>
                          <td><b>Imponibles:</b></td>
                          <td></td>
                        </tr>
                        @foreach($liquidacion['haberesImponibles'] as $haber)
                          <tr>
                            <td style="padding-left: 10px;">{{ $haber['tipo']['nombre'] }}</td>
                            <td>{{ Funciones::formatoPesos($haber['montoPesos']) }}</td>
                          </tr>
                        @endforeach
                      @endif
					  @if(count($liquidacion['haberesNoImponibles'])>0 || $liquidacion['colacion']['monto']>0 || $liquidacion['movilizacion']['monto']>0 || $liquidacion['viatico']['monto']>0 || $liquidacion['cargasFamiliares']['monto']>0)
                        <tr>
                          <td><b>No Imponibles:</b></td>
                          <td></td>
                        </tr>
                      @endif
                      @if($liquidacion['colacion']['monto']>0)
                        <tr>
                          <td style="padding-left: 10px;">Colación</td>
                          <td>{{ Funciones::formatoPesos($liquidacion['colacion']['monto']) }}</td>
                        </tr>
                      @endif
                      @if($liquidacion['movilizacion']['monto']>0)
                        <tr>
                          <td style="padding-left: 10px;">Movilización</td>
                          <td>{{ Funciones::formatoPesos($liquidacion['movilizacion']['monto']) }}</td>
                        </tr>
                      @endif
                      @if($liquidacion['viatico']['monto']>0)
                        <tr>
                          <td style="padding-left: 10px;">Viático</td>
                          <td>{{ Funciones::formatoPesos($liquidacion['viatico']['monto']) }}</td>
                        </tr>
                      @endif
                      @if($liquidacion['cargasFamiliares']['monto']>0)
                        <tr>
                          <td style="padding-left: 10px;">Asignación Familiar</td>
                          <td>{{ Funciones::formatoPesos($liquidacion['cargasFamiliares']['monto']) }}</td>
                        </tr>
                      @endif
					  @if(count($liquidacion['haberesNoImponibles'])>0)
                        @foreach($liquidacion['haberesNoImponibles'] as $haber)
                          <tr>
                            <td style="padding-left: 10px;">{{ $haber['tipo']['nombre'] }}</td>
                            <td>{{ Funciones::formatoPesos($haber['montoPesos']) }}</td>
                          </tr>
                        @endforeach
                      @endif
                    </tbody>
                  </table>
                </td>
                <div class="col">

                </div>
                <td valign="top">
                  <table class="cont2">
                    <tbody>
                      <tr class="title">
                        <td>DESCUENTOS</td>
                        <td></td>
                      </tr>
                      @if ($liquidacion['totalSalud']['obligatorio']>0)
                        <tr>
                          <td>Salud {{ $liquidacion['isapre']['nombre'] }}</td>                    
                          <td>{{ Funciones::formatoPesos($liquidacion['totalSalud']['obligatorio']) }}</td>
                        </tr> 
                      @endif
                      @if ($liquidacion['totalSalud']['adicional']>0)
                        <tr >
                          <td>Adicional Isapre</td>
                          <td>{{ Funciones::formatoPesos($liquidacion['totalSalud']['adicional']) }}</td>
                        </tr> 
                      @endif
                      @if ($liquidacion['afp']['id']!=35)
                        <tr>
                          <td>{{ $liquidacion['tasaAfp'] }}% Previsión {{ $liquidacion['afp']['nombre'] }}</td>
                          <td>{{ Funciones::formatoPesos($liquidacion['totalAfp']) }}</td>
                        </tr>
                      @endif
                      @if ($liquidacion['seguroDesempleo'] && $liquidacion['totalSeguroCesantia']['total']>0)
                        <tr>
                          <td>Seguro Cesantía</td>                                                
                          <td>{{ Funciones::formatoPesos($liquidacion['totalSeguroCesantia']['total']) }}</td>
                        </tr> 
                      @endif
                      @foreach($liquidacion['apvs'] as $apv)
                          @if(strtoupper($apv['regimen'])=='B')
                            <tr>
                              <td>APV régimen B ({{ $apv['afp']['nombre'] }})</td>
                              <td>{{ Funciones::formatoPesos($apv['montoPesos']) }}</td>
                            </tr>
                          @endif
                      @endforeach
                      @if ($liquidacion['impuestoDeterminado']>0)
                        <tr>
                          <td>Impuesto Único</td>
                          <td>{{ Funciones::formatoPesos($liquidacion['impuestoDeterminado']) }}</td>
                        </tr> 
                      @endif
                      @if(count($liquidacion['descuentos'])>0 || count($liquidacion['apvs'])>0)
                        <tr>
                          <td><b>Otros Descuentos:</b></td>
                          <td></td>
                        </tr>
                        @foreach($liquidacion['descuentos'] as $descuento)
                          <tr>
                            <td style="padding-left: 10px;">{{ $descuento['tipo']['nombre'] }}</td>
                            <td>{{ Funciones::formatoPesos($descuento['montoPesos']) }}</td>
                          </tr>
                        @endforeach
                        @foreach($liquidacion['apvs'] as $apv)
                            @if(strtoupper($apv['regimen'])!='B')
                              <tr>
                                <td style="padding-left: 10px;">APV régimen A ({{ $apv['afp']['nombre'] }})</td>
                                <td>{{ Funciones::formatoPesos($apv['montoPesos']) }}</td>
                              </tr>
                            @endif
                        @endforeach
                      @endif
                    </tbody>
                  </table>
                </td>
                <div class="col">

                </div>
              </tr>
            </tbody>
          </table>
         <!--
          <div class="fila"></div>
          <div class="fila"></div>
          <div class="fila"></div>-->
        </div>

        <div class="totalesContenedor">
          <table class="totalesContenido">
            <tbody>
              <tr class="totales">
                <td>Total Haberes :</td>
                <td>{{ Funciones::formatoPesos($liquidacion['totalHaberes']) }}</td>
                <td>Total Descuentos :</td>
                <td>{{ Funciones::formatoPesos($liquidacion['totalDescuentos']) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="liquidoContenedor">
          <table class="liquidoContenido">
            <tbody>
              <tr class="liquido">
                <td style="border: 0;"></td>
                <td style="border: 0;"></td>
                <td style="border: 1px solid black;">Alcance Líquido : </td>
                <td style="border: 1px solid black;">{{ Funciones::formatoPesos($liquidacion['sueldoLiquido']) }}</td>
              </tr>

            </tbody>
          </table>
        </div>

        <div class="pie">
          Son: <b>{{ $liquidacion['sueldoLiquidoPalabras'] }}</b>
        </div>

      </div>
      <div class="contenedor2">

        <div>
          <table class="encabezado">
            <tbody>
              <tr>
                <td><b>{{ $liquidacion['empresa']['razon_social'] }}</b></td>
                <td>RUT : <b>{{ $liquidacion['rutEmpresa'] }}</b></td>
              </tr>
              <tr>
                <td>Liquidación de Sueldo del mes de {{ $liquidacion['mes'] }}</td>
                <td></td>
              </tr>
              <tr>
                <td>Trabajador : <b>{{ $liquidacion['nombreCompleto'] }}</b></td>
                <td>RUT : <b>{{ $liquidacion['rutFormato'] }}</b></td>
              </tr>
            </tbody>
          </table>

          <table class="final">
            <tbody>
              <tr class="totales">
                <td>Total Haberes Imponibles :</td>
                <td>{{ Funciones::formatoPesos($liquidacion['imponibles']) }}</td>
                <td>Total Descuentos Legales :</td>
                <td>{{ Funciones::formatoPesos($liquidacion['totalDescuentosPrevisionales'] + $liquidacion['impuestoDeterminado']) }}</td>
              </tr>
              <tr class="totales">
                <td>Total Haberes No Imponibles :</td>
                <td>{{ Funciones::formatoPesos($liquidacion['noImponibles']) }}</td>
                <td>Total Otros Descuentos :</td>
                <td>{{ Funciones::formatoPesos($liquidacion['totalOtrosDescuentos']) }}</td>
              </tr> 
              <tr class="totales">
                <td>Total Haberes :</td>
                <td>{{ Funciones::formatoPesos($liquidacion['totalHaberes']) }}</td>
                <td>Total Descuentos :</td>
                <td>{{ Funciones::formatoPesos($liquidacion['totalDescuentos']) }}</td>
              </tr> 

              <tr class="liquido">
                <td style="border: 0;"></td>
                <td style="border: 0;"></td>
                  <td style="padding: 10px;"><b>Alcance Líquido :</b></td>
                <td style="padding: 10px;"><b>{{ Funciones::formatoPesos($liquidacion['sueldoLiquido']) }}</b></td>
              </tr>

            </tbody>
          </table>
        </div>

        <div class="conforme">
          @if($liquidacion['cuenta'] && $liquidacion['banco'])            
            Recibí conforme el alcance líquido de la presente liquidación en mi cuenta {{ $liquidacion['cuenta'] }} del banco {{ $liquidacion['banco'] }}, no teniendo cargo o cobre alguno que hacer por ningún concepto.
          @else
            Recibí conforme el alcance líquido de la presente liquidación, no teniendo cargo o cobro alguno que hacer por ningún concepto.
          @endif
        </div>

        <div class="firma">
          <table class="tablaFirma">
            <tbody>
              <tr>
                <td></td>
                <td></td>
                <td style="width: 40%; border-top: 1px solid black;">
                  {{ $liquidacion['nombreCompleto'] }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>

    </div>

  </body>
</html>
<!--

    Código Asociado Mutual
    Código Asociado Caja
    SobreGiro mes Anterior (descuento sindicato)
    Préstamo al trabajador (para pagar sobregiro)

    Descuentos:
    descuento cuenta de ahorro AFP
    descuento APVC

    Al momento de ingresar licencia, ¿Quién paga?
    RUTs    
    Compin Fonasa

    Tipos de Licencia
    -1,2,3,4,7 A:Compin, B:Isapre, C:CCAF, D: Empleador
    -5,6 E: Compin, F: Mutual, G: ISL, H: Empleador

    Mantenedor Tipos de Licencia

    Gratificación Anual
    Semana Corrida

-->
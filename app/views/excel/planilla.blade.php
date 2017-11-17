<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Planilla Costo Empresa</title>
</head>
<body>
    
    <table>
        <thead>
            <tr>
                <th>RUT</th>        
                <th>Nombre</th>                
                <th>Cargo</th>                
                <th>Sueldo Base</th>                
                <th>Total Imponibles</th>                
                <th>Total No Imponibles</th>                
                <th>Total Aportes</th>                
            </tr>
        </thead>
        <tbody>
            @foreach($planilla as $dato)
                <tr>
                    <td>{{ $dato->trabajador_rut }}</td>
                    <td>{{ $dato->trabajador_nombres }} {{ $dato->trabajador_apellidos }}</td>
                    <td>{{ $dato->trabajador_cargo }}</td>
                    <td>{{ $dato->sueldo_base }}</td>
                    <td>{{ $dato->imponibles }}</td>
                    <td>{{ $dato->no_imponibles }}</td>
                    <td>{{ $dato->total_aportes }}</td>
                </tr>
            @endforeach
        </tbody>        
    </table>
    
</body>
</html>
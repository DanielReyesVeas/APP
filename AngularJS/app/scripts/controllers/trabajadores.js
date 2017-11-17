'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:TrabajadoresCtrl
 * @description
 * # TrabajadoresCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('TrabajadoresCtrl', function ($scope, $uibModal, $filter, $anchorScroll, trabajador, $rootScope, Notification, plantillaContrato, fecha) {
    $anchorScroll();
    $scope.datos = [];
    $scope.cargado = false;

    /*$scope.script = function(){
      var string = "";
      var miFecha = new Date('2015-01-01T09:30:00');
      var cant;
      var sum;
      for(var i=1; i<=926; i++){
        sum = 0;
        if(miFecha.getFullYear()==2017){
          sum = 200;
        }
        string += 'insert into dbo.ft_banner values';
        miFecha = fecha.convertirFechaFormato(miFecha);        
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(1, 1, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(2, 1, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(3, 1, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(1, 2, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(2, 2, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(3, 2, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(1, 3, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(2, 3, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(3, 3, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(1, 4, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(2, 4, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(3, 4, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(1, 5, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(2, 5, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(3, 5, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(1, 6, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(2, 6, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(3, 6, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(1, 7, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(2, 7, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(3, 7, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(1, 8, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(2, 8, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(3, 8, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(1, 9, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(2, 9, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(3, 9, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(1, 10, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(2, 10, " + cant + ", '" + miFecha + "'),";
        cant = Math.floor((Math.random() * 200) + 1) + sum;
        string += "\n(3, 10, " + cant + ", '" + miFecha + "');\n";

        miFecha = fecha.convertirFecha(miFecha);
        miFecha.setSeconds(1*86400);
      }


      console.log(string)
    }*/
    /*
    $scope.script = function(){
      var nombresHombres = ['Ramón','Francisco','Joaquín','Raúl','Augusto','Pedro','Pablo','Oscar','Arturo','Raúl','Esteban','Humberto','Benjamín','Marcelo','Álvaro','Simón','Patricio','Joel','Alberto','Oscar','Felipe','Augusto','John','Gastón','Alexis','Jaime','Oscar','Daniel','Reimundo','Patricio','Francisco','Rodrigo','Esteban','Esteban','Marcelo','Francisco','Oscar','Rigoberto','Antonio','Alonso','Juan','Mateo','Simón','Pablo','Martín','Richard','Alonso','Augusto','Marcelo','Gonzalo','Gonzalo','Bernardo','Ignacio','Tomás','Alonso','Tomás','Matías','Ignacio','Marcelo','Oscar','Nicolás','Antonio','Julio','Andrés','Hector','Francisco','Lucas','Roberto','Federico','Luciano','Mauricio','Francisco','José','Francisco','Javier','Jorge','Antonio','Andrés','Daniel','Pablo','Pedro','Pedro','Antonio','Esteban','Alonso','Humberto','Pablo', 'Antonio','Javier','Francisco','Martín','Alonso','Jonathan','Marco','Alonso','Roberto','Jaime','Andrés'];
      var nombresMujeres = ['María','Marcela','Antonia','Jacinta','Marta','Tatiana','Angélica','Angela','Joaquín','Javier','Juan','Pablo','Javiera','Francisca','María','de los Angeles','María','Denisse','Victor','Reimundo','Antonia','Mirta','Angélica','Mercedez','Graciela','Angela','Pamela','Javiera','María','Marta','Angélica','Fernanda','Josefina','Jesus','Dominique','Francisca','Agustina','Tamara'];
      var apellidos = ['Tapia','Garate', 'Gutierrez','Villagrán', 'Gonzáles','Rebolledo', 'Méndez','Cruz', 'Barahona','Jiménez', 'Paredes','Rincón', 'Tapia','Barahona', 'Reyes','Pizarro', 'Pizarro','Lorca', 'Uribe','Gatica', 'Rincón','Araneda', 'Jiménez','Alvarez', 'Román','Reyes', 'Lorca','Tango', 'Gutierrez','Ojeda', 'Brito','Lorca', 'Orrego','Martinez', 'Gonzáles','Perez', 'Brito','Perez', 'Poblete','Ahumada', 'Concha','Román', 'Coderch','Román', 'Trinidad','Martinez', 'Huidobro','Mendoza', 'Barahona','Meza', 'Hurtado','del Río', 'Jiménez','Gatica', 'Osses','Gutierrez', 'Miranda','Tapia', 'Alomar','Jiménez', 'Hurtado','Meléndez', 'Brito','Suárez', 'Contreras','Concha', 'Caballero','Gutierrez', 'Caroca','Lombardo', 'Provoste','Snack', 'Rebolledo','Miranda', 'Valdivia','Espejo', 'Díaz','Reyes', 'Cariola','Osses', 'Nuñez','Miranda', 'Zamudio','Reyes', 'Gonzáles','Ortíz', 'Suazo','Gutierrez', 'Cavieres','Lorca', 'Reyes','Gonzáles', 'Jaramillo','Espejo', 'Tapia','Ortíz', 'Brito','Barahona', 'Dominguez','Gutierrez', 'García','Jiménez', 'Silva','Ortíz', 'Gutierrez','Román', 'Martinez','Jiménez', 'Rocca','Osses', 'Miranda','Solar', 'Ortíz','Zamorano', 'Brito','Zamorano', 'Zambrano','Ojeda', 'Valdivia','Osorio', 'Caballero','Brito', 'Laguna','Uribe', 'Santis', 'Perez', 'Jiménez','del Río', 'Rios','Huerta', 'Ahumada','Gonzáles', 'Allende','Solar', 'Hirsh','Brito','Barahona','Lorca'];
      var string = "";
      var miFecha = new Date('2015-01-01T09:30:00');
      var nombre1;
      var nombre2;
      var apellido1;
      var apellido2;
      var cant;
      var rut;
      var rut1;
      var rut21;
      var rut22;
      var rut23;
      var rut31;
      var rut32;
      var rut33;
      var rut4;
      var dia;
      var mes;
      var anio;
      var fechaNacimiento;
      var fechaIngreso;
      var comuna;
      var perfil;
      var ocupacion;
      var index;
      string += 'insert into dbo.usuario values\n';

      for(var i=1; i<=8000; i++){

        if(i%6==0){
          index = Math.floor((Math.random() * (nombresMujeres.length-1)));
          nombre1 = nombresMujeres[index];
          index = Math.floor((Math.random() * (nombresMujeres.length-1)));
          nombre2 = nombresMujeres[index];
        }else{
          index = Math.floor((Math.random() * (nombresHombres.length-1)));          
          nombre1 = nombresHombres[index];
          index = Math.floor((Math.random() * (nombresHombres.length-1)));          
          nombre2 = nombresHombres[index];
        }        
        index = Math.floor((Math.random() * (apellidos.length-1)));
        apellido1 = apellidos[index];
        index = Math.floor((Math.random() * (apellidos.length-1)));
        apellido2 = apellidos[index];
        miFecha = fecha.convertirFechaFormato(miFecha);
        rut1 = Math.floor((Math.random() * 20) + 1) + 5;
        rut21 = Math.floor((Math.random() * 9));
        rut22 = Math.floor((Math.random() * 9));
        rut23 = Math.floor((Math.random() * 9));
        rut31 = Math.floor((Math.random() * 9));
        rut32 = Math.floor((Math.random() * 9));
        rut33 = Math.floor((Math.random() * 9));
        if(i%10==0){
          rut4 = 'k';
        }else{
          rut4 = Math.floor((Math.random() * 9));          
        }

        dia = Math.floor((Math.random() * 27)) + 1;
        if(dia<10){
          dia = '0' + dia;
        }
        mes = Math.floor((Math.random() * 11)) + 1;
        if(mes<10){
          mes = '0' + mes;
        }
        anio = Math.floor((Math.random() * 35)) + 1960;

        rut = rut1 + '.' + rut21 + rut22 + rut23 + '.' + rut31 + rut32 + rut33 + '-' + rut4;
        fechaNacimiento = anio + '-' + mes + '-' + dia;

        dia = Math.floor((Math.random() * 27)) + 1;
        if(dia<10){
          dia = '0' + dia;
        }
        mes = Math.floor((Math.random() * 11)) + 1;
        if(mes<10){
          mes = '0' + mes;
        }
        if(i%2==0){          
          anio = 2017;
        }else{
          if(i%3==0){
            anio = 2015;
          }else{
            anio = 2016;
          }
        }

        comuna = Math.floor((Math.random() * 357)) + 45;
        perfil = Math.floor((Math.random() * 2)) + 1;
        ocupacion = Math.floor((Math.random() * 2)) + 1;

        rut = rut1 + '.' + rut21 + rut22 + rut23 + '.' + rut31 + rut32 + rut33 + '-' + rut4;
        fechaIngreso = anio + '-' + mes + '-' + dia;
        string += "(" + i + ", '" + nombre1 + " " + nombre2 + "', '" + apellido1 + " " + apellido2 + "', "  + comuna + ", '" + fechaNacimiento + "', '" + rut + "', " + perfil + ", '" + fechaIngreso + "', " + ocupacion + ")";

        if((i+1)%500==0){
          string += ";\ninsert into dbo.usuario values\n";
        }else if(i==8000){
          string += ";";
        }else{
          string += ",\n";          
        }

      }


      console.log(string)
    }*/

    /*$scope.script = function(){
      var titulos = [ 'Sename: las terribles cifras que demuestran que nada ha cambiado', 'Política de Fundiciones: la urgencia de avanzar en la industrialización del cobre', 'Sistema de pensiones: radiografía a las preferencias ciudadanas más allá de lo técnico', 'Serie sobre clase media: Entre la meritocracia y el pituto', 'El proyecto de las 40 horas y los efectos de reducir la jornada laboral en Chile', 'Cómo aumentar en 20% las pensiones de los jubilados en el corto plazo', 'Los factores que evitan que la Reforma Tributaria afecte la inversión y el endeudamiento', 'Cómo captar la riqueza regalada del cobre', '15 candidatos para el 40%: la incapacidad para convocar a la mayoría', 'Haciendo política a punta de eslóganes y sin poder dejar contento a nadie', 'Carabineros: Las múltiples irregularidades que obligan a depurar el alto mando', 'Los secretos de la millonaria campaña de Pablo Longueira en las primarias de 2013', 'El verdadero valor de la fortuna que acumuló Pinochet', 'Beatriz Sánchez publica su declaración de impuestos y se suma a Goic, Ossandón y Guillier', 'Corte Suprema acoge reclamo de SQM y declara reserva sobre sus exportaciones de litio', 'SII deberá entregar información sobre el origen de US$18,7 mil millones refugiados en el exterior', 'General Echeverría sepultó en 2011 pista clave que llevaba al mega-fraude en Carabineros', 'El exitoso lobby que tumbó artículos clave de la Reforma al Código de Aguas', 'Corte de Apelaciones ratifica que sanciones administrativas son públicas aunque sean antiguas', 'Elusión tributaria: es legal pero ilegítima' ];
      var subtitulos = [ 'CAÍDA DE 400% EN FONDOS PARA CAPACITACIÓN Y FALTA DE CONTROL SOBRE $146 MIL MILLONES', 'OPINIÓN', 'ESTUDIO DE ESPACIO PÚBLICO', 'OPINIÓN', 'OPINIÓN', 'OPINIÓN', 'OPINIÓN', 'OPINIÓN', 'OPINIÓN', 'SERIE: LA CRISIS DEL SISTEMA POLÍTICO', 'LOS GENERALES QUE FALLARON EN LOS CONTROLES INTERNOS','SERVEL RECONOCE FALTA DE RECURSOS PARA FISCALIZAR A FONDO CUENTAS', 'A 10 AÑOS DE SU MUERTE, CIPER TASÓ SUS PROPIEDADES', 'ASPIRANTES A LA MONEDA SE UNEN A PARLAMENTARIOS QUE TRANSPARENTARON SUS RENTAS','FALLO PONE EN DUDA ACCESO A INFORMACIÓN DE EMPRESAS REGULADAS POR EL ESTADO', 'CORTE DE APELACIONES DE SANTIAGO ACOGIÓ PETICIÓN DE CIPER', 'INFORME DEL GENERAL DE FINANZAS ARCHIVÓ LA ALERTA DE LA UAF', 'LA OFENSIVA DE GREMIOS, ABOGADOS Y EX DIRECTIVOS DE LA DGA', 'SVS INTENTA MANTENER EN SECRETO INFORMACIÓN SOBRE INFRACCIONES CUYA PENA ESTÁ CUMPLIDA', 'OPINIÓN' ];
      var cuerpos = [ 'A más de un año de la muerte de Lissette Villa, la segunda comisión de la Cámara que investigó al Sename aprobó su informe. Aún no se publica, pero CIPER accedió al último borrador. Allí figuran dos preocupantes datos: una caída de casi 400% en los recursos para capacitación y falta de control sobre $146 mil millones destinados a programas privados de cuidado de niños. Las cuentas de 338 de estos proyectos no se revisaron en los últimos tres años. CIPER contrastó estas cifras con funcionarios del Sename. Su conclusión es lapidaria: nada ha cambiado desde la muerte de la pequeña Lissette.', 'Los envíos de cobre al extranjero representan casi el 60% de las exportaciones totales de Chile. Para el economista Felipe Correa, el reciente anuncio de la Presidenta Michelle Bachelet de establecer una moderna fundición en Atacama para incorporar mayor valor a las exportaciones de ese mineral, va en la dirección correcta, pero es insuficiente. Para Correa, hoy existe la posibilidad real de que se refine en Chile el 100% del cobre que explotan Codelco y la minería privada, potenciando el empleo, la innovación, el desarrollo de conocimiento y tecnologías, y cumpliendo, de paso, con los compromisos ambientales adquiridos por nuestro país en el Acuerdo de París.', 'Esta columna sintetiza el análisis hecho por Espacio Público de los distintos instrumentos que han capturado la visión de la ciudadanía sobre el sistema de pensiones chileno, un insumo que debiera ser relevante en momentos en que se prepara una reforma. Se concluye que el rechazo a las AFP se debe sobre todo al monto de las pensiones que entregan, lo que se traduce en un serio problema de desconfianza en el sistema. Se demanda también una mayor presencia del Estado, no sólo como financista, sino también como garante de la seguridad social y fiscalizador. Hay, además, una tendencia a preferir un sistema con mayor solidaridad en su diseño.', 'En el discurso las clases medias son meritocráticas. Y se indignan mucho con los intercambios de favores que hace la elite y los arreglines de los políticos. Pero lo cierto es que recurren intensivamente al pituto para navegar tanto en el aparato público como en el mundo privado y apuntalar sus bajos sueldos. No es algo de ahora, remarca E. Barozet. Cuando hubo un Estado en expansión (entre los años 20 y 60) la clase media lo administró para sus propias redes. Así, tal como Chile nunca ha sido un país de clase media, Barozet sugiere que tampoco ha sido un país de derechos, sino de favores. ¿Quien se ve perjudicado? Los que no tienen pitutos, ni buena formación académica: las clases populares.', 'La propuesta de la diputada Camila Vallejo (PC) de reducir la jornada laboral de 45 a 40 horas ha causado bastante rechazo entre algunos expertos que participan en el debate público, incluyendo al ministro de Hacienda. En esta columna, dos economistas de la Universidad de Chile analizan técnicamente la propuesta desde los supuestos de la teoría económica moderna, considerando factores como productividad, desempleo y salarios. El efecto neto de esta medida, concluyen Ramón López y Javiera Petersen, sería positivo para el país.', 'El profesor de la Facultad de Economía de la Universidad de Chile Ramón López propone una nueva fórmula para mejorar las pensiones que actualmente entregan las AFP. Manteniendo los fondos individuales actuales pero entregando su administración a organizaciones sin fines de lucro, las cuantiosas utilidades que tienen las AFP podrían ser repartidas entre los jubilados. Según sus cálculos, esto aumentaría en un 20% las pensiones.', 'El economista Ramón López analiza los efectos de la Reforma Tributaria en los impuestos pagados por pequeños, medianos y grandes empresarios, a la luz de la teoría económica de que ciertas variaciones de impuestos son neutras. Concluye que, a diferencia de lo que han anunciado algunos de sus colegas, los cambios propuestos no reducirán la inversión ni harán que las empresas deban endeudarse más para financiar sus proyectos.', 'La gran minería privada del cobre ha obtenido en la última década una rentabilidad del 85% sobre el capital invertido, cifra que según los economistas López y Sturla ningún negocio lícito consigue (los bancos más importantes han obtenido una rentabilidad del 24%; las AFP un 26%). El exceso de ganancia, dicen, se debe a que el sistema político les permite a las mineras “ganar mucho más que lo que corresponde”. En esta segunda columna, proponen dos reformas que aumentan considerablemente los recursos que recibe el Estado, manteniendo niveles razonables de rentabilidad (sobre el 20%) para las mineras.', 'La sobreoferta de candidatos presidenciales no mejora la capacidad del sistema político de convocar ni dar respuesta a los problemas del 60% que no vota. En el mundo popular, la política se repliega, muestran los politólogos Luna y Toro. En sectores del sur de Santiago el vacío es llenado por iglesias evangélicas y bandas criminales que proveen mayores ingresos que la economía formal. En las zonas rurales se vota más: porque los partidos siguen siendo relevantes para conseguir favores. Los datos expuestos aquí son una bofetada para los medios y partidos cuya discusión se reduce a las encuestas de los pocos que votan. En el silencio de los que están afuera se fragua un futuro muy incierto.', 'Para el politólogo Juan Pablo Luna el objetivo de “educación universitaria gratuita y de calidad” se ha convertido en un slogan que puede no implicar nada bueno, como lo muestra el sistema universitario uruguayo: gratuito y sin lucro, pero malo académicamente y no inclusivo. Los eslóganes, como los prejuicios, solo iluminan una parte de la realidad. El sistema político chileno, afectado por una crisis de representatividad, intenta transformar ése y otros eslóganes en políticas para conectarse con los movimientos sociales. Pero no deja contento a nadie, porque los eslóganes ni se pueden materializar íntegramente en democracia ni resuelven los problemas, siempre más complejos y matizados.', 'Las réplicas del terremoto que sacudió a Carabineros el 6 de marzo con el estallido del mega fraude, aún no paran. La más fuerte sigue un cauce soterrado: el filtro que se instaló en La Moneda para buscar al sucesor del general Villalobos y seleccionar al nuevo alto mando con sello depurador. La tarea no es fácil. Armar este puzzle implica asumir que si un grupo logró robar $22.500 millones fue porque todos los controles internos fallaron y se instaló una cultura de tolerancia y complicidad. Al menos un 30% del cuerpo de generales ha ejercido mando en unidades donde se detectaron serias irregularidades.', 'Nueve semanas duró la aventura de Pablo Longueira en la primaria de 2013, en la que derrotó a Andrés Allamand. Pese a la corta carrera el líder de la UDI gastó más de $1.000 millones, pero declaró $885,8 millones. CIPER accedió a la cuenta corriente oficial de su campaña y la cotejó con lo declarado ante el Servel. Las cifras no calzan: sobrepasó los límites en gastos y “aportes anónimos”. En ese ítem declaró $71,5 millones, pero su cuenta registra $342 millones de ingresos no identificados y otros $23,5 millones de cinco empresarios. A ello se suman $709,8 millones de “aportes reservados”.', 'Durante sus 17 años como dictador Pinochet acumuló un patrimonio que la justicia avaluó en US$21,3 millones. Pero parte de su fortuna la invirtió en propiedades que sólo han aumentado su valor y que según expertos consultados por CIPER hoy costarían unos US$ 28 millones. A eso hay que agregar US$3,1 millones que la familia de Pinochet obtuvo de ventas inmobiliarias, US$5 millones en depósitos que están embargados y US$5,4 millones cuyo destino es desconocido. El futuro de gran parte del patrimonio de Pinochet depende del desenlace del Caso Riggs, que podría entregarlo al Fisco o a sus herederos.', 'La precandidata del Frente Amplio, Beatriz Sánchez, aceptó publicar su declaración de impuestos. Así, la campaña de CIPER para transparentar las rentas de los parlamentarios, se amplía a los candidatos a La Moneda. La iniciativa ya ha tenido una respuesta positiva de 43 congresistas, entre los cuales se encuentran tres presidenciables: Carolina Goic, Manuel José Ossandón y Alejandro Guillier. Viendo la respuesta positiva de estos últimos, y aunque no es parlamentaria, Beatriz Sánchez también transparentó sus rentas. Una nueva congresista también se sumó: la diputada Loreto Carvajal (PPD).', 'El máximo tribunal acaba de acoger un recurso de queja presentado por SQM Salar que buscaba impedir que se hiciera pública información comercial (clientes y precios de venta de litio) que CIPER había solicitado. Por tratarse de una empresa regulada por el Estado y que explota pertenencias mineras propiedad del Fisco, tanto el Consejo para la Transparencia como la Corte de Apelaciones habían dictaminado que se trataba de documentos de caracter público. El nuevo fallo establece que se trata de información reservada.', 'El Servicio de Impuestos Internos ha logrado dilatar durante un año la entrega de información sobre la amnistía tributaria de 2015 que le permitió a contribuyentes chilenos regularizar US$18,7 mil millones que estaban en el extranjero y no habían sido declarados. Luego de que el SII negara los datos a CIPER invocando el secreto tributario, primero el Consejo para la Transparencia y ahora la Corte de Apelaciones de Santiago determinaron que la información sobre el país donde se alberga ese dinero, los montos y su origen es información pública.', 'Una pista que llevaba directo al corazón del mega-fraude en Carabineros fue detectada en 2011 por la Unidad de Análisis Financiero (UAF). Los depósitos que recibió el coronel Arnoldo Riveros fueron investigados por la Dipolcar y el Ministerio Público. Pero la alerta se archivó por un informe del general Flavio Echeverría, quien exculpó a Riveros y lavó las huellas del delito. Lo más grave es que en su informe se afirma que la Inspectoría General de la policía hizo rigurosa auditoría y no detectó irregularidades. Hoy Echeverría está preso y dice que Riveros lo involucró en el fraude.', 'En medio de la peor crisis hídrica del país y tras seis años en el congelador del Congreso, la Cámara de Diputados aprobó en noviembre pasado por amplia mayoría una potente reforma al Código de Aguas, cuyas modificaciones aseguraban un acceso más equitativo al recurso para hoy y mañana. Eso implicaba que el Estado entrara a regular derechos ya otorgados. Los gremios del agro, la minería y la generación eléctrica se organizaron y dieron una batalla sorda pero efectiva. CIPER siguió la ruta del intenso lobby desplegado. En abril, el Ejecutivo envió 27 indicaciones que echan por tierra avances clave.', 'Gatillado por una solicitud de CIPER, el fallo del tribunal de alzada ratifica un dictamen del Consejo para la Transparencia, clave para el acceso a la información pública. Echa por tierra la interpretación de la SVS de que todas las sanciones y multas ya pagadas son información privada de quienes cometieron las infracciones, lo que en la práctica significaba que ni la ciudadanía ni el mercado podían acceder a los registros de quienes habían violado las normas.', '¿Es un problema que Sebastián Piñera tenga sociedades y patrimonio en paraísos tributarios? El economista J. Fábrega aborda el tema desde la disyuntiva entre lo legal y lo legítimo, que también está en el corazón del escándalo del financiamiento ilegal de la política. Contra la idea de que el único límite es la letra de la ley, Fábrega argumenta a favor del espíritu. Lo hace desde la economía: “El respeto de las exigencias mutuas son las formas más eficientes en que los humanos reducimos costos de transacción”. Y llama al público a pifiar con ganas a los pillos, sean del equipo propio o del ajeno, pues en ello se nos va la posibilidad de hacer una sociedad vivible.' ];


      var string = "";
      var minutos;
      var titulo;
      var subtitulo;
      var cuerpo;
      var dia;
      var mes;
      var anio;
      var fecha;
      var usuario;
      var categoria;
      var tipo;
      var estado;
      var index;
      string += 'insert into dbo.publicacion values\n';
      for(var i=1; i<=10000; i++){

        index = Math.floor((Math.random() * (titulos.length-1)));
        titulo = titulos[index];
        subtitulo = subtitulos[index];
        cuerpo = cuerpos[index];

        dia = Math.floor((Math.random() * 27)) + 1;
        if(dia<10){
          dia = '0' + dia;
        }
        mes = Math.floor((Math.random() * 11)) + 1;
        if(mes<10){
          mes = '0' + mes;
        }
        if(i%2==0){          
          anio = 2017;
        }else{
          if(i%3==0){
            anio = 2015;
          }else{
            anio = 2016;
          }
        }

        usuario = Math.floor((Math.random() * 20)) + 1;
        categoria = Math.floor((Math.random() * 13)) + 1;
        tipo = Math.floor((Math.random() * 3)) + 1;
        estado = Math.floor((Math.random() * 2)) + 1;
        if(anio===2017){
          minutos = Math.floor((Math.random() * 19)) + 1;
        }else{          
          minutos = Math.floor((Math.random() * 59)) + 1;
        }

        fecha = anio + '-' + mes + '-' + dia;
        string += "(" + i + ", '" + titulo + "', '" + subtitulo + "',' " + cuerpo + "', "  + usuario + ", "  +  categoria + ", "  +  tipo + ", '" + fecha + "', " + estado + ", " + minutos + ")";
        if((i+1)%500==0){
          string += ";\ninsert into dbo.publicacion values\n";
        }else if(i==10000){
          string += ";";
        }else{
          string += ",\n";          
        }

      }
      console.log(string)
    }*/

    $scope.script = function(){
      var string = "";
      var usuario;
      string += 'insert into dbo.usuario_publicacion values\n';
      for(var i=1; i<=10000; i++){

        usuario = Math.floor((Math.random() * 20)) + 1;
        string += "(" + i + ", "  + usuario + ")";
        if((i+1)%500==0){
          string += ";\ninsert into dbo.usuario_publicacion values\n";
        }else if(i==10000){
          string += ";";
        }else{
          string += ",\n";          
        }

      }
    }

    function cargarDatos(){
      $rootScope.cargando = true;
      $scope.cargado = false;
      var datos = trabajador.datos().get();
      datos.$promise.then(function(response){
        $scope.datos = response.datos;
        $scope.accesos = response.accesos;
        $rootScope.cargando = false;
        $scope.cargado = true;
      });
    };

    cargarDatos();  

    $scope.importarPlanilla = function () {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-planilla-trabajadores.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormPlanillaTrabajadoresCtrl',
        size: 'lg'
      });
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        cargarDatos();
      }, function () {
        javascript:void(0)
      });
    }  

    $scope.noOcultar = function(){
      var icon = angular.element(document.querySelector('#botonNoOcultar'));
      icon.addClass("active");
    }

    $scope.plantillasContratos = function(){
      $rootScope.cargando=true;
      var datos = plantillaContrato.datos().get();
      datos.$promise.then(function(response){
        openPlantillasContratos(response.datos);
        $rootScope.cargando=false;
      });
    }

    $scope.editar = function(plan){
      $rootScope.cargando=true;
      var datos = plantillaContrato.datos().get({sid: plan.sid});
      datos.$promise.then(function(response){
        $scope.openPlantillaContrato(response.datos);
        $rootScope.cargando=false;
      });
    }

    function openPlantillasContratos(obj){
      var miModal = $uibModal.open({
        animation: true,
        backdrop: false,
        templateUrl: 'views/forms/form-tipos-plantillas-contrato.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormTiposPlantillasContratoCtrl',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
      }, function () {
        javascript:void(0)
      });
    }

    $scope.openPlantillaContrato = function(obj){
      var miModal = $uibModal.open({
        animation: true,
        backdrop: false,
        templateUrl: 'views/forms/form-plantilla-contrato.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormPlantillaContratoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
      }, function () {
        javascript:void(0)
      });
    }

    $scope.open = function(){
      $rootScope.cargando=true;
      var datos = trabajador.datos().get({sid: 0});
      datos.$promise.then(function(respuesta){        
        $rootScope.cargando=false;
        openForm(respuesta.formulario, null);
      })
    };

    function openForm(form, trab) {
      var miModal = $uibModal.open({
        animation: true,
        backdrop: false,
        templateUrl: 'views/forms/form-nuevo-trabajador.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormTrabajadorCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return trab;
          },
          formulario: function () {
            return form;
          }
        }
      });
      miModal.result.then(function (obj) {
        Notification.success({message: obj.mensaje, title: 'Mensaje del Sistema'});
        if(obj.contrato){
          cargarDatos();
          openConfirmacionContrato(obj.trabajador);
        }else{
          cargarDatos();
        }
      }, function () {
        javascript:void(0)
      });
    };

    function openConfirmacionContrato(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-confirmacion.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormConfirmacionContratoCtrl',
        size: 'sm',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });
      miModal.result.then(function (trab) {
        $scope.generarContrato(trab);            
      }, function () {
        cargarDatos();
      });
    };

    $scope.generarContrato = function(trab){
      $rootScope.cargando=true;
      var datos = plantillaContrato.datos().get();
      datos.$promise.then(function(response){
        clausulas( response.datos, trab );
        $rootScope.cargando=false;
      });
    }

    function clausulas(obj, trab){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-clausulas.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormClausulasCtrl',
        resolve: {
          objeto: function () {
            return obj;          
          },
          trab: function () {
            return trab;          
          }
        }
      });     
      miModal.result.then(function (datos) {
        openContrato(datos);
      }, function () {
        javascript:void(0)
      });
    };

    function openContrato(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-contrato.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormContratoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });     
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        cargarDatos();
      }, function () {
        javascript:void(0)
      });
    };

    $scope.openDetalleTrabajador = function(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-detalle-trabajador.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormDetallesTrabajadoresCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });
      miModal.result.then(function () {
      }, function () {
        javascript:void(0)
      });
    };    

    $scope.eliminar = function(objeto){
      $rootScope.cargando=true;
      $scope.result = trabajador.datos().delete({ sid: objeto.sid });
      $scope.result.$promise.then( function(response){
          if(response.success){
            Notification.success({message: response.mensaje, title:'Notificación del Sistema'});
            cargarDatos();
          }
      });
    };

    $scope.detalle = function(tra){
      $rootScope.cargando=true;
      var datos = trabajador.datos().get({sid:tra.sid});
      datos.$promise.then(function(response){
        $scope.openDetalleTrabajador( response.trabajador );
        $rootScope.cargando=false;
      });
    };

    $scope.editar = function(tra){
      $rootScope.cargando=true;
      var datos = trabajador.datos().get({sid:tra.sid});
      datos.$promise.then(function(response){
        openForm( response.formulario, response.trabajador );
        $rootScope.cargando=false;
      });
    };    

    $scope.contratos = function(tra){
      $rootScope.cargando=true;
      var datos = trabajador.contratos().get({sid:tra.sid});
      datos.$promise.then(function(response){
        openContratos( response );
        $rootScope.cargando=false;
      });
    }

    function openContratos(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-trabajador-contratos.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormTrabajadorContratosCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });     
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        cargarDatos();
      }, function () {
        javascript:void(0)
      });
    };

  })
  .controller('FormPlanillaTrabajadoresCtrl', function ($scope, fecha, $uibModal, $uibModalInstance, $http, $filter, constantes, $rootScope, Notification, Upload, trabajador) {
    
    $scope.trabajadores = {};
    $scope.error = {};
    $scope.datos=[];
    $scope.listaErrores=[];
    $scope.constantes = constantes;

    $scope.convertirFechaFormato = function(date){
      return fecha.convertirFechaFormato(date);
    }

    $scope.$watch('files', function() {
      $scope.upload($scope.files);
    });

    $scope.upload = function(files) {      
      if(files) {              
        $scope.error = {};
        $scope.datos=[];
        $scope.listaErrores=[];
        var file = files;
        Upload.upload({
          url: constantes.URL + 'trabajadores/planilla/importar',
          data: { file : file}
        }).progress(function (evt) {
          var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
          $scope.dynamic = progressPercentage;
        }).success(function (data){
          $scope.dynamic=0;
          if( data.success ){
              $scope.datos = data.datos;
              $scope.trabajadores = data.trabajadores;
          }else{
            if( data.errores ){
              $scope.listaErrores = data.errores;
              Notification.error({message: 'Errores en los datos del archivo', title: 'Mensaje del Sistema'});
            }else{
              Notification.error({message: data.mensaje, title: 'Mensaje del Sistema'});                            
            }
          }
        });                
      }
    };

    $scope.confirmarDatos = function(){
      $rootScope.cargando=true;
      var obj = $scope.trabajadores;
      var datos = trabajador.importar().post({}, obj);
      datos.$promise.then(function(response){
        if(response.success){
          $uibModalInstance.close(response.mensaje);
        }else{
          // error
          $scope.erroresDatos = response.errores;
          Notification.error({message: response.mensaje, title: 'Mensaje del Sistema'});
        }
        $rootScope.cargando = false;
      });
    }

  })
  .controller('FormTrabajadorContratosCtrl', function ($scope, $uibModalInstance, documento, constantes, objeto, trabajador, Notification, $uibModal, $filter, $rootScope, plantillaContrato) {
    $scope.trabajador = angular.copy(objeto.datos);
    $scope.accesos = angular.copy(objeto.accesos);
    $scope.constantes = angular.copy(constantes);

    function cargarDatos(){
      $rootScope.cargando=true;
      var datos = trabajador.contratos().get({sid:$scope.trabajador.sid});
      datos.$promise.then(function(response){
        $scope.trabajador = response.datos;
        $scope.accesos = response.accesos;
        $rootScope.cargando=false;
      });
    }

    function openAsociar(trab, tipos, doc){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nuevo-documento.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormDocumentosCtrl',
        resolve: {
          trabajador: function () {
            return trab;          
          },
          tiposDoc: function () {
            return tipos;          
          },
          doc: function () {
            return doc;          
          }
        }
      });
     miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        cargarDatos($scope.trabajador.sid);           
      }, function () {
        javascript:void(0)
      });
    };

    $scope.editar = function(doc){
      $rootScope.cargando=true;
      $scope.result = documento.datos().get({ sid: doc.sid });
      $scope.result.$promise.then( function(response){
        openAsociar($scope.trabajador, response.datos, response.documento);
        $rootScope.cargando = false; 
      });
    };

    $scope.eliminar = function(doc){
      $rootScope.cargando=true;
      $scope.result = documento.datos().delete({ sid: doc.sid });
      $scope.result.$promise.then( function(response){
        if(response.success){
          Notification.success({message: response.mensaje, title:'Notificación del Sistema'});
          cargarDatos($scope.trabajador.sid);
        }
      });
    };

    function clausulas(obj, trab){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-clausulas.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormClausulasCtrl',
        resolve: {
          objeto: function () {
            return obj;          
          },
          trab: function () {
            return trab;          
          }
        }
      });     
      miModal.result.then(function (datos) {
        openContrato(datos);
      }, function () {
        javascript:void(0)
      });
    };

    $scope.frame = function(obj){
      var url = $scope.constantes.URL + 'trabajadores/documento/obtener/' + obj.sid;
      window.open(url);
    }

    function openContrato(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-contrato.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormContratoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });     
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        cargarDatos();
      }, function () {
        javascript:void(0)
      });
    };

    $scope.generarContrato = function(trab){
      $rootScope.cargando=true;
      var datos = plantillaContrato.datos().get();
      datos.$promise.then(function(response){
        clausulas( response.datos, trab );
        $rootScope.cargando=false;
      });
    }

    $scope.importar = function(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-importar-contrato.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormImportarContratoCtrl',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });     
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        cargarDatos();
      }, function () {
        javascript:void(0)
      });
    }

  })
  .controller('FormPdfContratoCtrl', function ($scope, $uibModalInstance, objeto, $rootScope, Upload, constantes, url) {
    $scope.contrato = angular.copy(objeto);
    $scope.url = angular.copy(url);

  })
  .controller('FormImportarContratoCtrl', function ($scope, $uibModalInstance, objeto, $rootScope, Upload, constantes) {
    $scope.trabajador = angular.copy(objeto);
    $scope.constantes = angular.copy(constantes);
    $scope.contrato = {};

    $scope.$watch('contrato.file', function () {
      $scope.importar($scope.contrato.file);
    });    

    $scope.importar = function (files) {      
      if(files) {              
        $rootScope.cargando = true;
        $scope.error = {};
        $scope.listaErrores=[];
        var file = files;
        Upload.upload({
          url: constantes.URL + 'documentos/archivo/importar',
          data: { file : file }
        }).success(function (data){
          $scope.dynamic=0;
          if( data.success ){
            $scope.isOK = true;
            $scope.nombreArchivo = data.nombre;
          }else{
            /*if( data.errores ){
              $scope.listaErrores = data.errores;
              Notification.error({message: 'Errores en los datos del archivo', title: 'Mensaje del Sistema'});
            }else{
              Notification.error({message: data.mensaje, title: 'Mensaje del Sistema'});                            
            }*/
            console.log(data.mensaje);
          }
          $rootScope.cargando = false;
        });                
      }
    };

    $scope.subir = function (file) {      
      if(file) {              
        $rootScope.cargando = true;
        $scope.error = {};
        $scope.listaErrores=[];
        var file = file;
        Upload.upload({
          url: constantes.URL + 'documentos/archivo/subir',
          data: { file : file, idTrabajador : $scope.trabajador.id, idTipoDocumento : 1, descripcion : $scope.contrato.descripcion }
        }).success(function (data){
          $scope.dynamic=0;
          if( data.success ){
            $uibModalInstance.close(data.mensaje);
          }else{
            if( data.errores ){
              $scope.listaErrores = data.errores;
              Notification.error({message: 'Errores en los datos del archivo', title: 'Mensaje del Sistema'});
            }else{
              Notification.error({message: data.mensaje, title: 'Mensaje del Sistema'});                            
            }
            console.log(data.mensaje);
          }
          $rootScope.cargando = false;
        });                
      }
    };

  })
  .controller('FormDetallesTrabajadoresCtrl', function ($scope, $uibModalInstance, objeto) {
    $scope.trabajador = angular.copy(objeto);
    $scope.isCargas = false;

    function isCargas(){
      for(var i=0, len=$scope.trabajador.cargas.length; i<len; i++){
        if($scope.trabajador.cargas[i].esCarga){
          $scope.isCargas = true;
        }
      }
    }

    isCargas();
  })
  .controller('FormTiposPlantillasContratoCtrl', function ($scope, $uibModal, $uibModalInstance, objeto, $http, $filter, $rootScope, plantillaContrato, Notification) {
    $scope.datos = angular.copy(objeto);

    function cargarDatos(){
      $rootScope.cargando=true;
      var datos = plantillaContrato.datos().get();
      datos.$promise.then(function(response){
        $scope.datos = response.datos;
        $rootScope.cargando=false;
      });
    }

    $scope.openPlantillaContrato = function(obj){
      var miModal = $uibModal.open({
        animation: true,
        backdrop: false,
        templateUrl: 'views/forms/form-plantilla-contrato.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormPlantillaContratoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        cargarDatos();
      }, function () {
        javascript:void(0)
      });
    }

    $scope.editar = function(plan){
      $rootScope.cargando=true;
      var datos = plantillaContrato.datos().get({sid: plan.sid});
      datos.$promise.then(function(response){
        $scope.openPlantillaContrato(response.datos);
        $rootScope.cargando=false;
      });
    }

    $scope.eliminar = function(plan){
      $rootScope.cargando=true;
      $scope.result = plantillaContrato.datos().delete({ sid: plan });
      $scope.result.$promise.then( function(response){
        if(response.success){
          $rootScope.cargando=false;
          Notification.success({message: response.mensaje, title:'Notificación del Sistema'});
          cargarDatos();
        }
      })
    }

  })
  .controller('FormPlantillaContratoCtrl', function ($scope, $uibModalInstance, objeto, $http, $filter, $rootScope, plantillaContrato, Notification) {

    $scope.empresa = $rootScope.globals.currentUser.empresa;
    $scope.isAyuda = false;

    if(objeto){
      $scope.plantillaContrato = angular.copy(objeto);
      $scope.isEdit = true;
      $scope.titulo = 'Modificación Plantilla de Contrato de Trabajo';
      $scope.encabezado = $scope.plantillaContrato.nombre;
    }else{
      $scope.isEdit = false;
      $scope.titulo = 'Ingreso Plantillas de Contratos de Trabajo';
      $scope.encabezado = 'Nueva Plantilla de Contrato de Trabajo';
    }

    $scope.tinymceOptions = {
        resize: false,
        width: 800,  // I *think* its a number and not '400' string
        height: 300,
        plugins: 'textcolor',
        entity_encoding : "raw",
        statusbar : false,
        toolbar_items_size: 'small',
        menubar: false,
        toolbar: "undo redo | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify"
    };

    $scope.ayuda = function(){
      $scope.isAyuda = !$scope.isAyuda;
      $scope.tinymceOptions.height = 200;
      console.log($scope.empresa)
    }

    $scope.guardar = function(plan){
      $rootScope.cargando=true;
      var response;
      if( plan.sid ){
        response = plantillaContrato.datos().update({sid:plan.sid}, $scope.plantillaContrato);
      }else{
        response = plantillaContrato.datos().create({}, $scope.plantillaContrato);
      }
      response.$promise.then(
        function(response){
          if(response.success){
            $uibModalInstance.close(response.mensaje);
          }else{
            // error
            $scope.erroresDatos = response.errores;
            Notification.error({message: response.mensaje, title: 'Mensaje del Sistema'});
          }
          $rootScope.cargando=false;
        }
      );
    }

  })
  .controller('FormContratoCtrl', function ($scope, $uibModalInstance, objeto, $http, $filter, $rootScope, contrato, Notification) {

    $scope.trabajador = angular.copy(objeto.trabajador);
    $scope.contrato = angular.copy(objeto.datos);
    $scope.empresa = $rootScope.globals.currentUser.empresa;
    $scope.representante = angular.copy(objeto.representante);
    $scope.empresa.domicilio = angular.copy(objeto.empresa.domicilio);

    $scope.tinymceOptions = {
        resize: false,
        width: 800,  // I *think* its a number and not '400' string
        height: 300,
        plugins: 'textcolor',
        entity_encoding : "raw",
        statusbar : false,
        toolbar_items_size: 'small',
        menubar: false,
        toolbar: "undo redo | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify"
    };

    $scope.ingresar = function(){
      $rootScope.cargando=true;
      var cont = { idTipoContrato : $scope.trabajador.tipoContrato.id, idTrabajador : $scope.trabajador.id, idEncargado : $scope.trabajador.id, idEmpresa : $scope.empresa.id, razonSocialEmpresa : $scope.empresa.empresa, rutEmpresa : $scope.empresa.rut, domicilioEmpresa : $scope.empresa.domicilio, rutTrabajador : $scope.trabajador.rut, nombreCompletoTrabajador : $scope.trabajador.nombreCompleto, cargoTrabajador : $scope.trabajador.cargo.nombre, estadoCivilTrabajador : $scope.trabajador.estadoCivil.nombre, fechaNacimientoTrabajador : $scope.trabajador.fechaNacimiento, seccionTrabajador : $scope.trabajador.seccion.nombre, fechaIngresoTrabajador : $scope.trabajador.fechaIngreso, domicilioTrabajador : $scope.trabajador.domicilio,  fechaVencimiento : $scope.trabajador.fechaVencimiento, cuerpo : $scope.contrato.cuerpo, nombreCompletoRepresentanteEmpresa : $scope.representante.nombreCompleto, domicilioRepresentanteEmpresa : $scope.representante.domicilio, rutRepresentanteEmpresa : $scope.representante.rut };
      var response;
      response = contrato.datos().create({}, cont);
      response.$promise.then(
        function(response){
          if(response.success){
            $uibModalInstance.close(response.mensaje);
          }else{
            // error
            $scope.erroresDatos = response.errores;
            Notification.error({message: response.mensaje, title: 'Mensaje del Sistema'});
          }
          $rootScope.cargando=false;
        }
      )
    }

  })
  .controller('FormClausulasCtrl', function ($scope, $uibModalInstance, objeto, $http, $filter, $rootScope, trab, trabajador, clausulaContrato) {
    
    $scope.tiposContrato = angular.copy(objeto);
    $scope.trabajador = angular.copy(trab);
    $scope.cargado = false;

    $scope.objeto = [];
    $scope.isSelect = false;

    $scope.generar = function(){
      var clausulas = [];
      for(var i=0,len=$scope.datos.length; i<len; i++){
        if($scope.objeto[i].check){
          clausulas.push($scope.datos[i]);
        }
      }
      var obj = { sidTrabajador : $scope.trabajador.sid, clausulas : clausulas, sidPlantilla : $scope.objeto.tipoContrato.sid };
      $rootScope.cargando=true;
      var datos = trabajador.contrato().post({}, obj);
      datos.$promise.then(function(response){
        $rootScope.cargando=false;
        $uibModalInstance.close(response);    
      }); 
    }

    $scope.seleccionarPlantilla = function(){
      $scope.cargado=false;
      $rootScope.cargando = true;
      var datos = clausulaContrato.plantilla().get({sid: $scope.objeto.tipoContrato.sid});
      datos.$promise.then(function(response){
        $scope.datos = response.datos;
        $scope.cargado=true;
        $rootScope.cargando = false;
        crearModels();
      });
    }

    function crearModels(){
      for(var i=0, len=$scope.datos.length; i<len; i++){
        $scope.objeto.push({ check : true });
      }         
      $scope.objeto.todos = true;
      $scope.cargado = true;
    }    

    $scope.select = function(index){
      if(!$scope.objeto[index].check){
        if($scope.objeto.todos){
          $scope.objeto.todos = false; 
        }
        countSelected(index);
        $scope.isSelect = isThereSelected();       
      }else{
        $scope.isSelect = true;
        countSelected(index);
      }
    }

    function isThereSelected(){
      var bool = false;
      for(var i=0, len=$scope.datos.length; i<len; i++){
        if($scope.objeto[i].check){
          bool = true;
          return bool;
        }
      }
      return bool;
    }

    function countSelected(index){
      var count = 0;
      for(var i=0, len=$scope.datos.length; i<len; i++){
        if($scope.objeto[i].check){
          count++;
          $scope.mensaje = 'Se generará el Contrato de Trabajo de <b>' + $scope.trabajador.nombre + ' con las <b>' + count + '</b> cláusulas seleccionadas. ¿Desea continuar?';
        }
      }
      if(count===1){
        $scope.mensaje = 'Se generará el Contrato de Trabajo de <b>' + $scope.trabajador.nombre + ' con la cláusula seleccionada. ¿Desea continuar?';
      }
      return count;
    }

    $scope.selectAll = function(){
      if($scope.objeto.todos){
        var total = 0;
        for(var i=0, len=$scope.datos.length; i<len; i++){
          $scope.objeto[i].check = true
          $scope.isSelect = true;
          total++;  
        }
        countSelected();
      }else{
        for(var i=0, len=$scope.datos.length; i<len; i++){
          $scope.objeto[i].check = false
          $scope.isSelect = false;
        }
      }
    }

    function limpiarChecks(){
      for(var i=0, len=$scope.datos.length; i<len; i++){
        $scope.objeto[i].check = false
      }
      $scope.isSelect = false;
      $scope.objeto.todos = false;
    }

  })
  .controller('FormConfirmacionContratoCtrl', function ($scope, $uibModalInstance, objeto, $http, $filter, $rootScope) {

    $scope.trabajador = angular.copy(objeto);

    $scope.titulo = 'Contrato de Trabajo';
    $scope.mensaje = "¿Desea generar el contrato de trabajo de " + $scope.trabajador.nombreCompleto + "?";
    $scope.ok = 'Sí';
    $scope.isOK = true;
    $scope.isQuestion = true;
    $scope.cancel = 'Posponer';

    $scope.aceptar = function(){
      $uibModalInstance.close($scope.trabajador);      
    }

    $scope.cerrar = function(){
      $uibModalInstance.dismiss('cancel');
    }
    
  })
  .controller('FormSueldoBaseCtrl', function ($scope, $uibModalInstance, $http, $filter, $rootScope, objeto, moneda) {
    
    $scope.detalle = angular.copy(objeto);

    $scope.convertir = function(valor, mon){
      return moneda.convertir(valor, mon);
    }
    
    $scope.asignar = function(){
      $uibModalInstance.close($scope.detalle.sueldoBase);
    }

  })
  .controller('FormCalcularSueldoBaseCtrl', function ($scope, tablaImpuestoUnico, $uibModalInstance, $uibModal, $http, $filter, $rootScope, trabajador, afps, isapres, seguroCesantia, rentasTopesImponibles, rmi, moneda) {

    $scope.uf = $rootScope.globals.indicadores.uf.valor;
    $scope.utm = $rootScope.globals.indicadores.utm.valor;
    $scope.afps = angular.copy(afps);
    $scope.seguroCesantia = angular.copy(seguroCesantia);
    $scope.isapres = angular.copy(isapres);
    var rmi = angular.copy(rmi);
    $scope.trabajador = angular.copy(trabajador);
    $scope.isAsignacion = false;
    $scope.isEditAsignacion = false;
    $scope.asignaciones = [];
    var asigIndex;
    var tabla = angular.copy(tablaImpuestoUnico);
    var contador = 0;

    $scope.monedas = [
                { id : 1, nombre : '$' }, 
                { id : 2, nombre : 'UF' }, 
                { id : 3, nombre : 'UTM' } 
    ];

    $scope.cotizaciones = [
                      { id : 1, nombre : 'UF' }, 
                      { id : 2, nombre : '$' }
    ];

    $scope.sueldo = { liquidoMoneda : $scope.monedas[0].nombre, cotizacionIsapre : $scope.monedas[1].nombre, brutoMoneda : $scope.monedas[0].nombre, asignacionesMoneda : $scope.monedas[0].nombre, tipoContrato : 'Indefinido', seguroCesantia : true, gratificacion : true };

    $scope.nuevaAsignacion = function(){
      $scope.tituloAsignacion = 'Nueva Asignación';
      $scope.sueldo.asignacionMoneda = $scope.monedas[0].nombre;
      $scope.sueldo.asignacion = null;
      $scope.isAsignacion = !$scope.isAsignacion;
    }

    $scope.editarAsignacion = function(asig){
      $scope.tituloAsignacion = 'Modificar Asignación';
      asigIndex = $scope.asignaciones.indexOf(asig);
      $scope.isAsignacion = true;
      $scope.isEditAsignacion = true;
      $scope.sueldo.asignacionMoneda = asig.moneda;
      $scope.sueldo.asignacion = asig.monto;
    }

    $scope.updateAsignacion = function(asig){
      $scope.isAsignacion = false;
      $scope.isEditAsignacion = false;

      $scope.asignaciones[asigIndex].moneda = asig.asignacionMoneda;
      $scope.asignaciones[asigIndex].monto = asig.asignacion;

      $scope.sueldo.asignacionMoneda = $scope.monedas[0].nombre;
      $scope.sueldo.asignacion = null;
    }

    $scope.eliminarAsignacion = function(asig){
      var index = $scope.asignaciones.indexOf(asig);
      $scope.asignaciones.splice(index,1);
    }

    $scope.agregarAsignacion = function(){
      $scope.asignaciones.push({ moneda : $scope.sueldo.asignacionMoneda, monto : $scope.sueldo.asignacion });
      $scope.isAsignacion = false;
    }

    function openSueldoBase(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-sueldo-base.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormSueldoBaseCtrl',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (sueldoBase) {
        $uibModalInstance.close(sueldoBase);
      }, function () {
        javascript:void(0)
      });
    }

    function crearModels(){
      for(var i=0,len=seguroCesantia.length; i<len; i++){
        if(seguroCesantia[i].tipoContrato==='Contrato Plazo Indefinido'){
          var tasaSeguroIndefinido = seguroCesantia[i].financiamientoTrabajador;
        }        
        if(seguroCesantia[i].tipoContrato==='Contrato Plazo Fijo'){
          var tasaSeguroFijo = seguroCesantia[i].financiamientoTrabajador;
        }          
      }
      $scope.tasaSeguroIndefinido = tasaSeguroIndefinido;
      $scope.tasaSeguroFijo = tasaSeguroFijo;
      for(var i=0,len=rentasTopesImponibles.length; i<len; i++){
        if(rentasTopesImponibles[i].nombre==='Para afiliados a una AFP'){
          var rtiAfp = moneda.convertirUF(rentasTopesImponibles[i].valor);
        }
        if(rentasTopesImponibles[i].nombre==='Para Seguro de Cesantía'){
          var rtiSeguroCesantia = moneda.convertirUF(rentasTopesImponibles[i].valor);
        }
      }
      $scope.rtiAfp = rtiAfp;
      $scope.rtiSeguroCesantia = rtiSeguroCesantia;
    }    

    $scope.cambiarIsapre = function(){
      if($scope.sueldo.isapre.nombre==='Fonasa'){
        $scope.sueldo.cotizacionIsapre = '%';
        $scope.sueldo.montoIsapre = 7;
      }else{
        $scope.sueldo.cotizacionIsapre = 'UF';
        $scope.sueldo.montoIsapre = null;
      }
    }

    /*function calcular(){
      var totalAsignaciones = 0;
      var montoSeguro = 0;
      var tasaAfp = 0;
      var tasaFonasa = 0;
      var montoAfp = 0;
      var tasaSeguro = 0;
      var montoSeguro = 0;
      var salud = 0;
      var asig = [];
      var asigPesos;
      var gratificacion = 0;
      var factorImpuesto = 0;
      var rebajar = 0;
      var impuesto = 0;

      for(var i=0,len=$scope.asignaciones.length; i<len; i++){
        asigPesos = $scope.convertir($scope.asignaciones[i].monto, $scope.asignaciones[i].moneda);
        totalAsignaciones = (totalAsignaciones + asigPesos);
        asig.push(asigPesos);
      }

      if($scope.sueldo.tipoContrato==='Indefinido'){
        $scope.tasaSeguroCesantia = $scope.tasaSeguroIndefinido;
      }else{
        $scope.tasaSeguroCesantia = $scope.tasaSeguroFijo;
      }

      var sueldoLiquido = $scope.convertir($scope.sueldo.liquido, $scope.sueldo.liquidoMoneda);
      if($scope.sueldo.isapre.nombre!=='Fonasa' && $scope.sueldo.isapre.nombre!=='Sin Isapre'){
        salud = $scope.convertir($scope.sueldo.montoIsapre, $scope.sueldo.cotizacionIsapre);
      }else if($scope.sueldo.isapre.nombre==='Sin Isapre'){
        salud = 0;        
        $scope.sueldo.montoIsapre = 0;
      }else{
        tasaFonasa = 0.07;        
      }
      var ri = (sueldoLiquido - totalAsignaciones + salud);
      
      if($scope.sueldo.afp.nombre!=='No está en AFP'){
        tasaAfp = ($scope.sueldo.afp.tasa / 100);
        if($scope.sueldo.seguroCesantia){
          tasaSeguro = ($scope.tasaSeguroCesantia / 100);
        }
      }else{
        $scope.sueldo.afp.tasa = 0;
      }
      var factor = (1 - (tasaAfp + tasaSeguro + tasaFonasa));
      ri = Math.round(ri / factor);   

      if(ri>$scope.rtiAfp){
        montoAfp = Math.round($scope.rtiAfp * tasaAfp);
      }else{
        montoAfp = Math.round(ri * tasaAfp);
      }
      if($scope.sueldo.isapre.nombre==='Fonasa'){
        salud = Math.round(ri * tasaFonasa);
      }else{
        var obligatorio = Math.round(ri * 0.07);
        if(salud<obligatorio){
          salud = obligatorio;
        }
      }
      var sueldoBase = angular.copy(ri);
      if($scope.sueldo.gratificacion){
        sueldoBase = Math.round(ri / 1.25);
        var tope = (( 4.75 * rmi ) / 12 );
        gratificacion = Math.round(sueldoBase * 0.25);
        if(gratificacion>tope){
          gratificacion = tope;
          sueldoBase = (ri - gratificacion);
        }
      }      
      if($scope.sueldo.seguroCesantia && $scope.sueldo.tipoContrato==='Indefinido'){
        montoSeguro = Math.round(ri * tasaSeguro);
        if(ri>$scope.rtiSeguroCesantia){
          montoSeguro = Math.round($scope.rtiSeguroCesantia * tasaSeguro);
        }
      }else{
        $scope.tasaSeguroCesantia = 0;
      }
      montoAfp = Math.round(ri * tasaAfp);
      if(ri>$scope.rtiAfp){
        ri = $scope.rtiAfp;
        montoAfp = Math.round(ri * tasaAfp);        
      }
      var baseImpuesto = (ri - salud - montoAfp - montoSeguro);

      sueldoBase = (sueldoLiquido - totalAsignaciones - gratificacion + salud + montoAfp + montoSeguro);

      console.log(baseImpuesto)
      var detalle = { sueldoLiquido : sueldoLiquido, rentaImponible : ri, sueldoBase : sueldoBase, gratificacion : gratificacion, tipoContrato : $scope.sueldo.tipoContrato, afp : { nombre : $scope.sueldo.afp.nombre, tasa : $scope.sueldo.afp.tasa, monto : montoAfp }, seguroCesantia : { tasa : $scope.tasaSeguroCesantia, monto : montoSeguro }, salud: { nombre : $scope.sueldo.isapre.nombre, plan : $scope.sueldo.montoIsapre, cotizacion : $scope.sueldo.cotizacionIsapre, monto : salud }, asignaciones : asig };

      if()
      return detalle;
    }*/

    function calcular(){
      var totalAsignaciones = 0;
      var montoSeguro = 0;
      var tasaAfp = 0;
      var tasaFonasa = 0;
      var montoAfp = 0;
      var tasaSeguro = 0;
      var montoSeguro = 0;
      var salud = 0;
      var asig = [];
      var asigPesos;
      var gratificacion = 0;
      var factorImpuesto = 0;
      var rebajar = 0;
      var impuesto = 0;      

      for(var i=0,len=$scope.asignaciones.length; i<len; i++){
        asigPesos = moneda.convertir($scope.asignaciones[i].monto, $scope.asignaciones[i].moneda);
        totalAsignaciones = (totalAsignaciones + asigPesos);
        asig.push(asigPesos);
      }

      if($scope.sueldo.tipoContrato==='Indefinido'){
        $scope.tasaSeguroCesantia = $scope.tasaSeguroIndefinido;
      }else{
        $scope.tasaSeguroCesantia = $scope.tasaSeguroFijo;
      }

      var sueldoLiquido = moneda.convertir($scope.sueldo.liquido, $scope.sueldo.liquidoMoneda);
      if($scope.sueldo.isapre.nombre!=='Fonasa' && $scope.sueldo.isapre.nombre!=='Sin Isapre'){
        salud = Math.round(moneda.convertir($scope.sueldo.montoIsapre, $scope.sueldo.cotizacionIsapre));
      }else if($scope.sueldo.isapre.nombre==='Sin Isapre'){
        salud = 0;        
        $scope.sueldo.montoIsapre = 0;
      }else{
        tasaFonasa = 0.07;        
      }
      var ri = (sueldoLiquido - totalAsignaciones + salud);
      
      if($scope.sueldo.afp.nombre!=='No está en AFP'){
        tasaAfp = ($scope.sueldo.afp.tasa / 100);
        if($scope.sueldo.seguroCesantia){
          tasaSeguro = ($scope.tasaSeguroCesantia / 100);
        }
      }else{
        $scope.sueldo.afp.tasa = 0;
      }
      var factor = (1 - (tasaAfp + tasaSeguro + tasaFonasa));
      ri = Math.round(ri / factor);   

      if(ri>$scope.rtiAfp){
        montoAfp = Math.round($scope.rtiAfp * tasaAfp);
      }else{
        montoAfp = Math.round(ri * tasaAfp);
      }
      if($scope.sueldo.isapre.nombre==='Fonasa'){
        salud = Math.round(ri * tasaFonasa);
      }else if($scope.sueldo.isapre.nombre!=='Sin Isapre'){
        var obligatorio = Math.round(ri * 0.07);
        if(salud<obligatorio){
          salud = obligatorio;
        }
      }
      var sueldoBase = angular.copy(ri);
      if($scope.sueldo.gratificacion){
        sueldoBase = Math.round(ri / 1.25);
        var tope = (( 4.75 * rmi ) / 12 );
        gratificacion = Math.round(sueldoBase * 0.25);
        if(gratificacion>tope){
          gratificacion = tope;
          sueldoBase = (ri - gratificacion);
        }
      }      
      if($scope.sueldo.seguroCesantia && $scope.sueldo.tipoContrato==='Indefinido'){
        montoSeguro = Math.round(ri * tasaSeguro);
        if(ri>$scope.rtiSeguroCesantia){
          montoSeguro = Math.round($scope.rtiSeguroCesantia * tasaSeguro);
        }
      }else{
        $scope.tasaSeguroCesantia = 0;
      }
      montoAfp = Math.round(ri * tasaAfp);
      if(ri>$scope.rtiAfp){
        ri = $scope.rtiAfp;
        montoAfp = Math.round(ri * tasaAfp);        
      }
      var baseImpuesto = (ri - salud - montoAfp - montoSeguro);

      sueldoBase = (sueldoLiquido - totalAsignaciones - gratificacion + salud + montoAfp + montoSeguro);

      var detalle = { sueldoLiquido : sueldoLiquido, rentaImponible : ri, baseImpuesto : baseImpuesto, impuesto : 0, sueldoBase : sueldoBase, gratificacion : gratificacion, tipoContrato : $scope.sueldo.tipoContrato, afp : { nombre : $scope.sueldo.afp.nombre, tasa : $scope.sueldo.afp.tasa, monto : montoAfp }, seguroCesantia : { tasa : $scope.tasaSeguroCesantia, monto : montoSeguro }, salud: { nombre : $scope.sueldo.isapre.nombre, plan : $scope.sueldo.montoIsapre, cotizacion : $scope.sueldo.cotizacionIsapre, monto : salud }, asignaciones : asig };
      console.log(ri)
      console.log(sueldoBase)
      if(isTributable(baseImpuesto)){
        return recalcular(sueldoBase, $scope.tasaSeguroCesantia, tasaAfp, asig, totalAsignaciones); 
      }else{
        return detalle;        
      }
    }

    function isTributable(baseImpuesto){
      var minimoTributable = moneda.convertirUTM(tabla[0].imponibleMensualHasta);
      if(baseImpuesto > minimoTributable){
       return true;
      }      
      return false;
    }

    function recalcular(sueldoBase, tasaSeguroCesantia, tasaAfp, asignaciones, totalAsignaciones){
      contador++;
      var gratificacion = 0;
      var rentaImponible = 0;
      var montoAfp = 0;
      var montoSeguro = 0;
      var salud = 0;
      var impuesto = 0;

      if($scope.sueldo.gratificacion){
        var tope = Math.round((( 4.75 * rmi ) / 12 ));
        gratificacion = Math.round(sueldoBase * 0.25);
        if(gratificacion>tope){
          gratificacion = tope;
        }
      }

      rentaImponible = (sueldoBase + gratificacion);

      if($scope.sueldo.seguroCesantia && $scope.sueldo.tipoContrato==='Indefinido' && $scope.sueldo.afp.nombre!=='No está en AFP'){
        montoSeguro = Math.round(rentaImponible * (tasaSeguroCesantia / 100));
        if(rentaImponible>$scope.rtiSeguroCesantia){
          montoSeguro = Math.round($scope.rtiSeguroCesantia * (tasaSeguroCesantia / 100));
        }
      }

      if(rentaImponible>$scope.rtiAfp){
        montoAfp = Math.round($scope.rtiAfp * tasaAfp);        
      }else{
        montoAfp = Math.round(rentaImponible * tasaAfp);        
      }
      

      if($scope.sueldo.isapre.nombre!=='Fonasa' && $scope.sueldo.isapre.nombre!=='Sin Isapre'){
        salud = Math.round(moneda.convertir($scope.sueldo.montoIsapre, $scope.sueldo.cotizacionIsapre));
        if(rentaImponible>$scope.rtiAfp){
          var obligatorio = Math.round($scope.rtiAfp * 0.07);  
        }else{
          var obligatorio = Math.round(rentaImponible * 0.07);  
        }
        if(salud<obligatorio){
          salud = obligatorio;
        }
      }else if($scope.sueldo.isapre.nombre==='Sin Isapre'){
        salud = 0;        
      }else{
        if(rentaImponible>$scope.rtiAfp){
          salud = Math.round($scope.rtiAfp * 0.07); 
        }else{
          salud = Math.round(rentaImponible * 0.07); 
        }
      }

      if(rentaImponible>$scope.rtiAfp){
        var baseImpuesto = ($scope.rtiAfp - (salud + montoAfp + montoSeguro));
      }else{
        var baseImpuesto = (rentaImponible - (salud + montoAfp + montoSeguro));
      }

      impuesto = calcularImpuesto(baseImpuesto);
      var sueldoLiquido = (rentaImponible - (montoAfp + salud + montoSeguro + impuesto) + totalAsignaciones);

      var detalle = { sueldoLiquido : sueldoLiquido, rentaImponible : rentaImponible, baseImpuesto : baseImpuesto, impuesto : impuesto, sueldoBase : sueldoBase, gratificacion : gratificacion, tipoContrato : $scope.sueldo.tipoContrato, afp : { nombre : $scope.sueldo.afp.nombre, tasa : $scope.sueldo.afp.tasa, monto : montoAfp }, seguroCesantia : { tasa : $scope.tasaSeguroCesantia, monto : montoSeguro }, salud: { nombre : $scope.sueldo.isapre.nombre, plan : $scope.sueldo.montoIsapre, cotizacion : $scope.sueldo.cotizacionIsapre, monto : salud }, asignaciones : asignaciones };
      var sueldoDeseado = moneda.convertir($scope.sueldo.liquido, $scope.sueldo.liquidoMoneda);
      
      if(sueldoLiquido===sueldoDeseado){
        return detalle;
      }else{
        if(sueldoLiquido<sueldoDeseado){
          var resto = (sueldoDeseado - sueldoLiquido);
          if(resto>1){
            return recalcular((sueldoBase + Math.round(resto / 2)), $scope.tasaSeguroCesantia, tasaAfp, asignaciones, totalAsignaciones); 
          }else{
            return recalcular((sueldoBase + 1), $scope.tasaSeguroCesantia, tasaAfp, asignaciones, totalAsignaciones); 
          }
        }else{
          var resto = (sueldoLiquido - sueldoDeseado);
          /*console.log('-')
          console.log('liquido: ' + sueldoLiquido)
          console.log('resto: ' + resto)*/
          if(resto>1){
            return recalcular((sueldoBase - Math.round(resto / 2)), $scope.tasaSeguroCesantia, tasaAfp, asignaciones, totalAsignaciones); 
          }else{
            return recalcular((sueldoBase - 1), $scope.tasaSeguroCesantia, tasaAfp, asignaciones, totalAsignaciones); 
          }
        }
      }

    }

    function calcularImpuesto(baseImpuesto){
      var factor = 0;
      var cantidadARebajar = 0;
      var impuesto = 0;

      for(var i=0,len=tabla.length; i<len; i++){
        if(baseImpuesto > moneda.convertirUTM(tabla[i].imponibleMensualDesde) && baseImpuesto < moneda.convertirUTM(tabla[i].imponibleMensualHasta)){
          factor = tabla[i].factor;
          cantidadARebajar = moneda.convertirUTM(tabla[i].cantidadARebajar);
          break;
        }
      }
      impuesto = Math.round((baseImpuesto * (factor / 100)) - cantidadARebajar);

      return impuesto;
    }

    $scope.calcularSueldoBase = function(){
      crearModels();
      var detalle = calcular();
      openSueldoBase(detalle);
    }

  })  
  .controller('FormTrabajadorCtrl', function ($scope, formulario, $uibModalInstance, $http, objeto, $uibModal, Notification, $rootScope, trabajador, constantes, $filter, haber, descuento, apv, fecha, moneda, validations) {
    
    $scope.empresa = $rootScope.globals.currentUser.empresa;
    $scope.opciones = angular.copy(formulario);
    $scope.errores = {};
    $scope.errorRUT = '';
    $scope.rutError = false;

    $scope.uf = $rootScope.globals.indicadores.uf.valor;
    $scope.utm = $rootScope.globals.indicadores.utm.valor;    
    $scope.apv = {};
    $scope.descuento = {};
    var rentasTopesImponibles;
    

    $scope.convertir = function(valor, mon){
      return moneda.convertir(valor, mon);
    }

    $scope.parentescos = [ 
              { id : 1, nombre : 'Hijo/a o Hijastro/a' }, 
              { id : 2, nombre : 'Cónyuge' },
              { id : 3, nombre : 'Nieto/a' },
              { id : 4, nombre : 'Bisnieto/a' },
              { id : 5, nombre : 'Madre' },
              { id : 6, nombre : 'Padre' },
              { id : 7, nombre : 'Madre Viuda' },
              { id : 8, nombre : 'Abuelo/a' },
              { id : 9, nombre : 'Bisabuelo/a' },
              { id : 10, nombre : 'Otro' }
    ];

    $scope.monedas = [
                { id : 1, nombre : '$' }, 
                { id : 2, nombre : 'UF' }, 
                { id : 3, nombre : 'UTM' } 
    ];

    $scope.cotizaciones = [
                      { id : 1, nombre : 'UF' }, 
                      { id : 2, nombre : '$' },
                      { id : 3, nombre : '7%' },
                      { id : 4, nombre : '7% + UF' }
    ];

    $scope.regimenes = [ 'A', 'B' ];

    $scope.tabDatosPersonales = true;
    $scope.tabDomicilio = false;
    $scope.tabDestinacion = false;
    $scope.tabAntecedentesComerciales = false;
    $scope.tabRemuneracion = false;
    $scope.tabAFP = false;
    $scope.tabDescuentos = false;
    $scope.isImponible = false; 
    $scope.isNoImponible = false; 
    $scope.isSelectSeguro = false;
    $scope.isSelectApv = false;
    $scope.isEditAFPSeguro = false;
    $scope.isEditAPV = false;
    $scope.isApv = false; 
    $scope.isDescuento = false; 
    $scope.isFamiliar = false; 
    $scope.isVencimiento = true;
    $scope.cargas = false;
    $scope.isEditNoImponible = false;
    $scope.isEditImponible = false;
    $scope.isEditApv = false;
    $scope.isEditDescuento = false;
    $scope.isEditFamiliar = false;
    $scope.isCargas = false;

    if(objeto){
      $scope.trabajador = angular.copy(objeto);
      $scope.trabajador.fechaNacimiento = fecha.convertirFecha($scope.trabajador.fechaNacimiento);
      $scope.trabajador.fechaIngreso = fecha.convertirFecha($scope.trabajador.fechaIngreso);
      $scope.trabajador.fechaReconocimiento = fecha.convertirFecha($scope.trabajador.fechaReconocimiento);
      $scope.trabajador.fechaVencimiento = fecha.convertirFecha($scope.trabajador.fechaVencimiento);
      if($scope.trabajador.montoColacion==0){
        $scope.trabajador.montoColacion = null;
      }
      if($scope.trabajador.montoMovilizacion==0){
        $scope.trabajador.montoMovilizacion = null;
      }
      if($scope.trabajador.montoViatico==0){
        $scope.trabajador.montoViatico = null;
      }
    }else{
      $scope.trabajador = { 
        rut : '',
        fechaNacimiento : null,
        monedaSueldo : $scope.monedas[0].nombre, 
        monedaColacion : $scope.monedas[0].nombre, 
        proporcionalColacion : true,
        monedaMovilizacion : $scope.monedas[0].nombre, 
        proporcionalMovilizacion : true,
        monedaViatico : $scope.monedas[0].nombre, 
        proporcionalViatico : true,
        tipoTrabajador : 'Normal',     
        gratificacion : 'm',   
        monedaSindicato : $scope.monedas[0].nombre,
        cargas : [],
        descuentos : [],
        apvs : [],
        haberes : []
      };
    }
    
    actualizarOptions();

    function isCargas(){
      $scope.isCargas = false;
      for(var i=0, len=$scope.trabajador.cargas.length; i<len; i++){
        if($scope.trabajador.cargas[i].esCarga){
          $scope.isCargas = true;
        }
      }
    }

    isCargas();

    $scope.openTab = function(tab){
      switch (tab) {
        case 'datosPersonales':
          $scope.tabDatosPersonales = true;
          $scope.tabDomicilio = false;
          $scope.tabDestinacion = false;
          $scope.tabAntecedentesComerciales = false;
          $scope.tabRemuneracion = false;
          $scope.tabAFP = false;
          $scope.tabDescuentos = false;
          $scope.tabCargas = false;
          break;
        case 'domicilio':
          $scope.tabDatosPersonales = false;
          $scope.tabDomicilio = true;
          $scope.tabDestinacion = false;
          $scope.tabAntecedentesComerciales = false;
          $scope.tabRemuneracion = false;
          $scope.tabAFP = false;
          $scope.tabDescuentos = false;
          $scope.tabCargas = false;
          break;
        case 'destinacion':
          $scope.tabDatosPersonales = false;
          $scope.tabDomicilio = false;
          $scope.tabDestinacion = true;
          $scope.tabAntecedentesComerciales = false;
          $scope.tabRemuneracion = false;
          $scope.tabAFP = false;
          $scope.tabDescuentos = false;
          $scope.tabCargas = false;
          break;
        case 'antecedentesComerciales':
          $scope.tabDatosPersonales = false;
          $scope.tabDomicilio = false;
          $scope.tabDestinacion = false;
          $scope.tabAntecedentesComerciales = true;
          $scope.tabRemuneracion = false;
          $scope.tabAFP = false;
          $scope.tabDescuentos = false;
          $scope.tabCargas = false;
          break;
        case 'remuneracion':
          $scope.tabDatosPersonales = false;
          $scope.tabDomicilio = false;
          $scope.tabDestinacion = false;
          $scope.tabAntecedentesComerciales = false;
          $scope.tabRemuneracion = true;
          $scope.tabAFP = false;
          $scope.tabDescuentos = false;
          $scope.tabCargas = false;
          break;
        case 'afp':
          $scope.tabDatosPersonales = false;
          $scope.tabDomicilio = false;
          $scope.tabDestinacion = false;
          $scope.tabAntecedentesComerciales = false;
          $scope.tabRemuneracion = false;
          $scope.tabAFP = true;
          $scope.tabDescuentos = false;
          $scope.tabCargas = false;
          break;
        case 'descuentos':
          $scope.tabDatosPersonales = false;
          $scope.tabDomicilio = false;
          $scope.tabDestinacion = false;
          $scope.tabAntecedentesComerciales = false;
          $scope.tabRemuneracion = false;
          $scope.tabAFP = false;
          $scope.tabDescuentos = true;
          $scope.tabCargas = false;
          break;
        case 'cargas':
          $scope.tabDatosPersonales = false;
          $scope.tabDomicilio = false;
          $scope.tabDestinacion = false;
          $scope.tabAntecedentesComerciales = false;
          $scope.tabRemuneracion = false;
          $scope.tabAFP = false;
          $scope.tabDescuentos = false;
          $scope.tabCargas = true;
          break;
      }
    }

    function actualizarOptions(){
      if( $scope.trabajador.id ){        
        if($scope.trabajador.prevision.id===8){
          $scope.trabajador.afp = $filter('filter')( $scope.opciones.afps, {id :  $scope.trabajador.afp.id }, true )[0];
        }else if($scope.trabajador.prevision.id===9){
          $scope.trabajador.afp = $filter('filter')( $scope.opciones.exCajas, {id :  $scope.trabajador.afp.id }, true )[0];
        }
        $scope.trabajador.prevision = $filter('filter')( $scope.opciones.previsiones, {id :  $scope.trabajador.prevision.id }, true )[0];
        $scope.trabajador.afpSeguro = $filter('filter')( $scope.opciones.afpsSeguro, {id :  $scope.trabajador.afpSeguro.id }, true )[0];
        $scope.trabajador.cargo = $filter('filter')( $scope.opciones.cargos, {id :  $scope.trabajador.cargo.id }, true )[0];
        $scope.trabajador.seccion = $filter('filter')( $scope.opciones.secciones, {id :  $scope.trabajador.seccion.id }, true )[0];
        $scope.trabajador.banco = $filter('filter')( $scope.opciones.bancos, {id :  $scope.trabajador.banco.id }, true )[0];
        $scope.trabajador.tienda = $filter('filter')( $scope.opciones.tiendas, {id :  $scope.trabajador.tienda.id }, true )[0];
        $scope.trabajador.centroCosto = $filter('filter')( $scope.opciones.centros, {id :  $scope.trabajador.centroCosto.id }, true )[0];
        $scope.trabajador.tipoCuenta = $filter('filter')( $scope.opciones.tiposCuentas, {id :  $scope.trabajador.tipoCuenta.id }, true )[0];
        $scope.trabajador.titulo = $filter('filter')( $scope.opciones.titulos, {id :  $scope.trabajador.titulo.id }, true )[0];
        $scope.trabajador.nacionalidad = $filter('filter')( $scope.opciones.nacionalidades, {id :  $scope.trabajador.nacionalidad.id }, true )[0];
        $scope.trabajador.estadoCivil = $filter('filter')( $scope.opciones.estadosCiviles, {id :  $scope.trabajador.estadoCivil.id }, true )[0];
        $scope.trabajador.tipo = $filter('filter')( $scope.opciones.tipos, {id :  $scope.trabajador.tipo.id }, true )[0];
        $scope.trabajador.tipoJornada = $filter('filter')( $scope.opciones.tiposJornadas, {id :  $scope.trabajador.tipoJornada.id }, true )[0];
        $scope.trabajador.tipoContrato = $filter('filter')( $scope.opciones.tiposContratos, {id :  $scope.trabajador.tipoContrato.id }, true )[0];
        $scope.trabajador.isapre = $filter('filter')( $scope.opciones.isapres, {id :  $scope.trabajador.isapre.id }, true )[0];
      }
      $scope.RMI = $scope.opciones.rmi.valor;   
      $scope.RTI = $scope.opciones.rti.valor;
      rentasTopesImponibles = $scope.opciones.rentasTopesImponibles;
    }    

    $scope.obtenerJefe = function(){
      $scope.jefe = $scope.trabajador.seccion.encargado.nombreCompleto;
    }

    $scope.cambiarMonedaSueldo = function(){
      $scope.trabajador.sueldoBase = null;
      $scope.trabajador.sueldoRMI = null;
      $scope.sueldoPesos = null;
    }

    $scope.cambiarMonedaSindicato = function(){
      $scope.trabajador.montoSindicato = null;
    }

    $scope.asignarSueldo = function(renta){
      if(renta==='rmi'){
        $scope.trabajador.monedaSueldo = $scope.monedas[0].nombre;
        $scope.trabajador.sueldoBase = $scope.RMI;
      }else if(renta==='rti'){
        $scope.trabajador.monedaSueldo = $scope.monedas[0].nombre;
        $scope.trabajador.sueldoBase = $scope.convertir($scope.RTI, 'UF');
      }else{
        $scope.trabajador.monedaSueldo = $scope.monedas[0].nombre;
        var gratificacion = (( 4.75 * $scope.RMI ) / 12 );
        var sueldo = $scope.convertir($scope.RTI, 'UF');
        $scope.trabajador.sueldoBase = (sueldo - gratificacion);      
      }
    }    

    $scope.cambiarTipoTrabajador = function(){
      if($scope.trabajador.tipoTrabajador==='Normal'){
        $scope.trabajador.excesoRetiro = false;
      }
    }

    $scope.cambiarMonedaColacion = function(){
      $scope.trabajador.colacion = null;
      $scope.colacionPesos = null;
    }

    $scope.cambiarMonedaMovilizacion = function(){
      $scope.trabajador.movilizacion = null;
      $scope.movilizacionPesos = null;
    }

    $scope.cambiarMonedaViatico = function(){
      $scope.trabajador.viatico = null;
      $scope.viaticoPesos = null;
    }

    $scope.cambiarMonedaAPV = function(){
      $scope.apv.monto = null;
      $scope.apvPesos = null;
    }

    $scope.cambiarMonedaDescuento = function(){
      $scope.descuento.monto = null;
      $scope.descuentoPesos = null;
    }

    $scope.cambiarMonedaImponible = function(){
      $scope.imponible.monto = null;
      $scope.imponiblePesos = null;
    }

    $scope.cambiarMonedaNoImponible = function(){
      $scope.noImponible.monto = null;
      $scope.noImponiblePesos = null;
    }

    $scope.editAFPSeguro = function(){
      if($scope.isEditAFPSeguro){
        $scope.isEditAFPSeguro = false;
      }else{
        $scope.isEditAFPSeguro = true;
      }
    }

    $scope.editAPV = function(){
      if($scope.isEditAPV){
        $scope.isEditAPV = false;
      }else{
        $scope.isEditAPV = true;
      }
    }

    $scope.selectSeguro = function(){
      $scope.isSelectSeguro = true;
    }

    $scope.selectAPV = function(){
      $scope.isSelectApv = true;
    }

    $scope.cambiarAFP = function(){
      $scope.seguroAFP();
      $scope.AFPAPV();
      if($scope.trabajador.afp.nombre === 'No está en AFP'){
        $scope.trabajador.seguroDesempleo = false;
        $scope.isApv = false;
        $scope.trabajador.apvs = [];
      }else{
        $scope.trabajador.seguroDesempleo = true;        
      }
    }

    $scope.seguroAFP = function(){
      if(!$scope.isSelectSeguro){
        if($scope.trabajador.afp.nombre === 'No está en AFP'){
          $scope.isEditAFPSeguro = true;
        }else{
          $scope.trabajador.afpSeguro = $filter('filter')( $scope.opciones.afpsSeguro, {id :  $scope.trabajador.afp.id }, true )[0];                  
        }
      }
    }

    $scope.AFPAPV = function(){
      if(!$scope.isSelectApv || $scope.trabajador.apvs.length > 0){
        $scope.apv.afp = $filter('filter')( $scope.opciones.afpsApvs, {nombre :  $scope.trabajador.afp.nombre }, true )[0]; 
      }
    }

    $scope.cambiarIsapre = function(){
      if($scope.trabajador.isapre.nombre==="Fonasa"){
        $scope.trabajador.cotizacionIsapre = '%';
        $scope.trabajador.montoIsapre = 7;
      }else{
        $scope.trabajador.montoIsapre = null;
        $scope.trabajador.cotizacionIsapre = $scope.cotizaciones[0].nombre;
      }
    }

    $scope.cotizacionIsapre = function(){
      $scope.trabajador.montoIsapre = null;
      $scope.isaprePesos = null;
    }

    $scope.agregarImponible = function(){
      if($scope.isImponible){
        $scope.isImponible = false;
      }else{
        $scope.tituloImponible = 'Agregar Imponible';
        $scope.isImponible = true;
        $scope.imponible = { moneda : $scope.monedas[0].nombre };
      }
    }

    $scope.updateImponible = function(imp){
      $scope.isImponible = false;
      $scope.isEditImponible = false;

      $scope.trabajador.haberes[$scope.impIndex].tipo = imp.tipo;
      $scope.trabajador.haberes[$scope.impIndex].moneda = imp.moneda;
      $scope.trabajador.haberes[$scope.impIndex].monto = imp.monto;

      $scope.imponible.tipo = "";
      $scope.imponible.monto = "";
    }

    $scope.guardarImponible = function(){
      var imp = angular.copy($scope.imponible);
      $scope.trabajador.haberes.push(imp);
      $scope.isImponible = false;
      $scope.imponible.tipo = "";
      $scope.imponible.monto = "";
    }

    $scope.editarImponible = function(imp){
      $scope.tituloImponible = 'Modificar no Imponible';
      $scope.impIndex = $scope.trabajador.haberes.indexOf(imp);
      $scope.isImponible = true;
      $scope.isEditImponible = true;
      $scope.imponible = { moneda : imp.moneda, monto : imp.monto, tipo : imp.tipo };
      $scope.imponible.tipo = $filter('filter')( $scope.opciones.tiposHaber, {id :  imp.tipo.id }, true )[0]; 
    }

    $scope.eliminarImponible = function(imp){
      var index = $scope.trabajador.haberes.indexOf(imp );
      $scope.trabajador.haberes.splice(index,1);
    }

    $scope.updateNoImponible = function(noImp){
      $scope.isNoImponible = false;
      $scope.isEditNoImponible = false;

      $scope.trabajador.haberes[$scope.noImpIndex].tipo = noImp.tipo;
      $scope.trabajador.haberes[$scope.noImpIndex].moneda = noImp.moneda;
      $scope.trabajador.haberes[$scope.noImpIndex].monto = noImp.monto;

      $scope.noImponible.tipo = "";
      $scope.noImponible.monto = "";
    }

    $scope.guardarNoImponible = function(){
      var noImp = angular.copy($scope.noImponible);
      $scope.trabajador.haberes.push(noImp);
      $scope.isNoImponible = false;
      $scope.noImponible.tipo = "";
      $scope.noImponible.monto = "";
    }

    $scope.editarNoImponible = function(noImp){
      $scope.tituloNoImponible = 'Modificar no Imponible';
      $scope.noImpIndex = $scope.trabajador.haberes.indexOf(noImp);
      $scope.isNoImponible = true;
      $scope.isEditNoImponible = true;
      $scope.noImponible = { moneda : noImp.moneda, monto : noImp.monto, tipo : noImp.tipo };
      $scope.noImponible.tipo = $filter('filter')( $scope.opciones.tiposHaber, {id :  noImp.tipo.id }, true )[0]; 
    }

    $scope.eliminarNoImponible = function(noImp){
      var index = $scope.trabajador.haberes.indexOf(noImp);
      $scope.trabajador.haberes.splice(index,1);
    }

    $scope.guardarAPV = function(){
      var apv = angular.copy($scope.apv);
      $scope.trabajador.apvs.push(apv);
      console.log($scope.trabajador.apvs)
      $scope.isApv = false;
      $scope.apv.afp = "";
      $scope.apv.monto = "";
      $scope.apv.regimen = $scope.regimenes[0];
      $scope.apv.moneda = $scope.monedas[0].nombre;
    }

    $scope.editarAPV = function(apv){
      $scope.apvIndex = $scope.trabajador.apvs.indexOf(apv);
      $scope.isApv = true;
      $scope.isEditApv = true;
      $scope.apv = { moneda : apv.moneda, regimen : apv.regimen, monto : apv.monto, afp : apv.afp, formaPago : apv.formaPago };
      $scope.apv.afp = $filter('filter')( $scope.opciones.afpsApvs, {nombre :  $scope.trabajador.afp.nombre }, true )[0];
      $scope.apv.formaPago = $filter('filter')( $scope.opciones.formasPago, {id :  apv.formaPago.id }, true )[0];
    }

    $scope.eliminarAPV = function(apv){
      var index = $scope.trabajador.apvs.indexOf(apv);
      $scope.trabajador.apvs.splice(index,1);
    }

    $scope.updateAPV = function(apv){
      $scope.isApv = false;
      $scope.isEditApv = false;

      $scope.trabajador.apvs[$scope.apvIndex].afp = apv.afp;
      $scope.trabajador.apvs[$scope.apvIndex].moneda = apv.moneda;
      $scope.trabajador.apvs[$scope.apvIndex].regimen = apv.regimen;
      $scope.trabajador.apvs[$scope.apvIndex].monto = apv.monto;

      $scope.apv.afp = "";
      $scope.apv.monto = "";
    }    

    $scope.guardarDescuento = function(){
      var desc = angular.copy($scope.descuento);
      $scope.trabajador.descuentos.push(desc);
      $scope.isDescuento = false;
      $scope.descuento.tipo = "";
      $scope.descuento.monto = "";
      $scope.descuento.moneda = $scope.monedas[0].nombre;
    }
    
    $scope.agregarNoImponible = function(){
      if($scope.isNoImponible){
        $scope.isNoImponible = false;
      }else{
        $scope.tituloNoImponible = 'Agregar no Imponible';
        $scope.isNoImponible = true;
        $scope.noImponible = { moneda : $scope.monedas[0].nombre };
      }
    }

    $scope.agregarAPV = function(){
      if($scope.isApv){
        $scope.isApv = false;
      }else{
        $scope.isApv = true;
        $scope.apv.moneda = $scope.monedas[0].nombre;        
        $scope.apv.regimen = $scope.regimenes[0];        
        $scope.apv.formaPago = $scope.opciones.formasPago[0];   
      }
      $scope.AFPAPV();
    }
    
    $scope.agregarDescuento = function(){
      if($scope.isDescuento){
        $scope.isDescuento = false;
      }else{
        $scope.isDescuento = true;
        $scope.descuento.moneda = $scope.monedas[0].nombre;
      }
    }

    $scope.editarDescuento = function(desc){
      $scope.descIndex = $scope.trabajador.descuentos.indexOf(desc);
      $scope.isDescuento = true;
      $scope.isEditDescuento = true;
      $scope.descuento = { moneda : desc.moneda, monto : desc.monto, tipo : desc.tipo };
      $scope.descuento.tipo = $filter('filter')( $scope.opciones.tiposDescuento, {id :  desc.tipo.id }, true )[0]; 
    }

    $scope.eliminarDescuento = function(desc){
      var index = $scope.trabajador.descuentos.indexOf(desc);
      $scope.trabajador.descuentos.splice(index,1);
    }

    $scope.updateDescuento = function(desc){
      $scope.isDescuento = false;
      $scope.isEditDescuento = false;

      $scope.trabajador.descuentos[$scope.descIndex].tipo = desc.tipo;
      $scope.trabajador.descuentos[$scope.descIndex].moneda = desc.moneda;
      $scope.trabajador.descuentos[$scope.descIndex].monto = desc.monto;

      $scope.descuento.tipo = "";
      $scope.descuento.monto = "";
    }

    $scope.agregarFamiliar = function(){
      if($scope.isFamiliar){
        $scope.isFamiliar = false;
      }else{
        $scope.tituloFamiliar = 'Nuevo Familiar';
        $scope.isFamiliar = true;
        $scope.carga = { esCarga : false, rut : null, nombreCompleto : null, sexo : null, fechaNacimiento : null, parentesco : null, tipo : $scope.opciones.tiposCargas[0] };
      }
      $scope.isEditFamiliar = false;
    }

    $scope.editarFamiliar = function(fam){
      $scope.tituloFamiliar = 'Modificar Familiar';
      $scope.cargaIndex = $scope.trabajador.cargas.indexOf(fam);
      $scope.isFamiliar = true;
      $scope.isEditFamiliar = true;
      $scope.carga = { rut : fam.rut, nombreCompleto : fam.nombreCompleto, esCarga : fam.esCarga, tipo : fam.tipo, sexo : fam.sexo, fechaNacimiento : fecha.convertirFecha(fam.fechaNacimiento) };
      $scope.carga.parentesco = $filter('filter')( $scope.parentescos, {nombre :  fam.parentesco }, true )[0].nombre;
      $scope.carga.tipo = $filter('filter')( $scope.opciones.tiposCargas, {id :  fam.tipo.id }, true )[0];
    }

    $scope.eliminarFamiliar = function(fam){      
      var index = $scope.trabajador.cargas.indexOf(fam);
      $scope.trabajador.cargas.splice(index,1);
      isCargas();
    }

    $scope.updateFamiliar = function(fam){
      $scope.isFamiliar = false;
      $scope.isEditFamiliar = false;

      $scope.trabajador.cargas[$scope.cargaIndex].rut = fam.rut;
      $scope.trabajador.cargas[$scope.cargaIndex].nombreCompleto = fam.nombreCompleto;
      $scope.trabajador.cargas[$scope.cargaIndex].esCarga = fam.esCarga;
      $scope.trabajador.cargas[$scope.cargaIndex].sexo = fam.sexo;
      $scope.trabajador.cargas[$scope.cargaIndex].fechaNacimiento = fecha.convertirFechaFormato(fam.fechaNacimiento);
      $scope.trabajador.cargas[$scope.cargaIndex].parentesco = fam.parentesco;
      $scope.trabajador.cargas[$scope.cargaIndex].tipo = fam.tipo;

      $scope.carga.rut = "";
      $scope.carga.nombreCompleto = "";
      $scope.carga.esCarga = "";
      $scope.carga.sexo = "";
      $scope.carga.fechaNacimiento = "";
      $scope.carga.parentesco = "";
      isCargas();
    }

    $scope.guardarFamiliar = function(){
      var car = angular.copy($scope.carga); 
      car.fechaNacimiento = fecha.convertirFechaFormato(car.fechaNacimiento);     
      $scope.trabajador.cargas.push(car);

      $scope.isFamiliar = false;
      $scope.carga.nombreCompleto = "";
      $scope.carga.rut = "";
      $scope.carga.fechaNacimiento = null;
      $scope.carga.parentesco = null;
      $scope.carga.esCarga = false;
      $scope.carga.sexo = null;
      $scope.carga.tipo = null;
      isCargas();
    }
    
    $scope.getComunas = function(val){
      return $http.get( constantes.URL + 'comunas/buscador/json', {
        params: {
          termino: val
        }
      }).then(function(response){
        return response.data.map(function(item){
          return item;
        });
      });
    };

    $scope.reincorporar = function(){
      $scope.trabajador.estado = 'En Creación';
    }

    function activarUsuario(){
      openActivarUsuario($scope.trabajador);
      return true;
    }

    function openActivarUsuario(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-confirmacion.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormActivarUsuarioCtrl',
        size: 'sm',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });
      miModal.result.then(function () {
      }, function () {
      });
    }

    $scope.guardar = function (full) {
      $rootScope.cargando=true;
      $scope.trabajador.estadoUser = false;
      var response;      
      var contrato = false;
      if( $scope.trabajador.sid ){
        if(full && $scope.trabajador.estado==='Ingresado'){
          $scope.trabajador.nuevaFicha = true;          
        }
        if(full && $scope.trabajador.estado==='En Creación'){
          $scope.trabajador.estadoUser = false;
          $scope.trabajador.estado = 'Ingresado';
          $scope.trabajador.nuevaFicha = false;
          contrato = true;
        }
        response = trabajador.datos().update({sid:$scope.trabajador.sid}, $scope.trabajador);
      }else{   
        if(full){
          $scope.trabajador.estadoUser = false;
          $scope.trabajador.estado = 'Ingresado';
        }else{
          $scope.trabajador.estado = 'En Creación';
        }     
        response = trabajador.datos().create({}, $scope.trabajador);
      }
      response.$promise.then(
        function(response){
          if(response.success){                        
            var nombre = $scope.trabajador.nombres + " " + $scope.trabajador.apellidos;
            $uibModalInstance.close({mensaje : response.mensaje, contrato : contrato, trabajador : { sid:  response.sid, nombreCompleto : nombre }});                   
            $rootScope.cargando=false;
          }else{
            // error
            $scope.erroresDatos = response.errores;
            Notification.error({message: response.mensaje, title: 'Mensaje del Sistema'});
          }
          $rootScope.cargando=false;
        }
      );      
    };  

    $scope.validaRUT = function(rut){
      return validations.validaRUT(rut);
    }

    $scope.errores = function(name){
      var s = $scope.formTrabajador[name];
      return s.$invalid && s.$touched;
    }

    $scope.validaFecha = function(fecha){
      var date = $scope.formTrabajador[fecha].$viewValue;
      switch(fecha){
        case 'FechaNacimiento':
          if(date){
            $scope.invalidFechaNacimiento = !validations.validaFecha(date);
          }else{
            $scope.invalidFechaNacimiento = false;            
          }
          break;
        case 'fechaIngreso':
          if(date){
            $scope.invalidFechaIngreso = !validations.validaFecha(date);
          }else{
            $scope.invalidFechaIngreso = false;            
          }
          break;
        case 'fechaReconocimiento':
          if(date){
            $scope.invalidFechaReconocimiento = !validations.validaFecha(date);
          }else{
            $scope.invalidFechaReconocimiento = false;            
          }
          break; 
        case 'fechaVencimiento':
          if(date){
            $scope.invalidFechaVencimiento = !validations.validaFecha(date);
          }else{
            $scope.invalidFechaVencimiento = false;            
          }
          break;      
      }      
    }

    $scope.validar = function(rut){
      var bool = false;
      $scope.rutError = false;
      
      if(rut){
        if(rut.length < 8){
          bool = true;
          $scope.errorRUT = 'RUT Inválido';
          $scope.rutError = true;
        }else if(rut.length >= 8){
          if(!$scope.validaRUT(rut)){
            bool = true;
            $scope.errorRUT = 'RUT Inválido';    
            $scope.rutError = true;   
          }else{
            $scope.errorRUT = 'valido';
          }
        }
      }
      return bool;
    };
    
    // Fecha

    $scope.inlineOptions = {
      customClass: getDayClass,
      minDate: new Date(),
      showWeeks: true
    };

    $scope.dateOptions = {
      formatYear: 'yy',
      maxDate: new Date(2020, 5, 22),
      minDate: new Date(),
      startingDay: 1
    };  

    $scope.toggleMin = function() {
      $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
      $scope.dateOptions.minDate = $scope.inlineOptions.minDate;
    };

    $scope.toggleMin();

    $scope.openFechaNacimiento = function() {
      $scope.popupFechaNacimiento.opened = true;
    };

    $scope.openFechaIngreso = function() {
      $scope.popupFechaIngreso.opened = true;
    };

    $scope.openFechaReconocimiento = function() {
      $scope.popupFechaReconocimiento.opened = true;
    };

    $scope.openFechaVencimiento = function() {
      $scope.popupFechaVencimiento.opened = true;
    };

    $scope.setDate = function(year, month, day) {
      $scope.fecha = new Date(year, month, day);
    };

    $scope.openFechaNacimientoCarga = function() {
      $scope.popupFechaNacimientoCarga.opened = true;
    };

    $scope.format = ['dd-MMMM-yyyy'];

    $scope.popupFechaNacimiento = {
      opened: false
    };
    $scope.popupFechaIngreso = {
      opened: false
    };
    $scope.popupFechaReconocimiento = {
      opened: false
    };
    $scope.popupFechaVencimiento = {
      opened: false
    };
    $scope.popupFechaNacimientoCarga = {
      opened: false
    };

    function getDayClass(data) {
      var date = data.date,
        mode = data.mode;
      if (mode === 'day') {
        var dayToCheck = new Date(date).setHours(0,0,0,0);
        for (var i = 0; i < $scope.events.length; i++) {
          var currentDay = new Date($scope.events[i].date).setHours(0,0,0,0);
          if (dayToCheck === currentDay) {
            return $scope.events[i].status;
          }
        }
      }
      return '';
    }

    $scope.openCalcularSueldoBase = function () {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-calcular-sueldo-base.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormCalcularSueldoBaseCtrl',
        resolve: {
          trabajador: function () {
            return $scope.trabajador;
          },
          afps: function () {
            return $scope.opciones.afps;
          },
          isapres: function () {
            return $scope.opciones.isapres;
          },
          seguroCesantia: function () {
            return $scope.opciones.tasasSeguroCesantia;
          },
          rmi: function () {
            return $scope.RMI;
          },
          rentasTopesImponibles: function () {
            return rentasTopesImponibles;
          },
          tablaImpuestoUnico: function () {
            return $scope.opciones.tablaImpuestoUnico;
          }
        }
      });
      miModal.result.then(function (sueldoBase) {
        $scope.trabajador.sueldoBase = sueldoBase;
      }, function () {
        javascript:void(0);
      });
    };

  })
  .controller('FormActivarUsuarioCtrl', function ($scope, $uibModalInstance, objeto, $http, $filter, $rootScope) {

    $scope.trabajador = objeto;

    $scope.titulo = 'Acceso Panel Empleados';
    $scope.mensaje = "¿Desea activar el acceso al Panel de Empleados al usuario " + $scope.trabajador.nombreCompleto + "?";
    $scope.ok = 'Activar';
    $scope.isOK = true;
    $scope.isQuestion = true;
    $scope.cancel = 'No Activar';

    $scope.aceptar = function(){
      $scope.trabajador.estadoUser = true;
      $uibModalInstance.close(true);      
    }

    $scope.cerrar = function(){
      $scope.trabajador.estadoUser = false;
      $uibModalInstance.dismiss('cancel');
    }

});

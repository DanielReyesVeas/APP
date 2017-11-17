<?php
/*$sistemas=array(
    'aaae',
    'ac',
    'aduarte',
    'ASCCS', 
    'BARRITELLI',
    'BASIC',
    'beta', 
    'br',
    'cgon',
    'cl075',
    'cl083',
    'cl091',
    'cl099',
    'cl107',
    'cl115',
    'cl123',
    'cl131',
    'cl139',
    'cl147',
    'cl155',
    'cl162',
    'cme101',
    'demo',
    'estay',
    'exclientes',
    'free01',
    'grupodaso',
    'ibs',
    'JC',
    'lienlaff',
    'ofjuce',
    'prueba1',
    'prueba2',
    'prueba3',
    'prueba4',
    'prueba5',
    'prueba6',
    'prueba7',
    'prueba8',
    'prueba9',
    'prueba10',
    'prueba11',
    'prueba12',
    'prueba13',
    'prueba14',
    'prueba15',
    'prueba16',
    'prueba17',
    'prueba18',
    'prueba19',
    'prueba20',
    'prueba21',
    'prueba22',
    'top',
    'transerch',
    'master',
    'hilda',
    'jug',
    'atc'
);*/


$sistemas=array(
    'demo'
);
    

foreach( $sistemas as $sistema ){
  //  $directorioRaiz = '/home/easysystems/public_html/'.$sistema;
    $directorioRaiz = '../'.$sistema;
    if( $directorioRaiz ){
        $controladores = $directorioRaiz."/app/controllers";
        $modelos = $directorioRaiz."/app/models";
        $vistas = $directorioRaiz."/app/views";
        $scriptAngularJS = $directorioRaiz."/public/scripts";
        $vistaAngularJS = $directorioRaiz."/public/views";
        $imagesAngularJS = $directorioRaiz."/public/images";
        $publicRoot = $directorioRaiz."/public";
        //$stylosPlantilla = $directorioRaiz."/public/assets/global/css";
        //$logosPlantilla = $directorioRaiz."/public/assets/global/images/logo";
        $appRoot = $directorioRaiz."/app";

        // vistas de angularJS
        $dirViewAngularJS=array('comun', 'forms');
        foreach( $dirViewAngularJS as $dirView ) {
            if (is_dir('angularjs/'.$dirView)) {
                if (is_dir($vistaAngularJS . "/".$dirView)) {
                    // se recorren los archivos del origen y se reemplazan
                    $gestor = opendir('angularjs/'.$dirView);
                    if ($gestor) {
                        while (false !== ($entrada = readdir($gestor))) {
                            if ($entrada != "." && $entrada != "..") {
                                copy('angularjs/'.$dirView.'/' . $entrada, $vistaAngularJS . "/".$dirView."/" . $entrada);
                            }
                        }
                        closedir($gestor);
                    }
                }
            }
        }

        // scripts de angularJS
        if ( is_dir('dist/scripts') ) {
            if ( is_dir($scriptAngularJS) ) {
                // se recorren los archivos del origen y se reemplazan
                $gestor = opendir('dist/scripts');
                if ($gestor) {
                    $limpiarDir=true;
                    while (false !== ($entrada = readdir($gestor))) {
                        if ($entrada != "." && $entrada != "..") {
                            if( $limpiarDir ){
                                // se limpia el directorio de destino
                                $gestorDest = opendir( $scriptAngularJS );
                                if ($gestorDest) {
                                    while (false !== ($entradaDest = readdir($gestorDest))) {
                                        if ($entradaDest != "." && $entradaDest != "..") {
                                            unlink( $scriptAngularJS.'/'.$entradaDest );
                                        }
                                    }
                                }
                                $limpiarDir=false;
                            }
                            copy('dist/scripts/' . $entrada, $scriptAngularJS . "/". $entrada);
                        }
                    }
                    closedir($gestor);
                }
            }
        }

        // imagenes de angular
        if ( is_dir('dist/images') ) {
            if ( is_dir($imagesAngularJS) ) {
                // se recorren los archivos del origen y se reemplazan
                $gestor = opendir('dist/images');
                if ($gestor) {
                    $limpiarDir=true;
                    while (false !== ($entrada = readdir($gestor))) {
                        if ($entrada != "." && $entrada != "..") {
                            if( $limpiarDir ){
                                // se limpia el directorio de destino
                                $gestorDest = opendir( $imagesAngularJS );
                                if ($gestorDest) {
                                    while (false !== ($entradaDest = readdir($gestorDest))) {
                                        if ($entradaDest != "." && $entradaDest != "..") {
                                            unlink( $imagesAngularJS.'/'.$entradaDest );
                                        }
                                    }
                                }
                                $limpiarDir=false;
                            }
                            copy('dist/images/' . $entrada, $imagesAngularJS . "/". $entrada);
                        }
                    }
                    closedir($gestor);
                }
            }
        }

        

        // imagenes de angular accesos
        if ( is_dir('dist/images/accesos') ) {
            if ( is_dir($imagesAngularJS.'/accesos') ) {
                // se recorren los archivos del origen y se reemplazan
                $gestor = opendir('dist/images/accesos');
                if ($gestor) {
                    $limpiarDir=true;
                    while (false !== ($entrada = readdir($gestor))) {
                        if ($entrada != "." && $entrada != "..") {
                            if( $limpiarDir ){
                                // se limpia el directorio de destino
                                $gestorDest = opendir( $imagesAngularJS.'/accesos' );
                                if ($gestorDest) {
                                    while (false !== ($entradaDest = readdir($gestorDest))) {
                                        if ($entradaDest != "." && $entradaDest != "..") {
                                            unlink( $imagesAngularJS.'/accesos'.'/'.$entradaDest );
                                        }
                                    }
                                }
                                $limpiarDir=false;
                            }
                            copy('dist/images/accesos/' . $entrada, $imagesAngularJS.'/accesos' . "/". $entrada);
                        }
                    }
                    closedir($gestor);
                }
            }
        }


        // controllers de laravel
        if (is_dir('php/controllers')) {
            if (is_dir($controladores)) {
                // se recorren los archivos del origen y se reemplazan
                $gestor = opendir('php/controllers');
                if ($gestor) {
                    while (false !== ($entrada = readdir($gestor))) {
                        if ($entrada != "." && $entrada != "..") {
                            copy('php/controllers/'. $entrada, $controladores . "/" . $entrada);
                        }
                    }
                    closedir($gestor);
                }
            }
        }

        // models de laravel
        if (is_dir('php/models')) {
            if (is_dir($modelos)) {
                // se recorren los archivos del origen y se reemplazan
                $gestor = opendir('php/models');
                if ($gestor) {
                    while (false !== ($entrada = readdir($gestor))) {
                        if ($entrada != "." && $entrada != "..") {
                            copy('php/models/'. $entrada, $modelos . "/" . $entrada);
                        }
                    }
                    closedir($gestor);
                }
            }
        }

        // views de laravel
        $dirViewPHP=array('excel', 'pdf');
        foreach( $dirViewPHP as $dirView ) {
            if (is_dir('php/views/'.$dirView)) {
                if (is_dir($vistas . "/".$dirView)) {
                    // se recorren los archivos del origen y se reemplazan
                    $gestor = opendir('php/views/'.$dirView);
                    if ($gestor) {
                        while (false !== ($entrada = readdir($gestor))) {
                            if ($entrada != "." && $entrada != "..") {
                                copy('php/views/'.$dirView.'/' . $entrada, $vistas . "/".$dirView."/" . $entrada);
                            }
                        }
                        closedir($gestor);
                    }
                }
            }
        }



        // stylos de la plantilla
        /*if (is_dir('dist/assets')) {
            if (is_dir($stylosPlantilla)) {
                // se recorren los archivos del origen y se reemplazan
                $gestor = opendir('dist/assets');
                if ($gestor) {
                    while (false !== ($entrada = readdir($gestor))) {
                        if ($entrada != "." && $entrada != "..") {
                            copy('dist/assets/'. $entrada, $stylosPlantilla . "/" . $entrada);
                        }
                    }
                    closedir($gestor);
                }
            }
        }


        if (is_dir('dist/assets/logos')) {
            if (is_dir($logosPlantilla)) {
                $limpiarDir=true;
                // se recorren los archivos del origen y se reemplazan
                $gestor = opendir('dist/assets/logos');
                if ($gestor) {
                    while (false !== ($entrada = readdir($gestor))) {
                        if ($entrada != "." && $entrada != "..") {
                            if( $limpiarDir ){
                                // se limpia el directorio de destino
                                $gestorDest = opendir( $logosPlantilla );
                                if ($gestorDest) {
                                    while (false !== ($entradaDest = readdir($gestorDest))) {
                                        if ($entradaDest != "." && $entradaDest != "..") {
                                            unlink( $logosPlantilla.'/'.$entradaDest );
                                        }
                                    }
                                }
                                $limpiarDir=false;
                            }
                            copy('dist/assets/logos/'. $entrada, $logosPlantilla . "/" . $entrada);
                        }
                    }
                    closedir($gestor);
                }
            }
        }*/



        // se reemplaza el archivo index.html por index.php
        if( file_exists('dist/index.html') ){
            copy('dist/index.html', $vistas.'/index.php');
        }


        // se reemplaza el archivo routes.php
        if( file_exists('php/routes.php') ){
            copy('php/routes.php', $directorioRaiz."/app/routes.php");
        }

        if( file_exists('php/config/constants.php') ){
            copy('php/config/constants.php', $directorioRaiz."/app/config/constants.php");
        }

        if( file_exists('php/libraries/funciones.php') ){
            copy('php/libraries/funciones.php', $directorioRaiz."/app/libraries/funciones.php");
        }

        /*if( file_exists('php/libraries/Sii.php') ){
            copy('php/libraries/Sii.php', $directorioRaiz."/app/libraries/Sii.php");
        }*/

        if( file_exists('php/estructura_empresa.sql') ){
            copy('php/estructura_empresa.sql', $publicRoot."/estructura_empresa.sql");
        }


        // archivos excel para importacion
        /*if( file_exists('php/ejmUploadLbrBoleta.csv') ){
            copy('php/ejmUploadLbrBoleta.csv', $publicRoot."/ejmUploadLbrBoleta.csv");
        }
        if( file_exists('php/ejmUploadLbrCmp.csv') ){
            copy('php/ejmUploadLbrCmp.csv', $publicRoot."/ejmUploadLbrCmp.csv");
        }
        if( file_exists('php/ejmUploadLbrVta.csv') ){
            copy('php/ejmUploadLbrVta.csv', $publicRoot."/ejmUploadLbrVta.csv");
        }
        if( file_exists('php/impBoletasHonorarios.xlsx') ){
            copy('php/impBoletasHonorarios.xlsx', $publicRoot."/impBoletasHonorarios.xlsx");
        }
        if( file_exists('php/impComprobantes.xlsx') ){
            copy('php/impComprobantes.xlsx', $publicRoot."/impComprobantes.xlsx");
        }*/

        if( file_exists('VERSIONAPP.dat') ){
            copy('VERSIONAPP.dat', $appRoot."/VERSIONAPP.dat");
        }

        // se borran las sesiones de usuario abiertas en el sistema
        /*
        $gestorDest = opendir( $appRoot.'/storage/sessions' );
        if ($gestorDest) {
            while (false !== ($entradaDest = readdir($gestorDest))) {
                if ($entradaDest != "." && $entradaDest != ".." && $entradaDest != '.gitignore') {
                    unlink( $appRoot.'/storage/sessions/'.$entradaDest );
                }
            }
        }
        */

        /*
            ACTUALIZACION DEL CONFIG PARA AGREGAR BASE DE DATOS GLOBAL
        */

        /*$configuracion = "
        'global' => array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'cmees_global',
            'username'  => 'cmees_global',
            'password'  => 'easy1q2w3e4r',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),
        'principal' => array(";

        $path = $appRoot."/config";
        $contenido = file_get_contents($path."/database.php");
        $contenido = str_replace("'principal' => array(", $configuracion, $contenido);

        // sobre-escribir archivo de configuraciones
        file_put_contents( $path."/database.php", $contenido );*/

        echo "ACTUALIZO SISTEMA : ".$sistema."<br/>";
    }
}


?>
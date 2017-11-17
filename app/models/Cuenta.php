<?php

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Cuenta extends Eloquent {
    
    protected $table = 'cuentas';
    
    static function listaCuentas1(){
        $datosConexionCME = array();
        $empresa = \Session::get('empresa');
        $isCME = $empresa->isCME;
        if($isCME){
            //$rutEmpresa = $empresa->rut;
            $rutEmpresa = 111111111;
            $client = new Client(); //GuzzleHttp\Client
            $result = $client->post('http://demo.cme-es.com/rest/rrhh/plan-de-cuentas', [
            //$result = $client->post('http://demo.cme-es.com/empresas', [
                'auth' => ['restfull', '1234'],
                'json' => [
                    'rutEmpresa' => $rutEmpresa
                ],
                'debug' => false
            ]);
            $datosConexionCME = $result->json(); 
        }
        
        return $datosConexionCME; 
    }


    static function listaCuentas(){
    	$listaCuentas = array();        
        $empresa = \Session::get('empresa');
        $isCME = $empresa->isCME;            
        $isSuccessCME = Empresa::isSuccessCME();            
    	
        if($isCME && $isSuccessCME){
            //$rutEmpresa = $empresa->rut;
            $rutEmpresa = 111111111;
            $client = new Client(); //GuzzleHttp\Client
            $result = $client->post('http://demo.cme-es.com/rest/rrhh/plan-de-cuentas', [
            //$result = $client->post('http://demo.cme-es.com/empresas', [
                'auth' => ['restfull', '1234'],
                'json' => [
                    'rutEmpresa' => $rutEmpresa
                ],
                'debug' => false
            ]);
            $resultado = $result->json(); 
            if($resultado['success']){
                $listaCuentas = $resultado['cuentas']; 
            }
        }else{
            $cuentas = Cuenta::orderBy('nombre', 'ASC')->get();
            if( $cuentas->count() ){
                foreach( $cuentas as $cuenta ){
                    $listaCuentas[]=array(
                        'id' => $cuenta->id,
                        'nombre' => $cuenta->nombre,
                        'codigo' => $cuenta->codigo,
                        'orden' => 1,
                        'nivel' => 1,
                        'cuenta' => true
                    );
                }
            }
        }
    	return $listaCuentas;
    }
    
    static function codigosCuentas(){
    	$codigosCuentas = array();
    	$cuentas = Cuenta::orderBy('nombre', 'ASC')->get();
    	if( $cuentas->count() ){
            foreach( $cuentas as $cuenta ){
                $codigosCuentas[]=array(
                    'codigo' => $cuenta->id,
                    'glosa' => $cuenta->nombre
                );
            }
    	}
    	return $codigosCuentas;
    }
        
    static function errores($datos){
         
        $rules = array(
            'nombre' => 'required'
        );

        $message = array(
            'cuenta.required' => 'Obligatorio!'
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
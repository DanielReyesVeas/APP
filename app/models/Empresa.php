<?php

class Empresa extends \Eloquent {
	protected $table = "empresas";
    protected $connection  = "principal";

    public function comuna(){
        return $this->belongsTo('Comuna', 'comuna_id');
    }
    
    public function comunaRepresentante(){
        return $this->belongsTo('Comuna', 'representante_comuna_id');
    }
    
    public function mutual(){
        return $this->belongsTo('Glosa', 'mutual_id');
    }
    
    public function caja(){
        return $this->belongsTo('Glosa', 'caja_id');
    }

    public function rut_formato(){
        return Funciones::formatear_rut($this->rut);
    }

    public function cuentasCorrientes(){
        return $this->hasMany('EmpresaCtaCte', 'empresa_id')->orderBy('id', 'ASC');
    }
    
    public function domicilio()
    {
        $direccion = $this->direccion;
        $comuna = $this->comuna->comuna;
        $provincia = $this->comuna->provincia->provincia;
        $domicilio = $direccion . ', comuna de ' . $comuna . ', de la ciudad de ' . $provincia;
        
        return $domicilio;
    }
    
    static function isCME()
    {        
        return true;
    }
    
    static function isMutual()
    {       
        $empresa = \Session::get('empresa');
        if($empresa->mutual_id==263){
            return false;
        }
        return true;
    }
    
    static function isCaja()
    {        
        $empresa = \Session::get('empresa');
        if($empresa->caja_id==257){
            return false;
        }
        return true;
    }
    
    static function isSuccessCME()
    {
        $sub = str_replace('rrhhes_', '', Config::get('cliente.CLIENTE.EMPRESA'));
        //$sub = 'demo';
        $url = 'http://' . $sub . '.cme-es.com//';
        if (@file_get_contents($url,false,NULL,0,1))
        {
            return true;
        }
        return false;
    }

    static function errores($datos){

        if($datos['id']){
            $rules =    array(
                'rut' => 'required|unique:empresas,rut,'.$datos['id'],
                'razon_social' => 'required|unique:empresas,razon_social,'.$datos['id']
            );
        }else{
            $rules =    array(
                'rut' => 'required|unique:empresas,rut',
                'razon_social' => 'required|unique:empresas,razon_social'
            );
        }

        $message =  array(
            'rut.required' => 'Obligatorio!',
            'representante_rut.required' => 'Obligatorio!',
            'representante_nombre.required' => 'Obligatorio!',
            'representante_direccion.required' => 'Obligatorio!',
            'representante_comuna_id.required' => 'Obligatorio!',
            'rut.unique' => 'El RUT ya se encuentra registrado!',
            'razon_social.required' => 'Obligatorio!',
            'razon_social.unique' => 'Ya se encuentra registrada!'
        );

        $verifier = App::make('validation.presence');
        $verifier->setConnection("principal");

        $validation = Validator::make($datos, $rules, $message);
        $validation->setPresenceVerifier($verifier);

        if($validation->fails()){
            // la validacion tubo errores
            return $validation->getMessageBag()->toArray();
        }else{
            // no hubo errores de validacion
            return false;
        }
    }
}
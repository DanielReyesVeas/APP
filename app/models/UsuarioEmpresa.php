<?php

class UsuarioEmpresa extends Eloquent {
    
    protected $table = 'usuarios_empresa';
    protected $connection = "principal";
    
    function trabajador(){
    	return $this->belongsTo('Trabajador', 'trabajador_id');
    }
    
    function empresa(){
    	return $this->belongsTo('Empresa', 'empresa_id');
    }

    
}
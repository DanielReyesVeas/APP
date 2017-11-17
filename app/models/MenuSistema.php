<?php
use Symfony\Component\Console\Tests\Descriptor\ObjectsProvider;
class MenuSistema extends Eloquent {
 
    protected $table = 'menu';
    protected $connection = "principal";

    static function obtenerPermisosAccesosURL($user, $url){
        $accesos=array();
        $abierto = AnioRemuneracion::isMesAbierto();
        
        if(Auth::user()->id > 1){
            $menuOpc = MenuSistema::where('href', $url)->get();
            if ($menuOpc->count()) {
                foreach($menuOpc as $menu){
                    if($user->perfil_id){
                        $usuarioMenu = PerfilDetalle::where('perfil_id', $user->perfil_id)->where('menu_id', $menu->id)->first();
                    }else{
                        $usuarioMenu = UsuarioPerfilDetalle::where('usuario_id', $user->id)->where('menu_id', $menu->id)->first();
                    }
                    if($usuarioMenu){
                        $accesos = array(
                            'ver' => $usuarioMenu->ver ? true : false,
                            'editar' => $usuarioMenu->editar ? true : false,
                            'crear' => $usuarioMenu->crear ? true : false,
                            'eliminar' => $usuarioMenu->eliminar ? true : false,
                            'abierto' => $abierto
                        );
                    }
                }
            }
        }else{
            $accesos=array(
                'ver' => true,
                'editar' => true,
                'crear' => true,
                'eliminar' => true,
                'abierto' => true
            );
        }
        return $accesos;
    }

    public function permisos(){
        return $this->hasMany('MenuSistemaPermiso', 'menu_id');
    }

    public function listaPermisos(){
        $lista=array();
        if( $this->permisos->count() ){
            foreach( $this->permisos as $permiso ){
                $lista[ $permiso->opcion ] = true;
            }
        }
        return $lista;
    }
 
    public function padre()
    {
        return $this->belongsTo('Menu', 'padre_id');
    }
 
    public function hijos()
    {
        return $this->hasMany('Menu', 'padre_id');
    }
    
    static public function estructura_menu($padre=0){

        if($padre==0){
            $menu = MenuSistema::where('padre_id','=', $padre)->orderBy('posicion', 'ASC')->get();
        }else{
            $menu = MenuSistema::where('padre_id','=', $padre)->orderBy('menu', 'ASC')->get();            
        }
        
        
        $lista=array();
        if($menu->count()){
            foreach($menu as $item)
            {
                $lista[] = array(
                    'id' => $item->id,
                    'datos' => $item,
                    'hijos' => MenuSistema::estructura_menu($item->id)
                );
            }
        }
        return $lista;
    }
    
    /*public function departamento(){
        $departamentos = Config::get('constants.departamentos');
        if( $this->departamento_id == 0 ){
            return "GENERICO";
        }else{
            if( array_key_exists($this->departamento_id, $departamentos) ){
                return $departamentos[ $this->departamento_id ];
            }
        }
        return "NO DEFINIDO";
    }*/
    
    static public function reordenar($padre, $nuevo, $origen, $id){
        
        if($origen <= $nuevo){
            // desplazamiento hacia abajo
            DB::connection('principal')->table('menu')
            ->where('padre_id', $padre)
            ->where('posicion', '>', $origen)
            ->where('posicion', '<', $nuevo)
            ->where('id', '!=', $id)
            ->decrement('posicion');
        }else{
            // desplazamiento hacia arriba
            DB::connection('principal')->table('menu')
            ->where('padre_id', $padre)
            ->where('posicion', '>=', $nuevo)
            ->where('posicion', '<', $origen)
            ->where('id', '!=', $id)
            ->increment('posicion');
        }
    }
    
    static function errores($datos){
        $rules = array(
			'nombre' => 'required',
			'titulo' => 'required'
     	);

        $message =  array(
			'nombre.required' => 'Obligatorio!',
			'titulo.required' => 'Obligatorio!'
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
    
    static public function get_all_padres(){
    	$storoge =array();
        $menu = MenuSistema::where('tipo', 1)->orderBy('posicion', 'ASC')->get();
        if($menu->count()){
            foreach($menu as $item){
            	$storoge[] = array(
            		'id' => $item->id,
                    'nivel' => 0,
            		'value' => $item->menu
            	);
                $subMenu = MenuSistema::where('padre_id', $item->id)->where('tipo', 3)->orderBy('posicion', 'ASC')->get();
                if( $subMenu->count() ){
                    foreach( $subMenu as $sub ){
                        $storoge[] = array(
                            'id' => $sub->id,
                            'value' => $sub->menu,
                            'nivel' => 1
                        );
                    }
                }
            }
        }
        return $storoge;
    }
}
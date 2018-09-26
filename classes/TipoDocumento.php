<?php
require_once(__DIR__.'/traits/from_std_object_or_array.php');

class TipoDocumento {
    use from_std_object_or_array;
    public $id;
    public $nombre;
    public $descripcion;
    /**
     * Retorna los tipos de documento existentes 
     * @return array<TipoDocumento> Tipo de documentos existentes en el sistema
     */
    public static function get_tipos_documento(){
        global $DB;
        $tipos_documento_array = $DB->get_records('talentospilos_tipo_documento');

        $tipos_documento = array();
        foreach($tipos_documento_array as  $tipos_documento_array) {
            
            $tipo_documento = new TipoDocumento();
            $tipo_documento->from_std_object_or_array($tipos_documento_array);
          
            array_push($tipos_documento, $tipo_documento);
        }
        
        return $tipos_documento;
    }
    /**
     * Obtener los tipos de documento con una descripcion legible
     * @return array Array donde las llaves son los id de los tipos de documento y el valor es el nombre del municipio
     */
    public static function getOptions() {
        $tipos_documento = TipoDocumento::get_tipos_documento();
        $opciones = array();
        foreach($tipos_documento as $tipo_documento) {
            $opciones[$tipo_documento->id] = $tipo_documento->descripcion;
        }
        return $opciones;
    }
}

?>
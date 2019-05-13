<?php

class usuario {
    const TIPO_DOCUMENTO = 'tipo_doc';
    const TIPO_DOCUMENTO_INICIAL = 'tipo_doc_ini';
    const ID_CIUDAD_INICIAL = 'id_ciudad_ini';
    const ID_CIUDAD_RESIDENCIA = 'id_ciudad_res';
    const FECHA_NACIMIENTO = 'fecha_nac';
    const ID_CIUDAD_NACIMIENTO = 'id_ciudad_nac';
    const SEXO = 'sexo';
    const ESTADO = 'estado';
    const ID_DISCAPACIDAD = 'id_discapacidad';
    const AYUDA_DISCAPACIDAD = 'ayuda_disc';
    const ESTADO_ASES = 'estado_ases';
    const NUMERO_DOCUMENTO = 'num_doc';
    const NUMERO_DOCUMENTO_INICIAL = 'num_doc_ini';
    const ID = 'id';
    public $tipo_doc_ini = -1;
    public $tipo_doc;
    public $num_doc;
    public $num_doc_ini;
    public $id_ciudad_ini;
    public $id_ciudad_res;
    public $fecha_nac;
    public $id_ciudad_nac; // see Municipio
    public $sexo; // see Gender
    /**
     * Deprecated, is replaced with estado_icetex, estado_programa and tracking_status
     *
     * Is no longer used after 01/01/2017
     *
     * The default value
     * @var string
     */
    public $estado;
    public $id_discapacidad;
    public $ayuda_disc;
    public $estado_ases;// see EstadoAses
    public $dir_ini; //Dirección inicial
    public $direccion_res; //Dirección residencia
    public $celular;
    public $emailpilos;
    public $acudiente;
    public $observacion;
    /**
     * @var $id_cond_excepcion int
     */
    public $id_cond_excepcion;
    public $colegio;
    public $barrio_ini; //Barrio procedencia
    public $barrio_res; //Barrio residencia
    public $id;
    public $tel_acudiente;
    public $tel_ini; // Telefono procedencia
    public $tel_res; // Telefono residencia;
    public $estamento; // Tipo colegio
    public $grupo;

}

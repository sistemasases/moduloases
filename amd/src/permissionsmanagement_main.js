// Standard license block omitted.
/*
 * @package    block_ases/permissionsmanagement
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/permissionsmanagement_main
 */
define(['jquery', 'block_ases/bootstrap', 'block_ases/datatables.net', 'block_ases/datatables.net-buttons', 'block_ases/buttons.flash', 'block_ases/jszip', 'block_ases/pdfmake', 'block_ases/buttons.html5', 'block_ases/buttons.print', 'block_ases/sweetalert', 'block_ases/select2'], function($,bootstrap, datatablesnet, datatablesnetbuttons, buttonsflash, jszip, pdfmake, buttonshtml5, buttonsprint, sweetalert, select2) {


    return {

        init: function() {
            console.log("a");


            $("#profiles_user").select2({

                language: {

                    noResults: function() {

                        return "No hay resultado";
                    },
                    searching: function() {

                        return "Buscando..";
                    }
                },
                width: '36%',
                dropdownAutoWidth: true,
                placeholder: "Seleccionar perfil"
            });
            $("#profiles_prof").select2({

                language: {

                    noResults: function() {

                        return "No hay resultado";
                    },
                    searching: function() {

                        return "Buscando..";
                    }
                },
                width: '40%',
                dropdownAutoWidth: true,
            });

            $("#actions").select2({
                dropdownAutoWidth: true,
                width: '100%',
                multiple: true,
                language: {

                    noResults: function() {

                        return "No hay resultado";
                    },
                    searching: function() {

                        return "Buscando..";
                    }
                },
                dropdownAutoWidth: true,
            });


            $(document).ready(function() {
                //Cargar los datos de los roles creados en el select de rol-cmb
                var informacionUrl = window.location.search.split("&");
                for (var i = 0; i < informacionUrl.length; i++) {
                    var elemento = informacionUrl[i].split("=");
                    if (elemento[0] == "?instanceid" || elemento[0] == "instanceid") {
                        var instance = elemento[1];
                    }
                }
                load_actions();
                load_functions();
                load_roles();

                $("#add_accion").on('click', function() {
                    crearAccion();
                    load_actions();
                });

                $("#add_function").on('click', function() {
                    crearFuncion();
                    load_functions();
                });

                $("#add_profile").on('click', function() {
                    crearPerfil();
                    load_roles();
                });


                $("#assign_user_profile").on('click', function() {
                    asignarUsuarioPerfil(instance);
                });


            });

            /**
             * Crea un ajax para llamar la accion que guarda la nueva accion en la base de datos
             * @param Nombre 
             * @param Descripcion
             * @author Edgar Mauricio Ceron
             */

            function crearAccion() {
                var nombre = $("#nombre").val().trim();
                var descripcion = $("#descripcion").val().trim();
                var id_funcionalidad = $("#functions").val();
                var msj = "";

                if (nombre.length == 0) {
                    msj = "Nombre no puede ser nulo";
                }
                if (descripcion.length == 0) {
                    msj = msj + "\nDescripcion no puede ser nulo";
                }

                if (id_funcionalidad.length == 0) {
                    msj = msj + "\nFuncionalidad no puede ser nulo";
                }

                if (nombre.length == 0 || descripcion.length == 0) {
                    alert(msj);
                } else {
                    $.ajax({
                        type: "POST",
                        data: {
                            nombre: nombre,
                            descripcion: descripcion,
                            id_funcionalidad: id_funcionalidad
                        },
                        url: "../managers/ActionCreateAction.php",
                        async: false,
                        success: function(msg) {
                            $("#formAction")[0].reset();
                            alert(msg);
                        }
                    });
                }
            }

            /**
             * Crea un ajax para llamar la accion que guarda la nueva función en la base de datos
             * @param Nombre 
             * @param Descripcion
             */

            function crearFuncion() {
                var nombre = $("#nombre_funcionalidad").val().trim();
                var descripcion = $("#descripcion_funcionalidad").val().trim();
                var msj = "";

                if (nombre.length == 0) {
                    msj = "Nombre no puede ser nulo";
                }
                if (descripcion.length == 0) {
                    msj = msj + "\nDescripcion no puede ser nulo";
                }

                if (nombre.length == 0 || descripcion.length == 0) {
                    alert(msj);
                } else {
                    $.ajax({
                        type: "POST",
                        data: {
                            nombre_funcionalidad: nombre,
                            descripcion_funcionalidad: descripcion
                        },
                        url: "../managers/ActionCreateAction.php",
                        success: function(msg) {
                            $("#formFuncion")[0].reset();
                            alert(msg);
                        },
                    });
                }
            }



            /**
             * Crea un ajax para llamar la accion que guarda el nuevo perfil en la base de datos
             * @param Nombre 
             * @param Descripcion
             */

            function crearPerfil() {
                var nombre = $("#nombre_perfil").val().trim();
                var descripcion = $("#descripcion_perfil").val().trim();
                var msj = "";

                if (nombre.length == 0) {
                    msj = "Nombre del perfil no puede ser nulo";
                }
                if (descripcion.length == 0) {
                    msj = msj + "\nDescripcion del perfil no puede ser nulo";
                }

                if (nombre.length == 0 || descripcion.length == 0) {
                    alert(msj);
                } else {
                    $.ajax({
                        type: "POST",
                        data: {
                            nombre_perfil: nombre,
                            descripcion_perfil: descripcion
                        },
                        url: "../managers/ActionCreateAction.php",
                        async: false,
                        success: function(msg) {
                            $("#formPerfil")[0].reset();
                            alert(msg);
                        }
                    });
                }
            }

            /**
             * Crea un ajax que asigna determinadas acciones a un pefil en la base de datos
             * @param perfil 
             * @param array acciones
             */
            function asignarAccionPerfil() {
                var id_profile = $("#profiles_prof").val();
                var id_actions = $("#actions").val();
                var msj = "";

                if (id_profile.length == 0) {
                    msj = "Escoger un perfil";
                }
                if (id_actions.length == 0) {
                    msj = msj + "\n Escoger acciones";
                }

                if (id_profile.length == 0 || id_actions.length == 0) {
                    alert(msj);
                } else {
                    $.ajax({
                        type: "POST",
                        data: {
                            profile: id_profile,
                            actions: id_actions
                        },
                        url: "../managers/ActionCreateAction.php",
                        async: false,
                        success: function(msg) {
                            alert(msg);
                        }
                    });
                }
            }

            /**
             * Crea un ajax que asigna un usuario a un pefil en la base de datos
             * @param perfil 
             * @param array acciones
             */
            function asignarUsuarioPerfil(instance) {
                var id_profile = $("#profiles_user").val();
                var actions = [];
                var acciones = $("input[name='actions[]']:checked").each(function() {
                    actions.push($(this).val());
                });
                var actions_array = JSON.stringify(actions);
                if (id_profile == "") {
                    swal({
                        title: "Error",
                        text: "Seleccione el rol del usuario",
                        type: "error",
                        confirmButtonColor: "#d51b23"
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        data: {
                            profile: id_profile,
                            actions: actions_array,
                            function: "assign_role"
                        },
                        url: "../managers/ActionCreateAction.php",
                        async: false,
                        success: function(msg) {
                            alert(msg);
                        }
                    });
                }
            }


            $("input[name='actions[]']").change(function() {
                $(this).attr('checked', true);

            });


            $("#profiles_user").change(function() {

                var user = $("#profiles_user").val();
                var source = "permissions_management";

                $.ajax({
                    type: "POST",
                    data: {
                        user: user,
                        source: source
                    },
                    url: "../managers/permissions_management/permissions_report.php",
                    success: function(msg) {
                        $("input[name='actions[]'").prop('checked', false);

                        $.each(msg, function(index, value) {
                            $("input[value='" + value + "']").prop('checked', true);
                        });
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al cargar gestion de permisos y roles")
                    },
                });

            });



            //editar
            $('body').on('click', 'tbody tr td .red.glyphicon.glyphicon-pencil', function() {
                var nombre_table = $(this).parents().eq(3).attr('id');
                var table = $("#" + nombre_table).DataTable();
                var nombre = table.cell(table.row($(this).parent()).index(), 0).data();
                var descripcion = table.cell(table.row($(this).parent()).index(), 1).data();
                var funcionalidad = table.cell(table.row($(this).parent()).index(), 2).data();

                $('#nombre_editar').val(nombre);
                $('#descripcion_editar').val(descripcion);


                if (nombre_table == 'tableActions') {
                    $(".form-pilos.func").removeClass('hide');
                    $("#save_seg").attr("name", this.id + "_accion");
                    console.log(funcionalidad);
                    //  $("#functions_table").val(funcionalidad).change();
                    $("#functions_table option").filter(function() {
                        //may want to use $.trim in here
                        return $(this).text() == funcionalidad;
                    }).prop('selected', true);


                } else if (nombre_table == 'tableFunctions') {
                    $("#save_seg").attr("name", this.id + "_funcionalidad");
                    $(".form-pilos.func").addClass('hide');

                } else {
                    $("#save_seg").attr("name", this.id + "_rol");
                    $(".form-pilos.func").addClass('hide');

                }
            });

            //modificar
            $(document).on('click', '#save_seg', function() {
                var texto = this.name.split("_");
                var id = texto[0];
                var table = texto[1];
                var nombre = $("#nombre_editar").val();
                var descripcion = $("#descripcion_editar").val();
                var funcionalidad = "";
                if (table == 'accion') {
                    funcionalidad = $("#functions_table").val();
                }

                $.ajax({
                    type: "POST",
                    data: {
                        id: id,
                        table: table,
                        source: "modify_register",
                        nombre: nombre,
                        descripcion: descripcion,
                        funcionalidad: funcionalidad
                    },
                    url: "../managers/permissions_management/permissions_report.php",
                    success: function(msg) {
                        swal({
                            title: msg.title,
                            html: true,
                            text: msg.text,
                            type: msg.type,
                            confirmButtonColor: "#d51b23"
                        });

                        load_actions();
                        load_functions();
                        load_roles();


                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        swal({
                            title: "Error",
                            html: true,
                            text: "Se presento un inconveniente al modificar registro",
                            type: "error",
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });



            });




            //eliminar
            $('#div_actions').on('click', '#delete_action', function() {

                var table = $("#div_actions #tableActions").DataTable();
                var td = $(this).parent();
                var childrenid = $(this).children('span').attr('id');
                console.log(this);
                var colIndex = table.cell(td).index().column;

                var nombre = table.cell(table.row(td).index(), 0).data();
                var descripcion = table.cell(table.row(td).index(), 1).data();
                swal({
                        title: "Estas seguro/a?",
                        text: "La acción <strong>" + nombre + "</strong> se eliminará",
                        type: "warning",
                        html: true,
                        showCancelButton: true,
                        confirmButtonColor: "#d51b23",
                        confirmButtonText: "Si!",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                    },
                    function(isConfirm) {
                        if (isConfirm) {

                            delete_record(childrenid, "accion");
                            load_actions();


                        }
                    }
                );

            });


            function delete_record(id, source) {
                $.ajax({
                    type: "POST",
                    data: {
                        id: id,
                        source: "delete_record",
                        type: source,
                    },
                    url: "../managers/permissions_management/permissions_report.php",
                    success: function(msg) {
                        swal({
                            title: msg.title,
                            html: true,
                            text: msg.text,
                            type: msg.type,
                            confirmButtonColor: "#d51b23"
                        });

                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al eliminar registro")
                    },
                });


            }



            function load_functions() {
                $.ajax({
                    type: "POST",
                    url: "../managers/permissions_management/load_function.php",
                    success: function(msg) {
                        $("#div_functions").empty();
                        $("#div_functions").append('<table id="tableFunctions"  class="display" cellspacing="0" width="100%" ><thead><thead></table>');
                        var table = $("#tableFunctions").DataTable(msg);
                        $('#div_functions #modify_function').css('cursor', 'pointer');


                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al cargar funcionalidades")
                    },
                })
            }


            function load_actions() {
                $.ajax({
                    type: "POST",
                    url: "../managers/permissions_management/load_actions.php",
                    success: function(msg) {
                        $("#div_actions").empty();
                        $("#div_actions").append('<table id="tableActions"  class="display" cellspacing="0" width="100%" ><thead><thead></table>');
                        var table = $("#tableActions").DataTable(msg);
                        $('#div_actions #delete_action').css('cursor', 'pointer');
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al cargar acciones")
                    },
                })
            }

            function load_roles() {
                $.ajax({
                    type: "POST",
                    url: "../managers/permissions_management/load_profiles.php",
                    success: function(msg) {
                        $("#div_profiles").empty();
                        $("#div_profiles").append('<table id="tableProfiles"  class="display" cellspacing="0" width="100%" ><thead><thead></table>');
                        var table = $("#tableProfiles").DataTable(msg);
                        $('#div_profiles #delete_profiles').css('cursor', 'pointer');
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al cargar roles")
                    },
                })
            }




        }
    };
});
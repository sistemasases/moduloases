 /**
  * @module block_ases/academic_reports
  */

  define(['jquery', 'block_ases/bootstrap', 'block_ases/datatables.net', 'block_ases/datatables.net-buttons', 'block_ases/buttons.flash', 'block_ases/jszip', 'block_ases/pdfmake', 'block_ases/buttons.html5', 'block_ases/buttons.print', 'block_ases/sweetalert2','block_ases/select2', 'block_ases/jqueryui'], function($, bootstrap, datatablesnet, datatablesnetbuttons, buttonsflash, jszip, pdfmake, buttonshtml5, buttonsprint, sweetalert2, select2, jqueryui) {
    
        return {
    
            init: function() {

                $(document).ready(function(){
                    
                    $("#students").DataTable();
                    $("#courses").DataTable();

                });
                

                $(document).on('click', '#students tbody tr td', function() {
                    var pagina = "student_profile.php";
                    var table = $("#students").DataTable();
                    var colIndex = table.cell(this).index().column;
                    var student_code = table.cell(table.row(this).index(), 0).data();
                    var username = $(this).attr('id');                           
                    // if (colIndex <= 2) {
                    //     $("#formulario").each(function() {
                    //         this.reset;
                    //     });
                    //     // document.getElementById("formulario").reset();
                    //     location.href = pagina + location.search + "&student_code=" + student_code;
                    // }COMENTADO MIENTRAS ESTA LA FICHA ACADEMICA
                    
                    if(colIndex == 3){
                        
                        $.ajax({
                            type: "POST",
                            data: {
                                student: username,
                                type: "load_loses"
                            },
                            url: "../managers/general_reports/academic_reports_processing.php",
                            success: function(msg) {
                                //alert(msg);
                                swal({ 
                                    title: "Notas Perdidas",
                                    type: "info", 
                                    text: msg,
                                    showCancelButton: false,
                                    confirmButtonColor: "#DD6B55", 
                                    confirmButtonText: "Cerrar", 
                                    closeOnConfirm: true })
                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                console.log(msg);
                            },
                        });
                        
                    }


                });

                $(document).on('click', '.curso_reporte', function() {
                    var course_id = $(this).attr('id');
                    var url = 'report_grade_book.php' + location.search + '&id_course=' + course_id;
                    window.open(url, '_blank');  
                });
                
            }
            
        }
    });
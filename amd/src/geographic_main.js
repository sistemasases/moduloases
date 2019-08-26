 /**
 * Load and save geographic information
 * @module amd/src/geographic_main
 * @author Jhonier Andrés Calero Rodas
 * @author Jorge Eduardo Mayor Fernández
 * @copyright  2018 Jhonier A. Calero <jhonier.calero@correounivalle.edu.co>
 * @copyright  2019 Jorge Eduardo Mayor <mayor.jorge@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */ 

define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui'], function($, bootstrap, sweetalert, jqueryui) {

    return {

        init: function(){
            
            var id_ases = $('#id_ases').val();
            var student_marker;

            /**
             * Executes the method search_direction() by unfocusing the address text area.
             */
            $("#geographic_direccion").focusout(function(){
                search_direction();
            });

            /**
             * Executes the method search_direction() by pressing the enter key.
             */
            $("#geographic_direccion").keypress(function(event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if (keycode == '13') {
                    search_direction();
                }
            });

            $("#select_neighborhood").select2({

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

            $("#geographic_ciudad").select2({

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

            $('#button_edit_geographic').on('click', function(){
                $('#button_edit_geographic').attr('hidden', true);
                $('#div_save_buttons').removeAttr('hidden');
                $('#select_neighborhood').removeAttr('disabled');
                $('#latitude').removeAttr('disabled');
                $('#longitude').removeAttr('disabled');
                $('#geographic_direccion').removeAttr('disabled');
                $('#geographic_ciudad').removeAttr('disabled');
                $('#geographic_checkbox_vive_lejos').removeAttr('disabled');
                $('#geographic_checkbox_zona_riesgo').removeAttr('disabled');
                $('#nivel_bajo').removeAttr('disabled');
                $('#nivel_medio').removeAttr('disabled');
                $('#nivel_alto').removeAttr('disabled');
                $('#geographic_text_area').removeAttr('disabled');
                $('#nativo').removeAttr("disabled");

                if($('#geographic_checkbox_zona_riesgo').prop("checked"))
                    $('#nativo').removeAttr("disabled");

                var latitude = $('#latitude').val();
                var longitude = $('#longitude').val();
                student_marker = edit_map(latitude, longitude);
            });

            $('#button_cancel_geographic').on('click', function(){
                $('#button_edit_geographic').removeAttr('hidden');
                $('#div_save_buttons').attr('hidden', true);
                $('#select_neighborhood').attr('disabled', true);
                $('#latitude').attr('disabled', true);
                $('#longitude').attr('disabled', true);
                $('#geographic_direccion').attr('disabled', true);
                $('#geographic_ciudad').attr('disabled', true);
                $('#geographic_checkbox_vive_lejos').attr('disabled', true);
                $('#geographic_checkbox_zona_riesgo').attr('disabled', true);
                $('#nivel_bajo').attr('disabled', true);
                $('#nivel_medio').attr('disabled', true);
                $('#nivel_alto').attr('disabled', true);
                $('#inmigrante').attr('disabled', true);
                $('#geographic_text_area').attr('disabled', true);
                $('#nativo').attr("disabled", true);

                var ciudad_est = document.getElementById('geographic_ciudad').value;
                var latitude = $('#latitude').val();
                var longitude = $('#longitude').val();

                if (ciudad_est == 1079) {

                    document.getElementById('mapa').innerHTML = "<iframe class='col-xs-12 col-sm-12 col-md-12 col-lg-12' height='396' frameborder='0' style='border:0' src='https://www.google.com/maps/embed/v1/directions?key=AIzaSyAoE-aPVfruphY4V4BbE8Gdwi93x-5tBTM&origin=" + latitude + "," + longitude + "&destination=3.3759493,-76.5355789&mode=transit'></iframe>";

                } else {

                    document.getElementById('mapa').innerHTML = "<iframe class='col-xs-12 col-sm-12 col-md-12 col-lg-12' height='396' frameborder='0' style='border:0' src='https://www.google.com/maps/embed/v1/directions?key=AIzaSyAoE-aPVfruphY4V4BbE8Gdwi93x-5tBTM&origin=" + latitude + "," + longitude + "&destination=3.3759493,-76.5355789&mode=driving'></iframe>";
                }
            });

            $('#button_save_geographic').on('click', function(){
                swal({
                    title: "Atención",
                    text: "¿Está seguro que la ubicación del marcador en el mapa coincide con la dirección del estudiante?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Sí",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        var latitude = $('#latitude').val();
                        var longitude = $('#longitude').val();
                        var address = $('#geographic_direccion').val();
                        var city = $('#geographic_ciudad').val();
                        var duration = 0;
                        var distance = 0;
                        var mode;

                        var ciudad = document.getElementById("geographic_ciudad");
                        var selectedCity = ciudad.options[ciudad.selectedIndex].text;
                        var query = address + " " + selectedCity + " Colombia";

                        document.getElementById('direccion_res').value = address;
                        document.getElementById('municipio_act').value = selectedCity;

                        var request = {
                            query: query,
                            fields: ['photos', 'formatted_address', 'name', 'rating', 'opening_hours', 'geometry'],
                        };

                        var destination;
                        var map = document.getElementById('mapa');
                        var directionsService = new google.maps.DirectionsService();
                        service = new google.maps.places.PlacesService(map);
                        service.findPlaceFromQuery(request, callback);

                        function callback(results) {

                            if (results != null) {
                                destination = results[0];
                            }
                        }

                        /**
                         * Sets the latitude and longitude due to the changes on the
                         * google map.
                         */
                        if (destination != null) {
                            latitude = destination.geometry.location.lat();
                            longitude = destination.geometry.location.lng();
                        } else {
                            latitude = student_marker.getPosition().lat();
                            longitude = student_marker.getPosition().lng();
                        }

                        document.getElementById('latitude').value = latitude;
                        document.getElementById('longitude').value = longitude;

                        var latLng_estudiante = new google.maps.LatLng(latitude, longitude);
                        var latLng_univalle = new google.maps.LatLng(3.3759493, -76.5355789);

                        /**
                         * Repaints the map after editing the student's marker.
                         * DO NOT delete the variable 'mapa'.
                         */
                        if(city == 1079){
                            var mapa = document.getElementById('mapa').outerHTML;
                            document.getElementById('geographic_map').innerHTML = mapa;
                            document.getElementById('mapa').innerHTML = "<iframe class='col-xs-12 col-sm-12 col-md-12 col-lg-12' height='396' frameborder='0' style='border:0' src='https://www.google.com/maps/embed/v1/directions?key=AIzaSyAoE-aPVfruphY4V4BbE8Gdwi93x-5tBTM&origin=" + latitude + "," + longitude + "&destination=3.3759493,-76.5355789&mode=transit'></iframe>";
                            mode='TRANSIT';
                        } else {
                            var mapa = document.getElementById('mapa').outerHTML;
                            document.getElementById('geographic_map').innerHTML = mapa;
                            document.getElementById('mapa').innerHTML = "<iframe class='col-xs-12 col-sm-12 col-md-12 col-lg-12' height='396' frameborder='0' style='border:0' src='https://www.google.com/maps/embed/v1/directions?key=AIzaSyAoE-aPVfruphY4V4BbE8Gdwi93x-5tBTM&origin=" + latitude + "," + longitude + "&destination=3.3759493,-76.5355789&mode=driving' allowfullscreen></iframe>";
                            mode = 'DRIVING';
                        }

                        //Instancies a route.
                        var route_request = {
                            origin: latLng_estudiante,
                            destination: latLng_univalle,
                            travelMode: mode
                        };

                        //Calculates the distance and the duration due to the specified route.
                        directionsService.route(route_request, function(response, status) {

                            var nivel_riesgo = $('input[name=geographic_nivel_riesgo]:checked').val();
                            var neighborhood = $('#select_neighborhood').val();

                            if (city == 1){
                                swal(
                                    "Error",
                                    "Debe definir la ciudad del estudiante antes de guardar",
                                    "error");
                            } else if(city == 1079 && neighborhood == 'No registra'){
                                swal(
                                    "Error",
                                    "Debe definir un barrio de residencia antes de guardar",
                                    "error");
                            } else if (nivel_riesgo == null){
                                swal(
                                    "Error",
                                    "Debe definir un nivel de riesgo antes de guardar",
                                    "error");
                            } else {

                                var legs = response.routes[0].legs[0];

                                distance = legs.distance.value;
                                duration = legs.duration.value;

                                save_geographic_info(id_ases, latitude, longitude, duration, distance, address, neighborhood, city, nivel_riesgo);
                            }
                        });
                    }
                });
            });

            /**
             * @method search_direction
             * @desc Searches the specified address in '#geographic_direccion' and sets the stundent's marker there.
             * @return {void}
             */
            function search_direction () {
                var geocoder = new google.maps.Geocoder();
                var select = document.getElementById('geographic_ciudad');
                var input_address = $('#geographic_direccion').val();
                var composed_address = input_address + " " + select.options[select.selectedIndex].text + " " + "Colombia";
                geocoder.geocode({
                    "address": composed_address
                }, function(results) {
                    if (results[0]) {
                        var latitud = results[0].geometry.location.lat();
                        var longitud = results[0].geometry.location.lng();
                        student_marker = edit_map(latitud, longitud);
                        document.getElementById('latitude').value = latitud;
                        document.getElementById('longitude').value = longitud;
                    } else {
                        console.log("No se encontraron resultados");
                    }
                });
            }

            /**
             * @method edit_map
             * @desc Makes the map editable and sets the student's marker due to a latitude and a longitude
             * @param latitude
             * @param longitude
             * @return {object} Student's marker
             */
            function edit_map(latitude, longitude){
                document.getElementById('mapa').innerHTML = "";

                var geocoder;

                var latLng_univalle = new google.maps.LatLng(3.3759493, -76.5355789);
                var opciones = {
                    center: latLng_univalle,
                    zoom: 14
                };

                var map = new google.maps.Map(document.getElementById('mapa'), opciones);

                var initial_marker = new google.maps.Marker({
                    position: latLng_univalle,
                    map: map,
                    title: 'Universidad del Valle'
                });

                var new_infowindow = new google.maps.InfoWindow();
                new_infowindow.setContent("Universidad del Valle");
                new_infowindow.open(map, initial_marker);

                var marker = new google.maps.Marker({
                    position: {
                        lat: parseFloat(latitude),
                        lng: parseFloat(longitude)
                    },
                    map: map
                });

                geocoder = new google.maps.Geocoder();

                var infowindow = new google.maps.InfoWindow();
                infowindow.setContent("Residencia Estudiante");
                infowindow.open(map, marker);

                google.maps.event.addListener(map, 'click', function (event) {
                    geocoder.geocode({
                            'latLng': event.latLng
                        },
                        function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                if (results[0]) {
                                    if (marker) {
                                        marker.setPosition(event.latLng);
                                    } else {
                                        marker = new google.maps.Marker({
                                            position: event.latLng,
                                            map: map
                                        });
                                    }
                                    infowindow.setContent(results[0].formatted_address + '<br/> Coordenadas: ' + results[0].geometry.location);
                                    infowindow.open(map, marker);
                                } else {
                                    console.log("No se encontraron resultados");
                                }
                            } else {
                                console.log("Geocodificación ha fallado debido a: " + status);
                            }
                        });
                });
                return marker;
            }
            /**
             * @method save_geographic_info
             * @desc Saves a student geographic information. Current processing on geographic_serverproc.php
             * @param {int} id_ases ASES student id
             * @param {float} latitude latitude coordenate
             * @param {float} longitude longitude coordenate
             * @param {int} duration
             * @param {int} distance
             * @param {int} address current student's residencial address
             * @param {int} neighborhood student's residencial neighborhood
             * @param {int} city current student's residencial city
             * @param {int} nivel_riesgo student's geographic risk level
             * @return {void}
             */
            function save_geographic_info(id_ases, latitude, longitude, duration, distance, address, neighborhood, city, nivel_riesgo){

                var observaciones = $('#geographic_text_area').val();
                var vive_lejos = $('#geographic_checkbox_vive_lejos').prop("checked");
                var vive_zona_riesgo = $('#geographic_checkbox_zona_riesgo').prop("checked");
                var nativo = $('#nativo').prop("checked");

                $.ajax({
                    type: "POST",
                    data: JSON.stringify({
                        "func": 'save_geographic_info',
                        "params": [id_ases, latitude, longitude, neighborhood,
                                    duration, distance, address, city,
                                    observaciones, vive_lejos, vive_zona_riesgo,
                                    nativo, nivel_riesgo],
                    }),
                    url: "../managers/student_profile/geographic_api.php",
                    success: function(msg) {
                        if(msg.status_code == 0) {
                            swal(
                                msg.title,
                                msg.message,
                                msg.type);
                        } else {
                            console.log(msg);
                        }

                        $('#button_edit_geographic').removeAttr('hidden');
                        $('#div_save_buttons').attr('hidden', true);
                        $('#select_neighborhood').attr('disabled', true);
                        $('#latitude').attr('disabled', true);
                        $('#longitude').attr('disabled', true);
                        $('#geographic_direccion').attr('disabled', true);
                        $('#geographic_ciudad').attr('disabled', true);
                        $('#geographic_checkbox_vive_lejos').attr('disabled', true);
                        $('#geographic_checkbox_zona_riesgo').attr('disabled', true);
                        $('#nivel_bajo').attr('disabled', true);
                        $('#nivel_medio').attr('disabled', true);
                        $('#nivel_alto').attr('disabled', true);
                        $('#geographic_text_area').attr('disabled', true);
                        $('#nativo').attr("disabled", true);
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        console.log(msg);
                    }
                });
            }
        }
    }
})
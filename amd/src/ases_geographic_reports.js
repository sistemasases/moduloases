// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @author     Joan Manuel Tovar Guzman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/ases_geographic_reports
 */
define(['jquery',
        'block_ases/jszip'
    ],

    function ($, jszip) {

        return {
            init: function () {
                window.JSZip = jszip;
                getDataMap();

                //Evento para controlar el checkbox de mapa de calor
                $('#heat_map').on('change', function () {

                    if (window.heatmap){
                        window.heatmap.setMap(heatmap.getMap() ? null : window.map);
                    }else{
                        mapaCalor();
                    }
                });

                //Evento para controlar el checkbox de mapa de marcadores
                $('#markers_map').on('change', function () {

                    if (window.markers){
                        for (var x in window.markers){
                            window.markers[x].setMap(window.markers[x].getMap() ? null : window.map);
                        }
                    }else{
                        marcadoresMapa();
                    }

                });

                //Evento para actualizar el mapa cuando cambie la seleccion de cohorte
                $('#conditions').on('change', function () {
                    getDataMap();
                });
            }
        }


        /**
         * Funcion getDataMap
         *
         * Funcion que se encarga de hacer el llamado para obtener los datos de las coordenadas en la base de datos
         */

        function getDataMap(){

            $.ajax({

                type: "POST",
                data: { type: 'mapa', cohort: $('#conditions').val(), instance_id: getIdinstancia() },
                url: "../managers/ases_report/asesreport_graphics_processing.php",
                success: function (msg) {
                    crearActualizarMapa(msg);
                },
                dataType: "json",
                cache: false,
                async: true,

                failure: function (msg) { }
            });
        }

        /**
         * Funcion crearActualizarMapa
         *
         * Funcion que recibe la informacion de las coordenadas y se encarga de crear o actualizar el mapa, segun corresponda
         *
         * @param data
         */

        function crearActualizarMapa(data) {

            //Si no existe el mapa, se crea desde cero
            if(!window.map){
                var latLng_Cali = new google.maps.LatLng(3.4247198, -76.5259052);

                window.map = new google.maps.Map(document.getElementById('map-canvas'), {
                    center: latLng_Cali,
                    zoom: 12
                });
            }

            //Se prepara la informacion de las coordenadas

            var puntosMapa = [], cohortes = [];
            var latitud, longitud;

            for (var x in data){
                latitud = Number(data[x].latitude);
                longitud = Number(data[x].longitude);
                puntosMapa.push(new google.maps.LatLng(latitud,longitud));
                cohortes.push(data[x].cohorte);
            };

            //Se asignan los atributos pertenecientes al mapa

            window.puntosMapa = puntosMapa;
            window.cohortes = cohortes;

            mapaCalor(puntosMapa);
            marcadoresMapa(puntosMapa);

        };


        /**
         * Funcion mapaCalor
         *
         * Funcion que se encarga de superponer el mapa de calor sobre el mapa existente
         *
         */

        function mapaCalor(){

            if(window.heatmap){
                window.heatmap.setMap(null);
            }

            if($("#heat_map").is(":checked")){

                window.heatmap = new google.maps.visualization.HeatmapLayer({
                    data: window.puntosMapa,
                    map: map,
                    opacity: 1,
                    maxIntensity: 10
                });
            }

        };

        /**
         * Funcion marcadoresMapa
         *
         * Funcion que se encarga de dibujar los marcadores en el mapa existente segun las coordenadas
         */


        function marcadoresMapa(){

            if (window.markers){
                for (var x in window.markers){
                    window.markers[x].setMap(null);
                }
            }

            if($("#markers_map").is(":checked")) {

                window.markers = [];
                var icon, color;

                for (var x in window.puntosMapa) {

                    switch (window.cohortes[x]) {

                        case 'SPP':
                            color = 'blue';
                            break;
                        case 'SPE':
                            color = 'green';
                            break;
                        case 'SPT':
                            color = 'red';
                            break;
                        case '3740':
                            color = 'purple';
                            break;
                        case 'Otros':
                            color = 'orange';
                            break;
                    };


                    icon = {
                        url: "https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_"+ color +".png", // url
                        scaledSize: new google.maps.Size(11, 20) // scaled size
                    };

                    window.markers.push(new google.maps.Marker({
                        position: window.puntosMapa[x],
                        map: map,
                        icon: icon,
                        title: window.cohortes[x]
                    }));
                }
            }

        };

        /**
         * Funcion getIdInstancia
         *
         * Funcion que retorna la instancia actual
         * @returns {*}
         */

        function getIdinstancia() {
            var urlParameters = location.search.split('&');

            for (x in urlParameters) {
                if (urlParameters[x].indexOf('instanceid') >= 0) {
                    var intanceparameter = urlParameters[x].split('=');
                    return intanceparameter[1];
                }
            }
            return 0;
        };
    });

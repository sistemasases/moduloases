// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/ases_geographic_reports
 */
define(['jquery',
        'block_ases/jszip',
        'block_ases/dataTables.autoFill',
        'block_ases/dataTables.buttons',
        'block_ases/buttons.html5',
        'block_ases/buttons.flash',
        'block_ases/buttons.print',
        'block_ases/bootstrap',
        'block_ases/sweetalert2',
        'block_ases/jqueryui',
        'block_ases/select2',
    ],

    function ($, jszip, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2) {

        return {
            init: function () {
                window.JSZip = jszip;
                getDataMap();

                $('#heat_map').on('change', function () {

                    if (window.heatmap){
                        window.heatmap.setMap(heatmap.getMap() ? null : window.map);
                    }else{
                        mapaCalor();
                    }
                });

                $('#markers_map').on('change', function () {

                    if (window.markers){
                        for (var x in window.markers){
                            window.markers[x].setMap(window.markers[x].getMap() ? null : window.map);
                        }
                    }else{
                        marcadoresMapa();
                    }

                });


                $('#conditions').on('change', function () {
                    getDataMap();
                });
            }
        }


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

        function crearActualizarMapa(data) {

            if(!window.map){
                var latLng_Cali = new google.maps.LatLng(3.4247198, -76.5259052);

                window.map = new google.maps.Map(document.getElementById('map-canvas'), {
                    center: latLng_Cali,
                    zoom: 12
                });
            }

            var puntosMapa = [];
            var latitud, longitud;

            for (var x in data){
                latitud = Number(data[x].latitude);
                longitud = Number(data[x].longitude);
                puntosMapa.push(new google.maps.LatLng(latitud,longitud));
            };

            window.puntosMapa = puntosMapa;

            mapaCalor(puntosMapa);
            marcadoresMapa(puntosMapa);

        };


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

        function marcadoresMapa(){

            if (window.markers){
                for (var x in window.markers){
                    window.markers[x].setMap(null);
                }
            }

            if($("#markers_map").is(":checked")) {

                var icon = {
                    url: "https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_blue.png", // url
                    scaledSize: new google.maps.Size(11, 20) // scaled size
                };

                window.markers = [];

                for (var x in window.puntosMapa) {
                    window.markers.push(new google.maps.Marker({
                        position: window.puntosMapa[x],
                        map: map,
                        icon: icon
                    }));
                }
            }

        };



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

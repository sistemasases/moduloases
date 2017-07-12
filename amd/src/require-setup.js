// file: js/require-setup.js
//
// Declare this variable before loading RequireJS JavaScript library
// To config RequireJS after it’s loaded, pass the below object into require.config();

var require = {
    shim : {
        'bootstrap': {  deps: ["jquery"],exports: "jQuery.fn.popover"},
        'datatables.net': { "deps" :['bootstrap','jquery']},
        'highcharts' : { exports: "Highcharts", "deps" : ['jquery']},
    },
    paths: {
        'jquery' : "//code.jquery.com/jquery-2.1.1.min",
        'bootstrap' : "//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min",
        'bootstrap-modal' : "//cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modal.min",

         //'datatables': "//cdn.datatables.net/1.10.12/js/dataTables.bootstrap",
        'datatables.net' : "//cdn.datatables.net/1.10.12/js/jquery.dataTables.min", //1.10.15
        'datatables.jqueryui' : "//cdn.datatables.net/1.10.12/js/dataTables.jqueryui.min",
        'jszip' : "//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min",
        'pdfmake' : "//cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min",
        'vfs_fonts' : "//cdn.rawgit.com/bpampuch/pdfmake/0.1.28/build/vfs_fonts",
        'datatables.net-buttons' : "//cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min",
        'buttons.flash' : "//cdn.datatables.net/buttons/1.3.1/js/buttons.flash.min",
        'buttons.html5' : "//cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min",
        'buttons.print' : "//cdn.datatables.net/buttons/1.3.1/js/buttons.print.min",
        'highstock': "//cdnjs.cloudflare.com/ajax/libs/highstock/5.0.12/highstock",
        'sweetalert' : "//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.min",
    }
};
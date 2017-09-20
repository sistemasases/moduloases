// file: js/require-setup.js
//
// Declare this variable before loading RequireJS JavaScript library
// To config RequireJS after it’s loaded, pass the below object into require.config();

var require = {
    shim: {
        'jquery': {
            exports: "jQuery"
        },
        'bootstrap': {
            deps: ["jquery"],
            exports: "jQuery.fn.popover"
        },
        'datatables.net': {
            deps: ['bootstrap', 'jquery']
        },
        'highcharts': {
            deps: ['jquery'],
            exports: "Highcharts",
        },
        'd3': {
            deps: ["jquery"]
        },
        'radarchart':{
            deps: ["d3"]
        },
        'select2':{
         deps: ['bootstrap', 'jquery']
        }
    },
    paths: {
        'jquery': "//code.jquery.com/jquery-2.1.1.min",
        'bootstrap': "//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min",
        'bootstrap-modal': "//cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modal.min",
        'select2':"https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min",
        'datatables.net': "//cdn.datatables.net/1.10.12/js/jquery.dataTables.min", //1.10.15
        'datatables.jqueryui': "//cdn.datatables.net/1.10.12/js/dataTables.jqueryui.min",
        'jszip': "//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min",
        'pdfmake': "//cdn.rawgit.com/bpampuch/pdfmake/0.1.29/build/pdfmake.min",
        'vfs_fonts': "//cdn.rawgit.com/bpampuch/pdfmake/0.1.28/build/vfs_fonts",
        'datatables.net-buttons': "//cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min",
        'buttons.flash': "//cdn.datatables.net/buttons/1.3.1/js/buttons.flash.min",
        'buttons.html5': "//cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min",
        'buttons.print': "//cdn.datatables.net/buttons/1.3.1/js/buttons.print.min",
        'highstock': "//cdnjs.cloudflare.com/ajax/libs/highstock/5.0.12/highstock",
        'sweetalert': "//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.min",
        'sweetmodal': "../../js/sweet-modal.min.js",
        'validator': "../../scripts/jquery.validate.min",
        'jquery-picker': "//code.jquery.com/jquery-1.12.4",
        'jqueryui-picker': "//code.jquery.com/ui/1.12.1/jquery-ui",
        'd3': "http://d3js.org/d3.v3.min",
        'radarchart': "c3/radarchart"
    }
};

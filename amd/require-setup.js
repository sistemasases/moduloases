// file: js/require-setup.js
//
// Declare this variable before loading RequireJS JavaScript library
// To config RequireJS after it’s loaded, pass the below object into require.config();

var require = {
    shim: {
        'moment':{
            exports: 'moment'
        }
    },
    paths:{
        'moment': '../scripts/moment.js'
    }
}
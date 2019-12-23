/**
 * @package     block_ases
 * @author      Jeison Cardona Gómez - <jeison.cardona@correounivalle.edu.co>
 * @copyright   2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @module      block_ases/_ases_api
 */


define([
    
    'jquery',
    'block_ases/loading_indicator'
    
], function ( $, loading_indicator ) {
        
    const MANAGER_DIR_BASE = "../managers";
    const METHODS = [ "POST", "GET" ];

    /**
    * Method that allow make Ajax requests to ASES's managers.
    * 
    * @param {string} manager_name [description]
    * @param {string} function_name [description]
    * @param {array}  parameters    [description]
    * @param {string}  method    [description]
    * @param {bool}  async_call    [description]
    * @param {bool}  use_loading_indicator    [description]
    * @param {Function}  ok_callback    [description]
    * @param {Function}  error_callback    [description]
    * 
    * @return {array}  parameters    [description]
    * 
    **/
    let general_call = (
            manager_name, function_name, parameters = [], 
            method = "POST", async_call = true, use_loading_indicator = false, 
            ok_callback, error_callback, manager_version = 1
    ) => {
        
        method = method.toUpperCase();                                          // Normalization of method name.

        if( use_loading_indicator ){                                            // Check if can be user loading indicator.
            loading_indicator.show();                                           // Display a loading indication in the screen.
        }
        
        if( !METHODS.includes( method ) ){                                      // Check if the given method is valid.
            throw new Error( `'${method}' is not a valid method.` );            // Throw if the given method is invalid.
        }
                
        let to_return;                                                          // If is not an async call, in this variable will be stored the request response.
                        
        $.ajax({                                                                // jQuery AJAX request.
            type: method,
            url: `${MANAGER_DIR_BASE}/${manager_name}${ 
                    ( manager_version === 1 ? "" : "/v" + manager_version ) 
                  }/${manager_name}_api.php`,                                   // Manager API location.
            data: JSON.stringify(
                { 
                    function: function_name,                                    // Function name to call in API.
                    params: parameters                                          // Function parameters.
                }
            ),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            cache: false,
            processData: false,                                                 // Preventing default data parse behavior.
            async: async_call,
            success: function (data) {
                if( use_loading_indicator ){
                    loading_indicator.hide();
                }
                
                if (ok_callback instanceof Function) {                          // Check if exist a callback for a success response.
                    ok_callback( data );                                        // Callback execution.
                }
                
                if( !async_call ){
                    to_return = data;                                           // Asignation of response to return var. 
                }
                
            },
            error: function (data) {
                if( use_loading_indicator ){
                    loading_indicator.hide();
                }
                
                if (error_callback instanceof Function) {
                    error_callback( data );
                }
                
                if( !async_call ){
                    to_return = data;
                }
            }
        });
        
        if( !async_call ){
            return to_return;
        }
        
    };
    
    let get = (
            manager_name, function_name, parameters = [], async_call = true,    // Short function to GET method.
            use_loading_indicator = false, ok_callback, error_callback, manager_version = 1
    ) => {
        return general_call( 
            manager_name, function_name, parameters, "GET", 
            async_call, use_loading_indicator, ok_callback, error_callback , manager_version
        );
    };
    
    let post = (                                                                // Short function to POST method.
            manager_name, function_name, parameters = [], async_call = true, 
            use_loading_indicator = false, ok_callback, error_callback, manager_version = 1
    ) => {
        return general_call( 
            manager_name, function_name, parameters, "POST", 
            async_call, use_loading_indicator, ok_callback, error_callback, manager_version 
        );
    };

    console.log("Ases API initialised");

    return {
        get: get,
        post: post
    };
});
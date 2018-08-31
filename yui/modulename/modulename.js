YUI.add('moodle-block_ases-modulename', function(Y) {
    var ModulenameNAME = 'block_ases';
    var MODULENAME = function() {
        MODULENAME.superclass.constructor.apply(this, arguments);
    };
    Y.extend(MODULENAME, Y.Base, {
        initializer : function(config) { // 'config' contains the parameter values
            alert('I am in initializer');
            Y.one('#update-profile-image-button').on('click', function () {
                console.log('hola');
                            // If your form has a cancel button, you need to disable it, otherwise it'll be sent with the request
            // and Moodle will think your form was cancelled
            var form = document.getElementById('update_user_profile_image');

            //Y.one('#id_cancel').set('disabled', 'disabled');
         
            // Send the request
            Y.io('http://localhost/moodle34_luis/blocks/ases/view/edit_user_image.php', {
                method: 'POST',
                on: {
                    success: function(id, o) {
                        console.log(o.responseText);
                        response = Y.JSON.parse(o.responseText);
                        // Display some feedback to the user
                    }
                },
                form: form,
                context: this
            });
            });
            
        },
        submit_form: function(e) {
            //var Y = this.Y;
            // Form serialisation works best if we get the form using getElementById, for some reason
            var form = document.getElementById('form_id');
            // If your form has a cancel button, you need to disable it, otherwise it'll be sent with the request
            // and Moodle will think your form was cancelled
            Y.one('#id_cancel').set('disabled', 'disabled');
         
            // Send the request
            Y.io(M.cfg.wwwroot+'/form_ajax.php', {
                method: post,
                on: {
                    success: function(id, o) {
                        response = Y.JSON.parse(o.responseText);
                        // Display some feedback to the user
                    }
                },
                form: form,
                context: this
            });
         
        }
    }, {
        NAME : ModulenameNAME, //module name is something mandatory. 
                                // It should be in lower case without space 
                                // as YUI use it for name space sometimes.
        ATTRS : {
                 aparam : {}
        } // Attributes are the parameters sent when the $PAGE->requires->yui_module calls the module. 
          // Here you can declare default values or run functions on the parameter. 
          // The param names must be the same as the ones declared 
          // in the $PAGE->requires->yui_module call.
    });
    M.block_ases = M.block_ases || {}; // This line use existing name path if it exists, otherwise create a new one. 
                                                 // This is to avoid to overwrite previously loaded module with same name.

    M.block_ases.init_modulename = function(config) { // 'config' contains the parameter values
        alert('I am in the javascript module, Yeah!');
        return new MODULENAME(config); // 'config' contains the parameter values
    };
  }, '@VERSION@', {
      requires:['base','another_required_YUI_module', 'a_moodle_YUI_module']
  });
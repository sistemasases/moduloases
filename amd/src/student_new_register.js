// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_ases/student_new_register
 */
define(['jquery',
    'block_ases/bootstrap',
    'block_ases/tagging',
    'block_ases/mustache',
    'block_ases/sweetalert',
    'block_ases/select2'],
    function($, bootstrap, tagging, mustache, sweetalert, select2) {

        (function (t, s, e, n) {
            "use strict";
            function i(s, e) {
                (this.options = t.extend(!0, {}, o, e)),
                    (this.main = t(s)),
                    (this.nav = this.main.children("ul")),
                    (this.steps = t("li > a", this.nav)),
                    (this.container = this.main.children("div")),
                    (this.pages = this.container.children("div")),
                    (this.current_index = null),
                    (this.options.toolbarSettings.toolbarButtonPosition = "right" === this.options.toolbarSettings.toolbarButtonPosition ? "end" : this.options.toolbarSettings.toolbarButtonPosition),
                    (this.options.toolbarSettings.toolbarButtonPosition = "left" === this.options.toolbarSettings.toolbarButtonPosition ? "start" : this.options.toolbarSettings.toolbarButtonPosition),
                    (this.options.theme = null === this.options.theme || "" === this.options.theme ? "default" : this.options.theme),
                    this.init();
            }
            var o = {
                selected: 0,
                keyNavigation: 0,
                autoAdjustHeight: !0,
                cycleSteps: !1,
                backButtonSupport: !0,
                useURLhash: !0,
                showStepURLhash: !0,
                lang: { next: "Next", previous: "Previous" },
                toolbarSettings: { toolbarPosition: "bottom", toolbarButtonPosition: "end", showNextButton: !0, showPreviousButton: !0, toolbarExtraButtons: [] },
                anchorSettings: { anchorClickable: !0, enableAllAnchors: !1, markDoneStep: !0, markAllPreviousStepsAsDone: !0, removeDoneStepOnNavigateBack: !1, enableAnchorOnDoneStep: !0 },
                contentURL: null,
                contentCache: !0,
                ajaxSettings: {},
                disabledSteps: [],
                errorSteps: [],
                hiddenSteps: [],
                theme: "default",
                transitionEffect: "none",
                transitionSpeed: "400",
            };
            t.extend(i.prototype, {
                init: function () {
                    this._setElements(), this._setToolbar(), this._setEvents();
                    var e = this.options.selected;
                    if (this.options.useURLhash) {
                        var n = s.location.hash;
                        if (n && n.length > 0) {
                            var i = t("a[href*='" + n + "']", this.nav);
                            if (i.length > 0) {
                                var o = this.steps.index(i);
                                e = o >= 0 ? o : e;
                            }
                        }
                    }
                    e > 0 && this.options.anchorSettings.markDoneStep && this.options.anchorSettings.markAllPreviousStepsAsDone && this.steps.eq(e).parent("li").prevAll().addClass("done"), this._showStep(e);
                },
                _setElements: function () {
                    this.main.addClass("sw-main sw-theme-" + this.options.theme),
                        this.nav.addClass("nav nav-tabs step-anchor").children("li").addClass("nav-item").children("a").addClass("nav-link"),
                        this.options.anchorSettings.enableAllAnchors !== !1 && this.options.anchorSettings.anchorClickable !== !1 && this.steps.parent("li").addClass("clickable"),
                        this.container.addClass("sw-container tab-content"),
                        this.pages.addClass("tab-pane step-content");
                    var s = this;
                    return (
                        this.options.disabledSteps &&
                            this.options.disabledSteps.length > 0 &&
                            t.each(this.options.disabledSteps, function (t, e) {
                                s.steps.eq(e).parent("li").addClass("disabled");
                            }),
                        this.options.errorSteps &&
                            this.options.errorSteps.length > 0 &&
                            t.each(this.options.errorSteps, function (t, e) {
                                s.steps.eq(e).parent("li").addClass("danger");
                            }),
                        this.options.hiddenSteps &&
                            this.options.hiddenSteps.length > 0 &&
                            t.each(this.options.hiddenSteps, function (t, e) {
                                s.steps.eq(e).parent("li").addClass("hidden");
                            }),
                        !0
                    );
                },
                _setToolbar: function () {
                    if ("none" === this.options.toolbarSettings.toolbarPosition) return !0;
                    var s = this.options.toolbarSettings.showNextButton !== !1 ? t("<button></button>").text(this.options.lang.next).addClass("btn btn-secondary sw-btn-next").attr("type", "button") : null,
                        e = this.options.toolbarSettings.showPreviousButton !== !1 ? t("<button></button>").text(this.options.lang.previous).addClass("btn btn-secondary sw-btn-prev").attr("type", "button") : null,
                        n = t("<div></div>").addClass("btn-group mr-2 sw-btn-group").attr("role", "group").append(e, s),
                        i = null;
                    this.options.toolbarSettings.toolbarExtraButtons &&
                        this.options.toolbarSettings.toolbarExtraButtons.length > 0 &&
                        ((i = t("<div></div>").addClass("btn-group mr-2 sw-btn-group-extra").attr("role", "group")),
                        t.each(this.options.toolbarSettings.toolbarExtraButtons, function (t, s) {
                            i.append(s.clone(!0));
                        }));
                    var o, a;
                    switch (this.options.toolbarSettings.toolbarPosition) {
                        case "top":
                            (o = t("<div></div>").addClass("btn-toolbar sw-toolbar sw-toolbar-top justify-content-" + this.options.toolbarSettings.toolbarButtonPosition)),
                                o.append(n),
                                "start" === this.options.toolbarSettings.toolbarButtonPosition ? o.prepend(i) : o.append(i),
                                this.container.before(o);
                            break;
                        case "bottom":
                            (a = t("<div></div>").addClass("btn-toolbar sw-toolbar sw-toolbar-bottom justify-content-" + this.options.toolbarSettings.toolbarButtonPosition)),
                                a.append(n),
                                "start" === this.options.toolbarSettings.toolbarButtonPosition ? a.prepend(i) : a.append(i),
                                this.container.after(a);
                            break;
                        case "both":
                            (o = t("<div></div>").addClass("btn-toolbar sw-toolbar sw-toolbar-top justify-content-" + this.options.toolbarSettings.toolbarButtonPosition)),
                                o.append(n),
                                "start" === this.options.toolbarSettings.toolbarButtonPosition ? o.prepend(i) : o.append(i),
                                this.container.before(o),
                                (a = t("<div></div>").addClass("btn-toolbar sw-toolbar sw-toolbar-bottom justify-content-" + this.options.toolbarSettings.toolbarButtonPosition)),
                                a.append(n.clone(!0)),
                                null !== i && ("start" === this.options.toolbarSettings.toolbarButtonPosition ? a.prepend(i.clone(!0)) : a.append(i.clone(!0))),
                                this.container.after(a);
                            break;
                        default:
                            (a = t("<div></div>").addClass("btn-toolbar sw-toolbar sw-toolbar-bottom justify-content-" + this.options.toolbarSettings.toolbarButtonPosition)),
                                a.append(n),
                                this.options.toolbarSettings.toolbarButtonPosition,
                                a.append(i),
                                this.container.after(a);
                    }
                    return !0;
                },
                _setEvents: function () {
                    var n = this;
                    return (
                        t(this.steps).on("click", function (t) {
                            if ((t.preventDefault(), n.options.anchorSettings.anchorClickable === !1)) return !0;
                            var s = n.steps.index(this);
                            if (n.options.anchorSettings.enableAnchorOnDoneStep === !1 && n.steps.eq(s).parent("li").hasClass("done")) return !0;
                            s !== n.current_index && (n.options.anchorSettings.enableAllAnchors !== !1 && n.options.anchorSettings.anchorClickable !== !1 ? n._showStep(s) : n.steps.eq(s).parent("li").hasClass("done") && n._showStep(s));
                        }),
                        t(".sw-btn-next", this.main).on("click", function (t) {
                            t.preventDefault(), n._showNext();
                        }),
                        t(".sw-btn-prev", this.main).on("click", function (t) {
                            t.preventDefault(), n._showPrevious();
                        }),
                        this.options.keyNavigation &&
                            t(e).keyup(function (t) {
                                n._keyNav(t);
                            }),
                        this.options.backButtonSupport &&
                            t(s).on("hashchange", function (e) {
                                if (!n.options.useURLhash) return !0;
                                if (s.location.hash) {
                                    var i = t("a[href*='" + s.location.hash + "']", n.nav);
                                    i && i.length > 0 && (e.preventDefault(), n._showStep(n.steps.index(i)));
                                }
                            }),
                        !0
                    );
                },
                _showNext: function () {
                    for (var t = this.current_index + 1, s = t; s < this.steps.length; s++)
                        if (!this.steps.eq(s).parent("li").hasClass("disabled") && !this.steps.eq(s).parent("li").hasClass("hidden")) {
                            t = s;
                            break;
                        }
                    if (this.steps.length <= t) {
                        if (!this.options.cycleSteps) return !1;
                        t = 0;
                    }
                    return this._showStep(t), !0;
                },
                _showPrevious: function () {
                    for (var t = this.current_index - 1, s = t; s >= 0; s--)
                        if (!this.steps.eq(s).parent("li").hasClass("disabled") && !this.steps.eq(s).parent("li").hasClass("hidden")) {
                            t = s;
                            break;
                        }
                    if (0 > t) {
                        if (!this.options.cycleSteps) return !1;
                        t = this.steps.length - 1;
                    }
                    return this._showStep(t), !0;
                },
                _showStep: function (t) {
                    return !!this.steps.eq(t) && t != this.current_index && !this.steps.eq(t).parent("li").hasClass("disabled") && !this.steps.eq(t).parent("li").hasClass("hidden") && (this._loadStepContent(t), !0);
                },
                _loadStepContent: function (s) {
                    var e = this,
                        n = this.steps.eq(this.current_index),
                        i = "",
                        o = this.steps.eq(s),
                        a = o.data("content-url") && o.data("content-url").length > 0 ? o.data("content-url") : this.options.contentURL;
                    if ((null !== this.current_index && this.current_index !== s && (i = this.current_index < s ? "forward" : "backward"), null !== this.current_index && this._triggerEvent("leaveStep", [n, this.current_index, i]) === !1))
                        return !1;
                    if (!(a && a.length > 0) || (o.data("has-content") && this.options.contentCache)) this._transitPage(s);
                    else {
                        var r = o.length > 0 ? t(o.attr("href"), this.main) : null,
                            h = t.extend(
                                !0,
                                {},
                                {
                                    url: a,
                                    type: "POST",
                                    data: { step_number: s },
                                    dataType: "text",
                                    beforeSend: function () {
                                        e._loader("show");
                                    },
                                    error: function (s, n, i) {
                                        e._loader("hide"), t.error(i);
                                    },
                                    success: function (t) {
                                        t && t.length > 0 && (o.data("has-content", !0), r.html(t)), e._loader("hide"), e._transitPage(s);
                                    },
                                },
                                this.options.ajaxSettings
                            );
                        t.ajax(h);
                    }
                    return !0;
                },
                _transitPage: function (s) {
                    var e = this,
                        n = this.steps.eq(this.current_index),
                        i = n.length > 0 ? t(n.attr("href"), this.main) : null,
                        o = this.steps.eq(s),
                        a = o.length > 0 ? t(o.attr("href"), this.main) : null,
                        r = "";
                    null !== this.current_index && this.current_index !== s && (r = this.current_index < s ? "forward" : "backward");
                    var h = "middle";
                    return (
                        0 === s ? (h = "first") : s === this.steps.length - 1 && (h = "final"),
                        (this.options.transitionEffect = this.options.transitionEffect.toLowerCase()),
                        this.pages.finish(),
                        "slide" === this.options.transitionEffect
                            ? i && i.length > 0
                                ? i.slideUp("fast", this.options.transitionEasing, function () {
                                      a.slideDown(e.options.transitionSpeed, e.options.transitionEasing);
                                  })
                                : a.slideDown(this.options.transitionSpeed, this.options.transitionEasing)
                            : "fade" === this.options.transitionEffect
                            ? i && i.length > 0
                                ? i.fadeOut("fast", this.options.transitionEasing, function () {
                                      a.fadeIn("fast", e.options.transitionEasing, function () {
                                          t(this).show();
                                      });
                                  })
                                : a.fadeIn(this.options.transitionSpeed, this.options.transitionEasing, function () {
                                      t(this).show();
                                  })
                            : (i && i.length > 0 && i.hide(), a.show()),
                        this._setURLHash(o.attr("href")),
                        this._setAnchor(s),
                        this._setButtons(s),
                        this._fixHeight(s),
                        (this.current_index = s),
                        this._triggerEvent("showStep", [o, this.current_index, r, h]),
                        !0
                    );
                },
                _setAnchor: function (t) {
                    return (
                        this.steps.eq(this.current_index).parent("li").removeClass("active"),
                        this.options.anchorSettings.markDoneStep !== !1 &&
                            null !== this.current_index &&
                            (this.steps.eq(this.current_index).parent("li").addClass("done"), this.options.anchorSettings.removeDoneStepOnNavigateBack !== !1 && this.steps.eq(t).parent("li").nextAll().removeClass("done")),
                        this.steps.eq(t).parent("li").removeClass("done").addClass("active"),
                        !0
                    );
                },
                _setButtons: function (s) {
                    return (
                        this.options.cycleSteps ||
                            (0 >= s ? t(".sw-btn-prev", this.main).addClass("disabled") : t(".sw-btn-prev", this.main).removeClass("disabled"),
                            this.steps.length - 1 <= s ? t(".sw-btn-next", this.main).addClass("disabled") : t(".sw-btn-next", this.main).removeClass("disabled")),
                        !0
                    );
                },
                _keyNav: function (t) {
                    var s = this;
                    switch (t.which) {
                        case 37:
                            s._showPrevious(), t.preventDefault();
                            break;
                        case 39:
                            s._showNext(), t.preventDefault();
                            break;
                        default:
                            return;
                    }
                },
                _fixHeight: function (s) {
                    if (this.options.autoAdjustHeight) {
                        var e = this.steps.eq(s).length > 0 ? t(this.steps.eq(s).attr("href"), this.main) : null;
                        this.container.finish().animate({ minHeight: e.outerHeight() }, this.options.transitionSpeed, function () {});
                    }
                    return !0;
                },
                _triggerEvent: function (s, e) {
                    var n = t.Event(s);
                    return this.main.trigger(n, e), !n.isDefaultPrevented() && n.result;
                },
                _setURLHash: function (t) {
                    this.options.showStepURLhash && s.location.hash !== t && (s.location.hash = t);
                },
                _loader: function (t) {
                    switch (t) {
                        case "show":
                            this.main.addClass("sw-loading");
                            break;
                        case "hide":
                            this.main.removeClass("sw-loading");
                            break;
                        default:
                            this.main.toggleClass("sw-loading");
                    }
                },
                theme: function (t) {
                    if (this.options.theme === t) return !1;
                    this.main.removeClass("sw-theme-" + this.options.theme), (this.options.theme = t), this.main.addClass("sw-theme-" + this.options.theme), this._triggerEvent("themeChanged", [this.options.theme]);
                },
                next: function () {
                    this._showNext();
                },
                prev: function () {
                    this._showPrevious();
                },
                reset: function () {
                    if (this._triggerEvent("beginReset") === !1) return !1;
                    this.container.stop(!0),
                        this.pages.stop(!0),
                        this.pages.hide(),
                        (this.current_index = null),
                        this._setURLHash(this.steps.eq(this.options.selected).attr("href")),
                        t(".sw-toolbar", this.main).remove(),
                        this.steps.removeClass(),
                        this.steps.parents("li").removeClass(),
                        this.steps.data("has-content", !1),
                        this.init(),
                        this._triggerEvent("endReset");
                },
                stepState: function (s, e) {
                    s = t.isArray(s) ? s : [s];
                    var n = t.grep(this.steps, function (e, n) {
                        return t.inArray(n, s) !== -1;
                    });
                    if (n && n.length > 0)
                        switch (e) {
                            case "disable":
                                t(n).parents("li").addClass("disabled");
                                break;
                            case "enable":
                                t(n).parents("li").removeClass("disabled");
                                break;
                            case "hide":
                                t(n).parents("li").addClass("hidden");
                                break;
                            case "show":
                                t(n).parents("li").removeClass("hidden");
                                break;
                            case "error-on":
                                t(n).parents("li").addClass("danger");
                                break;
                            case "error-off":
                                t(n).parents("li").removeClass("danger");
                        }
                },
            }),
                (t.fn.smartWizard = function (s) {
                    var e,
                        n = arguments;
                    return void 0 === s || "object" == typeof s
                        ? this.each(function () {
                              t.data(this, "smartWizard") || t.data(this, "smartWizard", new i(this, s));
                          })
                        : "string" == typeof s && "_" !== s[0] && "init" !== s
                        ? ((e = t.data(this[0], "smartWizard")), "destroy" === s && t.data(this, "smartWizard", null), e instanceof i && "function" == typeof e[s] ? e[s].apply(e, Array.prototype.slice.call(n, 1)) : this)
                        : void 0;
                });
        })($, window, document);
        

        return {
            init: function() {
                $(document).ready(function() {

                    var input_deportes;
                    var input_actividades;
                    var id_moodle = "";
                    var id_ases = "";
                    var data_ases = "";
                    var id_economics_data, id_academics_data, id_discapacity_data, id_healt_service = "";
                    var cod_programa = "";
                    var general_data = "";
                    //Banderas para las validaciones al guardar
                    var mdl_user, ases_user, user_extended = false;
                    var economics_data, academics_data = false;
                    var healthCondition, healthService, discapacity = false;
                    var cambios_s1, cambios_s2, cambios_s3, cambios_s4, cambios_s5, cambios_s6 = false;

                    $("#step-1 :input").prop("disabled", true);
                    $("#codigo_estudiantil").prop("disabled", false);
                    $("#validar_codigo").prop("disabled", false);
                    $("#limpiar_form").prop("disabled", false);

                    $('#pruebas').on('click', function() {

                    });

                    //Modal controls
                    $('#mostrar').on('click', function() {
                        //Secciòn 1
                        loadSelector($('#id_ciudad_ini'), "get_ciudades");
                        loadSelector($('#acompanamientos'), "get_otros_acompañamientos", );
                        loadSelector($('#id_cond_excepcion'), "get_cond_excepcion", );
                        loadSelector($('#id_estado_civil'), "get_estados_civiles");
                        loadSelector($('#sexo'), 'get_sex_options');
                        loadSelector($('#id_identidad_gen'), 'get_generos');
                        loadSelector($('#id_act_simultanea'), 'get_act_simultaneas');
                        loadSelector($('#id_etnia'), 'get_etnias');
                        loadSelector($('#tipo_doc'), 'get_document_types');
                        loadSelector($('#id_discapacidad'), 'get_discapacities');


                        //Secciòn 2
                        loadSelector($('#id_pais'), 'get_paises');
                        loadSelector($('#barrio_res'), 'get_barrios');
                        loadSelector($('#id_ciudad_res'), 'get_ciudades');
                        setSelectPrograma();
                        
                        //Secciòn 3
                        loadSelector($('#id_pais_res'), 'get_paises');
                        loadSelector($('#barrio_ini'), 'get_barrios');
                        loadSelector($('#tipo_doc_ini'), 'get_document_types');
                        loadSelector($('#select_sede'), 'get_sedes');

                        //controls radio
                        hideAndShow("permanencia", "solvencia_econo");
                        hideAndShow("set-desplazamiento", "ayuda_transporte");
                        hideAndShow("set-certificado-discapacidad", "discapacidad");
                        hideAndShow("set-apoyo", "apoyo_partic");
                        hideAndShow("set-participacion", "participacion");
                        hideAndShow("set-salud", "condicion");
                        hideAndShowCheckB('set-orientacion', "orientacion_sexual");
                        //hideAndShowCheckB('set-sexo', "sexo");
                        hideAndShowCheckB('set-identidad-gen', "identidad_genero");
                        hideAndShow('set-beca', 'beca')

                        //Habilitar tagging 
                        input_deportes = new Tagging($("#deportes_tag"), $("#tags_deportes"), 1, 3);
                        input_deportes.createTag();
                        
                        input_actividades = new Tagging($("#tiempo_libre"), $("#tags_tiempo_libre"));
                        input_actividades.createTag();

                        initSelect2()

                        $('#modalExample').show();
                        document.body.style.overflowY = "hidden";
                    });

                    function initSelect2() {
                        $(".select_modal").select2({
                            width: 'resolve',
                            height: 'resolve',
                            language: {
                                noResults: function () {
                                    return "No hay resultado";
                                },
                                searching: function () {
                                    return "Buscando..";
                                }
                            },
                        });
                    }

                    function setSelectPrograma() {
                        loadSelector($('#select_programa'), 'get_programas_academicos');
                    }

                    //Ocultar el modal
                    $('.closer').on('click', function() {
                        $('#modalExample').hide();
                        document.body.style.overflowY = "visible";
                    });


                    //Inicializar el modal mediante steps con el tema dots

                    $('#smartwizard').smartWizard({
                        selected: 0,
                        theme: 'dots',
                        autoAdjustHeight: true,
                        showStepURLhash: false,
                        lang: {
                            next: 'Continuar y Guardar',
                            previous: 'Anterior'
                        }
                    });

                    $('#limpiar_form').on('click', function() {
                        clearForm()
                    });

                    function clearForm() {
                        //Reinicio de las variables
                        input_deportes;
                        input_actividades;
                        id_moodle = "";
                        id_ases = "";
                        data_ases = "";
                        id_economics_data = "";
                        id_academics_data = "";
                        id_discapacity_data = "";
                        id_healt_service = "";
                        cod_programa = "";
                        general_data = "";
                        mdl_user =false;
                        ases_user =false;
                        user_extended = false;
                        economics_data = false;
                        academics_data = false;
                        healthCondition= false;
                        healthService= false;
                        discapacity = false;
                        cambios_s1= false;
                        cambios_s2= false;
                        cambios_s3= false;
                        cambios_s4= false;
                        cambios_s5= false;
                        cambios_s6 = false;

                        //Deshabilitar campos a excepcion del codigo
                        $("#step-1 :input").prop("disabled", true);
                        $("#codigo_estudiantil").prop("disabled", false);
                        $("#validar_codigo").prop("disabled", false);
                        $("#limpiar_form").prop("disabled", false);

                        //Vaciar todos los campos del formulario
                        $('#nombre').val("");
                        $('#apellido').val("");
                        $('#emailinstitucional').val("");
                        $('#emailpilos_modal').val("");
                        unsetData("step-1")
                        unsetData("step-2")
                        unsetData("step-3")
                        unsetData("div_educ_media")
                        unsetData("step-4")
                        unsetData("step-5")
                        unsetData("step-6")
                        $('#select_programa').empty();
                        setSelectPrograma()
                        hideAlerts('step1')

                        crearTags('deportes', [])
                        crearTags('actividades', [])

                        $("#table_familia").find("tbody tr").remove();
                        $("#table_ingresos").find("tbody tr").remove();

                        $('#smartwizard').smartWizard("reset");
                    }


                    //Funcion para obtener los datos del usuario al digitar el codigo
                    $('#validar_codigo').on('click', function() {
                        var codeUser = $("#codigo_estudiantil").val();
                        hideAlerts('step1')


                        //Ocultar campos de orientacion sexual e identidad si estan visibles
                       var s = $("#set-identidad-gen" + " .otro").parent().next();
                       s.attr('hidden', true);
                       s.addClass("otros");
                   
                       var s = $("#set-orientacion" + " .otro").parent().next();
                       s.attr('hidden', true);
                       s.addClass("otros");


                        if (codeUser === "" || codeUser === " ") {
                            $('#nombre').val("");
                            $('#apellido').val("");
                            $('#emailinstitucional').val("");
                            $("#step-1 :input").prop("disabled", true);
                            $("#codigo_estudiantil").prop("disabled", false);
                            $("#validar_codigo").prop("disabled", false);
                            $("#limpiar_form").prop("disabled", false);
                            setSelectPrograma();
                        } else {
                            $.ajax({
                                async: false,
                                type: "POST",
                                data: JSON.stringify({
                                    "function": 'get_user',
                                    "params": codeUser
                                }),
                                url: "../managers/student_profile/studentprofile_api.php",
                                success: function(msg) {
                                    var fullUser = JSON.parse(msg);
                                    validateStudent(fullUser);

                                },
                                error: function(msg) {
                                    swal(
                                        "Error",
                                        "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                        "error"
                                    );
                                },

                            });
                        }
                    });

                    /* Funcion para validar si el la consulta del usuario en la tabla mdl_user existe
                        En caso de no existir se determina si se creara un estudiante nuevo o se ingresara otro codigo*/
                    function validateStudent(fullUser) {
                        if (fullUser != false) {
                            $("#step-1 :input").prop("disabled", true);
                            $("#codigo_estudiantil").prop("disabled", false);
                            $("#validar_codigo").prop("disabled", false);
                            $("#limpiar_form").prop("disabled", false);
                            $("#tipo_doc_ini").prop("disabled", false);
                            $("#num_doc_ini").prop("disabled", false);
                            $("#select_sede").prop("disabled", false);
                            $("#select_programa").prop("disabled", false);
                            setUserData(fullUser);
                            ases_user = false;
                            mdl_user = false;

                        } else {
                            swal({
                                    title: "Warning",
                                    text: "Estudiante inexistente en la base de datos, ¿Esta seguro de registrar este usuario?.",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonClass: "btn-success",
                                    confirmButtonText: "Confirmar",
                                    cancelButtonText: "Cancelar",
                                    closeOnConfirm: false,
                                    closeOnCancel: false
                                },
                                function(isConfirm) {
                                    if (isConfirm) {
                                        swal({title: "Confirmado",text: "",type: "success", timer: 1500});
                                        mdl_user = true;
                                        ases_user = false;
                                        unsetData("step-2")
                                        unsetData("step-3")
                                        unsetData("div_educ_media")
                                        unsetData("step-4")
                                        unsetData("step-5")
                                        unsetData("step-6")
                                        disableMdliputs();
                                    } else {
                                        swal({title: "Cancelado",text: "Ingresa nuevamente el codigo del estudiante",type: "error", timer: 1500});
                                        $("#codigo_estudiantil").val("");
                                    }
                                });
                        }

                    }

                    /* Funcion para setear los datos del estudiante en caso de existir en la tabla mdl_user 
                        y cargar el programa academico al que pertenece*/
                    function setUserData(fullUser) {
                        $('#nombre').val(fullUser.firstname);
                        $('#apellido').val(fullUser.lastname);
                        $('#emailinstitucional').val(fullUser.email);
                        disableMdliputs();

                        programs = fullUser.username.slice(fullUser.username.indexOf('-') + 1);
                        $('#select_programa').empty();
                        loadSelector($('#select_programa'), 'get_programas_academicos_est', programs);
                    }

                    //Funcion para deshabilitar los campos o habilitar los campos de la seccion 1 del formulario
                    function disableMdliputs() {
                        if (!mdl_user) {
                            $('#nombre').prop("disabled", true);
                            $('#apellido').prop("disabled", true);
                            $('#emailinstitucional').prop("disabled", true);
                        } else {
                            $("#step-1 :input").prop("disabled", false);
                        }

                        $('#edad').prop("disabled", true);
                    }

                    $("#fecha_nac_modal").blur(function() {
                        var fecha = $('#fecha_nac_modal').val();
                        calc_edad(fecha);
                    });

                    function calc_edad(fecha) {
                        var hoy = new Date();
                        var cumpleanos = new Date(fecha);
                        var edad = hoy.getFullYear() - cumpleanos.getFullYear();
                        var m = hoy.getMonth() - cumpleanos.getMonth();

                        if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
                            edad--;
                        }
                        $('#edad').val(edad);
                    }

                    /*Funciones para añadir campos a la tabla de familia y a la tabla de ingresos a 
                        la universidad en las secciones 2 y 3 del formulario*/
                    $(document).on('click', '.remove_fila', function() {
                        cambios_s2 = true
                        $(this).parent().parent().remove();
                    });

                    $(document).on('click', '.remove_fila_ing', function() {
                        cambios_s3 = true
                        ing--;
                        $(this).parent().parent().remove();
                    });

                    $("#add_person_r").on('click', function() {
                        cambios_s2 = true
                        addTable($("#table_familia"));
                    });

                    $("#add_ingreso").on('click', function() {
                        cambios_s3 = true
                        ing++;
                        addTableIng($("#table_ingresos"));
                    });

                    //Funciòn para guardar la informaciòn de servicios de salud
                    $(document).on('click', '#guardar_info', function() {

                        if (!healthService) {
                            save_health_service();
                            healthService = true;
                            cambios_s6 = false;
                            //$('#smartwizard').smartWizard("reset");
                        } else if (cambios_s6) {
                            update_health_service()
                            cambios_s6 = false;
                        }
                    });


                    /*Controles para el registros de Familiares*/
                    function addTable(table) {
                        let nuevaFila = "";
                        nuevaFila += '<tr><td> <input  class="input_fields_general_tab_modal tabl_famiia"  type="text"/></td>';
                        nuevaFila += '<td> <input  class="input_fields_general_tab_modal tabl_famiia"  type="text" /></td>';
                        nuevaFila += '<td> <button class="btn btn-danger remove_fila" type="button" title="Eliminar persona" name="btn_delete_person" style="visibility:visible;"> X </button></td></tr>';
                        table.find("tbody").append(nuevaFila);
                        initCambios('tabl_famiia')
                    }

                    function setTableFamily(nombre, rol, i) {
                        let nuevaFila = "";
                        nuevaFila += '<tr><td> <input id="nom' + i + '" class="input_fields_general_tab_modal tabl_famiia"  type="text"/></td>';
                        nuevaFila += '<td> <input id="rol' + i + '" class="input_fields_general_tab_modal tabl_famiia"  type="text" /></td>';
                        nuevaFila += '<td> <button class="btn btn-danger remove_fila" type="button" title="Eliminar persona" name="btn_delete_person" style="visibility:visible;"> X </button></td></tr>';
                        $("#table_familia").find("tbody").append(nuevaFila);
                        $("#nom" + i).val(nombre)
                        $("#rol" + i).val(rol)
                        initCambios('tabl_famiia')
                    }

                    var ing = 0;
                    /*Controles para el registros de inresos a la universidad*/
                    function addTableIng(table) {
                        let nuevaFila = "";
                        nuevaFila += '<tr><td> <input id="anio_' + ing + '" class="input_fields_general_tab_modal ingresos_u tabl_ing" name="anio_' + ing + '"  type="text"/></td>';
                        nuevaFila += '<td> <select id="s_programa_' + ing + '" class="custom-select select-academics-data select_modal tabl_ing"></select> <input  id="id_programa_' + ing + '" name="id_programa_' + ing + '" class="ingresos_u" type="number" hidden></td>';
                        nuevaFila += '<td><select id="motivo_I' + ing + '" class="custom-select select-academics-data select_modal tabl_ing"> <option value="1">Bajos académicos</option> <option value="2">Condición de salud</option> <option value="3">Fallecimiento</option> <option value="4">Condición económica</option> <option value="5">Condición de programa académico</option> <option value="6">Cambio de institución educativa</option> <option value="7">Cambio de ciudad</option> <option value="8">Retiro voluntario</option> <option value="9">Prefiero no decirlo</option> </select> <input  id="motivo_' + ing + '" name="motivo_' + ing + '" class="ingresos_u" type="number" hidden> </td>';
                        nuevaFila += '<td> <button class="btn btn-danger remove_fila_ing" type="button" title="Eliminar persona" name="btn_delete_person" style="visibility:visible;"> X </button></td></tr>';
                        table.find("tbody").append(nuevaFila);
                        loadSelector($('#s_programa_' + ing + ''), 'get_programas_academicos');
                        initSelect2()
                        initCambios('tabl_ing')
                    }

                    function setTableIng(ingr, anioI, progI, motivoI) {
                        let nuevaFila = "";
                        nuevaFila += '<tr><td> <input id="anio_' + ingr + '" class="input_fields_general_tab_modal ingresos_u tabl_ing" name="anio_' + ingr + '"  type="text"/></td>';
                        nuevaFila += '<td> <select id="s_programa_' + ingr + '" class="custom-select select-academics-data select_modal tabl_ing"></select> <input  id="id_programa_' + ingr + '" name="id_programa_' + ingr + '" class="ingresos_u" type="number" hidden></td>';
                        nuevaFila += '<td><select id="motivo_I' + ingr + '" class="custom-select select-academics-data select_modal tabl_ing"> <option value="1">Bajos académicos</option> <option value="2">Condición de salud</option> <option value="3">Fallecimiento</option> <option value="4">Condición económica</option> <option value="5">Condición de programa académico</option> <option value="6">Cambio de institución educativa</option> <option value="7">Cambio de ciudad</option> <option value="8">Retiro voluntario</option> <option value="9">Prefiero no decirlo</option> </select> <input id="motivo_' + ingr + '" name="motivo_' + ingr + '" class="ingresos_u" type="number" hidden> </td>';
                        nuevaFila += '<td> <button class="btn btn-danger remove_fila_ing" type="button" title="Eliminar persona" name="btn_delete_person" style="visibility:visible;"> X </button></td></tr>';
                        $("#table_ingresos").find("tbody").append(nuevaFila);
                        loadSelector($('#s_programa_' + ingr + ''), 'get_programas_academicos');
                        $("#anio_" + ingr).val(anioI)
                        setTimeout(() => {
                            $("#s_programa_" + ingr).val(progI)

                        }, 1000);
                        $("#motivo_I" + ingr).val(motivoI)
                        ing = ingr
                        initCambios('tabl_ing')
                    }

                    //Generar un arreglo apartir de la tabla-familia
                    function buildArr(table) {
                        var arr = [];
                        var values = table.find("tbody input").map(function() { return $(this).val(); }).get();
                        for (var i = 0; i < values.length; i = i + 2) {
                            var f = {
                                nombre: values[i],
                                rol: values[i + 1]
                            }
                            arr.push(f)
                        }
                        return arr;
                    }

                    /*Funcion para determinar si el usuario existe en la tabla talentospilos_usuario
                        y talentospilos_user_extended mediante el numero de cedula*/
                    $("#num_doc_ini").blur(function() {
                        if ($("#num_doc_ini").val().length > 2) {
                            hideAlerts('step1')
                            getStudentAses($("#num_doc_ini").val());
                            $("#step-1 :input").prop("disabled", false);
                            if (id_ases != null) {
                                getStudent($("#codigo_estudiantil").val());
                                getExistUserExtended(id_ases, id_moodle);
                                if (user_extended) {
                                    unsetData("step-2")
                                    unsetData("step-3")
                                    unsetData("div_educ_media")
                                    unsetData("step-4")
                                    unsetData("step-5")
                                    unsetData("step-6")
                                    disableMdliputs();
                                    ases_user = true;
                                    getStudentAses($("#num_doc_ini").val(), 1);
                                    setAsesData(data_ases)
                                } else {
                                    disableMdliputs();
                                    $("#num_doc_ini").val("");
                                    swal(
                                        "Waarning",
                                        "No existe relacion entre el numero de documento y el codigo del estudiante, intentalo nuevamente.",
                                        "warning"
                                    );
                                }
                            } else {
    
                                swal({
                                    title: "Warning",
                                    text: "Usuario inexistente en la base de datos, ¿Esta seguro de registrar este usuario?.",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonClass: "btn-success",
                                    confirmButtonText: "Confirmar",
                                    cancelButtonText: "Cancelar",
                                    closeOnConfirm: false,
                                    closeOnCancel: false
                                },
                                function(isConfirm) {
                                    if (isConfirm) {
                                        swal({title: "Confirmado",text: "",type: "success", timer: 1500});
                                        $("#step-1 :input").prop("disabled", false);
                                        disableMdliputs();
                                    } else {
                                        swal({title: "Cancelado",text: "Ingresa nuevamente el documento del estudiante",type: "error", timer: 1500});
                                        $("#num_doc_ini").val("");
                                    }
                                });
                            }
                        }
                    });

                    function getExistUserExtended(id, id_moodle) {
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'get_exist_user_extended',
                                "params": [id, id_moodle]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {

                                user_extended = JSON.parse(msg);



                            },
                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    //Funciòn que obtiene un estudiante mediante la cedula en la tabla talentospilos_usuario
                    //Nota: en este caso solo retorna el id
                    function getStudentAses(code, data) {
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'get_ases_user_id',
                                "params": code
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                var options = JSON.parse(msg);
                                if (data == null) {
                                    handleIdAses(options.id);
                                } else if (data == 1) {
                                    handleDataAses(options);
                                }

                            },
                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }
                    //Funciòn para poder retornar desde una peticòn AJAX
                    function handleIdAses(data) {
                        id_ases = data;

                    }

                    //Funciòn para poder retornar desde una peticòn AJAX
                    function handleDataAses(data) {
                        data_ases = data;
                    }

                    /*Funcion para setear los campos extraidos de la base de datos de la tabla talentospilos_usuario
                        La mayoria de campos de esta funcion se encuentran en la seccion 1 */
                    function setAsesData(data) {
                        var version_register = true;
                        for (var key in data) {
                            if (key == "json_detalle") {
                                version_register = false;
                            }
                        }

                        for (var key in data) {
                            if (data[key] != null) {
                                switch (key) {
                                    case "vive_con":
                                        let dataF = ""
                                        if (data[key].indexOf("Array") == -1) {
                                            dataF = JSON.parse(data[key]);
                                            let radioF = dataF.find(obj => obj.key_input === "vive")
                                            if (radioF != null) {
                                                setRadio(radioF.key_input, radioF.val_input)
                                            }
                                            $("#table_familia").find("tbody tr").remove();
                                            for (let i = 0; i < dataF.length - 1; i++) {
                                                let object = dataF[i];
                                                setTableFamily(object.nombre, object.rol, i);
                                            }


                                        } else {
                                            $("#table_familia").find("tbody tr").remove();
                                        }
                                        break;
                                    case "fecha_nac":
                                        var fecha = new Date(data[key]);
                                        var dia = ""
                                        var mes = ""

                                        if ((1 + fecha.getMonth()) < 10) {
                                            mes = "0" + (1 + fecha.getMonth())
                                        } else {
                                            mes = (1 + fecha.getMonth())
                                        }

                                        if (fecha.getDate() < 10) {
                                            dia = "0" + fecha.getDate()
                                        } else {
                                            dia = fecha.getDate()
                                        }

                                        $("#fecha_nac_modal").val(fecha.getFullYear() + "-" + mes + "-" + dia)
                                        calc_edad(data[key]);
                                        break;
                                    case "json_detalle":
                                        var dataArray = data[key];
                                        dataArray = JSON.parse(dataArray)
                                        for (let i = 0; i < dataArray.length; i++) {
                                            const object = dataArray[i];
                                            if (object.key_input_text != null) {
                                                $('#' + object.key_input_text).val(object.val_input_text)
                                            } else {
                                                if (object.key_input_number == 'sede') {
                                                    $('#select_sede').val(object.val_input_number)
                                                    $('#select_sede').trigger('change')
                                                } else if (object.key_input_number == 'orientacion_s') {
                                                    $('#select-orientacion').val(object.val_input_number)
                                                    $('#select-orientacion').trigger('change')
                                                } else {
                                                    $('#acompanamientos').val(object.val_input_number)
                                                    $('#acompanamientos').trigger('change')
                                                }
                                            }

                                            if (object.key_input != null) {
                                                setRadioCheck(object.key_input)
                                                showInput(object.key_input)
                                            }

                                            if (object.v_modal != null) {
                                                version_register = true;
                                            }
                                        }
                                        
                                        break;
                                    case "actividades_ocio_deporte":
                                        var dataArray = data[key];
                                        dataArray = JSON.parse(dataArray)
                                        for (let i = 0; i < dataArray.length; i++) {
                                            const object = dataArray[i];
                                            crearTags(object.name, object.value)
                                        }
                                        break;
                                    case "id_economics_data":
                                        id_economics_data = data[key]
                                        if (id_economics_data != null) {
                                            economics_data = true;
                                            discapacity = true;
                                            checkEcxist(id_ases, 'exist_academics_data')
                                            checkEcxist(id_ases, 'exist_health_data')
                                            checkEcxist(id_ases, 'exist_discapacity_data')
                                        }

                                        break;
                                    default:
                                        if (key == "estrato" || key == "puntaje_icfes" || key == "hijos" || key == "tel_res" || key == "celular" || key == "tel_acudiente"
                                            || key == "emailpilos") {
                                            $('#' + key + '_modal').val(data[key])
                                        }else{
                                            $('#' + key).val(data[key])
                                            $('#' + key).trigger('change')
                                        }
                                        break;
                                }
                            }
                        }
                        if (!version_register) {
                            swal({title: "",text: "Este usuario ha sido registrado con otro formulario",type: "info"});
                            clearForm()
                        }
                        cambios_s1 = false;
                    }

                    // Funcion para crearlos tags de las actividades y deportes extraidos de la bd
                    // Recibe el id y el array de actividades
                    function crearTags(id, array) {
                        if (id == "deportes") {
                            input_deportes.displayTags(array, $("#tags_deportes"))
                        } else {
                            input_actividades.displayTags(array, $("#tags_tiempo_libre"))
                        }
                    }

                    //Funcion para setear radio buttons, recibe el name del radio y su value para searlo
                    function setRadio(name, value) {
                        $("input:radio[name=" + name + "]").filter("[value=" + value + "]").attr("checked", true);
                    }

                    //Funcion para setear radio buttons y chceck box recibe el name del radio o ckeck box y lo setea
                    function setRadioCheck(name) {
                        $("input[name=" + name + "]").attr("checked", true);
                    }

                    //Funcion para determinar si existen los datos academicos, de discapacidad, o de salud
                    function checkEcxist(id_ases_user, request) {
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": request,
                                "params": id_ases_user
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                var options = JSON.parse(msg);
                                if (request == 'exist_academics_data') {
                                    academics_data = options
                                } else if (request == 'exist_health_data') {
                                    healthService = options
                                } else if (request == 'exist_discapacity_data') {
                                    healthCondition = options
                                }

                            },
                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    //Funcion para extraer datos economicos, academicos, de salud y discapacidad
                    // recibe el id del usuario y la funcion para determinar que datos extraer
                    function getDataBD(id_ases_user, request) {
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": request,
                                "params": id_ases_user
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                var options = JSON.parse(msg);
                                handleDataBD(options)

                            },
                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    function handleDataBD(data) {
                        general_data = data;
                    }


                    function setEconomicsData(data) {
                        let datos = Object.values(data)
                        for (let i = 2; i < datos.length; i++) {
                            var dataObj = JSON.parse(datos[i])
                            if (dataObj != null) {
                                if (Array.isArray(dataObj)) {
                                    for (let j = 0; j < dataObj.length; j++) {
                                        if (typeof(dataObj[j].key_input) !== 'undefined') {
                                            setRadio(dataObj[j].key_input, dataObj[j].val_input)
                                            if (dataObj[j].val_input == 0) {
                                                showInput(dataObj[j].key_input)
                                                $("#" + dataObj[j].key_input_text).val(dataObj[j].val_input_text)
                                            }
                                        } else {
                                            $("#" + dataObj[j].key_input_text).val(dataObj[j].val_input_text)
                                        }

                                    }
                                } else {

                                    if (dataObj.key_input === "beca") {
                                        setRadio(dataObj.key_input, dataObj.val_input)
                                        if (dataObj.val_input == 0) {
                                            showInput('beca')
                                            $("#" + dataObj.key_input_text).val(dataObj.val_input_text)
                                            $("#" + dataObj.key_input_number).val(dataObj.val_input_number)
                                        }
                                    } else {
                                        setRadio('solvencia_econo', dataObj)
                                    }



                                }
                            }
                        }
                    }

                    function setAcademicsData(data) {
                        for (const key in data) {

                            switch (key) {
                                case 'id':
                                    id_academics_data = data[key]
                                    break;
                                case 'otras_instituciones':

                                    let dataInsti = JSON.parse(data[key])

                                    for (let i = 0; i < dataInsti.length; i++) {
                                        $("#" + dataInsti[i].key_input_text).val(dataInsti[i].val_input_text)
                                        if (dataInsti[i].key_input != null) {
                                            setRadio(dataInsti[i].key_input, dataInsti[i].val_input)
                                        }
                                    }

                                    break;
                                case 'dificultades':

                                    let datadif = JSON.parse(data[key])

                                    for (let i = 0; i < datadif.length; i++) {
                                        $("#" + datadif[i].key_input_text).val(datadif[i].val_input_text)
                                    }

                                    break;
                                case 'datos_academicos_adicionales':

                                    let dataAdic = JSON.parse(data[key])
                                    $("#table_ingresos").find("tbody").find("tr").remove();
                                    for (let i = 0; i < dataAdic.length; i++) {

                                        if (dataAdic[i].name != null && dataAdic[i].name.indexOf("anio") != -1) {
                                            setTableIng(i + 1, dataAdic[i].value, dataAdic[i + 1].value, dataAdic[i + 2].value)
                                        }

                                        $("#" + dataAdic[i].key_input_text).val(dataAdic[i].val_input_text)
                                        $("#" + dataAdic[i].key_input_date).val(dataAdic[i].val_input_date)
                                        if (dataAdic[i].key_input != null) {
                                            setRadio(dataAdic[i].key_input, dataAdic[i].val_input)
                                        }
                                    }
                                    setTimeout(() => {
                                        initSelect2()
                                    }, 1000);
                                    break;
                                default:
                                    if (key == 'observaciones') {
                                        $("#" + key + "_modal").val(data[key])
                                    }else{
                                        $("#" + key).val(data[key])
                                    }
                                    break;
                            }

                        }
                    }

                    function setDiscapacityData(data) {
                        for (const key in data) {

                            if (key == 'id') {
                                id_discapacity_data = data[key];
                            }

                            let dataArray = JSON.parse(data[key])

                            for (let i = 0; i < dataArray.length; i++) {

                                if (dataArray[i].val_input === "on") {
                                    setRadioCheck(dataArray[i].key_input)
                                } else {
                                    setRadio(dataArray[i].key_input, dataArray[i].val_input)
                                    if (dataArray[i].val_input == 0) {
                                        showInput(dataArray[i].key_input)
                                        $("#" + dataArray[i].key_input_text).val(dataArray[i].val_input_text)
                                    }
                                }

                                if (dataArray[i].key_input_text !== "doc_localizacion" && dataArray[i].key_input_text !== "certi_disca") {
                                    $("#" + dataArray[i].key_input_text).val(dataArray[i].val_input_text)
                                    $("#" + dataArray[i].key_input_date).val(dataArray[i].val_input_date)
                                }

                            }
                        }
                    }

                    function setHealtService(data) {
                        for (const key in data) {
                            switch (key) {
                                case 'id':
                                    id_healt_service = data[key]
                                    break;
                                case 'servicio_salud_vinculado':
                                    $("#EPS").val(data[key])
                                    break;
                                default:
                                    let dataArray = JSON.parse(data[key])
                                    for (let i = 0; i < dataArray.length; i++) {

                                        if (dataArray[i].val_input === "on") {
                                            setRadioCheck(dataArray[i].key_input)
                                        } else {
                                            setRadio(dataArray[i].key_input, dataArray[i].val_input)
                                        }

                                        $("#" + dataArray[i].key_input_text).val(dataArray[i].val_input_text)
                                        $("#" + dataArray[i].key_input_number).val(dataArray[i].val_input_number)
                                        $("#" + dataArray[i].key_input_date).val(dataArray[i].val_input_date)
                                    }
                                    break;
                            }
                        }
                    }

                    function showInput(nameRadio) {

                        switch (nameRadio) {
                            case 'solvencia_econo':
                                var s = $("#permanencia" + " .otro").parent().next();
                                s.attr('hidden', false);
                                s.removeClass("otros");
                                break;
                            case 'ayuda_transporte':
                                var s = $("#set-desplazamiento" + " .otro").parent().next();
                                s.attr('hidden', false);
                                s.removeClass("otros");
                                break;
                            case 'beca':
                                var s = $("#set-beca" + " .otro").parent().next();
                                s.attr('hidden', false);
                                s.removeClass("otros");
                                break;
                            case 'participacion':
                                var s = $("#set-participacion" + " .otro").parent().next();
                                s.attr('hidden', false);
                                s.removeClass("otros");
                                break;
                            case 'condicion':
                                var s = $("#set-salud" + " .otro").parent().next();
                                s.attr('hidden', false);
                                s.removeClass("otros");
                                break;
                            case 'apoyo_partic':
                                var s = $("#set-apoyo" + " .otro").parent().next();
                                s.attr('hidden', false);
                                s.removeClass("otros");
                                break;
                            case 'discapacidad':
                                var s = $("#set-certificado-discapacidad" + " .otro").parent().next();
                                s.attr('hidden', false);
                                s.removeClass("otros");
                                break;
                            case 'identidad_genero':
                                var s = $("#set-identidad-gen" + " .otro").parent().next();
                                s.attr('hidden', false);
                                s.removeClass("otros");
                                break;
                            case 'orientacion_sexual':
                                var s = $("#set-orientacion" + " .otro").parent().next();
                                s.attr('hidden', false);
                                s.removeClass("otros");
                                break;
                            default:
                                break;
                        }
                    }

                    function hideInput(nameRadio) {

                        switch (nameRadio) {
                            case 'solvencia_econo':
                                var s = $("#permanencia" + " .otro").parent().next();
                                s.attr('hidden', true);
                                s.addClass("otros");
                                break;
                            case 'ayuda_transporte':
                                var s = $("#set-desplazamiento" + " .otro").parent().next();
                                s.attr('hidden', true);
                                s.addClass("otros");
                                break;
                            case 'beca':
                                var s = $("#set-beca" + " .otro").parent().next();
                                s.attr('hidden', true);
                                s.addClass("otros");
                                break;
                            case 'participacion':
                                var s = $("#set-participacion" + " .otro").parent().next();
                                s.attr('hidden', true);
                                s.addClass("otros");
                                break;
                            case 'condicion':
                                var s = $("#set-salud" + " .otro").parent().next();
                                s.attr('hidden', true);
                                s.addClass("otros");
                                break;
                            case 'apoyo_partic':
                                var s = $("#set-apoyo" + " .otro").parent().next();
                                s.attr('hidden', true);
                                s.addClass("otros");
                                break;
                            case 'discapacidad':
                                var s = $("#set-certificado-discapacidad" + " .otro").parent().next();
                                s.attr('hidden', true);
                                s.addClass("otros");
                                break;
                            case 'identidad_genero':
                                var s = $("#set-identidad-gen" + " .otro").parent().next();
                                s.attr('hidden', true);
                                s.addClass("otros");
                                break;
                            case 'orientacion_sexual':
                                var s = $("#set-orientacion" + " .otro").parent().next();
                                s.attr('hidden', true);
                                s.addClass("otros");
                                break;
                            default:
                                break;
                        }
                    }

                    //Funciòn que obtiene un estudiante mediante el codigo
                    //Nota: en este caso solo retorna el id
                    function getStudent(code) {
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'get_user',
                                "params": code
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                var options = JSON.parse(msg);

                                handleData(options.id);

                            },
                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }
                    //Funciòn para poder retornar desde una peticòn AJAX
                    function handleData(data) {
                        id_moodle = data;
                    }

                    //Funciòn que obtiene el programa academico mediante el id
                    //Nota: en este caso solo retorna el codigo de programa
                    function getAcademicProgram(id) {
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'get_program',
                                "params": id
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                var options = JSON.parse(msg);

                                handleProgram(options.cod_univalle);

                            },
                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }
                    //Funciòn para poder retornar desde una peticòn AJAX
                    function handleProgram(data) {
                        cod_programa = data;
                    }
                    //Funciòn que carga los selectores, para el registro de un nuevo estudiante a acompañar
                    //select, hace referencia al id del select dònde se cargarà la info
                    //f, hace referencia al nombre de la funciòn definida en studentprofile_main.php, que recuperarà la info
                    function loadSelector(select, f, prog_act) {
                        var param = "1";
                        if (prog_act != null) {
                            param = prog_act;
                        }

                        $.ajax({
                            type: "POST",
                            data: JSON.stringify({
                                "function": f,
                                "params": param
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                var options = JSON.parse(msg);
                                for (var o in options) {
                                    select.append(
                                        "<option value='" + options[o].id + "'>" + Object.values(options[o])[1] + "</option>"
                                    )
                                }

                            },
                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    function unsetData(step) {
                        $("#" + step).find('input').each(function() {

                            var type = $(this).attr('type');

                            switch (type) {
                                case 'text':
                                    if ($(this).val() != "") {
                                        $(this).val("")
                                    }
                                    break;

                                case 'number':
                                    if ($(this).val() != "") {
                                        $(this).val("")
                                    }
                                    break;
                                case 'date':
                                    if ($(this).val() != "") {
                                        $(this).val("")
                                    }
                                    break;
                                case 'radio':

                                    let name = $(this).attr("name")
                                    if ($(this).is(":checked")) {
                                        $(this).attr('checked', false);
                                    }

                                    if ($(this).hasClass("otro")) {
                                        hideInput(name)
                                    }

                                    break;
                                case 'checkbox':
                                    if ($(this).is(":checked")) {
                                        let name = $(this).attr("name")
                                        $("input[name=" + name + "]").attr("checked", false);
                                    }
                                case 'tel':
                                    if ($(this).val() != "") {
                                        $(this).val("")
                                    }
                                    break;
                                    break;

                            }

                        })

                        $("#" + step).find('textarea').each(function() {
                            if ($(this).val() != "") {
                                $(this).val("")
                            }
                        })
                        $("#" + step).find('select').each(function() {
                            if ($(this).val() != "") {
                                $(this).val(1)
                            }
                        })

                    }

                    function getHealtService() {
                        var arr_service_disca = [];
                        var arr_insert = [];

                        buildJsonObject('regimen_salud', arr_insert);
                        buildJson('regimen_salud', arr_insert, arr_service_disca);
                        arr_insert = [];

                        buildJsonObject('data_sisben', arr_insert);
                        buildJsonTArea('observ_sisben', arr_insert);

                        buildJsonObject('serv_adicionales', arr_insert);
                        buildJsonObject('conclusion_jornada', arr_insert);
                        buildJsonTArea('conclusion', arr_insert);

                        buildJsonObject('json_detalle', arr_insert);
                        buildJson('datos_salud_adicionales', arr_insert, arr_service_disca);
                        arr_insert = [];

                        buildJsonObject('serv_usuario', arr_insert);
                        buildJson('servicios_usados', arr_insert, arr_service_disca);
                        arr_insert = [];



                        return arr_service_disca;
                    }



                    function getDiscapacityData() {
                        var arr_discapacidad = [];
                        var arr_insert = [];

                        buildJsonObject('percepcion_discapacidad', arr_insert);
                        buildJsonTArea('desc_discap', arr_insert);
                        buildJson('percepcion_disca', arr_insert, arr_discapacidad);
                        arr_insert = [];

                        buildJsonObject('diagnosticos', arr_insert);
                        buildJson('diagnosticos', arr_insert, arr_discapacidad);
                        arr_insert = [];

                        buildJsonObject('condicion_salud', arr_insert);
                        buildJsonTArea('rel_cond_salud', arr_insert);
                        buildJson('condicion_salud', arr_insert, arr_discapacidad);
                        arr_insert = [];

                        buildJsonObject('medicamentos', arr_insert);
                        buildJson('medicamentos', arr_insert, arr_discapacidad);
                        arr_insert = [];

                        buildJsonObject('cambio_tratamiento', arr_insert);
                        buildJsonTArea('obs_cambio', arr_insert);
                        buildJson('cambios_tratamiento', arr_insert, arr_discapacidad);
                        arr_insert = [];

                        buildJsonObject('disca_por_municipio', arr_insert);
                        buildJsonTArea('doc_localizacion', arr_insert);
                        buildJson('discapacidad_municipio', arr_insert, arr_discapacidad);
                        arr_insert = [];

                        buildJsonObject('certificado_disca', arr_insert);
                        buildJsonTArea('certi_disca', arr_insert);
                        buildJson('certificado_discapacidad', arr_insert, arr_discapacidad);
                        arr_insert = [];

                        buildJsonObject('datos_invalidez', arr_insert);
                        buildJson('certificado_invalidez', arr_insert, arr_discapacidad);
                        arr_insert = [];

                        buildJsonObject('cond_org_sist', arr_insert);
                        buildJson('condicion_organos', arr_insert, arr_discapacidad);
                        arr_insert = [];

                        buildJsonObject('dif_permanente', arr_insert);
                        buildJson('dificultad_permanente', arr_insert, arr_discapacidad);
                        arr_insert = [];

                        buildJsonObject('set-participacion', arr_insert);
                        buildJsonTArea('text_participacion', arr_insert);
                        buildJsonObject('set-apoyo', arr_insert);
                        buildJsonTArea('text_apoyo_partic', arr_insert);
                        buildJson('participacion_estudiantil', arr_insert, arr_discapacidad);
                        arr_insert = [];

                        buildJsonObject('asoc_disca', arr_insert);
                        buildJson('organizacion_disca', arr_insert, arr_discapacidad);
                        arr_insert = [];

                        buildJsonObject('apoyo_recibido', arr_insert);
                        buildJson('apoyo_dicapacidad', arr_insert, arr_discapacidad);
                        arr_insert = [];



                        return arr_discapacidad;
                    }

                    // Funciòn que recolecta los datos acadèmicos y los empaqueta en un arr
                    function getAcademicsData() {

                        var json_insert = {};

                        //Recolectar valores de los selects
                        var selects = $(".select-academics-data").map(function() { return $(this).attr("id"); }).get()
                        var inputs = $(".select-academics-data").map(function() { return $(this).next().next().attr("id") }).get()
                        for (var i = 0; i < inputs.length; i++) {

                            $("#" + inputs[i]).val($("#" + selects[i] + " option:selected").val());

                        }

                        var arr_academic = [];
                        var arr_estudios_realizados = [];

                        var arr_dificultades = [];
                        buildJsonObject('dif_educ_media', arr_dificultades);
                        buildJsonObject('dif_educ_superior', arr_dificultades);
                        buildJsonObject('dif_educ_paralelo', arr_dificultades);
                        json_insert.key_input = "dificultades"
                        json_insert.val_input = arr_dificultades;
                        arr_dificultades = [];
                        arr_dificultades.push(json_insert);
                        json_insert = {};

                        var datos_adicionales = [];
                        var add_data = $(".ingresos_u").serializeArray();
                        buildJsonObject('jornada', add_data);
                        buildJsonObject('p_ingreso', add_data);
                        buildJson('datos_academicos_adicionales', add_data, datos_adicionales);


                        buildJsonObject('div_tipo_institucion', arr_estudios_realizados);
                        buildJsonObject('div_tipo_institucion_superior', arr_estudios_realizados);
                        buildJsonObject('div_tipo_institucion_paralelo', arr_estudios_realizados);
                        json_insert.key_input = "otras_instituciones"
                        json_insert.val_input = arr_estudios_realizados;
                        arr_estudios_realizados = [];
                        arr_estudios_realizados.push(json_insert);
                        json_insert = {};


                        arr_academic.push(datos_adicionales);
                        arr_academic.push(arr_estudios_realizados);
                        arr_academic.push(arr_dificultades);
                        return arr_academic;
                    }
                    // Funciòn que recolecta los datos socio-econòmicos y los empaqueta en un arr
                    function getEconomicsData() {
                        var arr = [];
                        var arr_add = [];
                        var nivel_educ_padres = [];
                        var ocupacion_padres = [];
                        var situa_laboral_padres = [];
                        var arr_proyecto_vida = [];
                        var arr_dat_adicionales = [];

                        var json_proyecto = {};
                        var json_educ_padres = {};
                        var json_ocupacion_padres = {};
                        var json_laboral_padres = {};

                        buildJsonObject('set-sostenimiento', arr_dat_adicionales);
                        buildJsonObject('otro_ingreso', arr_dat_adicionales);
                        buildJson('datos_economicos_adicionales', arr_dat_adicionales, arr);

                        buildJsonObject('set-permanencia', arr);
                        buildJsonObject('set-beca', arr);

                        buildJsonObject('set-materiales', arr_add);
                        buildJsonObject('set-valor-materiales', arr_add);
                        arr.push(arr_add);
                        arr_add = [];

                        buildJsonObject('set-desplazamiento', arr_add);
                        buildJsonObject('set-valor-desplazamiento', arr_add);
                        arr.push(arr_add);

                        buildJsonObject('row_madre_academic', nivel_educ_padres);
                        buildJsonObject('row_padre_academic', nivel_educ_padres);
                        buildJsonObject('row_otro_academic', nivel_educ_padres);

                        buildJsonObject('row_madre_ocupacion', ocupacion_padres);
                        buildJsonObject('row_padre_ocupacion', ocupacion_padres);
                        buildJsonObject('row_otro_ocupacion', ocupacion_padres);

                        buildJsonObject('row_madre_laboral', situa_laboral_padres);
                        buildJsonObject('row_padre_laboral', situa_laboral_padres);
                        buildJsonObject('row_otro_laboral', situa_laboral_padres);

                        buildJsonTArea('motivo_ingreso', arr_proyecto_vida);
                        buildJsonTArea('expecativas_carrera', arr_proyecto_vida);
                        buildJsonTArea('expectativas_graduarse', arr_proyecto_vida);

                        json_proyecto.key_input = "expectativas_laborales";
                        json_proyecto.val_input = arr_proyecto_vida;

                        json_ocupacion_padres.key_input = "ocupacion_padres";
                        json_ocupacion_padres.val_input = ocupacion_padres;

                        json_educ_padres.key_input = "nivel_educ_padres";
                        json_educ_padres.val_input = nivel_educ_padres;

                        json_laboral_padres.key_input = "situa_laboral_padres";
                        json_laboral_padres.val_input = situa_laboral_padres;

                        arr.push(json_educ_padres);
                        arr.push(json_ocupacion_padres);
                        arr.push(json_laboral_padres);
                        arr.push(json_proyecto);

                        return arr;

                    }

                    //Funcion para crear el json a partir de los TextArea
                    function buildJsonTArea(id_text, arr) {
                        json_insert = {};

                        if ($("#" + id_text).val() != null) {
                            if ($("#" + id_text).val().length >= 3) {
    
                                json_insert.key_input_text = $("#" + id_text).attr("id");
                                json_insert.val_input_text = $("#" + id_text).val();
                                arr.push(json_insert);
                                json_insert = {};
                            }
                        }


                    }

                    //Funcion para crear un json haciendo uso de dos arrays, el value el cual sera insertado en el json 
                    // y el arr el cual es el arreglo general al cual se le hara push
                    function buildJson(key, value, arr) {
                        json_insert = {};
                        json_insert.key_input = key;
                        json_insert.val_input = value;
                        arr.push(json_insert);
                        json_insert = {};
                    }

                    //Funciòn que obtiene todos los id y valores de un conjunto de inputs, crea un objeto y lo añade a un arreglo
                    //id_container, hace referencia al contenedor donde estan los inputs
                    //arr, hace referencia al arreglo donde se alamacenaràn los objetos
                    function buildJsonObject(id_container, arr) {
                        $("#" + id_container).find('input').each(function() {
                            json_object = {};
                            var type = $(this).attr('type');

                            switch (type) {
                                case 'text':
                                    if ($(this).hasClass("otros") || $(this).val() == "") {
                                        break;
                                    } else {
                                        json_object.key_input_text = $(this).attr("id");
                                        json_object.val_input_text = $(this).val();
                                        arr.push(json_object);

                                    }
                                    break;

                                case 'number':
                                    if ($(this).hasClass("otros") || $(this).val() == "") {
                                        break;
                                    } else {
                                        json_object.key_input_number = $(this).attr("id");
                                        json_object.val_input_number = $(this).val();
                                        arr.push(json_object);
                                        break;
                                    }

                                case 'date':
                                    if ($(this).hasClass("otros") || $(this).val() == "") {
                                        break;
                                    } else {
                                        json_object.key_input_date = $(this).attr("id");
                                        json_object.val_input_date = $(this).val();
                                        arr.push(json_object);
                                        break;
                                    }
                                case 'radio':
                                    var o = getValue($(this));
                                    if (o.id !== "") {
                                        if (o.id1 != "") {
                                            json_object.key_input = $(this).attr("name");
                                            json_object.val_input = $(this).val();
                                            if (o.type == "text") {
                                                json_object.key_input_text = o.id;
                                                json_object.val_input_text = o.val;
                                                json_object.key_input_number = o.id1;
                                                json_object.val_input_number = o.val1;
                                            }
                                            arr.push(json_object);
                                        } else {
                                            json_object.key_input = $(this).attr("name");
                                            json_object.val_input = $(this).val();
                                            if (o.type == "text") {
                                                json_object.key_input_text = o.id;
                                                json_object.val_input_text = o.val;
                                            } else if (o.type == "number") {
                                                json_object.key_input_number = o.id;
                                                json_object.val_input_number = o.val;
                                            }
                                            arr.push(json_object);
                                        }

                                    } else {
                                        break;
                                    }
                                    break;
                                case 'checkbox':
                                    var o = getValue($(this));
                                    if (o.id !== "") {
                                        json_object.key_input = $(this).attr("id");
                                        json_object.val_input = 'on';
                                        if (o.type == "text") {
                                            json_object.key_input_text = o.id;
                                            json_object.val_input_text = o.val;
                                        } else if (o.type == "number") {
                                            json_object.key_input_number = o.id;
                                            json_object.val_input_number = o.val;
                                        }
                                        arr.push(json_object);

                                    } else {
                                        break;
                                    }
                                    break;


                                default:
                                    json_object.key_input = 0;
                                    json_object.val_input = 0;
                                    break;

                            }

                        })
                    }


                    //Funciòn que retorna el valor, tipo y el id o valor y id o 0, del radio checked de un c input de tipo radio,
                    //input, hace referencia al input d el cuàl se desea obtener su valor, id y tipo  
                    function getValue(input) {
                        var o = {};
                        if (input.is(":checked")) {
                            if (input.hasClass("otro")) {
                                var d = input.parent().next().find('input');
                                var d1 = input.parent().next().children('input:last');
                                o.id = d.attr('id');
                                o.val = d.val();
                                o.type = d.attr("type");
                                if (d1.attr('id') != o.id) {
                                    o.id1 = d1.attr('id');
                                    o.val1 = d1.val();
                                    o.type1 = d1.attr("type");
                                }
                                return o;
                            } else {
                                o.id = input.attr("name");
                                o.val = input.val();
                                return o;
                            }
                        } else {
                            o.id = "";
                            o.val = "";
                            return o;
                        }
                    }

                    //Funciòn que permite ocultar y desocultar un input, si el radio que esta check tiene la clase otro
                    //setOptions, hace referencia al id del contenedor donde estan los input de tipo radio
                    //nameRadio, hace referencia al atributo name del conjunto de radios 
                    function hideAndShow(setOptions, nameRadio) {
                        $("#" + setOptions).find("[name=" + nameRadio + "]").on('click', function() {

                            var s = $("#" + setOptions + " .otro").parent().next();

                            if ($(this).hasClass("otro")) {
                                s.attr('hidden', false);
                                s.removeClass("otros");
                            } else {
                                s.attr('hidden', true);
                                s.addClass("otros");
                            }
                        })
                    }

                    function hideAndShowCheckB(setOptions, nameRadio) {
                        $("#" + setOptions).find("[name=" + nameRadio + "]").on('change', function() {
  
                            var s = $("#" + setOptions + " .otro").parent().next();
  
                            if ($(this).hasClass("otro") && $(this).prop('checked')) {
                                s.attr('hidden', false);
                                s.removeClass("otros");
                            } else {
                                s.attr('hidden', true);
                                s.addClass("otros");
                            }
                        })
                    }
                    
                    //Funcion para detectar los cambios de la tabla familia e ingresos
                    function initCambios(nameClass) {
                        $('.'+nameClass).each(function() {
                            var elem = $(this);
    
                            // Save current value of element
                            elem.data('oldVal', elem.val());
    
                            // Look for changes in the value
                            elem.bind("propertychange change input paste", function(event) {
                                // If value has changed...
                                if (elem.data('oldVal') != elem.val()) {
                                    // Updated stored value
                                    elem.data('oldVal', elem.val());
    
                                    // Do action
                                    (nameClass === 'tabl_famiia') ? cambios_s2 = true : cambios_s3 = true
                                    
                                }
                            });
    
                            elem.bind('keypress', function(e) {
    
                                var elem_id = elem.attr("id")
                                detValidacion(e, elem_id)
                            });
    
                        });
                    }

                    //Validador si hay cambios en la seccion 1 del formulario, para actualizar en la BD   
                    $('.step1').each(function() {
                        var elem = $(this);
                        // Save current value of element
                        elem.data('oldVal', elem.val());

                        // Look for changes in the value
                        elem.bind("propertychange change input paste", function(event) {



                            // If value has changed...
                            if (elem.data('oldVal') != elem.val()) {
                                // Updated stored value
                                elem.data('oldVal', elem.val());

                                // Do action
                                cambios_s1 = true;
                            }
                        });

                        elem.bind('keypress', function(e) {

                            var elem_id = elem.attr("id")
                            detValidacion(e, elem_id)
                        });
                    });

                    //Validador si hay cambios en la seccion 2 del formulario, para actualizar en la BD   
                    $('.step2').each(function() {
                        var elem = $(this);

                        // Save current value of element
                        elem.data('oldVal', elem.val());

                        // Look for changes in the value
                        elem.bind("propertychange change input paste", function(event) {
                            // If value has changed...
                            if (elem.data('oldVal') != elem.val()) {
                                // Updated stored value
                                elem.data('oldVal', elem.val());

                                // Do action
                                cambios_s2 = true;
                            }
                        });

                        elem.bind('keypress', function(e) {

                            var elem_id = elem.attr("id")
                            detValidacion(e, elem_id)
                        });

                    });

                    //Validador si hay cambios en la seccion 3 del formulario, para actualizar en la BD   
                    $('.step3').each(function() {
                        var elem = $(this);

                        // Save current value of element
                        elem.data('oldVal', elem.val());

                        // Look for changes in the value
                        elem.bind("propertychange change input paste", function(event) {
                            // If value has changed...
                            if (elem.data('oldVal') != elem.val()) {
                                // Updated stored value
                                elem.data('oldVal', elem.val());

                                // Do action
                                cambios_s3 = true;
                            }
                        });

                        elem.bind('keypress', function(e) {

                            var elem_id = elem.attr("id")
                            detValidacion(e, elem_id)
                        });

                    });

                    //Validador si hay cambios en la seccion 3 del formulario, para actualizar en la BD   
                    $('.step4').each(function() {
                        var elem = $(this);

                        // Save current value of element
                        elem.data('oldVal', elem.val());

                        // Look for changes in the value
                        elem.bind("propertychange change input paste", function(event) {
                            // If value has changed...
                            if (elem.data('oldVal') != elem.val()) {
                                // Updated stored value
                                elem.data('oldVal', elem.val());

                                // Do action
                                cambios_s4 = true;
                            }
                        });
                    });

                    //Validador si hay cambios en la seccion 3 del formulario, para actualizar en la BD   
                    $('.step5').each(function() {
                        var elem = $(this);

                        // Save current value of element
                        elem.data('oldVal', elem.val());

                        // Look for changes in the value
                        elem.bind("propertychange change input paste", function(event) {
                            // If value has changed...
                            if (elem.data('oldVal') != elem.val()) {
                                // Updated stored value
                                elem.data('oldVal', elem.val());

                                // Do action
                                cambios_s5 = true;
                            }
                        });
                    });

                    //Validador si hay cambios en la seccion 3 del formulario, para actualizar en la BD   
                    $('.step6').each(function() {
                        var elem = $(this);

                        // Save current value of element
                        elem.data('oldVal', elem.val());

                        // Look for changes in the value
                        elem.bind("propertychange change input paste", function(event) {
                            // If value has changed...
                            if (elem.data('oldVal') != elem.val()) {
                                // Updated stored value
                                elem.data('oldVal', elem.val());

                                // Do action
                                cambios_s6 = true;
                            }
                        });
                    });

                    //Validar si hay cambios en los radio buttons para actualizar en la BD
                    $('input[type="radio"]').on('change', function() {
                        if ($(this).hasClass("step1")) {
                            cambios_s1 = true;
                        } else if ($(this).hasClass("step2")) {
                            cambios_s2 = true;
                        } else if ($(this).hasClass("step3")) {
                            cambios_s3 = true;
                        } else if ($(this).hasClass("step4")) {
                            cambios_s4 = true;
                        } else if ($(this).hasClass("step5")) {
                            cambios_s5 = true;
                        } else if ($(this).hasClass("step6")) {
                            cambios_s6 = true;
                        } else {

                        }

                    });

                    $('#codigo_estudiantil').each(function() {
                        var elem = $(this);

                        elem.bind('keypress', function(e) {

                            var elem_id = elem.attr("id")
                            detValidacion(e, elem_id)
                        });
                    });

                    //Funcion para detectar el campo y determinar que tipo de restriccion realizar
                    // Si solo deja ingresar numeros o letras
                    function detValidacion(event, input_id) {
                        switch (input_id) {
                            case 'codigo_estudiantil':
                                soloNumeros(event)
                                break;
                            case 'num_doc_ini':
                                soloNumeros(event)
                                break;
                            case 'nombre':
                                soloLetras(event)
                                break;
                            case 'apellido':
                                soloLetras(event)
                                break;
                            case 'celular_modal':
                                soloNumeros(event)
                                break;
                            case 'tel_res_modal':
                                soloNumeros(event)
                                break;
                            case 'tel_ini':
                                soloNumeros(event)
                                break;
                            case 'tel_acudiente_modal':
                                soloNumeros(event)
                                break;
                            case 'tel_cont_2':
                                soloNumeros(event)
                                break;
                            case 'estrato_modal':
                                soloNumeros(event)
                                break;
                            case 'hijos_modal':
                                soloNumeros(event)
                                break;
                            case 'semestre_actual':
                                soloNumeros(event)
                                break;
                            case 'anio_ingreso':
                                soloNumeros(event)
                                break;
                            case 'puntaje_icfes_modal':
                                soloNumeros(event)
                                break;

                            default:
                                break;
                        }
                    }

                    //Funcion para evitar que ingresen numeros en campos solo de texto
                    function soloLetras(e) {
                        key = e.keyCode || e.which;
                        tecla = String.fromCharCode(key).toLowerCase();
                        letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
                        especiales = [8, 37, 39, 46];

                        tecla_especial = false
                        for (var i in especiales) {
                            if (key == especiales[i]) {
                                tecla_especial = true;
                                break;
                            }
                        }

                        if (letras.indexOf(tecla) == -1 && !tecla_especial) {
                            e.preventDefault()
                        }
                    }

                    //Funcion para evitar que ingresen letras en campos que son solo numericos
                    function soloNumeros(e) {
                        key = e.keyCode || e.which;
                        tecla = String.fromCharCode(key).toLowerCase();
                        letras = " 0123456789";
                        especiales = [8, 37, 39];

                        tecla_especial = false
                        for (var i in especiales) {
                            if (key == especiales[i]) {
                                tecla_especial = true;
                                break;
                            }
                        }
                        if (letras.indexOf(tecla) == -1 && !tecla_especial) {
                            e.preventDefault()
                        }
                    }

                    //Validar si hay cambios en los checkbox buttons para actualizar en la BD
                    $('input[type="checkbox"]').on('change', function() {
                        if ($(this).hasClass("step4")) {
                            cambios_s4 = true;
                        } else if ($(this).hasClass("step5")) {
                            cambios_s5 = true;
                        } else if ($(this).hasClass("step6")) {
                            cambios_s6 = true;
                        } else {

                        }

                    });

                    $('#emailpilos_modal').on('keyup', function() {
                        var re = /([A-Z0-9a-z_-][^@])+?@[^$#<>?]+?\.[\w]{2,4}/.test(this.value);
                        if (!re) {
                            $('#errorEPilos').attr('hidden', false);
                            $('#successEPilos').attr('hidden', true);
                        } else {
                            $('#errorEPilos').attr('hidden', true);
                            $('#successEPilos').attr('hidden', false);
                            setTimeout(() => {
                                $('#successEPilos').attr('hidden', true);
                            }, 3000);
                        }
                    })

                    $('#emailinstitucional').on('keyup', function() {
                        var re = /([A-Z0-9a-z_-][^@])+?@[^$#<>?]+?\.[\w]{2,4}/.test(this.value);
                        if (!re) {
                            $('#errorEInst').attr('hidden', false);
                            $('#successEInst').attr('hidden', true);
                        } else {
                            $('#errorEInst').attr('hidden', true);
                            $('#successEInst').attr('hidden', false);
                            setTimeout(() => {
                                $('#successEInst').attr('hidden', true);
                            }, 3000);
                        }
                    })

                    ///Funcion para validar los campos requeridos en el step1
                    function validarCamposS1(step) {
                        var emptyS = false;
                        $('.' + step + '[required]:visible').each(function() {
                            var elem = $(this);
                            if (elem.val() == "") {
                                emptyS = true;
                                let alerta = $(this).next()
                                alerta.attr('hidden', false);
                            } else {
                                let alerta = $(this).next()
                                if ($(this).next().hasClass("alert")) {
                                    alerta.attr('hidden', true);
                                }
                            }
                        });

                        return emptyS;
                    }

                    ///Funcion para validar los campos requeridos en el step2 y step3
                    function validarCamposS2_3(step) {
                        var emptyS = false;
                        $('.' + step + '[required]').each(function() {
                            var elem = $(this);
                            if (elem.attr("type") == "radio") {
                                var name = elem.attr("name");
                                if (!$('input:radio[name="' + name + '"]').is(":checked")) {
                                    emptyS = true;
                                    let alerta = $(this).prev()
                                    alerta.attr('hidden', false);
                                } else {
                                    let alerta = $(this).prev()
                                    if ($(this).prev().hasClass("alert")) {
                                        alerta.attr('hidden', true);
                                    }
                                }
                            } else {
                                if (elem.val() == "") {
                                    emptyS = true;
                                    let alerta = $(this).next()
                                    alerta.attr('hidden', false);
                                } else {
                                    let alerta = $(this).next()
                                    if ($(this).next().hasClass("alert")) {
                                        alerta.attr('hidden', true);
                                    }
                                }
                            }
                        });

                        return emptyS;
                    }

                    function hideAlerts(step) {
                        $('.' + step + '[required]:visible').each(function() {

                            let alerta = $(this).next()
                            if ($(this).next().hasClass("alert")) {
                                alerta.attr('hidden', true);
                            }

                        });
                    }

                    /*
                    Funcion que determina el cambio entre paginas
                    */
                    $("#smartwizard").on("leaveStep", function(e, anchorObject, currentStepIndex, stepDirection, nextStepIndex) {
                        if (stepDirection === "forward") {
                            document.getElementById("modal_acomp").scrollIntoView({behavior: 'auto'});
                            getSelectValues();
                            switch (currentStepIndex) {
                                case 0:
                                    if (validarCamposS1("step1")) {
                                        swal("Oops!", "Rellena todos los campos necesarios para poder avanzar", "warning")
                                        return false;
                                    } else {
                                        //Validacion para la determinar la creacion en mdl_user
                                        if (mdl_user) {
                                            getAcademicProgram($('#programa').val());
                                            save_mdl_user();
                                            mdl_user = false;
                                        }

                                        // Validacion para determinar si es un insert o update
                                        //  en la tabla talentospilos_usuario
                                        if (!ases_user) {
                                            getStudent($("#codigo_estudiantil").val());
                                            save_data();
                                            ases_user = true;
                                            cambios_s1 = false;
                                        } else if (cambios_s1) {
                                            getStudentAses($("#num_doc_ini").val());
                                            update_ases_data();
                                            cambios_s1 = false;
                                        }

                                        if (economics_data) {
                                            getStudentAses($("#num_doc_ini").val());
                                            getDataBD(id_ases, 'get_economics_data')
                                            setEconomicsData(general_data)
                                        }                                          
                                    }
                                    break;
                                case 1:
                                    if (validarCamposS2_3("step2")) {
                                        swal("Oops!", "Rellena todos los campos necesarios para poder avanzar", "warning")
                                        return false;
                                    } else {
                                        $("#table_ingresos").find("tbody").find("tr").remove();
                                        if (!economics_data) {
                                            save_economics_data();
                                            save_data_user_step2();
                                            economics_data = true;
                                            cambios_s2 = false;
                                        } else if (cambios_s2) {
                                            save_data_user_step2();
                                            update_economics_dt()
                                            cambios_s2 = false;
                                        }

                                        if (academics_data) {
                                            getStudentAses($("#num_doc_ini").val());
                                            getDataBD(id_ases, 'get_academics_data')
                                            setAcademicsData(general_data);
                                            cambios_s3 = false;
                                        }
                                    }
                                    break;
                                case 2:
                                    if (validarCamposS2_3("step3")) {
                                        swal("Oops!", "Rellena todos los campos necesarios para poder avanzar", "warning")
                                        return false;
                                    } else {
                                        if (!academics_data) {
                                            save_data_user_step3();
                                            save_academics_data();
                                            academics_data = true;
                                            cambios_s3 = false;
                                        } else if (cambios_s3) {
                                            save_data_user_step3();
                                            update_academics_dt()
                                            cambios_s3 = false;
                                        }
                                    }

                                    if (healthCondition) {
                                        getStudentAses($("#num_doc_ini").val());
                                        getDataBD(id_ases, 'get_discapacity_data')
                                        setDiscapacityData(general_data);
                                    }

                                    break;
                                case 3:
                                    if (!discapacity) {
                                        save_data_user_step4();
                                        discapacity = true;
                                        cambios_s4 = false;
                                    } else if (cambios_s4) {
                                        save_data_user_step4();
                                        cambios_s4 = false;
                                    }
                                    break;
                                case 4:
                                    if (!healthCondition) {
                                        save_discapacity_data();
                                        healthCondition = true;
                                        cambios_s5 = false
                                    } else if (cambios_s5) {
                                        update_discapacity_dt();
                                        cambios_s5 = false;
                                    }

                                    if (healthService) {
                                        getStudentAses($("#num_doc_ini").val());
                                        getDataBD(id_ases, 'get_health_data')
                                        setHealtService(general_data);
                                    }
                                    break;
                                default:
                                    break;
                            }
                        }
                    });


                    //Funcion para recolectar valores de los selects
                    function getSelectValues() {
                        $('#tipo_doc').val($("#tipo_doc_ini option:selected").val())
                        var selects = $(".select-registro").map(function() { return $(this).attr("id"); }).get()
                        var inputs = $(".select-registro").map(function() { return $(this).next().next().attr("id") }).get()
                        for (var i = 0; i < inputs.length; i++) {

                            $("#" + inputs[i]).val($("#" + selects[i] + " option:selected").val());

                        }
                    }

                    //Funcion para la creacion de usuario en mdl_user, en caso de no existir
                    function save_mdl_user() {
                        var codigo = $("#codigo_estudiantil").val();
                        var nombre = $("#nombre").val();
                        var apellido = $("#apellido").val();
                        var emailI = $("#emailinstitucional").val();
                        var username = '' + codigo + "-" + cod_programa;
                        var pass = nombre.charAt(0) + codigo + apellido.charAt(0);
                        pass = pass.toUpperCase();

                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'save_mdl_user',
                                "params": [username, nombre, apellido, emailI, pass]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                id_moodle = msg;
                            },


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });

                    }

                    //Funciòn que hace una peticiòn asìncrona a la Api para el guardado, aquì se debe empaquetar en variables
                    //todos las variables a guardar del formulario
                    function save_data() {

                        $('#num_doc_modal').val($("#num_doc_ini").val())

                        var deportes = [];
                        deportes.push({ name: "deportes", value: input_deportes.getArr() });
                        deportes.push({ name: "actividades", value: input_actividades.getArr() });
                        deportes = JSON.stringify(deportes);

                        var a = $(".talentospilos_usuario").serializeArray();
                        var est = $("#div_tipo_institucion ").find("[name=estamento]:checked").val();
                        a.push({ name: "estamento", value: est });
                        a.push({ name: "id_ciudad_nac", value: $("#id_ciudad_ini").val() });

                        var familia = buildArr($("#table_familia"));
                        var programa = $("#programa").val();

                        var json_detalle = [];
                        buildJsonObject('otra_identidad', json_detalle);
                        json_detalle.push({ key_input_number: "orientacion_s", val_input_number: $("#select-orientacion").val() });
                        json_detalle.push({ v_modal: true});
                        buildJsonObject('otra_orientacion', json_detalle);
                        buildJsonObject('div_contacto_2', json_detalle);
                        buildJsonObject('otros_acomp', json_detalle);
                        buildJsonObject('sede_div', json_detalle);
                        json_detalle = JSON.stringify(json_detalle);


                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'save_data',
                                "params": [a, deportes, familia, programa, id_moodle, json_detalle]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {},


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    //Funciòn que hace una peticiòn asìncrona a la Api para la actualizacion de los datos, aquì se debe empaquetar en variables
                    //todos las variables a guardar del formulario
                    function update_ases_data() {

                        $('#num_doc_modal').val($("#num_doc_ini").val())

                        var deportes = [];
                        deportes.push({ name: "deportes", value: input_deportes.getArr() });
                        deportes.push({ name: "actividades", value: input_actividades.getArr() });
                        deportes = JSON.stringify(deportes);

                        var a = $(".talentospilos_usuario").serializeArray();
                        var est = $("#div_tipo_institucion ").find("[name=estamento]:checked").val();
                        a.push({ name: "estamento", value: est });
                        a.push({ name: "id_ciudad_nac", value: $("#id_ciudad_ini").val() });

                        var programa = $("#programa").val();

                        var json_detalle = [];
                        buildJsonObject('otra_identidad', json_detalle);
                        json_detalle.push({ key_input_number: "orientacion_s", val_input_number: $("#select-orientacion").val() });
                        json_detalle.push({ v_modal: true});
                        buildJsonObject('otra_orientacion', json_detalle);
                        buildJsonObject('div_contacto_2', json_detalle);
                        buildJsonObject('otros_acomp', json_detalle);
                        buildJsonObject('sede_div', json_detalle);
                        json_detalle = JSON.stringify(json_detalle);


                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'update_data_ases',
                                "params": [a, deportes, programa, id_ases, json_detalle]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                if (msg) {
                                    swal({title: "Cambios actualizados exitosamente!",text: "",type: "success", timer: 1500});
                                }
                            },


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    /*
                        Metodo para almacenar datos de la tabla talentospilos_usuario que se encuentran en la 
                        seccion 2 del formulario (familia, hijos, estrato)
                    */
                    function save_data_user_step2() {
                        var vive = buildArr($("#table_familia"));
                        buildJsonObject('set-vive', vive);
                        var familia = JSON.stringify(vive);
                        var hijos = $("#hijos_modal").val();
                        var estrato = $("#estrato_modal").val();
                        getStudentAses($("#num_doc_ini").val());

                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'save_data_user_step2',
                                "params": [id_ases, estrato, hijos, familia, id_economics_data]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {},


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    function save_economics_data() {
                        var economics_data = getEconomicsData();
                        var estrato = $("#estrato_modal").val();
                        getStudentAses($("#num_doc_ini").val());
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'insert_economics_data',
                                "params": [economics_data, estrato, id_ases]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                id_economics_data = msg
                            },


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    function update_economics_dt() {
                        var economics_data = getEconomicsData();
                        var estrato = $("#estrato_modal").val();
                        getStudentAses($("#num_doc_ini").val());
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'update_economics_dt',
                                "params": [economics_data, estrato, id_ases, id_economics_data]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                if (msg) {
                                    swal({title: "Cambios actualizados exitosamente!",text: "",type: "success", timer: 1500});
                                }
                            },


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    function save_academics_data() {
                        var academics_data = getAcademicsData();
                        getStudentAses($("#num_doc_ini").val());
                        var programa = $("#programa").val();
                        var titulo = $("#titulo_1").val();
                        var observaciones = $("#observaciones_modal").val();
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'insert_academics_data',
                                "params": [academics_data, programa, titulo, observaciones, id_ases]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {},


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    function update_academics_dt() {
                        var academics_data = getAcademicsData();
                        getStudentAses($("#num_doc_ini").val());
                        var programa = $("#programa").val();
                        var titulo = $("#titulo_1").val();
                        var observaciones = $("#observaciones_modal").val();
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'update_academics_dt',
                                "params": [academics_data, programa, titulo, observaciones, id_ases, id_academics_data]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                if (msg) {
                                    swal({title: "Cambios actualizados exitosamente!",text: "",type: "success", timer: 1500});
                                }
                            },


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    function save_discapacity_data() {
                        var datos_discapacidad = getDiscapacityData();
                        getStudentAses($("#num_doc_ini").val());
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'insert_disapacity_data',
                                "params": [datos_discapacidad, id_ases]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {},


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    function update_discapacity_dt() {
                        var datos_discapacidad = getDiscapacityData();
                        getStudentAses($("#num_doc_ini").val());
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'update_discapacity_dt',
                                "params": [datos_discapacidad, id_ases, id_discapacity_data]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                if (msg) {
                                    swal({title: "Cambios actualizados exitosamente!",text: "",type: "success", timer: 1500});
                                }
                            },


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    function save_health_service() {
                        var healtService_data = getHealtService();
                        getStudentAses($("#num_doc_ini").val());
                        var eps = $("#EPS").val();
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'insert_health_service',
                                "params": [healtService_data, eps, id_ases]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                var result = msg;
                                if (!isNaN(result)) {
                                    swal("Registro completado exitosamente!", "", "success")
                                }
                            },


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    function update_health_service() {
                        var healtService_data = getHealtService();
                        getStudentAses($("#num_doc_ini").val());
                        var eps = $("#EPS").val();
                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'update_health_service',
                                "params": [healtService_data, eps, id_ases, id_healt_service]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {
                                var result = msg;
                                if (result) {
                                    swal("Actualizacion completada exitosamente!", "", "success")
                                }
                            },


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    function save_data_user_step3() {
                        var icfes = $("#puntaje_icfes_modal").val();
                        var anio_ingreso = $("#anio_ingreso").val();
                        var colegio = $("#colegio").val();
                        getStudentAses($("#num_doc_ini").val());

                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'save_data_user_step3',
                                "params": [id_ases, icfes, anio_ingreso, colegio]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {},


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }

                    function save_data_user_step4() {
                        var id_discapacidad = $("#input_discapacidad").val();
                        //var ayuda_disc =  $("#ayuda_disc").val();
                        getStudentAses($("#num_doc_ini").val());

                        $.ajax({
                            async: false,
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'save_data_user_step4',
                                "params": [id_ases, id_discapacidad]
                            }),
                            url: "../managers/student_profile/studentprofile_api.php",
                            success: function(msg) {},


                            error: function(msg) {
                                swal(
                                    "Error",
                                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                    "error"
                                );
                            },

                        });
                    }
                })
            }
        }
    }

);
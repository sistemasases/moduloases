// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_ases/tagging
 */

//Teclas bindeadas para crear tags
const keys = {
    "enter":13,
    "space":32,
    "coma":44,    
}

//Se definen los estilos de los tag
const styles = {
    1: {
        tag: "badge-primary",
        remove: "badge-secondary"
    },

    2 : {
        tag: "badge-info",
        remove: "badge-primary"
    },

    3 : {
        tag: "badge-primary",
        remove: "badge-light"
    },

    4 : {
        tag: "badge-dark",
        remove: "badge-light"
    },

    5 : {
        tag: "badge-danger",
        remove: "badge-warning"
    },

    6 :{
        tag: "badge-success",
        remove: "badge-secondary"
    },

    7 : {
        tag: "badge-primary",
        remove: "badge-dark"
    },

    8 : {
        tag: "badge-dark",
        remove: "badge-danger"
    },

    9 : {
        tag: "badge-success",
        remove: "badge-dark"
    },

    10 : {
        tag:"badge-info",
        remove: "bagde-light"
    }

}

define(['jquery']);
class Tagging{
    //Constructor que inicializa las variables del objeto
    constructor(input,canvas,style=1,limit=10){

        this.input=input;
        this.canvas=canvas;
        this.limit=limit;
        this.style=style;
        this.arr = [];
        this.styleSelected = {};
        this.setStyle(this.style)

    }

    
    //Función que prepara el input para generar tags en un lienzo
    createTag(){
        var i = this.getInput();
        var l = this.getLimit();
        var k = this.getKeys();
        var c = this.getCanvas();
        var o = this; 
        var flag = false;

         //Se le añade al input el listener keypress
        i.keypress(function(e){
            if(o.arr.length < l){
                for (const key in keys) {        
                    if(e.which == keys[key] && i.val() !== "" && i.val().length>3){

                        //Se añade el tag al arreglo de tags                        
                        o.arr.push(i.val());
                        e.preventDefault();
                        
                        //Se muestra en el lienzo
                        c.html("");
                        o.displayTags(o.arr,c);
                   
                        //Reset del input
                        i.val("");
                        break;
                    }
                    
                }
            }else{
                flag = true;
                i.prop("disabled", true);
                i.addClass("is-invalid");
                i.after("<div class='col-md-8 invalid-feedback'>Se excedió el número de tags</div>");      
            }
        });

        //Evento para disparar removeTag
        $( document ).on( "click", ".remove", function(e) {

            //Obtención del tag
            var tagDelete=$(this).prev().text();

        

            //Llamado a la función remove para eliminar el tag del arr
            o.removeTag(tagDelete,o.arr);

            if(o.arr.length < l && flag){
                i.prop("disabled", false);
                i.next().remove();           
            }{
                flag = false;
            }

            //Refresh de los tags post remove
            o.displayTags(o.arr,c);
    
            
        });

       
    }
    

    //Función para imprimir el arreglo de tags en el lienzo(canvas)
    displayTags(arr,canvas){ 
        var s = this.getStyle();
        canvas.html("");
        arr.forEach(function(elemento, indice){
            canvas.append("<span class='tag badge "+s.tag+" '>"+ elemento +"</span>" + "<span role='button' class='remove mr-2 badge "+s.remove+"'>x</span>");
        });

    }

    //Función para eliminar del arreglo y del lienzo el tag seleccionado
    removeTag(tagDelete,arr){
    arr.forEach(function(element, index){
        if(element == tagDelete){
            console.log("entro" + index)
            var index = arr.findIndex(t => t ==  tagDelete);
            arr.splice(index,1);    
        }
    })    

}

    //get
    getInput(){
        return this.input;
    }

    getCanvas(){
        return this.canvas;
    }

    getKeys(){
        return this.keys;
    }

    getLimit(){
        return this.limit;
    }

    getStyle(){
        return this.styleSelected;
    }

    getArr(){
        return this.arr;
    }


    //set
    setInput(input){
        input=this.input;
    }

    setCanvas(canvas){
        canvas=this.canvas;
    }
    
    setLimit(limit){
        limit=this.limit;
    }

    setStyle(style){
        //Se almacena el objeto style correspondiente
        for(const s in styles){
          if(style == s)
              this.styleSelected = styles[s];    
      }
    }

    
}
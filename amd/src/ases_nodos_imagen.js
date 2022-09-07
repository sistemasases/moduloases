// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/ases_nodos_imagen
 */

/**
  * @autor Cristian Duvan Machado Mosquera <cristian.machado@correounivalle.edu.co>
  * @des  objecto para guardar los nodos y sus relaciones que fueron borrados
  * !important: no borrar
*/
let object_nodes = new Object()

define(['jquery',
],

    function ($) {

        return {

            init: function (array_data, edges) {


                let array_element = {
                    "nodes": [

                    ]
                }

                let array_style = [
                    {
                        "selector": "node",
                        "style": {
                            "height": 80,
                            "width": 80,
                            "background-fit": "cover",
                            "border-color": "#000",
                            "border-width": 3,
                            "border-opacity": 0.5,
                            "label": "data(name)"
                        }
                    },
                    {
                        "selector": "edge",
                        "style": {
                            "width": 3,
                            "line-color": "#ccc",
                            "target-arrow-color": "#ccc",
                            "target-arrow-shape": "triangle",
                            "curve-style": "bezier"
                        }
                    }
                ]

                /**
                  * @autor Cristian Duvan Machado Mosquera <cristian.machado@correounivalle.edu.co>
                  * @des pintar en nodos la jerarquia de los profecionales,monitores,estudiantes
                  * !no olvidar subir esto al campus y probarlo en el servidor de prueba y la ruta previamente creada en el campus
                  * TODO: falta hacer las funciones para que calculen la jerarquia y las posicones 
                  * TODO: falta la consulta a la base de datos del campus univalle
                  * ?El algotrimo que los acomoda por defecto es de la misma libreria pero el orden en el que los va mostar si toca programarlo
                  * ?me preocupa es la forma en la que van a salir de la base de datos mas que todo por la estrutura que espera cytoscape
                */

                var cy = cytoscape({
                    elements: array_element,
                    style: array_style,
                    layout: {
                        name: 'breadthfirst',
                        directed: true,
                        padding: 10
                    },
                    boxSelectionEnabled: false,
                    autounselectify: true,
                    container: document.getElementById('nodo-plugging')
                })


                /**
                  * @autor Cristian Duvan Machado Mosquera <cristian.machado@correounivalle.edu.co>
                  * @des  añadir los nodos al grafo
                  * 
                */
                function add_nodes(node_json) {

                    const data = Object.values(node_json)

                    //recorrer los nodos del grafo con map
                    data.map(function (node) {
    
                        let id_node = node.target
                        let name_node = node.firstname
                        cy.add({
                            data: { id: id_node, name: name_node }
                        });

                    })
                    
                    add_edges(node_json, 1)

                }

                /**
                  * @autor Cristian Duvan Machado Mosquera <cristian.machado@correounivalle.edu.co>
                  * @des  añadir los edges al grafo
                  * 
                */
                function add_edges(node_json, type) {

                    const data = Object.values(node_json)
                    //recorrer los nodos del grafo con map
                    data.map(function (node) {
                        
                        let source_data = (type === 1)? node.source : 104813
                        let target_data = node.target

                        if (source_data !== null && target_data !== null && source_data !== target_data 
                            && source_data !== undefined && target_data !== undefined) {

                            if (type === 1) {

                                cy.add({
                                    data: { id: source_data + '-' + target_data, source: source_data, target: target_data }
                                });

                            } else {

                                cy.add({
                                    data: { id: source_data + '-' + target_data, source: source_data, target: target_data }
                                });

                            }

                        }


                    })
                }

                /**
                  * @autor Cristian Duvan Machado Mosquera <cristian.machado@correounivalle.edu.co>
                  * @des  añadir los edges al grafo
                  * 
                */
                function jerarquia_cytoscape() {

                    add_nodes(array_data)
                    add_edges(edges, 0)

                    //variables
                    var nodes = cy.getElementById('104813');
                    var food = [];

                    //edges del nodo seleccionado
                    get_nodes_edges(food, nodes)

                    //animacion de los nodos al borrarlos
                    animacion_cy_arrow_node(food, 0, 0, 0);

                    get_nodes_edges(food, nodes)

                    var layout = cy.layout({
                        name: 'breadthfirst',
                        directed: true,
                        padding: 50
                    });


                    //agrega el nodo al flujo
                    layout.run();

                    //evitar efecto de movimiento del grafo
                    cy.pan({"x":177.79651400531588,"y":47.287313432835845})
                    cy.zoom(0.7158045389490901)



                }
                jerarquia_cytoscape()


                /**
                 *  @modificacion_por Cristian Duvan Machado Mosquera <cristian.machado@correounivalle.edu.co>
                 *  @nuevo ahora al dar click en un nodo despues de haberlo guardado y sacado del array de nodos, lo vuelve a poner en el grafo
                 *  funcion por defecto de cytoscape hecha por @author: https://cytoscape.org/
                 */
                cy.on('tap', 'node', function () {
                    var nodes = this;
                    var tapped = nodes;
                    var food = [];

                    nodes.addClass('eater');

                    //obtener los nodos y sus relaciones 
                    get_nodes_edges(food, nodes)

                    //animacion de los nodos al borrarlos
                    animacion_cy_arrow_node(food, 0, 500, 0);

                    //cuando no tiene hijos verifica si en el pasado tenia y se los agrega
                    if (food.length === 0) {
                       
                        //recorrer el objecto
                        for (var i in object_nodes) {
                            
                            //verifica si el nodo padre tenia hijoss y si es asi lo agrega
                            if (object_nodes[i].id === tapped.id()) {
                                  
                                cy.add({
                                    data: { id: i , name: object_nodes[i].name },
                                    style: {
                                        'opacity': 0,
                                        'width': 10,
                                        'height': 10,
                                    }
                                });

                                cy.add({ group: 'edges', data: { id: `${tapped.id()}${i}`, source: tapped.id(), target: i } })

                                var layout = cy.layout({
                                    name: 'breadthfirst',
                                    directed: true,
                                    padding: 50
                                });

                                Array.prototype.push.apply(food, cy.getElementById(i));
                            }

                        }

                        if (food[0] !== undefined) {

                            //guardar la posicion del nodo
                            localStorage.setItem('pan', JSON.stringify(cy.pan()))
                            localStorage.setItem('zoom', JSON.stringify(cy.zoom()))

                            //agrega el nodo al flujo
                            layout.run();

                            //evitar efecto de movimiento del grafo
                            cy.pan(JSON.parse(localStorage.getItem('pan')))
                            cy.zoom(JSON.parse(localStorage.getItem('zoom')))

                            //animar el nodo que se agrega
                            animacion_cy_arrow_node(food, 0, 800, 1);
                        }


                    }

                }); // on tap

            }

        }

        /**
          *  @modificacion_por Cristian Duvan Machado Mosquera <cristian.machado@correounivalle.edu.co>
          *  @nuevo ahora al hace la animacion de los nodos devolverlos al grafo
          *  funcion por defecto de cytoscape hecha por @author: https://cytoscape.org/
        */
        function animacion_cy_arrow_node(food, delay, duration, type) {

            let pistion_aux = {}
            let array_css = [
                {
                    'width': 10,
                    'height': 10,
                    'border-width': 0,
                    'opacity': 0
                },
                {
                    'opacity': 1,
                    'width': 80,
                    'height': 80,
                }
            ]

            for (var i = food.length - 1; i >= 0; i--) {

                (function () {
                    var thisFood = food[i];
                    var eater = thisFood.connectedEdges(function (el) {
                        return el.target().same(thisFood);
                    }).source();

                    //guardar la posicion del nodo y asignarle la del padre
                    if (type === 1) {
                        localStorage.setItem('position_aux', JSON.stringify(thisFood.position()))
                        thisFood.position(eater.position())
                        pistion_aux = JSON.parse(localStorage.getItem('position_aux'))
                    }
                    else {
                        pistion_aux = eater.position()
                    }

                    thisFood.delay(delay, function () {
                        eater.addClass('eating');
                    }).animate({
                        position: pistion_aux,
                        css: array_css[type]
                    }, {
                        duration: duration,
                        complete: function () {

                            if (type === 0) {
                                //guardamos el nombre del nodo borrado y quien era su padre
                                object_nodes[thisFood.id()] = { id: eater.id(),  name: thisFood.data('name') };
                                thisFood.remove();
                            }
                        }
                    });

                    delay += duration;

                })();
            } // for

        }

        /**
          *  @des funcion para obtener los nodos y sus relaciones 
          *  funcion por defecto de cytoscape hecha por @author: https://cytoscape.org/
        */
        function get_nodes_edges(food, nodes) {

            for (; ;) {
                var connectedEdges = nodes.connectedEdges(function (el) {
                    return !el.target().anySame(nodes);
                });

                var connectedNodes = connectedEdges.targets();

                Array.prototype.push.apply(food, connectedNodes);

                nodes = connectedNodes;

                if (nodes.empty()) { break; }

            }

        }


    }
);

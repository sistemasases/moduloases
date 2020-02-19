define([
    'block_ases/vendor-vuex',
    'block_ases/grader-store'
], function (Vuex, g_store) {
    var template = `
    <div class="gridGraphs">
        <canvas id="pie_passing_students" style="float:left" width="600" height="400"></canvas>
        <canvas id="pie_grades" style="float:right" width="600" height="400"></canvas>
        <canvas id="bar_grades" width="1400" height="350"></canvas>
        <canvas id="grades_histogram" width="1400" height="350"></canvas>
    </div>
    `;


    var name = 'Graph';
    var component  = {
        template: template,
        computed: {
            ...Vuex.mapState([
            ]),
            ...Vuex.mapGetters([
                'studentsCount',
                'studentsAsesCount',
                'passingGradesCount',
                'failingGradesCount',
                'nullGradesCount',
                'finalGradesSet',
                'finalPassingGradeSet',
                'itemSet',
                'itemOrderedNames',
                'passingGradesSet',
                'failingGradesSet',
                'nullGradesSet',
                'lineGraphLabel',
                'getGradesByRange'
            ]),
            itemNames: function (){
                names = [];
                itemlength = this.itemSet.length;

                for(i = 0; i < itemlength; i++){
                    names.push(this.itemSet[i].itemname);
                }

                names.push(itemlength);
                return names;
            },
            totalStudents: function (){
                return{
                    type: 'pie',
                    data: {
                        datasets: [{
                            data: [this.studentsCount - this.studentsAsesCount, this.studentsAsesCount],
                            backgroundColor: ['red', '#bdf2bd']
                        }],

                        // These labels appear in the legend and in the tooltips when hovering different arcs
                        labels: [
                            'Estudiantes no afiliados a ASES',
                            'Estudiantes afiliados a ASES'
                        ]
                    },
                    options: {
                    }
                }
            },
            passingStudents: function (){
                return{
                    type: 'pie',
                    data: {
                        datasets: [{
                            data: [this.finalPassingGradeSet.length, this.finalGradesSet.length - this.finalPassingGradeSet.length],
                            backgroundColor: ['green', 'red']
                        }],

                        // These labels appear in the legend and in the tooltips when hovering different arcs
                        labels: [
                            'Estudiantes que pasan la materia',
                            'Estudiantes que no pasan la materia'
                        ]
                    },
                    options: {
                        responsive: false,
                        title: {
                            display: true,
                            text: "Porcentaje de aprobacion de estudiantes"
                        }
                    }
                }
            },
            gradesReport: function (){
                return{
                    type: 'pie',
                    data: {
                        datasets: [{
                            data: [this.passingGradesCount, this.failingGradesCount, this.nullGradesCount],
                            backgroundColor: ['green', 'red', 'gray']
                        }],

                        // These labels appear in the legend and in the tooltips when hovering different arcs
                        labels: [
                            'Notas ganadas',
                            'Notas perdidas',
                            'Notas no calificadas'
                        ]
                    },
                    options: {
                        responsive: false,
                        title: {
                            display: true,
                            text: "Estado general de notas"
                        }
                    }
                }
            },
            gradesInfo: function (){
                return{
                    type: 'bar',
                    data: {labels: this.itemOrderedNames, //array with itemnames
                        datasets: [
                            //data: array with the amount of that type of grade
                            {
                                label: "Notas ganadas",
                                backgroundColor: "green",
                                data: this.passingGradesSet
                            },
                            {
                                label: "Notas perdidas",
                                backgroundColor: "red",
                                data: this.failingGradesSet
                            },
                            {
                                label: "Notas no calificadas",
                                backgroundColor: "gray",
                                data: this.nullGradesSet
                            }
                        ]},
                        options: {
                            title: {
                                display: true,
                                text: "Porcentaje de aprobacion por calificacion"
                            },
                            barValueSpacing: 20,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        min: 0,
                                    }
                                }]
                            }
                        }
                }
            },
            gradesHistogramGraph: function () {
                return{
                    type: 'line',
                    data: {
                        labels: this.lineGraphLabel,
                        datasets: [{
                            data: this.getGradesByRange,
                            label: "Notas finales",
                            borderColor: "#3e95cd",
                            fill: true
                        }
                        ]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'Histograma de notas finales',
                        }
                    }
                }
            }

},
        mounted () {
            this.$store.dispatch(g_store.actions.FETCH_STATE);

            /*var ctxcount = document.getElementById('pie_student_count').getContext('2d');
            var pieTotalstudents = new Chart(ctxcount, this.totalStudents);*/

            var ctxpass = document.getElementById('pie_passing_students').getContext('2d');
            var piePassingStudents = new Chart(ctxpass, this.passingStudents);

            var ctxgrade = document.getElementById('pie_grades').getContext('2d');
            var pieGraded = new Chart(ctxgrade, this.gradesReport);

            var ctxbargrades = document.getElementById("bar_grades").getContext("2d");
            var barGrades= new Chart(ctxbargrades, this.gradesInfo);

            var ctxhistogram = document.getElementById("grades_histogram").getContext("2d");
            var lineFinals = new Chart(ctxhistogram, this.gradesHistogramGraph);
        }
    };
    return {
        component: component,
        name: name
    };
});
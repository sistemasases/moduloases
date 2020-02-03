define(['block_ases/grader-enums'], function(gEnums) {
    var aggregations = [
        {id: gEnums.aggregations.SIMPLE, name: "Promedio simple"},
        {id: gEnums.aggregations.PROMEDIO, name: "Promedio ponderado"}
    ];
    return {
        aggregations: aggregations
    };
});
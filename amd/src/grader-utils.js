define([], function () {
    var removeInsignificantTrailZeros = function(value) {
        if (typeof value === 'string') {
            return Number(Number(value).toString());
        }
        if (typeof value === 'number') {
            return value.toString();
        }
    };

    var round = function (number, decimalPlaces)
    {
        var flotante = parseFloat(number);
        var result  = Math.round(flotante*Math.pow(10,decimalPlaces))/Math.pow(10,decimalPlaces);
        return result;
    };
    var ID = function () {
        // Math.random should be unique because of its seeding algorithm.
        // Convert it to base 36 (numbers + letters), and grab the first 9 characters
        // after the decimal.
        return '_' + Math.random().toString(36).substr(2, 9);
    };
    var orderGradeIdsInItemSetOrder = function (gradeIds, gradesObject, itemsOrderedIds) {
        return gradeIds.sort(function(gradeIdA, gradeIdB) {
            const itemIdA = gradesObject[gradeIdA].itemid;
            const itemIdB = gradesObject[gradeIdB].itemid;
            return itemsOrderedIds.indexOf(itemIdA) - itemsOrderedIds.indexOf(itemIdB);
        });
    };
    var getCourseId = function() {
        var informacionUrl = window.location.search.split("&");
        var curso = -1;
        for (var i = 0; i < informacionUrl.length; i += 1) {
            var detailsUrl = informacionUrl[i].split("=");
            if (detailsUrl[0] === "id_course") {
                curso = detailsUrl[1];
            }
        }
        console.log(curso);
        return curso;
    };
    return {
        removeInsignificantTrailZeros: removeInsignificantTrailZeros,
        getCourseId: getCourseId,
        ID: ID,
        round: round,
        orderGradeIdsInItemSetOrder: orderGradeIdsInItemSetOrder
    };
});
/**
 *
 */

define([], function() {
    var aggregations = {
        SIMPLE: 1,
        PROMEDIO: 10
    };
    var sortStudentMethods = {
        FIRST_NAME: 'firstname',
        LAST_NAME: 'lastname'
    };
    var sortDirection = {
        ASC: 'asc',
        DESC: 'desc'
    };
    return {
        aggregations: aggregations,
        sortStudentMethods: sortStudentMethods,
        sortDirection: sortDirection
    };
});
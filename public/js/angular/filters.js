angular.module('madison.dateFilters', []).filter('parseDate', function() {
    return function(date){
        return Date.parse(date);
    };
});
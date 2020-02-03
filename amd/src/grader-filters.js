define([], function() {
    var round_funct = function(value, accuracy, keep) {
        if (typeof value !== 'string' && typeof value !== 'number') {
            return value;
        }
        if (typeof value === 'string') {
            value = Number(value);
        }
        var fixed = value.toFixed(accuracy);
        return keep ? fixed : +fixed;
    };

    var trunc_funct = function(text, length, clamp){
        clamp = clamp || '...';
        var node = document.createElement('div');
        node.innerHTML = text;
        var content = node.textContent;
        return content.length > length ? content.slice(0, length) + clamp : content;
    };

    var round_name = 'round';
    var truncate_name = 'trunc';
    return {
        round: {name: round_name, func: round_funct},
        trun: {name: truncate_name, func: trunc_funct}
    };
});
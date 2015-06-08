function tmultisearch_start( id, minlen, maxsize, placeholder, multiple, preload_items, width, height, load_data ) {
    $('#'+id).select2( {
        minimumInputLength: minlen,
        maximumSelectionSize: maxsize,
        separator: '||',
        placeholder: placeholder,
        multiple: multiple,
        id: function(e) { return e.id + "::" + e.text; },
        query: function (query)
        {
            var data = {results: []};
            preload_data = preload_items;
            $.each(preload_data, function() {
                if(query.term.length == 0 || this.text.toUpperCase().indexOf(query.term.toUpperCase()) >= 0 ){
                    data.results.push({id: this.id, text: this.text });
                }
            });
            query.callback(data);
        }
    });
    $('#s2id_'+id+ '> .select2-choices').height(height).width(width).css('overflow-y','auto');
    
    if (typeof load_data !== "undefined") {
        $('#'+id).select2("data", load_data);
    }
}

function tmultisearch_enable_field(form_name, field) {
    try { $('#s2id_'+$('form[name='+form_name+'] [name="'+field+'"]').attr('id')).select2("enable", true); } catch (e) { }    
}

function tmultisearch_disable_field(form_name, field) {
    try { $('#s2id_'+$('form[name='+form_name+'] [name="'+field+'"]').attr('id')).select2("enable", false); } catch (e) { }    
}
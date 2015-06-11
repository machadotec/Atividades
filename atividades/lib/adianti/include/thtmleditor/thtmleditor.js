function thtmleditor_enable_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').cleditor()[0].disable(false).refresh(); },1);
}

function thtmleditor_disable_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').cleditor()[0].disable(true); },1);
}

function thtmleditor_clear_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').cleditor()[0].clear(); },1);    
}

function thtmleditor_start(objectId, width, height) {
    $('#'+objectId).cleditor({width: width+'px', height: height+'px'});    
}
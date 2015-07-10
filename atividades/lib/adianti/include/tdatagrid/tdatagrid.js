function tdatagrid_inlineedit( querystring )
{
    $(function() {
        $(".inlineediting").editInPlace({
            	callback: function(unused, enteredText)
            	{
            	    __adianti_load_page( $(this).attr("action") + querystring + '&key='+ $(this).attr("key")+"&field="+ $(this).attr("field")+"&value="+encodeURIComponent(enteredText));
            	    return enteredText;
            	},
            	show_buttons: false,
            	text_size:20,
            	params:column=name
        });
    });
}

function tdatagrid_add_serialized_row(datagrid, row)
{
    $('#'+datagrid+' > tbody:last-child').append(row);
}
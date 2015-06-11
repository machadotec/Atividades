function tdialog_start(id, callback)
{
    $(document).ready(function() {
        $( id ).modal({backdrop:true, keyboard:true});
        if (typeof callback != 'undefined')
        {
            $( id ).on("hidden.bs.modal", callback );
        }
    });
}

function tdialog_close(id)
{
    $( '.modal-backdrop' ).last().remove();
    $('#'+id).modal('hide');
    $('body').removeClass('modal-open');
    setTimeout(function(){ $('#'+id).remove(); }, 300);
}
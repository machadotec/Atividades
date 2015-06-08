var lastClass;
function tnotebook_hide(counter, count)
{
    lastClass = 'tnotebook_aba_sim';
    for (x = 0; x < count; x++)
    {
        obj = document.getElementById('painel_'+counter+'_'+x);
        if (obj) {
            obj.className = 'tnotebook_painel_nao';
        }
        
        obj = document.getElementById('aba_'+counter+'_'+x);
        if (obj) {
            obj.className = 'tnotebook_aba_nao';
        }
    }
}
function tnotebook_show_tab(counter, tab)
{
    lastClass = 'tnotebook_aba_sim';
    obj = document.getElementById('painel_'+counter+'_'+tab);
    
    if (obj) {
        obj.className = 'tnotebook_painel_sim';
    }

    obj = document.getElementById('aba_'+counter+'_'+tab);
    if (obj) {
       obj.className = 'tnotebook_aba_sim';
    }
}

function tnotebook_prelight(obj, tab)
{
    lastClass = obj.className;
    if (obj.className !=='tnotebook_aba_sim')
    {
        obj.className='tnotebook_aba_pre';
    }

}
function tnotebook_unprelight(obj, tab)
{
    obj.className=lastClass;
}
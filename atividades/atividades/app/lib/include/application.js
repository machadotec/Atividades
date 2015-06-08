loading = true;

function showLoading()
{
if(loading)
__adianti_block_ui('Carregando');
}

Adianti.onBeforeLoad = function()
{
loading = true;
setTimeout(function(){showLoading()}, 400);
};

Adianti.onAfterLoad = function()
{
loading = false;
__adianti_unblock_ui();
};
$(function(){
    skdropdowns();
    
});

function skdropdowns()
{
    $('.sk-dropdown')
        .click(
            function(e){
                console.log('click');
                var targetId = $(this).data('target');
                $('#'+targetId).toggle();
            }
        );
}

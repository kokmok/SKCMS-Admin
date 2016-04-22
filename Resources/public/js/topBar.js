$(function(){
    skdropdowns();
    skOpener();
});

function skOpener(){
    $('#skMenuOpener a').click(toggleSkMenu)
}

function toggleSkMenu(e){
    if ($('#sk-topBar').hasClass('closed')){
        $('#sk-topBar').animate({'right':'0px'},180);
    }
    else{
        $('#sk-topBar').animate({'right':'-300px'},180);
    }

    $('#sk-topBar')
        .toggleClass('closed')
        .toggleClass('opened')
    ;
    $('#skMenuOpener a i')
        .toggleClass('fa-arrow-left')
        .toggleClass('fa-arrow-right')
    ;


}

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

$(document).ready(function(){




    $('select')
        .filter(function() {
            return this.id.match(/_icon/);
        })
        .select2(
            {
                templateResult: function(result) {
//                    log('templateResult();');
//                    log(result);
                    if (!result.id) { return result.text; }
                    var $result = $('<span><span class="' + result.element.value.toLowerCase() + '"></span> ' + result.text+'</span>' );
                    return $result;
                }
            });
//                .find('option').each(function(){
////                    $(this).addClass($(this).attr('value'));
//                });

    lockedFormInput();
    skcms_ckfinder_popup_input();
    menus();
    addPrototypeForm();
    sortableLinks();
    dateTimePicker();
    $('.colorpicker').colorPicker({cssAddon:'.cp-color-picker{z-index:3000;}'});








    //disbaling some functions for Internet Explorer
    if($.browser.msie)
    {
        $('#is-ajax').prop('checked',false);
        $('#for-is-ajax').hide();
        $('#toggle-fullscreen').hide();
        $('.login-box').find('.input-large').removeClass('span10');

    }


    //highlight current / active link
    $('ul.main-menu li a').each(function(){
        if($($(this))[0].href==String(window.location))
            $(this).parent().addClass('active');
    });




//	

    //animating menus on hover
    $('ul.main-menu li:not(.nav-header)').hover(function(){
            $(this).animate({'margin-left':'+=5'},300);
        },
        function(){
            $(this).animate({'margin-left':'-=5'},300);
        });

    //other things to do on document ready, seperated for ajax calls
    docReady();
    ckBrowser();
});



function ckBrowserRefresh()
{
    var $ = jQuery;
    $('.ckBrowser').off('click',launchCKBrowser);
    ckBrowser();
}
function ckBrowser()
{
    var $ = jQuery;
    $('.ckBrowser').on('click',launchCKBrowser);
}

function launchCKBrowser(e)
{
    var config = {};
    config.basePath = $(e.target).data('basepath');
    config.connectorPath = $(e.target).data('connectorpath');
    config.selectActionData =$(e.target).prev('input').attr('id');

    var finder = new CKFinder(config);
    finder.selectActionFunction = SetFileField;
    finder.popup();
    return false;
}

function SetFileField( fileUrl, data )
{

    console.log('selected');
    console.log(data);
    var basePath = $('#'+data["selectActionData"]).next('input').attr('data-basepath');


    $('#'+data["selectActionData"]).prev('img').attr('src',fileUrl);
    $('#'+data["selectActionData"]).val(fileUrl.replace(basePath,''));

}


function docReady(){
    //prevent # links from moving to top
    $('a[href="#"][data-top!=true]').click(function(e){
        e.preventDefault();
    });

    //rich text editor
//	$('.cleditor').cleditor();

    //datepicker
    $('.datepicker').datepicker();

    //notifications
    $('.noty').click(function(e){
        e.preventDefault();
        var options = $.parseJSON($(this).attr('data-noty-options'));
        noty(options);
    });


    //uniform - styler for checkbox, radio and file input
//	$("input:checkbox, input:radio, input:file").not('[data-no-uniform="true"],#uniform-is-ajax').uniform();

    //chosen - improves select
    $('[data-rel="chosen"],[rel="chosen"]').chosen();



    //tabs
    $('#myTab a:first').tab('show');
    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    //makes elements soratble, elements that sort need to have id attribute to save the result
    $('.sortable').sortable({
        revert:true,
        cancel:'.btn,.box-content,.nav-header',
        update:function(event,ui){
            //line below gives the ids of elements, you can make ajax call here to save it to the database
            //console.log($(this).sortable('toArray'));
        }
    });

    //slider
    $('.slider').slider({range:true,values:[10,65]});

    //tooltip
    $('[rel="tooltip"],[data-rel="tooltip"]').tooltip({"placement":"bottom",delay: { show: 400, hide: 200 }});

    //auto grow textarea
//	$('textarea.autogrow').autogrow();

    //popover
    $('[rel="popover"],[data-rel="popover"]').popover();



    //iOS / iPhone style toggle switch
    $('.iphone-toggle').iphoneStyle();




    //gallery controlls container animation
    $('ul.gallery li').hover(function(){
        $('img',this).fadeToggle(1000);
        $(this).find('.gallery-controls').remove();
        $(this).append('<div class="well gallery-controls">'+
            '<p><a href="#" class="gallery-edit btn"><i class="icon-edit"></i></a> <a href="#" class="gallery-delete btn"><i class="icon-remove"></i></a></p>'+
            '</div>');
        $(this).find('.gallery-controls').stop().animate({'margin-top':'-1'},400,'easeInQuint');
    },function(){
        $('img',this).fadeToggle(1000);
        $(this).find('.gallery-controls').stop().animate({'margin-top':'-30'},200,'easeInQuint',function(){
            $(this).remove();
        });
    });


    //gallery image controls example
    //gallery delete
    $('.thumbnails').on('click','.gallery-delete',function(e){
        e.preventDefault();
        //get image id
        //alert($(this).parents('.thumbnail').attr('id'));
        $(this).parents('.thumbnail').fadeOut();
    });
    //gallery edit
    $('.thumbnails').on('click','.gallery-edit',function(e){
        e.preventDefault();
        //get image id
        //alert($(this).parents('.thumbnail').attr('id'));
    });

    //gallery colorbox
//	$('.thumbnail a').colorbox({rel:'thumbnail a', transition:"elastic", maxWidth:"95%", maxHeight:"95%"});

    //gallery fullscreen
    $('#toggle-fullscreen').button().click(function () {
        var button = $(this), root = document.documentElement;
        if (!button.hasClass('active')) {
            $('#thumbnails').addClass('modal-fullscreen');
            if (root.webkitRequestFullScreen) {
                root.webkitRequestFullScreen(
                    window.Element.ALLOW_KEYBOARD_INPUT
                );
            } else if (root.mozRequestFullScreen) {
                root.mozRequestFullScreen();
            }
        } else {
            $('#thumbnails').removeClass('modal-fullscreen');
            (document.webkitCancelFullScreen ||
            document.mozCancelFullScreen ||
            $.noop).apply(document);
        }
    });



    //datatable
    $('.datatable').dataTable({
        "sDom": "<'row'<'col-md-6 col-lg-6 col-sx-12 colsm-6'l><'col-md-6 col-lg-6 col-sx-12 colsm-6'f>r>t<'row'<'col-md-12 col-mlg-12 col-xs-12 col-sm-12'i><'col-md-12 col-mlg-12 col-xs-12 col-sm-12 center'p>>",
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sLengthMenu": "_MENU_ records per page"
        }
    } );
    $('.btn-close').click(function(e){
        e.preventDefault();
        $(this).parent().parent().parent().fadeOut();
    });
    $('.btn-minimize').click(function(e){
        e.preventDefault();
        var $target = $(this).parent().parent().next('.box-content');
        if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
        else 					   $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
        $target.slideToggle();
    });

    modals();





    //initialize the external events for calender

    $('#external-events div.external-event').each(function() {

        // it doesn't need to have a start or end
        var eventObject = {
            title: $.trim($(this).text()) // use the element's text as the event title
        };

        // store the Event Object in the DOM element so we can get to it later
        $(this).data('eventObject', eventObject);

        // make the event draggable using jQuery UI
        $(this).draggable({
            zIndex: 999,
            revert: true,      // will cause the event to go back to its
            revertDuration: 0  //  original position after the drag
        });

    });



}

function modals()
{
    $('.btn-setting').click(function(e){
        e.preventDefault();
        $('#myModal .modal-header h4').text($(this).data('title'));
        $('#myModal .modal-body p').text($(this).data('content'));
        $('#myModal .btn-primary').attr('href',$(this).attr('href'));
        $('#myModal .btn-primary').click(function(){window.location.href=$(this).attr('href')});
        $('#myModal .btn-primary').text($(this).data('confirm'));
        log('modal');
        $('#myModal').modal('show');
    });
}


//additional functions for data table
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
    return {
        "iStart":         oSettings._iDisplayStart,
        "iEnd":           oSettings.fnDisplayEnd(),
        "iLength":        oSettings._iDisplayLength,
        "iTotal":         oSettings.fnRecordsTotal(),
        "iFilteredTotal": oSettings.fnRecordsDisplay(),
        "iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
        "iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
    };
}
$.extend( $.fn.dataTableExt.oPagination, {
    "bootstrap": {
        "fnInit": function( oSettings, nPaging, fnDraw ) {
            var oLang = oSettings.oLanguage.oPaginate;
            var fnClickHandler = function ( e ) {
                e.preventDefault();
                if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
                    fnDraw( oSettings );
                }
            };

            $(nPaging).addClass('pagination').append(
                '<ul>'+
                '<li class="prev disabled"><a href="#">&larr; '+oLang.sPrevious+'</a></li>'+
                '<li class="next disabled"><a href="#">'+oLang.sNext+' &rarr; </a></li>'+
                '</ul>'
            );
            var els = $('a', nPaging);
            $(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
            $(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
        },

        "fnUpdate": function ( oSettings, fnDraw ) {
            var iListLength = 5;
            var oPaging = oSettings.oInstance.fnPagingInfo();
            var an = oSettings.aanFeatures.p;
            var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

            if ( oPaging.iTotalPages < iListLength) {
                iStart = 1;
                iEnd = oPaging.iTotalPages;
            }
            else if ( oPaging.iPage <= iHalf ) {
                iStart = 1;
                iEnd = iListLength;
            } else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
                iStart = oPaging.iTotalPages - iListLength + 1;
                iEnd = oPaging.iTotalPages;
            } else {
                iStart = oPaging.iPage - iHalf + 1;
                iEnd = iStart + iListLength - 1;
            }

            for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
                // remove the middle elements
                $('li:gt(0)', an[i]).filter(':not(:last)').remove();

                // add the new list items and their event handlers
                for ( j=iStart ; j<=iEnd ; j++ ) {
                    sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
                    $('<li '+sClass+'><a href="#">'+j+'</a></li>')
                        .insertBefore( $('li:last', an[i])[0] )
                        .bind('click', function (e) {
                            e.preventDefault();
                            oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
                            fnDraw( oSettings );
                        } );
                }

                // add / remove disabled classes from the static elements
                if ( oPaging.iPage === 0 ) {
                    $('li:first', an[i]).addClass('disabled');
                } else {
                    $('li:first', an[i]).removeClass('disabled');
                }

                if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
                    $('li:last', an[i]).addClass('disabled');
                } else {
                    $('li:last', an[i]).removeClass('disabled');
                }
            }

            modals();
        }
    }
});


function lockedFormInput()
{
    $('.ckcms_form_locker')
        .click(
            function()
            {
                var target = $('#'+$(this).data('target'));
                var child = $(this).find('i');
                if (child.hasClass('fa-unlock'))
                {
                    child.removeClass('fa-unlock');
                    child.addClass('fa-lock');
                    target.attr('readonly',true);
                }
                else
                {
                    child.addClass('fa-unlock');
                    child.removeClass('fa-lock');
                    target.removeAttr('readonly');
                }
            }
        );
}

function skcms_ckfinder_popup_input()
{
    //$('.ckfinder_popup_image').next('input')
    //    .change(
    //        function()
    //        {
    //            $(this).prev('img').attr('src',$(this).val());
    //        }
    //    );
    $('[data-type=elfinder-input-field]').each(function(){
        var img = $('<img />');
        var $elem = $(this)
        img.addClass('elfinderImg').attr('src',$(this).val());
        $(this).after(img);
        img.on('click',function(){$elem.trigger('click')});
    });

    $('[data-type=elfinder-input-field]').change(function(){
        $(this).next('img').attr('src',$(this).val())
    });
}


function menus()
{
    if ($( ".connectedSortable" ).length)
    {
//        log($('#menu-reel').data('children').length);
        if ($('#menu-reel').attr('data-children').length)
        {
            $('#menu-reel').html(fromArrayToList($('#menu-reel').data('children')).html());
            var nestedArray = fromListToArray($('#menu-reel'));
            $('#json_menu').val(JSON.stringify(nestedArray));
        }
        $( ".connectedSortable li" ).dblclick(function(e)
        {
            e.stopPropagation()
            menuPopoverForm($(this));
        });
        $( ".connectedSortable" ).nestedSortable({
            connectWith: ".connectedSortable",
            listType: 'ul',
            relocate: function(e,ui)
            {
                var item = ui.item;
                console.log(ui.item);
                menuPopoverForm(ui.item);

            }
        }).disableSelection();

        //    console.log(nestedArray);
    }

}

function menuPopoverForm(element)
{
    element.popover({trigger:'manual',html:true,'container':'body'});
    element.on('shown.bs.popover',function()
    {
        $('.menu_form input').focus().val(element.attr('data-name'));
//        $('.menu_form').blur(function(){element.popover('destroy');})
//                log($('.menu_form')[0].attr('data-target'));
        $('.menu_form').submit(
            function(e)
            {
                e.preventDefault();
                log('element');
                log(element);
                var newName = $(this).find('input').val();
                element.children('.entityName:first').text(newName);
                element.attr('data-name',newName);
                var nestedArray = fromListToArray($('#menu-reel'));
                $('#json_menu').val(JSON.stringify(nestedArray));
                element.popover('destroy');

            });
        $('.menu_form_closer').click(function(){element.popover('destroy');});
    })
    element.popover('show');
}

function fromListToArray(list)
{
    var farray = [];
    var i = 0;
    list.children('li').each(function()
    {
        var element = {'targetId':$(this).attr('target-id'),'name':$(this).attr('data-name'),'elementId':$(this).attr('data-elementId'),'entityClass':$(this).attr('data-entityClass')};
        if ($(this).find('ul').length)
        {
            element.children = fromListToArray($(this).find('ul'));
        }
        element.position = i;
        farray.push(element);
        i++;
    });

    return farray;

}
function fromArrayToList(childrenArray)
{
    log(childrenArray);
    var toReturn = '';
    var parent = $('<ul></ul>');

    childrenArray = $.map(childrenArray, function(value, index) {
        return [value];
    });
    for (var i=0;i<childrenArray.length;i++)
    {

        var id = uniqid();
        var proto = $($('#liPrototype').html()).clone();
        log('PROTO');
        log(proto);
        proto.attr('target-id', childrenArray[i].targetId);
        proto.attr('data-name', childrenArray[i].name);
        proto.attr('data-elementId', childrenArray[i].elementId);
        proto.attr('data-entityClass', childrenArray[i].entityClass);
//        proto.html(childrenArray[i].name);
        proto.attr('id', 'menu-li-'+id);
        proto.find('.pageName').text(childrenArray[i].name);
        proto.find('.entityName').text(childrenArray[i].name);

        var dataContent = proto.attr('data-content');
        dataContent = dataContent.replace(/%_pageName_%/g,childrenArray[i].name);
        dataContent = dataContent.replace(/%_index_%/g,id);

        proto.attr('data-content',dataContent);

        if (childrenArray[i].children != undefined && childrenArray[i].children.length)
        {
//            var childrenList = $('<ul></ul>');
//            childrenList.append(fromArrayToList(proto,childrenArray[i].children));
            proto.append(fromArrayToList(childrenArray[i].children));

        }
//        log(proto);
//        log(id);
        parent.append(proto);

    }


    return parent;



}
function uniqid(prefix, more_entropy) {
    //  discuss at: http://phpjs.org/functions/uniqid/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //  revised by: Kankrelune (http://www.webfaktory.info/)
    //        note: Uses an internal counter (in php_js global) to avoid collision
    //        test: skip
    //   example 1: uniqid();
    //   returns 1: 'a30285b160c14'
    //   example 2: uniqid('foo');
    //   returns 2: 'fooa30285b1cd361'
    //   example 3: uniqid('bar', true);
    //   returns 3: 'bara20285b23dfd1.31879087'

    if (typeof prefix === 'undefined') {
        prefix = '';
    }

    var retId;
    var formatSeed = function(seed, reqWidth) {
        seed = parseInt(seed, 10)
            .toString(16); // to hex str
        if (reqWidth < seed.length) { // so long we split
            return seed.slice(seed.length - reqWidth);
        }
        if (reqWidth > seed.length) { // so short we pad
            return Array(1 + (reqWidth - seed.length))
                    .join('0') + seed;
        }
        return seed;
    };

    // BEGIN REDUNDANT
    if (!this.php_js) {
        this.php_js = {};
    }
    // END REDUNDANT
    if (!this.php_js.uniqidSeed) { // init seed with big random int
        this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
    }
    this.php_js.uniqidSeed++;

    retId = prefix; // start with prefix, add current milliseconds hex string
    retId += formatSeed(parseInt(new Date()
            .getTime() / 1000, 10), 8);
    retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
    if (more_entropy) {
        // for more entropy we add a float lower to 10
        retId += (Math.random() * 10)
            .toFixed(8)
            .toString();
    }

    return retId;
}

function addPrototypeForm()
{
    $('.form-control[data-prototype]').each(function(){

        $(this).removeClass('form-control').addClass('collection-control');

        var collection = $(this);
        var prototype = $(this).data('prototype');

        var $addTagLink = $('<a href="#" class="add_entity_link btn pink-btn">Add</a>');


        collection.children('div').each(function() {
            addTagFormDeleteLink($(this));
        });

//        $(this).append(collection);
        collection.prepend($addTagLink);
        $addTagLink.after('<hr />');

        $addTagLink.on('click', function(e) {
            e.preventDefault();
            addTagForm(collection,prototype);
        });

    });
}

function addTagForm(collectionHolder, prototype) {
    var newForm = prototype.replace(/__name__/g, collectionHolder.children().length);
    var $newForm = $(newForm);
    collectionHolder.append($newForm);
    addTagFormDeleteLink($newForm);
    ckBrowserRefresh();

}

function addTagFormDeleteLink($tagFormLi)
{
    var $removeFormA = $('<a href="#" class="btn pink-btn delete_entity_link" >Delete</a>');
    $tagFormLi.prepend($removeFormA);

    $removeFormA.on('click', function(e) {
        e.preventDefault();
        $tagFormLi.remove();
    });
}


function sortableLinks()
{
    $('.sorterTd').width(200);
    $('.sorterLink').click(
        function(e){
            e.preventDefault();
            var target = $(this).closest('tr');
            var up = $(this).hasClass('up');
            $.ajax({
                url:this.href,
                success: function(){
                    log('success');
                    if (up)
                    {
                        target.find('.sortValue').text (parseInt(target.find('.sortValue').text())-1);
                        target.prev('tr').find('.sortValue').text ( parseInt(target.prev('tr').find('.sortValue').text())+1);
                        target.prev('tr').before(target);

                    }
                    else
                    {
                        target.find('.sortValue').text( parseInt(target.find('.sortValue').text())+1);
                        target.next('tr').find('.sortValue').text (parseInt(target.next('tr').find('.sortValue').text())-1);
                        log('down');
                        target.next('tr').after(target);
                    }

                }
            });

        });
}

function dateTimePicker()
{
    $('.form_datetime').datetimepicker({
//        container: 'body',
        language:  'fr',
        weekStart: 1,
//        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1
    });
    $('.form_date').datetimepicker({
//        container: 'body',
        language:  'fr',
        weekStart: 1,
//        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1,
        minView:4
    });
}




function log(msg)
{
    var url = window.location.href;
//    console.log(url);
    if (url.match('/app_dev.php/'))
    {
        try{
            console.log(msg);
        }
        catch(e)
        {
            return;
        }
    }

}

$('li.menu_item a').click(function(event){
    event.preventDefault(); 
    if(!isTabAdded($(this))){
         tabClick($(this)); 
    } else {
        activeTab($(this));
    }
   
});


var renderTabPane = function(tab){
    return '<div class="tab-pane" id="'+tab.attr('data-tab-id')+'" ><iframe src="'+tab.attr('data-src')+'"></iframe></div>';
};


var renderTab = function(tab){
    return '<li><a class="admin-tab" data-tab-id="'+tab.attr('data-tab-id')+'" href="#'+tab.attr('data-tab-id')+'" data-toggle="tab" aria-expanded="false"><i class="cursor fa fa-refresh"></i>&nbsp;&nbsp;'+tab.text()+'&nbsp;&nbsp;<i class="remove fa fa-times "></i></a></li>';
};


var tabClick = function(tab)
{
    var tabHTML     = $(renderTab(tab));
    var tabPaneHTML = $(renderTabPane(tab));
    var navBar      = $('#ul-nav-tabs');
    var tabContent  = $('#section-tab-content > #tab-content');

    navBar.find('>li').removeClass('active');
    tabContent.find('>div.tab-pane').removeClass('active');
    navBar.append($(tabHTML).addClass('active'));
    tabContent.append($(tabPaneHTML).addClass('active'));
}

var isTabAdded = function(tab){
    var navBar      = $('#ul-nav-tabs');
    var checkAdded = false;
    navBar.find('li').each(function(){
        if($(this).find('a').attr('data-tab-id') === tab.attr('data-tab-id')){
            checkAdded = true;  
        } 
    });
    return checkAdded;
}

var activeTab = function(tab)
{
    var navBar      = $('#ul-nav-tabs');
    var tabContent  = $('#section-tab-content div.tab-content');
    navBar.find('li').each(function(){
        if($(this).find('a[data-tab-id="'+tab.attr('data-tab-id')+'"]').length){
            navBar.find('li').removeClass('active');
            tabContent.find('div.tab-pane').removeClass('active');
            $(this).addClass('active');
            tabContent.find('#'+tab.attr('data-tab-id')).addClass('active');
        }
    });
    
}


$('section.content-header ul.nav').on('click' ,'a.admin-tab > i', function(event){

    var liParent = $(this).parent();
    var div_id   = liParent.attr('data-tab-id');

    if($(this).hasClass('remove')){
        liParent.parent().remove();
        $('div#'+div_id).remove();

        setTabActive();

    } else {
        $('div#'+div_id+' iframe').attr( 'src', function ( i, val ) { return val; });
    }
});



var setTabActive = function()
{
    if($('section.content-header ul.nav li').length){
        var tab = $('section.content-header ul.nav li:last-child').find('a');
        activeTab(tab);
    }
}

/**
 * function active
 */
function updateStatus(obj) {
    $(obj).html('<img src="../../images/indicator.gif" border="0">');
    var href = $(obj).attr('data-href');
    $.ajax({
        url: href,
        method: 'GET',
        dataType: 'html',
        success: function (data) {
            console.log(data);
            $(obj).html(data);
        }
    });
}





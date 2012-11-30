
$(function() {
    $tab = window.location.hash;
    if ($tab != "") {
        $('.tabs-nav li.active').removeClass('active');
        $('.tabs-content.active').removeClass('active');
	
        $("a[href="+$tab+"]").parent().addClass('active');
        $($tab).addClass('active');
    }
    $('.tabs-nav li a').click(function() {
        $('.tabs-nav li.active').removeClass('active');
        $('.tabs-content.active').removeClass('active');
	
        $(this).parent().addClass('active');
        $($(this).attr('href')).addClass('active');
	 
        window.location.hash = $(this).attr('href');
        $("html").scrollTop(0);
	
        return false;
    });
});

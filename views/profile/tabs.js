
$(function() {
    $tab = window.location.hash;
	$('.tabs-nav li.active').removeClass('active');
	$('.tabs-content.active').removeClass('active');
	
    if ($tab != "") {
        $("a[href="+$tab+"]").parent().addClass('active');
        $($tab).addClass('active');
    } else if ($("a[href="+$tab+"]").length  == 0 || $tab == "") {
        $(".tabs-nav li:eq(0) a").click();
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

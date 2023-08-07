
$(document).ready(function() {
	//handle external links (new window)
	$('a[href^=http]').bind('click',function(){
		window.open($(this).attr('href'));
		return false;
	});

    // make code pretty
    window.prettyPrint && prettyPrint();

});

$(document).ready(function(){
    
        ////////////////////////////////////////////////////////////
        // Back to Top Scroller
        ////////////////////////////////////////////////////////////

	//hide or show the "back to top" link
	$(window).scroll(function(){
		( $(this).scrollTop() > 300 ) ? $('.cd-top').addClass('cd-is-visible') : $('.cd-top').removeClass('cd-is-visible cd-fade-out');
		if( $(this).scrollTop() > 1200 ) { 
			$('.cd-top').addClass('cd-fade-out');
		}
	});

	//smooth scroll to top
	$('.cd-top').on('click', function(event){
		event.preventDefault();
		$('body,html').animate({
			scrollTop: 0 ,
		 	}, 700
		);
	});
        
        ////////////////////////////////////////////////////////////
        // Menu to text-scroller
        ////////////////////////////////////////////////////////////
        
        $('#nav ul li a, .Navi_big li a').on('click', function(event) {
            event.preventDefault();
            // Linkziel in Variable schreiben
            var ziel = $(this).attr("href");
            //Scrollen der Seite animieren, body benoetigt fuer Safari
            $('html,body').animate({                    
                    scrollTop: $(ziel).offset().top
            // Dauer der Animation
            }, 1000 , function (){location.hash = ziel;});
        });
});
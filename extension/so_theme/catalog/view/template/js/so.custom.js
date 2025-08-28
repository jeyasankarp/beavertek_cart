/* Add Custom Code Jquery
 ========================================================*/
$(document).ready(function(){
	// Fix hover on IOS
	$('body').bind('touchstart', function() {}); 
	

	// Messenger posmotion
	/*$( "#close-posmotion-header" ).click(function() {
		$('.promotion-top').toggleClass('hidden-promotion');
		$('body').toggleClass('hidden-promotion-body');

		if($(".promotion-top").hasClass("hidden-promotion")){
			$.cookie("open", 0);
			
		} else{
			$.cookie("open", 1);
		}

	});*/
	
	/*if($.cookie("open") == 0){
		$('.promotion-top').addClass('hidden-promotion');
		$('body').addClass('hidden-promotion-body');
	}*/

	jQuery(function(){
		$(window).on('load', function(){
			var windowswidth = $(window).width();
			var containerwidth = $('.container').width();
			var widthcss = (windowswidth-containerwidth)/2-55;
			var hei = $('.slide13').outerHeight();
			var rtl = jQuery( 'body' ).hasClass( 'rtl' );
			if( !rtl ) {
				jQuery(".custom-scoll").css("left",widthcss);
			}else{
				jQuery(".custom-scoll").css("right",widthcss);
			}
			var navScroll = $("#box-link1");
			
			if (navScroll.length > 0) {
				//$(".custom-scoll").fadeOut();
				jQuery(".custom-scoll").css("top",hei);
				jQuery(".custom-scoll").css("position","absolute");

				$(window).scroll(function() {
					if( $(window).scrollTop() > navScroll.offset().top - 30  ) {
						//$(".custom-scoll").fadeIn();
						
						jQuery(".custom-scoll").css("top",0);
						jQuery(".custom-scoll").css("position","fixed");
					} 
					else {
						//$(".custom-scoll").fadeOut();
						jQuery(".custom-scoll").css("top",navScroll.offset().top);
						jQuery(".custom-scoll").css("position","absolute");
					}
			
				});

	        }
	    })
	});
	
	
	// nav scroll
	jQuery(function(){
		$(window).on('load', function(){
			var hei1 = $('.typeheader-28').outerHeight();
			var hei2 = $('.slideshow28').outerHeight();
			var hei = hei1 + hei2;			
			
			var navScroll = $("#box-link1");
			
			// if (navScroll.length > 0) {
			// 	jQuery(".custom-scroll").css("top",hei);
	  		//       }
	  		if (navScroll.length > 0) {				
				//jQuery(".custom-scroll").css("top",hei);
				jQuery(".custom-scroll").css("display","none");

				$(window).scroll(function() {
					if( $(window).scrollTop() > navScroll.offset().top  ) {						
						jQuery(".custom-scroll").css("display","block");
					} 
					else {				
						//jQuery(".custom-scroll").css("top",navScroll.offset().top);
						jQuery(".custom-scroll").css("display","none");
					}
			
				});

	        }

	    })
	});
	
	jQuery(function(){
		$('#nav-scroll').onePageNav({
			currentClass: 'active',
			changeHash: false,
			scrollSpeed: 750,
			scrollThreshold: 0.5,
			filter: '',
			easing: 'swing',
			
		});

		
	});

	// Messenger Top Link
	$('.list-msg').owlCarousel2({
		pagination: false,
		center: false,
		nav: false,
		dots: false,
		loop: true,
		slideBy: 1,
		autoplay: true,
		margin: 30,
		autoplayTimeout: 4500,
		autoplayHoverPause: true,
		autoplaySpeed: 1200,
		startPosition: 0, 
		responsive:{
			0:{
				items:1
			},
			480:{
				items:1
			},
			768:{
				items:1
			},
			1200:{
				items:1
			}
		}
	});






	// Close pop up countdown
	 $( "#so_popup_countdown .customer a" ).click(function() {
	  $('body').toggleClass('hidden-popup-countdown');
	 });
	// =========================================


	// click header search header 
	jQuery(document).ready(function($){
		$( ".search-header-w .icon-search" ).click(function() {
		$('#sosearchpro .search').slideToggle(200);
		$(this).toggleClass('active');
		});
	});
	
	// click header search header 29,39 
	jQuery(document).ready(function($){
		$('.search-header .icon-search').click(function(e){
          e.preventDefault();
		  $('#sosearchpro').toggleClass("active");
          $('#sosearchpro .search').toggleClass("nav-open");
          $('.search-screen').toggleClass("nav-open");         
          $(this).toggleClass('active');
        });
	
        $('.search-screen ').click(function(e){
          e.preventDefault();
          $(this).toggleClass("nav-open");
		  $('#sosearchpro').toggleClass("active");
          $('#sosearchpro .search').toggleClass("nav-open");          
		  $('.header-search .icon-search').toggleClass("active");
        });
	});
	
	jQuery(function(){
		if($(window).hasClass('typeheader-34')) {
			$(window).on('load', function(){
				var hei = $('.typeheader-34').outerHeight();
				var windowsheight = $(window).height();	
					$(window).scroll(function() {
						if( $(window).scrollTop() > hei  ) {
							//$(".custom-scoll").fadeIn();
							
							jQuery("#content .col-left").addClass("sticky-active");
							jQuery("#content .col-right").addClass("right-active");
							jQuery("#content .col-left").css("max-height",windowsheight);
						} 
						else{
							jQuery("#content .col-left").removeClass("sticky-active");
							jQuery("#content .col-right").removeClass("right-active");
							jQuery(".box-info-product").removeClass("fixed-top");
						}
						
					});       
			})
		}
	});

	$(".typeheader-35 .nav-menu .ico-nav").click(function () {
		if($('.nav-menu .megamenu-style-dev').hasClass('so-vertical-active'))
			$('.nav-menu .megamenu-style-dev').removeClass('so-vertical-active');
		else
			$('.nav-menu .megamenu-style-dev').addClass('so-vertical-active');
	}); 
	$(".typeheader-35 #remove-verticalmenu").click(function () {
		if($('.nav-menu .megamenu-style-dev').hasClass('so-vertical-active'))
			$('.nav-menu .megamenu-style-dev').removeClass('so-vertical-active');
		
	}); 
	
	
	// video
	$(document).ready(function() {
	    $('.home23-video').magnificPopup({
	      type: 'iframe',
	      iframe: {
	      patterns: {
	         youtube: {
	          index: 'youtube.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).
	          id: 'v=', // String that splits URL in a two parts, second part should be %id%
	          src: '//www.youtube.com/embed/%id%?autoplay=0' // URL that will be set as a source for iframe. 
	          },
	        }
	      }
	    });
	});

	// add class Box categories
	jQuery(document).ready(function($){

		if($("#accordion-category .panel .panel-collapse").hasClass("in")){
			$('#accordion-category .panel .accordion-toggle').addClass("show");			
		} 
		else{
			$('#accordion-category .panel .accordion-toggle').removeClass("show");
		}

	});

	// slider categories
	jQuery(document).ready(function($) {
	    var slidercate = $(".layout-2 .so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:30,
	    nav:true,
	    loop:false,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:4
	            },
	            992:{
	                items:4
	            },
	            1200:{
	                items:7
	            },
	        },
	    })
	});

	jQuery(document).ready(function($) {
	    var slidercate = $(".layout-4 .so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:20,
	    autoWidth: false,
	    nav:true,
	    loop:false,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:3
	            },
	            992:{
	                items:4
	            },
	            1200:{
	                items:5
	            },
	        },
	    })
	});

	jQuery(document).ready(function($) {
	    var slidercate = $(".layout-5 .so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:0,
	    nav:true,
	    loop:false,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:3
	            },
	            992:{
	                items:4
	            },
	            1200:{
	                items:6
	            },
	        },
	    })
	});

	// slider categories
	jQuery(document).ready(function($) {
	    var slidercate = $(".layout-6 .so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:30,
	    nav:true,
	    loop:false,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:4
	            },
	            992:{
	                items:4
	            },
	            1200:{
	                items:5
	            },
	        },
	    })
	});

	jQuery(document).ready(function($) {
	    var slidercate = $(".layout-9 .so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:30,
	    nav:true,
	    loop:false,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:3
	            },
	            992:{
	                items:4
	            },
	            1200:{
	                items:5
	            },
	        },
	    })
	});

	jQuery(document).ready(function($) {
	    var slidercate = $(".layout-16 .so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:30,
	    nav:true,
	    loop:false,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:2
	            },
	            992:{
	                items:3
	            },
	            1200:{
	                items:3
	            },
	        },
	    })
	});
	
	
	// slider categories
	jQuery(document).ready(function($) {
	    var slidercate = $(".custom-slidercates.so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:10,
	    nav:true,
	    loop:true,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:4
	            },
	            992:{
	                items:4
	            },
	            1200:{
	                items:6
	            },
	        },
	    })
	});

	jQuery(document).ready(function($) {
	    var slidercate = $(".custom-slidercates25.so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:30,
	    nav:true,
	    loop:true,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:4
	            },
	            992:{
	                items:5
	            },
	            1200:{
	                items:6
	            },
	            1650:{
	                items:8
	            },
	        },
	    })
	});

	jQuery(document).ready(function($) {
	    var slidercate = $(".custom-slidercates31.so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:30,
	    nav:false,
	    loop:false,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:3
	            },
	            992:{
	                items:4
	            },
	            1200:{
	                items:4
	            },
	            1650:{
	                items:4
	            },
	        },
	    })
	});

	jQuery(document).ready(function($) {
	    var slidercate = $(".custom-slidercates32.so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:20,
	    nav:true,
	    loop:true,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:3
	            },
	            992:{
	                items:4
	            },
	            1200:{
	                items:5
	            },
	            1650:{
	                items:6
	            },
	        },
	    })
	});
	
	jQuery(document).ready(function($) {
	    var slidercate = $(".custom-slidercates36.so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:0,
	    nav:true,
	    loop:true,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:2
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:4
	            },
	            992:{
	                items:5
	            },
	            1200:{
	                items:6
	            },
	            1650:{
	                items:8
	            },
	        },
	    })
	});
	
	jQuery(document).ready(function($) {
	    var slidercate = $(".custom-slidercates37.so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:30,
	    nav:true,
	    loop:true,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:1
	            },
	            992:{
	                items:2
	            },
	            1200:{
	                items:4
	            },
	            1650:{
	                items:5
	            },
	        },
	    })
	});
	
	jQuery(document).ready(function($) {
	    var slidercate = $(".custom-slidercates38.so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:0,
	    nav:true,
	    loop:true,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:2
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:3
	            },
	            992:{
	                items:4
	            },
	            1200:{
	                items:4
	            },
	            1650:{
	                items:4
	            },
	        },
	    })
	});
	
	jQuery(document).ready(function($) {
	    var slidercate = $(".custom-slidercates39.so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:0,
	    nav:true,
	    loop:true,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:2
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:2
	            },
	            992:{
	                items:3
	            },
	            1200:{
	                items:4
	            },
	            1650:{
	                items:5
	            },
	        },
	    })
	});
	
	
	//header37 close button 
	$('.header-top .topbar-close').click(function() {
	  $(this).find('.button-ex').toggleClass('active');
	  $(this).next().fadeToggle(400);
	});
	
	// custom to show footer center
	$(".button-toggle").click(function () {
		if($(this).children('.showmore').hasClass('active')) $(this).children().removeClass('active');
		else $(this).children().addClass('active');
		
		
		
		if($(this).prev().hasClass('showdown')) $(this).prev().removeClass('showdown').addClass('showup');
		else $(this).prev().removeClass('showup').addClass('showdown');
	}); 


	$(".clearable").each(function() {
  
	  var $inp = $(this).find("input:text"),
	      $cle = $(this).find(".clearable__clear");

	  $inp.on("input", function(){
	    $cle.toggle(!!this.value);
	  });
	  
	  $cle.on("touchstart click", function(e) {
	    e.preventDefault();
	    $inp.val("").trigger("input");
	  });
	  
	});

});

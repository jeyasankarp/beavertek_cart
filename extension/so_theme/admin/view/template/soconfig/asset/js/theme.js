$(document).ready(function() {
	
	$('textarea[data-oc-toggle=\'ckeditor\']').ckeditor();

	var childParentEngine = function(){

            var classes = new Array();
            $(".form-group .parent").each(function(){
              var eleclass = $(this).attr('class').split(/\s/g);
              var $key = $.inArray("parent", eleclass);
              if( $key!=-1 ){
                classes.push( eleclass[$key+1] );
              }
            });

            $(".form-group .parent").each(function(){
              var parent = $(this);
              var eleclass = $(this).attr('class').split(/\s/g);
              var childClassName = '.child';
              var conditionClassName = '';
              var i;

              for (i=0;i<eleclass.length;i++) {
                if( $.inArray(eleclass[i], classes) < 0 ) {
                  continue;
                } else {
                 	var elecls =  '.' + eleclass[i];
                    var selected = $(parent).find('label input[type=radio]:checked').filter(':checked').val();
                    var radios = $(parent).find('.btn-default');

                    if (selected == 1) $(childClassName+elecls).parents('.form-group').show();
					else $(childClassName+elecls).parents('.form-group').hide();
					
                    $(radios).on("click", function(event){
                      	$(childClassName+elecls).parents('.form-group').fadeToggle();
                    });

                 
                }
              }
            });

    }//end childParentEngine

    childParentEngine();

	var tabs = $('.btn-toggle');
	tabs.each(function(i) {
		var tab = $(this).children('.btn');
		var ua = navigator.userAgent,
		event = (ua.match(/iPad/i)) ? "touchstart" : "click";
		tab.bind(event, function(e) {
			$(tab).removeClass("btn-success active");
			$(this).addClass(function() {
				if($(this).hasClass("btn-success")) return "";
				return "active btn-success";
			});
			//$(this).parent().find(".active").removeClass("btn-success");
		});
	});	
	
	//======= Create Cookies  MainTabs======= 
	var store_id ='';
	$('.main_tabs_vertical li a').bind('click', function(){
		menuTabs = $(this).attr('data-bs-target').replace('#', '').replace ('tab-', '');
		storeId = menuTabs.substr(menuTabs.length - 1);
		$.cookie('main_tabs_vertical',menuTabs);
	});
	
	main_tabs = $.cookie('main_tabs_vertical');
	if (main_tabs) changeMainTabs(main_tabs);
	
	//======= Font Setting======= 
	$(".fonts-change").each( function(){
		var $this = this;
		$(".items-font",$this).hide();  
		$(".font-"+$(".type-fonts:checked",$this).val(), this).show();
	 
		$(".type-fonts", this).change( function(){
			$(".items-font",$this).hide();
			$(".font-"+$(this).val(), $this).show();
		} );
	});
	
})

function changeMainTabs($menuItem){

}


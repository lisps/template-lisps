/**
 *  We handle several device classes based on browser width.
 *
 *  - desktop:   > __tablet_width__ (as set in style.ini)
 *  - mobile:
 *    - tablet   <= __tablet_width__
 *    - phone    <= __phone_width__
 */
var device_class = ''; // not yet known
var device_classes = 'desktop mobile tablet phone';
var abstand = 0;
function tpl_dokuwiki_mobile(){

    // the z-index in mobile.css is (mis-)used purely for detecting the screen mode here
    var screen_mode = jQuery('#screen__mode').css('z-index') + '';

    // determine our device pattern
    // TODO: consider moving into dokuwiki core
    switch (screen_mode) {
        case '1':
            if (device_class.match(/tablet/)) return;
            device_class = 'mobile tablet';
            break;
        case '2':
            if (device_class.match(/phone/)) return;
            device_class = 'mobile phone';
            break;
        default:
            if (device_class == 'desktop') return;
            device_class = 'desktop';
    }

    jQuery('html').removeClass(device_classes).addClass(device_class);

    // handle some layout changes based on change in device
    var $handle = jQuery('#dokuwiki__aside h3.toggle');
    var $toc = jQuery('#dw__toc h3');

    if (device_class == 'desktop') {
        // reset for desktop mode
        
		if($handle.length) {
            $handle[0].setState(1);
            $handle.hide();
        }
        if($toc.length) {
            $toc[0].setState(1);
        }
		
		//Seitenleiste wollen wir zeigen
		$handle.show();
		if(jQuery('#dokuwiki__aside').length){
			if(jQuery.cookie("sidebar_close")==1){ //cookie gesetzt?
				//wenn Seitenleiste geschlossen 
				$handle.removeClass('open');
				$handle.addClass('closed');
				$handle[0].setState(-1);
				jQuery('#dokuwiki__aside div.content').hide();
				jQuery('#dokuwiki__top').removeClass('showSidebar');
				jQuery('#dokuwiki__aside').css('position','absolute');
				jQuery('#dokuwiki__aside').css('top','');
				jQuery('#dokuwiki__aside').css('margin-top','-23px');
				
				
			}else { //Seitenleiste offen
				jQuery('#dokuwiki__top').addClass('showSidebar');
				jQuery('#dokuwiki__aside').css('position','fixed');
				jQuery('#dokuwiki__aside').css('top',abstand-jQuery(window).scrollTop());

			}
		}
		
    }
    if (device_class.match(/mobile/)){
        // toc and sidebar hiding
        if($handle.length) {
            $handle.show();
            $handle[0].setState(-1);
        }
        if($toc.length) {
            $toc[0].setState(-1);
        }
		
		//Normalwerte wiederherstellen
		jQuery('#dokuwiki__top').addClass('showSidebar');
		jQuery('#dokuwiki__aside').css('position','relative');
		jQuery('#dokuwiki__aside').css('top','');
		jQuery('#dokuwiki__aside').css('margin-top','');
    }
}

jQuery(function(){
    var resizeTimer;
	var $sidebar = jQuery('#dokuwiki__aside');

	if($sidebar.length)
		abstand = $sidebar.offset().top; //Abstand der Seitenleiste nach oben
	
	dw_page.makeToggle('#dokuwiki__aside h3.toggle','#dokuwiki__aside div.content');

    tpl_dokuwiki_mobile();
    jQuery(window).bind('resize',
        function(){
            if (resizeTimer) clearTimeout(resizeTimer);
            resizeTimer = setTimeout(tpl_dokuwiki_mobile,200);
        }
    );
	
	//Seitenleiste mitscrollen lassen wenn im Desktop Mode
	jQuery(window).scroll(function() {    
		if($handle.hasClass('open')&&jQuery('#screen__mode').css('z-index') + '' != '1' 
			&& $handle.hasClass('open')&&jQuery('#screen__mode').css('z-index') + '' != '2'){
				if(jQuery(this).scrollTop() > abstand) {        
					jQuery('#dokuwiki__aside').css('top',0); //Seitbar nach oben
				}else {       
					jQuery('#dokuwiki__aside').css('top',abstand-jQuery(this).scrollTop());
	
				}
			}
	});
	
	//wenn Seitbar ausgeblended werden soll
	var $handle = jQuery('#dokuwiki__aside h3.toggle');
	jQuery($handle).bind('click',
		function(){
			if(jQuery('#screen__mode').css('z-index') + '' == '0') {
				if($handle.hasClass('closed')){
					
					jQuery('#dokuwiki__aside').css('position','absolute');
					jQuery('#dokuwiki__aside').css('top','');
					jQuery('#dokuwiki__aside').css('margin-top','-23px');
					jQuery.cookie("sidebar_close",1);
					setTimeout("jQuery('#dokuwiki__top').removeClass('showSidebar')",100);
				} else {
					jQuery('#dokuwiki__top').addClass('showSidebar');
					jQuery('#dokuwiki__aside').css('position','fixed');
					//alert(jQuery(this).scrollTop());
					jQuery('#dokuwiki__aside').css('top',abstand-jQuery(window).scrollTop());
					jQuery('#dokuwiki__aside').css('margin-top','');
					jQuery.cookie("sidebar_close",0);
					
				}
			}
		}
	);
	
	

    // increase sidebar length to match content (desktop mode only)
    var $sidebar = jQuery('.desktop #dokuwiki__aside');
    if($sidebar.length) {
        var $content = jQuery('#dokuwiki__content div.page');
        $content.css('min-height', $sidebar.height());
    }
});

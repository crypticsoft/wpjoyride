(function($) {
$(document).ready( function() {

if(window.location.hash){  // Fragment exists
	var hash = window.location.hash.substring(1);//Puts hash in variable, and removes the # character
	var tours = JSON.parse($("#tour_data").html());
/*
	var tours = {

		'tour-title' : { //#hashtag
			'steps' : [
				{
					'id' : 'menu-settings', //parent ID to reference
					//'class' : 'menu-media',
					'text' : 'Next', //button text
					'content' : 'To edit the website title you can navigate to Settings -> General',
					'title' : 'General Settings',
					'options' : 'tipLocation:top;tipAnimation:pop',
				},
				{
					'id' : 'blogname', //parent ID to reference
					//'class' : 'custom',
					'text' : 'Next', //button text
					'content' : 'Add your own descriptive site title, keep it short.',
					'title' : 'Add a Title',
					'options' : 'tipLocation:right;tipAnimation:pop',
				},
				{
					'id' : 'submit', //parent ID to reference
					//'class' : 'custom',
					'text' : 'Done', //button text
					'content' : 'When you are finished don\'t forget to click "Submit" to save your changes.',
					'title' : 'Save Changes',
					'options' : 'tipLocation:top;tipAnimation:pop',
				},							

			]//end 'steps'
		} //end tour-video		

	};//end tours
*/

	if(hash!=''){
		
		//set the container for the tip(s)
		var tip_id = 'joyRideTipContent-' + hash;
		var tips = $("<ol/>",{ id: tip_id });
		var dat_class, dat_id;

		$.each(tours, function(i, val) {
			if(i==hash){ //if we have a match, parse out the object values into list items
				$.each(val.steps, function() {
					dat_class = this.class != undefined ? ' data-class="' + this.class + '"' : '';
					dat_id = this.id != undefined ? ' data-id="' + this.id + '"' : '';
					dat_options = this.options != undefined ? ' data-options="' + this.options + '"' : '';
					// data-id  - data-class are optional attributres, if neither is found then modal window is used
					tips.append('<li data-text="' + this.text + '"' + dat_id + dat_class + dat_options + '><h2>' + this.title + '</h2><p>' + this.content + '</p></li>');
				});
			}//end if
		});//end each

		$('body').append(tips); //append tip(s) to the body

		if( $('#'+tip_id).length > 0 ){

			$('#'+tip_id).css('display','none').joyride();	

			$('.joyride-next-tip').bind('click',function() {

				//sometimes the right align pushes the element off the page and beyond the content width
				//check the position and content width, find the offset and reset the position left if beyond content area
				setTimeout(function() {
      			// Do something after .5 second
				var pos = $('.joyride-tip-guide:visible').position();
				var cwidth = $('#wpcontent').width();
				var pwidth = $('.joyride-tip-guide:visible').width() / 2;
				var poffset = cwidth - pwidth;
				var npos = Math.round( pos.left ) - poffset;

				if( Math.round( pos.left ) > cwidth ){
					$('.joyride-tip-guide:visible').animate({left: '-=' + npos});
				}
				// animate top position to consider the wpadminbar height, need to find solution for the first slide
				if( $('#wpadminbar').length > 0 ){
					var ntop = $('#wpadminbar').height();					
					$('.joyride-tip-guide:visible').animate({top: '-=' + ntop });
				}

				}, 500);
			});
			setTimeout(function() {		
				$('#'+tip_id).joyride();	
				// animate top position to consider the wpadminbar height, need to find solution for the first slide
				if( $('#wpadminbar').length > 0 ){
					var ntop = $('#wpadminbar').height();					
					$('.joyride-tip-guide:visible').animate({top: '-=' + ntop });
				}

			}, 1000);

		} 

	}//end if hash
	}//end if location.hash
	});


})(jQuery);
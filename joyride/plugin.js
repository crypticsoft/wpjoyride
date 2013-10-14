(function($) {

$(document).ready( function() {

$("#selector_gadget").live('click',function(e){
	e.preventDefault();
	refreshSelectors();
});
$("#tours select").live('change', function() {
	var delete_button = $("<button/>", { id: "tour_delete", class: 'button button-small button-primary', html: 'Delete Tour' });
	var tour_id = $(this).val();
	if( tour_id ) {
		$("#tours").append( delete_button );
	}
});
$("#tour_delete").live('click', function(){
	// delete tour
	tourObj.deleteTour();
});

$("#tour_url").val( location.pathname + location.search );

$("#form-ui form").live('submit', function(event){
	event.preventDefault();
	// save tip
	tipObj.saveTip();
});

$("#tipCollection button.delete-tip").live('click',function(){
	// delete tip
	tipObj.deleteTip();
});

$( "#tourInputs span.title").live('click',function() {
	$(this).parent().find('div.form').toggle();
});
$( "#tourInputs button" ).click(function(){
	// save tour
	tourObj.saveTour();
});
$( "#accordion" ).accordion();
$( "#sortable" ).sortable({
/*  change: function( event, ui ) {
  	console.log(ui);
  }*/

		start: function(event, ui) {
            var start_pos = ui.item.index();
            ui.item.data('start_pos', start_pos);
            console.log('start' + start_pos);
        },
        change: function(event, ui) {
            var start_pos = ui.item.data('start_pos');
            var index = ui.placeholder.index();
            if (start_pos < index) {
                $('#sortable li:nth-child(' + index + ')').addClass('highlights');
            } else {
                $('#sortable li:eq(' + (index + 1) + ')').addClass('highlights');
            }
            console.log('change' + index);
        },
        update: function(event, ui) {
            $('#sortable li').removeClass('highlights');
        }  
});
$( "#sortable" ).disableSelection();

var tipObj = {
	saveTip : function() {
		// capture form data and serialize fields
		var dat = $("#form-ui form").serialize();
		// make sure you set an appropriate key for the new value
		dat = dat + '&' + $.param({ 'tour_id': $("#tour_id").val() });

		$.post( ajaxurl + '?action=save_tip', dat, function( data ) {
			
			data = $.parseJSON(data);
			var tour_id = $("#tour_id").val();

			if( data ){
				if( $("#tipCollection").is(':empty') ){
					var listObj = $("#tipCollection").append("<ul></ul>").find('ul');
				}else{
					var listObj = $("#tipCollection ul");
				}
					listObj.append('<li>' + data.tip_title + '<button class="delete-tip" data-title="' + data.tip_title + '" data-post-id="' + tour_id + '">Delete</button></li>');			
			}
		});		
		return;
	},
	deleteTip : function() {
		var id, title;
		id = $(this).data('post-id');
		title = $(this).data('title');
		parent_el = $(this).parent();
		var dat = { tip_title: title, post_id: id }
		$.post( ajaxurl + '?action=delete_tip', dat, function( data ) {
			// remove list item
			parent_el.remove();
		});
		return;
	}
}
var tourObj = {
	parseHTML : function(tip_id,tours) {

		$.each( tours, function(i,tour) {
				//console.log(tour.title);
				var hash = window.location.hash.substring(1) //Puts hash in variable, and removes the # character
				
				if( tour.hashtag.replace('#','') || tour.hashtag.replace('.','') ){
					tour.hashtag = tour.hashtag.substring(-1);
				}
				//console.log( hash + '=' + tour.hashtag );
				if( tour.hashtag != hash ) return;
				//set the container for the tip(s)
				var tips = $("<ol/>",{ id: tip_id });
				var dat_class, dat_id;
				//console.log(tour.hashtag + '=' + hash);
				//if( tour.hashtag == hash ){ //if we have a match, parse out the object values into list items
					//console.log(tour);
					$.each( tour.steps, function( step ) {
						dat_class = this.class != undefined ? ' data-class="' + this.class + '"' : '';
						dat_id = this.id != undefined ? ' data-id="' + this.id + '"' : '';
						dat_options = this.options != undefined ? ' data-options="' + this.options + '"' : '';
						// data-id  - data-class are optional attributres, if neither is found then modal window is used
						tips.append('<li data-text="' + this.text + '"' + dat_id + dat_class + dat_options + '><h2>' + this.title + '</h2><p>' + this.content + '</p></li>');
					});
				//}//end if

		$('body').append(tips); //append tip(s) to the body

		});


	},
	tipForm : function() {
		// build ui
		var tips_form = '<div id="tips-form">';

		tips_form += '<div class="input-wrap">';
		tips_form += '<p class="label"><label for="parent_id">';
		tips_form += 'Parent ID / Selector <em>( Be sure to use # for ID or . for class names )</em>';
		tips_form += '</label></p>';
		tips_form += '<input type="text" id="parent_id" name="parent_id" class="text" />';
		tips_form += '<button id="selector_gadget">Selector Gadget</button>';
		tips_form += '</div>';

		tips_form += '<div class="input-wrap">';
		tips_form += '<p class="label"><label for="tip_title">';
		tips_form += 'Tip Title';
		tips_form += '</label></p>';
		tips_form += '<input type="text" id="tip_title" name="tip_title" class="text" />';
		tips_form += '</div>';

		tips_form += '<div class="input-wrap">';
		tips_form += '<p class="label"><label for="tip_text">';
		tips_form += 'Tip Text';
		tips_form += '</label></p>';
		tips_form += '<input type="text" id="tip_text" name="tip_text" class="text" />';
		tips_form += '</div>';

		tips_form += '<div class="input-wrap">';
		tips_form += '<p class="label"><label for="tip_location">';
		tips_form += 'Tip Location';
		tips_form += '</label></p>';
		tips_form += '<ul class="checkbox-list">';
		tips_form += '<li><label><input type="checkbox" id="tip_location" name="tip_location" class="checkbox" value="Top" /> Top</label></li>';
		tips_form += '<li><label><input type="checkbox" id="tip_location" name="tip_location" class="checkbox" value="Bottom" /> Bottom</label></li>';
		tips_form += '<li><label><input type="checkbox" id="tip_location" name="tip_location" class="checkbox" value="Left" /> Left</label></li>';
		tips_form += '<li><label><input type="checkbox" id="tip_location" name="tip_location" class="checkbox" value="Right" /> Right</label></li>';				
		tips_form += '</ul>';
		tips_form += '</div>';

		tips_form += '<div class="input-wrap">';
		tips_form += '<p class="label"><label for="tip_animation">';
		tips_form += 'Tip Animation';
		tips_form += '</label></p>';
		tips_form += '<ul class="checkbox-list">';
		tips_form += '<li><label><input type="checkbox" id="tip_animation" name="tip_animation" class="checkbox" value="Pop" /> Pop</label></li>';
		tips_form += '<li><label><input type="checkbox" id="tip_animation" name="tip_animation" class="checkbox" value="Fade" /> Fade</label></li>';		
		tips_form += '</ul>';
		tips_form += '</div>';
/*
		tips_form += '<div class="input-wrap">';
		tips_form += '<p class="label"><label for="tour">';
		tips_form += 'Tour';
		tips_form += '</label></p>';
		//tips_form += '<input type="text" id="button_text" name="tour" class="text" />';
		tips_form += '<select name="tour" id="tour" class="text">';
		tips_form += tour_select;
		tips_form += '</select>';
		tips_form += '</div>';
*/
		tips_form += '<div class="input-wrap">';
		tips_form += '<p class="label"><label for="button_text">';
		tips_form += 'Button Text';
		tips_form += '</label></p>';
		tips_form += '<input type="text" id="button_text" name="button_text" class="text" />';
		tips_form += '</div>';
		tips_form += '</div>';

		var form_ui = '<div id="form-ui" class="hide"><form>';
/*
		form_ui += '<p><label for="tour_title">Tour Title</label><input type="text" name="tour_title"></p>';
		form_ui += '<p><label for="tour_hashtag">Tour Hashtag</label><input type="text" name="tour_hashtag"></p>';
		form_ui += '<p><label for="tour_url">Tour URL</label><input type="text" name="tour_url"></p>';
		form_ui += '<p><label for="tour_group">Tour Group</label></p>';
		form_ui += '<select name="tour_group" id="tour_group" class="text"><option>Select One</option>';
		form_ui += tour_group_select;
		form_ui += '</select>';
*/		
		form_ui += '<div class="steps">' + tips_form + '</div>';		
//		form_ui += '<p><input type="button" value="Add Tip" class="button" id="add_tip"></p>';
		form_ui += '<p><input type="button" id="preview_tip" value="Preview" class="button button-primary button-small"></p>';		
		form_ui += '<p><input type="submit" value="Save" class="button button-primary button-small"></p>';		
//		form_ui += '<input type="hidden" name="tour_id">';
//		form_ui += '<input type="hidden" name="action" value="create_tour">'
		form_ui += '</form></div>';

		$('#tipInputs').append(form_ui);	

	},
	parseTours : function() {
		var tours = $("#tours");
		var all_tours = $.parseJSON( $("#tours_all").html() );
		var tours_select = $("<select/>",{ name: 'tours_select' }).appendTo( tours );
		tours_select.append( $("<option/>",{ text: "Select Tour" }).attr('selected','selected') );

		$.each( all_tours, function(i,tour) {
			tours_select.append( $("<option/>",{ value: tour.id, text: tour.title }) );
		});
	},
	saveTour : function() {

		var item = {
	        tour_url: $("#tour_url").val(),
	        tour_hashtag: $("#tour_hashtag").val(),
	        tour_title: $("#tour_title").val(),
	        tour_id: $("#tour_id").val() ? $("#tour_id").val() : null
	    }
	    $.ajax({
	    	url: ajaxurl + '?action=save_tour',
	    	dataType: 'json',
	    	data: item,
	    	type: 'POST'
	    }).done(function( data ) {
	    	// set the hidden field
	    	if( data.id ){
		    	$("#tour_id").val( data.id );
		    	$("#tourInputs").find("button").html('Save Tour');
		    	$("#tourInputs").append( $("<span/>",{ html: data.title, class: 'title' }) );
		    	$("#tourInputs div.form").hide();
		    	// show tips form
		    	$("#tipInputs, #form-ui").removeClass("hide");		    	
	    	}
	    }).error(function( err ) {
	    	console.log(err);
	    });

	},
	deleteTour : function() {
		var tour_id = $("#tours select").val();
		if( tour_id ){
			// build post data to delete tour
			var dat = {
				post_id : tour_id
			}
			// post delete_tour action to return new json data set
			$.post( ajaxurl + '?action=delete_tour', dat, function(data){
				// update tours json in DOM
				$("#tours_all").html( data );
				$("#tours select, #tour_delete").remove();

				// update select menu
				tourObj.parseTours();
			});
		}
		return;
	}
}

function refreshSelectors() {
	
    var selector_field = $('#parent_id');
    var elementHoverClass = 'highlighter';

    //on mouseenter on every element in the body except the brosho wrapper and overlay
    $('body *:not(#tourPanel, #tourPanel *)').hover(function() {
    	$(this).addClass(elementHoverClass); //add the hover class to the current element
    	var selectorText = extractCssSelectorPath($(this));
    	selector_field.val( selectorText ); //update selector text on hover
    }, function() { //on mouseleave
      $(this).removeClass(elementHoverClass); //remove the hover class
      selector_field.empty(); //clear selector text	      
    }).click(function() { //on element click
      var el = $(this); //store the current element
      console.log('turn this shit off');
      $('body *').removeClass(elementHoverClass); //remove the brosho hover class on every element so we dont generate a false path
      selector_field.val(extractCssSelectorPath(el)); //set the css selector path of the current element
      $('body *:not(#tourPanel, #tourPanel *)').unbind( "hover" );
      return false; //dont follow the link if it is one
    });//end hover function
};	//end refreshSelectors

function extractCssSelectorPath(el) {
	var elementHoverClass = 'highlighter';
	el.removeClass( elementHoverClass );
	
	if(el.attr('id')) return '#' + el.attr('id'); //it is (should) be an unique element, return the id selector

	var path = extractCssSelectorPath(el.parent()); //to prepend the path of the parent element

	if(el.attr('class') && el.attr('class') != ' ' ) return path + ' .' + el.attr('class'); //if the lement has a class use this as selector
	return path + ' ' + el.get(0).tagName.toLowerCase(); //return the current path with the tag name of the element if t has no id or class
};

// build tips form
tourObj.tipForm();
tourObj.parseTours();

if(window.location.hash){  // Fragment exists
	var hash = window.location.hash.substring(1);//Puts hash in variable, and removes the # character
	var tip_id = 'joyRideTipContent-' + hash;	
	var tour = $.parseJSON( $("#tour_data").html() );
	var tours = $.parseJSON( $("#tours_all").html() );
	var tour_select, tour_group_select;
	$.each(tours, function(){
		tour_select += '<option value="' + this.id + '">' + this.title + '</option>';
	});
	var tours_groups = JSON.parse( $("#tours_groups").html() );
	$.each(tours_groups, function() {
		tour_group_select += '<option value="' + this.id + '">' + this.title + '</option>';
	});
	if(hash!=''){
		// parse JSON into ol>li list items for the tour : tips, then append to the body
		tourObj.parseHTML( tip_id, tours );

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
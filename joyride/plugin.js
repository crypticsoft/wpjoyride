(function($) {

$(document).ready( function() {

	window.wpTour = {};
	window.wpTip = {};
	var wpt = window.wpTour;
	var wptip = window.wpTip;

	wpt = tourObj;
	wptip = tipObj;

	// load events
	wptip.loadEvents();
	wpt.loadEvents();

	// parse tour JSON to DOM
	wpt.parseTours();
	// check for hashtag, if found then load joyride elements and plugin
	wpt.loadJoyride();

});

var tourObj = {
	id: null,
	hashtag: null,
	url: null,
	tips: [],
	loadJoyride : function(){

		if(window.location.hash){  // Fragment exists
			var hash = window.location.hash.substring(1);
			var tip_id = 'joyRideTipContent-' + hash;	
			var tours = $.parseJSON( $("#tours_all").html() );
			if(hash!=''){
				this.parseHTML(tip_id, tours);
				$('#'+tip_id).joyride();//not sure why i need this here but joyride won't start without it
				this.init_joyride(tip_id);
			}//end if hash
		}//end if location.hash

	},

	loadEvents : function(){

		var obj = this;
		$("#tourTab").click(function(){
			
			var is_visible = $(this).parent().attr('style') == undefined ? false : true;
			if( !is_visible ) {
				$("#tourPanel").animate({ right: 0 });
			}else{
				$("#tourPanel").animate({ right: -250 },function(){
					$(this).removeAttr('style');
				});
			}

		});

		$( "#tourInputs span.title").live('click',function() {
			$(this).parent().find('div.form').toggle();
		});
		$( "#tourInputs button" ).click(function(){
			// save tour
			obj.saveTour();
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
			obj.deleteTour();
		});

		$("#tour_url").val( location.pathname + location.search );

	},
	parseHTML : function(tip_id,tours) {

		$.each( tours, function(i,tour) {
			var hash = window.location.hash.substring(1) //Puts hash in variable, and removes the # character
			
			if( tour.hashtag.replace('#','') || tour.hashtag.replace('.','') ){
				tour.hashtag = tour.hashtag.substring(-1);
			}
			//console.log(tour.hashtag + '=' + hash);
			if( tour.hashtag != hash ) return;
			//set the container for the tip(s)
			var tips = $("<ol/>",{ id: tip_id });
			var dat_class, dat_id;
				$.each( tour.steps, function( step ) {
					dat_class = this.class != undefined ? ' data-class="' + this.class + '"' : '';
					dat_id = this.id != undefined ? ' data-id="' + this.id + '"' : '';
					dat_options = this.options != undefined ? ' data-options="' + this.options + '"' : '';
					tips.append('<li data-text="' + this.text + '"' + dat_id + dat_class + dat_options + '><h2>' + this.title + '</h2><p>' + this.content + '</p></li>');
				});

		$('body').append(tips); //append tip(s) to the body

		});


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
	},
	init_joyride : function( tip_id ) {

		$(window).load(function() {			
			//console.log($('#'+tip_id));
			$('#'+tip_id).joyride({
			'postStepCallback': function(i,val){ 	// A method to call after each step
				if( $('#wpadminbar').length > 0 ){
					var ntop = $('#wpadminbar').height();					
					$('.joyride-tip-guide:visible').animate({top: '-=' + ntop });
				}
			}        
			
			});
		});


	}
}

var tipObj = {
	loadEvents : function(){
		var obj = this;

		$("#selector_gadget").live('click',function(e){
		        e.preventDefault();
		        refreshSelectors();
		});
		$("#form-ui form").submit(function(event){
			event.preventDefault();
			// save tip
			obj.saveTip();
		});

		$("#tipCollection button.delete-tip").live('click',function(){
			// delete tip
			obj.deleteTip($(this));
		});

	},
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
			// clear out form fields
			$("#tips-form input[type=text]").val('');
			$("#tips-form input[type=checkbox]").removeAttr('checked');

		});		
		return;
	},
	deleteTip : function(el) {
		var id, title;
		id = el.data('post-id');
		title = el.data('title');
		parent_el = el.parent();
		var dat = { tip_title: title, post_id: id }
		$.post( ajaxurl + '?action=delete_tip', dat, function( data ) {
			// remove list item
			parent_el.remove();
		});
		return;
	}
}

/* helper functions */
function extractCssSelectorPath(el) {
		var elementHoverClass = 'highlighter';
		el.removeClass( elementHoverClass );
		
		if(el.attr('id')) return '#' + el.attr('id'); //it is (should) be an unique element, return the id selector

		var path = extractCssSelectorPath(el.parent()); //to prepend the path of the parent element
		if(el.attr('class') && el.attr('class') != ' ' ) return path + ' .' + el.attr('class'); //if the lement has a class use this as selector
		return path + ' ' + el.get(0).tagName.toLowerCase(); //return the current path with the tag name of the element if t has no id or class
}

function refreshSelectors() {

    //on mouseenter on every element in the body except the brosho wrapper and overlay
    $('body *:not(#tourPanel, #tourPanel *)').hover(function() {
    	var el = $(this);
    	var selectorText = extractCssSelectorPath(el);
    	console.log(selectorText);
	    var selector_field = $('#parent_id');
	    var elementHoverClass = 'highlighter';

    	el.addClass(elementHoverClass); //add the hover class to the current element
    	selector_field.val( selectorText ); //update selector text on hover

    }, function() { //on mouseleave
	    var selector_field = $('#parent_id');
	    var elementHoverClass = 'highlighter';
		$(this).removeClass(elementHoverClass); //remove the hover class
		selector_field.empty(); //clear selector text	      

    }).click(function(e) { //on element click
		var el = $(this); //store the current element
    	var selectorText = extractCssSelectorPath(el);	    
	    var elementHoverClass = 'highlighter';

		$('body *').removeClass(elementHoverClass); //remove the brosho hover class on every element so we dont generate a false path
		$('body *:not(#tourPanel, #tourPanel *)').unbind("mouseenter mouseleave click");
		return false; //dont follow the link if it is one

    });//end hover function

}
	
})(jQuery);



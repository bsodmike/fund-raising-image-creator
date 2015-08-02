jQuery(document).ready(function(){
	// Add a listener to the "Create New Image!" button
	jQuery( "#generate_button" ).on( "click", function(){ loadImage(); } );

	// Attach a listener to each usable image
	jQuery( ".possible_image_to_use").on( "click", function(){ updateImageToUse( jQuery( this ) ); } );

	// Attach a listener to each goal indicator color
	jQuery( ".possible_goal_indicator_color_to_use").on( "click", function(){ updateGoalIndicatorColor( jQuery( this ) ); } );

	// Attach a listener to each border color
	jQuery( ".possible_border_color_to_use").on( "click", function(){ updateBorderColor( jQuery( this ) ); } );

	 // Update our image whenever we change our raised amount
	jQuery( "#raised_amount" ).on( "keyup click", function(){ removeNonNumericCharacters( jQuery( this ) ); loadImage(); } );

	// Update our image whenever we change our goal amount
	jQuery( "#goal_amount" ).on( "keyup click", function(){ removeNonNumericCharacters( jQuery( this ) ); loadImage(); } );

	// Select the entire URL to the generated image whenever the user clicks anywhere in the URL
	// NOTE: This doesn't cause the input to be selected on mobile Safari (untested on Android)
	jQuery( "#fund_raising_image_url" ).on( "focus", function(){ jQuery( this ).select(); } );

	// Load the slider used to set the border width
	jQuery( "#border_width_slider" ).slider({
		range: "max",
		min: 0,
		max: 10,
		value: 1,
		create: function( event, ui ) {
			// When we create the slider, we want to put our title on the moving slider
			var borderWidthSlider = jQuery( "#border_width_slider" );
			var sliderTitle = borderWidthSlider.data( "slider_title" );
			borderWidthSlider.find( ".ui-slider-handle" ).text( sliderTitle );
		},
		slide: function( event, ui ) {
		  jQuery( "#border_width" ).val( ui.value );
		},
		stop: function(event, ui) {
			loadImage();
		}
	});
	
	// Load the slider used to set the goal indicator opacity
	jQuery( "#goal_indicator_opacity_slider").slider({
		range: "max",
		min: 1,
		max: 100,
		value: 80,
		create: function( event, ui ) {
			// When we create the slider, we want to put our title on the moving slider
			var borderWidthSlider = jQuery( "#goal_indicator_opacity_slider" );
			var sliderTitle = borderWidthSlider.data( "slider_title" );
			borderWidthSlider.find( ".ui-slider-handle" ).text( sliderTitle );
		},
		slide: function( event, ui ) {
			jQuery( "#goal_indicator_opacity" ).val( ui.value );
		},
		stop: function(event, ui) {
			loadImage();
		}
	});

	// Make sure things are appropriately hidden or shown based on chosen defaults
	resetSelectors();

	// Load the image for the first time
	loadImage();

	// Load our tooltips
	addTooltips();

});

/**
 * This function will reset our hidden values to the defaults
 */
function resetSelectors(){
	jQuery( "#image_to_use" ).val( "heart_for_underlay" );
	jQuery( "#goal_indicator_opacity" ).val( 80 );
	jQuery( "#goal_indicator_color" ).val( "red" );
	jQuery( "#goal_indicator_type" ).val( "underlay" );
	jQuery( "#border_width" ).val( 1 );
	jQuery( "#border_color" ).val( "black" );
}

/**
 * This function will update the selected image to use by changing the value in a hidden input, and changing the border around the selected image
 * This function will also update the type of goal indicator we want (based on the image the user selected), the choices are overlay and underlay
 * @param jQuery object chosenImageToUse - The image which the user has selected for use
 */
function updateImageToUse( chosenImageToUse ){
	jQuery( ".possible_image_to_use" ).removeClass( "selected" );
	chosenImageToUse.addClass( "selected" );
	jQuery( "#image_to_use" ).val( chosenImageToUse.data( "image_to_use" ) );
	jQuery( "#goal_indicator_type" ).val( chosenImageToUse.data( "goal_indicator_type" ) );
	loadImage(); // Now update our image
}

/**
 * This function will update chosen goal indicator color by changing the value in a hidden input, and changing the border around the selected color
 * @param jQuery object chosenGoalIndicatorColor - The color the user wishes to use for the goal indicator
 */
function updateGoalIndicatorColor( chosenGoalIndicatorColor ){
	jQuery( ".possible_goal_indicator_color_to_use" ).removeClass( "selected" );
	chosenGoalIndicatorColor.addClass( "selected" );
	jQuery( "#goal_indicator_color" ).val( chosenGoalIndicatorColor.data( "color_to_use" ) );
	loadImage();
}

/**
 * This function will update the chosen border color by changing the value in a hidden input, and changing the border around the selected color
 * @param jQuery object chosenBorderColor - The color the user wishes to use for the border
 */
function updateBorderColor( chosenBorderColor ){
	jQuery( ".possible_border_color_to_use" ).removeClass( "selected" );
	chosenBorderColor.addClass( "selected" );
	jQuery( "#border_color" ).val( chosenBorderColor.data( "color_to_use" ) );
	loadImage();
}

/**
 * This function will use the selected options to grab an image
 */
function loadImage(){
	if( !infoIsValid() ){
		// If we don't have valid info, we won't ask the server for an image
		return;
	}

	var basePath = window.location.href.substr( 0, window.location.href.lastIndexOf( "/" ) + 1 ); // This will give us the URL minus any file names
	var generatingScriptLocation = "build_fund_raising_image.php?";
	var imageInformation = jQuery( "#creator_wrapper .selection_input" ).serialize();
	jQuery( "#output_image" ).attr( "src", basePath + generatingScriptLocation + imageInformation );
	jQuery( "#fund_raising_image_url" ).val( basePath + generatingScriptLocation + imageInformation );
}

/**
 * This function will make sure we have all the data we need in order to create an image on the server
 * @return boolean - True if we have everything we need, false otherwise
 */
function infoIsValid(){
	// We'll only validate information that could be invalid during normal usage, further validation will be done server side
	
	var isValid = true;

	var raisedAmount = jQuery( "#raised_amount" ).val();
	var goalAmount = jQuery( "#goal_amount" ).val();

	if( raisedAmount === "" ){
		isValid = false;
	}else if( goalAmount === "" || goalAmount < 1 ){
		isValid = false;
	}

	return isValid;
}

/**
 * This function will remove any non numeric characters from a given inputs value
 * @param jQuery object inputToClean - The input we want to remove all non-numeric characters from
 */
function removeNonNumericCharacters( inputToClean ){
	inputToClean.val( inputToClean.val().replace( /[^0-9.]/g, '' ) );
}

/**
 * This function will add any tooltips that we need to the page
 */
function addTooltips(){
	var tooltipForUnderlayImageContainer = jQuery( "#tooltip_for_underlay_image_container" );
	tooltipForUnderlayImageContainer.tooltip({
		content: "With these images, our funding indicator will rise up beneath the image. This is the traditional style.",
		items: "#tooltip_for_underlay_image_container",
		tooltipClass: "tooltip",
		create: function( event, ui ){
			// For testing, we'll add a class to our tooltip_for_underlay_image_container to show that our toolTip was initialized
			tooltipForUnderlayImageContainer.addClass( 'has_tooltip' );
		}
	});
	
	var tooltipForOverlayImageContainer = jQuery( "#tooltip_for_overlay_image_container" );
	tooltipForOverlayImageContainer.tooltip({
		content: "With these images, our funding indicator is laid over the top of the image. This is a less traditional approach.",
		items: "#tooltip_for_overlay_image_container",
		tooltipClass: "tooltip",
		create: function( event, ui ){
			// For testing, we'll add a class to our tooltip_for_overlay_image_container to show that our toolTip was initialized
			tooltipForOverlayImageContainer.addClass( 'has_tooltip' );
		},
		destroy: function( event, ui ){
			// For testing, we'll remove the class added in create
			tooltipForOverlayImageContainer.removeClass( 'has_tooltip' );
		}
	});

}
/**
 * This function will test our resetSelectors function to make sure that it's resetting our selectors correctly
 */
QUnit.test("resetSelectors()", function( assert ){
	// Save a reference to the document elements we're going to check, so we don't have to find them multiple times
	var imageToUse = jQuery( "#image_to_use" );
	var goalIndicatorOpacity = jQuery( "#goal_indicator_opacity" );
	var goalIndicatorColor = jQuery( "#goal_indicator_color" );
	var goalIndicatorType = jQuery( "#goal_indicator_type" );
	var borderWidth = jQuery( "#border_width" );
	var borderColor = jQuery( "#border_color" );

	// Set our inputs to non-default values
	imageToUse.val( "wrong_image_for_everything" );
	goalIndicatorOpacity.val( 35 );
	goalIndicatorColor.val( "green" );
	goalIndicatorType.val( "overlay" );
	borderWidth.val( 8 );
	borderColor.val( "yellow" );

	// Call our resetSelects() function to reset all of our elements to their default value
	resetSelectors();

	// Make sure all of our values got reset to their default values
	assert.equal( imageToUse.val(), "heart_for_underlay", "imageToUse value was 'heart_for_underlay'" );
	assert.equal( goalIndicatorOpacity.val(), 80, "goalIndicatorOpacity value was 80" );
	assert.equal( goalIndicatorColor.val(), "red", "goalIndicatorColor value was red" );
	assert.equal( goalIndicatorType.val(), "underlay", "goalIndicatorType value was underlay" );
	assert.equal( borderWidth.val(), 1, "borderWidth value was 1" );
	assert.equal( borderColor.val(), "black", "borderColor value was black" );
});

/**
 * This function will test our updateImageToUse function to make sure that it's adjusting classes and values correctly
 */
QUnit.test("updateImageToUse()", function( assert ){
	var possibleImagesToUse = jQuery( '.possible_imageToUse' );
	var imageToUse = jQuery( "#image_to_use" );
	var goalIndicatorType = jQuery( "#goal_indicator_type" );

	// Case 1: Change our image to the "heart_for_underlay" image
	var heartForUnderlay = jQuery( "#possible_image_to_use_heart_for_underlay" );
	updateImageToUse( heartForUnderlay );

	// We need to confirm that only the #heart_for_underlay element has a "selected" class
	jQuery.each( possibleImagesToUse, function( index, element ){
		var jqueryElement = jQuery( element );
		var elementId = jqueryElement.attr( "id" );
		var elementHasClass = jqueryElement.hasClass( "selected" );

		if( elementId == "possible_image_to_use_heart_for_underlay" ){
			assert.ok( elementHasClass, elementId + " had 'selected' class" );
		}else{
			assert.notOk( elementHasClass, elementId + " did not have 'selected' class" );
		}
	});

	// We need to confirm that the #imageToUse input holds our heart_for_underlay value
	assert.equal( imageToUse.val(), "heart_for_underlay", "imageToUse value was 'heart_for_underlay'" );

	// We need to confirm that the goalIndicatorType was properly set to "underlay"
	assert.equal( goalIndicatorType.val(), "underlay", "goalIndicatorType value was 'underlay'" );


	// Case 2: Change our image to the "hour_glass" image
	var hour_glass = jQuery( "#possible_image_to_use_hour_glass" );
	updateImageToUse( hour_glass );

	// We need to confirm that only the #hour_glass element has a "selected" class
	jQuery.each( possibleImagesToUse, function( index, element ){
		var jqueryElement = jQuery( element );
		var elementId = jqueryElement.attr( "id" );
		var elementHasClass = jqueryElement.hasClass( "selected" );

		if( elementId == "possible_image_to_use_hour_glass" ){
			assert.ok( elementHasClass, elementId + " had 'selected' class" );
		}else{
			assert.notOk( elementHasClass, elementId + " did not have 'selected' class" );
		}
	});

	// We need to confirm that the #imageToUse input holds our hour_glass value
	assert.equal( imageToUse.val(), "hour_glass", "imageToUse value was 'hour_glass'" );

	// We need to confirm that the goalIndicatorType was properly set to "overlay"
	assert.equal( goalIndicatorType.val(), "overlay", "goalIndicatorType value was 'overlay'" );
});

/**
 * This function will test our updateGoalIndicatorColor function to make sure it's adjusting classes and values correctly
 */
QUnit.test("updateGoalIndicatorColor()", function( assert ){
	// Change our possible_goalIndicatorColor to yellow
	updateGoalIndicatorColor( jQuery( "#possible_goal_indicator_color_yellow" ) );

	// We need to confirm that only the element for possible_goalIndicatorColor_yellow has a "selected" class
	jQuery.each( jQuery( ".possible_goal_indicator_color_to_use" ), function( index, element ){
		var jqueryElement = jQuery( element );
		var elementId = jqueryElement.attr( "id" );
		var elementHasClass = jqueryElement.hasClass( "selected" );

		if( elementId == "possible_goal_indicator_color_yellow" ){
			assert.ok( elementHasClass, elementId + " had 'selected' class" );
		}else{
			assert.notOk( elementHasClass, elementId + " did not have 'selected' class" );
		}
	});

	// We need to assert that our hidden goalIndicatorColor input was updated
	assert.equal( jQuery( '#goal_indicator_color' ).val(), "yellow", "goalIndicatorColor value was 'yellow'" );
});

/**
 * This function will test our updateBorderColor function to make sure it's adjusting classes and values correctly
 */
QUnit.test("updateBorderColor()", function( assert ){
	// Change our border color
	updateBorderColor( jQuery( "#possible_border_color_green" ) );

	// We need to confirm that only the element for possible_borderColor_green has a "selected" class
	jQuery.each( jQuery( ".possible_border_color_to_use" ), function( index, element ){
		var jqueryElement = jQuery( element );
		var elementId = jqueryElement.attr( "id" );
		var elementHasClass = jqueryElement.hasClass( "selected" );

		if( elementId == "possible_border_color_green" ){
			assert.ok( elementHasClass, elementId + " had 'selected' class" );
		}else{
			assert.notOk( elementHasClass, elementId + " did not have 'selected' class" );
		}
	});

	// We need to confirm that our hidden borderColor input has been updated
	assert.equal( jQuery( "#border_color" ).val(), "green", "borderColor value was 'green'" );
});

/**
 * This function will test our loadImage function to make sure it builds our URL correctly
 */
QUnit.test("loadImage()", function( assert ){

	// Case 1: We load an image with all the default information
	loadImage();
	// Reason for split: the domain and path may change, so we only want to compare the parts of the URL that generate our images
	var actualImageSrc = jQuery( "#output_image" ).attr( "src" ).split( "?" )[1];
	var actualUrl = jQuery( "#fund_raising_image_url" ).val().split( "?" )[1];

	var expectedBuiltUrl = 'image_to_use=heart_for_underlay&goal_indicator_type=underlay&raised_amount=50&goal_amount=100&border_width=1&border_color=black&goal_indicator_opacity=80&goal_indicator_color=red';

	// Assert that the fund_raising_image_url input was updated correctly
	assert.equal( actualUrl, expectedBuiltUrl, "fund_raising_image_url value was as expected" );

	// Assert that the output_image source was updated correctly
	assert.equal( actualImageSrc, expectedBuiltUrl, "output_image URL was as expected" );

	// Case 2: We load an image after updating some fields
	updateBorderColor( jQuery( "#possible_border_color_yellow" ) );
	updateGoalIndicatorColor( jQuery( "#possible_goal_indicator_color_green" ) );
	updateImageToUse( jQuery( "#possible_image_to_use_hour_glass" ) );

	loadImage();
	// Reason for split: the domain and path may change, so we only want to compare the parts of the URL that generate our images
	actualImageSrc = jQuery( "#output_image" ).attr( "src" ).split( "?" )[1];
	actualUrl = jQuery( "#fund_raising_image_url" ).val().split( "?" )[1];
	expectedBuiltUrl = 'image_to_use=hour_glass&goal_indicator_type=overlay&raised_amount=50&goal_amount=100&border_width=1&border_color=yellow&goal_indicator_opacity=80&goal_indicator_color=green';

	// Assert that the fund_raising_image_url input was updated correctly
	assert.equal( actualUrl, expectedBuiltUrl, "fund_raising_image_url value was as expected" );

	// Assert that the output_image source was updated correctly
	assert.equal( actualImageSrc, expectedBuiltUrl, "Actual output_image URL was as expected" );
});

/**
 * This function will test our infoIsValid function to make sure it properly detects invalid info
 */
QUnit.test("infoIsValid()", function( assert ){
	var raisedAmount = jQuery( "#raised_amount" );
	var goalAmount = jQuery( "#goal_amount" );

	// Case 1: Raised amount is empty
	raisedAmount.val( "" );
	assert.notOk( infoIsValid(), "Case 1 returned false" );

	// Case 2: Goal amount is empty
	goalAmount.val( "" );
	assert.notOk( infoIsValid(), "Case 2 returned false" );

	// Case 3: Goal amount is less than 1
	goalAmount.val( 0 );
	assert.notOk( infoIsValid(), "Case 3 returned false" );

	// Case 4: Raised amount and goal amount are valid
	raisedAmount.val( 35 );
	goalAmount.val( 70 );
	assert.ok( infoIsValid(), "Case 4 returne true" );
});

/**
 * This function will test our removeNonNumericCharacters function to make sure it removes all non numeric characters from an input
 */
QUnit.test("removeNonNumericCharacters()", function( assert ){
	var raisedAmount = jQuery( "#raised_amount" );

	// Case 1: input has letters
	raisedAmount.val( "55se" );
	removeNonNumericCharacters( raisedAmount );
	assert.equal( raisedAmount.val(), "55", "Raised amount was updated to 55" );

	// Case 2: input has minus sign
	raisedAmount.val( "-35" );
	removeNonNumericCharacters( raisedAmount );
	assert.equal( raisedAmount.val(), "35", "Raised amount was updated to 35" );
});

/**
* This function will test our addTooltips() function to make sure all of our tool tips are initialized
 */
QUnit.test("addTooltips()", function( assert) {
	// addTooltips is called on document ready (testing it here using assert.async proved problematic)
	assert.equal( jQuery( '.has_tooltip').length , 2, "2 tooltips were initialized");
});
<?php
	require_once( './fundRaisingImageCreator.php' );
	$fundRaisingImage = new fundRaisingImageCreator();

	$fundRaisingImage->setFontLocation( './fonts/arial_black.ttf' );

	$imagesLocation = './images/';
	$availableImagesToUse = array(
		'heart' => 'heart.png',
		'hour_glass' => 'hour_glass.png',
		'heart_for_underlay' => 'heart_for_underlay.png',
		'thermometer_for_underlay' => 'thermometer_for_underlay.png',
	); // The images we have available for the user to choose from
	
	if( array_key_exists( 'raised_amount', $_REQUEST ) ){
		$fundRaisingImage->setRaisedAmount( $_REQUEST['raised_amount'] );
	}

	if( array_key_exists( 'goal_amount', $_REQUEST ) ){
		$fundRaisingImage->setGoalAmount( $_REQUEST['goal_amount'] );
	}

	if( array_key_exists( 'image_to_use', $_REQUEST ) && array_key_exists( $_REQUEST['image_to_use'], $availableImagesToUse ) ){
		// If the user has given us a choice of image, and that image is a valid choice, we use their chosen image
		$fundRaisingImage->setFundRaisingImage( $imagesLocation . $availableImagesToUse[$_REQUEST['image_to_use']] );
	}else{
		// Otherwise we'll use the heart by default
		$fundRaisingImage->setFundRaisingImage( $imagesLocation . $availableImagesToUse['heart'] );
	}

	if( array_key_exists( 'border_width', $_REQUEST ) ){
		$fundRaisingImage->setBorderWidth( $_REQUEST['border_width'] );
	}

	if( array_key_exists( 'border_color', $_REQUEST ) ){
		$fundRaisingImage->setBorderColor( $_REQUEST['border_color'] );
	}

	if( array_key_exists( 'goal_indicator_color', $_REQUEST ) ){
		$fundRaisingImage->setGoalIndicatorColor( $_REQUEST['goal_indicator_color'] );
	}

	if( array_key_exists( 'goal_indicator_opacity', $_REQUEST ) ){
		$fundRaisingImage->setGoalIndicatorOpacity( $_REQUEST['goal_indicator_opacity'] );
	}

	if( array_key_exists( 'goal_indicator_type', $_REQUEST ) ){
		$fundRaisingImage->setGoalIndicatorType( $_REQUEST['goal_indicator_type'] );
	}

	// We also want to use an image cache to reduce server load (if we've already generated an image before, we'll just load it from our cache)
	$fundRaisingImage->setImageCacheLocation( "./image_cache/" );

	$imageSuccess = $fundRaisingImage->displayFundRaisingImage(); // $imageSuccess will be set to true if the image was successfully created
	if( !$imageSuccess ){
		// If we didn't successfully make an image, we could find out why not here
	}
<?php

/**
 * This class will use the built in php image functions to create a fund raising image
 *
 * NOTES:
 *  - For reasons of compatibility and simplicity, this object will only use PNG files
 *  - You'll need PHP 5.3.6 or higher and have libmagic installed to use this class
 *  - You need to have GD 2 installed, not GD1. If GD1 is used, results may not be as expected.
 *  - If you use an $imageCacheLocation, be sure that apache/PHP has permission to access that directory and the files there in
 * 
 * @author justin@dynamogeek.com
 */
class fundRaisingImageCreator{
	// A list of the constants we'll use for assorted messages/warnings
	// NOTE: When error checking, compare the strings returned from getErrors() to these constants
	/*BEING ERROR CONSTANTS*/
	const INVALID_GOAL_INDICATOR_COLOR = "You supplied an invalid goal indicator color. Please provide an array of RGB component values or select a color from \$colorToRGB.";
	const INVALID_GOAL_INDICATOR_OPACITY = "You supplied in an invalid goal indicator opacity. Please provide a number between 1 and 100.";
	const INVALID_GOAL_INDICATOR_TYPE = "You supplied in invalid goal indicator type. Valid goal indicator types are \"overlay\" and \"underlay\".";
	const INVALID_BORDER_COLOR = "You supplied an invalid border color. Please provide an array of RGB component values or select a color from \$colorToRGB.";
	const INVALID_RAISED_AMOUNT = "You supplied in invalid amount to setRaised. Please supply a number such as 36 or 95.50.";
	const INVALID_GOAL_AMOUNT = "You supplied in invalid amount to setGoalAmount. Please supply a number such as 36 or 95.50.";
	const INVALID_FUND_RAISING_IMAGE = "You supplied an invalid image to setFundRaisingImage. Make sure you suppled a valid file path. In addition, the image must be a png.";
	const INVALID_BORDER_WIDTH = "You supplied an invalid border width. The border width must be greater than 0.";
	const INVALID_FONT_LOCATION = "You supplied an invalid font location. Did you escape the string properly?";
	const GOAL_AMOUNT_NOT_SET = "You must set a goal amount using setGoalAmount(), and it must be greater than 0.";
	const IMAGE_TO_USE_NOT_SET = "You must set an image using setFundRaisingImage().";
	const INVALID_IMAGE_CACHE_LOCATION = "You supplied in invalid image cache location. Make sure your image cache location isn't an empty string, starts with a leading / or ./, and is a location that exists.";
	/*END ERROR CONSTANTS*/

	const GOAL_MET_TEXT = 'Goal Met!';
	
	// A list of the properties we'll use when building our image
	private $borderColor = array( 0, 0, 0 ); // The color we'll use for the border (default of black)
	private $raisedAmount = 0; // The amount of money the user has raised so far
	private $goalAmount = 0; // The funding goal
	private $imageToUse = ""; // The path to the image we'll use to indicate what percentage of our funding $goalAmount has been met
	private $goalIndicatorType = "underlay"; // Whether we indicate progress by overlaying or underlaying color with regards to the image to use
	private $goalIndicatorColor = array( 255, 255, 255 ); // Will indicate what percent of the funding goal remains (default color of white)
	private $goalIndicatorOpacity = 80; // The opacity percent of our goal indicator
	private $borderWidth = 0; // The width of the border to put around the image (default of no border)
	private $fontLocation = ""; // If this property is set to the location of a font file, that font will be used to overlay the "GOAL_MET_TEXT"
	private $imageCacheLocation = ""; // If this property is set, this class will store and use (whenever possible) a cache of generated images
	private $imageLoadedFromCache = ""; // True if an image was loaded from our cache location, false otherwise

	private $errors = array(); // This array will hold all of the errors we encounter while trying to build our fund raising image
	
	private static $colorToRGB = array(
		"gray" => array( 84, 84, 84 ),
		"grey" => array( 84, 84, 84 ),
		"red" => array( 255, 0, 0 ),
		"blue" => array( 0, 0, 255 ),
		"green" => array( 0, 255, 0 ),
		"yellow" => array( 255, 255, 0 ),
		"aqua" => array( 0, 255, 255 ),
		"magenta" => array( 255, 0, 255 ),
		"white" => array( 255, 255, 255 ),
		"black" => array( 0, 0, 0 ),
	); // An array of common values one might send in as a "color" if they don't realize it needs to be an array of RGB component values

	/**
	 * A method allowing the user to set the amount of funding they've raised so far
	 * @param int $userRaisedAmount - The amount of funding the user has raised so far
	 */
	public function setRaisedAmount( $userRaisedAmount ){
		if( is_numeric( $userRaisedAmount ) ){
			// If the user has given us a valid number of some sort, we'll set the $raisedAmount (while rounding to an int)
			$this->raisedAmount = intval( $userRaisedAmount );
		}else{
			// Otherwise we'll add an error to our array of errors
			$this->errors[] = self::INVALID_RAISED_AMOUNT;
		}
	}

	/**
	 * A method allowing the user to set the goal amount of their funding
	 * @param int $userGoalAmount - The users funding goal
	 */
	public function setGoalAmount( $userGoalAmount ){
		if( is_numeric( $userGoalAmount ) && $userGoalAmount > 0 ){
			// If the user has given us a valid number of some sort, we'll set the $goalAmount
			$this->goalAmount = intval( $userGoalAmount );
		}else{
			// Otherwise we'll add an error to our array of errors
			$this->errors[] = self::INVALID_GOAL_AMOUNT;
		}
	}

	/**
	 * A method allowing the user to supply the image they wish to use to indicate the percentage of their funding $goalAmount that has been met
	 * @param string $userImageToUse - A base image to use, on top of which or under which we'll display the users funding progress
	 */
	public function setFundRaisingImage( $userImageToUse ){
		if( self::_isValidPNG( $userImageToUse ) ){
			// If the user gave us a valid png file, we'll set our $imageToUse
			$this->imageToUse = $userImageToUse;
		}else{
			// Otherwise we'll add an error to our $errors array
			$this->errors[] = self::INVALID_FUND_RAISING_IMAGE;
		}
	}

	/**
	 * A method allowing the user to choose whether they want the goal indicator placed on top of or underneath their chosen image
	 * @param string $userGoalIndicatorType - "overlay" if the user wants the goal indicator over top of the chosen image, "underlay" otherwise
	 */
	public function setGoalIndicatorType( $userGoalIndicatorType ){
		if( self::_isGoalIndicatorTypeValid( $userGoalIndicatorType) ){
			// If the user gave us a valid goal indicator type, we'll set our $goalIndicatorType
			$this->goalIndicatorType = $userGoalIndicatorType;
		}else{
			// Otherwise we'll add an error to our $errors array
			$this->errors[] = self::INVALID_GOAL_INDICATOR_TYPE;
		}
	}

	/**
	 * A method that will allow the user to set the opacity of the goal indicator
	 * @param int $userGoalIndicatorOpacity - The opacity the user wishes to use for their goal indicator
	 */
	public function setGoalIndicatorOpacity( $userGoalIndicatorOpacity ){
		if( self::_isPercentValid( $userGoalIndicatorOpacity ) ){
			$this->goalIndicatorOpacity = intval( $userGoalIndicatorOpacity );
		}else{
			$this->errors[] = self::INVALID_GOAL_INDICATOR_OPACITY;
		}
	}

	/**
	 * A method that will allow the user to choose which indicator color they want
	 * 
	 * The indicator will be used to indicate what percent of the funding goal remains
	 * 
	 * @param array/string $userGoalIndicatorColor - An array of RGB component values, or a string matching a key in $colorToRGB
	 */
	public function setGoalIndicatorColor( $userGoalIndicatorColor ){
		$isValidColor = self::_isColorValid( $userGoalIndicatorColor );

		if( $isValidColor ){
			// If the color is valid, we need to check if it's an array of RGB values or color string
			if( is_array( $userGoalIndicatorColor ) ){
				// If it's an RGB component array, we can use it directly
				$this->goalIndicatorColor = $userGoalIndicatorColor;
			}else{
				// If it's a color string, we need to convert it to an RGB component array before we use it
				$this->goalIndicatorColor = self::$colorToRGB[$userGoalIndicatorColor];
			}
		}else{
			// Otherwise, if the color wasn't valid, we throw an error
			$this->errors[] = self::INVALID_GOAL_INDICATOR_COLOR;
		}
	}

	/**
	 * A method that will set the border color of the output image
	 * 
	 * @param array/string $color - An array of RGB component color values in the RGB order, or a string matching a key in $colorToRGB
	 */
	public function setBorderColor( $userBorderColor ){
		$isValidColor = self::_isColorValid( $userBorderColor );

		if( $isValidColor ){
			// If the color is valid, we need to check if it's an array of RGB values or color string
			if( is_array( $userBorderColor ) ){
				// If it's an RGB component array, we can use it directly
				$this->borderColor = $userBorderColor;
			}else{
				// If it's a color string, we need to convert it to an RGB component array before we use it
				$this->borderColor = self::$colorToRGB[$userBorderColor];
			}
		}else{
			// Otherwise, if the color wasn't valid, we throw an error
			$this->errors[] = self::INVALID_BORDER_COLOR;
		}
	}

	/**
	 * A method that will allow the user to add a border to their fund raising image
	 * @param int $userBorderWidth - The width of the border the user wants to put around their fund raising image
	 */
	public function setBorderWidth( $userBorderWidth ){
		if( is_numeric( $userBorderWidth ) && $userBorderWidth >= 0 ){
			$this->borderWidth = intval( $userBorderWidth );
		}else{
			$this->errors[] = self::INVALID_BORDER_WIDTH;
		}
	}

	/**
	 * A method that will allow the user to provide a font to be used for displaying the "GOAL_MET_TEXT"
	 * NOTE: If no font is supplied, GOAL_MET_TEXT will not be displayed
	 * 
	 * @param string $userFontLocation - The location of a TTF font file
	 */
	public function setFontLocation( $userFontLocation ){
		if( self::_isFontLocationValid( $userFontLocation ) ){
			$this->fontLocation = $userFontLocation;
		}else{
			$this->errors[] = self::INVALID_FONT_LOCATION;
		}
	}

	/**
	 * A method that will allow the user to set a location where this class can store a cache of generated images
	 * 
	 * @param string $imageCacheLocation - A path to a folder where this class can store a cache of generated images
	 */
	public function setImageCacheLocation( $imageCacheLocation ){
		$lastCharacterOfImageCacheLocation = substr( $imageCacheLocation, -1 );
		if( false === file_exists( $imageCacheLocation ) ){
			// First we'll check if the given location actually exists
			$this->errors[] = self::INVALID_IMAGE_CACHE_LOCATION;
		}elseif( "/" !== $lastCharacterOfImageCacheLocation && "\\" !== $lastCharacterOfImageCacheLocation ){
			if( false !== strpos( $imageCacheLocation, "/" ) || false !== strpos( $imageCacheLocation, "\\" ) ){
				// NOTE: /path/to\some\place/cache/ is a valid path on Windows as far as PHP is concerned
				/*
				 * We confirm that we have at least one path separator (necessary for us to have a valid path for our purposes).
				 * If we do have at least one path separator, we'll add a path separator to the end (since we don't have one there)
				 */
				$this->imageCacheLocation = $imageCacheLocation . "/";
			}else{
				// Something is wrong with the path we were given, we'll let the user know with an error
				$this->errors[] = self::INVALID_IMAGE_CACHE_LOCATION;
			}
		}else{
			// We didn't find any errors or issues with the given $imageCacheLocation, we can just use it
			$this->imageCacheLocation = $imageCacheLocation;
		}
	}

	/**
	 * A method that will allow the user to see all the errors that have been generated
	 * @return array - The $this->errors array, which is filled with errors that have occurred thus far
	 */
	public function getErrors(){
		return $this->errors;
	}

	/**
	 * A method that will allow the user to check whether or not the image they just asked for was loaded from the image cache or not
	 * @return boolean - True if the image was loaded from our image cache, false otherwise
	 */
	public function getImageLoadedFromCache(){
		return $this->imageLoadedFromCache;
	}

	/**
	 * A method that will allow the user to clear out the errors generated
	 */
	public function clearErrors(){
		$this->errors = array();
	}

	/**
	 * A method that will use all the gathered information and output an image to the browser
	 * NOTE: This function must be called last, as we send headers and this can only be done once
	 * 
	 * @return boolean - true if we successfully created a fund raising image, false otherwise
	 */
	public function displayFundRaisingImage(){
		self::_checkForRequiredData(); // Make sure we have all the data that we need

		if( !empty( $this->errors ) ){
			// If the user has received any errors, we won't output an image (perhaps the user can fix the errors and try again)
			return false;
		}

		// We'll check if we have an image in our cache that matches our requirements
		$cachedImageLocation = self::_getCachedImageLocation( $this->imageToUse, $this->borderColor, $this->raisedAmount, $this->goalAmount, $this->goalIndicatorType, $this->goalIndicatorColor, $this->goalIndicatorOpacity, $this->borderWidth, $this->imageCacheLocation );

		if( false !== $cachedImageLocation ){
			// We'll simply serve up the already generated image from our cache location
			$imageToUseResource = imagecreatefrompng( $cachedImageLocation );
			imagealphablending( $imageToUseResource, false );
			imagesavealpha( $imageToUseResource, true );
			$this->imageLoadedFromCache = true;
		}else{
			// Create an image based on our users image, and then add a goal indicator and border
			$imageToUseResource = imagecreatefrompng( $this->imageToUse ); // Start with the image the user wanted to use
			imagealphablending( $imageToUseResource, false );
			imagesavealpha( $imageToUseResource, true );

			$imageToUseResource = $this->_addGoalIndicator( $imageToUseResource ); // Add the appropriate goal indicator to the image
			$imageToUseResource = $this->_addBorder( $imageToUseResource ); // Add the appropriate border to the output image
			$this->imageLoadedFromCache = false;

			if( "" !== $this->imageCacheLocation ){
				// If the user is using an image cache, save our generated image to that cache
				$filename = self::_buildCachedImageName( $this->imageToUse, $this->borderColor, $this->raisedAmount, $this->goalAmount, $this->goalIndicatorType, $this->goalIndicatorColor, $this->goalIndicatorOpacity, $this->borderWidth );
				imagepng( $imageToUseResource, $this->imageCacheLocation . $filename );
			}
		}

		// Now we output the image we created for the user (their originally chosen image plus border and goal indicator)
		header( "Content-type: image/png" );
		imagepng( $imageToUseResource );
		imagedestroy( $imageToUseResource ); // The resource will be destroyed at the end of the script run anyway, but this is good practice
		return true;
	}

	/**
	 * This function will add an overly/underlay to the image to represent what percent of the goal amount remains
	 * @param  resource $imageToUseResource - The image we'll be outputting to the browser at some point
	 * @return resource                     - A resource that is our $imageToUseResource plus a goal indicator
	 */
	private function _addGoalIndicator( $imageToUseResource ){
		if( "overlay" === $this->goalIndicatorType ){
			$imageToUseResource = $this->_addGoalIndicatorOverlay( $imageToUseResource, $this->raisedAmount, $this->goalAmount );
		}else{
			$imageToUseResource = $this->_addGoalIndicatorUnderlay( $imageToUseResource, $this->raisedAmount, $this->goalAmount );
		}

		$imageToUseResource = $this->_addGoalMetText( $imageToUseResource, $this->raisedAmount, $this->goalAmount, $this->fontLocation );

		return $imageToUseResource; // If we got here, we don't need to do anything to our $imageToUseResource, simply return it
	}

	/**
	 * A private helper method that'll be used to add a goal indicator overlay
	 * @param  resource $imageToUseResource - The base image that we'll overlay our goal indicator on
	 * @param  int      $raisedAmount       - The amount of their goal the user has raised
	 * @param  int      $goalAmount         - The users funding goal
	 * @return resource                     - Our $imageToUseResource, updated to include an overlay, if necessary
	 */
	private function _addGoalIndicatorOverlay( $imageToUseResource, $raisedAmount, $goalAmount ){
		// NOTE: When overlaying, the indicator covers the amount of the image corresponding to the goal remaining, starting at the top

		$imageToUseHeight = imagesy( $imageToUseResource );
		$goalIndicatorImageWidth = imagesx( $imageToUseResource );
		// Calculate the height of the goal indicator by finding what percent of their goal the user has met
		$goalIndicatorImageHeight = self::_getGoalIndicatorHeight( $imageToUseHeight, $raisedAmount, $goalAmount, "overlay" );
		if( $goalIndicatorImageHeight > 0 ){
			// If the goalIndicatorImageHeight is more than 0, we need to overlay an indicator (if they've raised 0, this height would be 0)
			
			$goalIndicatorImageResource = imagecreatetruecolor( $goalIndicatorImageWidth, $goalIndicatorImageHeight );
			imagealphablending( $goalIndicatorImageResource, false );
			imagesavealpha( $goalIndicatorImageResource, true );
			$chosenColor = imagecolorallocate( $goalIndicatorImageResource, $this->goalIndicatorColor[0], $this->goalIndicatorColor[1], $this->goalIndicatorColor[2] );
			imagefilledrectangle( $goalIndicatorImageResource, 0, 0, $goalIndicatorImageWidth, $goalIndicatorImageHeight, $chosenColor );
			imagecopymerge( $imageToUseResource, $goalIndicatorImageResource, 0, 0, 0, 0, $goalIndicatorImageWidth, $goalIndicatorImageHeight, $this->goalIndicatorOpacity );
		}
		return $imageToUseResource; // Return our image resource, updated to include goal indicator if necessary
	}

	/**
	 * A private helper method that'll be used to add a goal indicator underlay
	 * @param  resource $imageToUseResource - The base image that we'll underlay our goal indicator beneath
	 * @param  int      $raisedAmount       - The amount of their goal the user has raised
	 * @param  int      $goalAmount         - The users funding goal
	 * @return resource                     - Our $imageToUseResource, updated to include an underlay, if necessary
	 */
	private function _addGoalIndicatorUnderlay( $imageToUseResource, $raisedAmount, $goalAmount ){
		// NOTE: When underlaying, the goal indicator rises from the bottom of the image up to the amount of funding received

		if( $raisedAmount > $goalAmount ){
			// Even though the user raised more than their goal, we can't (or choose not to) display an indicator larger than the base image
			$raisedAmount = $goalAmount;
		}

		$imageToUseWidth = imagesx( $imageToUseResource );
		$imageToUseHeight = imagesy( $imageToUseResource );
		$goalIndicatorImageWidth = imagesx( $imageToUseResource );
		// Calculate the height of the goal indicator by finding what percent of their goal the user has met
		$goalIndicatorImageHeight = self::_getGoalIndicatorHeight( $imageToUseHeight, $raisedAmount, $goalAmount, "underlay" );
		if( $goalIndicatorImageHeight > 0 ){
			// If the goalIndicatorImageHeight is more than 0, we need to underlay an indicator (if they've raised 0, this height would be 0)
			
			// Create a transparent base background to place our underlay and image to use on top of
			$transparentBackgroundResource = imagecreatetruecolor( $imageToUseWidth, $imageToUseHeight );
			imagealphablending( $transparentBackgroundResource, true );
			$transparentColor = imagecolorallocatealpha( $transparentBackgroundResource, 0, 0, 0, 127 );
			imagefill($transparentBackgroundResource, 0, 0, $transparentColor);

			$goalIndicatorImageResource = imagecreatetruecolor( $goalIndicatorImageWidth, $goalIndicatorImageHeight );
			imagealphablending( $goalIndicatorImageResource, false );

			// Create a goal indicator image to put on our base transparent background
			$convertedOpacity = self::_convertPercentToAlpha( $this->goalIndicatorOpacity );
			$chosenColor = imagecolorallocatealpha( $goalIndicatorImageResource, $this->goalIndicatorColor[0], $this->goalIndicatorColor[1], $this->goalIndicatorColor[2], $convertedOpacity );
			imagefilledrectangle( $goalIndicatorImageResource, 0, 0, $goalIndicatorImageWidth, $goalIndicatorImageHeight, $chosenColor );

			// Put our indicator on top of our transparent background (starting from the bottom)
			$bottomHeight = $imageToUseHeight - $goalIndicatorImageHeight;
			imagecopy( $transparentBackgroundResource, $goalIndicatorImageResource, 0, $bottomHeight, 0, 0, $imageToUseWidth, $imageToUseHeight );
			imagesavealpha( $transparentBackgroundResource, true );

			// Put our image to use (which will have transparency through which we can see the background) on top of our background
			imagecopy( $transparentBackgroundResource, $imageToUseResource, 0, 0, 0, 0, $imageToUseWidth, $imageToUseHeight );
			
			return $transparentBackgroundResource; // Return an image resource, which is our original image, plus goal indicator
		}
		return $imageToUseResource; // If we got here, we didn't make any changes to $imageToUseResouce, we'll simply return it
	}

	/**
	 * A private helper method that'll be used to overlay our GOAL_MET_TEXT, if the user has met their goal
	 * @param  resource $imageToUseResource - The base image that we'll overlay our GOAL_MET_TEXT on
	 * @param  int      $raisedAmount       - The amount that the user has raised
	 * @param  int      $goalAmount         - The users funding goal
	 * @param  string   $fontLocation       - The location of the font file the users wishes to use
	 * @return resource                     - The users chosen image with the GOAL_MET_TEXT overlaid
	 */
	private function _addGoalMetText( $imageToUseResource, $raisedAmount, $goalAmount, $fontLocation ){
		if( $raisedAmount > $goalAmount && "" != $this->fontLocation ){
			// If they've reached their goal, and they've given us a font to use, we'll overlay the GOAL_MET_TEXT on top of the image
			imagealphablending( $imageToUseResource, true );
			imagesavealpha( $imageToUseResource, true );
			$imageToUseWidth = imagesx( $imageToUseResource );
			$imageToUseHeight = imagesy( $imageToUseResource );
			$chosenColor = imagecolorallocate( $imageToUseResource, $this->goalIndicatorColor[0], $this->goalIndicatorColor[1], $this->goalIndicatorColor[2] );
			$gray = imagecolorallocate( $imageToUseResource, 87, 87, 87 );

			$baseYAdjustment = 15; // To give us plenty of room between the bottom of the image and the start of our letters
			$baseX = 37; // The X point on our image where we'll start our text

			/*
			 * NOTE: These two methods won't work perfectly on images much wider than they are tall
			 * (or vice versa), but they're an excellent start overall.
			 * Adding a calculation to try and put the text somewhere in the middle
			 * of the X axis of the users image would be a good way to improve text placement
			 */
			// Calculate the angle and font size of our GOAL_MET_TEXT based on the dimensions of our $imageToUse
			$fontAngle = $this->_calculateFontAngle( $imageToUseWidth, $imageToUseHeight );
			$fontSize = $this->_calculateFontSize( $fontAngle, $imageToUseWidth - $baseX, $imageToUseHeight - $baseYAdjustment, $this->fontLocation, self::GOAL_MET_TEXT );

			// Multiple imagettftext() to create a "border" around the text, subtractions to make sure our text is wholly on the image
			imagettftext ( $imageToUseResource, $fontSize, $fontAngle, $baseX - 2, $imageToUseHeight - $baseYAdjustment, $gray, $fontLocation, self::GOAL_MET_TEXT );
			imagettftext ( $imageToUseResource, $fontSize, $fontAngle, $baseX + 2, $imageToUseHeight - $baseYAdjustment, $gray, $fontLocation, self::GOAL_MET_TEXT );
			imagettftext ( $imageToUseResource, $fontSize, $fontAngle, $baseX, $imageToUseHeight - $baseYAdjustment + 2, $gray, $fontLocation, self::GOAL_MET_TEXT );
			imagettftext ( $imageToUseResource, $fontSize, $fontAngle, $baseX, $imageToUseHeight - $baseYAdjustment + 2, $gray, $fontLocation, self::GOAL_MET_TEXT );
			imagettftext ( $imageToUseResource, $fontSize, $fontAngle, $baseX, $imageToUseHeight - $baseYAdjustment, $chosenColor, $fontLocation, self::GOAL_MET_TEXT );
		}
		return $imageToUseResource; // Return our $imageToUseResource, with our GOAL_MET_TEXT overlaid if necessary
	}

	/**
	 * A private helper method that'll be used to calculate the angle at which our GOAL_MET_TEXT should be overlaid on the image 
	 * 
	 * @param  int $imageToUseWidth  - The width of the image we're overlaying text on
	 * @param  int $imageToUseHeight - The height of the image we're overlaying text on
	 * @return int                   - Our font angle, in degrees
	 */
	private function _calculateFontAngle( $imageToUseWidth, $imageToUseHeight ){
		// Use some fancy triangle math to find the best angle for our text (and we said we'd never use math in the real word!)
		$fontAngle = rad2deg( atan( $imageToUseHeight / $imageToUseWidth ) );

		return $fontAngle;
	}

	/**
	 * A private helper function that'll be used to calculate what size our font should be to fit in the image with given width and height
	 *
	 * NOTE: We could optimize this area by making a better guess for starting $fontSize based on the size of the image
	 * 
	 * @param  int    $fontAngle        - The angle at which the font will be overlaid
	 * @param  int    $imageToUseWidth  - The width of the image we're overlaying text on
	 * @param  int    $imageToUseHeight - The height of the image we're overlaying text on
	 * @param  string $fontFile         - The font we should use for calculating the max font size
	 * @param  string $text             - The text string we want to fit in the given width and height
	 * @return int                      - The largest font size we can use and still fit on the image
	 */
	private function _calculateFontSize( $fontAngle, $imageToUseWidth, $imageToUseHeight, $fontFile, $text ){
		$maxIterations = 40; // Limiting our max iterations helps eliminate infinite loops (it shouldn't take more than 40 tries to find our font size)
		$fontSize = 2;
		$boundInsideImage = true; // This will get set to false as soon as our text wouldn't fit on our image

		for( $i = 0; $i < $maxIterations; $i++ ){ // +2 to lower the number of times we have to check with little loss of user value
			// imagettfbbox returns an array of X and Y coordinate points indicating the bounds of the text
			$boundingBox = imagettfbbox ( $fontSize + 1, $fontAngle, $fontFile, $text );
			foreach( $boundingBox as $index => $point ){
				$abs_point = abs( $point );
				// We'll loop over our coordinate points to determine whether they're within our width and height boundaries
				if( 0 === $index % 2 ){
					// Every even $index is for an X coordinate (must fit within the width of our image)
					if( $abs_point > $imageToUseWidth || $abs_point < 0 ){
						$boundInsideImage = false;
					}
				}else{
					// Every odd $index is for an Y coordinate (must fit within the height of our image)
					if( $abs_point > $imageToUseHeight || $abs_point < 0 ){
						$boundInsideImage = false;
					}
				}
			}

			if( !$boundInsideImage ){
				// If our text would be too long to fit on the image, we're done
				break;
			}else{
				// If we're still in the bounds of our height and width, we could safely use this $fontSize
				$fontSize++;	
			}
		}

		return $fontSize-1	;
	}

	/**
	 * This function will add a border to the image we're generating (if the user wants a border)
	 * @param  resource $imageToUseResource - The image we'll be outputting to the browser at some point
	 * @return resource                     - Our $imageToUseResource, updated to include a border
	 */
	private function _addBorder( $imageToUseResource ){
		if( $this->borderWidth > 0 ){
			// If they want a border, we'll add it to the image we eventually output
			
			$imageToUseWidth = imagesx( $imageToUseResource );
			$imageToUseHeight = imagesy( $imageToUseResource );

			$borderImageResource = imagecreatetruecolor( $imageToUseWidth + ( $this->borderWidth * 2 ), $imageToUseHeight + ( $this->borderWidth * 2) );
			$borderImageWidth = imagesx( $borderImageResource );
			$borderImageHeight = imagesy( $borderImageResource );
			imagealphablending( $borderImageResource, false );
			imagesavealpha( $borderImageResource, true );

			// The color of the border
			$chosenColor = imagecolorallocate( $borderImageResource, $this->borderColor[0], $this->borderColor[1], $this->borderColor[2] );
			imagefilledrectangle( $borderImageResource, 0, 0, $borderImageWidth, $borderImageHeight, $chosenColor );

			/*
			 * These next several lines will cause the inner portion of the border image to be transparent. If we didn't do this,
			 * it'd be a solid color and ruin our underlays (by making the color of the transparency show up as the color of the border).
			 */
			$transparentBackgroundResource = imagecreatetruecolor( $imageToUseWidth - $this->borderWidth, $imageToUseHeight - $this->borderWidth );
			imagealphablending( $transparentBackgroundResource, false );
			// +1 to make sure we don't use the color of the border for our transparency
			$transparentColor = imagecolorallocate( $transparentBackgroundResource, $this->borderColor[0], $this->borderColor[0]+1, $this->borderColor[0] );
			imagefilledrectangle( $transparentBackgroundResource, 0, 0, $borderImageWidth, $borderImageHeight, $transparentColor );
			imagecopy( $borderImageResource, $transparentBackgroundResource, $this->borderWidth, $this->borderWidth, 0, 0, $imageToUseWidth, $imageToUseHeight );
			imagecolortransparent( $borderImageResource, $transparentColor );

			// Merge our border with our main image that we'll eventually output, and make it the same size as $imageToUseResource
			imagecopy( $borderImageResource, $imageToUseResource, $this->borderWidth, $this->borderWidth, 0, 0, $imageToUseWidth, $imageToUseHeight );

			// Resize our image to be the same size as $imageToUseResource
			$resizedImageResource = imagecreatetruecolor( $imageToUseWidth, $imageToUseHeight );
			imagealphablending( $resizedImageResource, false );
			imagesavealpha( $resizedImageResource, true );
			imagecopyresampled( $resizedImageResource, $borderImageResource, 0, 0, 0, 0, $imageToUseWidth, $imageToUseHeight, $borderImageWidth, $borderImageHeight );
			
			return $resizedImageResource; // Return our resized image resource, which is our original image plus a border

			// Destroying our created image here would actually cause us to use more memory
		}
		return $imageToUseResource; // If we got here, we don't need to do anything to our $imageToUseResource, simply return it
	}

	/**
	 * A private helper method that will check to make sure we have all the data we require. If we don't, we'll let the user know with some errors.
	 */
	private function _checkForRequiredData(){
		if( 0 === $this->goalAmount ){
			// If the user hasn't set a goalAmount (or set a goalAmount of 0), we'll add an error to our $errors array
			$this->errors[] = self::GOAL_AMOUNT_NOT_SET;
		}

		if( "" === $this->imageToUse ){
			// If the user hasn't chosen an image to use, we'll add an error to our $errors array
			$this->errors[] = self::IMAGE_TO_USE_NOT_SET;
		}
	}

	/**
	 * A private helper method to make sure we received a valid color, whether it's an RGB component array or a string
	 * @param array/string $color - An array of integers intended to make up a set of RGB component values, or a string from $colorToRGB
	 * @return boolean            - True if the color is a valid, false otherwise
	 */
	private static function _isColorValid( $color ){
		$isValidColor = true; // A boolean which will get set to false if any of our component values are invalid
		if( is_array( $color ) ){
			// If the user gave us an array, we'll make sure we received valid RGB component values
			foreach( $color as $colorValue ){
				if( !is_int( $colorValue ) || $colorValue < 0 || $colorValue > 255 ){
					$isValidColor = false;
					break; // No reason to keep searching through our component values if we find an invalid one
				}
			}
		}else{
			// Otherwise we'll assume they gave us a string and check our $colorToRGB array to see if we can determine what they wanted
			if( !array_key_exists( $color, self::$colorToRGB ) ){
				$isValidColor = false;
			}
		}

		return $isValidColor;
	}

	/**
	 * A private helper method to make sure that our percent is between 0 and 100
	 * @param  int $percent - An integer to check to make sure it is a valid percent
	 * @return boolean      - True if the integer is a valid percent, false otherwise
	 */
	private static function _isPercentValid( $percent ){
		if( is_numeric( $percent ) && $percent >= 0 && $percent <= 100 ){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * A private helper method to make sure the user supplied a path to a valid file, which is also a PNG image
	 * @param  string $imagePath - A file path to check for a valid PNG image
	 * @return boolean           - True if the give path is a valid PNG image, false otherwise
	 */
	private static function _isValidPNG( $imagePath ){
		$isValidPNG = false;
		$finfo = finfo_open( FILEINFO_MIME );
		if( is_file( $imagePath ) && false !== strpos( finfo_file( $finfo, $imagePath ), 'image/png' ) ){
			$isValidPNG = true;
		}
		finfo_close( $finfo );
		return $isValidPNG;
	}

	/**
	 * A private helper method to make sure the user supplied a valid font location
	 * @param  string  $fontLocation - A file path to check for a valid TTF font file
	 * @return boolean               - Will return true if the given location is a valid ttf font, false otherwise
	 */
	private static function _isFontLocationValid( $fontLocation ){
		$isValidFont = false;
		$mimeTypes = array( 'application/x-font-ttf', 'application/x-font-truetype', 'font/ttf','font/truetype' ); // valid mime types for our font files
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mime = finfo_file( $finfo, $fontLocation );
		foreach( $mimeTypes as $mimeType ){
			if( is_file( $fontLocation ) && false !== strpos( finfo_file( $finfo, $fontLocation ), $mimeType ) ){
				$isValidFont = true;
				break; // It's good practice to break out of a loop as soon as possible
			}
		}
		finfo_close( $finfo );
		return $isValidFont;
	}

	/**
	 * A private helper method to make sure the user supplied a valid goal indicator type
	 * 
	 * @param  string $goalIndicatorType - The type of indicator the user wants to use (over or under the chosen fund raising image)
	 * @return boolean                   - True if the given $goalIndicatorType is valid, false otherwise
	 */
	private static function _isGoalIndicatorTypeValid( $goalIndicatorType ){
		if( 'overlay' === $goalIndicatorType || 'underlay' === $goalIndicatorType ){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * A private helper method to convert a given percent to its equivalent alpha value
	 * 
	 * @param  int $percentToConvert - A percent indicating how opaque the alpha should be
	 * @return int                   - The percent opacity converted into an alpha channel value (a number between 0 and 127)
	 */
	private static function _convertPercentToAlpha( $percentToConvert ){		
		/* NOTES:
		 * 0 is completely opaque, 127 is completely transparent
		 * We subtract from 127 because 127 is full transparency, and our $percentToConvert refers to percent opacity
		 * We multiply by 1.27 because 1% of full transparency is 1.27 (127 / 100)
		 * We use intval because alpha values must be integers
		 */
		return 127 - ( intval( $percentToConvert * 1.27 ) );
	}

	/**
	 * A private helper method to figure out how tall our goal indicator should be
	 *
	 * NOTE: By the time this function is called, we've already validated $raisedAmount and $goalAmount
	 * 
	 * @param  string $imageToUseHeight  - The height of the image that we're overlaying the indicator on
	 * @param  int    $raisedAmount      - The amount the of the goal that has been raised
	 * @param  int    $goalAmount        - The funding goal
	 * @param  int    $goalIndicatorType - The type of indicator we'll use, underlay or overlay
	 * @return int                       - The height that the indicator image should be
	 */
	private static function _getGoalIndicatorHeight( $imageToUseHeight, $raisedAmount, $goalAmount, $goalIndicatorType ){
		// If we have amounts greater than 0, we'll calculate the height of the goal indicator
		if( "overlay" === $goalIndicatorType ){
			$goalIndicatorImageHeight = $imageToUseHeight - intval( $imageToUseHeight * ( $raisedAmount / $goalAmount ) );
		}else{
			$goalIndicatorImageHeight = intval( $imageToUseHeight * ( $raisedAmount / $goalAmount ) );
		}

		return $goalIndicatorImageHeight;
	}

	/**
	 * A private helper method to figure out if we currently have a cached version of the file the user tried to create
	 * 
	 * @param  string         $imageTouse           - The base image the user chose for their fund raising image
	 * @param  array/string   $borderColor          - The border color the user chose for their fund raising image
	 * @param  int            $raisedAmount         - The amount the user has raised so far
	 * @param  int            $goalAmount           - The goal amount of the users fund raiser
	 * @param  string         $goalIndicatorType    - The type of indicator the user chose to use (underlay or overlay)
	 * @param  array/string   $goalIndicatorColor   - The color the user chose to use for their indicator
	 * @param  int            $goalIndicatorOpacity - The opacity the user chose to use for their indicator
	 * @param  int            $borderWidth          - The border width the user chose to use for their image
	 * @param  string         $imageCacheLocation   - The location of the image cache
	 * @return boolean/string                       - The path to the cached image if it exists, false otherwise
	 */
	private static function _getCachedImageLocation( $imageToUse, $borderColor, $raisedAmount, $goalAmount, $goalIndicatorType, $goalIndicatorColor, $goalIndicatorOpacity, $borderWidth, $imageCacheLocation ){
		if( "" === $imageCacheLocation ){
			// If the user hasn't set an image cache location, we obviously can't have a cached image
			return false;
		}

		$cachedImageName = self::_buildCachedImageName( $imageToUse, $borderColor, $raisedAmount, $goalAmount, $goalIndicatorType, $goalIndicatorColor, $goalIndicatorOpacity, $borderWidth ); // First, we get the name we would have used to cache our image

		if( self::_isValidPNG( $imageCacheLocation . $cachedImageName) ){
			return $imageCacheLocation . $cachedImageName;
		}else{
			return false;
		}
	}

	/**
	 * A private helper method to build a filename to use for naming cached images
	 * 
	 * @param  string         $imageTouse           - The base image the user chose for their fund raising image
	 * @param  array/string   $borderColor          - The border color the user chose for their fund raising image
	 * @param  int            $raisedAmount         - The amount the user has raised so far
	 * @param  int            $goalAmount           - The goal amount of the users fund raiser
	 * @param  string         $goalIndicatorType    - The type of indicator the user chose to use (underlay or overlay)
	 * @param  array/string   $goalIndicatorColor   - The color the user chose to use for their indicator
	 * @param  int            $goalIndicatorOpacity - The opacity the user chose to use for their indicator
	 * @param  int            $borderWidth          - The border width the user chose to use for their image
	 * @return boolean/string                       - The name we would use for a cached image with the given parameters
	 */
	private static function _buildCachedImageName( $imageToUse, $borderColor, $raisedAmount, $goalAmount, $goalIndicatorType, $goalIndicatorColor, $goalIndicatorOpacity, $borderWidth ){
		$borderColor = implode( $borderColor ); // We need a string in order to build a file name
		$goalIndicatorColor = implode( $goalIndicatorColor ); // We need a string in order to build a file name

		// Now we'll start building our filename
		// We'll pull the filename of our $imageToUse out of the path the user gave us, and strip the .png off the end
		$filename = basename( $imageToUse, ".png" );

		// Next we'll determine what percentage of their goal the user met, and then add that to the $filename
		if( $raisedAmount < $goalAmount){
			// If the user has raised less than their goal, we'll figure out what percent of their goal they've met
			$raisedPercent = intval( ( $raisedAmount / $goalAmount ) * 100 ); // intval() to increase likelihood of cache hit
		}else{
			// Otherwise, as far as our image is concerned, anything above 100% makes no difference to the output image
			$raisedPercent = 100;
		}
		$filename .= $raisedPercent;

		if( $raisedAmount > 0 ){
			// The goal indicator information only impacts the look of the image if the user has raised some money
			$filename .= $goalIndicatorType . $goalIndicatorColor . $goalIndicatorOpacity;
		}

		if( $borderWidth > 0 ){
			// The border information only impacts the look of the image if the border is at least 1px
			$filename .= $borderWidth . $borderColor;
		}

		$filename .= ".png";

		return $filename;
	}
}
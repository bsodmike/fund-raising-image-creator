<?php
require_once( "../../fundRaisingImageCreator.php" ); // Include the class we want to test
/**
 * This class will test the functionality of our fundRaisingImageCreator class
 *
 * In this test we're only going to be testing observable behavior. In other words,
 * we won't be specifically checking that private variables get set correctly,
 * only that they didn't throw an error. We'll be testing the class as a whole when
 * we compare created images with known correct images in the displayFundRaisingImage test.
 *
 * NOTE: To avoid issues involving the use of header() in displayFundRaisingImage,
 * run this test with the --stderr option Ex. phpunit --stderr --coverage-text fundRaisingImageTest.php
 *
 * NOTE: Be sure to switch to the directory containing this test file before running it.
 *
 * NOTE: You'll need imagick installed to successfully run the displayFundRaisingImage test.
 */
class fundRaisingImageCreatorTest extends PHPUnit_Framework_TestCase{
	/**
	 * This method will test the setRaisedAmount method of the fundRaisingImageCreator class
	 *
	 * @covers fundRaisingImageCreator::setRaisedAmount
	 */
	public function testSetRaisedAmount(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();

		// Case 1: We send in a valid integer amount
		$fundRaisingImageCreator->setRaisedAmount( 15 );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetRaisedAmount: We received errors and should not have." );

		// Case 2: We send in a valid string amount
		$fundRaisingImageCreator->setRaisedAmount( "35" );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetRaisedAmount: We received errors and should not have." );

		// Case 3: We send in an invalid amount
		$fundRaisingImageCreator->setRaisedAmount( "Whatkindofmonstersendsinastring?!" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_RAISED_AMOUNT
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetRaisedAmount: We didn't receive the correct errors." );
	}

	/**
	 * This method will test the setGoalAmount method of the fundRaisingImageCreator class
	 *
	 * @covers fundRaisingImageCreator::setGoalAmount
	 */
	public function testSetGoalAmount(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();

		// Case 1: We send in a valid integer amount
		$fundRaisingImageCreator->setGoalAmount( 15 );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetGoalAmount case 1: We received errors and should not have." );

		// Case 2: We send in a valid string amount
		$fundRaisingImageCreator->setGoalAmount( "42" );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetGoalAmount case 2: We received errors and should not have." );

		// Case 3: We send in an invalid amount
		$fundRaisingImageCreator->setGoalAmount( "Whatkindofmonstersendsinanon-numericstring?!" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_GOAL_AMOUNT
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetGoalAmount case 3: We didn't receive the correct errors." );
	}

	/**
	 * This method will test the setFundRaisingImage and _isValidPNG methods of the fundRaisingImageCreator class
	 *
	 * @covers fundRaisingImageCreator::setFundRaisingImage
	 * @covers fundRaisingImageCreator::_isValidPNG
	 */
	public function testSetFundRaisingImage(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();

		// Case 1: We send in a valid PNG image
		$fundRaisingImageCreator->setFundRaisingImage( "./images/test.png" );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetFundRaisingImage case 1: We received errors and should not have.");
		
		// Case 2: We send in an invalid image (an image of JPG format)
		$fundRaisingImageCreator->setFundRaisingImage( "./images/test.jpg" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_FUND_RAISING_IMAGE
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetFundRaisingImage case 2: We didn't receive the correct errors." );
		
		// Case 3: We send in an invalid path
		$fundRaisingImageCreator->setFundRaisingImage( "./invalidpath/nonexistant.png" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_FUND_RAISING_IMAGE
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetFundRaisingImage case 3: We didn't receive the correct errors." );
	}

	/**
	 * This method will test the getErrors method of the fundRaisingImageCreator class
	 *
	 * @covers fundRaisingImageCreator::getErrors
	 */
	public function testGetErrors(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();

		// Case 1: We haven't caused any errors, and shouldn't getErrors should give us an empty array
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testGetErrors case 1: We received errors and should not have." );

		// Case 2: We've caused an error, we should get at least 1 error back
		$fundRaisingImageCreator->setFundRaisingImage( "./invalidpath/nonexistant.png" );
		$errors = $fundRaisingImageCreator->getErrors();
		$this->assertGreaterThan( 0, $errors, "testGetErrors case 2: We expected at least  1 error and didn't receive any." );
	}

	/**
	 * This method will test the clearErrors method of the fundRaisingImageCreator class
	 *
	 * @covers fundRaisingImageCreator::clearErrors
	 */
	public function testClearErrors(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();

		// Case 1: We haven't caused any errors, and clearErrors should do nothing
		$beforeErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$afterErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $beforeErrors, $afterErrors, "testClearErrors case 1: We expected no change in errors." );

		// Case 2: We cause errors, and then clear them
		$fundRaisingImageCreator->setFundRaisingImage( "./invalidpath/nonexistant.png" );
		$beforeErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$afterErrors = $fundRaisingImageCreator->getErrors();
		$this->assertNotEmpty( $beforeErrors, "testClearErrors case 2: We expected to have errors but didn't." );
		$this->assertEmpty( $afterErrors, "testClearErrors case 2: We didn't expect to have errors but did." );
	}

	/**
	 * This method will test the setGoalIndicatorType and _isGoalIndicatorTypeValid methods of the fundRaisingImageCreator class
	 *
	 * @covers fundRaisingImageCreator::setGoalIndicatorType
	 * @covers fundRaisingImageCreator::_isGoalIndicatorTypeValid
	 */
	public function testSetGoalIndicatorType(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();

		// Case 1: We send in a valid goal indicator type (overlay)
		$fundRaisingImageCreator->setGoalIndicatorType( "overlay" );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetGoalIndicatorType case 1: We received errors and should not have." );
		
		// Case 2: We send in a valid goal indicator type (underlay)
		$fundRaisingImageCreator->setGoalIndicatorType( "underlay" );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetGoalIndicatorType case 2: We received errors and should not have." );

		// Case 3: We send in an invalid goal indicator type
		$fundRaisingImageCreator->setGoalIndicatorType( "youshallnotpass!" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_GOAL_INDICATOR_TYPE,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetGoalIndicatorType case 3: We didn't receive the correct errors." );
	}

	/**
	 * This method will test the setGoalIndicatorColor and _isColorValid methods of the fundRaisingImageCreator class
	 *
	 * @covers fundRaisingImageCreator::setGoalIndicatorColor
	 * @covers fundRaisingImageCreator::_isColorValid
	 */
	public function testSetGoalIndicatorColor(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();

		// Case 1: We send in a valid RGB component array
		$fundRaisingImageCreator->setGoalIndicatorColor( array( 147, 147, 147 ) );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testsetIndicatorColor case 1: We received errors and should not have." );
		
		// Case 2: We send in an invalid RGB component array
		$fundRaisingImageCreator->setGoalIndicatorColor( array( 350, 350, 350 ) );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_GOAL_INDICATOR_COLOR,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testsetIndicatorColor case 2: We didn't receive the correct errors." );

		
		// Case 3: We send in a string that we're able to convert into an RGB component array using $colorToRGB
		$fundRaisingImageCreator->setGoalIndicatorColor( "white" );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testsetIndicatorColor case 3: We received errors and should not have. " );
		
		// Case 4: We send in a string that we're not able to convert into an RGB component array using $colorToRGB
		$fundRaisingImageCreator->setGoalIndicatorColor( "seriouslywhowouldputinmauve?!" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_GOAL_INDICATOR_COLOR,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testsetIndicatorColor case 4: We didn't receive the correct errors." );
	}

	/**
	 * This method will test the setBorderColor method of the fundRaisingImageCreator class
	 *
	 * @covers fundRaisingImageCreator::setBorderColor
	 */
	public function testSetBorderColor(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();

		// Case 1: We send in a valid RGB component array
		$fundRaisingImageCreator->setBorderColor( array( 147, 147, 147 ) );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetBorderColor case 1: We received errors and should not have." );
		
		// Case 2: We send in an invalid RGB component array
		$fundRaisingImageCreator->setBorderColor( array( 350, 350, 350 ) );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_BORDER_COLOR,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetBorderColor case 2: We didn't receive the correct errors." );

		
		// Case 3: We send in a string that we're able to convert into an RGB component array using $colorToRGB
		$fundRaisingImageCreator->setBorderColor( "white" );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetBorderColor case 3: We received errors and should not have. " );
		
		// Case 4: We send in a string that we're not able to convert into an RGB component array using $colorToRGB
		$fundRaisingImageCreator->setBorderColor( "seriouslywhowouldputinmauve" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_BORDER_COLOR,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetBorderColor case 4: We received errors and should not have." );
	}

	/**
	 * This method will test the indicatorOpactiy and _isPercentValid methods of the fundRaisingImageCreator class
	 * 
	 * @covers fundRaisingImageCreator::setGoalIndicatorOpacity
	 * @covers fundRaisingImageCreator::_isPercentValid
	 */
	public function testSetGoalIndicatorOpacity(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();

		// Case 1: Send in a valid indicator opacity integer
		$fundRaisingImageCreator->setGoalIndicatorOpacity( 55 );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testsetIndicatorOpacity case 1: We received errors and should not have." );

		// Case 2: Send in a valid indicator opacity string
		$fundRaisingImageCreator->setGoalIndicatorOpacity( "55" );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testsetIndicatorOpacity case 2: We received errors and should not have." );

		// Case 3: Send in an invalid indicator opacity (above 100)
		$fundRaisingImageCreator->setGoalIndicatorOpacity( 142 );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_GOAL_INDICATOR_OPACITY,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testsetIndicatorOpacity case 3: We didn't receive the correct errors." );
		
		// Case 4: Send in an invalid indicator opacity (below 0)
		$fundRaisingImageCreator->setGoalIndicatorOpacity( -1337 );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_GOAL_INDICATOR_OPACITY,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testsetIndicatorOpacity case 4: We didn't receive the correct errors." );

		// Case 5: Send in an invalid indicator opacity (non-numeric string)
		$fundRaisingImageCreator->setGoalIndicatorOpacity( "seriouslywhydopeoplekeepsendinginstrings?!" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_GOAL_INDICATOR_OPACITY,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testsetIndicatorOpacity case 5: We didn't receive the correct errors." );
	}

	/**
	 * This method will test the setBorderWidth and method of the fundRaisingImageCreator class
	 *
	 * @covers fundRaisingImageCreator::setBorderWidth
	 */
	public function testSetBorderWidth(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();

		// Case 1: Send in a valid border width int
		$fundRaisingImageCreator->setBorderWidth( 8 );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetBorderWidth case 1: We received errors and should not have." );
		
		// Case 2: Send in a valid border width string
		$fundRaisingImageCreator->setBorderWidth( "8" );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetBorderWidth case 2: We received errors and should not have." );
		
		// Case 3: Send in an invalid border width (less than 0)
		$fundRaisingImageCreator->setBorderWidth( -3 );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_BORDER_WIDTH,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetBorderWidth case 3: We didn't receive the correct errors." );
		
		// Case 5: Send in an invalid border width (non-numeric string)
		$fundRaisingImageCreator->setBorderWidth( "stringystring" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_BORDER_WIDTH,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetBorderWidth case 5: We didn't receive the correct errors." );
	}

	/**
	 * This method will test the setFontLocation and _isFontLocationValid methods of the fundRaisingImageCreator class
	 *
	 * @covers fundRaisingImageCreator::setFontLocation
	 * @covers fundRaisingImageCreator::_isFontLocationValid
	 */
	public function testSetFontLocation(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();

		// Case 1: Send in a valid font location
		$fundRaisingImageCreator->setFontLocation( "./fonts/arial_black.ttf" );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetFontLocation case 1: We received errors and should not have." );

		// Case 2: Send in the location of a file that isn't a font
		$fundRaisingImageCreator->setFontLocation( "./images/test.png" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_FONT_LOCATION,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetFontLocation case 2: We didn't receive the correct errors." );
		
		// Case 3: Send in an invalid path
		$fundRaisingImageCreator->setFontLocation( "./images/test.png" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_FONT_LOCATION,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors( "./invalidpath/nonexistant.ttf" );
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetFontLocation case 3: We didn't receive the correct errors." );
	}

	/**
	 * This method will test the setImageCacheLocation method of the fundRaisingImageCreator class
	 * 
	 * @covers fundRaisingImageCreator::setImageCacheLocation
	 */
	public function testSetImageCacheLocation(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();
		
		// Case 1: Send in a valid image cache location with a trailing slash
		$fundRaisingImageCreator->setImageCacheLocation( "./image_cache/" );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetImageCacheLocation case 1: We received errors and should not have." );

		// Case 2: Send in a valid image cache location without a trailing slash
		$fundRaisingImageCreator->setImageCacheLocation( "./image_cache" );
		$expectedErrors = array();
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetImageCacheLocation case 2: We received errors and should not have." );

		// Case 3: Send in a location without any path separators
		$fundRaisingImageCreator->setImageCacheLocation( "image_cache" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_IMAGE_CACHE_LOCATION,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetImageCacheLocation case 3: We didn't receive the correct errors." );

		// Case 4: Send in a location that doesn't exist
		$fundRaisingImageCreator->setImageCacheLocation( "./invalid_cache" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_IMAGE_CACHE_LOCATION,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetImageCacheLocation case 4: We didn't receive the correct errors." );

		// Case 5: Send in an empty string
		$fundRaisingImageCreator->setImageCacheLocation( "" );
		$expectedErrors = array(
			$fundRaisingImageCreator::INVALID_IMAGE_CACHE_LOCATION,
		);
		$actualErrors = $fundRaisingImageCreator->getErrors();
		$fundRaisingImageCreator->clearErrors();
		$this->assertEquals( $expectedErrors, $actualErrors, "testSetImageCacheLocation case 5: We didn't receive the correct errors.");
	}

	/**
	 * This method will test the displayFundRaisingImage, _addGoalIndicator, _addBorder and _checkForRequiredData methods of the fundRaisingImageCreator class
	 *
	 * NOTE: This test is by no means an exhaustive test of every possible combination of items between these 4 methods.
	 * However, in combination with all the other tests, it provides a reasonable level of testing certainty.
	 *
	 * NOTE: While this test doesn't detect the difference between an RGB of 35, 99, 185 and 35, 98, 185 on a 1 pixel border color,
	 * it does detect differences better than nearly everyone on the planet.
	 *
	 * @covers fundRaisingImageCreator::displayFundRaisingImage
	 * @covers fundRaisingImageCreator::_addGoalIndicator
	 * @covers fundRaisingImageCreator::_addBorder
	 * @covers fundRaisingImageCreator::_checkForRequiredData
	 * @covers fundRaisingImageCreator::_convertPercentToAlpha
	 * @covers fundRaisingImageCreator::_getGoalIndicatorHeight
	 * @covers fundRaisingImageCreator::_addGoalIndicatorOverlay
	 * @covers fundRaisingImageCreator::_addGoalIndicatorUnderlay
	 * @covers fundRaisingImageCreator::_addGoalMetText
	 * @covers fundRaisingImageCreator::_calculateFontAngle
	 * @covers fundRaisingImageCreator::_calculateFontSize
	 * @covers fundRaisingImageCreator::_getCachedImageLocation
	 * @dataProvider dataProviderForTestDisplayFundRaisingImage
	 *
	 * @param array $params - An array of parameters passed in by our data provider for use in creating our test image
	 */
	public function testDisplayFundRaisingImage( $params ){
		$fundRaisingImageCreator = new fundRaisingImageCreator();
		$fundRaisingImageCreator->setFundRaisingImage( $params["fundRaisingImage"] );
		$fundRaisingImageCreator->setFontLocation( $params["fontLocation"] );
		$fundRaisingImageCreator->setRaisedAmount( $params["raisedAmount"] );
		$fundRaisingImageCreator->setGoalAmount( $params["goalAmount"] );
		$fundRaisingImageCreator->setBorderWidth( $params["borderWidth"] );
		$fundRaisingImageCreator->setBorderColor( $params["borderColor"] );
		$fundRaisingImageCreator->setGoalIndicatorColor( $params["goalIndicatorColor"] );
		$fundRaisingImageCreator->setGoalIndicatorOpacity( $params["goalIndicatorOpacity"] );
		$fundRaisingImageCreator->setGoalIndicatorType( $params["goalIndicatorType"] );
		$fundRaisingImageCreator->clearErrors(); // Any errors that occur when using the above methods have been checked for in other test methods

		$generatedImagePath = './images/generatedImage.png';
		// The following ob_* things allow us to capture an image stream created in fundRaisingImageCreator.php and write it to file
		ob_start();
		$fundRaisingImageCreator->displayFundRaisingImage();
		$objectContents = ob_get_contents();
		ob_end_clean();

		if( "" != $objectContents ){
			// If we had some errors, nothing will output, we only want to try to create an image if we've received something
			$createdImage = imagecreatefromstring( $objectContents );
			imagepng( $createdImage, $generatedImagePath );
		}
		
		if( !empty( $params['expectedErrors'] ) ){
			// If we're expecting errors, check for the errors (nice and simple!)
			$actualErrors = $fundRaisingImageCreator->getErrors();
			$this->assertEquals( $params['expectedErrors'], $actualErrors, "testDisplayFundRaisingImage case " . $params["case"] . ": We didn't receive the correct errors." );
		}else{
			$generatedImage = new imagick( $generatedImagePath );
			$knownGoodImage = new imagick( $params["knownCorrectImage"] );

			$result = $generatedImage->compareImages( $knownGoodImage, Imagick::METRIC_UNDEFINED );
			/*
			 * Now that we're done with our generated image, we'll delete it (it's good to free up system resources as quickly as possible).
			 * Additionally, if we were to delete it after our assert, it may never get deleted,
			 * as a php unit case stops as soon as it hits a failure.
			 */
			unlink( $generatedImagePath );
			/* NOTE: comparing the *exact* same image yields a compareImages result[1] of 0.866062,
			 * and comparing *vastly* different images yields a compareImages result[1] of ~0.003.
			*/
			$maximumImagickSimilarityValue = 0.866062;
			// Convert the "similarity" to a more human readable format (out of 100%, based on $maximumImagickSimilarityValue)
			$imageSimilarityPercent = round( ( $result[1]/$maximumImagickSimilarityValue ) * 100, 4 );
			$requiredSimilarityMinimumPercent = 99.9977;
			
			$this->assertGreaterThan( $requiredSimilarityMinimumPercent, $imageSimilarityPercent, "testDisplayFundRaisingImage case ". $params["case"] . ": image similarity was " . $imageSimilarityPercent . " but the required similarity minimum was " . $requiredSimilarityMinimumPercent . "." );
		}
	}

	/**
	 * This method will test that image caching works correctly
	 * 
	 * @covers fundRaisingImageCreator::displayFundRaisingImage
	 * @covers fundRaisingImageCreator::getImageLoadedFromCache
	 * @covers fundRaisingImageCreator::_getCachedImageLocation
	 * @covers fundRaisingImageCreator::_buildCachedImageName
	 * 
	 */
	public function testImageCaching(){
		$fundRaisingImageCreator = new fundRaisingImageCreator();
		$fundRaisingImageCreator->setFundRaisingImage( "./images/heart.png" );
		$fundRaisingImageCreator->setFontLocation( "./fonts/arial_black.ttf" );
		$fundRaisingImageCreator->setImageCacheLocation( "./image_cache/" );
		$fundRaisingImageCreator->setRaisedAmount( 300 );
		$fundRaisingImageCreator->setGoalAmount( 250 );
		$fundRaisingImageCreator->setBorderWidth( 1 );
		$fundRaisingImageCreator->setBorderColor( "black" );
		$fundRaisingImageCreator->setGoalIndicatorColor( "white" );
		$fundRaisingImageCreator->setGoalIndicatorOpacity( 80 );
		$fundRaisingImageCreator->setGoalIndicatorType( "overlay" );
		$fundRaisingImageCreator->clearErrors(); // Any errors that occur when using the above methods have been checked for in other test methods

		// Case 1: Image isn't cached, we call displayFundRaisingImage to generate/cache it, we call displayFundRaisingImage again to load from cache
		$pathOfImageWhenCached = "./image_cache/heart100overlay255255255801000.png";

		if( file_exists( $pathOfImageWhenCached ) ){
			// If this test has previously failed, our generated image wouldn't have been deleted
			unlink( $pathOfImageWhenCached );
		}

		ob_start();
		$fundRaisingImageCreator->displayFundRaisingImage();
		// We don't really need the created image, we just don't want any errors, hence the ob_start and ob_end_clean
		ob_end_clean();

		$this->assertTrue( file_exists( $pathOfImageWhenCached ), "testImageCaching case 1: Image was not cached and should have been." );

		ob_start();
		$fundRaisingImageCreator->displayFundRaisingImage(); // Call displayFundRaisingImage a second time to load from cache
		// We don't really need the created image, we just don't want any errors, hence the ob_start and ob_end_clean
		ob_end_clean();

		$this->assertTrue( $fundRaisingImageCreator->getImageLoadedFromCache(), "testImageCaching case 1: Image was not loaded from cache and should have been." );

		unlink( $pathOfImageWhenCached ); // Delete our cached image

		// Case 2: We change options so we don't include border options in the file name, and it will cause us to calculate a percent of goal complete
		$pathOfImageWhenCached = "./image_cache/heart80overlay255255255801000.png";

		$fundRaisingImageCreator->setRaisedAmount( 200 );
		$fundRaisingImageCreator->setBorderWidth( 1 );

		if( file_exists( $pathOfImageWhenCached ) ){
			// If this test has previously failed, our generated image wouldn't have been deleted
			unlink( $pathOfImageWhenCached );
		}

		ob_start();
		$fundRaisingImageCreator->displayFundRaisingImage();
		// We don't really need the created image, we just don't want any errors, hence the ob_start and ob_end_clean
		ob_end_clean();

		$this->assertTrue( file_exists( $pathOfImageWhenCached ), "testImageCaching case 2: Image was not cached and should have been." );

		unlink( $pathOfImageWhenCached ); // Delete our cached image

		// Case 3: We change our raised amount to 0, which will remove goal indicator options from the file name
		$pathOfImageWhenCached = "./image_cache/heart01000.png";

		$fundRaisingImageCreator->setRaisedAmount( 0 );

		if( file_exists( $pathOfImageWhenCached ) ){
			// If this test has previously failed, our generated image wouldn't have been deleted
			unlink( $pathOfImageWhenCached );
		}

		ob_start();
		$fundRaisingImageCreator->displayFundRaisingImage();
		// We don't really need the created image, we just don't want any errors, hence the ob_start and ob_end_clean
		ob_end_clean();

		$this->assertTrue( file_exists( $pathOfImageWhenCached ), "testImageCaching case 3: Image was not cached and should have been." );

		unlink( $pathOfImageWhenCached ); // Delete our cached image
	}

	/**
	 * This method is the data provider that provides data for testDisplayFundRaisingImage
	 * @return array - An array of data 
	 */
	public function dataProviderForTestDisplayFundRaisingImage(){
		return array(
			array(
				array(
					"case" => 1,
					"fundRaisingImage" => "./images/heart.png",
					"knownCorrectImage" => "./images/displayFundRaisingImageCase1.png",
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 300,
					"goalAmount" => 250,
					"borderWidth" => 1,
					"borderColor" => "black",
					"goalIndicatorColor" => "white",
					"goalIndicatorOpacity" => 80,
					"goalIndicatorType" => "overlay",
					"expectedErrors" => array(),
				),
			),
			array(
				array(
					"case" => 2,
					"fundRaisingImage" => "./images/hour_glass.png",
					"knownCorrectImage" => "./images/displayFundRaisingImageCase2.png",
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 100,
					"goalAmount" => 165,
					"borderWidth" => 1,
					"borderColor" => "red",
					"goalIndicatorColor" => "blue",
					"goalIndicatorOpacity" => 80,
					"goalIndicatorType" => "overlay",
					"expectedErrors" => array(),
				),
			),
			array(
				array(
					"case" => 3,
					"fundRaisingImage" => "./images/hour_glass.png",
					"knownCorrectImage" => "./images/displayFundRaisingImageCase3.png",
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 0,
					"goalAmount" => 300,
					"borderWidth" => 6,
					"borderColor" => "green",
					"goalIndicatorColor" => "yellow",
					"goalIndicatorOpacity" => 60,
					"goalIndicatorType" => "overlay",
					"expectedErrors" => array(),
				),
			),
			array(
				array(
					"case" => 4,
					"fundRaisingImage" => "./images/heart.png",
					"knownCorrectImage" => "./images/displayFundRaisingImageCase4.png",
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 50,
					"goalAmount" => 100,
					"borderWidth" => 1,
					"borderColor" => array( 35, 99, 185 ),
					"goalIndicatorColor" => array( 19, 105, 222 ),
					"goalIndicatorOpacity" => 59,
					"goalIndicatorType" => "overlay",
					"expectedErrors" => array(),
				),
			),
			array(
				array(
					"case" => 5,
					"fundRaisingImage" => "./images/heart.png",
					"knownCorrectImage" => "./images/heart.png", // This won't be used, so it doesn't matter what we choose
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 50,
					"goalAmount" => 0,
					"borderWidth" => 1,
					"borderColor" => array( 35, 99, 185 ),
					"goalIndicatorColor" => array( 19, 105, 222 ),
					"goalIndicatorOpacity" => 59,
					"goalIndicatorType" => "overlay",
					"expectedErrors" => array( fundRaisingImageCreator::GOAL_AMOUNT_NOT_SET ),
				),
			),
			array(
				array(
					"case" => 6,
					"fundRaisingImage" => "",
					"knownCorrectImage" => "./images/heart.png", // This won't be used, so it doesn't matter what we choose
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 50,
					"goalAmount" => 100,
					"borderWidth" => 1,
					"borderColor" => array( 35, 99, 185 ),
					"goalIndicatorColor" => array( 19, 105, 222 ),
					"goalIndicatorOpacity" => 59,
					"goalIndicatorType" => "overlay",
					"expectedErrors" => array( fundRaisingImageCreator::IMAGE_TO_USE_NOT_SET ),
				),
			),
			array(
				array(
					"case" => 7,
					"fundRaisingImage" => "./images/heart_for_underlay.png",
					"knownCorrectImage" => "./images/displayFundRaisingImageCase7.png",
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 50,
					"goalAmount" => 100,
					"borderWidth" => 4,
					"borderColor" => "aqua",
					"goalIndicatorColor" => "green",
					"goalIndicatorOpacity" => 67,
					"goalIndicatorType" => "underlay",
					"expectedErrors" => array(),
				),
			),
			array(
				array(
					"case" => 8,
					"fundRaisingImage" => "./images/thermometer_for_underlay.png",
					"knownCorrectImage" => "./images/displayFundRaisingImageCase8.png",
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 35,
					"goalAmount" => 100,
					"borderWidth" => 10,
					"borderColor" => "red",
					"goalIndicatorColor" => "blue",
					"goalIndicatorOpacity" => 21,
					"goalIndicatorType" => "underlay",
					"expectedErrors" => array(),
				),
			),
			array(
				array(
					"case" => 9,
					"fundRaisingImage" => "./images/heart_for_underlay.png",
					"knownCorrectImage" => "./images/displayFundRaisingImageCase9.png",
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 500,
					"goalAmount" => 100,
					"borderWidth" => 6,
					"borderColor" => "blue",
					"goalIndicatorColor" => "green",
					"goalIndicatorOpacity" => 80,
					"goalIndicatorType" => "underlay",
					"expectedErrors" => array(),
				),
			),
			array(
				array(
					"case" => 10,
					"fundRaisingImage" => "./images/thermometer_for_underlay.png",
					"knownCorrectImage" => "./images/displayFundRaisingImageCase10.png",
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 0,
					"goalAmount" => 100,
					"borderWidth" => 6,
					"borderColor" => "yellow",
					"goalIndicatorColor" => "blue",
					"goalIndicatorOpacity" => 80,
					"goalIndicatorType" => "underlay",
					"expectedErrors" => array(),
				),
			),
			array(
				array(
					"case" => 11,
					"fundRaisingImage" => "./images/hour_glass.png",
					"knownCorrectImage" => "./images/displayFundRaisingImageCase11.png",
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 50,
					"goalAmount" => 100,
					"borderWidth" => 0,
					"borderColor" => "yellow",
					"goalIndicatorColor" => "white",
					"goalIndicatorOpacity" => 80,
					"goalIndicatorType" => "overlay",
					"expectedErrors" => array(),
				),
			),
			array(
				array(
					"case" => 12,
					"fundRaisingImage" => "./images/thermometer_for_underlay.png",
					"knownCorrectImage" => "./images/displayFundRaisingImageCase12.png",
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 505,
					"goalAmount" => 100,
					"borderWidth" => 3,
					"borderColor" => "yellow",
					"goalIndicatorColor" => "blue",
					"goalIndicatorOpacity" => 80,
					"goalIndicatorType" => "underlay",
					"expectedErrors" => array(),
				),
			),
			array(
				array(
					"case" => 13,
					"fundRaisingImage" => "./images/thermometer_for_underlay.png",
					"knownCorrectImage" => "./images/displayFundRaisingImageCase12.png",
					"fontLocation" => "./fonts/arial_black.ttf",
					"raisedAmount" => 505,
					"goalAmount" => 100,
					"borderWidth" => 3,
					"borderColor" => "yellow",
					"goalIndicatorColor" => "blue",
					"goalIndicatorOpacity" => 80,
					"goalIndicatorType" => "underlay",
					"expectedErrors" => array(),
				),
			),
		);
	}
}
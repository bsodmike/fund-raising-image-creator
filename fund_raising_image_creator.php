 <!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Fund Raising Image Designer</title>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="/fund_raising/stylesheets/base.css">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="/fund_raising/javascripts/base.js"></script>
</head>

<body>
<?php

$imagesLocation = '/fund_raising/images/';

$availableImagesToUse = array(
	'underlay' => array(
		'Heart Underlay' => 'heart_for_underlay',
		'Thermometer Underlay' => 'thermometer_for_underlay',
	), // These images will have a color laid under them as a fund raising indicator
	'overlay' => array(
		'Heart' => 'heart',
		'Hour Glass' => 'hour_glass',
	), // These images will have color laid over them as a fund raising indicator
); // The images we have available for the user to choose from

// Keyed by RGB values
$colorsToUse = array(
	"84, 84, 84" => "gray",
	"255, 0, 0" => "red",
	"0, 0, 255" => "blue",
	"0, 255, 0" => "green",
	"255, 255, 0" => "yellow",
	"0, 255, 255" => "aqua",
	"255, 0, 255" => "magenta",
	"255, 255, 255" => "white",
	"0, 0, 0" => "black"
);

echo '
<div id="page_header"><h1>Fund Raising Image Designer</h1></div>
<div id="creator_wrapper">
<div class="tile_wrapper">
	<div class="tile">
		<h2>Image To Use</h2>
		<input type="hidden" id="image_to_use" class="selection_input" name="image_to_use" value="heart_for_underlay"/>
		<input type="hidden" id="goal_indicator_type" class="selection_input" name="goal_indicator_type" value="underlay">';
	foreach( $availableImagesToUse as $typeOfImage => $images ){
		// We want the images used for overlays and the images used for underlays put on separate rows
		echo '
		<div class="bold">
		' . ucfirst( $typeOfImage ) .' <span id="tooltip_for_' . $typeOfImage . '_image_container" class="informational_tooltip"></span>
		</div>
		<div id="container_for_' . $typeOfImage . '_images">';
		foreach( $images as $humanReadableName => $fileBaseName ){
			$selected = '';
			if( 'heart_for_underlay' == $fileBaseName ){
				$selected = ' selected';
			}
			echo '<span class="image_to_use"><img alt="An image showing a possible base image to use" data-goal_indicator_type="' . $typeOfImage . '" data-image_to_use="' . $fileBaseName . '" id="possible_image_to_use_' . $fileBaseName . '" class="possible_image_to_use' . $selected . '" src="' . $imagesLocation . $fileBaseName . '_thumb.png"/></span>';
		}
		echo '
		</div>';
	}
echo '
	</div><div class="tile">
		<h2>Raised and Goal Amounts</h2>
		<table>
			<tr>
				<td class="align_right"><label for="raised_amount">Raised</label></td>
				<td><input id="raised_amount" type="number" class="selection_input" name="raised_amount" value="50"/></td>
			</tr>
			<tr>
				<td class="align_right"><label for="goal_amount">Goal</label></td>
				<td><input id="goal_amount" type="number" class="selection_input" name="goal_amount" value="100"/></td>
			</tr>
		</table>
	</div>
</div><div class="tile_wrapper">
	<div class="tile">
		<div><h2>Border</h2></div>
		<div id="border_options">
			<input type="hidden" id="border_width" class="selection_input" name="border_width" value="1"/>
			<div id="border_width_slider" data-slider_title="Width"></div>
			<input type="hidden" id="border_color" class="selection_input" name="border_color" value="black"/>';
	foreach( $colorsToUse as $RGBValueForColor => $variableName ){
		$selected = '';
		if( 'black' == $variableName ){
			$selected = ' selected';
		}
		// Putting this particular style in-line makes it less likely that we'll forget to update one part of our script and not another
		echo '<span data-color_to_use="' . $variableName . '" id="possible_border_color_' . $variableName . '" class="possible_border_color_to_use' . $selected . '" style="background-color: rgb(' . $RGBValueForColor . ');"></span>';
	}
echo '
		</div>
	</div><div class="tile">
		<h2>Indicator</h2>
		<input type="hidden" id="goal_indicator_opacity" class="selection_input" name="goal_indicator_opacity" value="80"/>
		<div id="goal_indicator_opacity_slider" data-slider_title="Opacity"></div>
		<input type="hidden" id="goal_indicator_color" class="selection_input" name="goal_indicator_color" value="red"/>';
	foreach( $colorsToUse as $RGBValueForColor => $variableName ){
		$selected = '';
		if( 'red' == $variableName ){
			$selected = ' selected';
		}
		// Putting this particular style in-line makes it less likely that we'll forget to update one part of our script and not another
		echo '<span data-color_to_use="' . $variableName . '" id="possible_goal_indicator_color_' . $variableName . '" class="possible_goal_indicator_color_to_use' . $selected . '" style="background-color: rgb(' . $RGBValueForColor . ');"></span>';
	}
echo '
	</div>
</div>
</div><div class="tile">
	<div><img alt="The image created from the users choices" id="output_image" src="#toBeFilledByJavaScript"/></div>
	<div><input id="fund_raising_image_url" readonly/></div>
</div>';
?>
</body>
</html> 
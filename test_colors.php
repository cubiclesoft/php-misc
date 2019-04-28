<?php
	// ColorTools and XTerm color space terminal tool.
	// (C) 2018 CubicleSoft.  All Rights Reserved.

	if (!isset($_SERVER["argc"]) || !$_SERVER["argc"])
	{
		echo "This file is intended to be run from the command-line.";

		exit();
	}

	// Temporary root.
	$rootpath = str_replace("\\", "/", dirname(__FILE__));

	require_once $rootpath . "/support/cli.php";

	// Process the command-line options.
	$options = array(
		"shortmap" => array(
			"p" => "palette",
			"t" => "truecolor",
			"s" => "sampletext",
			"?" => "help"
		),
		"rules" => array(
			"palette" => array("arg" => false),
			"truecolor" => array("arg" => false),
			"sampletext" => array("arg" => true),
			"help" => array("arg" => false)
		)
	);
	$args = CLI::ParseCommandLine($options);

	if (isset($args["opts"]["help"]) || !count($args["params"]) || (!isset($args["opts"]["palette"]) && !isset($args["opts"]["truecolor"])))
	{
		echo "ColorTools and XTerm color space terminal tool\n";
		echo "Purpose:  Show some of the ColorTools and XTerm color selection and normalization feature set given the input background RGB color in hex.\n";
		echo "\n";
		echo "Syntax:  " . $args["file"] . " [options] BackgroundRGB\n";
		echo "Options:\n";
		echo "\t-p       Run palette optimized text conversion mode.  Shows before and after.  This mode is how the black/white optimized palettes in the XTerm class were generated.\n";
		echo "\t-t       Run true color optimized text conversion mode.  Shows before and after.\n";
		echo "\t-s RGB   Show sample text in the specified foreground color in the before sample and the calculated nearest readable text color in the after sample.\n";
		echo "\n";
		echo "Examples:\n";
		echo "\tphp " . $args["file"] . " -p #000000\n";
		echo "\tphp " . $args["file"] . " -t #000080\n";
		echo "\tphp " . $args["file"] . " -s #000000 #000000\n";

		exit();
	}

	require_once $rootpath . "/support/xterm.php";
	require_once $rootpath . "/support/color_tools.php";

	$bgrgb = ColorTools::ConvertFromHex($args["params"][0]);
	$bgstr = ColorTools::ConvertToHex($bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);

	$sampletext = "Spicy jalapeno pastrami flank sirloin strip steak.\nTurducken boudin buffalo picanha tenderloin.\nFilet mignon buffalo pork loin andouille.";

	if (isset($args["opts"]["sampletext"]))  $fgrgb = ColorTools::ConvertFromHex($args["opts"]["sampletext"]);

	if (isset($args["opts"]["palette"]))
	{
		// Display the standard palette.
		echo "Palette before:\n";

		XTerm::SetBackgroundColor($bgstr);

		for ($x = 16; $x < 256; $x++)
		{
			if ($x > 16 && $x % 16 == 0)  echo "\n";

			XTerm::SetForegroundColor($x);
			echo sprintf("%3d", $x) . ", ";
		}

		if (isset($args["opts"]["sampletext"]))
		{
			XTerm::SetForegroundColor(ColorTools::ConvertToHex($fgrgb["r"], $fgrgb["g"], $fgrgb["b"]));

			echo "\n\n" . $sampletext;
		}

		XTerm::SetForegroundColor(false);
		XTerm::SetBackgroundColor(false);

		echo "\n\n";

		// Display a palette modified for maximum readability for the input background color.
		echo "Palette after:\n";

		XTerm::SetBackgroundColor($bgstr);

		$palette = XTerm::GetDefaultColorPalette();

		$palette2 = ColorTools::GetReadableTextForegroundColors($palette, $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);
		for ($x = 16; $x < 256; $x++)
		{
			if ($x > 16 && $x % 16 == 0)  echo "\n";

			$x2 = ColorTools::FindNearestPaletteColorIndex($palette2, $palette[$x][0], $palette[$x][1], $palette[$x][2]);

			XTerm::SetForegroundColor($x2);
			echo sprintf("%3d", $x2) . ", ";
		}

		if (isset($args["opts"]["sampletext"]))
		{
			$x2 = ColorTools::FindNearestPaletteColorIndex($palette2, $fgrgb["r"], $fgrgb["g"], $fgrgb["b"]);

			XTerm::SetForegroundColor($x2);

			echo "\n\n" . $sampletext;
		}

		XTerm::SetForegroundColor(false);
		XTerm::SetBackgroundColor(false);

		echo "\n\n";
	}

	if (isset($args["opts"]["truecolor"]))
	{
		XTerm::SetBackgroundColor($bgstr);

		$vscolor = ColorTools::FindNearestReadableTextColor(255, 255, 255, $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);
		$vscolor = ColorTools::ConvertToHex($vscolor[0], $vscolor[1], $vscolor[2]);

		// Display hues 0-360 in 15 degree increments.
		for ($h = 0; $h < 360; $h += 15)
		{
			$fg = array_values(ColorTools::ConvertHSBToRGB($h, 100, 100));

			// On the left is the original hue at 100% brightness and saturation.
			XTerm::SetForegroundColor(ColorTools::ConvertToHex($fg[0], $fg[1], $fg[2]));
			$hsb = ColorTools::ConvertRGBToHSB($fg[0], $fg[1], $fg[2]);
			echo "Color " . ColorTools::ConvertToHex($fg[0], $fg[1], $fg[2]) . " - H: " . (int)$hsb["h"] . ", S: " . (int)$hsb["s"] . ", B: " . (int)$hsb["b"];

			XTerm::SetForegroundColor($vscolor);
			echo "  =>  ";

			// On the right is the nearest match to the original color for the input background.
			$result = ColorTools::FindNearestReadableTextColor($fg[0], $fg[1], $fg[2], $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);
			XTerm::SetForegroundColor(ColorTools::ConvertToHex($result[0], $result[1], $result[2]));
			$hsb = ColorTools::ConvertRGBToHSB($result[0], $result[1], $result[2]);
			echo "Color " . ColorTools::ConvertToHex($result[0], $result[1], $result[2]) . " - H: " . (int)$hsb["h"] . ", S: " . (int)$hsb["s"] . ", B: " . (int)$hsb["b"] . "\n";
		}

		// Repeat the process but display grayscale in 17 different levels of brightness.
		$palette = array();
		for ($c = 0; $c < 256; $c += 16)  $palette[] = array($c, $c, $c);
		$palette[] = array(255, 255, 255);

		foreach ($palette as $fg)
		{
			$result = ColorTools::FindNearestReadableTextColor($fg[0], $fg[1], $fg[2], $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);

			XTerm::SetForegroundColor(ColorTools::ConvertToHex($fg[0], $fg[1], $fg[2]));
			$hsb = ColorTools::ConvertRGBToHSB($fg[0], $fg[1], $fg[2]);
			echo "Color " . ColorTools::ConvertToHex($fg[0], $fg[1], $fg[2]) . " - H: " . (int)$hsb["h"] . ", S: " . (int)$hsb["s"] . ", B: " . (int)$hsb["b"];

			XTerm::SetForegroundColor($vscolor);
			echo "  =>  ";

			XTerm::SetForegroundColor(ColorTools::ConvertToHex($result[0], $result[1], $result[2]));
			$hsb = ColorTools::ConvertRGBToHSB($result[0], $result[1], $result[2]);
			echo "Color " . ColorTools::ConvertToHex($result[0], $result[1], $result[2]) . " - H: " . (int)$hsb["h"] . ", S: " . (int)$hsb["s"] . ", B: " . (int)$hsb["b"] . "\n";
		}

		if (isset($args["opts"]["sampletext"]))
		{
			echo "\n";

			XTerm::SetForegroundColor(ColorTools::ConvertToHex($fgrgb["r"], $fgrgb["g"], $fgrgb["b"]));
			echo $sampletext . "\n\n";

			$result = ColorTools::FindNearestReadableTextColor($fgrgb["r"], $fgrgb["g"], $fgrgb["b"], $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);
			XTerm::SetForegroundColor(ColorTools::ConvertToHex($result[0], $result[1], $result[2]));
			echo $sampletext . "\n";
		}

		XTerm::SetForegroundColor(false);
		XTerm::SetBackgroundColor(false);

		echo "\n";
	}
?>
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
			"a" => "ansitest",
			"l" => "lumcalc",
			"p" => "palette",
			"t" => "truecolor",
			"s" => "sampletext",
			"?" => "help"
		),
		"rules" => array(
			"ansitest" => array("arg" => false),
			"lumcalc" => array("arg" => false),
			"palette" => array("arg" => false),
			"truecolor" => array("arg" => false),
			"sampletext" => array("arg" => true),
			"help" => array("arg" => false)
		)
	);
	$args = CLI::ParseCommandLine($options);

	if (isset($args["opts"]["help"]) || !count($args["params"]) || (!isset($args["opts"]["ansitest"]) && !isset($args["opts"]["palette"]) && !isset($args["opts"]["truecolor"])))
	{
		echo "ColorTools and XTerm color space terminal tool\n";
		echo "Purpose:  Show some of the ColorTools and XTerm color selection and normalization feature set given the input background RGB color in hex.\n";
		echo "Requirements:  A true color XTerm-compatible terminal is required for the text conversion modes.\n";
		echo "\n";
		echo "Syntax:  " . $args["file"] . " [options] BackgroundRGB\n";
		echo "Options:\n";
		echo "\t-a       Run ANSI 16-color test.  When -s is used, it shows the sample text in each of the 16 colors.\n";
		echo "\t-l       Show Luminosity distance and perceptual color distance calculations.\n";
		echo "\t-p       Run palette optimized text conversion mode.  Shows before and after.  This mode is how the black/white optimized palettes in the XTerm class were generated.\n";
		echo "\t-t       Run true color optimized text conversion mode.  Shows before and after.\n";
		echo "\t-s RGB   Show sample text in the specified foreground color in the before sample and the calculated nearest readable text color in the after sample.  RGB value ignored for ANSI color test.\n";
		echo "\n";
		echo "Examples:\n";
		echo "\tphp " . $args["file"] . " -a 000000\n";
		echo "\tphp " . $args["file"] . " -p 000000\n";
		echo "\tphp " . $args["file"] . " -t 000080\n";
		echo "\tphp " . $args["file"] . " -s 000000 -p 000000\n";

		exit();
	}

	require_once $rootpath . "/support/xterm.php";
	require_once $rootpath . "/support/color_tools.php";

	$bgrgb = ColorTools::ConvertFromHex($args["params"][0]);
	$bgstr = ColorTools::ConvertToHex($bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);

	$sampletext = explode("\n", "Spicy jalapeno pastrami flank sirloin strip steak.\nTurducken boudin buffalo picanha tenderloin.\nFilet mignon buffalo pork loin andouille.");
	$sampletextshort = "Spicy jalapeno pastrami flank sirloin strip steak.";

	// Expand each line of sample text to the maximum line length.
	$maxlen = 0;
	foreach ($sampletext as $num => $str)
	{
		if ($maxlen < strlen($str))  $maxlen = strlen($str);
	}

	foreach ($sampletext as $num => $str)
	{
		$sampletext[$num] = $str . str_repeat(" ", $maxlen - strlen($str));
	}

	if (isset($args["opts"]["sampletext"]))  $fgrgb = ColorTools::ConvertFromHex($args["opts"]["sampletext"]);

	if (isset($args["opts"]["ansitest"]))
	{
		// Display the named ANSI color palette.
		echo "ANSI color palette:\n";

		for ($x = 0; $x < 16; $x++)
		{
			XTerm::SetBackgroundColor($x);
			echo "                                                                       ";
			XTerm::SetBackgroundColor(false);
			echo "\n";

			XTerm::SetBackgroundColor($x);
			for ($x2 = 0; $x2 < 16; $x2++)
			{
				XTerm::SetForegroundColor($x2);

				echo " [" . $x2 . "]";
			}
			echo " ";

			XTerm::SetBackgroundColor(false);
			echo "\n";

			XTerm::SetBackgroundColor($x);
			echo "                                                                       ";
			XTerm::SetBackgroundColor(false);
			echo "\n";
		}

		XTerm::SetForegroundColor(false);
		XTerm::SetBackgroundColor(false);

		if (isset($args["opts"]["sampletext"]))
		{
			// There's no easy way to figure out, universally, what the ANSI color RGB values are set to.
			for ($x = 0; $x < 16; $x++)
			{
				XTerm::SetForegroundColor($x);

				echo "\n" . $sampletextshort . "\n";
			}
		}

		XTerm::SetForegroundColor(false);
	}

	if (isset($args["opts"]["palette"]))
	{
		// Display the standard palette.
		echo "Palette before:\n";

		XTerm::SetBackgroundColor($bgstr);

		for ($x = 16; $x < 256; $x++)
		{
			if ($x > 16 && $x % 16 == 0)
			{
				XTerm::SetBackgroundColor(false);
				echo "\n";
				XTerm::SetBackgroundColor($bgstr);
			}

			XTerm::SetForegroundColor($x);
			echo sprintf("%3d", $x) . ", ";
		}

		XTerm::SetBackgroundColor(false);
		echo "\n\n";

		if (isset($args["opts"]["sampletext"]))
		{
			$colorstr = ColorTools::ConvertToHex($fgrgb["r"], $fgrgb["g"], $fgrgb["b"]);
			$hsb = ColorTools::ConvertRGBToHSB($fgrgb["r"], $fgrgb["g"], $fgrgb["b"]);
			XTerm::SetForegroundColor($colorstr);
			XTerm::SetBackgroundColor($bgstr);
			$str = "Color " . $colorstr . " - H: " . (int)$hsb["h"] . ", S: " . (int)$hsb["s"] . ", B: " . (int)$hsb["b"];
			$str .= str_repeat(" ", $maxlen - strlen($str));
			echo $str;

			XTerm::SetBackgroundColor(false);
			echo "\n";

			foreach ($sampletext as $str)
			{
				XTerm::SetBackgroundColor($bgstr);
				echo $str;

				XTerm::SetBackgroundColor(false);
				echo "\n";
			}

			echo "\n";
		}

		XTerm::SetForegroundColor(false);
		XTerm::SetBackgroundColor(false);

		// Display a palette modified for maximum readability for the input background color.
		echo "Palette after:\n";

		XTerm::SetBackgroundColor($bgstr);

		$palette = XTerm::GetDefaultColorPalette();

		$palette2 = ColorTools::GetReadableTextForegroundColors($palette, $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);
		for ($x = 16; $x < 256; $x++)
		{
			if ($x > 16 && $x % 16 == 0)
			{
				XTerm::SetBackgroundColor(false);
				echo "\n";
				XTerm::SetBackgroundColor($bgstr);
			}

			$x2 = ColorTools::FindNearestPaletteColorIndex($palette2, $palette[$x][0], $palette[$x][1], $palette[$x][2]);

			XTerm::SetForegroundColor($x2);
			echo sprintf("%3d", $x2) . ", ";
		}

		XTerm::SetBackgroundColor(false);
		echo "\n\n";

		if (isset($args["opts"]["sampletext"]))
		{
			$x2 = ColorTools::FindNearestPaletteColorIndex($palette2, $fgrgb["r"], $fgrgb["g"], $fgrgb["b"]);

			$colorstr = ColorTools::ConvertToHex($palette2[$x2][0], $palette2[$x2][1], $palette2[$x2][2]);
			$hsb = ColorTools::ConvertRGBToHSB($palette2[$x2][0], $palette2[$x2][1], $palette2[$x2][2]);
			XTerm::SetForegroundColor($colorstr);
			XTerm::SetBackgroundColor($bgstr);
			$str = "Color " . $colorstr . " - H: " . (int)$hsb["h"] . ", S: " . (int)$hsb["s"] . ", B: " . (int)$hsb["b"];
			$str .= str_repeat(" ", $maxlen - strlen($str));
			echo $str;

			XTerm::SetBackgroundColor(false);
			echo "\n";

			foreach ($sampletext as $str)
			{
				XTerm::SetBackgroundColor($bgstr);
				echo $str;

				XTerm::SetBackgroundColor(false);
				echo "\n";
			}

			echo "\n";
		}

		XTerm::SetForegroundColor(false);
		XTerm::SetBackgroundColor(false);

		if (isset($args["opts"]["lumcalc"]))
		{
			$bglab = ColorTools::ConvertRGBToCIELab($bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);

			$vscolor = ColorTools::FindNearestReadableTextColor(255, 255, 255, $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);
			$vscolor = ColorTools::ConvertToHex($vscolor[0], $vscolor[1], $vscolor[2]);

			for ($x = 16; $x < 256; $x++)
			{
				$colorstr = ColorTools::ConvertToHex($palette[$x][0], $palette[$x][1], $palette[$x][2]);
				$hsb = ColorTools::ConvertRGBToHSB($palette[$x][0], $palette[$x][1], $palette[$x][2]);
				$lab = ColorTools::ConvertRGBToCIELab($palette[$x][0], $palette[$x][1], $palette[$x][2]);
				$ldist = abs($bglab["l"] - $lab["l"]);
				$dist = ColorTools::GetDistance($lab, $bglab);
				XTerm::SetForegroundColor($colorstr);
				XTerm::SetBackgroundColor($bgstr);
				$str = "[" . sprintf("%3d", $x) . "] Color " . $colorstr . " - H: " . sprintf("%3d", (int)$hsb["h"]) . ", S: " . sprintf("%3d", (int)$hsb["s"]) . ", B: " . sprintf("%3d", (int)$hsb["b"]);
				echo $str;

				$x2 = ColorTools::FindNearestPaletteColorIndex($palette2, $palette[$x][0], $palette[$x][1], $palette[$x][2]);

				XTerm::SetForegroundColor($vscolor);
				echo ($x == $x2 ? "  ==  " : "  !=  ");

				$colorstr2 = ColorTools::ConvertToHex($palette[$x2][0], $palette[$x2][1], $palette[$x2][2]);
				$hsb = ColorTools::ConvertRGBToHSB($palette[$x2][0], $palette[$x2][1], $palette[$x2][2]);
				$lab = ColorTools::ConvertRGBToCIELab($palette[$x2][0], $palette[$x2][1], $palette[$x2][2]);
				$ldist2 = abs($bglab["l"] - $lab["l"]);
				$dist2 = ColorTools::GetDistance($lab, $bglab);
				XTerm::SetForegroundColor($colorstr2);
				XTerm::SetBackgroundColor($bgstr);
				$str = "[" . sprintf("%3d", $x2) . "] Color " . $colorstr2 . " - H: " . sprintf("%3d", (int)$hsb["h"]) . ", S: " . sprintf("%3d", (int)$hsb["s"]) . ", B: " . sprintf("%3d", (int)$hsb["b"]);
				echo $str;

				XTerm::SetBackgroundColor(false);
				echo "\n";

				XTerm::SetBackgroundColor($bgstr);
				XTerm::SetForegroundColor($colorstr);
				$str = "      L dist: " . round($ldist, 0) . " - Dist: ". round($dist, 0) . " - Avg: " . round(($ldist + $dist) / 2.0, 0);
				$str .= str_repeat(" ", 44 - strlen($str));
				echo $str;

				echo "      ";

				XTerm::SetForegroundColor($colorstr2);
				$str = "      L dist: " . round($ldist2, 0) . " - Dist: ". round($dist2, 0) . " - Avg: " . round(($ldist2 + $dist2) / 2.0, 0);
				$str .= str_repeat(" ", 44 - strlen($str));
				echo $str;

				XTerm::SetBackgroundColor(false);
				echo "\n";
			}

			echo "\n";
		}

		XTerm::SetForegroundColor(false);
		XTerm::SetBackgroundColor(false);

		// Display palette entries 0-15 specially.
		echo "ANSI named colors\n";
		echo "[0-15 in system color] Name    XTerm color => Optimized color\n";

		// Source:  https://en.wikipedia.org/wiki/ANSI_escape_code
		$palettemap = array(
			array("name" => "Black         ", "xterm" => array(0, 0, 0)),
			array("name" => "Red           ", "xterm" => array(205, 0, 0)),
			array("name" => "Green         ", "xterm" => array(0, 205, 0)),
			array("name" => "Yellow        ", "xterm" => array(205, 205, 0)),
			array("name" => "Blue          ", "xterm" => array(0, 0, 238)),
			array("name" => "Magenta       ", "xterm" => array(205, 0, 205)),
			array("name" => "Cyan          ", "xterm" => array(0, 205, 205)),
			array("name" => "White         ", "xterm" => array(229, 229, 229)),
			array("name" => "Bright Black  ", "xterm" => array(127, 127, 127)),
			array("name" => "Bright Red    ", "xterm" => array(255, 0, 0)),
			array("name" => "Bright Green  ", "xterm" => array(0, 255, 0)),
			array("name" => "Bright Yellow ", "xterm" => array(255, 255, 0)),
			array("name" => "Bright Blue   ", "xterm" => array(92, 92, 255)),
			array("name" => "Bright Magenta", "xterm" => array(255, 0, 255)),
			array("name" => "Bright Cyan   ", "xterm" => array(0, 255, 255)),
			array("name" => "Bright White  ", "xterm" => array(255, 255, 255))
		);

		$vscolor = ColorTools::FindNearestReadableTextColor(255, 255, 255, $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);
		$vscolor = ColorTools::ConvertToHex($vscolor[0], $vscolor[1], $vscolor[2]);

		foreach ($palettemap as $x => $info)
		{
			XTerm::SetBackgroundColor($bgstr);
			XTerm::SetForegroundColor($x);
			echo "[" . $x . "] " . ($x < 10 ? " " : "");

			XTerm::SetForegroundColor($vscolor);
			echo $info["name"] . "    ";

			$tinfo = $info["xterm"];

			$colorstr = ColorTools::ConvertToHex($tinfo[0], $tinfo[1], $tinfo[2]);
			$hsb = ColorTools::ConvertRGBToHSB($tinfo[0], $tinfo[1], $tinfo[2]);
			XTerm::SetForegroundColor($colorstr);
			echo "Color " . $colorstr . " - H: " . sprintf("%3d", (int)$hsb["h"]) . ", S: " . sprintf("%3d", (int)$hsb["s"]) . ", B: " . sprintf("%3d", (int)$hsb["b"]);

			XTerm::SetForegroundColor($vscolor);
			echo " => ";

			$result = ColorTools::FindNearestReadableTextColor($tinfo[0], $tinfo[1], $tinfo[2], $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);
			$colorstr = ColorTools::ConvertToHex($result[0], $result[1], $result[2]);
			$hsb = ColorTools::ConvertRGBToHSB($result[0], $result[1], $result[2]);
			XTerm::SetForegroundColor($colorstr);
			echo "Color " . $colorstr . " - H: " . sprintf("%3d", (int)$hsb["h"]) . ", S: " . sprintf("%3d", (int)$hsb["s"]) . ", B: " . sprintf("%3d", (int)$hsb["b"]);

			XTerm::SetBackgroundColor(false);
			echo "\n";
		}

		XTerm::SetForegroundColor(false);
		XTerm::SetBackgroundColor(false);
	}

	if (isset($args["opts"]["truecolor"]))
	{
		$bglab = ColorTools::ConvertRGBToCIELab($bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);

		$vscolor = ColorTools::FindNearestReadableTextColor(255, 255, 255, $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);
		$vscolor = ColorTools::ConvertToHex($vscolor[0], $vscolor[1], $vscolor[2]);

		// Display hues 0-360 in 15 degree increments.
		for ($h = 0; $h < 360; $h += 15)
		{
			XTerm::SetBackgroundColor($bgstr);

			$fg = array_values(ColorTools::ConvertHSBToRGB($h, 100, 100));

			// On the left is the original hue at 100% brightness and saturation.
			$colorstr = ColorTools::ConvertToHex($fg[0], $fg[1], $fg[2]);
			$hsb = ColorTools::ConvertRGBToHSB($fg[0], $fg[1], $fg[2]);
			if (isset($args["opts"]["lumcalc"]))
			{
				$lab = ColorTools::ConvertRGBToCIELab($fg[0], $fg[1], $fg[2]);
				$ldist = abs($bglab["l"] - $lab["l"]);
				$dist = ColorTools::GetDistance($lab, $bglab);
			}
			XTerm::SetForegroundColor($colorstr);
			echo "Color " . $colorstr . " - H: " . sprintf("%3d", (int)$hsb["h"]) . ", S: " . sprintf("%3d", (int)$hsb["s"]) . ", B: " . sprintf("%3d", (int)$hsb["b"]);

			XTerm::SetForegroundColor($vscolor);
			echo "  =>  ";

			// On the right is the nearest match to the original color for the input background.
			$result = ColorTools::FindNearestReadableTextColor($fg[0], $fg[1], $fg[2], $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);
			$colorstr2 = ColorTools::ConvertToHex($result[0], $result[1], $result[2]);
			$hsb = ColorTools::ConvertRGBToHSB($result[0], $result[1], $result[2]);
			if (isset($args["opts"]["lumcalc"]))
			{
				$lab = ColorTools::ConvertRGBToCIELab($result[0], $result[1], $result[2]);
				$ldist2 = abs($bglab["l"] - $lab["l"]);
				$dist2 = ColorTools::GetDistance($lab, $bglab);
			}
			XTerm::SetForegroundColor($colorstr2);
			echo "Color " . $colorstr2 . " - H: " . sprintf("%3d", (int)$hsb["h"]) . ", S: " . sprintf("%3d", (int)$hsb["s"]) . ", B: " . sprintf("%3d", (int)$hsb["b"]);

			XTerm::SetBackgroundColor(false);
			echo "\n";

			if (isset($args["opts"]["lumcalc"]))
			{
				XTerm::SetBackgroundColor($bgstr);
				XTerm::SetForegroundColor($colorstr);
				$str = "L dist: " . round($ldist, 0) . " - Dist: ". round($dist, 0) . " - Avg: " . round(($ldist + $dist) / 2.0, 0);
				$str .= str_repeat(" ", 38 - strlen($str));
				echo $str;

				echo "      ";

				XTerm::SetForegroundColor($colorstr2);
				$str = "L dist: " . round($ldist2, 0) . " - Dist: ". round($dist2, 0) . " - Avg: " . round(($ldist2 + $dist2) / 2.0, 0);
				$str .= str_repeat(" ", 38 - strlen($str));
				echo $str;

				XTerm::SetBackgroundColor(false);
				echo "\n";
			}
		}

		// Repeat the process but display grayscale in 17 different levels of brightness.
		$palette = array();
		for ($c = 0; $c < 256; $c += 16)  $palette[] = array($c, $c, $c);
		$palette[] = array(255, 255, 255);

		foreach ($palette as $fg)
		{
			XTerm::SetBackgroundColor($bgstr);

			$colorstr = ColorTools::ConvertToHex($fg[0], $fg[1], $fg[2]);
			$hsb = ColorTools::ConvertRGBToHSB($fg[0], $fg[1], $fg[2]);
			if (isset($args["opts"]["lumcalc"]))
			{
				$lab = ColorTools::ConvertRGBToCIELab($fg[0], $fg[1], $fg[2]);
				$ldist = abs($bglab["l"] - $lab["l"]);
				$dist = ColorTools::GetDistance($lab, $bglab);
			}
			XTerm::SetForegroundColor($colorstr);
			echo "Color " . $colorstr . " - H: " . sprintf("%3d", (int)$hsb["h"]) . ", S: " . sprintf("%3d", (int)$hsb["s"]) . ", B: " . sprintf("%3d", (int)$hsb["b"]);

			XTerm::SetForegroundColor($vscolor);
			echo "  =>  ";

			$result = ColorTools::FindNearestReadableTextColor($fg[0], $fg[1], $fg[2], $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);
			$colorstr2 = ColorTools::ConvertToHex($result[0], $result[1], $result[2]);
			$hsb = ColorTools::ConvertRGBToHSB($result[0], $result[1], $result[2]);
			if (isset($args["opts"]["lumcalc"]))
			{
				$lab = ColorTools::ConvertRGBToCIELab($result[0], $result[1], $result[2]);
				$ldist2 = abs($bglab["l"] - $lab["l"]);
				$dist2 = ColorTools::GetDistance($lab, $bglab);
			}
			XTerm::SetForegroundColor($colorstr2);
			echo "Color " . $colorstr2 . " - H: " . sprintf("%3d", (int)$hsb["h"]) . ", S: " . sprintf("%3d", (int)$hsb["s"]) . ", B: " . sprintf("%3d", (int)$hsb["b"]);

			XTerm::SetBackgroundColor(false);
			echo "\n";

			if (isset($args["opts"]["lumcalc"]))
			{
				XTerm::SetBackgroundColor($bgstr);
				XTerm::SetForegroundColor($colorstr);
				$str = "L dist: " . round($ldist, 0) . " - Dist: ". round($dist, 0) . " - Avg: " . round(($ldist + $dist) / 2.0, 0);
				$str .= str_repeat(" ", 38 - strlen($str));
				echo $str;

				echo "      ";

				XTerm::SetForegroundColor($colorstr2);
				$str = "L dist: " . round($ldist2, 0) . " - Dist: ". round($dist2, 0) . " - Avg: " . round(($ldist2 + $dist2) / 2.0, 0);
				$str .= str_repeat(" ", 38 - strlen($str));
				echo $str;

				XTerm::SetBackgroundColor(false);
				echo "\n";
			}
		}

		echo "\n";

		if (isset($args["opts"]["sampletext"]))
		{
			$colorstr = ColorTools::ConvertToHex($fgrgb["r"], $fgrgb["g"], $fgrgb["b"]);
			$hsb = ColorTools::ConvertRGBToHSB($fgrgb["r"], $fgrgb["g"], $fgrgb["b"]);
			XTerm::SetForegroundColor($colorstr);
			XTerm::SetBackgroundColor($bgstr);
			$str = "Color " . $colorstr . " - H: " . (int)$hsb["h"] . ", S: " . (int)$hsb["s"] . ", B: " . (int)$hsb["b"];
			$str .= str_repeat(" ", $maxlen - strlen($str));
			echo $str;

			XTerm::SetBackgroundColor(false);
			echo "\n";

			foreach ($sampletext as $str)
			{
				XTerm::SetBackgroundColor($bgstr);
				echo $str;

				XTerm::SetBackgroundColor(false);
				echo "\n";
			}

			echo "\n";

			$result = ColorTools::FindNearestReadableTextColor($fgrgb["r"], $fgrgb["g"], $fgrgb["b"], $bgrgb["r"], $bgrgb["g"], $bgrgb["b"]);
			$colorstr = ColorTools::ConvertToHex($result[0], $result[1], $result[2]);
			$hsb = ColorTools::ConvertRGBToHSB($result[0], $result[1], $result[2]);
			XTerm::SetForegroundColor($colorstr);
			XTerm::SetBackgroundColor($bgstr);
			$str = "Color " . $colorstr . " - H: " . (int)$hsb["h"] . ", S: " . (int)$hsb["s"] . ", B: " . (int)$hsb["b"];
			$str .= str_repeat(" ", $maxlen - strlen($str));
			echo $str;

			XTerm::SetBackgroundColor(false);
			echo "\n";

			foreach ($sampletext as $str)
			{
				XTerm::SetBackgroundColor($bgstr);
				echo $str;

				XTerm::SetBackgroundColor(false);
				echo "\n";
			}

			echo "\n";
		}

		XTerm::SetForegroundColor(false);
		XTerm::SetBackgroundColor(false);
	}
?>
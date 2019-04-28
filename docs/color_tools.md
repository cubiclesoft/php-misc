ColorTools Class:  'support/color_tools.php'
============================================

The ColorTools class converts RGB to other color spaces (HSB, XYZ, CIE Lab) and has useful functions to calculate readable foreground text colors for any background color.

ColorTools::LimitRGB(&$r, &$g, &$b)
-----------------------------------

Access:  public static

Parameters:

* $r - A number to limit to 0-255 inclusive.
* $g - A number to limit to 0-255 inclusive.
* $b - A number to limit to 0-255 inclusive.

Returns:  Nothing.

This static function modifies the input Red, Green, and Blue (RGB) values so that they are integers in the range of 0-255 inclusive.

ColorTools::ConvertRGBtoHSB($r, $g, $b)
---------------------------------------

Access:  public static

Parameters:

* $r - An integer containing the red component.
* $g - An integer containing the green component.
* $b - An integer containing the blue component.

Returns:  An array containing the HSB color as key-value pairs.

This static function calculates and returns the equivalent Hue, Saturation, and Brightness (HSB) values.

Example usage:

```php
<?php
	require_once "support/color_tools.php";

	$hsb = ColorTools::ConvertRGBtoHSB(255, 0, 0);

	echo "H: " . $hsb["h"] . ", S: " . $hsb["s"] . ", B: " . $hsb["b"] . "\n";
?>
```

ColorTools::LimitHSB(&$h, &$s, &$b)
-----------------------------------

Access:  public static

Parameters:

* $h - A number to limit to 0-360 inclusive.
* $s - A number to limit to 0-100 inclusive.
* $b - A number to limit to 0-100 inclusive.

Returns:  Nothing.

This static function modifies the input Hue, Saturation, and Brightness (HSB) values so that they are within their valid ranges.

ColorTools::ConvertHSBToRGB($h, $s, $b)
---------------------------------------

Access:  public static

Parameters:

* $h - A number containing the hue (0-360).
* $s - A number containing the saturation (0-100).
* $b - A number containing the brightness (0-100).

Returns:  An array containing the RGB color as key-value pairs.

This static function calculates and returns the equivalent Red, Green, and Blue (RGB) values.

Example usage:

```php
<?php
	require_once "support/color_tools.php";

	$rgb = ColorTools::ConvertHSBToRGB(0, 100, 100);

	echo "R: " . $rgb["r"] . ", G: " . $rgb["g"] . ", B: " . $rgb["b"] . "\n";
?>
```

ColorTools::ConvertRGBToXYZ($r, $g, $b)
---------------------------------------

Access:  public static

Parameters:

* $r - An integer containing the red component.
* $g - An integer containing the green component.
* $b - An integer containing the blue component.

Returns:  An array containing the XYZ color as key-value pairs.

This static function calculates and returns the equivalent X, Y, and Z (XYZ) values for a standard 2 degree Observer and D65 Illuminant.  Converting to RGB to XYZ is the first step to converting a color into the CIE Lab color space.

ColorTools::ConvertXYZToCIELab($x, $y, $z)
------------------------------------------

Access:  public static

Parameters:

* $x - A number containing the X component.
* $y - A number containing the Y component.
* $z - A number containing the Z component.

Returns:  An array containing the CIE-Lab color as key-value pairs.

This static function calculates and returns the equivalent CIE Luminescence and chromatic a and b components (CIE-Lab) values for a standard 2 degree Observer and D65 Illuminant.  Converting to CIE Lab is useful when determining the perceptual distance between any two colors.

ColorTools::ConvertRGBToCIELab($r, $g, $b)
------------------------------------------

Access:  public static

Parameters:

* $r - An integer containing the red component.
* $g - An integer containing the green component.
* $b - An integer containing the blue component.

Returns:  An array containing the CIE-Lab color as key-value pairs.

This static function is a convenient shortcut for directly converting RGB to CIE-Lab.  Internally calls `ConvertRGBToXYZ()` and then `ConvertXYZToCIELab()`.

ColorTools::GetDistance($lab1, $lab2)
-------------------------------------

Access:  public static

Parameters:

* $lab1 - An array containing the output of a call to `ConvertXYZToCIELab()` or `ConvertRGBToCIELab()`.
* $lab2 - An array containing the output of a call to `ConvertXYZToCIELab()` or `ConvertRGBToCIELab()`.

Returns:  A number containing the Delta E CIE calculated distance.

This static function calculates the distance between two CIE-Lab values.  The Delta E is the rough perceptual distance between any two colors.

ColorTools::GetMaxSaturation($fg_h, $fg_b, $bg_b)
-------------------------------------------------

Access:  public static

Parameters:

* $fg_h - A numeric value containing the hue of a foreground color.
* $fg_b - A numeric value containing the brightness of a foreground color.
* $bg_b - A numeric value containing the brightness of a background color.

Returns:  A number containing the maximum allowable saturation.

This static function applies a radial saturation threshold based on the foreground hue, foreground brightness, and background brightness.

When the background is too dark, the foreground text color can become oversaturated.  Oversaturated text colors on a screen "bloom" and cause the text to appear blurry and leads to increased eyestrain.  This function solves that problem by calculating a lower saturation level for very dark backgrounds (i.e. background brightness < 15).

Note that this function uses a computationally expensive square root.

ColorTools::GetMinBrightness($bg_b)
-----------------------------------

Access:  public static

Parameters:

* $bg_b - A numeric value containing the brightness of a background color.

Returns:  A numeric value containing the minimum allowable brightness.

This static function applies a simple brightness threshold based on the background brightness.

When the background is too dark, the foreground text can be too dark and fade out/blend into the background.  Faded text on the screen leads to increased eyestrain.  This function solves the problem by requiring text on dark backgrounds to be at a minimum brightness level.

ColorTools::GetReadableTextForegroundColors($palette, $bg_r, $bg_g, $bg_b)
--------------------------------------------------------------------------

Access:  public static

Parameters:

* $palette - An array containing index to RGB value pairs for use as a limited color palette.
* $bg_r - An integer containing the red component of the background color.
* $bg_g - An integer containing the green component of the background color.
* $bg_b - An integer containing the blue component of the background color.

Returns:  An array containing foreground colors in the palette that are compatible with the background.

This static function uses various techniques and functions to create a limited palette of foreground colors suitable for readable text based on the specified RGB palette and background color.

A color is deemed suitable as a foreground text color if:

* There is a sufficient brightness differential between the background color and the palette color.  This helps improve overall contrast (both color and grayscale).
* There isn't too much saturation when the background is very dark.  This helps avoid text glow/halo effects on very dark backgrounds.
* There is sufficient distance between the luminosities (> 41.0), the perceptual colors (> 41.0), and the average between the two (> 50.0).  The minimum distance helps improve contrast between any two colors.

Example usage:

```php
<?php
	require_once "support/xterm.php";
	require_once "support/color_tools.php";

	$palette = XTerm::GetDefaultColorPalette();

	$palette2 = ColorTools::GetReadableTextForegroundColors($palette, 0, 0, 0);
	for ($x = 16; $x < 256; $x++)
	{
		if ($x > 16 && $x % 16 == 0)  echo "\n";

		$x2 = ColorTools::FindNearestPaletteColorIndex($palette2, $palette[$x][0], $palette[$x][1], $palette[$x][2]);

		XTerm::SetForegroundColor($x2);
		echo sprintf("%3d", $x2) . ", ";
	}
	echo "\n\n";

	XTerm::SetForegroundColor(false);
?>
```

ColorTools::FindNearestPaletteColorIndex($palette, $r, $g, $b)
--------------------------------------------------------------

Access:  public static

Parameters:

* $palette - An array containing index to RGB value pairs for use as a limited color palette.
* $r - An integer containing the red component of the foreground color.
* $g - An integer containing the green component of the foreground color.
* $b - An integer containing the blue component of the foreground color.

Returns:  An integer containing the nearest color index in the supplied palette to the requested color.  If the palette is empty, returns a boolean of false.

This static function finds the nearest color index in a RGB palette that matches the requested color.

The function uses HSB instead of CIE-Lab to do its distance calculations since this function is intended to be called after `GetReadableTextForegroundColors()` which already handles perceptual color differences.  The approach results in more consistent color selection accuracy from a limited palette.

ColorTools::FindNearestReadableTextColor($fg_r, $fg_g, $fg_b, $bg_r, $bg_g, $bg_b)
----------------------------------------------------------------------------------

Access:  public static

Parameters:

* $fg_r - An integer containing the red component of the foreground color.
* $fg_g - An integer containing the green component of the foreground color.
* $fg_b - An integer containing the blue component of the foreground color.
* $bg_r - An integer containing the red component of the background color.
* $bg_g - An integer containing the green component of the background color.
* $bg_b - An integer containing the blue component of the background color.

Returns:  An array containing the nearest readable RGB text color as key-value pairs.

This static function finds the nearest RGB color that will produce readable text based on the desired foreground color and the specified background color.

The result aims to meet the following criteria assuming perfect 20/20 vision:

* The target hue, brightness, and saturation are as close as possible to the desired foreground color.
* Text in the color in the Courier New font at 15 screen pixels tall is plainly readable from 3 1/2 feet away from the screen.
* Selected text color has sufficient contrast from the background color.
* Selected text color is not oversaturated on dark backgrounds.
* Selected text color has sufficient brightness on dark backgrounds.

This can be a CPU intensive operation, particularly for very bright background colors.  The resulting colors, however, are probably worth the CPU cycles spent.

Example usage:

```php
<?php
	require_once "support/color_tools.php";

	// Black text on a black background is invisible...
	$rgb = ColorTools::FindNearestReadableTextColor(0, 0, 0, 0, 0, 0);

	echo "R: " . $rgb["r"] . ", G: " . $rgb["g"] . ", B: " . $rgb["b"] . "\n";
?>
```

ColorTools::ConvertToHex($r, $g, $b, $prefix = "#")
---------------------------------------------------

Access:  public static

Parameters:

* $r - An integer containing the red component.
* $g - An integer containing the green component.
* $b - An integer containing the blue component.
* $prefix - A string containing the prefix to apply (Default is "#").

Returns:  A string containing the prefix plus the RGB value in hex.

This static function converts the RGB value to a hex string.  Useful for HTML and other purposes.

ColorTools::ConvertFromHex($str)
--------------------------------

Access:  public static

Parameters:

* $str - A string containing a hex color code.

Returns:  An array containing the RGB color as key-value pairs.

This static function converts a hex string to a RGB value.

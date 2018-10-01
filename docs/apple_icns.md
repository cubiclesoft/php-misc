AppleICNS Class:  'support/apple_icns.php'
==========================================

The AppleICNS class creates and parses Apple icon (.icns) files.  The .icns file format contains multiple images.  Requires PHP GD to be installed.

AppleICNS::$knowntypes
----------------------

Access:  public static

This static array contains information about every known Apple icon image type.  Note that some metadata types are not included in this array.

AppleICNS::Create($data)
------------------------

Access:  public static

Parameters:

* $data - A string containing a GD-compatible image, preferably 512x512 or larger.

Returns:  A standard array of information.

This static function creates an Apple icon from a source image.  A square PNG is preferred but any GD-compatible image will work.

The generated icon has the following types:

* ic10 - 1024x1024 PNG
* ic09 - 512x512 PNG
* ic14 - 512x512 PNG (retina)
* ic08 - 256x256 PNG
* ic13 - 256x256 PNG (retina)
* ic07 - 128x128 PNG
* icp6 - 64x64 PNG
* ic12 - 64x64 PNG (retina)
* icp5 - 32x32 PNG
* ic11 - 32x32 PNG (retina)
* icp4 - 16x16 PNG

If the source image is smaller than a specific type, then the type is skipped.

Example usage:

```php
<?php
	require_once "support/apple_icns.php";

	$data = file_get_contents("installer_icon.png");
	$result = AppleICNS::Create($data);
	if (!$result["success"])
	{
		var_dump($result);

		exit();
	}

	file_put_contents("installer_icon.icns", $result["data"]);
?>
```

AppleICNS::ResizeImage(&$data, $destwidth, $destheight)
-------------------------------------------------------

Access:  public static

Parameters:

* $data - A string containing a GD-compatible image.
* $destwidth - An integer containing the width to resize to.
* $destheight - An integer containing the height to resize to.

Returns:  A standard array of information.

This static function is used by `Create()` to resize the input image.

AppleICNS::Parse($data)
-----------------------

Access:  public static

Parameters:

* $data - A string containing Apple icon (.icns) file data.

Returns:  A standard array of information.

This static function parses and extracts Apple icon data.  Note that the function does not attempt to decode or decompress the binary image data.

Example usage:

```php
<?php
	require_once "support/apple_icns.php";

	$data = file_get_contents("installer_icon.icns");
	$result = AppleICNS::Parse($data);
	if (!$result["success"])
	{
		var_dump($result);

		exit();
	}

	foreach ($result["icons"] as &$icon)
	{
		unset($icon["data"]);
	}

	var_dump($result);
?>
```

AppleICNS::Generate($icons)
---------------------------

Access:  public static

Parameters:

* $icons - An array of icons to include in the final output.

Returns:  A string containing the final Apple icon.

This static function generates a string containing an Apple icon from a set of icons.  The typical use for this function is to `Parse()` an icon file, add or remove an icon, and then call this function to generate a new icon file.

Example usage:

```php
<?php
	require_once "support/apple_icns.php";

	$data = file_get_contents("installer_icon.icns");
	$result = AppleICNS::Parse($data);
	if (!$result["success"])
	{
		var_dump($result);

		exit();
	}

	foreach ($result["icons"] as $num => $icon)
	{
		if (!isset(AppleICNS::$knowntypes[$icon["type"]]) || $icon["type"] === "ic10")  unset($result["icons"][$num]);
	}

	$data = AppleICNS::Generate($result["icons"]);
	file_put_contents("installer_icon_cleaned.icns", $data);
?>
```

AppleICNS::GetUInt32(&$data, &$x, $y)
-------------------------------------

Access:  _internal_ static

Parameters:

* $data - A string to work with.
* $x - An integer containing the current position in the string to start at.
* $y - An integer containing the size of the string.

Returns:  The unsigned 32-bit integer representation of the next 4 bytes of data starting at `$x`.

This internal static function converts a 4-byte big-endian chunk into an integer.  Also moves `$x` forward.

AppleICNS::GetBytes(&$data, &$x, $y, $size)
-------------------------------------------

Access:  _internal_ static

Parameters:

* $data - A string to work with.
* $x - An integer containing the current position in the string to start at.
* $y - An integer containing the size of the string.
* $size - An integer containing the number of bytes to return.

Returns:  A string containing the next `$size` bytes starting at `$x`.

This internal static function retrieves the next `$size` bytes and pads out the data to `$size` with 0x00 bytes if insufficient data is available.

AppleICNS::AICNSTranslate($format, ...)
---------------------------------------

Access:  _internal_ static

Parameters:

* $format - A string containing valid sprintf() format specifiers.

Returns:  A string containing a translation.

This internal static function takes input strings and translates them from English to some other language if CS_TRANSLATE_FUNC is defined to be a valid PHP function name.

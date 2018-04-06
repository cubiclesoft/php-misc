Str Class:  'support/str_basics.php'
====================================

This class handles basic string processing issues across all platforms.  A lot of these functions are very old and deal with old bugs/problems in PHP.

```php
<?php
	require_once "support/str_basics.php";

	$filename = __FILE__;
	echo "Path:  " . Str::ExtractPathname($filename) . "\n";
	echo "Filename:  " . Str::ExtractFilename($filename) . "\n";
	echo "File extension:  " . Str::ExtractFileExtension($filename) . "\n";
	echo "File without extension:  " . Str::ExtractFilenameNoExtension($filename) . "\n";
	echo "Safe filename:  " . Str::FilenameSafe($filename) . "\n";
	echo "12MB:  " . number_format(Str::ConvertUserStrToBytes("12MB"), 0) . " bytes\n";
	echo "1234567890 bytes:  " . Str::ConvertBytesToUserStr(1234567890) . "\n";
	echo "\n";
?>
```

Str::ProcessSingleInput($data)
------------------------------

Access:  protected static

Parameters:

* $data - An array of key-value pairs to merge into $_REQUEST.

Returns:  Nothing.

This internal static function trims strings in the input array and merges the result into the $_REQUEST superglobal.

Str::ProcessAllInput()
----------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function merges the $_COOKIE, $_GET, and $_POST superglobals into the $_REQUEST superglobal (in that order).  This normalizes user input into the system and allows for simpler code (and therefore fewer application bugs).

Str::ExtractPathname($dirfile)
------------------------------

Access:  public static

Parameters:

* $dirfile - A string containing a path and filename.

Returns:  A string containing the path.

This static function extracts the path from a path and filename combination.  For example, '/test/index.php' becomes '/test/'.

Str::ExtractFilename($dirfile)
------------------------------

Access:  public static

Parameters:

* $dirfile - A string containing a path and filename.

Returns:  A string containing the filename.

This static function extracts the filename from a path and filename combination.  For example, '/test/index.php' becomes 'index.php'.

Str::ExtractFileExtension($dirfile)
-----------------------------------

Access:  public static

Parameters:

* $dirfile - A string containing a path and filename.

Returns:  A string containing the file extension.

This static function extracts the file extension from a path and filename combination.  For example, '/test/index.php' becomes 'php'.

Str::ExtractFilenameNoExtension($dirfile)
-----------------------------------------

Access:  public static

Parameters:

* $dirfile - A string containing a path and filename.

Returns:  A string containing the filename without the file extension.

This static function extracts the filename without the file extension from a path and filename combination.  For example, '/test/index.php' becomes 'index'.

Str::FilenameSafe($filename)
----------------------------

Access:  public static

Parameters:

* $filename - A string containing a filename.

Returns:  A string containing a safe filename prefix.

This function allows the characters A-Z, a-z, 0-9, '_' (underscore), '.' (period), and '-' (hyphen) through.  All other characters are converted to hyphens.  Multiple hyphens in a row are converted to one hyphen.  So a filename like 'index@$%*&^$+hacked?12.php' becomes 'index-hacked-12.php'.

Note that this function still allows file extensions through.  You should always add your own file extension when calling this function.

Str::ReplaceNewlines($replacewith, $data)
-----------------------------------------

Access:  public static

Parameters:

* $replacewith - A string to replace newlines with.
* $data - A string to replace newlines in.

Returns:  A string with newlines replaced.

This static function replaces any newline combination within the input data with the target newline.  All known (DOS, Mac, *NIX) and unknown newline combinations are handled to normalize on the replacement newline string.

Str::LineInput($data, &$pos)
----------------------------

Access:  _internal_ static

Parameters:

* $data - A string to extract a line from.
* $pos - An integer containing the position in $data.

Returns:  A string containing the next line of data minus the newline.

This internal static function reads in the next line of buffered data and moves $pos to the start of the next line.  This function is very old and was designed to mimic a very old, no longer used internal C++ library.  Do not use.

Str::CTstrcmp($secret, $userinput)
----------------------------------

Access:  public static

Parameters:

* $secret - A string containing the secret (e.g. a hash).
* $userinput - A string containing user input.

Returns:  An integer of zero if the two strings match, non-zero otherwise.

This static function performs a constant-time strcmp() operation.  Constant-time string compares are used in timing-attack defenses - that is, where comparing two strings with normal functions is a security vulnerability.

Example usage:

```php
<?php
	require_once "support/str_basics.php";

	$secret = "388e04532229f622cd86";
	if (Str::CTstrcmp($secret, $_REQUEST["sig"]) !== 0)
	{
		echo "Access denied.";

		exit();
	}
?>
```

Str::ConvertUserStrToBytes($str)
--------------------------------

Access:  public static

Parameters:

* $str - A string containing a size.

Returns:  An integer containing the expanded number of bytes.

This static function converts a string from a compact size format (e.g. "12MB") to an integer value (e.g. 12582912).  Useful for expanding values stored in the PHP INI file.

Str::ConvertBytesToUserStr($num)
--------------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of bytes to convert.

Returns:  A string in a compact size format ready for display to a user.

This static function converts an integer value (e.g. 1234567890) to a compact size format (e.g. "1.1 GB") for display.

Str::JSSafe($data)
------------------

Access:  public static

Parameters:

* $data - A string to make safe for use in Javascript code.

Returns:  A sanitized string ready for output in Javascript.

This static function escapes single quotes (') and newline characters (\r and \n).  For more advanced functionality, use the built-in PHP `json_encode()` function.

UTF8 Class:  'support/utf8.php'
===============================

The UTF8 class aids in validation, cleanup, processing, and conversion of UTF-8 code points in PHP.

UTF8::MakeValid($data)
----------------------

Access:  public static

Parameters:

* $data - A string to clean up.

Returns:  A valid UTF-8 string.

This static function analyzes an input string for invalid UTF-8 code points, removes them, and returns the cleaned up string.  Most built-in PHP functions that handle Unicode will return an error or stop processing the moment an invalid byte is encountered.

For performance reasons, use `UTF8::IsValid()` to test a string for validity before calling this function.

Example usage:

```php
<?php
	require_once "support/utf8.php";

	// The last two bytes are invalid so the first couple of var_dump()'s will output strange results.
	$str = "So good \xF0\x9F\x98\x82\xFF\x80";
	var_dump($str);
	var_dump(htmlspecialchars($str));

	if (!UTF8::IsValid($str))  $str = UTF8::MakeValid($str);
	var_dump($str);
	var_dump(htmlspecialchars($str));
?>
```

UTF8::IsValid($data)
--------------------

Access:  public static

Parameters:

* $data - A string to check.

Returns:  A boolean of true if the string is valid UTF-8, false otherwise.

This static function checks a string to see if it is 100% valid UTF-8.

UTF8::NextChrPos(&$data, $datalen, &$pos, &$size)
-------------------------------------------------

Access:  public static

Parameters:

* $data - A string to retrieve the next code point from.
* $datalen - An integer containing the length of `$data`.
* $pos - An integer containing the starting position.
* $size - An integer containing the length of the last code point.

Returns:  A boolean of true if there is a code point and the next UTF-8 code point is valid, false otherwise.  `$size` is set to the length of the code point.

This static function is intended to be used in a loop to get the next code point in the string and leave the loop when this function returns false.  Set `$pos` and `$size` to 0 before calling.

`$datalen` should be set to the value of `strlen($data)`.  See the source code to `UTF8::IsASCII()` for an example.

UTF8::MakeChr($num)
-------------------

Access:  public static

Parameters:

* $num - An integer representing a valid Unicode code point.

Returns:  A string containing the UTF-8 encoded version of the code point.

This static function accepts a valid 32-bit Unicode code point numeric value and returns a UTF-8 string.  If the code point is not valid (e.g. a surrogate), an empty string is returned.

UTF8::IsCombiningCodePoint($val)
--------------------------------

Access:  public static

Parameters:

* $val - An integer containing a code point.

Returns:  A boolean of true if the code point is a combining code point, false otherwise.

This static function returns whether or not a code point is a combining code point.  Combining code points allow for combining diacritics with other code points to form a single character.

UTF8::IsASCII($data)
--------------------

Access:  public static

Parameters:

* $data - A string to check.

Returns:  A boolean of true if the string is valid ASCII, false otherwise.

This static function determines if the supplied UTF-8 string is 100% ASCII.  Useful for some ancient technologies (e.g. calculating e-mail headers for SMTP).

UTF8::strlen($data)
-------------------

Access:  public static

Parameters:

* $data - The string to calculate the number of code points in.

Returns:  An integer containing the number of code points in the supplied string up to the first invalid byte.

This static function calculates the real length of a UTF-8 string by code points.

UTF8::ConvertToASCII($data)
---------------------------

Access:  public static

Parameters:

* $data - A string to convert from UTF-8 to ASCII.

Returns:  A string containing only ASCII characters.

This function drops bad UTF-8 code points and non-ASCII characters and then returns the result.

UTF8::ConvertToHTML($data)
--------------------------

Access:  public static

Parameters:

* $data - A UTF-8 string to convert into HTML entities.

Returns:  A string with Unicode code points converted into HTML entities.

This function makes it possible to display some Unicode characters on pages with a different encoding other than UTF-8.  Unicode code points are converted to HTML entities.  The resulting output takes up a lot more space but should work under a wide variety of web browser encodings.

Example usage:

```php
<?php
	require_once "support/utf8.php";

	$str = "So good \xF0\x9F\x98\x82";
	var_dump($str);
	var_dump(UTF8::ConvertToHTML($str));
?>
```

UTF8::ConvertToHTML__Callback($data)
------------------------------------

Access:  protected static

Parameters:

* $data - A UTF-8 code point to convert into a HTML entity.

Returns:  A string with the Unicode code point converted into a HTML entity.

This internal static function is a regular expression callback used by `ConvertToHTML()`.

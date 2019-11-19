UTFUtils Class:  'support/utf_utils.php'
========================================

The UTFUtils class implements common Unicode string transformations between UTF-8, UTF-16, and UTF-32 in pure PHP userland without dependencies on the intl, mbstring, or flaky iconv PHP extensions.

UTFUtils::IsCombiningCodePoint($val)
------------------------------------

Access:  public static

Parameters:

* $val - An integer containing a code point.

Returns:  A boolean of true if the code point is a combining code point, false otherwise.

This static function returns whether or not a code point is a combining code point.  Combining code points allow for combining diacritics with other code points to form a single character.

UTFUtils::Convert($data, $srctype, $desttype)
---------------------------------------------

Access:  public static

Parameters:

* $data - A string or array to convert from the source type to destination type.
* $srctype - An integer containing one of the UTFUtils constants that specifies what UTF type the data is stored as.
* $desttype - An integer containing one of the UTFUtils constants that specifies what UTF type the data should be transformed into.

Returns:  A transformed string.

This static function transforms from one UTF format to another.  The most common use-case is converting text stored in UTF-16 little endian (Windows) to UTF-8 (basically everything else).

Available constants for the source and destination types:

* UTFUtils::UTF8 - UTF-8
* UTFUtils::UTF8_BOM - UTF-8 with byte order marker (BOM)
* UTFUtils::UTF16_LE - UTF-16, little endian
* UTFUtils::UTF16_BE - UTF-16, big endian
* UTFUtils::UTF16_BOM - UTF-16, byte order marker (BOM)
* UTFUtils::UTF32_LE - UTF-32, little endian
* UTFUtils::UTF32_BE - UTF-32, big endian
* UTFUtils::UTF32_BOM - UTF-32, byte order marker (BOM)
* UTFUtils::UTF32_ARRAY - UTF-32 array of integer code points

When any of the three BOM options above are used for a destination type, the data and the BOM are both stored as little endian.

Example usage:

```php
<?php
	require_once "support/utf_utils.php";

	$str = "A\x00B\x00C\x00";

	echo UTFUtils::Convert($str, UTFUtils::UTF16_LE, UTFUtils::UTF8);
?>
```

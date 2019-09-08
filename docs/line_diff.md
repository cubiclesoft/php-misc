LineDiff Class:  'support/line_diff.php'
========================================

The LineDiff class creates a line-by-line diff of two input strings (or two arrays containing strings).  The algorithm produces similar output to several commercial diff/merge software products and produces accurate diffs at a rate of several MB/sec.  The class is limited to diffing lines of text.

LineDiff::Compare($left, $right, $options = array())
----------------------------------------------------

Access:  public static

Parameters:

* $left - A string or an array of strings containing the left-hand content.
* $right - A string or an array of strings containing the right-hand content.
* $options - An array of options (Default is array()).

Returns:  An array of arrays.

This function compares the lines of the left and right strings/arrays and returns a diff.  That is, the transformation from left to right.  The format of the returned array is:

```
array(
	array("Line 1", LineDiff::DELETED),
	array("Line 2", LineDiff::DELETED),
	array("Line 3", LineDiff::DELETED),
	array("Line 2a", LineDiff::INSERTED),
	array("Line 3a", LineDiff::INSERTED),
	array("Line 4", LineDiff::UNMODIFIED),
	array("Line 5", LineDiff::UNMODIFIED)
	array("Line 1a", LineDiff::INSERTED),
)
```

The $options array accepts these options:

* ltrim - A boolean that indicates that each line should be compared ignoring leading whitespace (Default is false).
* rtrim - A boolean that indicates that each line should be compared ignoring trailing whitespace (Default is false).
* ignore_whitespace - A boolean that indicates that each line should be compared ignoring all whitespace (Default is false).
* ignore_case - A boolean that indicates that each line should be compared ignoring case-sensitivity (Default is false).  When the PHP mbstring extension is enabled, this has better Unicode handling.
* consolidate - A boolean that indicates that diff chunks separated only by empty lines should be combined into single chunks (Default is true).  Results in slightly larger, but generally more readable diffs.

Example:

```php
<?php
	require_once "support/line_diff.php";

	$data = "Line 1\nLine 2\nLine 3\nLine 4\nLine 5";
	$data2 = "Line 2a\nLine 3a\nLine 4\nLine 5\nLine 1a";

	$diff = LineDiff::Compare($data, $data2);

	foreach ($diff as $line)
	{
		if ($line[1] === LineDiff::UNMODIFIED)  echo "  ";
		else if ($line[1] === LineDiff::DELETED)  echo "- ";
		else if ($line[1] === LineDiff::INSERTED)  echo "+ ";

		echo $line[0] . "\n";
	}
?>
```

LineDiff::TransformLine($line, $options)
----------------------------------------

Access:  protected static

Parameters:

* $line - A string containing a line to transform.
* $options - An array of options.

Returns:  A transformed string.

This internal function transforms a line as per the options passed to Compare().

ArrayUtils Class:  'support/array_utils.php'
============================================

The ArrayUtils class implements missing associative array functionality.  Essentially a backport of features from the [Cross-platform C++ Snippet Library](https://github.com/cubiclesoft/cross-platform-cpp).

ArrayUtils::InsertAfterKey($data, $findkey, ...$params)
-------------------------------------------------------

Access:  public static

Parameters:

* $data - The source array.
* $findkey - The associative key to insert $params after.  Passing `null` for this option will insert at the beginning of the array.
* $params - One or more arrays to insert at the new position.

Returns:  An array containing the combined arrays.

This static function inserts one or more key-value array parameters after the specified key.  Overrides existing keys in $data.  Preserves both string and integer keys and their positions in the new array.  Useful when display order matters.  If $findkey can't be found, the returned array will be identical to the input array.

Note that this constructs an entirely new array in PHP userland and is therefore not terribly efficient.  Use sparingly.

Example usage:

```php
<?php
	require_once "support/array_utils.php";

	$data = array(
		"test_1" => null,
		"test_2" => "Neat",
		5 => "Don't reset me bro!",
		"test_3" => "Cool",
	);

	var_dump(ArrayUtils::InsertAfterKey($data, "test_1", array(4 => "I should be between test_1 and test_2!", "me_too" => "Me too!")));
	var_dump(ArrayUtils::InsertAfterKey($data, null, array("frist" => "Firrst!")));
	var_dump(ArrayUtils::InsertAfterKey($data, "test_1", array("test_3" => $data["test_3"])));
?>
```

ArrayUtils::InsertBeforeKey($data, $findkey, ...$params)
--------------------------------------------------------

Access:  public static

Parameters:

* $data - The source array.
* $findkey - The associative key to insert $params before.  Passing `null` for this option will insert at the end of the array.
* $params - One or more arrays to insert at the new position.

Returns:  An array containing the combined arrays.

This static function inserts one or more key-value array parameters before the specified key.  Overrides existing keys in $data.  Preserves both string and integer keys and their positions in the new array.  Useful when display order matters.  If $findkey can't be found, the returned array will be identical to the input array.

Note that this constructs an entirely new array in PHP userland and is therefore not terribly efficient.  Use sparingly.  PHP already has sufficient support for appending items to the end of an associative array, so passing `null` for $findkey is wasteful of system resources.

Example usage:

```php
<?php
	require_once "support/array_utils.php";

	$data = array(
		"test_1" => null,
		"test_2" => "Neat",
		5 => "Don't reset me bro!",
		"test_3" => "Cool",
	);

	var_dump(ArrayUtils::InsertBeforeKey($data, "test_2", array(4 => "I should be between test_1 and test_2!", "me_too" => "Me too!")));
	var_dump(ArrayUtils::InsertBeforeKey($data, null, array("last" => "Laast!")));
	var_dump(ArrayUtils::InsertBeforeKey($data, "test_2", array("test_3" => $data["test_3"])));
?>
```

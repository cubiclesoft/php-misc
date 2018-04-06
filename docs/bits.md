StringBitStream Class:  'support/bits.php'
==========================================

The StreamBitStream class allows a string to be treated as a stream of bits for easy bit-level manipulation.  This class only offers read-only functionality.

Example usage for reading information from a Flash (SWF) file:

```php
<?php
	require_once "support/bits.php";

	function ExtractSWFInfo($data)
	{
		if (substr($data, 0, 3) != "CWS" && substr($data, 0, 3) != "FWS")  return array("success" => false, "error" => "Data is not a Flash (SWF) file.", "errorcode" => "not_a_swf");

		$result = array("success" => true);

		// Attempt to automatically extract useful information.
		// [F|C]WS, Flash version, decompressed file size, (compressed data starts here) rectangle in twips (20 twips = 1 pixel).
		$result["minflashver"] = (string)ord(substr($data, 3, 1));
		if (substr($data, 0, 3) == "FWS")
		{
			$data = substr($data, 8);
		}
		else if (substr($data, 0, 3) == "CWS" && function_exists("gzuncompress"))
		{
			$data = @gzuncompress(substr($data, 8));
			if ($data === false)  $data = "";
		}
		else  $data = "";

		if ($data != "")
		{
			$sbs = new StringBitStream($data);
			$numbits = $sbs->ReadBits(5);
			$x = $sbs->ReadBits($numbits);
			$x2 = $sbs->ReadBits($numbits);
			$y = $sbs->ReadBits($numbits);
			$y2 = $sbs->ReadBits($numbits);

			$result["width"] = (int)(($x2 - $x) / 20);
			$result["height"] = (int)(($y2 - $y) / 20);
		}

		return $result;
	}

	$data = file_get_contents("test.swf");
	$info = ExtractSWFInfo($data);
	var_dump($info);
?>
```

StringBitStream::__construct($data = "")
----------------------------------------

Access:  public

Paramters:

* $data - A string containing the data to use (Default is "").

Returns:  Nothing.

This function initializes a StringBitStream instance with the data that will be used.

StringBitStream::Init($data = "")
---------------------------------

Access:  public

Parameters:

* $data - A string containing the data to use (Default is "").

Returns:  Nothing.

This function initializes a StringBitStream instance with the data that will be used and resets all internal variables.

StringBitStream::ReadBits($numbits, $toint = true, $intsigned = true, $intdirforward = true)
--------------------------------------------------------------------------------------------

Access:  public

Parameters:

* $numbits - An integer containing how many bits to read starting at the last known location.
* $toint - A boolean of true to convert the bits to an integer, false to leave as a string (Default is true).
* $intsigned - A boolean of true to convert the bits as a signed integer, false as an unsigned integer (Default is true).  Only valid if `$toint` is true.
* $intdirforward - A boolean of true to specify that the storage of the bits is forward, false for reverse (Default is true).  Only valid if `$toint` is true.

Returns:  An integer if `$toint` is true, an array otherwise on success.  If there are insufficient bits for the request, a boolean of false is returned.

This function reads in the specified number of bits and returns an integer or an array containing those bits.

The array form is an array of arrays where the first element of each subarray is the number of bits and the second element are the extracted bits in integer form.  Each subarray contains up to one byte of the stream.  The $toint option is a shortcut to calling `ConvertBitsToInt()` after each `ReadBits()` call.

StringBitStream::ConvertBitsToInt($data, $signed = true, $dirforward = true)
----------------------------------------------------------------------------

Access:  public

Parameters:

* $data - An array from ReadBits() to convert to an integer.
* $signed - A boolean of true to convert the bits as a signed integer, false as an unsigned integer (Default is true).
* $dirforward - A boolean of true to specify that the storage of the bits is forward, false for reverse (Default is true).

Returns:  An integer containing the converted array.

This function is intended to be used after a call to `ReadBits()`.  This function is automatically called when the `$toint` option of the `ReadBits()` function is set to true.

StringBitStream::GetBytePos()
-----------------------------

Access:  public

Parameters:  None.

Returns:  An integer containing the current byte being processed.

This function can be useful to know where in the data stream the byte pointer is at.  Usually used in conjunction with the `AlignBytes()` function.  If processing hasn't begun, it will return -1.

StringBitStream::AlignBytes($base, $size)
-----------------------------------------

Access:  public

Parameters:

* $base - An integer representing the starting point for alignment.
* $size - An integer representing the alignment size.

Returns:  Nothing.

This function aligns the byte pointer and resets the bit pointer.  For instance, if binary data in a row is DWORD-aligned (4 bytes), then `$base` could be 0 and `$size` would be 4 to move the internal byte pointer to the start of the next row once the end of the current row was reached with `ReadBits()`.  Basically, this offers a flushing mechanism.

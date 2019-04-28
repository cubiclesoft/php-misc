XTerm Class:  'support/xterm.php'
=================================

The XTerm class simplifies emitting the XTerm-compatible escape codes to alter terminal behavior such as changing font styles and colors.  Many features also work with the Command Prompt in Windows 10 and later.

XTerm::ResetAttributes()
------------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code to resets all character/font attributes.

Example usage:

```php
<?php
	require_once "support/xterm.php";

	XTerm::SetForegroundColor("#FF0000");
	XTerm::SetBold();

	echo "Red, bold text!\n";

	XTerm::ResetAttributes();
?>
```

XTerm::SetBold($on = true)
--------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if bold will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn bold on or off.  Note that each terminal interprets this differently.  Bold may simply result in making the text color brighter rather than bold.

XTerm::SetFaint($on = true)
---------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if faint will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn faint on or off.  Note that this is not widely supported.  When faint is disabled, it also disables bold.

XTerm::SetItalic($on = true)
----------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if italic will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn italics on or off.  Note that this is not widely supported.

XTerm::SetUnderline($on = true)
-------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if underline will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn underline on or off.

XTerm::SetBlink($on = true)
---------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if blinking will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn blinking on or off.  Please don't enable the blinking text attribute though.

XTerm::SetInverse($on = true)
-----------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if inverted color mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn inverted color mode on or off.  This mode inverts the foreground and background colors.

XTerm::SetConceal($on = true)
-----------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if conceal mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn conceal mode on or off.  This mode hides the output which could be used to hide text as it is typed in when disabling echo in a TTY is not available.

XTerm::SetStrikethrough($on = true)
-----------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if strikethrough will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn strikethrough on or off.  Note that this is not widely supported.

XTerm::SetFontNum($num = 0)
---------------------------

Access:  public static

Parameters:

* $num - An integer containing the font number, 0-10 inclusive (Default is 0).

Returns:  Nothing.

This static function emits an escape code to change the font to one of ten predefined fonts.

* 0 - Default font.
* 1-9 - Alternate font (sometimes supported).
* 10 - Fraktur (hardly ever supported).

XTerm::GetDefaultColorPalette()
-------------------------------

Access:  public static

Parameters:  None.

Returns:  An array containing an indexed palette of standard XTerm colors.

This static function calculates palette indexes 16-255 inclusive and returns the default RGB palette.  Colors 0-15 are not standardized across all terminals but the rest of the palette is.  Useful for later calculations via ColorTools functions.

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

XTerm::MapOptimizedColorPalette($palettemap, $palette)
------------------------------------------------------

Access:  public static

Parameters:

* $palettemap - An array containing an optimized palette index map.
* $palette - An array containing a palette.

Returns:  An array containing an optimized color palette.

This static function returns a mapping from each position in the optimized index palette map (returned from a function like `GetBlackOptimizedColorPalette()`) to the associated RGB color in the palette (returned from a function like `GetDefaultColorPalette()`).

XTerm::GetBlackOptimizedColorPalette()
--------------------------------------

Access:  public static

Parameters:  None.

Returns:  An array containing a palette map optimized for text colors on a solid black background.

This static function returns a precalculated array of optimal text colors indexes for use on a solid black background.  See `ColorTools::GetReadableTextForegroundColors()` and the example for `GetDefaultColorPalette()` above to learn see this array was calculated.

XTerm::GetWhiteOptimizedColorPalette()
--------------------------------------

Access:  public static

Parameters:  None.

Returns:  An array containing a palette map optimized for text colors on a solid white background.

This static function returns a precalculated array of optimal text colors indexes for use on a solid white background.  See `ColorTools::GetReadableTextForegroundColors()` and the example for `GetDefaultColorPalette()` above to learn see this array was calculated.

XTerm::ConvertRGBToString($r, $g, $b, $prefix = "")
---------------------------------------------------

Access:  public static

Parameters:

* $r - An integer containing the red component.
* $g - An integer containing the green component.
* $b - An integer containing the blue component.
* $prefix - A string containing the prefix to apply (Default is "").

Returns:  A string containing the prefix plus the RGB value in hex.

This static function converts the RGB value to a hex string.  Useful for the string option for `SetForegroundColor()` and `SetBackgroundColor()`.  Note that the numbers are expected to be limited to 0-255 inclusive.

XTerm::SetForegroundColor($fgcolor)
-----------------------------------

Access:  public static

Parameters:

* $fgcolor - A boolean of false, an integer palette index, or a hex string containing the exact foreground color to use.

Returns:  Nothing.

This static function sets or resets the foreground color.  For maximum compatability, a palette index is recommended.  However, many terminals have true color support or will translate the foreground color into a palette index.  Note that palette indexes 0-15 inclusive, while supported everywhere, may emit different colors depending on the terminal (e.g. PuTTY vs. XTerm).

Passing a boolean of false to this function emits the escape code to reset the foreground color to the default.  It is recommended to do this prior to program termination.

XTerm::SetBackgroundColor($bgcolor)
-----------------------------------

Access:  public static

Parameters:

* $bgcolor - A boolean of false, an integer palette index, or a hex string containing the exact background color to use.

Returns:  Nothing.

This static function sets or resets the background color.  For maximum compatability, a palette index is recommended.  However, many terminals have true color support or will translate the background color into a palette index.  Note that palette indexes 0-15 inclusive, while supported everywhere, may emit different colors depending on the terminal (e.g. PuTTY vs. XTerm).

Passing a boolean of false to this function emits the escape code to reset the background color to the default.  It is recommended to do this prior to program termination.

XTerm::SetColors($fgcolor, $bgcolor)
------------------------------------

Access:  public static

Parameters:

* $fgcolor - A boolean of false, an integer palette index, or a hex string containing the exact foreground color to use.
* $bgcolor - A boolean of false, an integer palette index, or a hex string containing the exact background color to use.

Returns:  Nothing.

This static function sets/resets the foreground and background colors.  This is a shortcut to calling both `SetForegroundColor()` and `SetBackgroundColor()`.

XTerm::SetFramed($on = true)
----------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if framed mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn framed mode on or off.  Note that this is not widely supported.

XTerm::SetEncircled($on = true)
-------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if encircled mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn encircled mode on or off.  Note that this is not widely supported.  When encircled mode is disabled, it also disables framed mode.

XTerm::SetOverlined($on = true)
-------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if overline will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn overline on or off.  Note that this is not widely supported.

XTerm::InsertBlankChars($num)
-----------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of blank characters to insert.

Returns:  Nothing.

This static function emits an escape code that inserts the specified number of blank characters with the normal character attribute.  The cursor remains at the beginning of the blank characters.  Text between the cursor and right margin moves to the right.  Characters scrolled past the right margin are lost.

XTerm::MoveCursorUp($num = 1)
-----------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of lines to move the cursor up (Default is 1).

Returns:  Nothing.

This static function emits an escape code that moves the cursor up the specified number of lines.

XTerm::MoveCursorDown($num = 1)
-------------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of lines to move the cursor down (Default is 1).

Returns:  Nothing.

This static function emits an escape code that moves the cursor down the specified number of lines.

XTerm::MoveCursorForward($num = 1, $tabstops = false)
-----------------------------------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of characters/tab stops to move the cursor forward (Default is 1).
* $tabstops - A boolean that indicates whether to move forward in characters or tab stops (Default is false).

Returns:  Nothing.

This static function emits an escape code that moves the cursor forward the specified number of characters or tab stops.

XTerm::MoveCursorBack($num = 1, $tabstops = false)
--------------------------------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of characters/tab stops to move the cursor back (Default is 1).
* $tabstops - A boolean that indicates whether to move back in characters or tab stops (Default is false).

Returns:  Nothing.

This static function emits an escape code that moves the cursor back the specified number of characters or tab stops.

XTerm::MoveCursorNextLines($num = 1)
------------------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of lines to move the cursor down (Default is 1).

Returns:  Nothing.

This static function emits an escape code that moves the cursor to the beginning of the line and down the specified number of lines.

XTerm::MoveCursorPrevLines($num = 1)
------------------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of lines to move the cursor up (Default is 1).

Returns:  Nothing.

This static function emits an escape code that moves the cursor to the beginning of the line and up the specified number of lines.

XTerm::SetCursorCharacterAbsolute($pos = 1, $capped = true)
-----------------------------------------------------------

Access:  public static

Parameters:

* $pos - An integer containing the character position to move to on the current row (Default is 1).
* $capped - A boolean indicating whether or not to limit how far the cursor can move (Default is true).

Returns:  Nothing.

This static function emits an escape code that moves the cursor to the specified character position on the current line.

XTerm::SetCursorPosition($col = 1, $row = 1)
--------------------------------------------

Access:  public static

Parameters:

* $col - An integer containing the column to move the cursor to (Default is 1).
* $row - An integer containing the row to move the cursor to (Default is 1).

Returns:  Nothing.

This static function emits an escape code that moves the cursor to the specified row and column.  Note that the default values exist due to the odd allowance of not specifying either the row or column in the escape code itself.

XTerm::EraseToDisplayEnd()
--------------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that clears all displayed characters from the start of the cursor to the end of the display.  The cursor stays at its current position.

XTerm::EraseFromDisplayStart()
------------------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that clears all displayed characters from the start of the display through the cursor.  The cursor stays at its current position.  Note that scrollback is unaffected.

XTerm::EraseDisplay()
---------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that clears the entire display.  The cursor stays at its current position.  Note that scrollback is unaffected.

XTerm::ClearScrollback()
------------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that clears all scrollback.  The cursor stays at its current position.  Supported only in XTerm terminals.  Note that the current display is unaffected.

XTerm::ResetDisplay($scrollback = false)
----------------------------------------

Access:  public static

Parameters:

* $scrollback - A boolean indicating whether or not to also clear scrollback (Default is false).

Returns:  Nothing.

This static function combines `SetCursorPosition()`, `EraseDisplay()`, and `ClearScrollback()` to erase the display, set the cursor position, and optionally clear scrollback.

XTerm::EraseToLineEnd()
-----------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that clears all displayed characters from the start of the cursor to the end of the line.

XTerm::EraseFromLineStart()
---------------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that clears all displayed characters from the start of the line through the cursor.

XTerm::EraseLine()
------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that clears the entire line.

XTerm::InsertLines($num = 1)
----------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of lines to insert (Default is 1).

Returns:  Nothing.

This static function emits an escape code that inserts one or more blank lines with no attributes at the cursor position.  Lines below are moved down.  Lines that fall off the page are lost.

XTerm::DeleteLines($num = 1)
----------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of lines to delete (Default is 1).

Returns:  Nothing.

This static function emits an escape code that deletes one or more lines at the cursor position.  Lines below are moved up.  New blank lines with no attributes are placed at the bottom.

XTerm::DeleteChars($num = 1)
----------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of characters to delete (Default is 1).

Returns:  Nothing.

This static function emits an escape code that deletes one or more characters at the cursor position.  Characters and attributes move left.  New blank characters with no attributes are placed at the end.

XTerm::ScrollDisplayUp($num = 1)
--------------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of lines to scroll up (Default is 1).

Returns:  Nothing.

This static function emits an escape code that scrolls the display up.  New blank lines with no attributes are placed at the bottom.

XTerm::ScrollDisplayDown($num = 1)
----------------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of lines to scroll down (Default is 1).

Returns:  Nothing.

This static function emits an escape code that scrolls the display down.  New blank lines with no attributes are placed at the top.

XTerm::EraseChars($num = 1)
---------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of characters to erase (Default is 1).

Returns:  Nothing.

This static function emits an escape code that clears one or more characters and their attributes starting at the cursor position.

XTerm::RepeatPrecedingCharacter($num = 1)
-----------------------------------------

Access:  public static

Parameters:

* $num - An integer containing the number of characters to repeat (Default is 1).

Returns:  Nothing.

This static function emits an escape code that copies the preceding character one or more times starting at the cursor position.

XTerm::SetCursorLineAbsolute($pos = 1)
--------------------------------------

Access:  public static

Parameters:

* $num - An integer containing the line to move to (Default is 1).

Returns:  Nothing.

This static function emits an escape code that sets the cursor's row position.

XTerm::DeleteTabStop()
----------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that permanently deletes a tab stop at the cursor position until set/reset.  The cursor must be at the tab stop to delete it.

XTerm::DeleteAllTabStops()
--------------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that permanently deletes all tab stops until set/reset.

XTerm::SetTabStop()
-------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that sets a tab stop at the current cursor position.

XTerm::SetCursorStyle($type, $blink)
------------------------------------

Access:  public static

Parameters:

* $type - A string containing one of 'block', 'underline', or 'bar'.  The 'bar' type is XTerm only.
* $blink - A boolean indicating whether or not the cursor should blink.

Returns:  Nothing.

This static function emits an escape code that sets the cursor style and whether or not it blinks.

XTerm::SetScrollRegion($top = 1, $bottom = false)
-------------------------------------------------

Access:  public static

Parameters:

* $top - An integer containing the top line of the scrolling region (Default is 1).
* $bottom - A boolean of false or an integer containing the bottom line of the scrolling region (Default is false).

Returns:  Nothing.

This static function emits an escape code that sets the vertical scrolling region.

XTerm::SaveCursor()
-------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that saves the cursor position.  Note that only the most recently saved position is stored (i.e. not a push/pop stack).

XTerm::RestoreCursor()
----------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that restores a saved cursor position.

XTerm::SetInsertMode($on = true)
--------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if insert mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn insert mode on or off.  Note that the default terminal behavior is to overwrite, not insert.

XTerm::SetApplicationCursorMode($on = true)
-------------------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if application cursor mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn application cursor mode on or off.  Changes how arrow keys are handled.

XTerm::SetOriginMode($on = true)
--------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if origin mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn origin mode on or off.  Restrict the cursor to the upper-left corner of the margins.  Off by default.

XTerm::SetWraparoundMode($on = true)
------------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if wraparound mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn wraparound mode on or off.  Wrap text if the cursor is at the edge of the screen.  When turned off, text cuts off at the edge of the screen.  On by default.

XTerm::ShowCursor()
-------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code to show the cursor.

XTerm::HideCursor()
-------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code to hide the cursor.

XTerm::SetAltScreenBuffer($on = true)
-------------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if the alternate or the regular screen buffer will be used (Default is true).

Returns:  Nothing.

This static function emits an escape code to switch to/from the alternate screen buffer (e.g. vim).  XTerm only.

XTerm::SendPrimaryDeviceAttributes()
------------------------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code to request the primary device attributes.  It is up to the caller to get/handle the response.

XTerm::SendSecondaryDeviceAttributes()
--------------------------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code to request the secondary device attributes.  It is up to the caller to get/handle the response.

XTerm::SetMouseEventsMode($on = true)
-------------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if mouse event tracking mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn mouse event tracking mode on or off.  Button presses and mouse movements will be sent as escape codes.  It is up to the caller to handle the data.  XTerm only.

XTerm::SetFocusEventsMode($on = true)
-------------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if mouse focus mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn mouse focus mode on or off.  Mouse focus events will be sent as escape codes.  It is up to the caller to handle the data.  XTerm only.

XTerm::SetUTFMouseMode($on = true)
----------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if UTF mouse mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn UTF mouse mode on or off.  It is up to the caller to handle mouse event data.  XTerm only.

XTerm::SetSGRMouseMode($on = true)
----------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if SGR mouse mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn SGR mouse mode on or off.  It is up to the caller to handle mouse event data.  XTerm only.

XTerm::SetURXVTMouseMode($on = true)
------------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if URXVT mouse mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn URXVT mouse mode on or off.  It is up to the caller to handle mouse event data.  XTerm only.

XTerm::SetBracketedPasteMode($on = true)
----------------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if bracketed paste mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn bracketed paste mode on or off.  Surrounds pasted text with special escape codes.  It is up to the caller to handle pasted data.  Off by default.

XTerm::RequestCursorPosition()
------------------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code to request the current cursor position.  It is up to the caller to get/handle the response.

XTerm::ResetTerminal($full)
---------------------------

Access:  public static

Parameters:

* $full - A boolean that indicates whether to perform a full reset or a soft reset.

Returns:  Nothing.

This static function emits an escape code to perform a full or soft reset of the terminal.

XTerm::Bell()
-------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code to ring the bell.  Usually a ding or thunk sound.

XTerm::MoveForwardIndex()
-------------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that moves the cursor down or scrolls if the cursor is at the scroll region.

XTerm::MoveToNextLine()
-----------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that is equivalent to calling `SetCursorCharacterAbsolute(1)` and `MoveForwardIndex()`.

XTerm::MoveReverseIndex()
-------------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function emits an escape code that moves the cursor up or scrolls if the cursor is at the scroll region.

XTerm::SetApplicationKeypadMode($on = true)
-------------------------------------------

Access:  public static

Parameters:

* $on - A boolean indicating if application keypad mode will be enabled/disabled (Default is true).

Returns:  Nothing.

This static function emits an escape code to turn application keypad mode on or off.  Equivalent to turning NumLock mode off.  Default is numeric keypad mode.

XTerm::SetTitle($title)
-----------------------

Access:  public static

Parameters:

* $title - A string containing the new title.

Returns:  Nothing.

This static function emits an OSC escape code that sets the terminal's title.

XTerm::SetCustomInputMode($mode)
--------------------------------

Access:  public static

Parameters:

* $mode - A string containing one of 'interactive', 'interactive_echo', 'readline', 'readline_secure', or 'none'.

Returns:  Nothing.

This static function emits a custom OSC escape code that sets the terminal's input mode.  Only works with the Run Process SDK.

The most common use-case is to set 'readline_secure' mode to switch the input to a password field, ask the user for a password, and then set the mode to 'readline' to switch the input back to a regular text input field.

The 'interactive' mode requires a non-blocking stdin pipe or socket to function.  Lots of software treats non-blocking stdin as EOF when it fails to read any data on the pipe and will close it, including PHP.

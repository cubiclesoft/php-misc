CalendarEvent Class:  'support/calendar_event.php'
==================================================

The CalendarEvent class is a reusable class that provides a versatile scheduling and calendaring interface.  It can be used to generate a calendar of days for a given month and supports 'cron'-like scheduling of events.  The class is very efficient at calculating a calendar, supports schedule exceptions, one-time and recurring events, timezones, and also supports serialization.

The class can be used for displaying a calendar but is much more useful for calculating the next UNIX timestamp of when a complex schedule will trigger.  That timestamp can be stored in an indexed database column and then used by a script to know when to run a process based on a specific row of data.  When the process finishes running, the next UNIX timestamp is calculated and stored.

Example usage:

```php
<?php
	require_once "support/calendar_event.php";

	// Every January and July 1st and 15th-17th at midnight starting at Jan 1, 2010 (CalendarEvent style).
	$calendar = new CalendarEvent();
	$result = $calendar->AddSchedule("Jan,7 * * 1,15-17 0 0 0 2010-01-01 *");
	if (!$result["success"])
	{
		echo "Unable to set schedule.\n";
		var_dump($result);

		exit();
	}
	$calendar->RebuildCalendar();

	$result = $calendar->NextTrigger();
	echo "Next January or July 1, 15, 16, or 17:  " . date("Y-m-d H:i:s", $result["ts"]) . "\n";

	// Every five minutes (standard cron-style).
	$calendar = new CalendarEvent();
	$result = $calendar->AddSchedule("cron 0/5 * * * *");
	if (!$result["success"])
	{
		echo "Unable to set schedule.\n";
		var_dump($result);

		exit();
	}
	$calendar->RebuildCalendar();

	$result = $calendar->NextTrigger();
	echo "Next 5 minute marker:  " . date("Y-m-d H:i:s", $result["ts"]) . "\n";
?>
```

While cron-style schedules such as the second example above are probably the more common use-case, the CalendarEvent style supports a richer range of schedules, including complex rules such as "the second to last Thursday of the month" and also supports event triggers to a resolution of one second.

CalendarEvent::__construct($data = array())
-------------------------------------------

Access:  public

Parameters:

* $data - An array containing data previously retrieved with GetData().

Returns:  Nothing.

Initializes the class with the supplied data array.

CalendarEvent::Init()
---------------------

Access:  private

Parameters:  None.

Returns:  Nothing.

This internal function initializes the class (or reinitializes the class when `SetData()` is called).

CalendarEvent::SetData($data = array())
---------------------------------------

Access:  public

Parameters:

* $data - An array containing data previously retrieved with GetData().

Returns:  Nothing.

Restores a class instance's values with the values in the array.  The CalendarEvent constructor also accepts similar input.  This function should only be called with the same data that GetData() returns.

CalendarEvent::GetData()
------------------------

Access:  public

Parameters:  None.

Returns:  An array of data suitable for later use with `SetData()` or the constructor of a new object.

This function returns the internal data structure as an array ready for serialization.  Designed to be used in a later `SetData()` call.

CalendarEvent::SetTime($ts = false)
-----------------------------------

Access:  public

Parameters:

* $ts - An integer representing a valid timestamp or a boolean of false (Default is false).

Returns:  Nothing.

This function sets an internal current timestamp.  Automatically set to the current time on class initialization.  This value is used during processing so that dates and times don't potentially change and therefore won't mess up calculations.

CalendarEvent::SetTimezone($tz)
-------------------------------

Access:  public

Parameters:

* $tz - A string containing a valid timezone.

Returns:  Nothing.

Sets the internal class timezone to the specified timezone.  It is recommended that this function be called before any schedules or exceptions are added if the timezone is different than the configured default timezone in the PHP INI file.

CalendarEvent::GetTimezone()
----------------------------

Access:  public

Parameters:  None.

Returns:  A string containing the internal class timezone.

Gets the internal class timezone.

CalendarEvent::SetStartWeekday($weekday)
----------------------------------------

Access:  public

Parameters:

* $weekday - A string containing an English three-letter weekday.

Returns:  A boolean of true if the starting weekday was successfully set, false otherwise.

Sets the internal starting weekday.  The starting weekday affects the numeric value of the 'weekday' option and the 'weekrows' option of schedules.  The default starting weekday is "Sun".  It is recommended that this function be called before any schedules or exceptions are added.

CalendarEvent::AddSchedule($options, $replaceid = false)
--------------------------------------------------------

Access:  public

Parameters:

* $options - A string or array of options that define a schedule.
* $replaceid - An integer containing a valid schedule ID to replace an existing schedule or a boolean of false to create a new schedule.

Returns:  A standard array of information.

This function is the primary scheduling workhorse of CalendarEvent.  It parses, validates, and adds schedules to the internal data structures.  The `$options` parameter accepts three different forms of input:

* A string in CalendarEvent style:  "months weekrows weekday days hours mins secs startdate[/dayskip[/weekskip]] enddate [duration]"
* A string in cron-style:  "cron [secs] mins hours days months weekday"
* An array of key/value pairs where each key is optional and can be any of "months", "weekrows", "weekday", "days", "hours", "mins", "secs", "startdate", "enddate", and/or "duration".

The cron-style format is the official 'cron' format and must begin with the word "cron" followed by a space (not in quotes).

The "months", "weekrows", "weekday", "days", "hours", "mins", and "secs" options support various common cron expressions for pattern matching:

* Asterisk (*) - All valid values for the option will pattern match.
* Hyphen (-) - Used to specify ranges of values (e.g. 4-10).
* Comma (,) - Used to separate multiple values or ranges (e.g. 1,4-6,15).
* Slash (/) - Used to separate a base from an increment (e.g. if "mins" is defined as 0/5, then, starting at 0, every fifth minute is a pattern match).

Other rules that apply to each option:

* months - The valid integer range is 1-12.  Also accepts English three-letter abbreviations that are case-insensitive.
* weekrows - The valid integer range is 1-6.  Prefix with 'R' to "reverse" the order of the rows of the weeks.  Prefix with 'F' to only count complete weeks of the month.
* weekday - The valid integer range is 1-7.  Also accepts English three-letter abbreviations that are case-insensitive.  Prefix with 'N' to select the nearest weekday to the selected weekdays within the current month.  Useful for selecting the nearest weekday to a specific date.  In the event of a tie, the earlier weekday is selected.  Prefix with 'N-' to look for a match first to earlier days.  Prefix with 'N+' to look for a match first to later days.  If the 'R' prefix for "days" is specified, then the preferred direction is inverted.
* days - The valid integer range is 1-31.  Prefix with 'R' to "reverse" the order of the days in each month.  Useful for scheduling events 'x' days from the end of the month.
* hours - The valid integer range without 'am' or 'pm' indicators is 0-23.  The valid integer range with 'am' and 'pm' indicators is 1-12.  Also supports 'a.m.' and 'p.m.'.
* mins - The valid integer range is 0-59.
* secs - The valid integer range is 0-59.
* startdate - The date is specified in the ISO 8601 International Date Format (YYYY-MM-DD).
	* dayskip - Optional.  The valid integer range is 1 or greater.  '*', '0', and '1' are treated as synonymous.  Prefixes for "days" and "weekrows" do not affect this value.  Based at "startdate".
	* weekskip - Optional.  The valid integer range is 1 or greater.  '*', '0', and '1' are treated as synonymous.  Prefixes for "days" and "weekrows" do not affect this value.  Based at "startdate".
* enddate - An asterisk (*) means no end date, otherwise the date is specified in the ISO 8601 International Date Format (YYYY-MM-DD).
* duration - Optional.  The valid integer range is 0-86400.

Example schedules using strings in the CalendarEvent style:

```
Jan,7 * * 1,15-17 0 0 0 2010-01-01 *
(Every January and July 1st, and 15th-17th at midnight starting at Jan 1, 2010.)

* 2,4 Tue-Thu * 15 30 0/5 2010-01-01 *
* * Tue-Thu 8-14,22-28 15 30 0/5 2010-01-01 *
(Every 2nd and 4th Tue, Wed, and Thu of each month and every 5 seconds during 3:30 p.m. starting at Jan 1, 2010.
 Both examples are similar but they depend on the perspective.)

* * Sat,Sun * 0 0 0 2010-01-01/*/2 *
(Every other Sat and Sun at midnight starting at Jan 1, 2010.)

* * * * * * 0 2010-01-01 *
(Every minute of every day starting at Jan 1, 2010.)

* * * * 3pm 30 0 2010-01-01 2010-01-01
(One-time at 3:30:00 p.m. on Jan 1, 2010.)

* * Fri R1-7 12am 0 0 2010-01-01 *
(Every last Friday of every month at midnight starting at Jan 1, 2010.)

Jul * N-Mon-Fri 4 0 0 0 2010-01-01 *
(The nearest weekday in July to every July 4 at midnight starting at Jan 1, 2010 with a preference for Friday.)
```

CalendarEvent::GetSchedules()
-----------------------------

Access:  public

Parameters:  None.

Returns:  An array of schedules.

This function returns the internal array of schedules.  Each array element is a key/value pair where the key is the schedule ID and the value is an array containing expanded options and the "origopts" key contains the original options passed to AddSchedule().

CalendarEvent::GetCachedSchedules()
-----------------------------------

Access:  public

Parameters:  None.

Returns:  An array of cached schedules or a boolean of false if `RebuildCalendar()` has not been called.

This function returns the internal array of cached schedules.  Each array element is a key/value pair where the key is the schedule ID and the value contains parsed options based on `AddSchedule()`.  This array is used to rapidly generate calendars of schedules.

CalendarEvent::RemoveSchedule($id)
----------------------------------

Access:  public

Parameters:

* $id - An integer containing a valid schedule ID.

Returns:  A boolean of true if the schedule was successfully removed, false otherwise.

This function removes a specific schedule by ID.

CalendarEvent::SetScheduleDuration($id, $duration)
--------------------------------------------------

Access:  public

Parameters:

* $id - An integer containing a valid schedule ID.
* $duration - An integer containing the duration to set.

Returns:  A boolean of true if the duration was successfully set, false otherwise.

This function sets the duration of a schedule.  It is up to the application to best determine how to use a schedule duration.  CalendarEvent just offers this functionality as a convenient storage mechanism.

CalendarEvent::AddScheduleException($options)
---------------------------------------------

Access:  public

Parameters:

* $options - A string or array of options that define a schedule exception.

Returns:  An array with "success" set to a boolean of true plus other information if the schedule exception was successfully added, false otherwise.

This function creates a schedule exception for a specific date.  A schedule exception replaces all schedules that run on a specific date with the schedule in the exception on a target date.  The $options parameter accepts two different forms of input:

* A string in the following format:  "srcdate destdate hours mins secs [duration]"
* An array of key/value pairs where each key is optional and can be any of "srcdate", "destdate", "hours", "mins", "secs", and/or "duration".

The "hours", "mins", and "secs" options support various common cron expressions for pattern matching:

* Asterisk (*) - All valid values for the option will pattern match.
* Hyphen (-) - Used to specify ranges of values (e.g. 4-10).
* Comma (,) - Used to separate multiple values or ranges (e.g. 1,4-6,15).
* Slash (/) - Used to separate a base from an increment (e.g. if "mins" is defined as 0/5, then, starting at 0, every fifth minute is a pattern match).

Other rules that apply to each option:

* srcdate - The date is specified in the ISO 8601 International Date Format (YYYY-MM-DD).
* destdate - The date is specified in the ISO 8601 International Date Format (YYYY-MM-DD).
* hours - The valid integer range without 'am' or 'pm' indicators is 0-23.  The valid integer range with 'am' and 'pm' indicators is 1-12.  Also supports 'a.m.' and 'p.m.'.
* mins - The valid integer range is 0-59.
* secs - The valid integer range is 0-59.
* duration - Optional.  The valid integer range is 0-86400.

Most of the time a schedule exception occurs on the same date, so both "srcdate" and "destdate" will typically be the same.  The "destdate" is the date on which the schedule exception will take place, so it is possible to shift the exception to some other date - including dates that have already past.

CalendarEvent::GetScheduleExceptions()
--------------------------------------

Access:  public

Parameters:  None.

Returns:  An array of schedule exceptions.

This function returns the internal array of schedules.  Each array element is a key/value pair where the key is the schedule exception "srcdate" and the value is an array containing expanded options and the "origopts" key contains the original options passed to AddScheduleException().

CalendarEvent::GetCachedScheduleExceptions()
--------------------------------------------

Access:  public

Parameters:  None.

Returns:  An array of cached schedule exceptions or a boolean of false if `RebuildCalendar()` has not been called.

This function returns the internal array of cached schedule exceptions.  Each array element is a key/value pair where the key is the schedule exception "srcdate" and the value contains parsed options based on AddScheduleException().  This array is used to rapidly generate calendars of schedules.

CalendarEvent::RemoveScheduleException($id)
-------------------------------------------

Access:  public

Parameters:

* $id - A string containing a valid schedule exception "srcdate".

Returns:  A boolean of true if the schedule exception was successfully removed, false otherwise.

This function removes a specific schedule exception by "srcdate".

CalendarEvent::SetScheduleExceptionDuration($id, $duration)
-----------------------------------------------------------

Access:  public

Parameters:

* $id - A string containing a valid schedule exception "srcdate".
* $duration - An integer containing the duration to set.

Returns:  A boolean of true if the duration was successfully set, false otherwise.

This function sets the duration of a schedule exception.  It is up to the application to best determine how to use a schedule exception duration.  CalendarEvent just offers this functionality as a convenient storage mechanism.

CalendarEvent::GetCalendar($year, $month, $sparse = false)
----------------------------------------------------------

Access:  public

Parameters:

* $year - An integer containing a four-digit year.
* $month - An integer containing a month number in the range 1-12.
* $sparse - A boolean of true to generate a sparse calender, false to generate a full calendar.

Returns:  An array containing scheduling information for the given month.

Generates a calendar of days for a given month and IDs of schedules and schedule exceptions that run on those days.  When `$sparse` is true, only days with schedules that run are included in the results.  Additional information is included in the returned results to easily generate a displayable calendar (e.g. HTML).

CalendarEvent::GetTimes($id)
----------------------------

Access:  public

Parameters:

* $id - An integer containing a valid schedule ID.

Returns:  An array with "success" set to a boolean of true plus other information if the schedule ID is valid, false otherwise.

This function retrieves the expanded times (hours, mins, secs) used in the AddSchedule() call.  Generally used by the class while rebuilding the calendar.

CalendarEvent::GetExceptionTimes($srcdate)
------------------------------------------

Access:  public

Parameters:

* $srcdate - A string containing a valid schedule exception "srcdate".

Returns:  An array with "success" set to a boolean of true plus other information if the schedule exception $srcdate is valid, false otherwise.

This function retrieves the expanded times (hours, mins, secs) used in the AddScheduleException() call.  Generally used by the class while rebuilding the calendar.

CalendarEvent::NextTrigger()
----------------------------

Access:  public

Parameters:  None.

Returns:  An array of information if the next trigger timestamp is less than 14 months away, a boolean of false otherwise.

This function determines the timestamp of the next trigger - the next timestamp a pattern matches - based on schedules and schedule exceptions.  The best approach is to cache the result of this function.  Then wait for the cached value to expire/pass, do whatever needs to be processed, and set the cached value to false.  Then, whenever the cached result is false, create a CalendarEvent object to call NextTrigger() and save the result to the cache.

CalendarEvent::RebuildCalendar()
--------------------------------

Access:  public

Parameters:  None.

Returns:  Nothing.

This function forces the internal calendar to rebuild and reset the next trigger timestamp.  Call this function after making changes that affect schedules, exceptions, timezones, or the starting weekday.  For example, adding a new schedule requires calling AddSchedule() and then RebuildCalendar().  Multiple changes can be made before calling this function.

CalendarEvent::AddNextMonthToCalendar()
---------------------------------------

Access:  private

Parameters:  None.

Returns:  Nothing.

This internal function uses `GetCalendar()` to add the next month to the cached calendar.

CalendarEvent::FindNextTrigger()
--------------------------------

Access:  private

Parameters:  None.

Returns:  Nothing.

This internal function is used by `RebuildCalendar()` and `NextTrigger()` to calculate the next date and time that a schedule will fire.

CalendarEvent::FindNextTriggerToday($ts, $newts, &$info, $type, $id)
--------------------------------------------------------------------

Access:  private

Parameters:

* $ts - An integer containing the current time.
* $newts - An array containing information about a new time (without date) or false.
* $info - An array containing the results of a `GetTimes()` or `GetExceptionTimes()` call.
* $type - A string containing one of "id" or "exception".
* $id - An integer containing a schedule ID when the type is "id" or a string containing a date when the type is "exception.

Returns:  An array containing information about a new time or false.

This internal function uses the input information about each schedule to calculate the earliest possible time after $ts.

CalendarEvent::ExpandNewTS($newts, $year, $month, $day)
-------------------------------------------------------

Access:  private

Parameters:

* $newts - An array containing information about a new time (without date).
* $year - An integer containing the year.
* $month - An integer containing the month.
* $day - An integer containing the day.

Returns:  An updated array of information with a UNIX timestamp (date + time) on success, a boolean of false on failure (mktime() fails for some reason).

This internal function expands a timestamp to its correct value with auto-adjustment for DST.  Used by `FindNextTrigger()` once it has calculated the earliest "next" time without a date.

CalendarEvent::IsValidExpr(&$result, $expr, $minnum, $maxnum, $namemap = array(), $exprprefix = array(), $hours = false)
------------------------------------------------------------------------------------------------------------------------

Access:  private

Parameters:

* $result - A variable passed by reference to collect the results of a `ParseExpr()` call.
* $expr - A string containing an expression to be parsed.
* $minnum - An integer containing the minimum allowed value in the expression.
* $maxnum - An integer containing the maximum allowed value in the expression.
* $namemap - An array of key-value pairs that map a string to their integer equivalent (Default is array()).
* $exprprefix - An array of strings containing allowed expression prefixes (Default is array()).
* $hours - A boolean that indicates whether or not the expression contains hours (Default is false).

Returns:  A boolean of true if the expression is valid, false otherwise.

This internal function calls `ParseExpr()`.

CalendarEvent::ParseExpr($expr, $minnum, $maxnum, $namemap = array(), $exprprefix = array(), $hours = false)
------------------------------------------------------------------------------------------------------------

Access:  private

Parameters:

* $result - A variable passed by reference to collect the results of a `ParseExpr()` call.
* $expr - A string containing an expression to be parsed.
* $minnum - An integer containing the minimum allowed value in the expression.
* $maxnum - An integer containing the maximum allowed value in the expression.
* $namemap - An array of key-value pairs that map a string to their integer equivalent (Default is array()).
* $exprprefix - An array of strings containing allowed expression prefixes (Default is array()).
* $hours - A boolean that indicates whether or not the expression contains hours (Default is false).

Returns:  A standard array of information.

This internal function parses the expression into its component parts.  When names are allowed (e.g. months and weekdays), they are mapped to their integer equivalents.  When `$hours` is true, the component parts are passed to `ParseHours()` for 12-hour clock conversion.

CalendarEvent::ParseHour($hour)
-------------------------------

Access:  private

Parameters:

* $hour - A string containing an hour.

Returns:  An integer in the range of 0-23 based on the hour supplied on success, -1 otherwise.

This internal function parses the hour supplied, converting from a 12-hour clock (am and pm) to a 24-hour clock (0-23).  Strings such as "14am" are treated as invalid.

CalendarEvent::IsValidDate(&$result, $date, $start = false)
-----------------------------------------------------------

Access:  private

Parameters:

* $result - A variable passed by reference to collect the results of a ParseDate()` call.
* $date - A string containing a date in 'YYYY-MM-DD' international format.
* $start - A boolean that also parses optional 'dayskip' and 'weekskip' values calculated from the start date (Default is false).

Returns:  A boolean of true if the date is valid, false otherwise.

This internal function calls `ParseDate()`.

CalendarEvent::ParseDate($date, $start = false)
-----------------------------------------------

Access:  private

Parameters:

* $result - A variable passed by reference to collect the results of a ParseDate()` call.
* $date - A string containing a date in 'YYYY-MM-DD' international format.
* $start - A boolean that also parses optional 'dayskip' and 'weekskip' values calculated from the start date (Default is false).

Returns:  A standard array of information.

This internal function parses the input date and optional 'dayskip' and 'weekskip' values for start date into a standardized format.  This function also calculates the timestamp of the supplied date.  The first day of the month of the supplied date and the number of days in the month are also calculated for later use.

CalendarEvent_TZSwitch::__construct($tz)
----------------------------------------

Access:  public

Parameters:

* $tz - A string containing the timezone to set.

Returns:  Nothing.

This class is useful for creating a new, temporary object that sets the timezone until the end of the current function scope, at which point the original timezone is restored.

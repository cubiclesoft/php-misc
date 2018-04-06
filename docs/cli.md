CLI Class:  'support/cli.php'
=============================

This class simplifies a variety of command-line only functions such as parsing command-line options.  It also has question-answer interface input functions to give command-line applications a significant boost to aiding the user in making correct decisions instead of simply displaying an error message and bailing out.

There are also basic logging/error message handling, hex dump, and timer functions.

CLI::ParseCommandLine($options, $args = false)
----------------------------------------------

Access:  public static

Parameters:

* $options - An array containing parsing rules.
* $args - A string of arguments to parse, an array, or a boolean of false to use the $_SERVER["argv"] (Default is false).

Returns:  A standard array of information.

This static function parses the input arguments using the specified rules.  The `$options` array consists of these options:

* shortmap - An array containing key-value pairs that map between a short value (e.g. 'v') and maps it to a rule in the 'rules' map (e.g. 'verbose') (Default is array()).
* rules - An array containing key-value pairs that map a rule name to an array of "arg" and "multiple" boolean options (Default is array()).
* userinput - A boolean of false or a string that specifies the byte (or bytes) to split parameters on (e.g. "=") for later use with the question-answer functions (Default is false).
* allow_opts_after_param - A boolean that specifies whether or not to continue to process options from "rules" after the first parameter is found (Default is true).

Example usage:

```php
<?php
	if (!isset($_SERVER["argc"]) || !$_SERVER["argc"])
	{
		echo "This file is intended to be run from the command-line.";

		exit();
	}

	// Temporary root.
	$rootpath = str_replace("\\", "/", dirname(__FILE__));

	require_once $rootpath . "/support/cli.php";

	// Process the command-line options.
	$options = array(
		"shortmap" => array(
			"c" => "config",
			"d" => "debug",
			"s" => "suppressoutput",
			"?" => "help"
		),
		"rules" => array(
			"config" => array("arg" => true),
			"debug" => array("arg" => false),
			"suppressoutput" => array("arg" => false),
			"help" => array("arg" => false)
		),
		"userinput" => "="
	);
	$args = CLI::ParseCommandLine($options);

	// Display syntax/options.
	if (isset($args["opts"]["help"]))
	{
		echo "A command-line tool\n";
		echo "Purpose:  Manage stuff.\n";
		echo "\n";
		echo "This tool is question/answer enabled.  Just running it will provide a guided interface.  It can also be run entirely from the command-line if you know all the answers.\n";
		echo "\n";
		echo "Syntax:  " . $args["file"] . " [options] [cmd [cmdoptions]]\n";
		echo "Options:\n";
		echo "\t-s   Suppress most output.  Useful for capturing JSON output.\n";
		echo "\n";
		echo "Examples:\n";
		echo "\tphp " . $args["file"] . "\n";
		echo "\tphp " . $args["file"] . " create name=test\n";
		echo "\tphp " . $args["file"] . " -s list\n";

		exit();
	}

	$suppressoutput = (isset($args["opts"]["suppressoutput"]) && $args["opts"]["suppressoutput"]);
?>
```

CLI::CanGetUserInputWithArgs(&$args, $prefix)
---------------------------------------------

Access:  public static

Parameters:

* $args - An array of arguments from an earlier ParseCommandLine() call with a "userinput" rule.
* $prefix - A boolean of false or a string containing the key to look up in user input.

Returns:  A boolean that indicates whether or not there is user input available.

This static function can be used to decide whether or not to perform an expensive operation (e.g. avoid an unnecessary network request) or display extra information when using a question-answer interface.

Example usage:

```php
<?php
	...

	function GetDomainName()
	{
		global $suppressoutput, $args, $api;

		if ($suppressoutput || CLI::CanGetUserInputWithArgs($args, "domain"))
		{
			$domainname = CLI::GetUserInputWithArgs($args, "domain", "Domain name (TLD, no subdomains)", false, "", $suppressoutput);
		}
		else
		{
			$result = $api->DomainsList();
			if (!$result["success"])  DisplayResult($result);

			$domainnames = array();
			foreach ($result["data"] as $domain)  $domainnames[] = $domain["name"];
			if (!count($domainnames))  CLI::DisplayError("No domains have been created.  Try creating your first domain with the API:  domains create");
			$domainname = CLI::GetLimitedUserInputWithArgs($args, "domain", "Domain name (TLD, no subdomains)", false, "Available domain names:", $domainnames, true, $suppressoutput);
			$domainname = $domainnames[$domainname];
		}

		return $domainname;
	}

	$domain = GetDomainName();
?>
```

CLI::GetUserInputWithArgs(&$args, $prefix, $question, $default, $noparamsoutput = "", $suppressoutput = false, $callback = false, $callbackopts = false)
--------------------------------------------------------------------------------------------------------------------------------------------------------

Access:  public static

Parameters:

* $args - An array of arguments from an earlier ParseCommandLine() call with a "userinput" rule.
* $prefix - A boolean of false or a string containing the key to look up in user input.
* $question - A string containing the question to ask the user if user input is not available.
* $default - A boolean of false or a string containing a default value.
* $noparamsoutput - A string containing the information to display to the user before asking the question (Default is "").
* $suppressoutput - A boolean that determines whether or not to suppress output for purposes of automation such as JSON output (Default is false).
* $callback - A valid callback function for validating the entered line of information from the user (Default is false).  The callback function must accept two parameters - callback($line, $opts).
* $callbackopts - Data to pass as the second parameter to the function specified by the callback option (Default is false).

Returns:  A string containing the user's input.

This static function returns a single line freeform response from the user as an answer to a question.  When using question-answer interfaces, the prefix option allows for additional named options to be supplied on the command-line, which allows all questions to be answered there.  Output suppression allows for automation with JSON output as a response from the command which allows for further automation.

Example usage:

```php
<?php
	...

	$ipaddr = CLI::GetUserInputWithArgs($args, "ipaddr", "Your IP address", false, "To quickly find out what your IP address is, go to:  https://www.google.com/search?q=what's+my+ip");
?>
```

CLI::GetLimitedUserInputWithArgs(&$args, $prefix, $question, $default, $allowedoptionsprefix, $allowedoptions, $loop = true, $suppressoutput = false, $multipleuntil = false)
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------

Access:  public static

Parameters:

* $args - An array of arguments from an earlier ParseCommandLine() call with a "userinput" rule.
* $prefix - A boolean of false or a string containing the key to look up in user input.
* $question - A string containing the question to ask the user if user input is not available.
* $default - A boolean of false or a string containing a default value.
* $allowedoptionsprefix - A string to display before showing the list of options (e.g. "Available domains:").
* $allowedoptions - An array of key-value pairs that map keys to human-readable strings of selectable options.  The user can enter either the key or the value so all keys and values should be unique (the key is returned).
* $loop - A boolean that loops that determines whether or not to loop until a valid value is entered (Default is true).
* $suppressoutput - A boolean that determines whether or not to suppress output for purposes of automation such as JSON output (Default is false).
* $multipleuntil - A boolean of false or an array containing "exit", "nextquestion", and "nextdefault" key-value pairs (Default is false).

Returns:  A string containing the user's input if `$multipleuntil` is false, an array of user inputs otherwise.

This static function returns a strictly controlled response from the user as one or more answers to a question.  When using question-answer interfaces, the prefix option allows for additional named options to be supplied on the command-line, which allows all questions to be answered there.  Output suppression allows for automation with JSON output as a response from the command which allows for further automation.

This function will loop indefinitely until the user enters a valid value from a list of supplied options.

Example usage:

```php
<?php
	...

	$cmds = array(
		"create" => "Create a new entry",
		"list" => "List all entries",
		"delete" => "Delete an entry"
	);

	$cmd = CLI::GetLimitedUserInputWithArgs($args, "cmd", "Command", false, "Available commands:", $cmds, true, $suppressoutput);
?>
```

CLI::GetYesNoUserInputWithArgs(&$args, $prefix, $question, $default, $noparamsoutput = "", $suppressoutput = false)
-------------------------------------------------------------------------------------------------------------------

Access:  public static

Parameters:

* $args - An array of arguments from an earlier ParseCommandLine() call with a "userinput" rule.
* $prefix - A boolean of false or a string containing the key to look up in user input.
* $question - A string containing the question to ask the user if user input is not available.
* $default - A boolean of false or a string containing a default value.
* $noparamsoutput - A string containing the information to display to the user before asking the question (Default is "").
* $suppressoutput - A boolean that determines whether or not to suppress output for purposes of automation such as JSON output (Default is false).

Returns:  A boolean containing the user's response to a Yes/No question.

This static function returns a Y/N response from the user as an answer to a Yes/No question.  When using question-answer interfaces, the prefix option allows for additional named options to be supplied on the command-line, which allows all questions to be answered there.  Output suppression allows for automation with JSON output as a response from the command which allows for further automation.

This function is a convenient wrapper around `GetUserInputWithArgs()`.  The default to a non-Yes response (e.g. 'n', 'N', 'No', 'Zebra') is the assumption the user entered a "No" and therefore false will be returned.  Supplying a sane default response when calling this function is recommended.

Example usage:

```php
<?php
	...

	$quantumcool = CLI::GetYesNoUserInputWithArgs($args, "quantum_cool", "Enable quantum cool mode", "Y", "Enabling quantum cool mode slows things down but keeps qubits from producing wildly inaccurate results.", $suppressoutput);
?>
```

CLI::GetHexDump($data)
----------------------

Access:  public static

Parameters:

* $data - A string to convert to a displayable hex dump.

Returns:  A string containing a displayable hex dump.

This static function converts each input byte to its hex equivalent and, if in the ASCII range, displays the character as well.  The result is mostly compact.

Example usage:

```php
<?php
	echo CLI::GetHexDump("Just a quick test.\n");
?>
```

CLI::LogMessage($msg, $data = null)
-----------------------------------

Access:  public static

Parameters:

* $msg - A string containing the message to output.
* $data - A non-null data type that supports JSON encoding (Default is null).

Returns:  Nothing.

This static function logs to memory a formatted message and also outputs the message to `stderr`.

Example usage:

```php
<?php
	CLI::LogMessage("[Processed] " . $filename);
?>
```

CLI::DisplayError($msg, $result = false, $exit = true)
------------------------------------------------------

Access:  public static

Parameters:

* $msg - A string containing the message to output.
* $result - A boolean of false or a standard array of information containing error details (Default is false).
* $exit - A boolean that indicates whether or not to immediately exit the running script (Default is true).

Returns:  Nothing.

This static function logs an error message, outputs additional error information from result, and exits the process by default.  Useful for quickly exiting a script when a failure condition occurs.

Example usage:

```php
<?php
	...

	$result = array("success" => false, "error" => "Unable to connect to 0.0.0.0:80.", errorcode => "network_failure");
	if (!$result["success"])  CLI::DisplayError("Unable to connect to server.", $result);
?>
```

CLI::GetLogMessages($filters = array())
---------------------------------------

Access:  public static

Parameters:

* $filters - A string or an array of strings containing regular expressions to match against the message log (Default is array()).

Returns:  An array of messages from the message log so far that match the supplied filters.

This static function retrieves matching messages from the message log.  When an empty filters array is supplied (the default), all messages are returned.

Example usage:

```php
<?php
	...

	$filename = "test.jpg";
	CLI::LogMessage("[Processed] " . $filename);

	echo "Logged messages:\n";
	var_dump(CLI::GetLogMessages());
	echo "\n";
	echo "Logged messages after reset:\n";
	CLI::ResetLogMessages();
	var_dump(CLI::GetLogMessages());
	echo "\n\n";
?>
```

CLI::ResetLogMessages()
-----------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function clears the message log.  This function should be called periodically such as after every 20,000 messages.  This is useful for sending notification e-mails and then calling this function to clear the log to continue processing until another bunch of messages have accumulated.

CLI::StartTimer()
-----------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function starts a rudimentary timer.  Useful for quickly identifying how long a script is running in various places.  It is not useful for much else.  Since the timer is static, only one timer can be running.

CLI::UpdateTimer()
------------------

Access:  public static

Parameters:  None.

Returns:  A standard array of information.

This static function calculates the difference in time since the last time this function was called as well as the total amount of time taken since the timer was started.

Example usage:

```php
<?php

	CLI::StartTimer();

	usleep(250000);

	$result = CLI::UpdateTimer();
	echo "Operation took:  " . $result["diff"] . " seconds.  Total time:  " . $result["total"] . " seconds.\n\n";

	usleep(250000);

	$result = CLI::UpdateTimer();
	echo "Operation took:  " . $result["diff"] . " seconds.  Total time:  " . $result["total"] . " seconds.\n\n";
?>
```

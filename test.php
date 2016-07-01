<?php
	// CubicleSoft miscellaneous classes test.
	// (C) 2016 CubicleSoft.  All Rights Reserved.

	if (!isset($_SERVER["argc"]) || !$_SERVER["argc"])
	{
		echo "This file is intended to be run from the command-line.";

		exit();
	}

	// Temporary root.
	$rootpath = str_replace("\\", "/", dirname(__FILE__));

	require_once $rootpath . "/support/cli.php";
	require_once $rootpath . "/support/calendar_event.php";
	require_once $rootpath . "/support/event_manager.php";
	require_once $rootpath . "/support/str_basics.php";

	// Process the command-line options.
	$options = array(
		"shortmap" => array(
			"e" => "examplearg",
			"f" => "flag",
			"v" => "verbose",
			"?" => "help"
		),
		"rules" => array(
			"examplearg" => array("arg" => true),
			"flag" => array("arg" => false),
			"verbose" => array("arg" => false, "multiple" => true),
			"help" => array("arg" => false)
		)
	);
	$args = CLI::ParseCommandLine($options);

	if (isset($args["opts"]["help"]))
	{
		echo "Testing tool for miscellaneous classes\n";
		echo "Purpose:  Perform tests of miscellaneous classes.\n";
		echo "\n";
		echo "Syntax:  " . $args["file"] . " [options] answers\n";
		echo "Options:\n";
		echo "\t-e   Example option with argument.\n";
		echo "\t-f   Test passing a flag.\n";
		echo "\t-v   Verbose.\n";
		echo "\n";
		echo "Examples:\n";
		echo "\tphp " . $args["file"] . " -e=something -f -v -v -v N Y\n";

		exit();
	}

	var_dump($args);
	echo "\n\n";

	$line = CLI::GetUserInputWithArgs($args, "Do you have a CPU", "Y");
	if ($line === "Y")  CLI::LogMessage("Correct!");
	else  CLI::DisplayError("Actually, you do!", false, false);
	echo "\n";

	$line = CLI::GetUserInputWithArgs($args, "Do you have RAM", "Y");
	if ($line === "Y")  CLI::LogMessage("Correct!");
	else  CLI::DisplayError("Actually, you do!", false, false);
	echo "\n";

	echo "Logged messages:\n";
	var_dump(CLI::GetLogMessages());
	echo "\n";
	echo "Logged messages after reset:\n";
	CLI::ResetLogMessages();
	var_dump(CLI::GetLogMessages());
	echo "\n\n";

	echo "CalendarEvent test\n";
	$calendar = new CalendarEvent();
	$result = $calendar->AddSchedule("Jan,7 * * 1,15-17 0 0 0 2010-01-01 *");
	if (!$result["success"])  CLI::DisplayError("Unable to set schedule.", $result);
	$calendar->RebuildCalendar();

	$result = $calendar->NextTrigger();
	echo "Next January or July 1, 15, or 17:  " . date("Y-m-d H:i:s", $result["ts"]) . "\n";

	$calendar = new CalendarEvent();
	$result = $calendar->AddSchedule("cron 0/5 * * * *");
	if (!$result["success"])  CLI::DisplayError("Unable to set schedule.", $result);
	$calendar->RebuildCalendar();

	$result = $calendar->NextTrigger();
	echo "Next 5 minute marker:  " . date("Y-m-d H:i:s", $result["ts"]) . "\n";
	echo "\n\n";

	function TestEventCallback($msg, $data)
	{
		echo __FUNCTION__ . ":  " . $msg . "\n";
		var_dump($data);
		echo "\n";
	}

	function GlobalTestEventCallback($event, $msg, $data)
	{
		echo __FUNCTION__ . ":  " . $event . ":  " . $msg . "\n";
		var_dump($data);
		echo "\n";
	}

	echo "EventManager test\n";
	$em = new EventManager();
	echo "Registered event ID " . $em->Register("awesome::test_event", false, "TestEventCallback") . "\n";
	echo "Registered event ID " . $em->Register("", false, "GlobalTestEventCallback") . "\n";

	$em->Fire("awesome::test_event", array("I like", array("to" => "eat food.")));
	echo "\n\n";

	echo "Str test\n";
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
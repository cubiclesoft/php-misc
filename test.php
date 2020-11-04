<?php
	// CubicleSoft miscellaneous classes test.
	// (C) 2018 CubicleSoft.  All Rights Reserved.

	if (!isset($_SERVER["argc"]) || !$_SERVER["argc"])
	{
		echo "This file is intended to be run from the command-line.";

		exit();
	}

	// Temporary root.
	$rootpath = str_replace("\\", "/", dirname(__FILE__));

	require_once $rootpath . "/support/cli.php";
	require_once $rootpath . "/support/array_utils.php";
	require_once $rootpath . "/support/calendar_event.php";
	require_once $rootpath . "/support/event_manager.php";
	require_once $rootpath . "/support/ipaddr.php";
	require_once $rootpath . "/support/utf8.php";
	require_once $rootpath . "/support/utf_utils.php";
	require_once $rootpath . "/support/serial_number.php";
	require_once $rootpath . "/support/php_minifier.php";
	require_once $rootpath . "/support/natural_language.php";
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

	CLI::StartTimer();

	$line = CLI::GetUserInputWithArgs($args, "cpu", "Do you have a CPU", "Y");
	usleep(250000);
	if ($line === "Y")  CLI::LogMessage("Correct!");
	else  CLI::DisplayError("Actually, you do!", false, false);
	echo "\n";

	$result = CLI::UpdateTimer();
	echo "Question + operation took " . $result["diff"] . " seconds.  Total time so far is " . $result["total"] . " seconds.\n\n";

	$line = CLI::GetUserInputWithArgs($args, "ram", "Do you have RAM", "Y");
	usleep(250000);
	if ($line === "Y")  CLI::LogMessage("Correct!");
	else  CLI::DisplayError("Actually, you do!", false, false);
	echo "\n";

	$result = CLI::UpdateTimer();
	echo "Question + operation took " . $result["diff"] . " seconds.  Total time so far is " . $result["total"] . " seconds.\n\n";

	echo "Logged messages:\n";
	var_dump(CLI::GetLogMessages());
	echo "\n";
	echo "Logged messages after reset:\n";
	CLI::ResetLogMessages();
	var_dump(CLI::GetLogMessages());
	echo "\n\n";

	echo "Hex dump test\n";
	echo CLI::GetHexDump("Just a quick test.\n");
	echo "\n\n";

	echo "ArrayUtils test\n";
	$data = array(
		"test_1" => null,
		"test_2" => "Neat",
		5 => "Don't reset me bro!",
		"test_3" => "Cool",
	);

	var_dump(ArrayUtils::InsertAfterKey($data, "test_1", array(4 => "I should be between test_1 and test_2!", "me_too" => "Me too!")));
	var_dump(ArrayUtils::InsertAfterKey($data, null, array("frist" => "Firrst!")));
	var_dump(ArrayUtils::InsertAfterKey($data, "test_1", array("test_3" => $data["test_3"])));

	var_dump(ArrayUtils::InsertBeforeKey($data, "test_2", array(4 => "I should be between test_1 and test_2!", "me_too" => "Me too!")));
	var_dump(ArrayUtils::InsertBeforeKey($data, null, array("last" => "Laast!")));
	var_dump(ArrayUtils::InsertBeforeKey($data, "test_2", array("test_3" => $data["test_3"])));
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
	echo "Registered event ID " . $em->Register("awesome.test_event", "TestEventCallback") . "\n";
	echo "Registered event ID " . $em->Register("", "GlobalTestEventCallback") . "\n";

	$em->Fire("awesome.test_event", array("I like", array("to" => "eat food.")));
	echo "\n\n";

	echo "IPAddr test\n";
	$ipaddr = IPAddr::NormalizeIP("127.0.0.1");
	var_dump($ipaddr);
	echo "\n";

	$preferipv6 = false;
	$remotehost = "localhost";

	if (IPAddr::IsHostname($remotehost))
	{
		$info = ($preferipv6 ? @dns_get_record($remotehost . ".", DNS_AAAA) : false);
		if ($info === false || !count($info))  $info = @dns_get_record($remotehost . ".", DNS_A);
		if ($info === false || !count($info))  $info = @dns_get_record($remotehost . ".", DNS_ANY);

		$valid = false;

		if ($info !== false)
		{
			foreach ($info as $entry)
			{
				if ($entry["type"] === "A" || ($preferipv6 && $entry["type"] === "AAAA"))
				{
					$remoteip = IPAddr::NormalizeIP($info[0]["ip"]);

					$valid = true;

					break;
				}
			}
		}
	}
	else
	{
		$remoteip = IPAddr::NormalizeIP($remotehost);

		$valid = true;
	}

	if (!$valid)  echo "Invalid remote host specified.  Try again.";
	else  var_dump($remoteip);
	echo "\n";

	$pattern = "64.18.0-15.0-255";
	var_dump(IPAddr::IsMatch($pattern, "127.0.0.1"));
	var_dump(IPAddr::IsMatch($pattern, "64.18.5.2"));
	var_dump(IPAddr::IsMatch($pattern, "::ffff:4012:502"));  // "64.18.5.2" in IPv6 notation.
	echo "\n\n";

	// The last two bytes are invalid so the first couple of var_dump()'s will output strange results.
	echo "UTF8 test\n";
	$str = "So good \xF0\x9F\x98\x82\xFF\x80";
	var_dump($str);
	var_dump(htmlspecialchars($str));
	echo "\n";

	if (!UTF8::IsValid($str))  $str = UTF8::MakeValid($str);
	var_dump($str);
	var_dump(htmlspecialchars($str));
	var_dump(UTF8::ConvertToHTML($str));
	echo "\n\n";

	echo "UTFUtils test\n";
	var_dump(UTFUtils::Convert(UTFUtils::Convert($str, UTFUtils::UTF8, UTFUtils::UTF16_LE), UTFUtils::UTF16_LE, UTFUtils::UTF8));
	echo "\n\n";

	echo "Request class test is in 'test_request.php'.\n";
	echo "\n\n";

	echo "SerialNumber test\n";
	$encryptsecret = "\x26\x2A\x58\xAD\x7C\xC3\x33\x06\x30\x20\xE6\xCE\x11\x18\x01\x1D\x67\x7F\x60\xCE";
	$validatesecret = "\x54\x8E\x92\x07\x34\xCF\xAE\xF4\x70\x5B\x62\xB9\x89\x59\xFD\x62\xEE\x4E\xD1\x2E";

	$options = array(
		"expires" => true,
		"date" => mktime(0, 0, 0, date("n"), date("j") + 31),
		"product_class" => 0,
		"major_ver" => 1,
		"minor_ver" => 0,
		"custom_bits" => 0,
		"encrypt_secret" => $encryptsecret,
		"validate_secret" => $validatesecret
	);

	for ($x = 0; $x < 10; $x++)
	{
		$result = SerialNumber::Generate(1, 1, "test" . $x . "@cubiclesoft.com", $options);
		if (!$result["success"])
		{
			var_dump($result);

			exit();
		}
		echo ($x + 1) . ":  " . $result["serial"] . "\n";

		$result = SerialNumber::Verify($result["serial"], 1, 1, "test" . $x . "@cubiclesoft.com", $options);
		if (!$result["success"])
		{
			var_dump($result);

			exit();
		}
	}
	echo "\n\n";

	echo "PHPMinifier test\n";
	$result = PHPMinifier::Minify("test_request.php", file_get_contents("test_request.php"));
	echo $result["data"] . "\n";
	echo "\n\n";

	echo "NaturalLanguage test\n";
	$cond = "[[test var]]*2<16&&(x+2*3<14&&cool_beans=='Cool beans!')";
	echo $cond . "\n";

	$result = NaturalLanguage::ParseConditional($cond);
	if (!$result["success"])
	{
		var_dump($result);

		exit();
	}

	echo NaturalLanguage::MakeConditional($result["tokens"]) . "\n";

	$data = array(
		"test var" => 7,
		"x" => "7",
		"cool_beans" => "Cool beans!",
		"first_name" => "John",
		"last_name" => "Smith",
		"last_profile_date" => "2019-03-18"
	);

	$data["last_profile_days"] = (new DateTime())->diff(new DateTime($data["last_profile_date"]))->days;

	$result2 = NaturalLanguage::RunConditionalCheck($result["tokens"], $data);
	if (!$result2["success"])
	{
		var_dump($result2);

		exit();
	}

	$rules = array(
		"" => array(
			"type" => "if",
			"matches" => 1,
			"rules" => array(
				array(
					"cond" => $cond,
					"output" => array(
						"Hello ", "@greeting_name", " ", "[[last_name]]", "!  ", "@special_message_intro", "\n\n",
						"@special_message", "\n\n",
						"You last updated your profile on ", "@profile_date", ".", "@old_profile_check"
					)
				),
				array("output" => array("Hello.  There is no special message available."))
			)
		),
		"greeting_name" => array(
			"type" => "data",
			"key" => "first_name",
			"case" => "upper"
		),
		"special_message_intro" => array(
			"type" => "if",
			"randomize" => true,
			"matches" => 1,
			"rules" => array(
				array("output" => "Your special message is:"),
				array("output" => "Your special message today:"),
				array("output" => "The super secret message is:"),
			)
		),
		"special_message" => array(
			"type" => "data",
			"key" => "cool_beans",
			"case" => "first"
		),
		"profile_date" => array(
			"type" => "data",
			"key" => "last_profile_date",
			"format" => "date",
			"date" => "D, M j, Y"
		),
		"old_profile_check" => array(
			"type" => "if",
			"matches" => 1,
			"rules" => array(
				array(
					"cond" => "last_profile_days > 365 * 2",
					"output" => "  {{very_old_profile}}"
				),
				array(
					"cond" => "last_profile_days > 365",
					"output" => "  {{old_profile}}"
				),
			)
		),
		"old_profile" => array(
			"type" => "if",
			"randomize" => true,
			"matches" => 1,
			"rules" => array(
				array("output" => "Your profile hasn't been updated in a while."),
				array("output" => "You might consider updating your profile for best results."),
				array("output" => "Updating your profile every so often is a good idea."),
			)
		),
		"very_old_profile" => array(
			"type" => "if",
			"randomize" => true,
			"matches" => 1,
			"rules" => array(
				array("output" => "Hey, we've noticed you haven't updated your profile for a very long time."),
				array("output" => "It's at least a good idea to change your password on your account."),
				array("output" => "Really old accounts are more easily hacked.  Updating your account password is recommended."),
			)
		),
		"orphaned_rule" => array(
			"type" => "if",
			"randomize" => true,
			"matches" => 1,
			"rules" => array(
				array("output" => "I'm unreferenced by all the other rules."),
				array("output" => "No, really.  I'm not gonna show up."),
			)
		),
	);

	$result2 = NaturalLanguage::ValidateRules($data, $rules);
	if (!$result2["success"])
	{
		var_dump($result2);

		exit();
	}

	$result2 = NaturalLanguage::Generate($data, $rules);
	if (!$result2["success"])
	{
		var_dump($result2);

		exit();
	}

	echo $result2["value"] . "\n\n";
	echo "Unique states:  " . NaturalLanguage::CalculateUniqueStates($rules) . "\n";
	echo "Used data:  " . implode(", ", array_keys($result2["used_data"])) . "\n";
	echo "Used rules:  " . implode(", ", array_keys($result2["used_rules"])) . "\n";
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
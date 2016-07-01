<?php
	// CubicleSoft PHP Miscellaneous README.md generator.
	// (C) 2016 CubicleSoft.  All Rights Reserved.

	if (!isset($_SERVER["argc"]) || !$_SERVER["argc"])
	{
		echo "This file is intended to be run from the command-line.";

		exit();
	}

	$rootpath = str_replace("\\", "/", dirname(__FILE__));

	$classes = json_decode(file_get_contents($rootpath . "/classes.json"), true);
	$classes2 = array();
	foreach ($classes as $name => $details)
	{
		$classes2[] = "* " . $name . " - " . $details;
	}
	$classes = implode("\n", $classes2);

	$data = file_get_contents($rootpath . "/README.md");
	$data = str_replace("@CLASSES@", $classes, $data);

	file_put_contents($rootpath . "/../README.md", $data);
?>
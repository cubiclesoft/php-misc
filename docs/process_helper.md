ProcessHelper Class:  'support/process_helper.php'
==================================================

The ProcessHelper class simplifies non-blocking process creation and process termination across all major platforms.  Either of the [CubicleSoft CreateProcess()](https://github.com/cubiclesoft/createprocess-windows) Windows binaries must be located in the same directory as the ProcessHelper class for the `ProcessHelper::StartProcess()` function to work on Windows.

ProcessHelper::FindExecutable($file, $path = false)
---------------------------------------------------

Access:  public static

Parameters:

* $file - A string containing an executable filename to locate on the system.
* $path - A boolean of false or a string containing the initial path to look in (Default is false).

Returns:  A string containing the full path and filename to the executable on success, a boolean of false otherwise.

This static function attempts to locate a matching executable file.  When $path is not supplied or the file is not found in the specified path, the environment PATH variable is processed.

Example usage:

```php
<?php
	require_once "support/process_helper.php";

	$exefile = self::FindExecutable("taskkill.exe");
var_dump($exefile);

	$ps = self::FindExecutable("ps", "/bin");
var_dump($ps);
?>
```

ProcessHelper::GetUserInfoByID($uid)
------------------------------------

Access:  public static

Parameters:

* $uid - An integer containing a user ID to retrieve information for.

Returns:  An array of information about the user on success, a boolean of false otherwise.

This static function calls `posix_getpwuid` and also caches the response information for later.  This function only works on systems that support the POSIX extension.

ProcessHelper::GetUserInfoByName($name)
---------------------------------------

Access:  public static

Parameters:

* $name - A string containing a username.

Returns:  An array of information about the user on success, a boolean of false otherwise.

This static function calls `posix_getpwnam` and also caches the response information for later.  This function only works on systems that support the POSIX extension.

ProcessHelper::GetUserName($uid)
--------------------------------

Access:  public static

Parameters:

* $uid - An integer containing a user ID to retrieve the username of.

Returns:  A string containing the username on success, an empty string otherwise.

This static function calls `GetUserInfoByID()`.

ProcessHelper::GetGroupInfoByID($gid)
-------------------------------------

Access:  public static

Parameters:

* $gid - An integer containing a group ID to retrieve information for.

Returns:  An array of information about the group on success, a boolean of false otherwise.

This static function calls `posix_getgrgid` and also caches the response information for later.  This function only works on systems that support the POSIX extension.

ProcessHelper::GetGroupInfoByName($name)
----------------------------------------

Access:  public static

Parameters:

* $name - A string containing a group name.

Returns:  An array of information about the group on success, a boolean of false otherwise.

This static function calls `posix_getgrnam` and also caches the response information for later.  This function only works on systems that support the POSIX extension.

ProcessHelper::GetGroupName($gid)
---------------------------------

Access:  public static

Parameters:

* $gid - An integer containing a group ID to retrieve information for.

Returns:  A string containing the username on success, an empty string otherwise.

This static function calls `GetGroupInfoByID()`.

ProcessHelper::GetCleanEnvironment()
------------------------------------

Access:  public static

Parameters:  None.

Returns:  An array of environment key-value pairs.

This static function retrieves the current running script environment but removes specific entries that PHP CLI adds on startup.  Useful for constructing an environment for `StartProcess()`.

ProcessHelper::StartProcess($cmd, $options = array())
-----------------------------------------------------

Access:  public static

Parameters:

* $cmd - A string containing the properly escaped command to run.
* $options - An array of options (Default is array()).

Returns:  A standard array of information.

This static function starts a process with non-blocking stdin, stdout, and stderr.  Depending on input options, Windows will generally require 'createprocess.exe' or 'createprocess-win.exe' from [CubicleSoft CreateProcess()](https://github.com/cubiclesoft/createprocess-windows) to be placed into the same directory as this class.

The input command and arguments must be properly escaped with `escapeshellarg()` to avoid the unfortunate situation of letting user input run whatever commands the user might want to run on the system.

The $options array accepts these options:

* stdin - A boolean, a string, a resource, or an array (Default is true).
* stdout - A boolean, a string, a resource, or an array (Default is true).
* stderr - A boolean, a string, a resource, or an array (Default is true).
* tcpstdin - A boolean that indicates whether or not to use TCP/IP for stdin (Default is true).  Windows only.
* tcpstdout - A boolean that indicates whether or not to use TCP/IP for stdout (Default is true).  Windows only.
* tcpstderr - A boolean that indicates whether or not to use TCP/IP for stderr (Default is true).  Windows only.
* createprocess_exe - A string containing the path and filename to 'createprocess.exe' or 'createprocess-win.exe' (or equivalent software).
* user - A string containing the username to start the process as.  POSIX extension required.
* group - A string containing the group name to start the process as.  POSIX extension required.
* env - An array containing the starting environment for the process (Defaults to the output of `GetCleanEnvironment()`).
* dir - A string containing the starting directory for the process (Defaults to current working directory).
* blocking - A boolean that indicates whether or not to set non-blocking mode on the handles (Default is false for non-blocking mode).

For the stdin, stdout, and stderr options:

* true - A pipe will be returned.
* false - Input/output redirected from/to 'NUL' (Windows) or '/dev/null' (other OSes).
* A string - Contains the name of the file to read from/write to.  Will be relative to the starting directory so a full path and filename should ideally be used here.
* A resource - A compatible resource.
* An array - A valid `proc_open()` pipe array.

Note that processes on Windows are not necessarily expecting TCP/IP socket handles to be used in place of pipes (e.g. PHP).  As a result, as soon as zero bytes of data is read from 'stdin' during the next read in PHP userland, the underlying code will decide that it means End-Of-File (EOF) instead of temporarily reaching the end of the stream and will close the handle.  Using false for 'tcpstdin' will correct the problem but run the risk of blocking on writing to stdin.

Example usage:

```php
<?php
	require_once "support/process_helper.php";

	$env = ProcessHelper::GetCleanEnvironment();
	$env["PASSWORD"] = "supersecret!";

	$options = array(
		"tcpstdin" => false,
		"env" => $env
	);

	$cmd = escapeshellarg(PHP_BINARY) . " testfile.php";
	echo $cmd . "\n";
	$result = ProcessHelper::StartProcess($cmd, $options);
	if (!$result["success"])
	{
		var_dump($result);

		exit();
	}

	$stdindata = str_repeat("Lovin' it.\n", 10000);
	$pipes = $result["pipes"];
	$proc = $result["proc"];
	$pid = $result["pid"];

	while (count($pipes))
	{
		$readfps = array();
		if (isset($pipes[1]))  $readfps[] = $pipes[1];
		if (isset($pipes[2]))  $readfps[] = $pipes[2];
		if (!count($readfps))  $readfps = NULL;
		$writefps = (isset($pipes[0]) ? array($pipes[0]) : NULL);
		$exceptfps = NULL;

		$result = @stream_select($readfps, $writefps, $exceptfps, 3);
		if ($result === false)  break;

		$pinfo = @proc_get_status($proc);

		// Send data to stdin.
		if (isset($pipes[0]))
		{
			$result = fwrite($pipes[0], $stdindata);
			if ($result > 0)
			{
				$stdindata = (string)substr($stdindata, $result);
				if ($stdindata === "")
				{
					fclose($pipes[0]);

					unset($pipes[0]);
				}
			}
			else if (!$pinfo["running"])
			{
				fclose($pipes[0]);

				unset($pipes[0]);
			}
		}

		// Read data from stdout and echo it.
		if (isset($pipes[1]))
		{
			$data = fread($pipes[1], 4096);
			if ($data === false || ($data === "" && feof($pipes[1])))
			{
				fclose($pipes[1]);

				unset($pipes[1]);
			}
			else
			{
				echo $data . "\n";
			}
		}

		// Read data from stdout and echo it.
		if (isset($pipes[2]))
		{
			$data = fread($pipes[2], 4096);
			if ($data === false || ($data === "" && feof($pipes[2])))
			{
				fclose($pipes[2]);

				unset($pipes[2]);
			}
			else
			{
				echo $data . "\n";
			}
		}
	}

	echo "Done.\n";
?>
```

ProcessHelper::TerminateProcess($id, $children = true, $force = true)
---------------------------------------------------------------------

Access:  public static

Parmeters:

* $id - An integer containing the ID of the process to terminate.
* $children - A boolean indicating whether or not to terminate all child processes as well (Default is true).
* $force - A boolean indicating whether or not to forcefully terminate the process(es) (Default is true).

Returns:  A boolean indicating whether or not the ability to terminate processes is possible.

This static function attempts to terminate a process and its children by process ID.  Note that this approach to terminating processes has known issues (e.g. a different process could have started and been given the same process ID by the kernel).  This function will only return a failure condition if it can't find a suitable application or function on the system to terminate processes.  It will return true even if the process is not killed for some reason (e.g. permission issues).

Setting $force to false may also have no impact in certain cases (e.g. Windows XP Home and earlier, which don't have `taskkill.exe`).

ProcessHelper::CTstrcmp($secret, $userinput)
--------------------------------------------

Access:  _internal_ static

Parameters:

* $secret - A string containing the secret (e.g. a hash).
* $userinput - A string containing user input.

Returns:  An integer of zero if the two strings match, non-zero otherwise.

This internal static function performs a constant-time strcmp() operation.  Constant-time string compares are used in timing-attack defenses - that is, where comparing two strings with normal functions is a security vulnerability.

This function is a copy of the same function in 'str_basics.php'.

ProcessHelper::PHTranslate($format, ...)
----------------------------------------

Access:  _internal_ static

Parameters:

* $format - A string containing valid sprintf() format specifiers.

Returns:  A string containing a translation.

This internal static function takes input strings and translates them from English to some other language if CS_TRANSLATE_FUNC is defined to be a valid PHP function name.

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

ProcessHelper::NormalizeCommand($cmd)
-------------------------------------

Access:  public static

Parameters:

* $cmd - A string containing a command to eventually execute.

Returns:  The string with the executable portion of the command replaced with a full path and filename properly escaped.

This static function extracts the executable portion of a command, locates the executable on the system, and replaces the command with a shell-escaped variant.  Useful for taking an input string such as "git push origin master" and transforming it into "C:\\path\\to\\git.exe push origin master" on Windows and "/usr/bin/git push origin master" on *NIX.

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

ProcessHelper::MakeTempDir($prefix, $perms = 0770)
--------------------------------------------------

Access:  public static

Parameters:

* $prefix - A string containing a prefix for the temporary directory.
* $perms - An integer, usually octal format, containing the permissions to set the created directory to (Default is 0770).

Returns:  A string containing the newly created directory in the temporary path.

This static function creates and returns a temporary directory with specified access permissions based on the prefix, current process ID, and timestamp.

ProcessHelper::GetCleanEnvironment()
------------------------------------

Access:  public static

Parameters:  None.

Returns:  An array of environment key-value pairs.

This static function retrieves the current running script environment but removes specific entries that PHP CLI adds on startup.  Useful for constructing an environment for `StartProcess()`.

ProcessHelper::ConnectTCPPipe($host, $port, $pipenum, $token)
-------------------------------------------------------------

Access:  public static

Parameters:

* $host - A string containing a host to connect to.
* $port - An integer containing the port to connect to.
* $pipenum - An integer containing the pipe number the socket will map to.
* $token - A string containing a security token used by `GetTCPPipes()`.

Returns:  A standard array of information.

This static function connects to a server started by `StartTCPServer()` and is listening via `GetTCPPipes()`.  Rarely used.

ProcessHelper::StartTCPServer()
-------------------------------

Access:  public static

Parameters:  None.

Returns:  A standard array of information.

This static function starts a localhost only TCP/IP server on a random port and returns a handle to it and some information along with a security token.  Subsequent calls return the already started server information and a new security token.  Used primarily by `StartProcess()` on Windows.

ProcessHelper::GetTCPPipes(&$pipes, $servertoken, $proc, $waitfor = 0.5, $checkcallback = false)
------------------------------------------------------------------------------------------------

Access:  public static

Parameters:

* $pipes - An array of pipe numbers to fill in with TCP/IP sockets.
* $servertoken - A string containing a security token from a `StartTCPServer()` call.
* $proc - A resource to a process handle or a boolean of false.
* $waitfor - A double containing the amount of time to wait, in seconds, before checking for process termination (Default is 0.5).
* $checkcallback - A valid callback function for a timed check callback (Default is false).  The callback function must accept one parameter - callback($pipes).

Returns:  A standard array of information.

This static function determines how many pipes in the array need to be filled in (they contain a boolean of false) and then waits for that many TCP/IP connections with a valid pipe number and security tokens to connect in.

ProcessHelper::StartProcess($cmd, $options = array())
-----------------------------------------------------

Access:  public static

Parameters:

* $cmd - A string containing the properly escaped command to run.
* $options - An array of options (Default is array()).

Returns:  A standard array of information.

This static function starts a process with non-blocking stdin, stdout, and stderr.  Depending on input options, Windows will generally require 'createprocess.exe' or 'createprocess-win.exe' from [CubicleSoft CreateProcess()](https://github.com/cubiclesoft/createprocess-windows/) to be placed into the same directory as this class.

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
* A string - Contains the name of the file to read from/write to.  Will be relative to the starting directory so a full path and filename should ideally be used here.  If an empty string is passed, the default (probably the terminal or console) will be used and no pipe will be returned.
* A resource - A compatible resource.
* An array - A valid `proc_open()` pipe array.

Example usage:

```php
<?php
	require_once "support/process_helper.php";

	$env = ProcessHelper::GetCleanEnvironment();
	$env["PASSWORD"] = "supersecret!";

	$options = array(
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

ProcessHelper::Wait($proc, &$pipes, $stdindata = "", $timeout = -1, $outputcallback = false)
--------------------------------------------------------------------------------------------

Access:  public static

Parmeters:

* $proc - A resource to a process handle or a boolean of false.
* $pipes - An array of standard pipes (0 = stdin, 1 = stdout, 2 = stderr) associated with the process.
* $stdindata - A string containing the entire string to pass to the stdin pipe (Default is "").
* $timeout - An integer containing the amount of time to run the function for, -1 is infinite (Default is -1).
* $outputcallback - A valid callback function for handling output (Default is false).  The callback function must accept two parameters - callback($data, $pipenum).

Returns:  A standard array of information.

This static function passes `stdin` data and waits for the process to complete.  It gathers all `stdout` and `stderr` content and returns it all at once.  This may not be desirable for large amounts of output as it can use up RAM but can be useful for smaller amounts of output.

The optional output callback can be used to echo stdout/stderr data as it arrives.  If stderr data exists, it is only passed to the callback after a newline on stdout and only if stderr has a newline.  This guarantees that stderr data won't show up in the middle of a line of output when echo'ed.

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

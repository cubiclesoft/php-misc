SystemProfile Class:  'support/system_profile.php'
==================================================

The SystemProfile class generates an array of internal device hardware information for the current device and also calculates a unique device fingerprint.  Works for Windows, Mac OSX, various Linux distros (primarily Debian, RedHat, and Arch), FreeBSD, and perhaps other OSes.

Example usage:

```php
<?php
	require_once "support/system_profile.php";

	$result = SystemProfile::GetProfile();

	var_dump($result);
?>
```

SystemProfile::GetHostname()
----------------------------

Access:  public static

Parameters:  None.

Returns:  A string containing the hostname on success, a boolean of false otherwise.

This static function returns the hostname of the current device.

SystemProfile::GetMachineID()
-----------------------------

Access:  public static

Parameters:  None.

Returns:  A string containing the unique machine ID on success, a boolean of false otherwise.

This static function returns the unique machine ID of the current device.

SystemProfile::GetMotherboardInfo()
-----------------------------------

Access:  public static

Parameters:  None.

Returns:  An array containing information about the motherboard on success, a boolean of false otherwise.

This static function returns information about the motherboard for the current device.  Not all OSes have the ability to retrieve this information.

SystemProfile::GetCPUInfo()
---------------------------

Access:  public static

Parameters:  None.

Returns:  An array containing information about the CPU(s) on success, a boolean of false otherwise.

This static function returns information about the CPU(s) for the current device.

SystemProfile::GetRAMInfo()
---------------------------

Access:  public static

Parameters:  None.

Returns:  An array containing information about the RAM on success, a boolean of false otherwise.

This static function returns information about the RAM for the current device.  Most OSes just return the total capacity.  Windows returns information about each individual stick/chip.

SystemProfile::GetGPUInfo()
---------------------------

Access:  public static

Parameters:  None.

Returns:  An array containing information about the GPU on success, a boolean of false otherwise.

This static function returns information about the GPU for the current device.

SystemProfile::GetNICInfo()
---------------------------

Access:  public static

Parameters:  None.

Returns:  An array containing information about the network interfaces on success, a boolean of false otherwise.

This static function returns information about the network interfaces for the current device.  Where possible, physical controllers are included.

SystemProfile::GetDiskInfo()
----------------------------

Access:  public static

Parameters:  None.

Returns:  An array containing information about the internal storage on success, a boolean of false otherwise.

This static function returns information about the internal storage for the current device.

SystemProfile::GetOSInfo()
--------------------------

Access:  public static

Parameters:  None.

Returns:  An array containing information about the current OS on success, a boolean of false otherwise.

This static function returns information about the current OS for the current device.

SystemProfile::GetProfile()
---------------------------

Access:  public static

Parameters:  None.

Returns:  An array containing information about the device.

This static function returns information about the current device and calculates a unique device fingerprint.  Depending on the OS, this function can take upwards of several seconds to run.

SystemProfile::ExtractWMICResults($data, $expectedheader)
---------------------------------------------------------

Access:  protected static

Parameters:

* $data - A string containing the output from `wmic.exe`.
* $expectedheader - A string containing an expected header to exist.

Returns:  An array containing the extracted data on success, a boolean of false otherwise.

This internal static function extracts columnar data from `wmic.exe` output.

SystemProfile::GetPCIConfBSD()
------------------------------

Access:  protected static

Parameters:  None.

Returns:  An array of information from `pciconf` on FreeBSD and similar systems on success, a boolean of false otherwise.

This internal static function extracts data from `pciconf` on FreeBSD and similar systems.

SystemProfile::GetIORegPlatformDeviceOSX()
------------------------------------------

Access:  protected static

Parameters:  None.

Returns:  An array of information from `IOPlatformExpertDevice` data via `ioreg` on Mac OSX on success, a boolean of false otherwise.

This internal static function extracts `IOPlatformExpertDevice` data via `ioreg` on Mac OSX and similar systems.

SystemProfile::FindExecutable($file, $path = false)
---------------------------------------------------

Access:  protected static

Parameters:

* $file - A string containing an executable filename to locate on the system.
* $path - A boolean of false or a string containing the initial path to look in (Default is false).

Returns:  A string containing the full path and filename to the executable on success, a boolean of false otherwise.

This internal static function attempts to locate a matching executable file.  When $path is not supplied or the file is not found in the specified path, the environment PATH variable is processed.  Identical to `ProcessHelper::FindExecutable()`.

SystemProfile::RunCommand($cmd)
-------------------------------

Access:  protected static

Parameters:

* $cmd - A string containing the command line to execute.

Returns:  A string containing the `stdout` output from the process on success, a boolean of false otherwise.

This internal static function runs an executable and gathers output.  On Windows, this function can optionally utilize the `ProcessHelper` class to avoid flashing console windows via `createprocess-win.exe`.

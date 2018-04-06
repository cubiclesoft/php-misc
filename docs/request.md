Request Class:  'support/request.php'
=====================================

This class handles and normalizes common incoming request handling issues across all platforms.

Example usage:

```php
<?php
	require_once "support/request.php";

	// Merge user superglobals into $_REQUEST.
	Request::Normalize();

	if (isset($_REQUEST["test"]))  echo htmlspecialchars($_REQUEST["test"])  . "<br>\n";

	if (Request::IsSSL())  echo "Request is over SSL!<br>\n";

	echo "Current base URL:  " . Request::GetFullURLBase() . "<br>\n";
?>
```

Request::ProcessSingleInput($data)
----------------------------------

Access:  protected static

Parameters:

* $data - An array of key-value pairs to merge into $_REQUEST.

Returns:  Nothing.

This internal static function trims strings in the input array and merges the result into the $_REQUEST superglobal.

Request::Normalize()
--------------------

Access:  public static

Parameters:  None.

Returns:  Nothing.

This static function merges the $_COOKIE, $_GET, and $_POST superglobals into the $_REQUEST superglobal (in that order).  This normalizes user input into the system and allows for simpler code (and therefore fewer application bugs).

Request::IsSSL()
----------------

Access:  public static

Parameters:  None.

Returns:  A boolean of true if the browser is loading the page via SSL, false otherwise.

This static function attempts to detect a SSL connection.  Not all web servers accurately provide the status of SSL to scripting languages.

Request::GetHost($protocol = "")
--------------------------------

Access:  public static

Parameters:

* $protocol - A string containing one of "", "http", or "https" (Default is "").

Returns:  A string containing the host in URL format.

This static function retrieves the host in URL format and looks like `http[s]://www.something.com[:port]` based on the current page request.  The result of this function is cached.  The `$protocol` parameter defaults to whatever type the connection is detected with IsSSLRequest() but can be overridden by specifying "http" or "https".

Request::GetURLBase()
---------------------

Access:  public static

Parameters:  None.

Returns:  A string containing the path part of the request URL (excludes query string).

This static function retrieves the path of request URL.  The $_SERVER["REQUEST_URI"] variable is parsed and the protocol, host, and query string parts are removed if they exist.  This function is used to calculate the destination for generated forms.

Request::GetFullURLBase($protocol = "")
---------------------------------------

Access:  public static

Parameters:

* $protocol - A string containing one of "", "http", or "https" (Default is "").

Returns: A string containing the full request URL.

This function combines GetHost() and GetURLBase() to obtain the full request URL.

Request::PrependHost($url, $protocol = "")
------------------------------------------

Access:  public static

Parameters:

* $url - A string containing part or all of a URL.
* $protocol - A string containing one of "", "http", or "https" (Default is "").

Returns:  A string containing a URL with the host prepended as necessary.

This function takes an incoming partial URL for the current domain and returns a calculated absolute URL based on the current request information.

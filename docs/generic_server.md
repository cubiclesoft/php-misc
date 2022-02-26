GenericServer Class:  'support/generic_server.php'
==================================================

This class implements a generic TCP/IP server.

For example usage, see:

https://github.com/cubiclesoft/net-test
https://github.com/cubiclesoft/php-license-server
https://github.com/cubiclesoft/xcron

GenericServer::Reset()
----------------------

Access:  public

Parameters:  None.

Returns:  Nothing.

This function reinitializes the GenericServer class.

GenericServer::SetDebug($debug)
-------------------------------

Access:  public

Parameters:

* $debug - A boolean indicating whether or not to enable debugging output.

Returns:  Nothing.

This function turns debugging mode on and off.  The initial default is off.  When debugging mode is turned on, the class is fairly noisy.

GenericServer::SetDefaultTimeout($timeout)
------------------------------------------

Access:  public

Parameters:

* $timeout - An integer representing the new timeout value in seconds.

Returns:  Nothing.

This function sets the default timeout for stream_select() calls.  The initial default is 30 seconds.

GenericServer::SetDefaultClientTimeout($timeout)
------------------------------------------------

Access:  public

Parameters:

* $timeout - An integer representing the new timeout value in seconds.

Returns:  Nothing.

This function sets the default timeout for inactive clients.  The initial default is 30 seconds.

GenericServer::GetSSLCiphers($type = "intermediate")
----------------------------------------------------

Access:  public static

Parameters:

* $type - A string containing one of "modern", "intermediate", or "old" (Default is "intermediate").

Returns:  A string containing the SSL cipher list to use.

This static function returns SSL cipher lists extracted from the [Mozilla SSL configuration generator](https://mozilla.github.io/server-side-tls/ssl-config-generator/).

GenericServer::Start($host, $port, $sslopts = false)
----------------------------------------------------

Access:  public

Parameters:

* $host - A string containing the host to bind to.
* $port - An integer containin the port number to bind to.
* $sslopts - An array of PHP SSL context options to use SSL mode on the socket or a boolean of false (Default is false).

Returns:  A standard array of information.

This function attempts to bind to the specified TCP/IP host and port.  Common options for the host are:

* `0.0.0.0` to bind to all IPv4 interfaces.
* `127.0.0.1` to bind to the localhost IPv4 interface.
* `[::0]` to bind to all IPv6 interfaces.
* `[::1]` to bind to the localhost IPv6 interface.

To select a new port number for a server, use the following link:

https://www.random.org/integers/?num=1&min=5001&max=49151&col=5&base=10&format=html&rnd=new

If it shows port 8080, just reload to get a different port number.

The most common options for the $sslopts array are "local_cert" and "local_pk" for selecting a signed certificate and private key respectively.

Example usage:

```php
<?php
	require_once "support/web_server.php";

	$rootpath = str_replace("\\", "/", dirname(__FILE__));

	// Start a localhost web server on port 5585.
	// You should pick a random port as indicated above this example.
	$gs = new GenericServer();
	$result = $gs->Start("127.0.0.1", 5585, array("local_cert" => $rootpath . "/server_cert.pem", "local_pk" => $rootpath . "/server_key.pem"));

	var_dump($result);
?>
```

GenericServer::Stop()
---------------------

Access:  public

Parameters:  None.

Returns:  Nothing.

This function stops the server and cleans up after itself.  Automatically called by the destructor.

GenericServer::GetStream()
--------------------------

Access:  public

Parameters:  None.

Returns:  The internal server socket handle.

This function is considered "dangerous" but allows for stream_select() calls on multiple, separate stream handles to be used.

GenericServer::UpdateStreamsAndTimeout($prefix, &$timeout, &$readfps, &$writefps)
---------------------------------------------------------------------------------

Access:  public

Parameters:

* $prefix - A unique prefix to identify the various streams (server and client handles).
* $timeout - An integer reference containing the maximum number of seconds or a boolean of false.
* $readfps - An array reference to add streams wanting data to arrive.
* $writefps - An array reference to add streams wanting to send data.

Returns:  Nothing.

This function updates the timeout and read/write arrays with prefixed names so that a single stream_select() call can manage all sockets.

GenericServer::FixedStreamSelect(&$readfps, &$writefps, &$exceptfps, $timeout)
------------------------------------------------------------------------------

Access:  public static

Parameters:  Same as stream_select() minus the microsecond parameter.

Returns:  A boolean of true on success, false on failure.

This function allows key-value pairs to work properly for the usual read, write, and except arrays.  PHP's stream_select() function is buggy and sometimes will return correct keys and other times not.  This function is called by Wait().  Directly calling this function is useful if multiple servers are running at a time (e.g. one public SSL server, one localhost non-SSL server).

GenericServer::Wait($timeout = false)
-------------------------------------

Access:  public

Parameters:

* $timeout - An integer containing the maximum number of seconds or a boolean of false.

Returns:  A standard array of information.

This function handles new connections, the initial conversation, basic packet management, rate limits, and timeouts.  The returned "clients" and "removed" arrays contain clients that may need processing.  This function is expected to be part of a loop.

Example usage:

```php
<?php
	require_once "support/web_server.php";

	$rootpath = str_replace("\\", "/", dirname(__FILE__));

	// Start a localhost web server on port 5585.
	// You should pick a random port as indicated by GenericServer::Start().
	$gs = new GenericServer();
	$result = $gs->Start("127.0.0.1", 5585, array("local_cert" => $rootpath . "/server_cert.pem", "local_pk" => $rootpath . "/server_key.pem"));
	if (!$result["success"])
	{
		var_dump($result);

		exit();
	}

	do
	{
		$result = $gs->Wait();
		if (!$result["success"])  break;

		foreach ($result["clients"] as $id => $client)
		{
			// Process the client.
		}

		foreach ($result["removed"] as $id => $info)
		{
			// Process the removed client information (e.g. clean up various tracking arrays).
		}

	} while (1);

	var_dump($result);
?>
```

GenericServer::ProcessWaitResult(&$result)
------------------------------------------

Access:  protected

Parameters:

* $result - An array of standard information containing file handles.

Returns:  Nothing.

This function processes the result of the Wait() function.  Derived classes may call this function (e.g. LibEvGenericServer).

GenericServer::GetClients()
---------------------------

Access:  public

Parameters:  None.

Returns:  The internal array of active clients.

This function retrieves the internal array of active clients.  These are the clients that have made it past the initialization states.

GenericServer::NumClients()
---------------------------

Access:  public

Parameters:  None.

Returns:  The number of active clients.

This function returns the number clients currently connected to the server.  It's more efficient to call this function than to get a copy of the clients array just to `count()` them.

GenericServer::UpdateClientState($id)
-------------------------------------

Access:  public

Parameters:

* $id - An integer containing the ID of the client to update the internal state for.

Returns:  Nothing.

This function does nothing by default.  Derived classes may maintain internal technical state for optimized performance later on (e.g. LibEvGenericServer updates read/write notification state for the socket descriptor for use with a later Wait() call).  It is recommended that this function be called after appending data to $client->writedata.

GenericServer::GetClient($id)
-----------------------------

Access:  public

Parameters:

* $id - An integer containing a client ID.

Returns:  The associated client instance on success, a boolean of false otherwise.

This function retrieves a specific active client.  An active client is one that has made it past the initialization states.

GenericServer::DetachClient($id)
--------------------------------

Access:  _internal_

Parameters:

* $id - An integer containing a client ID.

Returns:  The associated client instance on success, a boolean of false otherwise.

This function detaches a specific active client.  Note that there is no AttachClient() function for GenericServer.  This function may be used by other classes to handle 'Upgrade' style requests to other protocols.

GenericServer::RemoveClient($id)
--------------------------------

Access:  public

Parameters:

* $id - An integer containing a client ID.

Returns:  Nothing.

This function disconnects and removes a specific active client.

GenericServer::InitNewClient($fp)
---------------------------------

Access:  _internal_

Parameters:

* $fp - A stream resource or a boolean of false.

Returns:  A new stdClass instance.

This internal function creates a new client and adds it to the `initclients` array.  The following public variables are available for applications to access in a read-only fashion:

* id - The client ID.
* readdata - A string containing the data read in.
* writedata - A string containing the data to send.
* recvsize - An integer containing the total number of bytes received.
* sendsize - An integer containing the total number of bytes sent.
* lastts - The time of the last interaction with this client.
* ipaddr - A string containing the source IP of the client.

GenericServer::HandleNewConnections(&$readfps, &$writefps)
----------------------------------------------------------

Access:  protected

Parameters:

* $readfps - An array reference to manage streams that might have data to read.
* $writefps - An array reference to manage streams that are probably ready to send data.

Returns:  Nothing.

This protected function handles new incoming connections in Wait().  Can be overridden in a derived class to provide alternate functionality.

GenericServer::StreamTimedOut($fp)
----------------------------------

Access:  private static

Parameters:

* $fp - A valid socket handle.

Returns:  A boolean of true if the underlying socket has timed out, false otherwise.

This internal static function calls `stream_get_meta_data()` to determine the validity of the socket.

GenericServer::ReadClientData($client)
--------------------------------------

Access:  private

Parameters:

* $client - An object containing client information.

Returns:  A standard array of information.

This internal function attempts to read data off the socket and into the `readdata` variable.

GenericServer::WriteClientData($client)
---------------------------------------

Access:  private

Parameters:

* $client - An object containing client information.

Returns:  A standard array of information.

This internal function attempts to write data to the socket from the `writedata` variable.

GenericServer::GSTranslate($format, ...)
----------------------------------------

Access:  _internal_ static

Parameters:

* $format - A string containing valid sprintf() format specifiers.

Returns:  A string containing a translation.

This internal static function takes input strings and translates them from English to some other language if CS_TRANSLATE_FUNC is defined to be a valid PHP function name.

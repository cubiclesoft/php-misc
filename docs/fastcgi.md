FastCGI Class:  'support/fastcgi.php'
=====================================

This class provides client-side routines to communicate with a FastCGI server and can also be the basis of a FastCGI server application.

Example usage:

```php
<?php
	require_once "support/fastcgi.php";

	$ignore = array(
		"PHP_SELF" => true,
		"SCRIPT_NAME" => true,
		"SCRIPT_FILENAME" => true,
		"PATH_TRANSLATED" => true,
		"DOCUMENT_ROOT" => true,
		"REQUEST_TIME_FLOAT" => true,
		"REQUEST_TIME" => true,
		"argv" => true,
		"argc" => true,
	);

	$env = array();
	foreach ($_SERVER as $key => $val)
	{
		if (!isset($ignore[$key]) && is_string($val))  $env[$key] = $val;
	}

	// Set various additional environment variables/params here such as "REQUEST_URI" and "SCRIPT_FILENAME".

	// Some FastCGI server implementation (e.g. PHP-FPM), are unable to properly handle basic information requests.
	// See:  https://bugs.php.net/bug.php?id=76922
	// For such scenarios, make two separate requests.
	$fcgi = new FastCGI();
	$result = $fcgi->Connect("127.0.0.1", 9001);
	if (!$result["success"])
	{
		var_dump($result);
		exit();
	}

	$fcgi->RequestUpdatedLimits();
	$result = $fcgi->Wait();
	while ($result["success"] && !$fcgi->GetRecvRecords())
	{
		do
		{
			$result = $fcgi->NextReadyRequest();
			if (!$result["success"] || $result["id"] === false)  break;

		} while (1);

		$result = $fcgi->Wait();
	}

	$fcgilimits = array(
		"connection" => $fcgi->GetConnectionLimit(),
		"concurrency" => $fcgi->GetConncurrencyLimit(),
		"multiplex" => $fcgi->CanMultiplex(),
	);

	$fcgi->Disconnect();


	// Use the previously retrieved FastCGI limits to initialize the new FastCGI instance.
	$fcgi = new FastCGI();
	$fcgi->SetConnectionLimit($fcgilimits["connection"]);
	$fcgi->SetConncurrencyLimit($fcgilimits["concurrency"]);
	$fcgi->SetMultiplex($fcgilimits["multiplex"]);

	$result = $fcgi->Connect("127.0.0.1", 9001);
	if (!$result["success"])
	{
		var_dump($result);
		exit();
	}

	// Initialize the request.  Requests connection termination at the end of the request.
	$result = $fcgi->BeginRequest(FastCGI::ROLE_RESPONDER, false);
	if (!$result["success"])
	{
		var_dump($result);
		exit();
	}

	$requestid = $result["id"];

	// Send params.
	$result = $fcgi->SendParams($requestid, $env);
	if (!$result["success"])
	{
		var_dump($result);
		exit();
	}

	// Finalize params.
	$result = $fcgi->SendParams($requestid, array());
	if (!$result["success"])
	{
		var_dump($result);
		exit();
	}

	// Send stdin data here (if any).

	// Finalize stdin.
	$result = $fcgi->SendStdin($requestid, "");
	if (!$result["success"])
	{
		var_dump($result);
		exit();
	}

	// Wait for response.
	$result = $fcgi->Wait();
	while ($result["success"])
	{
		do
		{
			$result = $fcgi->NextReadyRequest();
			if (!$result["success"] || $result["id"] === false)  break;

			$request = $result["request"];

			if ($request->stdout !== "")
			{
				echo $request->stdout;
				$request->stdout = "";
			}

			if ($request->stderr !== "")
			{
				echo $request->stderr;
				$request->stderr = "";
			}

			if ($request->ended)
			{
				$fcgi->RemoveRequest($request->id);

				echo "Request complete.\n";
				exit();
			}

		} while (1);

		$result = $fcgi->Wait();
	}
?>
```

FastCGI::Reset()
----------------

Access:  public

Parameters:  None.

Returns:  Nothing.

This function resets a class instance to the default state.  Note that Disconnect() should be called first as this simply resets all internal class variables.

FastCGI::SetServerMode()
------------------------

Access:  public

Parameters:  None.

Returns:  Nothing.

This function switches the client to server mode.  Prefer using the FastCGIServer class for implementing an actual FastCGI server.

FastCGI::SetClientMode()
------------------------

Access:  public

Parameters:  None.

Returns:  Nothing.

This function switches to client mode.  This is the default mode.

FastCGI::SetDebug($debug)
-------------------------

Access:  public

Parameters:

* $debug - A boolean that indicates whether or not to enable debug mode.

Returns:  Nothing.

This function enables or disables connection debug mode.

FastCGI::GetConnectionLimit()
-----------------------------

Access:  public

Parameters:  None.

Returns:  An integer specifying the maximum number of connections supported by the server.

This function returns the server connection limit.  Can be set with `SetConnectionLimit()` or a response by the server to a `RequestUpdatedLimits()` call.

FastCGI::SetConnectionLimit($limit)
-----------------------------------

Access:  public

Parameters:

* $limit - An integer specifying the maximum number of connections supported by the server.

Returns:  Nothing.

This function sets the internal server connection limit value.

FastCGI::GetConncurrencyLimit()
-------------------------------

Access:  public

Parameters:  None.

Returns:  An integer specifying the maximum number of requests supported by the server at one time.

This function returns the server concurrency limit.  Can be set with `SetConncurrencyLimit()` or a response by the server to a `RequestUpdatedLimits()` call.

FastCGI::SetConncurrencyLimit($limit)
-------------------------------------

Access:  public

Parameters:

* $limit - An integer specifying the maximum number of requests supported by the server at one time.

Returns:  Nothing.

This function sets the internal server concurrency limit value.

FastCGI::CanMultiplex()
-----------------------

Access:  public

Parameters:  None.

Returns:  A boolean that indicates whether or not the server supports multiplexing requests.

This function returns whether or not the server supports multiplexing.  Can be set with `SetMultiplex()` or a response by the server to a `RequestUpdatedLimits()` call.

FastCGI::SetMultiplex($multiplex)
---------------------------------

Access:  public

Parameters:

* $multiplex - A boolean that indicates whether or not the server supports multiplexing requests.

Returns:  Nothing.

This function sets the internal server multiplexing support value.

FastCGI::GetRecvRecords()
-------------------------

Access:  public

Parameters:  None.

Returns:  An integer specifying the number of complete records requests received.

This function returns the total number of complete FastCGI records received from the FastCGI server.

FastCGI::GetRawRecvSize()
-------------------------

Access:  public

Parameters:  None.

Returns:  An integer specifying the number of bytes received.

This function returns the total number bytes received from the FastCGI server.

FastCGI::GetSendRecords()
-------------------------

Access:  public

Parameters:  None.

Returns:  An integer specifying the number of complete records requests sent.

This function returns the total number of complete FastCGI records sent to the FastCGI server.

FastCGI::GetRawSendSize()
-------------------------

Access:  public

Parameters:  None.

Returns:  An integer specifying the number of bytes sent.

This function returns the total number bytes sent to the FastCGI server.

FastCGI::GetRawSendQueueSize()
------------------------------

Access:  public

Parameters:  None.

Returns:  An integer specifying the number of bytes to be sent.

This function returns the total number bytes queued up to be sent to the FastCGI server.

FastCGI::Connect($host, $port = -1, $timeout = 10, $async = false)
------------------------------------------------------------------

Access:  public

Parameters:

* $host - A string containing a valid host IP address or Unix socket.
* $port - An integer containing a port number (Default is -1 for a Unix socket host).
* $timeout - An integer containing the amount of time in seconds to wait for the connection to succeed (Default is 10).
* $async - A boolean indicating whether or not to connect asynchronously to the FastCGI server (Default is false).

Returns:  A standard array of information.

This function connects to a FastCGI server.  It supports both IP address and Unix socket hosts.  The FastCGI client always switches to non-blocking mode after connecting.

FastCGI::Disconnect()
---------------------

Access:  public

Parameters:  None.

Returns:  Nothing.

This function disconnects an active connection and resets a few internal variables in case the class is reused.

FastCGI::RequestUpdatedLimits()
-------------------------------

Access:  public, client mode

Parameters:  None.

Returns:  A standard array of information.

This function adds a record to the queue for updated information from the FastCGI server.  Depending on the FastCGI server implementation, this may cause the server to disconnect after this "request" is completed.

Currently requests updated information for:  FCGI_MAX_CONNS, FCGI_MAX_REQS, and FCGI_MPXS_CONNS.

FastCGI::BeginRequest($role, $keepalive = true)
-----------------------------------------------

Access:  public, client mode

Parameters:

* $role - An integer containing a valid FastCGI role.
* $keepalive - A boolean that tells the server whether or not to keep the connection alive after completing the request (Default is true).

Returns:  A standard array of information.

This function starts a new FastCGI request with the FastCGI server.  Multiple requests at one time are only supported if the server supports multiplexing.  The first available request ID is used.

The following FastCGI role constants are available:

* FastCGI::ROLE_RESPONDER - This is the most common role and probably the right one to use.
* FastCGI::ROLE_AUTHORIZER - The authorizer role.  Not really sure what the use-case is for this one.
* FastCGI::ROLE_FILTER - The filter role.  Intended to transform output before sending it to a web browser where the transformation instructions are passed on the DATA channel.  Probably mostly for XSLT, which no one uses.

FastCGI::GetRequest($requestid)
-------------------------------

Access:  public

Parameters:

* $requestid - An integer containing the ID of a request.

Returns:  The associated request object on success, false otherwise.

This function returns the request object associated with a specific request ID.

FastCGI::AbortRequest($requestid)
---------------------------------

Access:  public, client mode

Parameters:

* $requestid - An integer containing the ID of a request.

Returns:  A standard array of information.

This function tells the FastCGI server to abort the specified request as soon as possible.

FastCGI::EndRequest($requestid, $appstatus, $protocolstatus = FastCGI::PROTOCOL_STATUS_REQUEST_COMPLETE)
--------------------------------------------------------------------------------------------------------

Access:  public, server mode

Parameters:

* $requestid - An integer containing the ID of a request.
* $appstatus - An integer containing the application status.
* $protocolstatus - An integer containing one of the FastCGI protocol status codes (Default is FastCGI::PROTOCOL_STATUS_REQUEST_COMPLETE).

Returns:  A standard array of information.

This function tells the FastCGI client that a specific request has ended, the application status code, and the FastCGI protocol status code.

The following FastCGI protocol status code constants are available:

* FastCGI::PROTOCOL_STATUS_REQUEST_COMPLETE - The most common status code.
* FastCGI::PROTOCOL_STATUS_CANT_MPX_CONN - The server does not support multiplexing (automatically handled by this class).
* FastCGI::PROTOCOL_STATUS_OVERLOADED - The server is overloaded (automatically handled by this class when limits are set).
* FastCGI::PROTOCOL_STATUS_UNKNOWN_ROLE - A role outside of allowed ranges (automatically handled by this class) or a role not supported by the server was requested.

FastCGI::SendParams($requestid, $params)
----------------------------------------

Access:  public, client mode

Parameters:

* $requestid - An integer containing the ID of a request.
* $params - An array containing key-value pairs for the environment variables equivalent of the FastCGI request.

Returns:  A standard array of information.

This function sends environment variables to the FastCGI server for the specified request.  Pass an empty array to finalize the params portion of a FastCGI request.

FastCGI::IsStdinOpen($requestid)
--------------------------------

Access:  public

Parameters:

* $requestid - An integer containing the ID of a request.

Returns:  A boolean indicating whether or not stdin is open.

This function returns the open status of stdin for the specified FastCGI request.

FastCGI::SendStdin($requestid, $data)
-------------------------------------

Access:  public, client mode

Parameters:

* $requestid - An integer containing the ID of a request.
* $data - A string containing the data to send.

Returns:  A standard array of information.

This function sends stdin data to the FastCGI server for the specified request.  Pass an empty string for $data to finalize the stdin portion of a FastCGI request.

FastCGI::IsStdoutOpen($requestid)
---------------------------------

Access:  public

Parameters:

* $requestid - An integer containing the ID of a request.

Returns:  A boolean indicating whether or not stdout is open.

This function returns the open status of stdout for the specified FastCGI request.

FastCGI::SendStdout($requestid, $data)
--------------------------------------

Access:  public, server mode

Parameters:

* $requestid - An integer containing the ID of a request.
* $data - A string containing the data to send.

Returns:  A standard array of information.

This function sends stdout data to the FastCGI client for the specified request.  Pass an empty string for $data to finalize the stdout portion of a FastCGI request.

FastCGI::IsStderrOpen($requestid)
---------------------------------

Access:  public

Parameters:

* $requestid - An integer containing the ID of a request.

Returns:  A boolean indicating whether or not stderr is open.

This function returns the open status of stderr for the specified FastCGI request.

FastCGI::SendStderr($requestid, $data)
--------------------------------------

Access:  public, server mode

Parameters:

* $requestid - An integer containing the ID of a request.
* $data - A string containing the data to send.

Returns:  A standard array of information.

This function sends stderr data to the FastCGI client for the specified request.  Pass an empty string for $data to finalize the stderr portion of a FastCGI request.

FastCGI::IsDataOpen($requestid)
-------------------------------

Access:  public, client mode

Parameters:

* $requestid - An integer containing the ID of a request.

Returns:  A boolean indicating whether or not the data channel is open.

This function returns the open status of the data channel for the specified FastCGI request.  Note that the data channel is only open for the FastCGI filter role.

FastCGI::SendData($requestid, $data)
------------------------------------

Access:  public, client mode

Parameters:

* $requestid - An integer containing the ID of a request.
* $data - A string containing the data to send.

Returns:  A standard array of information.

This function sends data channel data to the FastCGI server for the specified request.  Pass an empty string for $data to finalize the data channel portion of a FastCGI request.  Note that the data channel is only open for the FastCGI filter role.

FastCGI::NextReadyRequest($wait = false)
----------------------------------------

Access:  public

Parameters:

* $wait - A boolean indicating whether or not to wait indefinitely for a ready request (Default is false).

Returns:  A standard array of information.

This function gets the next ready request.  Returns immediately unless $wait is not false.

FastCGI::RemoveRequest($requestid)
----------------------------------

Access:  public

Parameters:

* $requestid - An integer containing the ID of a request.

Returns:  Nothing.

This function forcibly removes the request from the queue.  Should only be called after the application has finished with the request (i.e. after the request is marked done).

FastCGI::NeedsWrite()
---------------------

Access:  public

Parameters:  None.

Returns:  A boolean of true if there is data ready to be written to the socket, false otherwise.

This function moves up to 65KB of data for writing from the records queue into the write buffer and returns whether or not there is data for writing.  This function can be useful in conjunction with GetStream() when handling multiple streams.

FastCGI::GetStream()
--------------------

Access:  public

Parameters:  None.

Returns:  The underlying socket stream (PHP resource) if connected, a boolean of false otherwise.

This function is considered "dangerous" as it allows direct access to the underlying data stream.  However, as long as it is only used with functions like PHP stream_select() and Wait()/ProcessQueues() is used to do actual management, it should be safe enough.  This function is intended to be used where there are multiple handles being waited on (e.g. handling multiple connections to multiple FastCGI servers).

FastCGI::Wait($timeout = false)
-------------------------------

Access:  public

Parameters:

* $timeout - A boolean of false or an integer containing the number of seconds to wait for an event to trigger such as a write operation to complete (Default is false).

Returns:  A standard array of information.

This function waits until an event occurs such as data arriving or the write end clearing so more data can be sent.  Then FastCGI::ProcessQueues() is called.  This function is the core of the FastCGI class and should be called frequently (e.g. a while loop).

FastCGI::ProcessQueues($read, $write, $readsize = 65536)
--------------------------------------------------------

Access:  _internal_

Parameters:

* $read - A boolean that indicates that data is available to be read.
* $write - A boolean that indicates that the connection is ready for more data to be written to it.
* $readsize - An positive integer specifying the maximum number of bytes to read in at once (Default is 65536).

Returns:  A standard array of information.

This mostly internal function handles post-Wait() queue processing.  It is declared public so that FastCGIServer can call it to handle the queues for an individual client.

FastCGI::ProcessReadData()
--------------------------

Access:  protected

Parameters:  None.

Returns:  A standard array of information.

This internal function extracts complete records that have been read in and manages the associated request for a later NextReadyRequest() call to handle.  Management records are automatically handled here.

FastCGI::ReadRecord()
---------------------

Access:  protected

Parameters:  None.

Returns:  A boolean of false if there isn't enough data for a complete record otherwise an array containing the results of the call.

This internal function attempts to read in the next complete record from the data that has been read in so far from the underlying socket.  This function does not do any validation beyong extracting the record.

FastCGI::ParseNameValues($data)
-------------------------------

Access:  protected static

Parameters:

* $data - A string containing a completed FastCGI name-value stream.

Returns:  An array containing the extracted name-value pairs.

This internal static function extracts name-value pairs from a complete FastCGI name-value stream.  Used by the PARAMS, GET_VALUES, and GET_VALUES_RESULT record types.

FastCGI::CreateNameValueChunks($namevalues)
-------------------------------------------

Access:  protected static

Parameters:

* $namevalues - An array containing name-value pairs.

Returns:  An array of strings containing chunks of encoded name-value pairs.

This internal static function generates name-value pairs suitable for transport over FastCGI.  If a chunk exceeds 65,535 bytes, then a new chunk is started and picks up where the previous chunk left off.  Used for the PARAMS, GET_VALUES, and GET_VALUES_RESULT record types.

FastCGI::FillWriteData()
------------------------

Access:  protected

Parameters:  None.

Returns:  Nothing.

This internal function moves up to 65KB of data for writing from the records queue into the write buffer.  The write buffer is intentionally kept small to avoid memory thrashing.

FastCGI::WriteRecord($type, $requestid, $content)
-------------------------------------------------

Access:  protected

Parameters:

* $type - An integer containing the record type (must fit in one byte).
* $requestid - An integer containing the ID of a request.
* $content - A string containing the content to send (maximum length of 65,535 bytes).

Returns:  Nothing.

This internal function generates the binary record data and places into the write records queue.  FillWriteData() eventually moves the record into the write buffer.

FastCGI::FCGITranslate($format, ...)
------------------------------------

Access:  _internal_ static

Parameters:

* $format - A string containing valid sprintf() format specifiers.

Returns:  A string containing a translation.

This internal static function takes input strings and translates them from English to some other language if CS_TRANSLATE_FUNC is defined to be a valid PHP function name.

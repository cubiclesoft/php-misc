LibEvGenericServer Class:  'support/generic_server_libev.php'
=============================================================

This class overrides specific functions of GenericServer to add PECL ev and libev support.  This class is designed to require only minor code changes in order to support PECL ev.

For example usage, see the [PHP-based Software License Server](https://github.com/cubiclesoft/php-license-server).

LibEvGenericServer::IsSupported()
---------------------------------

Access:  public static

Parameters:  None.

Returns:  A boolean of true if the PECL ev extension is available and will function on the platform, false otherwise.

This static function returns whether or not the class will work.  Since libev doesn't use I/O Completion Ports (IOCP) on Windows, the function always returns false for PHP on Windows.

LibEvGenericServer::Reset()
---------------------------

Access:  public

Parameters:  None.

Returns:  Nothing.

This function resets a class instance to the default state.  Note that LibEvGenericServer::Stop() should be called first as this simply resets all internal class variables.

LibEvGenericServer::Internal_LibEvHandleEvent($watcher, $revents)
-----------------------------------------------------------------

Access:  _internal_

Parameters:

* $watcher - An object containing a PECL ev watcher.
* $revents - An integer containing a set of watcher event flags.

Returns:  Nothing.

This internal callback function handles PECL ev socket events that are fired.

LibEvGenericServer::Start($host, $port, $sslopts = false)
---------------------------------------------------------

Access:  public

Parameters:

* $host - A string containing the host IP to bind to.
* $port - A port number to bind to.  On some systems, ports under 1024 are restricted to root/admin level access only.
* $sslopts - An array of PHP SSL context options to use SSL mode on the socket or a boolean of false (Default is false).

Returns:  An array containing the results of the call.

This function attempts to bind to the specified TCP/IP host and port.  Identical to GenericServer::Start() but also registers for read events on the server socket handle to accept connections.

LibEvGenericServer::Stop()
--------------------------

Access:  public

Parameters:  None.

Returns:  Nothing.

This function stops the server after disconnecting all clients and resets some internal variables in case the class instance is reused.  Also stops all registered event watchers.

LibEvGenericServer::InitNewClient($fp)
--------------------------------------

Access:  _internal_

Parameters:

* $fp - A stream resource or a boolean of false.

Returns:  The new stdClass instance.

This function creates a new client object.  Identical to GenericServer::InitNewClient() but also registers for read events on the socket handle.

LibEvGenericServer::Internal_LibEvTimeout($watcher, $revents)
-------------------------------------------------------------

Access:  _internal_

Parameters:

* $watcher - An object containing a PECL ev watcher.
* $revents - An integer containing a set of watcher event flags.

Returns:  Nothing.

This internal callback function handles PECL ev timer events that are fired.

LibEvGenericServer::Wait($timeout = false)
------------------------------------------

Access:  public

Parameters:

* $timeout - A boolean of false or an integer containing the number of seconds to wait for an event to trigger such as a write operation to complete (default is false).

Returns:  An array containing the results of the call.

This function is the core of the LibEvGenericServer class and should be called frequently (e.g. a while loop).  It runs the libev event loop one time and processes clients that are returned.

LibEvGenericServer::UpdateClientState($id)
------------------------------------------

Access:  public

Parameters:

* $id - An integer containing the ID of the client to update the internal state for.

Returns:  Nothing.

This function updates the watcher for the client for read/write handling during the next Wait() operation.  It is recommended that this function be called after appending data to $client->writedata.

LibEvGenericServer::RemoveClient($id)
-------------------------------------

Access:  public

Parameters:

* $id - An integer containing the ID of the client to retrieve.

Returns:  Nothing.

This function terminates a specified client by ID.  Identical to GenericServer::RemoveClient() and also stops the PECL ev watcher associated with the client.

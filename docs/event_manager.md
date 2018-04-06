EventManager Class:  'support/event_manager.php'
================================================

This class can form the basis of a plugin/module system in an application.  Plugins register for specific events (or global events) and the application fires those events at the appropriate times during the execution cycle.  Plugins allow for applications to be modified without affecting the core of the product.

The idea behind plugins is to allow a software product to be updated without necessarily having to worry about plugins breaking the software.  That, of course, doesn't always happen but without plugin architecture there is no way to easily modify a piece of software without badly breaking the upgrade path.  Software upgrades patch security vulnerabilities, introduce new features, and fix various bugs.

Example application usage:

```php
<?php
	require_once "support/event_manager.php";

	$em = new EventManager();

	$path = __DIR__ . "/plugins";
	$dir = opendir($path);
	if ($dir)
	{
		while (($file = readdir($dir)) !== false)
		{
			if (substr($file, -4) === ".php")  require_once $path . "/" . $file;
		}
	}

	$em->Fire("plugins_loaded", array());

	// Let plugins modify the array.
	$modifyme = array();
	$em->Fire("modify_me", array(&$modifyme));

	var_dump($modifyme);
?>
```

Example plugin:

```php
<?php
	// My cool plugin.

	class MyCoolPlugin
	{
		public static function ProcessModifyMe(&$result)
		{
			$result[] = __CLASS__ . " | " . __LINE__;
		}

		public function ProcessModifyMe2(&$result)
		{
			$result[] = __CLASS__ . " | " . __LINE__;
		}
	}

	if (class_exists("EventManager", false) && isset($em))
	{
		// For simple plugins, use static functions.
		$em->Register("modify_me", "MyCoolPlugin::ProcessModifyMe");

		// For complex plugins, use an object.
		$plugin = new MyCoolPlugin();
		$em->Register("modify_me", $plugin, "ProcessModifyMe2");
	}
?>
```

EventManager::__construct()
---------------------------

Access:  public

Parameters:  None.

Returns:  Nothing.

This function initializes the class.

EventManager::Register($eventname, $objorfuncname, $funcname = false)
---------------------------------------------------------------------

Access:  public

Parameters:

* $eventname - A string containing an event name to register the callback for.
* $objorfuncname - An instantiated object, a boolean of false, or a string containing a function name.
* $funcname - A string containing the function name to call in an instantiated object (Default is false).

Returns:  An integer containing the ID of the registered event.  Can be used to unregister the callback later.

This function registers to listen for an event.  For simple plugins, static functions may be all that is required.  For complex plugins, one or more objects may be instantiated to enable better tracking of information across multiple calls.

When the event name is the empty string (""), any fired event will call the function.  This can be useful for implementing generic plugins that handle application performance analysis or track feature usage.

There are no priorities in EventManager other than first-come, first-served.

EventManager::Unregister($eventname, $id)
-----------------------------------------

Access:  public

Parameters:

* $eventname - A string containing an event name to register the callback for.
* $id - An integer containing the ID from the call to Register().

Returns:  Nothing.

This function unregisters a callback by event name and ID.  Both parameters are required due to how events are tracked in the system.

EventManager::GetAllUsedCounts()
--------------------------------

Access:  public

Parameters:  None.

Returns:  Nothing.

This function returns all registered events and used counts for those events.  Useful for tracking plugin execution paths and frequency.

EventManager::GetUsedCount($eventname)
--------------------------------------

Access:  public

Parameters:  None.

Returns:  Nothing.

This function returns the used count for a specific event.  Useful for tracking plugin execution paths and frequency.

EventManager::Fire($eventname, $options)
----------------------------------------

Access:  public

Parameters:

* $eventname - A string containing an event name to register the callback for.
* $options - An array containing options to pass for the specified event.

Returns:  An array of gathered results from all executed event handlers.

This function fires the specified event as well as global events, passing along the contents of the `$options` array to each handler.

The used count is incremented for each event fired where at least one valid registered callback exists.

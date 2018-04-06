Miscellaneous CubicleSoft PHP Classes
=====================================

Miscellaneous, lonely PHP classes that don't already have a home in a CubicleSoft library but want to be free and open source and loved.  MIT or LGPL, your choice.

Classes
-------

* CalendarEvent - Powerful scheduling class.  Feed in a cron line, get back the next timestamp of when something should trigger.
* CLI - Static functions in a class to extract command-line options, parse user input on the command-line, and log messages to the console.  Do you really need a separate logging library?  I don't.
* EventManager - Register to listen for events whenever the application fires them.  Can be the basis of a plugin/module system.
* IPAddr - Static functions in a class for processing IPv4 and IPv6 addresses into a uniform format.
* Request - Static functions in a class for doing basic, common, but missing request initialization handling.  Common initialization routines for CubicleSoft applications.
* Str - Static functions in a class for doing basic, common, but missing string manipulation.  Common initialization routines for CubicleSoft applications.  Some minor carryover from extremely old C++ libraries.
* StringBitStream - Parse data stored in a bit stream such as Flash (SWF) files.
* UTF8 - Flexible UTF-8 string manipulation static functions in a class.  CubicleSoft was doing Unicode and UTF-8 long before Unicode and UTF-8 were cool.

How To Use
----------

See the 'docs' directory for official documentation and example usage.

The 'test.php' file also contains example usage patterns.  Run it via the command-line like this:

````php test.php -e=something -f -v -v -v N Y````

The 'test_request.php' file contains example usage patterns for the `Request` class.  Run it via a web server request like:

````http://localhost/path/to/test_request.php?action=test&id=5````

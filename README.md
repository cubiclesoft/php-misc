Miscellaneous CubicleSoft PHP Classes
=====================================

Miscellaneous, lonely PHP classes that don't already have a home in a CubicleSoft library but want to be free and open source and loved.  MIT or LGPL, your choice.

[![Donate](https://cubiclesoft.com/res/donate-shield.png)](https://cubiclesoft.com/donate/) [![Discord](https://img.shields.io/discord/777282089980526602?label=chat&logo=discord)](https://cubiclesoft.com/product-support/github/)

Classes
-------

* AppleICNS - Create and parse Apple icon (.icns) files.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/apple_icns.md)
* ArrayUtils - Implements missing functions for associative arrays.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/array_utils.md)
* CalendarEvent - Powerful scheduling class.  Feed in a cron line, get back the next timestamp of when something should trigger.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/calendar_event.md)
* CLI - Static functions in a class to extract command-line options, parse user input on the command-line, and log messages to the console.  Do you really need a separate logging library?  I don't.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/cli.md)
* ColorTools - Static functions in a class to convert RGB to other color spaces and calculate readable foreground text colors for any background color.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/color_tools.md)
* DirHelper - Static functions in a class for simplifying common file system tasks regarding directories, including recursive copy, delete, and permissions changes when building installers, live demos, and testing tools.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/dir_helper.md)
* EventManager - Register to listen for events whenever the application fires them.  Can be the basis of a plugin/module system.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/event_manager.md)
* FastCGI - Implements a FactCGI client.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/fastcgi.md)
* GenericServer - Implements a generic TCP/IP server.  Can be used for creating custom protocols.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/generic_server.md)
* IPAddr - Static functions in a class for processing IPv4 and IPv6 addresses into a uniform format.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/ipaddr.md)
* LineDiff - Static functions in a class for generating line-by-line diffs.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/line_diff.md)
* NaturalLanguage - Static functions in a class for dynamically generating content based on data inputs and rulesets via PHP arrays.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/natural_language.md)
* PHPMinifier - Static functions in a class for minifying PHP code while still generally maintaining readability.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/php_minifier.md)
* ProcessHelper - Static functions in a class for starting and terminating non-blocking processes across all platforms.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/process_helper.md)
* Request - Static functions in a class for doing basic, common, but missing request initialization handling.  Common initialization routines for CubicleSoft applications.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/request.md)
* Str - Static functions in a class for doing basic, common, but missing string manipulation.  Common initialization routines for CubicleSoft applications.  Some minor carryover from extremely old C++ libraries.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/str_basics.md)
* StringBitStream - Parse data stored in a bit stream such as Flash (SWF) files.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/bits.md)
* UTF8 - Flexible UTF-8 string manipulation static functions in a class.  CubicleSoft was doing Unicode and UTF-8 long before Unicode and UTF-8 were cool.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/utf8.md)
* UTFUtils - Convert between various Unicode Transformation Formats (UTF-8, UTF-16, UTF-32).  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/utf_utils.md)
* XTerm - Static functions in a class for emitting XTerm-compatible escape codes to alter terminal behavior.  Mostly for changing font styles and colors but also supports most escape codes with easier to comprehend functions.  Many features also work with the Command Prompt in Windows 10 and later.  [Documentation](https://github.com/cubiclesoft/php-misc/blob/master/docs/xterm.md)

How To Use
----------

See the 'docs' directory for official documentation and example usage.

The 'test.php' file also contains example usage patterns.  Run it via the command-line like this:

````php test.php -e something -f -v -v -v N Y````

The 'test_request.php' file contains example usage patterns for the `Request` class.  Run it via a web server request like:

````http://localhost/path/to/test_request.php?action=test&id=5````

The 'test_colors.php' file performs a variety of color tests.  Requires a XTerm-compatible terminal.  Command Prompt in Windows 10 and later works too!  Run it via the command-line like this:

```php test_colors.php```

PHPMinifier Class:  'support/php_minifier.php'
==============================================

The PHPMinifier class minifies a PHP file by removing comments and rewriting whitespace while leaving the code's readability intact.  It also wraps all code in a PHP namespace (even if it is just the global namespace) to aid in concatenating several files together.  Uses PHP's built-in tokenizer to accomplish the task, which makes it fairly future-proof.

PHPMinifier::DumpTokens($tokens)
--------------------------------

Access:  public static

Parameters:

* $tokens - An array containing the results of a PHP `token_get_all()` call.

Returns:  Nothing.

This static function dumps out the contents of each token as PHP sees the code onto its own line of output.  Only intended to be used with small subsets of data as the output can get unwieldy with even a few hundred tokens.

PHPMinifier::GetClassAliasHandlerStart($prefix)
-----------------------------------------------

Access:  public static

Parameters:

* $prefix - A string containing a prefix to use.

Returns:  A string containing an empty namespace-wrapped PHP function declaration.

This static function returns a custom `class_alias` handler that registers class aliases for later.  Useful for when an autoloader does things like declare class aliases.  Used by CubicleSoft PHP Decomposer.

PHPMinifier::GetClassAliasHandlerEnd($prefix)
---------------------------------------------

Access:  public static

Parameters:

* $prefix - A string containing a prefix to use.

Returns:  A string containing an empty namespace-wrapped PHP function declaration.

This static function returns a custom `class_alias` handler and creates the registered class aliases from an earlier point.  Useful for when an autoloader does things like declare class aliases.  Used by CubicleSoft PHP Decomposer.

PHPMinifier::Minify($filename, $data, $options = array())
---------------------------------------------------------

Access:  public static

Parameters:

* $filename - A string containing the filename that is the source of `$data`.  Used for errors.
* $data - A string contining the contents of `$filename`.
* $options - An array containing options that affect the output (Default is array()).

Returns:  A standard array of information.

This static function minifies the PHP code stored in `$data` according to the supplied options.  The default behavior is to strip comments and rewrite whitespace to use tabs instead of spaces.  The reductions in file size can be rather significant.

The $options array accepts these options:

* require_namespace - A boolean that indicates whether or not the code must have a namespace and to fail if it doesn't have one (Default is false).
* remove_comments - A boolean that indicates whether or not to remove comments from the code (Default is true).
* remove_declare - A boolean that indicates whether or not to remove declare statements from the start of the code (Default is false).
* convert_whitespace - A boolean that indicates whether or not to convert whitespace to tabs (Default is true).
* check_dir_functions - A boolean that indicates whether or not to check the code for PHP directory scanning functions and return warnings (Default is false).  Used by CubicleSoft PHP Decomposer.
* replace_class_alias - A string containing a prefix to use to replace `class_alias` with `PREFIX___class_alias` or a boolean of false (Default is false).  Used by CubicleSoft PHP Decomposer.
* wrap_includes - A boolean that indicates whether or not to wrap includes (`require_once`, `require`, `include_once`, `include`) with a `file_exists` check (Default is false).  Used by CubicleSoft PHP Decomposer.
* return_tokens - A boolean that indicates whether to return the final data as tokens or a string (Default is false).

PHPMinifier::MinifyFiles($srcdir, $destdir, $recurse = true, $options = array())
--------------------------------------------------------------------------------

Access:  public static

Parameters:

* $srcdir - A string containing the source directory to clone and minify.
* $destdir - A string containing the destination directory.
* $recurse - A boolean indicating whether or not to recursively traverse the source directory (Default is true).
* $options - An array containing options that affect the output of PHP files (Default is array()).

Returns:  Nothing.

This static function copies all files in a source directory to a destination directory, minifying all PHP files in the process.

The $options array accepts these options:

* file_exts - An array of key-value pairs that defines what file extensions qualify as a PHP file (Default is `array("php" => true)`).
* require_namespace - A boolean that indicates whether or not the code must have a namespace and to fail if it doesn't have one (Default is false).
* remove_comments - A boolean that indicates whether or not to remove comments from the code (Default is true).
* convert_whitespace - A boolean that indicates whether or not to convert whitespace to tabs (Default is true).
* wrap_includes - A boolean that indicates whether or not to wrap includes (`require_once`, `require`, `include_once`, `include`) with a `file_exists` check (Default is false).  Used by CubicleSoft PHP Decomposer.

PHPMinifier::MinifyFiles_Internal($srcdir, $destdir, $recurse = true, $options = array())
-----------------------------------------------------------------------------------------

Access:  private static

Parameters:

* $srcdir - A string containing the source directory to clone and minify.
* $destdir - A string containing the destination directory.
* $recurse - A boolean indicating whether or not to recursively traverse the source directory (Default is true).
* $options - An array containing options that affect the output of PHP files (Default is array()).

Returns:  Nothing.

This internal static function is called by `MinifyFiles()`.

PHPMinifier::PMTranslate($format, ...)
--------------------------------------

Access:  _internal_ static

Parameters:

* $format - A string containing valid sprintf() format specifiers.

Returns:  A string containing a translation.

This internal static function takes input strings and translates them from English to some other language if CS_TRANSLATE_FUNC is defined to be a valid PHP function name.

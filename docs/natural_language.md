NaturalLanguage Class:  'support/natural_language.php'
======================================================

The NaturalLanguage class dynamically generates content based on data inputs and rulesets via PHP arrays.  It also includes a handy statement/conditional tokenizer and processor.

Example usage:

```php
	require_once "support/natural_language.php";

	$data = array(
		"test var" => 7,
		"x" => "7",
		"cool_beans" => "Cool beans!",
		"first_name" => "John",
		"last_name" => "Smith",
		"last_profile_date" => "2019-03-18"
	);

	$data["last_profile_days"] = (new DateTime())->diff(new DateTime($data["last_profile_date"]))->days;

	$rules = array(
		"" => array(
			"type" => "if",
			"matches" => 1,
			"rules" => array(
				array(
					"cond" => "[[test var]] * 2 < 16 && (x + 2 * 3 < 14 && cool_beans == 'Cool beans!')",
					"output" => array(
						"Hello ", "@greeting_name", " ", "[[last_name]]", "!  ", "@special_message_intro", "\n\n",
						"@special_message", "\n\n",
						"You last updated your profile on ", "@profile_date", ".", "@old_profile_check"
					)
				),
				array("output" => array("Hello.  There is no special message available."))
			)
		),
		"greeting_name" => array(
			"type" => "data",
			"key" => "first_name",
			"case" => "upper"
		),
		"special_message_intro" => array(
			"type" => "if",
			"randomize" => true,
			"matches" => 1,
			"rules" => array(
				array("output" => "Your special message is:"),
				array("output" => "Your special message today:"),
				array("output" => "The super secret message is:"),
			)
		),
		"special_message" => array(
			"type" => "data",
			"key" => "cool_beans",
			"case" => "first"
		),
		"profile_date" => array(
			"type" => "data",
			"key" => "last_profile_date",
			"format" => "date",
			"date" => "D, M j, Y"
		),
		"old_profile_check" => array(
			"type" => "if",
			"matches" => 1,
			"rules" => array(
				array(
					"cond" => "last_profile_days > 365",
					"output" => array("  ", "@old_profile")
				),
				array(
					"cond" => "last_profile_days > 365 * 2",
					"output" => array("  ", "@very_old_profile")
				),
			)
		),
		"old_profile" => array(
			"type" => "if",
			"randomize" => true,
			"matches" => 1,
			"rules" => array(
				array("output" => "Your profile hasn't been updated in a while."),
				array("output" => "You might consider updating your profile for best results."),
				array("output" => "Updating your profile every so often is a good idea."),
			)
		),
		"very_old_profile" => array(
			"type" => "if",
			"randomize" => true,
			"matches" => 1,
			"rules" => array(
				array("output" => "Hey, we've noticed you haven't updated your profile for a very long time."),
				array("output" => "It's at least a good idea to change your password on your account."),
				array("output" => "Really old accounts are more easily hacked.  Updating your account password is recommended."),
			)
		),
		"orphaned_rule" => array(
			"type" => "if",
			"randomize" => true,
			"matches" => 1,
			"rules" => array(
				array("output" => "I'm unreferenced by all the other rules."),
				array("output" => "No, really.  I'm not gonna show up."),
			)
		),
	);

	$result2 = NaturalLanguage::ValidateRules($data, $rules);
	if (!$result2["success"])
	{
		var_dump($result2);

		exit();
	}

	echo $result2["value"] . "\n\n";

	$result2 = NaturalLanguage::Generate($data, $rules);
	if (!$result2["success"])
	{
		var_dump($result2);

		exit();
	}

	echo $result2["value"] . "\n\n";
```

Example output:

```
Hello JOHN Smith!  Your special message today:

Cool beans!

You last updated your profile on Mon, Mar 18, 2019.  Updating your profile every so often is a good idea.
```

NaturalLanguage::CalculateUniqueStates(&$rules)
-----------------------------------------------

Access:  public static

Parameters:

* $rules - An array containing a set of rules.

Returns:  An integer containing the number of unique rule states.

This static function calculates the total number of unique content states that can be emitted by the rules.  It also factors in 'if' rules that select multiple, random matches (e.g. where paragraph A and B might be output as B then A).  More variations in the rules increase the number of unique possibilities.

NaturalLanguage::Generate($data, &$rules, $options = array())
-------------------------------------------------------------

Access:  public static

Parameters:

* $data - An array containing string key-value pairs to be used with the rules.
* $rules - An array containing a set of rules.
* $options - An array containing options (Default is array()).

Returns:  An array containing standard information.

This static function processes the input data and rules arrays to generate content based on the rules and data.  Note that this function does not sanitize inputs.  It is recommended that input data be sanitized prior to calling this function.

The $rules array consists of named key-value pairs.  Each rule can be one of two types:  "data" and "if".

The "data" rule type has the following options:

* key - A string containin the key in the data to lookup for the value.
* format - An optional string containing one of "number", "date", or "time".  Each format has additional options:
	* number:  decimals - An integer containing the number of decimal places to output (Default is 0).
	* number:  decpoint - A string containing the character to use for the decimal point (Default is ".").
	* number:  separator - A string containing the character to use for the thousands separator (Default is ",").
	* date/time:  date - A string containing the date format to use for the output.  See the PHP date() function for allowed format specifiers.
	* date/time:  gmt - A boolean indicating whether or not to output the string in GMT (Default is false).
* case - A string containing one or more comma-separated values of "lower", "upper", "words", and/or "first" to transform the case of the text (e.g. lowercase).  Case changes are applied after "format".
* replace - An array containing key-value pairs of string replacements to transform the value.  String replacements are applied after "case".

The "if" rule type has the following options:

* matches - An integer containing the maximum number of rules to match (Default is -1).
* randomize - A boolean indicating whether or not to randomize/shuffle rules before matching (Default is false).
* rules - An array containing rules to match against the input data.  Each rule has additional options:
	* cond - A string containing a conditional statement (Default is null).  When not specified, the rule always matches.
	* output - An array of strings or a string containing the output to use for when the rule matches.  A string that starts with an "@" prefix is assumed to be referencing another top-level rule.  A string that wraps a string with double brackets (e.g. `"[[variable_here]]"`) is assumed to be referencing a data item to be included as-is.  Any other string is output as-is.

Conditional statements for "if" rules can be simple or complex.  Example:

```
[[test var]] * 2 < 16 && (x + 2 * 3 < 14 && cool_beans == 'Cool beans!')
```

The example above uses the input data values for "test var", "x", and "cool_beans" and then performs some math and comparisons to determine if the output should be included.  Note that, in general, complex values should be precalculated and passed in the data array as only simple and binary math are supported.  Features such as function calls and typecasting are not supported in conditional statements at this time.

The $options array accepts these options:

* reuse_rules - An integer containing the maximum number of times a rule can be used (Default is 10).  This helps prevent infinite loops from occurring.
* max_depth - An integer containing the maximum depth to traverse into the rules (Default is 100).  This helps prevent infinite loops from occurring.
* max_process - An integer containing the maximum number of rules to process (Default is 1000).  This helps prevent infinite loops from occurring.

NaturalLanguage::ProcessRule(&$data, &$rules, $rkey, &$options)
---------------------------------------------------------------

Access:  protected static

Parameters:

* $data - An array containing string key-value pairs to be used with the rules.
* $rules - An array containing a set of rules.
* $rkey - A string containing the top-level key to use.
* $options - An array containing options.

Returns:  A standard array of information.

This internal static function processes the input data and rules arrays to generate content based on the rules and data.  This function is the primary workhorse of the Generate() function.

NaturalLanguage::ValidateRules($data, &$rules, $options = array())
------------------------------------------------------------------

Access:  public static

Parameters:

* $data - An array containing string key-value pairs to be used with the rules.
* $rules - An array containing a set of rules.
* $options - An array containing options (Default is array()).

Returns:  An array containing standard information.

This static function validates the input data and rules arrays for common conditions.  Useful for identifying issues with rules to be passed to Generate() that might not regularly show up.  Also can be used to identify all possible references to data fields and rules for finding areas that could be optimized (e.g. unreferenced data and orphaned rules).

This function is much more strict/pedantic than Generate() and will return errors for rules that will function just fine within Generate() but should probably fixed to avoid issues.

NaturalLanguage::ValidateRule(&$data, &$rules, $rkey, &$options)
----------------------------------------------------------------

Access:  protected static

Parameters:

* $data - An array containing string key-value pairs to be used with the rules.
* $rules - An array containing a set of rules.
* $rkey - A string containing the top-level key to use.
* $options - An array containing options.

Returns:  A standard array of information.

This internal static function validates the input data and specified rule.  This function is the primary workhorse of the ValidateRules() function.

NaturalLanguage::MakeConditional($tokens)
-----------------------------------------

Access:  public static

Parameters:

* $tokens - An array of tokens for a conditional statement.

Returns:  A string containing the generated conditional statement.

This static function performs the inverse of ParseConditional().  Useful for beautifying statements.

Example usage:

```php
	require_once "support/natural_language.php";

	$cond = "[[test var]]*2<16&&(x+2*3<14&&cool_beans=='Cool beans!')";
	echo $cond . "\n";

	$result = NaturalLanguage::ParseConditional($cond);
	if (!$result["success"])
	{
		var_dump($result);

		exit();
	}

	echo NaturalLanguage::MakeConditional($result["tokens"]) . "\n";
```

Example output:

```
[[test var]]*2<16&&(x+2*3<14&&cool_beans=='Cool beans!')
[[test var]] * 2 < 16 && (x + 2 * 3 < 14 && cool_beans == "Cool beans!")
```

NaturalLanguage::ParseConditional($cond, $keepspaces = false)
-------------------------------------------------------------

Access:  public static

Parameters:

* $cond - A string containing a conditional statement to parse.
* $keepspaces - A boolean that indicates whether or not to include space tokens in the output (Default is false).

Returns:  A standard array of information.

This static function tokenizes and performs lexical analysis of the input conditional statement.

Returned tokens may have the following types:

* var - A variable reference.
* val - A value that may be treated as a numeric or string.
* op - An operator.
* cond - A conditional operator.
* lop - A logical operator.
* grp_s - Group start (open parenthesis).
* grp_e - Group end (close parenthesis).
* space - A string containing whitespace characters.

NaturalLanguage::ReduceConditionalCheckStacks(&$valstack, &$parenstack, &$opstack)
----------------------------------------------------------------------------------

Access:  protected static

Parameters:

* $valstack - An array containing values/operands.
* $parenstack - An array containing groups/parenthesis.
* $opstack - An array containing operators.

Returns:  A boolean indicating whether or not the operation was successful.

This internal static function reduces the conditional check stacks for RunConditionalCheck() by processing and resolving operators in the following operator precedence order (left-to-right):

```
* / % (multiply, divide, modulus)
+ - (addition, subtraction)
<< >> (bitwise shift)
< <= > >= (less-than, greater-than, etc.)
== != (equal to, not equal to)
& (bitwise AND)
^ (bitwise XOR)
| (bitwise OR)
&& (logical AND, handled by RunConditionalCheck)
|| (logical OR, handled by RunConditionalCheck)
```

The function can fail if an unknown operator is submitted in the tokens to RunConditionalCheck().  In general, the lexical analyzer in ParseConditional() will catch most issues.

NaturalLanguage::RunConditionalCheck($tokens, &$data, $options = array())
-------------------------------------------------------------------------

Access:  public static

Parameters:

* $tokens - An array containing tokens from a previous call to ParseConditional() or a string containing a conditional statement to parse.
* $data - An array containing string key-value pairs to be used with the conditional.
* $options - An array containing options (Default is array()).

Returns:  A standard array of information.

This static function evaluates a conditional statement using the input data for 'var' token types.  Evaluation happens in a single pass.

Conditional statement precedence is processed as per ReduceConditionalCheckStacks().

NaturalLanguage::NLBTranslate($format, ...)
-------------------------------------------

Access:  _internal_ static

Parameters:

* $format - A string containing valid sprintf() format specifiers.

Returns:  A string containing a translation.

This internal static function takes input strings and translates them from English to some other language if CS_TRANSLATE_FUNC is defined to be a valid PHP function name.

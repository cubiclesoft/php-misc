IPAddr Class:  'support/ipaddr.php'
===================================

The IPAddr class transforms and matches IP addresses.  This class works primarily with IPv6 addresses but will automatically detect IPv4 addresses and patterns and act appropriately.  This class also deals with a few security vulnerabilities involving handling of IP addresses.

IPAddr::IsHostname($str)
------------------------

Access:  public static

Parameters:

* $str - A string that contains a hostname or IP address.

Returns:  A boolean of true if the string contains a hostname.

This static function helps to differentiate between hostnames and IP addresses.  This is useful to, for example, identify a string as a hostname and then translate it to an IP address with a DNS lookup.

Example usage:

```php
<?php
	require_once "support/ipaddr.php";

	$preferipv6 = false;
	$remotehost = "localhost";

	if (IPAddr::IsHostname($remotehost))
	{
		$info = ($preferipv6 ? @dns_get_record($remotehost . ".", DNS_AAAA) : false);
		if ($info === false || !count($info))  $info = @dns_get_record($remotehost . ".", DNS_A);
		if ($info === false || !count($info))  $info = @dns_get_record($remotehost . ".", DNS_ANY);

		$valid = false;

		if ($info !== false)
		{
			foreach ($info as $entry)
			{
				if ($entry["type"] === "A" || ($preferipv6 && $entry["type"] === "AAAA"))
				{
					$remoteip = IPAddr::NormalizeIP($info[0]["ip"]);

					$valid = true;

					break;
				}
			}
		}
	}
	else
	{
		$remoteip = IPAddr::NormalizeIP($remotehost);

		$valid = true;
	}

	if (!$valid)  echo "Invalid remote host specified.  Try again.\n";
	else  var_dump($remoteip);
?>
```

IPAddr::NormalizeIP($ipaddr)
----------------------------

Access:  public static

Parameters:

* $ipaddr - A string containing an IP address.

Returns:  An array containing an IPv6 address, a short version of the IPv6 address, an IPv4 address (or an empty string).

This static function takes an input IP address string and normalizes it to a full IPv6 address.  Then it takes that and creates the short IPv6 and IPv4 addresses from that.  The result is normalized output that an application can rely upon.

Example usage:

```php
<?php
	require_once "support/ipaddr.php";

	$ipaddr = IPAddr::NormalizeIP("127.0.0.1");
	var_dump($ipaddr);
?>
```

IPAddr::GetRemoteIP($proxies = array())
---------------------------------------

Access:  public static

Parameters:

* $proxies - An array containing key-value pairs that map trusted proxy IP addresses to input HTTP header types (Default is array()).

Returns:  A normalized remote IP address.

This static function takes the IP address in `$_SERVER["REMOTE_ADDR"]` and normalizes it.  If any trusted proxies are specified, any relevant "HTTP_X_FORWARDED_FOR" and "HTTP_CLIENT_IP" headers are analyzed looking for the real remote IP address, stopping at the first untrusted proxy.

Valid HTTP header types for `$proxies` are the strings "xforward" and "clientip".

Example usage:

```php
<?php
	require_once "support/ipaddr.php";

	$ipaddr = IPAddr::GetRemoteIP();
	var_dump($ipaddr);

	// Example of handling requests from a reverse proxy running on the same system.
	$proxies = array("127.0.0.1" => "xforward");
	$ipaddr = IPAddr::GetRemoteIP($proxies);
	var_dump($ipaddr);
?>
```

IPAddr::IsMatch($pattern, $ipaddr)
----------------------------------

Access:  public static

Parameters:

* $pattern - A string containing a valid IPv6 or IPv4 pattern to match against.
* $ipaddr - A string containing a valid IP address or an array containing a normalized IP address.

Returns:  A boolean of true if the IP address matches the pattern, false otherwise.

This static function normalizes the IP address if it isn't already normalized and then attempts to match the input pattern against the address.  Useful for filtering incoming IP addresses against a whitelist based on a pattern.

The pattern must contain the correct number of ':' (colon) or '.' (dot) characters corresponding to an IPv6 or IPv4 address respectively.

Special characters may be used within each segment of the pattern:

* '*' (asterisk) - Match any.
* '-' (hyphen) - Match range. (e.g. '12-15' matches 12, 13, 14, and 15).
* ',' (comma) - Match alternate. (e.g. '12,15' matches 12 and 15).

IPv6 segments can also contain IPv4-style notation, but it looks a little weird.  For example, '*:*:*:*:*:*:127.0:0.1' (notice the colon).

Example usage:

```php
<?php
	require_once "support/ipaddr.php";

	$pattern = "64.18.0-15.0-255";
	var_dump(IPAddr::IsMatch($pattern, "127.0.0.1"));
	var_dump(IPAddr::IsMatch($pattern, "64.18.5.2"));
	var_dump(IPAddr::IsMatch($pattern, "::ffff:4012:502"));  // "64.18.5.2" in IPv6 notation.
?>
```

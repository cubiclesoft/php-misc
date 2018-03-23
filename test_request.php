<?php
	// CubicleSoft miscellaneous web request dependent classes test.
	// (C) 2018 CubicleSoft.  All Rights Reserved.

	// Temporary root.
	$rootpath = str_replace("\\", "/", dirname(__FILE__));

	require_once $rootpath . "/support/request.php";

?>
<html>
<head><title>Test</title></head>
<body>
<h4>$_REQUEST keys before:</h4>
<ul>
<?php
	foreach ($_REQUEST as $key => $val)
	{
		echo htmlspecialchars($key) . "<br>";
	}
?>
</ul>

<h4>$_REQUEST keys after:</h4>
<ul>
<?php
	Request::Normalize();

	foreach ($_REQUEST as $key => $val)
	{
		echo "<li>" . htmlspecialchars($key) . "</li>";
	}
?>
</ul>

<?php
	if (Request::IsSSL())  echo "<h4>Request is:</h4><div style=\"margin-left: 2.5em;\">HTTPS</div>";
	else  echo "<h4>Request is:</h4><div style=\"margin-left: 2.5em;\">HTTP</div>";
?>

<h4>Full request URL bases:</h4>
<ul>
	<li><b>Default:</b>  <?=htmlspecialchars(Request::GetFullURLBase())?></li>
	<li><b>HTTP:</b>  <?=htmlspecialchars(Request::GetFullURLBase("http"))?></li>
	<li><b>HTTPS:</b>  <?=htmlspecialchars(Request::GetFullURLBase("hTtPs"))?></li>
</ul>
</body>
</html>
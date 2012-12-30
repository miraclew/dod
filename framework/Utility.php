<?php
function starts_with($haystack, $needle)
{
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

function ends_with($haystack, $needle)
{
	$length = strlen($needle);
	if ($length == 0) {
		return true;
	}

	$start  = $length * -1; //negative
	return (substr($haystack, $start) === $needle);
}

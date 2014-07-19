<?php
function StartWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}
// ---------------------------------------------------------------------------------------------- //
function EndWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}
// ---------------------------------------------------------------------------------------------- //
/**
 * case-insensitive startswith
 */
function IStartWith($haystack, $needle)
{
    return $needle === "" || stripos($haystack, $needle) === 0;
}
// ---------------------------------------------------------------------------------------------- //
/**
 * case-insensitive endswith
 */
function IEndWith($haystack, $needle)
{
    return $needle === "" || strcasecmp(substr($haystack, -strlen($needle)), $needle) === 0;
}
// ---------------------------------------------------------------------------------------------- //
function Empty2StrEmpty($strNeeded){
	return empty($strNeeded) ? '' : $strNeeded;
}
// ---------------------------------------------------------------------------------------------- //

<?php

/*
 * SimpleModal Contact Form
 * http://www.ericmmartin.com/projects/simplemodal/
 * http://code.google.com/p/simplemodal/
 *
 * Copyright (c) 2009 Eric Martin - http://ericmmartin.com
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Revision: $Id: contact-dist.php 204 2009-06-09 22:43:28Z emartin24 $
 *
 */

date_default_timezone_set('UTC');

function smcf_token($s) {
	return md5("smcf-" . $s . date("WY"));
}

// Validate and send email
function smcf_send($name, $email, $subject, $message, $cc) {
	global $to, $extra;

        // Filter and validate fields
        $name = smcf_filter($name);
        $subject = smcf_filter($subject);
        $email = smcf_filter($email);
        if (!smcf_validate_email($email)) {
                $subject .= " - invalid email";
                $message .= "\n\nBad email: $email";
                $email = $to;
                $cc = 0; // do not CC "sender"
        }

        // Add additional info to the message
        if ($extra["ip"]) {
                        $message .= "\n\n-----------------------\nIP: " . $_SERVER["REMOTE_ADDR"];
        }
        if ($extra["user_agent"]) {
                $message .= "\nUSER AGENT: " . $_SERVER["HTTP_USER_AGENT"];
        }

	// smtp abilities
	require_once('./mail.class.php');

	$mailer = new Mail();
	$mailer->protocol = 'smtp';

	$mailer->setTo($to);
	$mailer->setFrom($email);
	$mailer->setSender($name);
	$mailer->setSubject($subject);
	$mailer->setText($message);

	$mailer->send();
}

// Remove any un-safe values to prevent email injection
function smcf_filter($value) {
	$pattern = array("/\n/","/\r/","/content-type:/i","/to:/i", "/from:/i", "/cc:/i");
	$value = preg_replace($pattern, "", $value);
	return $value;
}

// Validate email address format in case client-side validation "fails"
function smcf_validate_email($email) {
	$at = strrpos($email, "@");

	// Make sure the at (@) sybmol exists and  
	// it is not the first or last character
	if ($at && ($at < 1 || ($at + 1) == strlen($email)))
		return false;

	// Make sure there aren't multiple periods together
	if (preg_match("/(\.{2,})/", $email))
		return false;

	// Break up the local and domain portions
	$local = substr($email, 0, $at);
	$domain = substr($email, $at + 1);


	// Check lengths
	$locLen = strlen($local);
	$domLen = strlen($domain);
	if ($locLen < 1 || $locLen > 64 || $domLen < 4 || $domLen > 255)
		return false;

	// Make sure local and domain don't start with or end with a period
	if (preg_match("/(^\.|\.$)/", $local) || preg_match("/(^\.|\.$)/", $domain))
		return false;

	// Check for quoted-string addresses
	// Since almost anything is allowed in a quoted-string address,
	// we're just going to let them go through
	if (!preg_match('/^"(.+)"$/', $local)) {
		// It's a dot-string address...check for valid characters
		if (!preg_match('/^[-a-zA-Z0-9!#$%*\/?|^{}`~&\'+=_\.]*$/', $local))
			return false;
	}

	// Make sure domain contains only valid characters and at least one period
	if (!preg_match("/^[-a-zA-Z0-9\.]*$/", $domain) || !strpos($domain, "."))
		return false;	

	return true;
}

?>

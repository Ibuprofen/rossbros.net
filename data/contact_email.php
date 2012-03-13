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

require_once('./contact_functions.php');

// User settings
#$to = "rossbros@hotmail.com";
$to = 'tony@plenary.org';
$subject = "RossBros.net - Contact Us";

// Include extra form fields and/or submitter data?
// false = do not include
$extra = array(
	"form_subject"	=> true,
	"form_cc"		=> false,
	"ip"			=> true,
	"user_agent"	=> true,
	"contact-title" => "Send us a message"
);

require_once('./contact_process.php');
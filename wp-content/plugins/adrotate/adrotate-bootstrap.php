<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2023 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
 Name:      adrotate_get_plugin_information
 Purpose:   Grab plugin_information from transient data
-------------------------------------------------------------*/
function adrotate_get_plugin_information() {
	$result = new stdClass();
	$result->name = 'AdRotate Bootstrap';
	$result->slug = 'adrotate-bootstrap';
	$result->version = '1.0';
	$result->tested = '6.0';
	$result->requires = '4.9';
	$result->requires_php = '5.4';
	$result->download_link = 'https://ajdg.solutions/api/updates/files/adrotate-bootstrap.zip';
	$result->author = 'Arnan de Gans';
	$result->homepage = 'https://ajdg.solutions';
	$result->sections = array('description' => stripslashes('A plugin to migrate from AdRotate Banner Manager to AdRotate Professional.'));

	return $result;
}
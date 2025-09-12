<?php

/** 
 * [BEGIN_COT_EXT]
 * Hooks=input
 * [END_COT_EXT]
 */
 
/**
 * Payeer billing Plugin
 *
 * payeerbilling plugin for Cotonti 0.9.26, PHP 8.4+
 * Filename: payeerbilling.input.php
 * @package payeerbilling
 * @version 3.3.36
 * @author CMSWorks,webitproff
 * @copyright Copyright (c) CMSWorks, webitproff 2025 | https://github.com/webitproff
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$e = cot_import('e', 'G', 'ALP');
$r = cot_import('r', 'G', 'ALP');

if(($e == 'payeerbilling' || $r == 'payeerbilling') && $_SERVER['REQUEST_METHOD'] == 'POST')
{
	define('COT_NO_ANTIXSS', TRUE) ;
	$cfg['referercheck'] = false;
}
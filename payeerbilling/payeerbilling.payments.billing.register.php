<?php

/**
 * [BEGIN_COT_EXT]
 * Hooks=payments.billing.register
 * [END_COT_EXT]
 */
/**
 * Payeer billing Plugin
 *
 * payeerbilling plugin for Cotonti 0.9.26, PHP 8.4+
 * Filename: payeerbilling.payments.billing.register.php
 * @package payeerbilling
 * @version 3.3.36
 * @author CMSWorks,webitproff
 * @copyright Copyright (c) CMSWorks, webitproff 2025 | https://github.com/webitproff
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('payeerbilling', 'plug');
// Проверяем наличие ключа 'payeerbilling_note' в массиве $L перед его использованием
$cot_billings['payeer'] = array(
    'plug' => 'payeerbilling',
    'title' => $L['payeerbilling_title'] ?? '', // Если ключ отсутствует, подставляем пустую строку
    'note' => $L['payeerbilling_note'] ?? '',   // Если ключ отсутствует, подставляем пустую строку
    'icon' => $cfg['plugins_dir'] . '/payeerbilling/images/payeer.png'
);

/*$cot_billings['payeer'] = array(
	'plug' => 'payeerbilling',
	'title' => $L['payeerbilling_title'],
	'note' => $L['payeerbilling_note'],
	'icon' => $cfg['plugins_dir'] . '/payeerbilling/images/payeer.png'
);*/


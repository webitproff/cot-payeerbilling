<?php
/**
 * [BEGIN_COT_EXT]
 * Hooks=ajax
 * [END_COT_EXT]
 */
// Указываем, что плагин работает как обработчик AJAX-запросов в Cotonti

/**
 * Payeer billing Plugin
 *
 * payeerbilling plugin for Cotonti 0.9.26, PHP 8.4+
 * Filename: payeerbilling.ajax.php
 * @package payeerbilling
 * @version 3.3.36
 * @author CMSWorks,webitproff
 * @copyright Copyright (c) CMSWorks, webitproff 2025 | https://github.com/webitproff
 * @license BSD
 */
// Документируем метаданные плагина: название, версия, автор, копирайт, лицензия

// Импортируем класс PaymentDictionary для работы со статусами платежей
use cot\modules\payments\dictionaries\PaymentDictionary;
// Импортируем класс PaymentService для управления статусами платежей
use cot\modules\payments\Services\PaymentService;

// Проверяем, определена ли константа COT_CODE, иначе выдаём ошибку "Wrong URL"
defined('COT_CODE') or die('Wrong URL');

// Подключаем файл модуля payments для работы с системой платежей
require_once cot_incfile('payments', 'module');

// Проверяем, совпадает ли IP-адрес клиента с разрешёнными IP Payeer
if (!in_array($_SERVER['REMOTE_ADDR'], array('185.71.65.92', '185.71.65.189', '149.202.17.210'))) {
    // Если IP не в списке, прекращаем выполнение
    return;
}

// Проверяем наличие параметров m_operation_id и m_sign в POST-запросе
if (isset($_POST['m_operation_id']) && isset($_POST['m_sign'])) {
    // Получаем секретный ключ из конфигурации плагина
    $m_key = $cfg['plugin']['payeerbilling']['key'];
    
    // Формируем массив для генерации подписи платежа
    $arHash = array(
        // ID операции из POST-запроса
        $_POST['m_operation_id'],
        // ID платежной системы из POST-запроса
        $_POST['m_operation_ps'],
        // Дата операции из POST-запроса
        $_POST['m_operation_date'],
        // Дата оплаты из POST-запроса
        $_POST['m_operation_pay_date'],
        // ID магазина из POST-запроса
        $_POST['m_shop'],
        // ID заказа из POST-запроса
        $_POST['m_orderid'],
        // Сумма платежа из POST-запроса
        $_POST['m_amount'],
        // Валюта из POST-запроса
        $_POST['m_curr'],
        // Описание платежа из POST-запроса
        $_POST['m_desc'],
        // Статус платежа из POST-запроса
        $_POST['m_status']
    );
    
    // Проверяем, передан ли параметр m_params в POST-запросе
    if (isset($_POST['m_params'])) {
        // Добавляем m_params в массив подписи, если он существует
        $arHash[] = $_POST['m_params'];
    }
    
    // Добавляем секретный ключ в массив подписи
    $arHash[] = $m_key;
    
    // Генерируем подпись SHA-256, объединяя элементы массива через двоеточие
    $sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
    
    // Проверяем, совпадает ли подпись из POST и статус 'success'
    if ($_POST['m_sign'] == $sign_hash && $_POST['m_status'] == 'success') {
        // Обновляем статус платежа на 'paid' через PaymentService
        if (PaymentService::getInstance()->setStatus($_POST['m_orderid'], PaymentDictionary::STATUS_PAID, 'payeer')) {
            // Очищаем буфер вывода для чистого ответа
            ob_end_clean();
            // Выводим ID заказа и 'success', завершаем выполнение
            exit($_POST['m_orderid'].'|success');
        } else {
            // Очищаем буфер вывода для чистого ответа
            ob_end_clean();
            // Выводим ID заказа и 'error', завершаем выполнение
            exit($_POST['m_orderid'].'|error');
        }
    }
    
    // Очищаем буфер вывода для чистого ответа
    ob_end_clean();
    // Если подпись или статус не прошли проверку, выводим 'error' и завершаем
    exit($_POST['m_orderid'].'|error');
}

 
/* use cot\modules\payments\dictionaries\PaymentDictionary;
use cot\modules\payments\Services\PaymentService;

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('payments', 'module');
if (!in_array($_SERVER['REMOTE_ADDR'], array('185.71.65.92', '185.71.65.189', '149.202.17.210'))) return;
if (isset($_POST['m_operation_id']) && isset($_POST['m_sign']))
{
	$m_key = $cfg['plugin']['payeerbilling']['key'];
	$arHash = array($_POST['m_operation_id'],
			$_POST['m_operation_ps'],
			$_POST['m_operation_date'],
			$_POST['m_operation_pay_date'],
			$_POST['m_shop'],
			$_POST['m_orderid'],
			$_POST['m_amount'],
			$_POST['m_curr'],
			$_POST['m_desc'],
			$_POST['m_status'],
			$m_key);
	$sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
	if ($_POST['m_sign'] == $sign_hash && $_POST['m_status'] == 'success')
	{
		//if(cot_payments_updatestatus($_POST['m_orderid'], 'paid'))
		// Обновляем статус платежа на "оплачен"
		if (PaymentService::getInstance()->setStatus($_POST['m_orderid'], PaymentDictionary::STATUS_PAID, 'payeer')) {
		{
			echo $_POST['m_orderid'].'|success';
		}else{
			echo $_POST['m_orderid'].'|error';
		}
	}
	echo $_POST['m_orderid'].'|error';
} */
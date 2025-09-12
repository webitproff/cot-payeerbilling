<?php
/**
 * [BEGIN_COT_EXT]
 * Hooks=standalone
 * [END_COT_EXT]
 */
// Указываем, что плагин работает как самостоятельный обработчик в Cotonti

/**
 * Payeer billing Plugin
 *
 * payeerbilling plugin for Cotonti 0.9.26, PHP 8.4+
 * Filename: payeerbilling.php
 * @package payeerbilling
 * @version 3.3.36
 * @author CMSWorks, webitproff
 * @copyright Copyright (c) CMSWorks, webitproff 2025 | https://github.com/webitproff
 * @license BSD
 */


// Импортируем класс PaymentDictionary для работы со статусами платежей
use cot\modules\payments\dictionaries\PaymentDictionary;
// Импортируем класс PaymentRepository для взаимодействия с базой данных платежей
use cot\modules\payments\Repositories\PaymentRepository;
// Импортируем класс PaymentService для управления статусами и процессами платежей
use cot\modules\payments\Services\PaymentService;

// Проверяем, определены ли константы COT_CODE и COT_PLUG, иначе выдаём ошибку "Wrong URL"
defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

// Подключаем файл конфигурации и функций плагина payeerbilling
require_once cot_incfile('payeerbilling', 'plug');
// Подключаем файл модуля payments для работы с системой платежей
require_once cot_incfile('payments', 'module');

// Извлекаем параметр 'm' из GET-запроса, ожидая алфавитно-цифровую строку
$m = cot_import('m', 'G', 'ALP');
// Извлекаем параметр 'pid' из GET-запроса, ожидая целое число
$pid = cot_import('pid', 'G', 'INT');

// Загружаем шаблон для обработки вывода (инициализируем $t)
$mskin = cot_tplfile(array('payeerbilling'), 'plug');
$t = new XTemplate($mskin);

// Проверяем, пустой ли параметр 'm', чтобы начать процесс формирования формы оплаты
if (empty($m))
{
    // Проверяем, передан ли 'pid' и существует ли платеж в базе данных
    if (!empty($pid) && $pinfo = PaymentRepository::getInstance()->getById($pid))
    {
        // Проверяем, совпадает ли ID пользователя с владельцем платежа
        cot_block($usr['id'] == $pinfo['pay_userid']);
        // Проверяем, что статус платежа 'new' или 'process'
        cot_block($pinfo['pay_status'] == 'new' || $pinfo['pay_status'] == 'process');

        // Рассчитываем сумму платежа с учётом курса валюты из конфигурации
        $amount = $pinfo['pay_summ']*$cfg['plugin']['payeerbilling']['rate'];
        // Форматируем сумму до двух знаков после запятой
        $amount = number_format($amount, 2, '.', '');
        
        // Формируем массив для генерации подписи платежа
        $arHash = array(
            $cfg['plugin']['payeerbilling']['shop'],
            $pid,
            $amount,
            $cfg['plugin']['payeerbilling']['curr'],
            base64_encode($pinfo['pay_desc']),
            $cfg['plugin']['payeerbilling']['key']
        );
        // Генерируем подпись SHA-256 из массива $arHash
        $sign = strtoupper(hash('sha256', implode(':', $arHash)));

        // Формируем HTML-форму для отправки данных на сервер Payeer с методом POST
        $payeer_form = "<form id=\"payeerform\" method=\"POST\" action=\"https://payeer.com/merchant/\">
            <input type=\"hidden\" name=\"m_shop\" value=\"".$cfg['plugin']['payeerbilling']['shop']."\">
            <input type=\"hidden\" name=\"m_orderid\" value=\"".$pid."\">
            <input type=\"hidden\" name=\"m_amount\" value=\"".$amount."\">
            <input type=\"hidden\" name=\"m_curr\" value=\"".$cfg['plugin']['payeerbilling']['curr']."\">
            <input type=\"hidden\" name=\"m_desc\" value=\"".base64_encode($pinfo['pay_desc'])."\">
            <input type=\"hidden\" name=\"m_sign\" value=\"".$sign."\">
            <input type=\"submit\" name=\"m_process\" value=\"".$L['payeerbilling_formbuy']."\" class=\"btn btn-success btn-large\">
        </form>";
        
        // Передаём форму в шаблон через переменную BILLING_FORM
        $t->assign(array(
            'BILLING_FORM' => $payeer_form,
        ));
        // Обрабатываем блок шаблона MAIN.BILLINGFORM
        $t->parse("MAIN.BILLINGFORM");
        
        // Устанавливаем статус платежа на 'process' через PaymentService
        PaymentService::getInstance()->setStatus($pid, PaymentDictionary::STATUS_PROCESS, 'payeer');
    }
    // Если 'pid' пустой или платеж не найден, завершаем выполнение
    else
    {
        // Вызываем функцию для вывода ошибки
        cot_die();
    }
}
// Проверяем, равен ли параметр 'm' значению 'success' для обработки успешного платежа
elseif ($m == 'success')
{
    // Извлекаем параметры из GET для обработки как web-hook (дублирует ajax.php)
    $m_orderid = cot_import('m_orderid', 'G', 'INT');
    if (!empty($m_orderid) && isset($_GET['m_status']) && $_GET['m_status'] == 'success' && isset($_GET['m_sign'])) {
        // Получаем секретный ключ
        $m_key = $cfg['plugin']['payeerbilling']['key'];
        // Формируем массив для подписи из GET-параметров
        $arHash = array(
            $_GET['m_operation_id'],
            $_GET['m_operation_ps'],
            $_GET['m_operation_date'],
            $_GET['m_operation_pay_date'],
            $_GET['m_shop'],
            $m_orderid,
            $_GET['m_amount'],
            $_GET['m_curr'],
            $_GET['m_desc'],
            $_GET['m_status']
        );
        if (isset($_GET['m_params'])) {
            $arHash[] = $_GET['m_params'];
        }
        $arHash[] = $m_key;
        // Генерируем подпись
        $sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
        
        if ($_GET['m_sign'] == $sign_hash) {
            // Получаем данные о платеже
            $pinfo = PaymentRepository::getInstance()->getById($m_orderid);
            if ($pinfo && $pinfo['pay_status'] != 'paid' && $pinfo['pay_status'] != 'done') {
                // Меняем статус на 'paid'
                if (PaymentService::getInstance()->setStatus($m_orderid, PaymentDictionary::STATUS_PAID, 'payeer')) {
                    // Редирект на getSuccessUrl для показа успеха и зачисления
                    cot_redirect(PaymentService::getInstance()->getSuccessUrl($m_orderid));
                    exit;
                }
            }
        }
    }
    
    // Если не success или подпись неверна, показываем ошибку (как раньше)
    $m_orderid = cot_import('m_orderid', 'G', 'INT');
    if (!empty($m_orderid)) {
        $pinfo = PaymentRepository::getInstance()->getById($m_orderid);
        if ($pinfo && $pinfo['pay_status'] == 'done') {
            $plugin_body = $L['payeerbilling_error_done'];
            $redirect = $pinfo['pay_redirect'];
        } elseif ($pinfo && $pinfo['pay_status'] == 'paid') {
            $plugin_body = $L['payeerbilling_error_paid'];
        }
    }
    
    $t->assign(array(
        "BILLING_TITLE" => $L['payeerbilling_error_title'],
        "BILLING_ERROR" => $L['payeerbilling_error_paid']
    ));
    
    if ($redirect) {
        $t->assign(array(
            "BILLING_REDIRECT_TEXT" => sprintf($L['payeerbilling_redirect_text'], $redirect),
            "BILLING_REDIRECT_URL" => $redirect,
        ));
    }
    
    $t->parse("MAIN.ERROR");
}
// Проверяем, равен ли параметр 'm' значению 'fail' для обработки неуспешного платежа
elseif ($m == 'fail')
{
    // Передаём заголовок и текст ошибки для неуспешного платежа в шаблон
    $t->assign(array(
        'BILLING_TITLE' => $L['payeerbilling_error_title'],
        'BILLING_ERROR' => $L['payeerbilling_error_fail']
    ));
    // Обрабатываем блок шаблона MAIN.ERROR
    $t->parse("MAIN.ERROR");
}
?>
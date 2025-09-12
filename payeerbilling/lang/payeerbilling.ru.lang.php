<?php
/**
 * Payeer billing Plugin
 *
 * payeerbilling plugin for Cotonti 0.9.26, PHP 8.4+
 * Filename: payeerbilling.ru.lang.php
 * @package payeerbilling
 * @version 3.3.36
 * @author CMSWorks, webitproff
 * @copyright Copyright (c) CMSWorks, webitproff 2025 | https://github.com/webitproff
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Module Config
 */

$L['info_desc'] = 'Payeer прием онлайн платежей для сайта Cotonti https://github.com/webitproff/cot-payeerbilling';

$L['cfg_shop'] = array('Идентификатор магазина', '');
$L['cfg_key'] = array('Секретный ключ', '');
$L['cfg_curr'] = array('Валюта платежа', '');
$L['cfg_rate'] = array('Соотношение суммы к валюте сайта', '');

$L['payeerbilling_title'] = 'Payeer';
$L['payeerbilling_note'] = 'Примаются Payeer-USD, Payeer-RUB, Payeer-EUR, USDT, BTC и множество разных криптовалют';

$L['payeerbilling_formtext'] = 'Сейчас вы будете перенаправлены на сайт платежной системы Payeer для проведения оплаты. Если этого не произошло, нажмите кнопку "Перейти к оплате".';
$L['payeerbilling_formtext_terms'] = 'Средства на личный кошелек зачисляются как правило мгновенно, сразу после оплаты. Если этого не произошло, - напишите администратору через <a target="_blank" href="' . cot_url('contact') . '"><strong>форму обратной связи</strong></a>';
$L['payeerbilling_formbuy'] = 'Перейти к оплате';
$L['payeerbilling_error_paid'] = 'Оплата прошла успешно. В ближайшее время услуга будет активирована!';
$L['payeerbilling_error_done'] = 'Оплата прошла успешно.';
$L['payeerbilling_error_incorrect'] = 'Некорректная подпись';
$L['payeerbilling_error_otkaz'] = 'Отказ от оплаты.';
$L['payeerbilling_error_title'] = 'Результат операции оплаты';
$L['payeerbilling_error_fail'] = 'Оплата не произведена! Пожалуйста, повторите попытку. Если ошибка повторится, обратитесь к администратору сайта';

$L['payeerbilling_redirect_text'] = 'Сейчас произойдет редирект на страницу оплаченной услуги. Если этого не произошло, перейдите по <a href="%1$s">ссылке</a>.';
<?php

/* ====================
 * [BEGIN_COT_EXT]
 * Code=payeerbilling
 * Name=Payeer billing
 * Category=Pay
 * Version=3.3.36
 * Date=12 Sep 2025
 * Author=CMSWorks, webitproff
 * Copyright=&copy; CMSWorks, webitproff 2025 | https://github.com/webitproff
 * Notes=
 * Auth_guests=RW
 * Lock_guests=12345A
 * Auth_members=RW
 * Lock_members=12345A
 * Requires_modules=payments
 * [END_COT_EXT]
 *
 * [BEGIN_COT_EXT_CONFIG]
 * shop=01:string:::Идентификатор магазина
 * key=02:string:::Секретное слово
 * curr=03:select:RUB,USD,EUR:RUB:Валюта платежа
 * rate=04:string::1:Соотношение суммы к валюте сайта
 * [END_COT_EXT_CONFIG]
 */

/**
 * Payeer billing Plugin
 *
 * payeerbilling plugin for Cotonti 0.9.26, PHP 8.4+
 * Filename: payeerbilling.setup.php
 * @package payeerbilling
 * @version 3.3.36
 * @author CMSWorks, webitproff
 * @copyright Copyright (c) CMSWorks, webitproff 2025 | https://github.com/webitproff
 * @license BSD
 */
?>
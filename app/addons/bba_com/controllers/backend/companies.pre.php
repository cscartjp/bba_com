<?php
/**
 * コントローラーファイル
 * @noinspection PhpUndefinedClassInspection
 * @var $mode
 * @var  $action
 * @var $auth
 */

use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

// ---------------------- POST routine ------------------------------------- //

//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    if ($mode === 'update') {}
//}

// ---------------------- GET routine ------------------------------------- //

if ($mode === 'manage') {
    //出品者の場合は、自社情報の編集画面にリダイレクト
    $runtime_company_id = Registry::get('runtime.company_id');
    if ($runtime_company_id > 0) {
        return [CONTROLLER_STATUS_REDIRECT, 'companies.update?company_id=' . $runtime_company_id];
    }
}
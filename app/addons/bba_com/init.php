<?php
/**
 * アドオンで使用するPHP HOOKポイントをfn_register_hooks()で登録する
 * @noinspection PhpUndefinedClassInspection
 */

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

/** @noinspection PhpUndefinedFunctionInspection */
fn_register_hooks(
    'get_addons_mail_tpl'
);
<?php
/**
 * コントローラーファイル
 * @noinspection PhpUndefinedClassInspection
 * @var $mode
 * @var  $action
 * @var $auth
 */

use Tygh\Registry;
use Tygh\Tools\SecurityHelper;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

// ---------------------- POST routine ------------------------------------- //
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {}


if ($mode === 'list') {
    if (defined('AJAX_REQUEST')) {
        $tags = fn_get_tag_names(array('tag' => $_REQUEST['q']));
        Tygh::$app['ajax']->assign('autocomplete', $tags);

        exit();
    }
}
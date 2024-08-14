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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //preview_as_user
    if ($mode === 'preview_as_user') {
        $now_hash = fn_bbcmm_get_hashed_time();
        /** @noinspection PhpUndefinedFunctionInspection */
        fn_login_user($_REQUEST['user_id'], true);
        $redirect_url = $_REQUEST['redirect_url'] ?? 'community.index';
        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_REDIRECT, $_REQUEST['redirect_url']];
    }

    //add_new_post
    if ($mode === 'add_new_post') {

        if (defined('DEVELOPMENT')) {
            fn_lcjp_dev_notify([
                $_REQUEST
            ]);
        }

        $redirect_url = $_REQUEST['redirect_url'] ?? 'community.index';
        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_REDIRECT, $_REQUEST['redirect_url']];
    }
}

// ---------------------- GET routine ------------------------------------- //

//community.my_profile
if ($mode === 'my_profile') {

    //ログインしていない場合はログインページへ
    if (empty($auth['user_id'])) {
        /** @noinspection PhpUndefinedConstantInspection */
        return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
    }


    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_index'), 'community.index');
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_my_profile'));


    //ユーザーデータを取得
    /** @noinspection PhpUndefinedFunctionInspection */
    $cp_data = db_get_row("SELECT * FROM ?:community_profiles WHERE user_id = ?i", $auth['user_id']);
    if ($cp_data) {
        /** @noinspection PhpUndefinedFunctionInspection */
        /** @noinspection PhpUndefinedConstantInspection */
        $cp_data['profile_image'] = fn_get_image_pairs($auth['user_id'], 'community_profile', 'M', true, true, CART_LANGUAGE);
        $cp_data['community_image_1'] = fn_get_image_pairs($auth['user_id'], 'community_image_1', 'M', true, true, CART_LANGUAGE);
        $cp_data['community_image_2'] = fn_get_image_pairs($auth['user_id'], 'community_image_2', 'M', true, true, CART_LANGUAGE);
        $cp_data['community_image_3'] = fn_get_image_pairs($auth['user_id'], 'community_image_3', 'M', true, true, CART_LANGUAGE);

        Tygh::$app['view']->assign('cp_data', $cp_data);
    }
}

//GET routineの場合は404
if ($mode === 'preview_as_user') {
    return [CONTROLLER_STATUS_NO_PAGE];
}
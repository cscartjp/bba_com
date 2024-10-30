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

//ログインしていない場合はログインページへ
if (empty($auth['user_id'])) {
    /** @noinspection PhpUndefinedConstantInspection */
    return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
}

$params = $_REQUEST;


// ---------------------- POST routine ------------------------------------- //

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //send_dm
    if ($mode === 'send_dm') {
        //送信相手の情報
        $to_user_id = $params['to_user_id'];

        //to_user_idが数字でない場合は404
        if (!is_numeric($to_user_id)) {
            /** @noinspection PhpUndefinedConstantInspection */
            return [CONTROLLER_STATUS_NO_PAGE];
        }

        //送信相手の情報を取得
        //ユーザーデータを取得
        /** @noinspection PhpUndefinedFunctionInspection */
        $to_userdata = db_get_row("SELECT * FROM ?:community_profiles WHERE user_id = ?i", $params['to_user_id']);
        if (!$to_userdata) {
            /** @noinspection PhpUndefinedConstantInspection */
            return [CONTROLLER_STATUS_NO_PAGE];
        }


        //DMデータをDBに保存 ?:community_direct_mails

//        `direct_mail_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
//        `from_user_id` mediumint(8) UNSIGNED NOT NULL,
//        `to_user_id` mediumint(8) UNSIGNED NOT NULL,
//        `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
//        `subject` varchar(128) NOT NULL DEFAULT '',
//        `message` text NOT NULL DEFAULT '',
//        `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
//        `status` char(1) NOT NULL DEFAULT 'A',

        $dm_data = $params['dm_data'];

        $_data = [
            'from_user_id' => $auth['user_id'],
            'to_user_id' => $to_user_id,
            'subject' => $dm_data['subject'],
            'message' => $dm_data['message'],
            'timestamp' => date('Y-m-d H:i:s'),
        ];


        //$dm_dataをサニタイズする
        //データのサニタイズ
        SecurityHelper::sanitizeObjectData('community_direct_mails', $_data);

        /** @noinspection PhpUndefinedFunctionInspection */
        $direct_mail_id = db_query("INSERT INTO ?:community_direct_mails ?e", $_data);

        //DM送信完了メッセージを表示
        /** @noinspection PhpUndefinedFunctionInspection */
        fn_set_notification('N', __('notice'), __('bba_com.direct_mail_sent'));

        //TODO 宛先に通知を送る

        //リダイレクト
        /** @noinspection PhpUndefinedFunctionInspection */
        return [CONTROLLER_STATUS_OK, 'community_dm.view?direct_mail_id=' . $direct_mail_id];

    }

    //send_dm_res(返信を送る)
    if ($mode === 'send_dm_res') {

        die(fn_print_r([
            $mode,
            $params
        ]));


        //送信相手の情報
        $to_user_id = $params['to_user_id'];

        //to_user_idが数字でない場合は404
        if (!is_numeric($to_user_id)) {
            /** @noinspection PhpUndefinedConstantInspection */
            return [CONTROLLER_STATUS_NO_PAGE];
        }

        //送信相手の情報を取得
        //ユーザーデータを取得
        /** @noinspection PhpUndefinedFunctionInspection */
        $to_userdata = db_get_row("SELECT * FROM ?:community_profiles WHERE user_id = ?i", $params['to_user_id']);
        if (!$to_userdata) {
            /** @noinspection PhpUndefinedConstantInspection */
            return [CONTROLLER_STATUS_NO_PAGE];
        }

        //DMデータをDBに保存 ?:community_direct_mails

    }
}

// ---------------------- GET routine ------------------------------------- //

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //自分のデータを取得////////////////////////////////////////////////////
    $cp_data = fn_bbcmm_get_my_community_info();
    Tygh::$app['view']->assign('cp_data', $cp_data);
    ////////////////////////////////////////////////////////////////////////
}

//send_dm
if ($mode === 'send_dm') {

    //送信相手の情報
    $to_user_id = $params['to_user_id'];

    //to_user_idが数字でない場合は404
    if (!is_numeric($to_user_id)) {
        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    //送信相手の情報を取得
    //ユーザーデータを取得
    /** @noinspection PhpUndefinedFunctionInspection */
    $to_userdata = db_get_row("SELECT * FROM ?:community_profiles WHERE user_id = ?i", $params['to_user_id']);
    if (!$to_userdata) {
        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    //送信相手の情報をテンプレートにアサイン
    Tygh::$app['view']->assign('to_userdata', $to_userdata);

    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_home'), 'community.home');
    //bba_com.community_groups
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_direct_mail_list'), 'community_dm.list');
    //bba_com.view_group
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb($to_userdata['name'] . __("dear"));
}

//view
if ($mode === 'view') {

    $direct_mail_id = $params['direct_mail_id'];

    //DMデータを取得
    $dm_data = fn_bbcmm_get_direct_mail_data($direct_mail_id);

    //$dm_dataがない場合は404
    if (!$dm_data) {
        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    //DMデータをテンプレートにアサイン
    Tygh::$app['view']->assign('dm_data', $dm_data);

    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_home'), 'community.home');

    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_direct_mail_list'), 'community_dm.list');
    //bba_com.view_group
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb($dm_data['subject'] . '::' . $dm_data['to_user_name'] . __("dear"));

}
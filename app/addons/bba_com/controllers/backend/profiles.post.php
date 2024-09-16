<?php
/**
 * コントローラーファイル
 * @noinspection PhpUndefinedClassInspection
 * @var $mode
 * @var  $action
 * @var $auth
 */

use Tygh\Tools\SecurityHelper;
use Tygh\Registry;


if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

// ---------------------- POST routine ------------------------------------- //

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'update') {

        $user_id = $_REQUEST['user_id'];
        $community_profile_data = $_REQUEST['cp_data'];
        $user_data = $_REQUEST['user_data'];

        //
        $community_profile_data['user_id'] = $user_id;

        //プロフィールデータのサニタイズ
        SecurityHelper::sanitizeObjectData('community_profile', $community_profile_data);

        //$community_profile_dataを?:community_profilesに保存する REPLACE INTO
        /** @noinspection PhpUndefinedFunctionInspection */
        db_query("REPLACE INTO ?:community_profiles SET ?u", $community_profile_data);

        //画像データを保存
        $object_types = [
            'community_profile',
            'community_image_1',
            'community_image_2',
            'community_image_3'
        ];
        fn_bbcmm_attach_image_pairs($user_id, $object_types);
    }
}

// ---------------------- GET routine ------------------------------------- //

if ($mode === 'update') {

    $user_type = $_REQUEST['user_type'];
    if ($user_type !== 'V') {
        return;
    }

    //user_dataを取得する
    $user_data = Tygh::$app['view']->getTemplateVars('user_data');

    //?:community_profilesからデータを取得する
    /** @noinspection PhpUndefinedFunctionInspection */
    $cp_data = db_get_row("SELECT * FROM ?:community_profiles WHERE user_id = ?i", $user_data['user_id']);
    if ($cp_data) {

        //自分の画像データを取得
        $object_types = [
            'community_profile',
            'community_image_1',
            'community_image_2',
            'community_image_3'
        ];
        fn_bbcmm_get_image_pairs($auth['user_id'], $object_types, $cp_data);

        Tygh::$app['view']->assign('cp_data', $cp_data);
    }

    //company_dataを取得する
    /** @noinspection PhpUndefinedFunctionInspection */
    $company_data = fn_get_company_data($user_data['company_id']);

    if ($company_data) {
        Tygh::$app['view']->assign('company_data', $company_data);
    }

    //「コミュニティ用プロフィール」タブを追加する
    /** @noinspection PhpUndefinedFunctionInspection */
    Registry::set('navigation.tabs.community_profile', [
        'title' => __('bba_com.community_profile'),
        'js' => true,
    ]);
}


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
    if ($mode === 'update') {

        $user_id = $_REQUEST['user_id'];
        $community_profile_data = $_REQUEST['cp_data'];
        $user_data = $_REQUEST['user_data'];

        //
        $community_profile_data['user_id'] = $user_id;

        //$community_profile_dataを?:community_profilesに保存する REPLACE INTO
        /** @noinspection PhpUndefinedFunctionInspection */
        db_query("REPLACE INTO ?:community_profiles SET ?u", $community_profile_data);


        /** @noinspection PhpUndefinedFunctionInspection */
        /** @noinspection PhpUndefinedConstantInspection */
        fn_attach_image_pairs('community_profile', 'community_profile', $user_id, CART_LANGUAGE);

        //community_image_1
        fn_attach_image_pairs('community_image_1', 'community_image_1', $user_id, CART_LANGUAGE);
        fn_attach_image_pairs('community_image_2', 'community_image_2', $user_id, CART_LANGUAGE);
        fn_attach_image_pairs('community_image_3', 'community_image_3', $user_id, CART_LANGUAGE);

//        if (defined('DEVELOPMENT')) {
//            fn_lcjp_dev_notify([
////                $user_id,
////                $_FILES,
//                $community_profile_data,
////                $user_data
//            ]);
//        }
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

        /** @noinspection PhpUndefinedFunctionInspection */
        /** @noinspection PhpUndefinedConstantInspection */
        $cp_data['profile_image'] = fn_get_image_pairs($user_data['user_id'], 'community_profile', 'M', true, true, CART_LANGUAGE);
        $cp_data['community_image_1'] = fn_get_image_pairs($user_data['user_id'], 'community_image_1', 'M', true, true, CART_LANGUAGE);
        $cp_data['community_image_2'] = fn_get_image_pairs($user_data['user_id'], 'community_image_2', 'M', true, true, CART_LANGUAGE);
        $cp_data['community_image_3'] = fn_get_image_pairs($user_data['user_id'], 'community_image_3', 'M', true, true, CART_LANGUAGE);

        Tygh::$app['view']->assign('cp_data', $cp_data);
    }

    //company_dataを取得する
    /** @noinspection PhpUndefinedFunctionInspection */
    $company_data = fn_get_company_data($user_data['company_id']);

    if ($company_data) {
        Tygh::$app['view']->assign('company_data', $company_data);
    }

//    if (defined('DEVELOPMENT')) {
//        fn_lcjp_dev_notify([
//            $company_data
//        ]);
//    }
    //「コミュニティ用プロフィール」タブを追加する
    /** @noinspection PhpUndefinedFunctionInspection */
    Registry::set('navigation.tabs.community_profile', [
        'title' => __('bba_com.community_profile'),
        'js' => true,
    ]);
}


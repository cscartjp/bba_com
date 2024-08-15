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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //like
    if ($mode === 'like') {
        $post_id = $_REQUEST['post_id'];
        $user_id = $auth['user_id'];


        //既にいいねしているか確認
        /** @noinspection PhpUndefinedFunctionInspection */
        $like_data = db_get_row("SELECT * FROM ?:community_user_post_likes WHERE post_id = ?i AND user_id = ?i", $post_id, $user_id);

        if ($like_data) {
            /** @noinspection PhpUndefinedFunctionInspection */
            db_query("DELETE FROM ?:community_user_post_likes WHERE post_id = ?i AND user_id = ?i", $post_id, $user_id);
            /** @noinspection PhpUndefinedFunctionInspection */
            fn_set_notification('N', __('notice'), __('bba_com.like_removed'));
        } else {
            $_like_data = [
                'post_id' => $post_id,
                'user_id' => $user_id,
                'timestamp' => date('Y-m-d H:i:s'),
            ];

            /** @noinspection PhpUndefinedFunctionInspection */
            db_query("INSERT INTO ?:community_user_post_likes ?e", $_like_data);
//            /** @noinspection PhpUndefinedFunctionInspection */
//            fn_set_notification('N', __('notice'), __('bba_com.like_added'));
        }

        //追加後のいいね数を取得
        /** @noinspection PhpUndefinedFunctionInspection */
        $like_count = db_get_field("SELECT COUNT(*) FROM ?:community_user_post_likes WHERE post_id = ?i", $post_id);

        $like_data = [
            'post_id' => $post_id,
            'like_count' => $like_count,
        ];

        //$like_countをJSON形式で返す
        try {
            echo json_encode($like_data, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            echo '';
        }
        exit;
    }

    //add_new_post
    if ($mode === 'add_new_post') {


//        CREATE TABLE `?:community_user_posts` (
//        `post_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
//        `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
//        `user_id` mediumint(8) UNSIGNED NOT NULL,
//        `post_type` char(1) NOT NULL DEFAULT 'T',
//        `article` text NOT NULL,
//        `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
//        `status` char(1) NOT NULL DEFAULT 'A',
//        PRIMARY KEY (`post_id`)
//        ) ENGINE=MyISAM DEFAULT CHARSET UTF8;

        $user_post_data = $_REQUEST['new_post'];
        $_post_data = [
            'user_id' => $auth['user_id'],
            'article' => $user_post_data['article'],
            'timestamp' => date('Y-m-d H:i:s'),
        ];


        //データのサニタイズ
//        SecurityHelper::sanitizeObjectData('newsletter', $_post_data);

        //データベースに保存
        /** @noinspection PhpUndefinedFunctionInspection */
        $post_id = db_query("INSERT INTO ?:community_user_posts ?e", $_post_data);

        if ($post_id) {
            /** @noinspection PhpUndefinedFunctionInspection */
            fn_set_notification('N', __('notice'), __('bba_com.post_added'));
        } else {
            /** @noinspection PhpUndefinedFunctionInspection */
            fn_set_notification('E', __('error'), __('bba_com.post_not_added'));
        }

        $redirect_url = $_REQUEST['redirect_url'] ?? 'community.index';

        //$redirect_urlのクエリパラメーターを取得
        $parameter = parse_url($redirect_url, PHP_URL_QUERY);
        //$parameterを配列に変換
        parse_str($parameter, $parameter);

        //$parameterにpageが含まれている場合は除外
        if (isset($parameter['page'])) {
            unset($parameter['page']);
        }

        //クエリパラメーターを再構築
        $redirect_url = strtok($redirect_url, '?') . '?' . http_build_query($parameter);

        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_REDIRECT, $redirect_url];
    }


    //add_new_comment
    if ($mode === 'add_new_comment') {

        $user_post_data = $_REQUEST['new_comment'];
        $_post_data = [
            'user_id' => $auth['user_id'],
            'parent_id' => $user_post_data['parent_id'],
            'post_type' => 'C',
            'article' => $user_post_data['article'],
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        //データのサニタイズ
//        SecurityHelper::sanitizeObjectData('newsletter', $_post_data);

        //データベースに保存
        /** @noinspection PhpUndefinedFunctionInspection */
        $post_id = db_query("INSERT INTO ?:community_user_posts ?e", $_post_data);

        if ($post_id) {
            /** @noinspection PhpUndefinedFunctionInspection */
            fn_set_notification('N', __('notice'), __('bba_com.comment_added'));
        } else {
            /** @noinspection PhpUndefinedFunctionInspection */
            fn_set_notification('E', __('error'), __('bba_com.comment_not_added'));
        }

        $redirect_url = $_REQUEST['redirect_url'] ?? 'community.index';

        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_OK, $redirect_url];
    }


    //preview_as_user
    if ($mode === 'preview_as_user') {
        $now_hash = fn_bbcmm_get_hashed_time();
        /** @noinspection PhpUndefinedFunctionInspection */
        fn_login_user($_REQUEST['user_id'], true);
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

    $params = $_REQUEST;


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


    //ユーザーポスト(タイムライン)を取得
    $params['user_id'] = $auth['user_id'];//ログインユーザーのID
    $params['post_type'] = 'T';//T: タイムライン
    $params['disp_like'] = true;//いいねボタンを表示するか

    [$user_posts, $search] = fn_bbcmm_get_user_posts($params, Registry::get('settings.Appearance.elements_per_page'));


    Tygh::$app['view']->assign('user_posts', $user_posts);
    Tygh::$app['view']->assign('search', $search);
}


//community.view_user 他のユーザーのプロフィールを表示
if ($mode === 'view_user') {

    //ログインしていない場合はログインページへ
    if (empty($auth['user_id'])) {
        /** @noinspection PhpUndefinedConstantInspection */
        return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    $params = $_REQUEST;

    //$params['user_id']が数字でない場合は404
    if (!is_numeric($params['user_id'])) {
        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    //$params['user_id']と$auth['user_id']が一致する場合はmy_profileにリダイレクト
    if ((int)$params['user_id'] === (int)$auth['user_id']) {
        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_REDIRECT, 'community.my_profile'];
    }


    //ユーザーデータを取得
    /** @noinspection PhpUndefinedFunctionInspection */
    $cp_data = db_get_row("SELECT * FROM ?:community_profiles WHERE user_id = ?i", $params['user_id']);
    if (!$cp_data) {
        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    //このユーザーのcompany_idを取得
    $cp_data['company_id'] = db_get_field("SELECT company_id FROM ?:users WHERE user_id = ?i", $params['user_id']);


    //画像データを取得
    /** @noinspection PhpUndefinedFunctionInspection */
    /** @noinspection PhpUndefinedConstantInspection */
    $cp_data['profile_image'] = fn_get_image_pairs($cp_data['user_id'], 'community_profile', 'M', true, true, CART_LANGUAGE);
    $cp_data['community_image_1'] = fn_get_image_pairs($cp_data['user_id'], 'community_image_1', 'M', true, true, CART_LANGUAGE);
    $cp_data['community_image_2'] = fn_get_image_pairs($cp_data['user_id'], 'community_image_2', 'M', true, true, CART_LANGUAGE);
    $cp_data['community_image_3'] = fn_get_image_pairs($cp_data['user_id'], 'community_image_3', 'M', true, true, CART_LANGUAGE);

    //このユーザーのタイムラインを取得
    $params['post_type'] = 'T';//T: タイムライン
    $params['user_id'] = $cp_data['user_id'];//表示するユーザーのID
    $params['disp_like'] = true;//いいねボタンを表示するか
    [$user_posts, $search] = fn_bbcmm_get_user_posts($params, Registry::get('settings.Appearance.elements_per_page'));

    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_index'), 'community.index');
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb($cp_data['name'] . __('bba_com.community_profile'));


    Tygh::$app['view']->assign('cp_data', $cp_data);
    Tygh::$app['view']->assign('user_posts', $user_posts);
    Tygh::$app['view']->assign('search', $search);
}


//GET routineの場合は404
if ($mode === 'preview_as_user') {
    return [CONTROLLER_STATUS_NO_PAGE];
}
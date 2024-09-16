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
    //edit_profile
    if ($mode === 'edit_profile') {
        $user_id = $auth['user_id'];

        //コミュニティプロフィールデータを取得
        /** @noinspection PhpUndefinedFunctionInspection */
        $cp_data = db_get_row("SELECT * FROM ?:community_profiles WHERE user_id = ?i", $auth['user_id']);


        $profile_data = $_REQUEST['profile_data'];
        $profile_data['user_id'] = $user_id;

        //プロフィールデータのサニタイズ
        SecurityHelper::sanitizeObjectData('community_profile', $profile_data);

        //$cp_dataと$profile_dataをマージ
        $profile_data = array_merge($cp_data, $profile_data);

        ////データベースに保存 REPLACE INTO
        /** @noinspection PhpUndefinedFunctionInspection */
        db_query("REPLACE INTO ?:community_profiles SET ?u", $profile_data);

        //画像データを保存
        $object_types = [
            'community_profile',
            'community_image_1',
            'community_image_2',
            'community_image_3'
        ];
        fn_bbcmm_attach_image_pairs($user_id, $object_types);


        //セッションをリセットする $auth['cp_data']
        $auth = &Tygh::$app['session']['auth'];
        unset($auth['cp_data']);

        //自分のデータを取得////////////////////////////////////////////////////
        $cp_data = fn_bbcmm_get_my_community_info(false);
        ////////////////////////////////////////////////////////////////////////


        /** @noinspection PhpUndefinedFunctionInspection */
        fn_set_notification('N', __('notice'), __('bba_com.community_profile_updated'));

        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_OK, 'community.my_profile'];
    }

    //add_friend
    if ($mode === 'add_friend') {
        $user_id = $auth['user_id'];
        $friend_id = $_REQUEST['friend_id'];

        $_friend_data = [
            'user_id' => $user_id,
            'friend_id' => $friend_id,
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        /** @noinspection PhpUndefinedFunctionInspection */
        $relationship_id = db_query("INSERT INTO ?:community_relationships ?e", $_friend_data);

        //逆も友達追加する(相互フォロー)
        $_friend_data_op = [
            'user_id' => $friend_id,
            'friend_id' => $user_id,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        $op_relationship_id = db_query("INSERT INTO ?:community_relationships ?e", $_friend_data_op);


        /** @noinspection PhpUndefinedFunctionInspection */
        fn_set_notification('N', __('notice'), __('bba_com.friend_added'));


        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_OK, 'community.view_user?user_id=' . $friend_id];
    }

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
        $user_post_data = $_REQUEST['new_post'];
        $_post_data = [
            'user_id' => $auth['user_id'],
            'article' => $user_post_data['article'],
            'timestamp' => date('Y-m-d H:i:s'),
        ];


        //データのサニタイズ
        SecurityHelper::sanitizeObjectData('community_user_posts', $_post_data);

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

        $redirect_url = $_REQUEST['redirect_url'] ?? 'community.home';

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

        $redirect_url = $_REQUEST['redirect_url'] ?? 'community.home';

        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_OK, $redirect_url];
    }


    //preview_as_user
    if ($mode === 'preview_as_user') {
        $now_hash = fn_bbcmm_get_hashed_time();
        /** @noinspection PhpUndefinedFunctionInspection */
        fn_login_user($_REQUEST['user_id'], true);
        $redirect_url = $_REQUEST['redirect_url'] ?? 'community.home';
        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_REDIRECT, $_REQUEST['redirect_url']];
    }
}

// ---------------------- GET routine ------------------------------------- //

//community.home
if ($mode === 'home') {

    //ログインしていない場合はログインページへ
    if (empty($auth['user_id'])) {
        /** @noinspection PhpUndefinedConstantInspection */
        return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    $params = $_REQUEST;

    //自分のデータを取得////////////////////////////////////////////////////
    $cp_data = fn_bbcmm_get_my_community_info();
    ////////////////////////////////////////////////////////////////////////


    Tygh::$app['view']->assign('cp_data', $cp_data);

    //友達情報を取得
    /** @noinspection PhpUndefinedFunctionInspection */
    $relationships = fn_bbcmm_get_friends($auth['user_id']);
    Tygh::$app['view']->assign('relationships', $relationships);

    $friend_ids = array_column($relationships, 'friend_id');


    //全体のタイムラインを取得
//    $params['user_id'] = $auth['user_id'];//ログインユーザーのID
    $params['post_type'] = 'T';//T: タイムライン
    $params['disp_like'] = true;//いいねボタン

    if ($friend_ids) {
        $params['friend_ids'] = $friend_ids;
    }


    [$user_posts, $search] = fn_bbcmm_get_user_posts($params, Registry::get('settings.Appearance.elements_per_page'));

    Tygh::$app['view']->assign('user_posts', $user_posts);
    Tygh::$app['view']->assign('search', $search);

    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_home'));
}

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
    fn_add_breadcrumb(__('bba_com.community_home'), 'community.home');
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_my_profile'));

    //自分のデータを取得////////////////////////////////////////////////////
    $cp_data = fn_bbcmm_get_my_community_info();
    ////////////////////////////////////////////////////////////////////////

    Tygh::$app['view']->assign('cp_data', $cp_data);


    //ユーザーポスト(タイムライン)を取得
    $params['user_id'] = $auth['user_id'];//ログインユーザーのID
    $params['post_type'] = 'T';//T: タイムライン
    $params['disp_like'] = true;//いいねボタンを表示するか

    [$user_posts, $search] = fn_bbcmm_get_user_posts($params, Registry::get('settings.Appearance.elements_per_page'));


    //友達情報を取得
    /** @noinspection PhpUndefinedFunctionInspection */
    $relationships = fn_bbcmm_get_friends($auth['user_id']);

    
    Tygh::$app['view']->assign('user_posts', $user_posts);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('relationships', $relationships);
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
    /** @noinspection PhpUndefinedFunctionInspection */
    $cp_data['company_id'] = db_get_field("SELECT company_id FROM ?:users WHERE user_id = ?i", $params['user_id']);


    //ユーザーの画像データを取得
    $object_types = [
        'community_profile',
        'community_image_1',
        'community_image_2',
        'community_image_3'
    ];
    fn_bbcmm_get_image_pairs($params['user_id'], $object_types, $cp_data);


    //このユーザーのタイムラインを取得
    $params['post_type'] = 'T';//T: タイムライン
    $params['user_id'] = $cp_data['user_id'];//表示するユーザーのID
    $params['disp_like'] = true;//いいねボタンを表示するか
    [$user_posts, $search] = fn_bbcmm_get_user_posts($params, Registry::get('settings.Appearance.elements_per_page'));


    //友達関係を取得
    /** @noinspection PhpUndefinedFunctionInspection */
    $relationship_data = db_get_row("SELECT * FROM ?:community_relationships WHERE user_id = ?i AND friend_id = ?i", $auth['user_id'], $cp_data['user_id']);

    //友達情報を取得
    $relationships = fn_bbcmm_get_friends($cp_data['user_id']);

    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_home'), 'community.home');
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb($cp_data['name'] . __('bba_com.community_profile'));


    Tygh::$app['view']->assign('cp_data', $cp_data);
    Tygh::$app['view']->assign('user_posts', $user_posts);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('relationship_data', $relationship_data);
    Tygh::$app['view']->assign('relationships', $relationships);
}

//community.edit_profile
if ($mode === 'edit_profile') {

    //ログインしていない場合はログインページへ
    if (empty($auth['user_id'])) {
        /** @noinspection PhpUndefinedConstantInspection */
        return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    $params = $_REQUEST;

    //自分のデータを取得////////////////////////////////////////////////////
    $cp_data = fn_bbcmm_get_my_community_info(false);
    ////////////////////////////////////////////////////////////////////////
    ///
    /** @noinspection PhpUndefinedFunctionInspection */
    $user_data = fn_get_user_info($auth['user_id']);

    Tygh::$app['view']->assign('cp_data', $cp_data);
    Tygh::$app['view']->assign('user_data', $user_data);

    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_home'), 'community.home');
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_edit_profile'));
}

//friends
if ($mode === 'friends') {

    //ログインしていない場合はログインページへ
    if (empty($auth['user_id'])) {
        /** @noinspection PhpUndefinedConstantInspection */
        return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    $params = $_REQUEST;

    //自分のデータを取得////////////////////////////////////////////////////
    $cp_data = fn_bbcmm_get_my_community_info();
    ////////////////////////////////////////////////////////////////////////

    Tygh::$app['view']->assign('cp_data', $cp_data);

    //友達情報を取得
    $relationships = fn_bbcmm_get_friends($auth['user_id'], 0);
    Tygh::$app['view']->assign('relationships', $relationships);

    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_home'), 'community.home');
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_friends'));
}


//search
if ($mode === 'search') {

    //ログインしていない場合はログインページへ
    if (empty($auth['user_id'])) {
        /** @noinspection PhpUndefinedConstantInspection */
        return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    $params = $_REQUEST;
    $params['search_all'] = true;

    //$paramsをサニタイズ
    SecurityHelper::sanitizeObjectData('community_user_posts', $params);

    //自分のデータを取得////////////////////////////////////////////////////
    $cp_data = fn_bbcmm_get_my_community_info();
    ////////////////////////////////////////////////////////////////////////

    Tygh::$app['view']->assign('cp_data', $cp_data);


    //投稿の検索結果を取得する
    $params['post_type'] = 'T';//T: タイムライン
    $params['disp_like'] = true;//いいねボタンを表示するか

    $match_posts = "0";
    $match_people = "0";


    //$params['cq']が2文字以下の場合は、検索結果を表示しない
    if (mb_strlen($params['cq']) < 2) {

        /** @noinspection PhpUndefinedFunctionInspection */
        fn_set_notification('W', __('warning'), __('bba_com.search_query_too_short'));

        Tygh::$app['view']->assign('user_posts', null);
        Tygh::$app['view']->assign('search', $params);
    } else {
        //投稿の検索結果を取得
        [$user_posts, $search] = fn_bbcmm_get_user_posts($params, Registry::get('settings.Appearance.elements_per_page'));
        $match_posts = $search['total_items'];

        //人物の検索結果を取得
        [$people, $people_search] = fn_bbcmm_search_community_profiles($params, 999);
        $match_people = $people_search['total_items'];

        Tygh::$app['view']->assign('user_posts', $user_posts);
        Tygh::$app['view']->assign('people', $people);
        Tygh::$app['view']->assign('search', $search);
        Tygh::$app['view']->assign('people_search', $people_search);
    }

    Registry::set('navigation.tabs', [
        'posts' => [
            'title' => __('bba_com.tab_posts', ['[count]' => $match_posts]),
            'js' => true
        ],
        'people' => [
            'title' => __('bba_com.tab_people', ['[count]' => $match_people]),
            'js' => true
        ]
    ]);


    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_home'), 'community.home');
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_search'));
}

//GET routineの場合は404
if ($mode === 'preview_as_user') {
    /** @noinspection PhpUndefinedConstantInspection */
    return [CONTROLLER_STATUS_NO_PAGE];
}
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

    //create
    if ($mode === 'create') {
        $user_id = $auth['user_id'];
        $user_type = $auth['user_type'];

        //$user_idが数字でない場合は404
        if (!is_numeric($user_id)) {
            /** @noinspection PhpUndefinedConstantInspection */
            return [CONTROLLER_STATUS_NO_PAGE];
        }
        //$user_typeがVでない場合は404
        if ($user_type !== 'V') {
            /** @noinspection PhpUndefinedConstantInspection */
            return [CONTROLLER_STATUS_NO_PAGE];
        }

        $group_data = $_REQUEST['group_data'];


        //データのサニタイズ
        SecurityHelper::sanitizeObjectData('community_groups', $group_data);

        //データベースに保存
//        `group_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
//        `create_user_id` mediumint(8) UNSIGNED NOT NULL,
//        `group` varchar(128) NOT NULL DEFAULT '',
//        `description` text NOT NULL DEFAULT '',
//        `type` char(1) NOT NULL DEFAULT 'P',
//        `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
//        `status` char(1) NOT NULL DEFAULT 'A',

        $group_data['create_user_id'] = $user_id;
        $group_data['timestamp'] = date('Y-m-d H:i:s');


        /** @noinspection PhpUndefinedFunctionInspection */
        $group_id = db_query("INSERT INTO ?:community_groups ?e", $group_data);

        if ($group_id) {

            //group_iconを保存
            //画像データを保存
            $object_types = [
                'group_icon'
            ];
            fn_bbcmm_attach_image_pairs($group_id, $object_types);

            //メンバーに追加
            $member_data = [
                'group_id' => $group_id,
                'user_id' => $user_id,
                'role' => 'A',
                'timestamp' => date('Y-m-d H:i:s'),
            ];
            /** @noinspection PhpUndefinedFunctionInspection */
            $group_member_id = db_query("INSERT INTO ?:community_group_members ?e", $member_data);


            /** @noinspection PhpUndefinedFunctionInspection */
            fn_set_notification('N', __('notice'), __('bba_com.group_created'));
        } else {
            /** @noinspection PhpUndefinedFunctionInspection */
            fn_set_notification('E', __('error'), __('bba_com.group_not_created'));
        }

        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_OK, 'community_groups.list'];
    }

    //edit
    if ($mode === 'edit') {
        $group_id = $_REQUEST['group_id'];

        //ログインしていない場合はログインページへ
        if (empty($auth['user_id'])) {
            /** @noinspection PhpUndefinedConstantInspection */
            return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
        }

        $params = $_REQUEST;

        //自分のデータを取得////////////////////////////////////////////////////
        $cp_data = fn_bbcmm_get_my_community_info();
        Tygh::$app['view']->assign('cp_data', $cp_data);
        ////////////////////////////////////////////////////////////////////////

        //ユーザーがグループに参加しているか確認
        $is_member = fn_bbcmm_is_user_in_group($group_id, $auth['user_id']);
        Tygh::$app['view']->assign('is_member', $is_member);

        //$is_memberがA以外の場合は404
        if ($is_member !== 'A') {
            /** @noinspection PhpUndefinedConstantInspection */
            return [CONTROLLER_STATUS_NO_PAGE];
        }

        $group_data = $_REQUEST['group_data'];

        //データのサニタイズ
        SecurityHelper::sanitizeObjectData('community_groups', $group_data);

        //データベースに保存
        /** @noinspection PhpUndefinedFunctionInspection */
        db_query("UPDATE ?:community_groups SET ?u WHERE group_id = ?i", $group_data, $group_id);

        //group_iconを保存
        //画像データを保存
        $object_types = [
            'group_icon'
        ];
        fn_bbcmm_attach_image_pairs($group_id, $object_types);

        /** @noinspection PhpUndefinedFunctionInspection */
        fn_set_notification('N', __('notice'), __('bba_com.group_updated'));

        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_OK, 'community_groups.view?group_id=' . $group_id];
    }

    //join
    if ($mode === 'join') {
        //ログインしていない場合はログインページへ
        if (empty($auth['user_id'])) {
            /** @noinspection PhpUndefinedConstantInspection */
            return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
        }

        $group_id = $_REQUEST['group_id'];
        $user_id = $auth['user_id'];

        //$group_idが数字でない場合は404
        if (!is_numeric($group_id)) {
            /** @noinspection PhpUndefinedConstantInspection */
            return [CONTROLLER_STATUS_NO_PAGE];
        }

        //リダイレクト先
        $redirect_url = $_REQUEST['redirect_url'] ?? 'community_groups.list';

        //ユーザーがグループに参加しているか確認
        $is_member = fn_bbcmm_is_user_in_group($group_id, $user_id);
        //すでにメンバーの場合はリダイレクト
        if ($is_member) {
            /** @noinspection PhpUndefinedFunctionInspection */
            fn_set_notification('E', __('error'), __('bba_com.already_joined_group'));

            /** @noinspection PhpUndefinedConstantInspection */
            return [CONTROLLER_STATUS_REDIRECT, $redirect_url];
        }

        //グループデータを取得
        $group_data = fn_bbcmm_get_group_data($group_id);
        $group_type = $group_data['type'];


        //データベースに保存
        $member_data = [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'role' => 'M',//M: member
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        //グループのタイプがIの場合は承認待ち
        if ($group_type === 'I') {
            $member_data['status'] = 'P';//P: pending
        }

        /** @noinspection PhpUndefinedFunctionInspection */
        $group_member_id = db_query("INSERT INTO ?:community_group_members ?e", $member_data);

        if ($group_member_id) {

            if ($group_type === 'I') {
                /** @noinspection PhpUndefinedFunctionInspection */
                fn_set_notification('N', __('notice'), __('bba_com.group_join_request'));
            } else {
                /** @noinspection PhpUndefinedFunctionInspection */
                fn_set_notification('N', __('notice'), __('bba_com.group_joined'));
            }
        } else {
            /** @noinspection PhpUndefinedFunctionInspection */
            fn_set_notification('E', __('error'), __('bba_com.group_not_joined'));
        }


        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_OK, $redirect_url];
    }

    //change_group_status
    if ($mode === 'change_group_status') {

        $group_id = $_REQUEST['group_id'];
        $status_to = $_REQUEST['status_to'];
        $user_id = $_REQUEST['user_id'];


        //ログインしていない場合はログインページへ
        if (empty($auth['user_id'])) {
            /** @noinspection PhpUndefinedConstantInspection */
            return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
        }


        //$group_idが数字でない場合は404
        if (!is_numeric($group_id)) {
            /** @noinspection PhpUndefinedConstantInspection */
            return [CONTROLLER_STATUS_NO_PAGE];
        }

        //リダイレクト先
        $redirect_url = $_REQUEST['redirect_url'] ?? 'community_groups.list';

        //グループデータを取得
        $group_data = fn_bbcmm_get_group_data($group_id);

        //グループデータがない場合は404
        if (empty($group_data)) {
            /** @noinspection PhpUndefinedConstantInspection */
            return [CONTROLLER_STATUS_NO_PAGE];
        }

        //ユーザーがグループに参加しているか確認
        /** @noinspection PhpUndefinedFunctionInspection */
        $group_member_data = db_get_row("SELECT * FROM ?:community_group_members WHERE group_id = ?i AND user_id = ?i", $group_id, $user_id);


        //$group_member_dataが空の場合は404
        if (empty($group_member_data)) {
            /** @noinspection PhpUndefinedConstantInspection */
            return [CONTROLLER_STATUS_NO_PAGE];
        }


        $_update_data = [
            'status' => $status_to,
        ];

        //データベースを更新
        /** @noinspection PhpUndefinedFunctionInspection */
        db_query("UPDATE ?:community_group_members SET ?u WHERE group_member_id = ?i", $_update_data, $group_member_data['group_member_id']);

        /** @noinspection PhpUndefinedFunctionInspection */
        fn_set_notification('N', __('notice'), __('bba_com.group_status_changed'));

        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_OK, $redirect_url];
    }


    //add_new_post
    if ($mode === 'add_new_post') {
        $user_post_data = $_REQUEST['new_post'];
        $_post_data = [
            'user_id' => $auth['user_id'],
            'object_id' => $user_post_data['object_id'],
            'post_type' => $user_post_data['post_type'] ?? 'G',
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

}

// ---------------------- GET routine ------------------------------------- //

//community.home
if ($mode === 'list') {

    //ログインしていない場合はログインページへ
    if (empty($auth['user_id'])) {
        /** @noinspection PhpUndefinedConstantInspection */
        return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    $params = $_REQUEST;

    //自分のデータを取得////////////////////////////////////////////////////
    $cp_data = fn_bbcmm_get_my_community_info();
    Tygh::$app['view']->assign('cp_data', $cp_data);
    ////////////////////////////////////////////////////////////////////////


    [$groups, $search] = fn_bbcmm_get_groups($params, Registry::get('settings.Appearance.elements_per_page'));


    Tygh::$app['view']->assign('groups', $groups);
    Tygh::$app['view']->assign('search', $search);

    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_home'), 'community.home');
    //bba_com.community_groups
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_groups'));
}

//view
if ($mode === 'view') {
    $group_id = $_REQUEST['group_id'];

    //ログインしていない場合はログインページへ
    if (empty($auth['user_id'])) {
        /** @noinspection PhpUndefinedConstantInspection */
        return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    $params = $_REQUEST;

    //自分のデータを取得////////////////////////////////////////////////////
    $cp_data = fn_bbcmm_get_my_community_info();
    Tygh::$app['view']->assign('cp_data', $cp_data);
    ////////////////////////////////////////////////////////////////////////

    //ユーザーがグループに参加しているか確認
    $is_member = fn_bbcmm_is_user_in_group($group_id, $auth['user_id']);
    Tygh::$app['view']->assign('is_member', $is_member);


    //グループデータを取得
    $group_data = fn_bbcmm_get_group_data($group_id);
    Tygh::$app['view']->assign('group_data', $group_data);

    //グループの投稿データを取得
    $params['post_type'] = 'G';//G: グループの投稿
    $params['object_id'] = $group_id;
    $params['disp_like'] = true;//いいねボタン
    [$user_posts, $search] = fn_bbcmm_get_user_posts($params, Registry::get('settings.Appearance.elements_per_page'));
    Tygh::$app['view']->assign('user_posts', $user_posts);
    Tygh::$app['view']->assign('search', $search);


    //グループのメンバーデータを取得
    $member_params['group_id'] = $group_id;
    $member_params['status'] = 'A';
    [$group_members,] = fn_bbcmm_get_group_members($member_params, 10);

    Tygh::$app['view']->assign('group_members', $group_members);


    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_home'), 'community.home');
    //bba_com.community_groups
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_groups'), 'community_groups.list');
    //bba_com.view_group
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb($group_data['group']);
}

//edit
if ($mode === 'edit') {

    $group_id = $_REQUEST['group_id'];

    //ログインしていない場合はログインページへ
    if (empty($auth['user_id'])) {
        /** @noinspection PhpUndefinedConstantInspection */
        return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    $params = $_REQUEST;

    //自分のデータを取得////////////////////////////////////////////////////
    $cp_data = fn_bbcmm_get_my_community_info();
    Tygh::$app['view']->assign('cp_data', $cp_data);
    ////////////////////////////////////////////////////////////////////////

    //ユーザーがグループに参加しているか確認
    $is_member = fn_bbcmm_is_user_in_group($group_id, $auth['user_id']);
    Tygh::$app['view']->assign('is_member', $is_member);

    //$is_memberがA以外の場合は404
    if ($is_member !== 'A') {
        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    //グループデータを取得
    $group_data = fn_bbcmm_get_group_data($group_id);
    Tygh::$app['view']->assign('group_data', $group_data);

    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_home'), 'community.home');
    //bba_com.community_groups
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_groups'), 'community_groups.list');
    //bba_com.view_group
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb($group_data['group'], 'community_groups.view?group_id=' . $group_id);

    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('edit'));
}

//create
if ($mode === 'create') {

    //ログインしていない場合はログインページへ
    if (empty($auth['user_id'])) {
        /** @noinspection PhpUndefinedConstantInspection */
        return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    $params = $_REQUEST;

    //自分のデータを取得////////////////////////////////////////////////////
    $cp_data = fn_bbcmm_get_my_community_info();
    Tygh::$app['view']->assign('cp_data', $cp_data);
    ////////////////////////////////////////////////////////////////////////

    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_home'), 'community.home');
    //bba_com.community_groups
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_groups'), 'community_groups.list');
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.create_group'));
}

//group_members_manage
if ($mode === 'group_members_manage') {

    $group_id = $_REQUEST['group_id'];

    //ログインしていない場合はログインページへ
    if (empty($auth['user_id'])) {
        /** @noinspection PhpUndefinedConstantInspection */
        return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    $params = $_REQUEST;

    //自分のデータを取得////////////////////////////////////////////////////
    $cp_data = fn_bbcmm_get_my_community_info();
    Tygh::$app['view']->assign('cp_data', $cp_data);
    ////////////////////////////////////////////////////////////////////////

    //ユーザーがグループに参加しているか確認
    $is_member = fn_bbcmm_is_user_in_group($group_id, $auth['user_id']);
    Tygh::$app['view']->assign('is_member', $is_member);


    //$is_memberがA以外の場合は404
    if ($is_member !== 'A') {
        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    //グループデータを取得
    $group_data = fn_bbcmm_get_group_data($group_id);
    Tygh::$app['view']->assign('group_data', $group_data);

    //グループのメンバーデータを取得
    $params['group_id'] = $group_id;
    [$group_members, $search] = fn_bbcmm_get_group_members($params, Registry::get('settings.Appearance.elements_per_page'));

    Tygh::$app['view']->assign('group_members', $group_members);
    Tygh::$app['view']->assign('search', $search);

    //パンくずリストを追加
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_home'), 'community.home');
    //bba_com.community_groups
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.community_groups'), 'community_groups.list');
    //bba_com.view_group
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb($group_data['group'], 'community_groups.view?group_id=' . $group_id);
    //bba_com.group_members_manage
    /** @noinspection PhpUndefinedFunctionInspection */
    fn_add_breadcrumb(__('bba_com.group_members_manage'));

}


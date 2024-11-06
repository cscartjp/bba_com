<?php
/**
 * アドオンで使用するPHP HOOK関数や独自の関数を定義するファイル
 * @noinspection PhpUndefinedClassInspection
 */

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

use Tygh\Registry;


/**
 * メールテンプレートを利用する
 * fn_set_hook('get_addons_mail_tpl', $tpl_code, $filename);
 *
 * @param string $tpl_code
 * @param string $filename
 */
function fn_bba_com_get_addons_mail_tpl(string $tpl_code, string &$filename)
{
    $templates = [
        BBA_NOTIFY_MAIL_TPL_CODE_LIKED,//いいね通知
        BBA_NOTIFY_MAIL_TPL_CODE_COMMENTED,//コメント通知
        BBA_NOTIFY_MAIL_TPL_CODE_FRIEND_REQUEST,//友達申請通知
        BBA_NOTIFY_MAIL_TPL_CODE_GROUP_POST,//グループへの投稿通知
        BBA_NOTIFY_MAIL_TPL_CODE_GROUP_JOIN,//グループへの参加通知
        BBA_NOTIFY_MAIL_TPL_CODE_DM,//DM通知
    ];

    if (!in_array($tpl_code, $templates, true)) {
        return;
    }

    $filename = Registry::get('config.dir.addons') . 'bba_com/tpl_variants/' . $tpl_code . '.php';
}


//自分のコミュニティ情報を取得する
function fn_bbcmm_get_my_community_info($redirect = true)
{

    $auth = &Tygh::$app['session']['auth'];

    if (isset($auth['cp_data'])) {
        return $auth['cp_data'];
    }

    //自分のデータを取得
    /** @noinspection PhpUndefinedFunctionInspection */
    $cp_data = db_get_row("SELECT * FROM ?:community_profiles WHERE user_id = ?i", $auth['user_id']);

    //タグ
    [$tags] = fn_get_tags(array(
        'object_type' => 'U',
        'object_id' => $auth['user_id']
    ));
    $cp_data['tags'] = $tags;


    //ブログスタートがセットされていない場合
    if ($redirect && !isset($cp_data['blog_start'])) {
        /** @noinspection PhpUndefinedFunctionInspection */
        fn_set_notification('E', __('error'), __('bba_com.no_profile_data'));
        /** @noinspection PhpUndefinedFunctionInspection */
        fn_redirect('community.edit_profile');
        exit;
    }


    //自分の画像データを取得
    $object_types = [
        'community_profile',
        'community_image_1',
        'community_image_2',
        'community_image_3'
    ];
    fn_bbcmm_get_image_pairs($auth['user_id'], $object_types, $cp_data);


    //セッションに保存
    $auth['cp_data'] = $cp_data;

    return $cp_data;
}


//?:community_user_postsテーブルからデータを取得する
//function fn_bbcmm_get_user_posts($params = []): array
//{
//    $params = array_merge([
//        'user_id' => 0,
//        'status' => 'A',
//        'sort_by' => 'timestamp',
//        'sort_order' => 'DESC',
//        'page' => 1,
//        'items_per_page' => 10,
//    ], $params);
//
//    $sql = 'SELECT * FROM ?:community_user_posts WHERE user_id = ?i AND status = ?s ORDER BY ?n ?p LIMIT ?i, ?i';
//
//    $posts = db_get_array($sql, $params['user_id'], $params['status'], $params['sort_by'], $params['sort_order'], ($params['page'] - 1) * $params['items_per_page'], $params['items_per_page']);
//
//
//    return [$posts, $params];
//}

//コミュニティプロフィールの検索
function fn_bbcmm_search_community_profiles($params = [], int $items_per_page = 0): array
{
    $auth = Tygh::$app['session']['auth'];

    $default_params = [
        'items_per_page' => $items_per_page,
        'parent_id' => 0,
        'my_user_id' => $auth['user_id'],
    ];

    /** @noinspection PhpUndefinedConstantInspection */
    if (AREA === 'C') {
        $default_params['status'] = 'A';
    }

    $params = array_merge($default_params, $params);

    $fields = ['cp.*'];

    $condition = '1';
    $join = '';
    $group_by = '';

    //自分のユーザーIDを除外する
    /** @noinspection PhpUndefinedFunctionInspection */
    $condition .= db_quote(" AND cp.user_id != ?i", $params['my_user_id']);

    //cq：検索クエリ LIKE
    if ($params['cq']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND cp.name LIKE ?l", '%' . trim($params['cq']) . '%');
    }

    ////tagがある場合?:tagsからデータを取得する
    if ($params['tag']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $tag_id = db_get_field("SELECT tag_id FROM ?:tags WHERE tag = ?s", $params['tag']);

        //$tag_idが存在しない場合は、空の配列を返す
        if (!$tag_id) {
            return [[], $params];
        }

        //$tag_idがある場合、?:tag_linksテーブルからobject_typeがU（ユーザー）のobject_idを取得する
        /** @noinspection PhpUndefinedFunctionInspection */
        $user_ids = db_get_fields("SELECT object_id FROM ?:tag_links WHERE tag_id = ?i AND object_type = ?s", $tag_id, 'U');
        
        //object_idsがある場合、?:community_profilesテーブルからuser_idがobject_idsに含まれるものを取得する
        if ($user_ids) {
            /** @noinspection PhpUndefinedFunctionInspection */
            $condition .= db_quote(" AND cp.user_id IN (?a)", $user_ids);
        }
    }


    // ソート順
    $sortings = [
        'sort_user_id' => 'cp.user_id',
    ];

    /** @noinspection PhpUndefinedFunctionInspection */
    $sorting = db_sort($params, $sortings, 'sort_user_id', 'asc');

    $fields = implode(',', $fields);

    $limit = '';
    if (!empty($params['items_per_page'])) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:community_profiles AS cp ?p WHERE ?p", $join, $condition);
        /** @noinspection PhpUndefinedFunctionInspection */
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }

    /** @noinspection PhpUndefinedFunctionInspection */
    $community_profiles = db_get_array("SELECT $fields FROM ?:community_profiles AS cp ?p WHERE ?p ?p ?p", $join, $condition, $group_by, $sorting, $limit);

    //$community_profilesをforeachで回し、user_idが1以上のものを取得する。またnameが空のものは除外する
    $community_profiles = array_filter($community_profiles, static function ($community_profile) {
        return $community_profile['user_id'] > 0 && $community_profile['name'];
    });


    //プロフィールアイコン取得
    $community_profile_images = [];
    foreach ($community_profiles as &$community_profile) {
        //user_idからアイコン画像を取得する
        /** @noinspection PhpUndefinedFunctionInspection */
        $community_profile_images[$community_profile['user_id']] = fn_get_image_pairs($community_profile['user_id'], 'community_profile', 'M', true, true, CART_LANGUAGE);
        $community_profile['profile_image'] = $community_profile_images[$community_profile['user_id']];
    }


//    if (defined('DEVELOPMENT')) {
//        fn_lcjp_dev_notify([
//            $community_profiles, $params
//        ]);
//    }


    return [$community_profiles, $params];
}


//?:community_user_postsテーブルからデータを取得する
function fn_bbcmm_get_user_posts(array $params = [], int $items_per_page = 0): array
{
    $auth = Tygh::$app['session']['auth'];

    $default_params = [
        'items_per_page' => $items_per_page,
        'parent_id' => 0,
//        'user_id' => $auth['user_id'],
    ];

    /** @noinspection PhpUndefinedConstantInspection */
    if (AREA === 'C') {
        $default_params['status'] = 'A';
    }
    $params = array_merge($default_params, $params);

    $fields = ['up.*'];

    $condition = '1';

//    if ($params['parent_id']) {}
    /** @noinspection PhpUndefinedFunctionInspection */
    $condition .= db_quote(" AND up.parent_id = ?i", $params['parent_id']);

    //object_idがある場合
    if ($params['object_id']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND up.object_id = ?i", $params['object_id']);
    }

    //cq：検索クエリ LIKE
    if ($params['cq']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND up.article LIKE ?l", '%' . trim($params['cq']) . '%');
    }


    //投稿タイプ
    if ($params['post_type']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND up.post_type = ?s", $params['post_type']);
    }

    //user_idがある場合
    if ($params['user_id']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND up.user_id = ?i", $params['user_id']);
    }


    //$params['friend_ids']がある場合
    if ($params['friend_ids']) {

        //$params['friend_ids']に自分のIDを追加する
        $params['friend_ids'][] = $auth['user_id'];

        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND up.user_id IN (?a)", $params['friend_ids']);
    }

    if ($params['status']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND up.status = ?s", $params['status']);
    }

    $join = '';
    $group_by = '';

    //?:community_profilesをJOINして、ユーザー名を取得する
    /** @noinspection PhpUndefinedFunctionInspection */
    $join .= db_quote(" LEFT JOIN ?:community_profiles AS cp ON up.user_id = cp.user_id");
    $fields[] = 'cp.name AS poster_name';


    // ソート順
    $sortings = [
        'sort_timestamp' => 'up.timestamp',
    ];

    //親投稿の場合
//    if ($params['disp_like'] === true && $params['post_type'] === 'T' && $params['parent_id'] === 0) {
    if ($params['disp_like'] === true && $params['parent_id'] === 0) {
        //タイムラインの場合のみuser_id
        if ($params['user_id']) {
            /** @noinspection PhpUndefinedFunctionInspection */
            $condition .= db_quote(" AND up.user_id = ?i", $params['user_id']);
        }


        //いいね数を取得する
        /** @noinspection PhpUndefinedFunctionInspection */
        $join .= db_quote(" LEFT JOIN ?:community_user_post_likes AS upl ON up.post_id = upl.post_id");

        //いいね数を取得する
        $fields[] = 'COUNT(DISTINCT upl.like_id) AS likes_count';
        $group_by = 'GROUP BY up.post_id';

        //自分()のいいねがあるかどうか
        /** @noinspection PhpUndefinedFunctionInspection */
        $fields[] = db_quote("IFNULL(SUM(upl.user_id = ?i), 0) AS is_liked", $auth['user_id']);//自分()のいいねがあるかどうか
    }


    //post_typeがC：コメントの場合は、ソートを降順にする
    if ($params['post_type'] === 'C') {
        /** @noinspection PhpUndefinedFunctionInspection */
        $sorting = db_sort($params, $sortings, 'sort_timestamp', 'asc');
    } else {
        /** @noinspection PhpUndefinedFunctionInspection */
        $sorting = db_sort($params, $sortings, 'sort_timestamp', 'desc');
    }


    $fields = implode(',', $fields);


    $limit = '';
    if (!empty($params['items_per_page'])) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:community_user_posts AS up WHERE ?p", $condition);
        /** @noinspection PhpUndefinedFunctionInspection */
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }
    /** @noinspection PhpUndefinedFunctionInspection */
    $user_posts = db_get_array("SELECT $fields FROM ?:community_user_posts AS up $join WHERE ?p ?p ?p ?p", $condition, $group_by, $sorting, $limit);


    //プロフィールアイコン取得
    $community_profile_images = [];
    foreach ($user_posts as &$user_post) {

        if (!$community_profile_images[$user_post['user_id']]) {
            //user_idからアイコン画像を取得する
            /** @noinspection PhpUndefinedFunctionInspection */
            $community_profile_images[$user_post['user_id']] = fn_get_image_pairs($user_post['user_id'], 'community_profile', 'M', true, true, CART_LANGUAGE);
        }
        $user_post['profile_image'] = $community_profile_images[$user_post['user_id']];


        //投稿内容を改行をBRタグに変換する、URLをリンクに変換し、OGP情報を取得する
        //親記事の場合
//        if ($params['post_type'] === 'T' && $params['parent_id'] === 0) {
        if ($params['parent_id'] === 0) {
            fn_bbcmm_format_post($user_post);


            //TODO コメントを取得する
            $user_comments = fn_bbcmm_get_user_comments($user_post['post_id']);
            if ($user_comments) {
                $user_post['comments'] = $user_comments;
            }
        }
    }

    return [$user_posts, $params];
}

//コメントを取得する（最大3件）
function fn_bbcmm_get_user_comments($parent_id, $max = 3)
{
    $params = [
        'items_per_page' => $max,
        'parent_id' => $parent_id,
        'post_type' => 'C',//C: コメント
    ];

    [$user_comments, $search] = fn_bbcmm_get_user_posts($params, $max);

    //articleを整形する
    foreach ($user_comments as &$user_comment) {
        //URLをリンクに変換する
        fn_bbcmm_format_post($user_comment, false);

        //mb_strimwidth
//        $user_comment['article'] = mb_strimwidth($user_comment['article'], 0, 120, '...', 'UTF-8');
    }

    return $user_comments;
}


//友達情報を取得する
//$user_idは、自分のユーザーID
function fn_bbcmm_get_friends($user_id, $max = 5)
{
    $fields = [
        'r.friend_id',
        'cp.name',
        'cp.company_name',
    ];

    /** @noinspection PhpUndefinedFunctionInspection */
    $join = db_quote(" LEFT JOIN ?:community_profiles AS cp ON r.friend_id = cp.user_id");

    /** @noinspection PhpUndefinedFunctionInspection */
    $condition = db_quote("r.user_id = ?i", $user_id);

    $limit = '';
    if ($max > 0) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $limit = db_quote("LIMIT 0, ?i", $max);
    }
    /** @noinspection PhpUndefinedFunctionInspection */
    $friends = db_get_array("SELECT ?p FROM ?:community_relationships AS r ?p WHERE ?p ?p", implode(',', $fields), $join, $condition, $limit);

    foreach ($friends as &$friend) {
        //user_idからアイコン画像を取得する
        /** @noinspection PhpUndefinedFunctionInspection */
        $friend['profile_image'] = fn_get_image_pairs($friend['friend_id'], 'community_profile', 'M', true, true, CART_LANGUAGE);
    }

    return $friends;
}


//グループ一覧を取得する
function fn_bbcmm_get_groups($params = [], int $items_per_page = 0): array
{
    $auth = Tygh::$app['session']['auth'];

    $default_params = [
        'items_per_page' => $items_per_page,
//        'my_user_id' => $auth['user_id'],
    ];

    /** @noinspection PhpUndefinedConstantInspection */
    if (AREA === 'C') {
        $default_params['status'] = 'A';
    }

    $params = array_merge($default_params, $params);

    $fields = ['cg.*'];

    $condition = '1';

    //cq：検索クエリ LIKE
    if ($params['cq']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND cg.name LIKE ?l", '%' . trim($params['cq']) . '%');
    }

    $join = '';
    $group_by = '';

    // ソート順
    $sortings = [
        'sort_group_id' => 'cg.group_id',
    ];

    /** @noinspection PhpUndefinedFunctionInspection */
    $sorting = db_sort($params, $sortings, 'sort_group_id', 'desc');

    $fields = implode(',', $fields);

    $limit = '';
    if (!empty($params['items_per_page'])) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:community_groups AS cg $join WHERE ?p", $condition);
        /** @noinspection PhpUndefinedFunctionInspection */
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }

    /** @noinspection PhpUndefinedFunctionInspection */
    $groups = db_get_array("SELECT $fields FROM ?:community_groups AS cg $join WHERE ?p ?p ?p", $condition, $group_by, $sorting, $limit);

    //グループアイコン取得
    $group_images = [];
    foreach ($groups as &$group) {
        //group_idからアイコン画像を取得する
        /** @noinspection PhpUndefinedFunctionInspection */
        $group_images[$group['group_id']] = fn_get_image_pairs($group['group_id'], 'group_icon', 'M', true, true, CART_LANGUAGE);
        $group['group_icon'] = $group_images[$group['group_id']];
    }

    return [$groups, $params];
}

//fn_bbcmm_get_group_data は、引数（グループID）からグループに関する情報を取得する
function fn_bbcmm_get_group_data(int $group_id): array
{
    $fields = [
        'cg.*',
    ];

    /** @noinspection PhpUndefinedFunctionInspection */
    $condition = db_quote("group_id = ?i", $group_id);

    /** @noinspection PhpUndefinedConstantInspection */
    if (AREA === 'C') {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND status = ?s", 'A');
    }

    $fields = implode(',', $fields);

    /** @noinspection PhpUndefinedFunctionInspection */
    $group_data = db_get_row("SELECT $fields FROM ?:community_groups AS cg WHERE 1 AND ?p", $condition);

    //グループアイコン取得
    fn_bbcmm_get_image_pairs($group_id, ['group_icon'], $group_data);

    return $group_data;
}

//ユーザーがグループに参加しているかどうかを取得する
function fn_bbcmm_is_user_in_group(int $group_id, int $user_id)
{
    //グループのタイプ
    /** @noinspection PhpUndefinedFunctionInspection */
    $group_type = db_get_field("SELECT type FROM ?:community_groups WHERE group_id = ?i", $group_id);

    /** @noinspection PhpUndefinedFunctionInspection */
    $condition = db_quote(" AND group_id = ?i", $group_id);
    /** @noinspection PhpUndefinedFunctionInspection */
    $condition .= db_quote(" AND user_id = ?i", $user_id);

    //グループのタイプが「I: 招待制」ではない場合
    if ($group_type !== 'I') {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND status = ?s", 'A');
    }

    /** @noinspection PhpUndefinedFunctionInspection */
    $group_data = db_get_row("SELECT role, status FROM ?:community_group_members WHERE 1 $condition", $group_id, $user_id);
    $role = $group_data['role'];
    $status = $group_data['status'];

    if (!$role) {
        return false;
    }

    //グループのタイプが「I: 招待制」の場合
    if ($group_type === 'I' && $status === 'P') {
        return 'P';
    }

    return $role;
}

//グループのメンバーデータを取得
function fn_bbcmm_get_group_members(array $params = [], int $items_per_page = 0): array
{
    $group_id = $params['group_id'];
    //$group_idがない場合は、空の配列を返す
    if (!$group_id) {
        return [[], $params];
    }

    $default_params = [
        'items_per_page' => $items_per_page,
    ];

    $params = array_merge($default_params, $params);

    $fields = [
        'cgm.*',
        'cp.name',
        'cp.company_name',
        'u.email',
    ];

    /** @noinspection PhpUndefinedFunctionInspection */
    $join = db_quote(" LEFT JOIN ?:community_profiles AS cp ON cgm.user_id = cp.user_id");
    //?:usersテーブルをJOINして、メールアドレスを取得する
    /** @noinspection PhpUndefinedFunctionInspection */
    $join .= db_quote(" LEFT JOIN ?:users AS u ON cgm.user_id = u.user_id");

    /** @noinspection PhpUndefinedFunctionInspection */
    $condition = db_quote("cgm.group_id = ?i", $group_id);

    //exclude_user_idがある場合
    if ($params['exclude_user_id']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND cgm.user_id != ?i", $params['exclude_user_id']);
    }

    $limit = '';
    if ($params['items_per_page'] > 0) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $limit = db_quote("LIMIT 0, ?i", $params['items_per_page']);
    }

    $fields = implode(',', $fields);

    /** @noinspection PhpUndefinedFunctionInspection */
    $group_members = db_get_array("SELECT ?p FROM ?:community_group_members AS cgm ?p WHERE ?p ?p", $fields, $join, $condition, $limit);

    //グループメンバーのアイコン取得
    $group_member_images = [];
    foreach ($group_members as &$group_member) {
        //user_idからアイコン画像を取得する
        /** @noinspection PhpUndefinedFunctionInspection */
        $group_member_images[$group_member['user_id']] = fn_get_image_pairs($group_member['user_id'], 'community_profile', 'M', true, true, CART_LANGUAGE);
        $group_member['profile_image'] = $group_member_images[$group_member['user_id']];
    }

    return [$group_members, $params];
}


//DMデータを取得 引数：$direct_mail_id
function fn_bbcmm_get_direct_mail_data(int $direct_mail_id, $params = []): array
{

    //自分のユーザーID
    $my_id = Tygh::$app['session']['auth']['user_id'];

    /** @noinspection PhpUndefinedFunctionInspection */
    $dm_data = db_get_row("SELECT * FROM ?:community_direct_mails WHERE direct_mail_id = ?i", $direct_mail_id);

    //$my_idとfrom_user_idまたはto_user_idが一致しない場合は、404
    if ($dm_data['from_user_id'] !== $my_id && $dm_data['to_user_id'] !== $my_id) {
        /** @noinspection PhpUndefinedConstantInspection */
        return [CONTROLLER_STATUS_NO_PAGE];
    }


    //相手のuser_id
    $to_user_id = $dm_data['from_user_id'] === $my_id ? $dm_data['to_user_id'] : $dm_data['from_user_id'];

    $is_from_me = false;
    //$my_idとfrom_user_idが一致する場合
    if ($dm_data['from_user_id'] === $my_id) {
        $is_from_me = true;
    }
    //$my_idとto_user_idが一致する場合
    if ($dm_data['to_user_id'] === $my_id) {
        $is_from_me = false;
    }


    //default_params
    $default_params = [
        'parent_id' => 0,
    ];

    $params = array_merge($default_params, $params);


    $fields = [
        'dm.*',
        'tou.name AS to_user_name',
    ];

    $join = '';

    //from_user_idが自分の場合
    if (!$is_from_me) {
        //相手のID：to_user_idでJOINする
        /** @noinspection PhpUndefinedFunctionInspection */
        $join = db_quote(" LEFT JOIN ?:community_profiles AS tou ON dm.to_user_id = tou.user_id");
    } else {
        //相手のID：from_user_idでJOINする
        /** @noinspection PhpUndefinedFunctionInspection */
        $join = db_quote(" LEFT JOIN ?:community_profiles AS tou ON dm.from_user_id = tou.user_id");
    }


    /** @noinspection PhpUndefinedFunctionInspection */
    $condition = db_quote(" AND dm.direct_mail_id = ?i", $direct_mail_id);

    //$params['parent_id']がある場合
    if ($params['parent_id']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND dm.parent_id = ?i", $params['parent_id']);
    }

    /** @noinspection PhpUndefinedConstantInspection */
//    if (AREA === 'C') {
//        /** @noinspection PhpUndefinedFunctionInspection */
//        $condition .= db_quote(" AND dm.from_user_id = ?i", Tygh::$app['session']['auth']['user_id']);
//    }

    $fields = implode(',', $fields);

    /** @noinspection PhpUndefinedFunctionInspection */
    $direct_mail_data = db_get_row("SELECT ?p FROM ?:community_direct_mails AS dm ?p WHERE 1 ?p", $fields, $join, $condition);

    //to_userの画像を取得
    fn_bbcmm_get_image_pairs($to_user_id, ['community_profile'], $direct_mail_data);


    return $direct_mail_data;
}


//親投稿のデータを整形する
function fn_bbcmm_format_post(&$parent_post, $ogp = true)
{
    //投稿内容を改行をBRタグに変換する、URLをリンクに変換し、OGP情報を取得する
    [$article, $urls] = fn_bbcmm_convert_post_content($parent_post['article']);
    $parent_post['article'] = $article;
    $parent_post['url'] = $urls[0][0];

    if ($parent_post['url'] && $ogp) {
        //$parent_post['url']からOGP画像情報を取得する
        $parent_post['ogp_info'] = fn_bbcmm_get_ogp_info($parent_post['url']);
    }

//    return $parent_post;
}


//引数（投稿内容）を改行をBRタグに変換する、URLをリンクに変換する
function fn_bbcmm_convert_post_content($str): array
{
    $str = trim($str);

    //改行をBRタグに変換
    $str = nl2br($str);

    //$strに含まれるURLを抽出する
    $urls = [];
    preg_match_all('/(https?:\/\/[a-zA-Z0-9\.\-\/\?\&\=\_\%\#\~\:\;\,\@\!\+\*]+)/', $str, $urls);

    //URLをリンクに変換
    $str = preg_replace('/(https?:\/\/[a-zA-Z0-9\.\-\/\?\&\=\_\%\#\~\:\;\,\@\!\+\*]+)/', '<i class="ty-icon ty-icon-popup"></i><a href="$1" target="_blank">$1</a>', $str);

    return [$str, $urls];
}

//fn_bbcmm_get_ogp_info は、引数（URL）からOGPに関する情報を取得する(image, title, description, link)
function fn_bbcmm_get_ogp_info($url): array
{
    $ogp = [
        'image' => '',
        'title' => '',
        'description' => '',
        'link' => $url,
    ];

    $html = @file_get_contents($url);
    if (!$html) {
        return [];
    }


    $doc = new DOMDocument();
    @$doc->loadHTML($html);

    $xpath = new DOMXPath($doc);

    //OGP画像
    $ogp_image = $xpath->query('//meta[@property="og:image"]/@content');
    if ($ogp_image->length > 0) {
        $ogp['image'] = $ogp_image->item(0)->nodeValue;
    }

    //OGPタイトル
    $ogp_title = $xpath->query('//meta[@property="og:title"]/@content');
    if ($ogp_title->length > 0) {
        $ogp['title'] = $ogp_title->item(0)->nodeValue;
    }

    //OGPディスクリプション
    $ogp_description = $xpath->query('//meta[@property="og:description"]/@content');
    if ($ogp_description->length > 0) {
        $ogp['description'] = $ogp_description->item(0)->nodeValue;
    }

    return $ogp;
}


//指定した画像を取得する
function fn_bbcmm_get_image_pairs(int $object_id, array $object_types, array &$profile_data): void
{
    foreach ($object_types as $object_type) {
        /** @noinspection PhpUndefinedFunctionInspection */
        /** @noinspection PhpUndefinedConstantInspection */
        $image_data = fn_get_image_pairs($object_id, $object_type, 'M', true, true, CART_LANGUAGE);
        if ($image_data) {
            $profile_data[$object_type] = $image_data;
        }
    }
}

//指定した画像を保存する
function fn_bbcmm_attach_image_pairs(int $object_id, array $object_types): void
{
    foreach ($object_types as $object_type) {
        /** @noinspection PhpUndefinedFunctionInspection */
        /** @noinspection PhpUndefinedConstantInspection */
        fn_attach_image_pairs($object_type, $object_type, $object_id, CART_LANGUAGE);
    }
}


//引数（カナ）をひらがなに変換する
function fn_bbcmm_convert_kana($str): string
{
    $str = trim($str);

    return mb_convert_kana($str, 'c', 'UTF-8');
}

//現在時刻をハッシュにして返す
function fn_bbcmm_get_hashed_time(): string
{
    return md5(date('Y-m-d H:i:00'));
}

//引数（日付）をフォーマットする SNSのようにX時間前、X日前、X週間前、Xヶ月前、X年前のような形式にする
/** @noinspection PhpUndefinedFunctionInspection */
function fn_bbcmm_format_date($date): string
{
    $date = strtotime($date);
    $now = time();
    $diff = $now - $date;

    if ($diff < 60) {
        return __('bba_com.just_now');
    }

    $diff = floor($diff / 60);
    if ($diff < 60) {
        return __('bba_com.minutes_ago', ['[diff]' => $diff]);
    }

    $diff = floor($diff / 60);
    if ($diff < 24) {
        return __('bba_com.hours_ago', ['[diff]' => $diff]);
    }

    $diff = floor($diff / 24);
    if ($diff < 7) {
        return __('bba_com.days_ago', ['[diff]' => $diff]);
    }

    $diff = floor($diff / 7);
    if ($diff < 4) {
        return __('bba_com.weeks_ago', ['[diff]' => $diff]);
    }

    $diff = floor($diff / 4);
    if ($diff < 12) {
        return __('bba_com.months_ago', ['[diff]' => $diff]);
    }

    $diff = floor($diff / 12);
    return __('bba_com.years_ago', ['[diff]' => $diff]);
}


////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////

//投稿データを取得する
function fn_bbcmm_get_user_post_data(int $post_id): array
{
    //$post_idから投稿データを取得する ?:community_user_posts
    $fields = [
        'up.*',
        'u.user_id',
        'u.email',
        'u.company_id',
    ];
    //?:usersテーブルをJOINして、メールアドレスを取得する
    $join = ' LEFT JOIN ?:users AS u ON up.user_id = u.user_id';

    $condition = '1';
    /** @noinspection PhpUndefinedFunctionInspection */
    $condition .= db_quote(" AND up.post_id = ?i", $post_id);


    //投稿データを取得する
    /** @noinspection PhpUndefinedFunctionInspection */
    return db_get_row("SELECT ?p FROM ?:community_user_posts AS up ?p WHERE ?p", implode(',', $fields), $join, $condition);
}


/**
 * いいね！をメールで通知する
 *
 * @param $user_id
 * @param $otp
 *
 * @return bool
 * @noinspection PhpUndefinedConstantInspection
 */
function fn_bbcmm_send_like_notify($post_id): bool
{

    $event_dispatcher = Tygh::$app['event.dispatcher'];
    $storefront = Tygh::$app['storefront'];
    $storefront_id = $storefront->storefront_id;

    /** @noinspection PhpUndefinedConstantInspection */
    $lang_code = (AREA === 'A' && !empty($user_data['lang_code'])) ? $user_data['lang_code'] : CART_LANGUAGE;

    $date_format = Registry::get('settings.Appearance.date_format') . ' ' . Registry::get('settings.Appearance.time_format');
    //投稿データを取得する
    $post_data = fn_bbcmm_get_user_post_data($post_id);

    //$post_dataがない場合は、falseを返す
    if (!$post_data) {
        return false;
    }

    $post_type = $post_data['post_type'];

    $notify_to = '';
    //$post_typeごとにnotify_liked_urlを設定する
    //T: タイムライン、C: コメント G: グループ
    if ($post_type === 'T') {
        $notify_to = 'community.TIMELINE?post_id=' . $post_id;
    } elseif ($post_type === 'C') {
        $notify_to = 'community.COMMENT?post_id=' . $post_id;
    } elseif ($post_type === 'G') {
        $notify_to = 'community.GROUP?post_id=' . $post_id;
    }

    /** @noinspection PhpUndefinedFunctionInspection */
    $notify_liked_url = fn_url($notify_to);


    //メールで送信する
    /** @noinspection PhpUndefinedFunctionInspection */
    $email_data = [
        'email' => $post_data['email'],
        'post_id' => $post_id,
        'post_type' => $post_type,
        'notify_liked_url' => $notify_liked_url,
        'lang_code' => $lang_code,
        'storefront_id' => $storefront_id,
        'company_id' => $post_data['company_id'],
        'like_time' => fn_date_format(TIME, $date_format),
    ];

    // Emailで通知
    $event_dispatcher->dispatch(
        BBA_NOTIFY_ID_LIKED,
        ['email_data' => $email_data]
    );

    return true;
}

/**
 * コメントをメールで通知する
 *
 * @param int $post_id The ID of the post for which the comment notification should be sent.
 *
 * @return bool Returns true if the notification was successfully sent; false otherwise.
 */
function fn_bbcmm_send_comment_notify(int $post_id): bool
{
    $event_dispatcher = Tygh::$app['event.dispatcher'];
    $storefront = Tygh::$app['storefront'];
    $storefront_id = $storefront->storefront_id;

    /** @noinspection PhpUndefinedConstantInspection */
    $lang_code = (AREA === 'A' && !empty($user_data['lang_code'])) ? $user_data['lang_code'] : CART_LANGUAGE;

    $date_format = Registry::get('settings.Appearance.date_format') . ' ' . Registry::get('settings.Appearance.time_format');
    //投稿データを取得する
    $post_data = fn_bbcmm_get_user_post_data($post_id);

    //$post_dataがない場合は、falseを返す
    if (!$post_data) {
        return false;
    }

    $post_type = $post_data['post_type'];

    $notify_to = '';
    //$post_typeごとにnotify_liked_urlを設定する
    //T: タイムライン、C: コメント G: グループ
    if ($post_type === 'T') {
        $notify_to = 'community.TIMELINE?post_id=' . $post_id;
    } elseif ($post_type === 'C') {
        $notify_to = 'community.COMMENT?post_id=' . $post_id;
    } elseif ($post_type === 'G') {
        $notify_to = 'community.GROUP?post_id=' . $post_id;
    }

    /** @noinspection PhpUndefinedFunctionInspection */
    $notify_liked_url = fn_url($notify_to);


    //メールで送信する
    /** @noinspection PhpUndefinedFunctionInspection */
    $email_data = [
        'email' => $post_data['email'],
        'post_id' => $post_id,
        'post_type' => $post_type,
        'notify_liked_url' => $notify_liked_url,
        'lang_code' => $lang_code,
        'storefront_id' => $storefront_id,
        'company_id' => $post_data['company_id'],
        'comment_time' => fn_date_format(TIME, $date_format),
    ];

//    //logに保存
//    //FIXME ログに保存////////////////////////////
//    $update_log = [
//        'url' => 'fn_bbcmm_send_comment_notify',
//        'data' => var_export($email_data, true),
//        'response' => var_export($post_data, true),
//    ];
//    /** @noinspection PhpUndefinedFunctionInspection */
//    fn_log_event('requests', 'http', $update_log);
//    //FIXME ログに保存////////////////////////////

    // Emailで通知
    $event_dispatcher->dispatch(
        BBA_NOTIFY_ID_COMMENTED,
        ['email_data' => $email_data]
    );

    return true;
}

//グループへの投稿通知を送信する
function fn_bbcmm_send_group_post_notify(int $group_id, $post_id, $my_user_id = null): bool
{
    $event_dispatcher = Tygh::$app['event.dispatcher'];
    $storefront = Tygh::$app['storefront'];
    $storefront_id = $storefront->storefront_id;

    $date_format = Registry::get('settings.Appearance.date_format') . ' ' . Registry::get('settings.Appearance.time_format');

    //投稿データを取得する
    $post_data = fn_bbcmm_get_user_post_data($post_id);

    //グループデータを取得する
    $group_data = fn_bbcmm_get_group_data($group_id);


    //TODO グループに投稿があった場合は、グループメンバーに通知する
    //$group_idからグループメンバーを取得
    $group_member_params = [
        'group_id' => $group_id,
        'status' => 'A',
    ];

    if ($my_user_id) {
        $group_member_params['exclude_user_id'] = $my_user_id;
    }

    [$group_members,] = fn_bbcmm_get_group_members($group_member_params);
    $emails = array_column($group_members, 'email');

    //$emailsがない場合は、falseを返す
    if (!$emails) {
        return false;
    }

    /** @noinspection PhpUndefinedConstantInspection */
    $lang_code = (AREA === 'A' && !empty($post_data['lang_code'])) ? $post_data['lang_code'] : CART_LANGUAGE;

    /** @noinspection PhpUndefinedFunctionInspection */
    $notify_group_url = fn_url('community_groups.view&group_id' . $group_id);

    //メールで送信する
    foreach ($emails as $email) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $email_data = [
            'email' => $email,
            'group_id' => $group_id,
            'group_name' => $group_data['group'],
            'post_id' => $post_id,
            'notify_group_url' => $notify_group_url,
            'lang_code' => $lang_code,
            'storefront_id' => $storefront_id,
            'company_id' => $post_data['company_id'],
            'post_time' => fn_date_format(TIME, $date_format),
            'post_name' => $post_data['name'],
        ];

        // Emailで通知
        $event_dispatcher->dispatch(
            BBA_NOTIFY_ID_GROUP_POST,
            ['email_data' => $email_data]
        );
    }
    return true;
}

//user_idからユーザー情報を取得する
function fn_bbcmm_get_user_info(int $user_id): array
{
    $fields = [
        'cp.*',
        'u.email',
        'u.lang_code',
    ];

    //usersテーブルからメールアドレスを取得する（JOIN）
    $join = ' LEFT JOIN ?:users AS u ON u.user_id = cp.user_id';

    $condition = '1';
    /** @noinspection PhpUndefinedFunctionInspection */
    $condition .= db_quote(" AND cp.user_id = ?i", $user_id);


    //    ?:community_profiles
    /** @noinspection PhpUndefinedFunctionInspection */
    return db_get_row("SELECT ?p FROM ?:community_profiles AS cp ?p WHERE ?p", implode(',', $fields), $join, $condition);
}


//友達申請をメールで通知する
//受け取った相手側のメールアドレスと、友達申請を送信したユーザーの情報を取得する
/**
 * 友達申請通知を送信する
 *
 * @param int $user_id 通知を送信するユーザーのID
 * @param int $friend_id 友達申請の対象となる友達のID
 *
 * @return bool 送信が成功した場合にtrue、失敗した場合にfalseを返す
 */
function fn_bbcmm_send_friend_request_notify(int $friend_id, int $user_id): bool
{
    $event_dispatcher = Tygh::$app['event.dispatcher'];
    $storefront = Tygh::$app['storefront'];
    $storefront_id = $storefront->storefront_id;

    $date_format = Registry::get('settings.Appearance.date_format') . ' ' . Registry::get('settings.Appearance.time_format');

    //ユーザー情報を取得する(申請者)
    $user_data = fn_bbcmm_get_user_info($user_id);

    //フレンド相手の情報を取得する
    $friend_data = fn_bbcmm_get_user_info($friend_id);

    /** @noinspection PhpUndefinedConstantInspection */
    $lang_code = (AREA === 'A' && !empty($friend_data['lang_code'])) ? $friend_data['lang_code'] : CART_LANGUAGE;


    //$user_dataまたは$friend_dataがない場合は、falseを返す
    if (!$user_data || !$friend_data) {
        return false;
    }

    //メールで送信する
    /** @noinspection PhpUndefinedFunctionInspection */
    $email_data = [
        'email' => $friend_data['email'],//友達申請を受け取る相手のメールアドレス
        'friend_id' => $friend_id,//友達申請を受け取る相手のユーザーID
        'request_sender_name' => $user_data['name'],//友達申請を送信したユーザーの名前
        'request_sender_user_id' => $user_id,//友達申請を送信したユーザーのユーザーID
        'notify_friend_request_url' => fn_url('community.friends'),
        'lang_code' => $lang_code,
        'storefront_id' => $storefront_id,
        'company_id' => $user_data['company_id'],
        'friend_request_time' => fn_date_format(TIME, $date_format),
        'friend_request_name' => $friend_data['name'],
    ];

    // Emailで通知
    $event_dispatcher->dispatch(
        BBA_NOTIFY_ID_FRIEND_REQUEST,
        ['email_data' => $email_data]
    );

    return true;
}

//TODO グループへの参加通知を送信する(グループタイプがIの場合は管理者：create_user_idに通知)
function fn_bbcmm_send_group_join_notify(int $group_id): bool
{
    $event_dispatcher = Tygh::$app['event.dispatcher'];
    $storefront = Tygh::$app['storefront'];
    $storefront_id = $storefront->storefront_id;

    $date_format = Registry::get('settings.Appearance.date_format') . ' ' . Registry::get('settings.Appearance.time_format');


    //グループデータを取得する
    $group_data = fn_bbcmm_get_group_data($group_id);
    $create_user_id = $group_data['create_user_id'];

    //ユーザー（このグループの管理者）情報を取得する
    $user_data = fn_bbcmm_get_user_info($create_user_id);

    /** @noinspection PhpUndefinedConstantInspection */
    $lang_code = (AREA === 'A' && !empty($user_data['lang_code'])) ? $user_data['lang_code'] : CART_LANGUAGE;

    //$user_dataまたは$group_dataがない場合は、falseを返す
    if (!$user_data || !$group_data) {
        return false;
    }

    /** @noinspection PhpUndefinedFunctionInspection */
    $notify_group_url = fn_url('community_groups.view&group_id' . $group_id);


    //メールで送信する
    /** @noinspection PhpUndefinedFunctionInspection */
    $email_data = [
        'email' => $user_data['email'],
        'group_id' => $group_id,
        'group_name' => $group_data['group'],
        'notify_group_url' => $notify_group_url,
        'user_id' => $create_user_id,
        'lang_code' => $lang_code,
        'storefront_id' => $storefront_id,
        'company_id' => $user_data['company_id'],
        'group_join_time' => fn_date_format(TIME, $date_format),
        'group_join_name' => $user_data['name'],
    ];

    if (defined('DEVELOPMENT')) {
        fn_lcjp_dev_notify([
            'email_data ' => $email_data,
            'group_data' => $group_data,
            'create_user_id' => $create_user_id,
            'user_data' => $user_data,
        ]);
    }

    // Emailで通知
    $event_dispatcher->dispatch(
        BBA_NOTIFY_ID_GROUP_JOIN,
        ['email_data' => $email_data]
    );

    return true;
}


//DM通知を送信する
function fn_bbcmm_send_dm_notify(int $direct_mail_id, array $dm_data): bool
{
    $event_dispatcher = Tygh::$app['event.dispatcher'];
    $storefront = Tygh::$app['storefront'];
    $storefront_id = $storefront->storefront_id;

    $date_format = Registry::get('settings.Appearance.date_format') . ' ' . Registry::get('settings.Appearance.time_format');

    //DMデータを取得する
//    $dm_data = fn_bbcmm_get_direct_mail_data($direct_mail_id);


    //        `direct_mail_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
//        `from_user_id` mediumint(8) UNSIGNED NOT NULL,
//        `to_user_id` mediumint(8) UNSIGNED NOT NULL,
//        `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
//        `subject` varchar(128) NOT NULL DEFAULT '',
//        `message` text NOT NULL DEFAULT '',
//        `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
//        `status` char(1) NOT NULL DEFAULT 'A',


    //DMデータがない場合は、falseを返す
    if (!$dm_data) {
        return false;
    }

    //DMの送信者の情報を取得する
    $from_user_id = $dm_data['from_user_id'];
    $to_user_id = $dm_data['to_user_id'];

    $from_user_data = fn_bbcmm_get_user_info($from_user_id);
    $to_user_data = fn_bbcmm_get_user_info($to_user_id);

    //DMの受信者の情報を取得する
//    $to_user_id = $dm_data['to_user_id'];
//    $to_user_data = fn_bbcmm_get_user_info($to_user_id);

    /** @noinspection PhpUndefinedConstantInspection */
    $lang_code = (AREA === 'A' && !empty($to_user_data['lang_code'])) ? $to_user_data['lang_code'] : CART_LANGUAGE;

    //$from_user_dataまたは$to_user_dataがない場合は、falseを返す
    if (!$from_user_data || !$to_user_data) {
        return false;
    }

    /** @noinspection PhpUndefinedFunctionInspection */
    $notify_dm_url = fn_url('community_dm.view&direct_mail_id=' . $direct_mail_id);

    //メールで送信する
    /** @noinspection PhpUndefinedFunctionInspection */
    $email_data = [
        'email' => $to_user_data['email'],
        'direct_mail_id' => $direct_mail_id,
        'from_user_id' => $from_user_id,
        'sender_name' => $from_user_data['name'],
        'to_user_id' => $to_user_id,
        'lang_code' => $lang_code,
        'notify_dm_url' => $notify_dm_url,
        'storefront_id' => $storefront_id,
        'company_id' => $to_user_data['company_id'],
        'dm_time' => fn_date_format(TIME, $date_format),
        'from_user_name' => $from_user_data['name'],
    ];

    // Emailで通知
    $event_dispatcher->dispatch(
        BBA_NOTIFY_ID_DM,
        ['email_data' => $email_data]
    );

    return true;
}


////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
// インストール時
function fn_bbcmm_addon_install()
{
    //////////////////////////////////////////////
    //メールテンプレートをインストールする
    $mail_templates = [];
    $mail_template_desc = [];

    //メールテンプレートの種類を定義'
    //いいね通知
    $mail_templates[] = [
        'tpl_code' => BBA_NOTIFY_MAIL_TPL_CODE_LIKED
    ];
    //コメント通知
    $mail_templates[] = [
        'tpl_code' => BBA_NOTIFY_MAIL_TPL_CODE_COMMENTED
    ];
    //友達申請通知
    $mail_templates[] = [
        'tpl_code' => BBA_NOTIFY_MAIL_TPL_CODE_FRIEND_REQUEST
    ];
    //グループへの投稿通知
    $mail_templates[] = [
        'tpl_code' => BBA_NOTIFY_MAIL_TPL_CODE_GROUP_POST
    ];
    //グループへの参加通知
    $mail_templates[] = [
        'tpl_code' => BBA_NOTIFY_MAIL_TPL_CODE_GROUP_JOIN
    ];
    //DM通知
    $mail_templates[] = [
        'tpl_code' => BBA_NOTIFY_MAIL_TPL_CODE_DM
    ];

    //メールテンプレートの詳細を定義
    //いいね通知
    $mail_template_desc[] = [
        'tpl_name' => 'いいね通知（アドオン）',
        'tpl_trigger' => BBA_NOTIFY_MAIL_TPL_TRIGGER_LIKED,
        'subject' => '【{%SP_NAME%}】いいねがありました',
        'body_txt' => "いいねがありました。\r\n----------------------------------------\r\n{%BBA_COM_LIKE_URL%}\r\n\r\nなお、このメールに見覚えがない場合は、管理者にご確認ください。",
    ];
    //コメント通知
    $mail_template_desc[] = [
        'tpl_name' => 'コメント通知（アドオン）',
        'tpl_trigger' => BBA_NOTIFY_MAIL_TPL_TRIGGER_COMMENTED,
        'subject' => '【{%SP_NAME%}】コメントがありました',
        'body_txt' => "コメントがありました。\r\n----------------------------------------\r\n{%BBA_COM_COMMENT_URL%}\r\n\r\nなお、このメールに見覚えがない場合は、管理者にご確認ください。",
    ];
    //友達申請通知
    $mail_template_desc[] = [
        'tpl_name' => '友達申請通知（アドオン）',
        'tpl_trigger' => BBA_NOTIFY_MAIL_TPL_TRIGGER_FRIEND_REQUEST,
        'subject' => '【{%SP_NAME%}】友達申請がありました',
        'body_txt' => "友達申請がありました。\r\n----------------------------------------\r\n{%BBA_COM_FRIEND_REQUEST_URL%}\r\n\r\nなお、このメールに見覚えがない場合は、管理者にご確認ください。",
    ];
    //参加グループへの投稿通知
    $mail_template_desc[] = [
        'tpl_name' => '参加グループへの投稿通知（アドオン）',
        'tpl_trigger' => BBA_NOTIFY_MAIL_TPL_TRIGGER_GROUP_POST,
        'subject' => '【{%SP_NAME%}】参加グループへの投稿がありました',
        'body_txt' => "参加グループへの投稿がありました。\r\n----------------------------------------\r\n{%BBA_COM_GROUP_NAME%}\r\n{%BBA_COM_GROUP_URL%}\r\n\r\nなお、このメールに見覚えがない場合は、管理者にご確認ください。",
    ];
    //グループへの参加通知
    $mail_template_desc[] = [
        'tpl_name' => 'グループへの参加通知（アドオン）',
        'tpl_trigger' => BBA_NOTIFY_MAIL_TPL_TRIGGER_GROUP_JOIN,
        'subject' => '【{%SP_NAME%}】グループへの参加がありました',
        'body_txt' => "グループへの参加がありました。\r\n----------------------------------------\r\n{%BBA_COM_GROUP_NAME%}\r\n{%BBA_COM_GROUP_URL%}\r\n\r\nなお、このメールに見覚えがない場合は、管理者にご確認ください。",
    ];
    //DM通知
    $mail_template_desc[] = [
        'tpl_name' => 'DM通知（アドオン）',
        'tpl_trigger' => BBA_NOTIFY_MAIL_TPL_TRIGGER_DM,
        'subject' => '【{%SP_NAME%}】DMが届きました',
        'body_txt' => "DMが届きました。\r\n----------------------------------------\r\n{%BBA_COM_DM_SENDER_NAME%}\r\n{%BBA_COM_DM_URL%}\r\n\r\nなお、このメールに見覚えがない場合は、管理者にご確認ください。",
    ];

    // インストールされた言語を取得
    /** @noinspection PhpUndefinedFunctionInspection */
    $languages = db_get_hash_array("SELECT * FROM ?:languages", 'lang_code');

    // メールテンプレートを管理するテーブルにデータをセット
    $cnt = 0;
    foreach ($mail_templates as $_data) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $tpl_id = db_query("REPLACE INTO ?:jp_mtpl ?e", $_data);
        foreach ($languages as $lc => $_v) {
            $mail_template_desc[$cnt]['company_id'] = 1;
            $mail_template_desc[$cnt]['tpl_id'] = $tpl_id;
            $mail_template_desc[$cnt]['lang_code'] = $lc;
            /** @noinspection PhpUndefinedFunctionInspection */
            db_query("REPLACE INTO ?:jp_mtpl_descriptions ?e", $mail_template_desc[$cnt]);
        }
        $cnt++;
    }
}


// アンインストール時
function fn_bbcmm_addon_uninstall()
{
    //////////////////////////////////////////////
    //メールテンプレートをアンインストールする
    $uninstall = [
        BBA_NOTIFY_MAIL_TPL_CODE_LIKED => BBA_NOTIFY_MAIL_TPL_TRIGGER_LIKED,//いいね通知
        BBA_NOTIFY_MAIL_TPL_CODE_COMMENTED => BBA_NOTIFY_MAIL_TPL_TRIGGER_COMMENTED,//コメント通知
        BBA_NOTIFY_MAIL_TPL_CODE_FRIEND_REQUEST => BBA_NOTIFY_MAIL_TPL_TRIGGER_FRIEND_REQUEST,//友達申請通知
        BBA_NOTIFY_MAIL_TPL_CODE_GROUP_POST => BBA_NOTIFY_MAIL_TPL_TRIGGER_GROUP_POST,//参加グループへの投稿通知
        BBA_NOTIFY_MAIL_TPL_CODE_GROUP_JOIN => BBA_NOTIFY_MAIL_TPL_TRIGGER_GROUP_JOIN,//グループへの参加通知
    ];
    foreach ($uninstall as $key => $value) {
        /** @noinspection PhpUndefinedFunctionInspection */
        db_query("DELETE FROM ?:jp_mtpl WHERE tpl_code = ?s", $key);
        /** @noinspection PhpUndefinedFunctionInspection */
        db_query("DELETE FROM ?:jp_mtpl_descriptions WHERE tpl_trigger = ?s", $value);
    }
    //////////////////////////////////////////////
}

<?php
/**
 * アドオンで使用するPHP HOOK関数や独自の関数を定義するファイル
 * @noinspection PhpUndefinedClassInspection
 */

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

use Tygh\Registry;


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

    //自分のユーザーIDを除外する
    /** @noinspection PhpUndefinedFunctionInspection */
    $condition .= db_quote(" AND cp.user_id != ?i", $params['my_user_id']);

    //cq：検索クエリ LIKE
    if ($params['cq']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND cp.name LIKE ?l", '%' . trim($params['cq']) . '%');
    }

    $join = '';
    $group_by = '';

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
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:community_profiles AS cp WHERE ?p", $condition);
        /** @noinspection PhpUndefinedFunctionInspection */
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }

    /** @noinspection PhpUndefinedFunctionInspection */
    $community_profiles = db_get_array("SELECT $fields FROM ?:community_profiles AS cp WHERE ?p ?p ?p", $condition, $group_by, $sorting, $limit);

    //プロフィールアイコン取得
    $community_profile_images = [];
    foreach ($community_profiles as &$community_profile) {
        //user_idからアイコン画像を取得する
        /** @noinspection PhpUndefinedFunctionInspection */
        $community_profile_images[$community_profile['user_id']] = fn_get_image_pairs($community_profile['user_id'], 'community_profile', 'M', true, true, CART_LANGUAGE);
        $community_profile['profile_image'] = $community_profile_images[$community_profile['user_id']];
    }

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

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
    if ($redirect && $cp_data['blog_start'] === '0000-00-00') {
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

//    $params['parent_id'] = $params['parent_id'] ?? 0;

    $fields = ['up.*'];

    $condition = '1';

//    if ($params['parent_id']) {}
    /** @noinspection PhpUndefinedFunctionInspection */
    $condition .= db_quote(" AND up.parent_id = ?i", $params['parent_id']);


    //
    if ($params['post_type']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND up.post_type = ?s", $params['post_type']);
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

    //T：タイムラインに投稿した場合(親投稿)
    if ($params['disp_like'] === true && $params['post_type'] === 'T' && $params['parent_id'] === 0) {


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
//        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:community_user_posts AS up $join WHERE ?p ?p ?p", $condition, $group_by, $limit);
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
        //T：タイムラインに投稿した場合
        if ($params['post_type'] === 'T' && $params['parent_id'] === 0) {
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

////プロフィールアイコン取得
//function fn_bbcmm_get_profile_image($user_posts)
//{
//    $community_profile_images = [];
//    foreach ($user_posts as &$user_post) {
//        if (!$community_profile_images[$user_post['user_id']]) {
//            //user_idからアイコン画像を取得する
//            /** @noinspection PhpUndefinedFunctionInspection */
//            $community_profile_images[$user_post['user_id']] = fn_get_image_pairs($user_post['user_id'], 'community_profile', 'M', true, true, CART_LANGUAGE);
//        }
//        $user_post['profile_image'] = $community_profile_images[$user_post['user_id']];
//    }
//
//    return $user_posts;
//}

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
function fn_bbcmm_get_friends($user_id)
{
    $fields = [
        'r.friend_id',
        'cp.name',
    ];

    $join = db_quote(" LEFT JOIN ?:community_profiles AS cp ON r.friend_id = cp.user_id");

    $condition = db_quote("r.user_id = ?i", $user_id);

    $limit = db_quote("LIMIT 0, 5");

    $friends = db_get_array("SELECT ?p FROM ?:community_relationships AS r ?p WHERE ?p ?p", implode(',', $fields), $join, $condition, $limit);

    foreach ($friends as &$friend) {
        //user_idからアイコン画像を取得する
        $friend['profile_image'] = fn_get_image_pairs($friend['friend_id'], 'community_profile', 'M', true, true, CART_LANGUAGE);
    }

    return $friends;
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

<?php
/**
 * アドオンで使用するPHP HOOK関数や独自の関数を定義するファイル
 * @noinspection PhpUndefinedClassInspection
 */

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

use Tygh\Registry;


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
    $default_params = [
        'items_per_page' => $items_per_page,
    ];

    /** @noinspection PhpUndefinedConstantInspection */
    if (AREA === 'C') {
        $default_params['status'] = 'A';
    }
    $params = array_merge($default_params, $params);

    $fields = ['up.*'];

    $condition = '1';
    if ($params['status']) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $condition .= db_quote(" AND up.status = ?s", $params['status']);
    }

    $join = '';
    $group_by = '';

    // ソート順
    $sortings = [
//        'sort_business_category' => 'up.business_category',
//        'sort_position' => 'up.position',
        'sort_timestamp' => 'up.timestamp',
    ];

//    //登録店舗数を表示する場合
//    if ($params['display_store_count'] === 'Y' || $params['display_only_registered_store'] === 'Y') {
//        $join .= db_quote(" LEFT JOIN ?:companies AS c ON up.business_category_id = c.business_category_id AND c.status = ?s", "A");
//        $fields[] = 'COUNT(DISTINCT c.company_id) AS stores_count';
//        $group_by = 'GROUP BY up.business_category_id';
//        $sortings['sort_store_count'] = 'stores_count';
//        $sorting = db_sort($params, $sortings, 'sort_store_count', 'desc');
//    } else {
//        $sorting = db_sort($params, $sortings, 'sort_position', 'asc');
//    }

    /** @noinspection PhpUndefinedFunctionInspection */
    $sorting = db_sort($params, $sortings, 'sort_timestamp', 'desc');

    $fields = implode(',', $fields);


    $limit = '';
    if (!empty($params['items_per_page'])) {
        /** @noinspection PhpUndefinedFunctionInspection */
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:community_user_posts AS up $join WHERE ?p ?p ?p", $condition, $group_by, $limit);
        /** @noinspection PhpUndefinedFunctionInspection */
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }
    /** @noinspection PhpUndefinedFunctionInspection */
    $user_posts = db_get_array("SELECT $fields FROM ?:community_user_posts AS up $join WHERE ?p ?p ?p ?p", $condition, $group_by, $sorting, $limit);

    foreach ($user_posts as &$user_post) {
        [$article, $urls] = fn_bbcmm_convert_post_content($user_post['article']);
        $user_post['article'] = $article;
        $user_post['url'] = $urls[0][0];

        if ($user_post['url']) {
            //$user_post['url']からOGP画像情報を取得する
            $user_post['ogp_info'] = fn_bbcmm_get_ogp_info($user_post['url']);
        }
    }
    

    return [$user_posts, $params];
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

    $html = file_get_contents($url);
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

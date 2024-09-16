<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

use Tygh\Tools\SecurityHelper;


//`name` varchar(64) NOT NULL DEFAULT '',
//`name_kana` varchar(64) NOT NULL DEFAULT '',
//`company_name` varchar(64) NOT NULL DEFAULT '',
//`company_position` varchar(64) NOT NULL DEFAULT '' COMMENT '役職',
//`company_postal_code` varchar(8) NOT NULL DEFAULT '',
//`company_address` varchar(128) NOT NULL DEFAULT '',
//`company_tel` varchar(16) NOT NULL DEFAULT '',
//`company_fax` varchar(16) NOT NULL DEFAULT '',
//`mobile_tel` varchar(16) NOT NULL DEFAULT '',
//`email` varchar(128) NOT NULL DEFAULT '',
//`business_category_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '業種ID',
//`business_content` text NOT NULL DEFAULT '' COMMENT '事業内容',
//`company_established_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '設立年月日',
//`company_capital` varchar(16) NOT NULL DEFAULT '' COMMENT '資本金',
//`company_employees` varchar(16) NOT NULL DEFAULT '' COMMENT '社員数',
//`catch_copy` text NOT NULL DEFAULT '',
//`bis_info` varchar(128) NOT NULL DEFAULT '',
//`blog_start` date NOT NULL DEFAULT '0000-00-00',
//`blog_url` varchar(128) NOT NULL DEFAULT '',
//`company_url` varchar(128) NOT NULL DEFAULT '',
//`facebook_url` varchar(128) NOT NULL DEFAULT '',
//`x_url` varchar(128) NOT NULL DEFAULT '',
//`instagram_url` varchar(128) NOT NULL DEFAULT '',
//`youtube_url` varchar(128) NOT NULL DEFAULT '',
//`my_profile` text NOT NULL DEFAULT '' COMMENT 'プロフィール自由記入欄',
//`status` char(1) NOT NULL DEFAULT 'A',

//プロフィールデータのサニタイズ
$schema['community_profile'] = [
    SecurityHelper::SCHEMA_SECTION_FIELD_RULES => [
        'name' => SecurityHelper::ACTION_REMOVE_HTML,
        'name_kana' => SecurityHelper::ACTION_REMOVE_HTML,
        'company_name' => SecurityHelper::ACTION_REMOVE_HTML,
        'company_position' => SecurityHelper::ACTION_REMOVE_HTML,
        'company_postal_code' => SecurityHelper::ACTION_REMOVE_HTML,
        'company_address' => SecurityHelper::ACTION_REMOVE_HTML,
        'company_tel' => SecurityHelper::ACTION_REMOVE_HTML,
        'company_fax' => SecurityHelper::ACTION_REMOVE_HTML,
        'mobile_tel' => SecurityHelper::ACTION_REMOVE_HTML,
        'email' => SecurityHelper::ACTION_REMOVE_HTML,
        'business_category_id' => SecurityHelper::ACTION_REMOVE_HTML,
        'business_content' => SecurityHelper::ACTION_SANITIZE_HTML,//textの場合
        'company_established_date' => SecurityHelper::ACTION_REMOVE_HTML,
        'company_capital' => SecurityHelper::ACTION_REMOVE_HTML,
        'company_employees' => SecurityHelper::ACTION_REMOVE_HTML,
        'catch_copy' => SecurityHelper::ACTION_SANITIZE_HTML,//textの場合
        'bis_info' => SecurityHelper::ACTION_REMOVE_HTML,
        'blog_start' => SecurityHelper::ACTION_REMOVE_HTML,
        'blog_url' => SecurityHelper::ACTION_REMOVE_HTML,
        'company_url' => SecurityHelper::ACTION_REMOVE_HTML,
        'facebook_url' => SecurityHelper::ACTION_REMOVE_HTML,
        'x_url' => SecurityHelper::ACTION_REMOVE_HTML,
        'instagram_url' => SecurityHelper::ACTION_REMOVE_HTML,
        'youtube_url' => SecurityHelper::ACTION_REMOVE_HTML,
        'my_profile' => SecurityHelper::ACTION_SANITIZE_HTML,//textの場合
//        'status' => SecurityHelper::ACTION_REMOVE_HTML,
    ]
];

//コミュニティへの投稿//community_user_posts
$schema['community_user_posts'] = [
    SecurityHelper::SCHEMA_SECTION_FIELD_RULES => [
        'cq' => SecurityHelper::ACTION_REMOVE_HTML,
        'article' => SecurityHelper::ACTION_SANITIZE_HTML
    ]
];

return $schema;

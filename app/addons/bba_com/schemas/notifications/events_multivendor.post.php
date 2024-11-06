<?php
/**
 * マルチベンダー用の通知イベントスキーマ
 * @noinspection PhpUndefinedClassInspection
 * @var $schema array
 */

use Tygh\Addons\BbaCom\Notifications\EventIdProviders\BbaComEventProvider;
use Tygh\Enum\Addons\VendorDataPremoderation\PremoderationStatuses;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\SiteArea;
use Tygh\Enum\UserTypes;
use Tygh\Notifications\DataValue;
use Tygh\Notifications\Transports\Internal\InternalTransport;
use Tygh\Notifications\Transports\Mail\MailTransport;
use Tygh\Notifications\Transports\Mail\MailMessageSchema;
use Tygh\Notifications\Transports\Internal\InternalMessageSchema;
use Tygh\NotificationsCenter\NotificationsCenter;

defined('BOOTSTRAP') or die('Access denied');

//いいね！通知
/** @noinspection PhpUndefinedConstantInspection */
$schema[BBA_NOTIFY_ID_LIKED] = [
    'group' => BBA_NOTIFY_GROUP_LIKED,
    'name' => [
        'template' => 'bba_com.event.bba_com.liked',
        'params' => [],
    ],
    'receivers' => [
        UserTypes::VENDOR => [
            //メールを送信する
            MailTransport::getId() => MailMessageSchema::create([
                'area' => SiteArea::STOREFRONT,//ADMIN_PANEL STOREFRONT
                'from' => 'company_site_administrator',
                'to' => DataValue::create('email_data.email'),
                'to_company_id' => DataValue::create('email_data.company_id'),
                'template_code' => BBA_NOTIFY_MAIL_TPL_CODE_LIKED,
                'legacy_template' => BBA_NOTIFY_MAIL_TPL_CODE_LIKED . '.tpl',
                'company_id' => 0,//VENDOR用は0
                'language_code' => DataValue::create('email_data.lang_code', CART_LANGUAGE),
            ]),
        ],
    ],
];

//コメント通知
/** @noinspection PhpUndefinedConstantInspection */
$schema[BBA_NOTIFY_ID_COMMENTED] = [
    'group' => BBA_NOTIFY_GROUP_COMMENTED,
    'name' => [
        'template' => 'bba_com.event.bba_com.commented',
        'params' => [],
    ],
    'receivers' => [
        UserTypes::VENDOR => [
            //メールを送信する
            MailTransport::getId() => MailMessageSchema::create([
                'area' => SiteArea::STOREFRONT,//ADMIN_PANEL STOREFRONT
                'from' => 'company_site_administrator',
                'to' => DataValue::create('email_data.email'),
                'to_company_id' => DataValue::create('email_data.company_id'),
                'template_code' => BBA_NOTIFY_MAIL_TPL_CODE_COMMENTED,
                'legacy_template' => BBA_NOTIFY_MAIL_TPL_CODE_COMMENTED . '.tpl',
                'company_id' => 0,
                'language_code' => DataValue::create('email_data.lang_code', CART_LANGUAGE),
            ]),
        ],
    ],
];


//define('BBA_NOTIFY_ID_FRIEND_REQUEST', 'bbacom.notify_friend_request');
//
//// Event Notificationのグループ名
//define('BBA_NOTIFY_GROUP_FRIEND_REQUEST', 'bbacom.friend_request');
//
//// メールテンプレートコード（テンプレートファイル名）
//define('BBA_NOTIFY_MAIL_TPL_CODE_FRIEND_REQUEST', 'bbac_friend_request');
//
//// メールテンプレートトリガー名
//define('BBA_NOTIFY_MAIL_TPL_TRIGGER_FRIEND_REQUEST', '友達申請通知');


//友達申請通知
/** @noinspection PhpUndefinedConstantInspection */
$schema[BBA_NOTIFY_ID_FRIEND_REQUEST] = [
    'group' => BBA_NOTIFY_GROUP_FRIEND_REQUEST,
    'name' => [
        'template' => 'bba_com.event.bba_com.friend_requested',
        'params' => [],
    ],
    'receivers' => [
        UserTypes::VENDOR => [
            //メールを送信する
            MailTransport::getId() => MailMessageSchema::create([
                'area' => SiteArea::STOREFRONT,//ADMIN_PANEL STOREFRONT
                'from' => 'company_site_administrator',
                'to' => DataValue::create('email_data.email'),
                'bcc' => '',
                'to_company_id' => DataValue::create('email_data.company_id'),
                'template_code' => BBA_NOTIFY_MAIL_TPL_CODE_FRIEND_REQUEST,
                'legacy_template' => BBA_NOTIFY_MAIL_TPL_CODE_FRIEND_REQUEST . '.tpl',
                'company_id' => 0,
                'language_code' => DataValue::create('email_data.lang_code', CART_LANGUAGE),
            ]),
        ],
    ],
];


//参加グループに投稿通知
/** @noinspection PhpUndefinedConstantInspection */
$schema[BBA_NOTIFY_ID_GROUP_POST] = [
    'group' => BBA_NOTIFY_GROUP_GROUP_POST,
    'name' => [
        'template' => 'bba_com.event.bba_com.group_posted',
        'params' => [],
    ],
    'receivers' => [
        UserTypes::VENDOR => [
            //メールを送信する
            MailTransport::getId() => MailMessageSchema::create([
                'area' => SiteArea::STOREFRONT,//ADMIN_PANEL STOREFRONT
                'from' => 'company_site_administrator',
                'to' => DataValue::create('email_data.email'),
                'to_company_id' => DataValue::create('email_data.company_id'),
                'template_code' => BBA_NOTIFY_MAIL_TPL_CODE_GROUP_POST,
                'legacy_template' => BBA_NOTIFY_MAIL_TPL_CODE_GROUP_POST . '.tpl',
                'company_id' => 0,
                'language_code' => DataValue::create('email_data.lang_code', CART_LANGUAGE),
            ]),
        ],
    ],
];

//グループへの参加通知
/** @noinspection PhpUndefinedConstantInspection */
$schema[BBA_NOTIFY_ID_GROUP_JOIN] = [
    'group' => BBA_NOTIFY_GROUP_GROUP_JOIN,
    'name' => [
        'template' => 'bba_com.event.bba_com.group_joined',
        'params' => [],
    ],
    'receivers' => [
        UserTypes::VENDOR => [
            //メールを送信する
            MailTransport::getId() => MailMessageSchema::create([
                'area' => SiteArea::STOREFRONT,//ADMIN_PANEL STOREFRONT
                'from' => 'company_site_administrator',
                'to' => DataValue::create('email_data.email'),
                'to_company_id' => DataValue::create('email_data.company_id'),
                'template_code' => BBA_NOTIFY_MAIL_TPL_CODE_GROUP_JOIN,
                'legacy_template' => BBA_NOTIFY_MAIL_TPL_CODE_GROUP_JOIN . '.tpl',
                'company_id' => 0,
                'language_code' => DataValue::create('email_data.lang_code', CART_LANGUAGE),
            ]),
        ],
    ],
];

//DM通知
/** @noinspection PhpUndefinedConstantInspection */
$schema[BBA_NOTIFY_ID_DM] = [
    'group' => BBA_NOTIFY_GROUP_DM,
    'name' => [
        'template' => 'bba_com.event.bba_com.dm',
        'params' => [],
    ],
    'receivers' => [
        UserTypes::VENDOR => [
            //メールを送信する
            MailTransport::getId() => MailMessageSchema::create([
                'area' => SiteArea::STOREFRONT,//ADMIN_PANEL STOREFRONT
                'from' => 'company_site_administrator',
                'to' => DataValue::create('email_data.email'),
                'to_company_id' => DataValue::create('email_data.company_id'),
                'template_code' => BBA_NOTIFY_MAIL_TPL_CODE_DM,
                'legacy_template' => BBA_NOTIFY_MAIL_TPL_CODE_DM . '.tpl',
                'company_id' => 0,
                'language_code' => DataValue::create('email_data.lang_code', CART_LANGUAGE),
            ]),
        ],
    ],
];


return $schema;

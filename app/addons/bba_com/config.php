<?php
/**
 * アドオンで使用する定数を定義するファイル
 * @noinspection PhpUndefinedClassInspection
 */

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

//コミュニティーのみの会社ID
define('COMMUNITY_ONLY_COMPANY_ID', 22);

////2段階認証
//// Event Notificationの識別子
//define( 'NOTIFICATION_ID_2FA', 'mkm2fa.send_otp' );
//
//// Event Notificationのグループ名
//define( 'NOTIFICATION_GROUP_2FA', 'mkm2fa.otp' );
//
//// メールテンプレートコード
//define( 'MAIL_TPL_CODE_2FA', 'mkm2fa_notification' );
//
//// メールテンプレートトリガー名
//define( 'MAIL_TPL_TRIGGER_2FA', '2段階認証' );

//////////////////////////////////////////////////////////////////////////////////////////
//いいね通知
// Event Notificationの識別子
define('BBA_NOTIFY_ID_LIKED', 'bbacom.notify_liked');

// Event Notificationのグループ名
define('BBA_NOTIFY_GROUP_LIKED', 'bbacom.liked');

// メールテンプレートコード（テンプレートファイル名）
define('BBA_NOTIFY_MAIL_TPL_CODE_LIKED', 'bbac_liked');

// メールテンプレートトリガー名
define('BBA_NOTIFY_MAIL_TPL_TRIGGER_LIKED', 'いいね通知');

//////////////////////////////////////////////////////////////////////////////////////////
//コメント通知
// Event Notificationの識別子
define('BBA_NOTIFY_ID_COMMENTED', 'bbacom.notify_commented');

// Event Notificationのグループ名
define('BBA_NOTIFY_GROUP_COMMENTED', 'bbacom.commented');

// メールテンプレートコード（テンプレートファイル名）
define('BBA_NOTIFY_MAIL_TPL_CODE_COMMENTED', 'bbac_commented');

// メールテンプレートトリガー名
define('BBA_NOTIFY_MAIL_TPL_TRIGGER_COMMENTED', 'コメント通知');

//////////////////////////////////////////////////////////////////////////////////////////
/// 友達申請通知
// Event Notificationの識別子
define('BBA_NOTIFY_ID_FRIEND_REQUEST', 'bbacom.notify_friend_request');

// Event Notificationのグループ名
define('BBA_NOTIFY_GROUP_FRIEND_REQUEST', 'bbacom.friend_request');

// メールテンプレートコード（テンプレートファイル名）
define('BBA_NOTIFY_MAIL_TPL_CODE_FRIEND_REQUEST', 'bbac_friend_request');

// メールテンプレートトリガー名
define('BBA_NOTIFY_MAIL_TPL_TRIGGER_FRIEND_REQUEST', '友達申請通知');


//////////////////////////////////////////////////////////////////////////////////////////
/// グループに投稿通知
// Event Notificationの識別子
define('BBA_NOTIFY_ID_GROUP_POST', 'bbacom.notify_group_post');

// Event Notificationのグループ名
define('BBA_NOTIFY_GROUP_GROUP_POST', 'bbacom.group_post');

// メールテンプレートコード（テンプレートファイル名）
define('BBA_NOTIFY_MAIL_TPL_CODE_GROUP_POST', 'bbac_group_post');

// メールテンプレートトリガー名
define('BBA_NOTIFY_MAIL_TPL_TRIGGER_GROUP_POST', '参加グループに投稿通知');


//////////////////////////////////////////////////////////////////////////////////////////
/// グループ参加通知
// Event Notificationの識別子
define('BBA_NOTIFY_ID_GROUP_JOIN', 'bbacom.notify_group_join');

// Event Notificationのグループ名
define('BBA_NOTIFY_GROUP_GROUP_JOIN', 'bbacom.group_join');

// メールテンプレートコード（テンプレートファイル名）
define('BBA_NOTIFY_MAIL_TPL_CODE_GROUP_JOIN', 'bbac_group_join');

// メールテンプレートトリガー名
define('BBA_NOTIFY_MAIL_TPL_TRIGGER_GROUP_JOIN', 'グループ参加通知');


//////////////////////////////////////////////////////////////////////////////////////////
/// DM通知
// Event Notificationの識別子
define('BBA_NOTIFY_ID_DM', 'bbacom.notify_dm');

// Event Notificationのグループ名
define('BBA_NOTIFY_GROUP_DM', 'bbacom.dm');

// メールテンプレートコード（テンプレートファイル名）
define('BBA_NOTIFY_MAIL_TPL_CODE_DM', 'bbac_dm');

// メールテンプレートトリガー名
define('BBA_NOTIFY_MAIL_TPL_TRIGGER_DM', 'DM通知');
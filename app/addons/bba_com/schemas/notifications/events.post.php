<?php
/**
 * 用の通知イベントスキーマ
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


return $schema;

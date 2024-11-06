<?php
/*
 * Copyright (c) 2021.
 * cs-cart.jp / mekuma.jp
 */
/**
 * DM通知のテンプレート変数を定義するファイル
 * @var $auth
 * @var $tpl_base_data
 * @var  $tpl_email_data
 */


/////////////////////////////////////////////////////////////////////////////
// データ取得 BOF
/////////////////////////////////////////////////////////////////////////////
// メールテンプレート編集ページ以外の場合
if (empty($_edit_mail_tpl)) {
    // ユーザーに関するデータ
    $tpl_email_data = $tpl_base_data['email_data']->value;
}
/////////////////////////////////////////////////////////////////////////////
// データ取得 EOF
/////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////
// メールテンプレート取得 BOF
/////////////////////////////////////////////////////////////////////////////
// メールテンプレートコードとユーザーが使用中の言語コードでメールテンプレートを抽出
if (!empty($tpl_code)) {
    /** @noinspection PhpUndefinedConstantInspection */
    $mtpl_lang_code = CART_LANGUAGE;
    /** @noinspection PhpUndefinedFunctionInspection */
    $mail_template = fn_mtpl_get_email_contents($tpl_code, $mtpl_lang_code);
}
/////////////////////////////////////////////////////////////////////////////
// メールテンプレート取得 EOF
/////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////
// 利用可能なテンプレート変数を定義 BOF
/////////////////////////////////////////////////////////////////////////////

//テンプレート変数を定義
$mail_tpl_var = [
    //URL
    'BBA_COM_DM_URL' =>
        [
            'desc' => 'bba_com.notify_dm_url',
            'value' => empty($_edit_mail_tpl) ? $tpl_email_data['notify_dm_url'] : ''
        ],
    //グループ名
    'BBA_COM_DM_SENDER_NAME' =>
        [
            'desc' => 'bba_com.notify_dm_sender_name',
            'value' => empty($_edit_mail_tpl) ? $tpl_email_data['sender_name'] : ''
        ],

];

/////////////////////////////////////////////////////////////////////////////
// 利用可能なテンプレート変数を定義 EOF
/////////////////////////////////////////////////////////////////////////////

{*読み込むLESSファイルを指定します*}
{style src="addons/bba_com/styles.less"}
{style src="addons/bba_com/tags.less"}


{*コミュニティメンバーのみのスタイル*}
{if $auth.user_type == "V" && $auth.company_id == 22}
    {style src="addons/bba_com/comunity_member.less"}
{/if}
{$post_max_size = $server_env->getIniVar("post_max_size")}
{$upload_max_filesize = $server_env->getIniVar("upload_max_filesize")}

{if $max_upload_filesize}
    {if $post_max_size > $max_upload_filesize}
        {$post_max_size = $max_upload_filesize}
    {/if}
    {if $upload_max_filesize > $max_upload_filesize}
        {$upload_max_filesize = $max_upload_filesize}
    {/if}
{/if}


<script>
    (function (_, $) {
        $.extend(_, {
            post_max_size_bytes: '{$post_max_size|fn_return_bytes}',
            files_upload_max_size_bytes: '{$upload_max_filesize|fn_return_bytes}',
            max_images_upload: '{$max_images_upload}',

            post_max_size_mbytes: '{$post_max_size}',
            files_upload_max_size_mbytes: '{$upload_max_filesize}'
        });

        _.tr({
            file_is_too_large: '{__("file_is_too_large")|escape:"javascript"}',
            files_are_too_large: '{__("files_are_too_large")|escape:"javascript"}'
        });
    })(Tygh, Tygh.$);
</script>


{script src="js/tygh/fileuploader_scripts.js"}


<form name="community_profiles_update_form" enctype="multipart/form-data" action="{""|fn_url}" method="post">
    <input type="hidden" name="user_id" value="{$auth.user_id}"/>

    <div class="row-fluid ty-account bba-com-profile">
        <div class="span10">

            {*プロフィール画像*}
            <div class="ty-control-group">
                <label for="community_profile"
                       class="ty-control-group__title">{__("bba_com.community_profile_image")}</label>
                <div>
                    {*                    {include file="common/fileuploader.tpl" var_name="community_profile"}*}
                    {include file="addons/bba_com/views/community/components/attach_images.tpl" image_name="community_profile" image_object_type="community_profile" image_pair=$cp_data.community_profile no_detailed=true hide_titles=true hide_alt=true}
                </div>
            </div>


            {*名前*}
            {capture name="community_profiles_name_default"}
                {$user_data.firstname} {$user_data.lastname}
            {/capture}
            <div class="ty-control-group">
                <label for="name"
                       class="ty-control-group__title cm-required cm-trim">{__("bba_com.community_profiles_name")}</label>
                <input type="text" id="name" name="profile_data[name]" size="32" maxlength="128"
                       value="{$cp_data.name|default:$smarty.capture.community_profiles_name_default|trim}"
                       class="ty-input-text"/>
            </div>

            {*名前かな*}
            {capture name="community_profiles_name_kana_default"}
                {$user_data.fields.54} {$user_data.fields.56}
            {/capture}
            <div class="ty-control-group">
                <label for="name_kana"
                       class="ty-control-group__title cm-required cm-trim">{__("bba_com.community_profiles_name_kana")}</label>
                <input type="text" id="name_kana" name="profile_data[name_kana]" size="32" maxlength="128"
                       value="{$cp_data.name_kana|default:$smarty.capture.community_profiles_name_kana_default|trim}"
                       class="ty-input-text"/>
            </div>

            {*キャッチコピー catch_copy textarea*}
            <div class="ty-control-group">
                <label for="catch_copy"
                       class="ty-control-group__title cm-required cm-trim">{__("bba_com.community_profiles_catch_copy")}</label>
                <textarea id="catch_copy" name="profile_data[catch_copy]" rows="5" cols="64"
                          class="ty-input-textarea">{$cp_data.catch_copy}</textarea>
            </div>

            {* BS情報（期生） bis_info textarea*}
            <div class="ty-control-group">
                <label for="bis_info"
                       class="ty-control-group__title cm-required cm-trim">{__("bba_com.community_profiles_bis_info")}</label>
                <textarea id="bis_info" name="profile_data[bis_info]" rows="5" cols="64"
                          class="ty-input-textarea">{$cp_data.bis_info}</textarea>
            </div>

            {*携帯電話 mobile_tel*}
            <div class="ty-control-group ty-billing-phone">
                <label for="mobile_tel"
                       class="ty-control-group__title cm-required cm-trim">{__("bba_com.community_profiles_mobile_tel")}</label>
                <input type="text" id="mobile_tel" name="profile_data[mobile_tel]" size="32" maxlength="128"
                       value="{$cp_data.mobile_tel}"
                       class="ty-input-text"/>
            </div>

            {*コミュニティ用自由入力欄 my_profile textarea*}
            <div class="ty-control-group">
                <label for="my_profile"
                       class="ty-control-group__title cm-required cm-trim">{__("bba_com.community_profiles_my_profile")}</label>
                <textarea id="my_profile" name="profile_data[my_profile]" rows="5" cols="64"
                          class="ty-input-textarea">{$cp_data.my_profile}</textarea>
            </div>

            {*その他画像 3つ*}
            <div class="ty-control-group">
                <label for="other_images"
                       class="ty-control-group__title">{__("bba_com.community_images")}</label>
                <div class="community-images">
                    <div>
                        {include file="addons/bba_com/views/community/components/attach_images.tpl" image_name="community_image_1" image_object_type="community_image_1" image_pair=$cp_data.community_image_1 no_detailed=true hide_titles=true hide_alt=true}
                    </div>
                    <div>
                        {include file="addons/bba_com/views/community/components/attach_images.tpl" image_name="community_image_2" image_object_type="community_image_2" image_pair=$cp_data.community_image_2 no_detailed=true hide_titles=true hide_alt=true}
                    </div>
                    <div>
                        {include file="addons/bba_com/views/community/components/attach_images.tpl" image_name="community_image_3" image_object_type="community_image_3" image_pair=$cp_data.community_image_3 no_detailed=true hide_titles=true hide_alt=true}
                    </div>
                </div>
            </div>

            {*ブログ・SNS情報*}
            {include file="common/subheader.tpl" title=__("bba_com.community_profiles_blog_sns")}

            {*ブログスタート⽇ blog_start required calendar*}
            <div class="ty-control-group">
                <label for="blog_start"
                       class="ty-control-group__title cm-required cm-trim">{__("bba_com.community_profiles_blog_start")}</label>
                <input type="text" id="blog_start" name="profile_data[blog_start]" size="32" maxlength="128"
                       value="{if $cp_data.blog_start != "0000-00-00"}{$cp_data.blog_start}{/if}"
                       class="ty-input-text"/>
            </div>

            {*ブログURL blog_url required*}
            <div class="ty-control-group">
                <label for="blog_url"
                       class="ty-control-group__title cm-required cm-trim">{__("bba_com.community_profiles_blog_url")}</label>
                <input type="text" id="blog_url" name="profile_data[blog_url]" size="32" maxlength="128"
                       value="{$cp_data.blog_url}"
                       class="ty-input-text"/>
            </div>

            {*Facebook URL facebook_url*}
            <div class="ty-control-group">
                <label for="facebook_url"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_facebook_url")}</label>
                <input type="text" id="facebook_url" name="profile_data[facebook_url]" size="32" maxlength="128"
                       value="{$cp_data.facebook_url}"
                       class="ty-input-text"/>
            </div>

            {*X(Twitter)URL x_url*}
            <div class="ty-control-group">
                <label for="x_url"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_x_url")}</label>
                <input type="text" id="x_url" name="profile_data[x_url]" size="32" maxlength="128"
                       value="{$cp_data.x_url}"
                       class="ty-input-text"/>
            </div>

            {*Instagram URL instagram_url*}
            <div class="ty-control-group">
                <label for="instagram_url"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_instagram_url")}</label>
                <input type="text" id="instagram_url" name="profile_data[instagram_url]" size="32" maxlength="128"
                       value="{$cp_data.instagram_url}"
                       class="ty-input-text"/>
            </div>

            {*YouTube URL youtube_url*}
            <div class="ty-control-group">
                <label for="youtube_url"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_youtube_url")}</label>
                <input type="text" id="youtube_url" name="profile_data[youtube_url]" size="32" maxlength="128"
                       value="{$cp_data.youtube_url}"
                       class="ty-input-text"/>
            </div>


            {*会社情報 company_name*}
            {include file="common/subheader.tpl" title=__("bba_com.community_profiles_company")}

            {*会社名 company_name*}
            <div class="ty-control-group">
                <label for="company_name"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_company_name")}</label>
                <input type="text" id="company_name" name="profile_data[company_name]" size="32" maxlength="128"
                       value="{$cp_data.company_name}"
                       class="ty-input-text"/>
            </div>

            {*役職 company_position*}
            <div class="ty-control-group">
                <label for="company_position"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_company_position")}</label>
                <input type="text" id="company_position" name="profile_data[company_position]" size="32" maxlength="128"
                       value="{$cp_data.company_position}"
                       class="ty-input-text"/>
            </div>

            {*会社郵便番号 company_postal_code*}
            <div class="ty-control-group">
                <label for="company_postal_code"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_company_postal_code")}</label>
                <input type="text" id="company_postal_code" name="profile_data[company_postal_code]" size="32"
                       maxlength="128"
                       value="{$cp_data.company_postal_code}"
                       class="ty-input-text"/>
            </div>

            {*会社住所 company_address*}
            <div class="ty-control-group">
                <label for="company_address"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_company_address")}</label>
                <input type="text" id="company_address" name="profile_data[company_address]" size="32" maxlength="128"
                       value="{$cp_data.company_address}"
                       class="ty-input-text"/>
            </div>

            {*会社電話番号 company_tel*}
            <div class="ty-control-group">
                <label for="company_tel"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_company_tel")}</label>
                <input type="text" id="company_tel" name="profile_data[company_tel]" size="32" maxlength="128"
                       value="{$cp_data.company_tel}"
                       class="ty-input-text"/>
            </div>

            {*会社FAX番号 company_fax*}
            <div class="ty-control-group">
                <label for="company_fax"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_company_fax")}</label>
                <input type="text" id="company_fax" name="profile_data[company_fax]" size="32" maxlength="128"
                       value="{$cp_data.company_fax}"
                       class="ty-input-text"/>
            </div>

            {*会社URL company_url*}
            <div class="ty-control-group">
                <label for="company_url"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_company_url")}</label>
                <input type="text" id="company_url" name="profile_data[company_url]" size="32" maxlength="128"
                       value="{$cp_data.company_url}"
                       class="ty-input-text"/>
            </div>

            {*業種 business_category_id*}
            <div class="ty-control-group">
                <label for="business_category_id"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_business_category")}</label>

                ---
                {*                {include file="addons/bba_com/views/community/components/business_category_select.tpl" bc_data=$bc_data}*}
            </div>


            {*事業内容 business_content textarea*}
            <div class="ty-control-group">
                <label for="business_content"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_business_content")}</label>
                <textarea id="business_content" name="profile_data[business_content]" rows="5" cols="64"
                          class="ty-input-textarea">{$cp_data.business_content}</textarea>
            </div>

            {*設⽴年⽉⽇ company_established_date calendar*}
            <div class="ty-control-group">
                <label for="company_established_date"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_company_established_date")}</label>
                <input type="text" id="company_established_date" name="profile_data[company_established_date]" size="32"
                       maxlength="128"
                       value="{if $cp_data.company_established_date != "0000-00-00"}{$cp_data.company_established_date}{/if}"
                       class="ty-input-text"/>
            </div>

            {*資本金 company_capital*}
            <div class="ty-control-group">
                <label for="company_capital"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_company_capital")}</label>
                <input type="text" id="company_capital" name="profile_data[company_capital]" size="32" maxlength="128"
                       value="{$cp_data.company_capital}"
                       class="ty-input-text"/>
            </div>

            {*社員数 company_employees*}
            <div class="ty-control-group">
                <label for="company_employees"
                       class="ty-control-group__title cm-trim">{__("bba_com.community_profiles_company_employees")}</label>
                <input type="text" id="company_employees" name="profile_data[company_employees]" size="32"
                       maxlength="128"
                       value="{$cp_data.company_employees}"
                       class="ty-input-text"/>
            </div>

        </div>

        <div class="span6">

        </div>
    </div>


    <div class="ty-profile-field__buttons buttons-container">
        {*        {include file="buttons/register_profile.tpl" but_name="dispatch[community.edit_profile]"}*}
        {include file="buttons/button.tpl" but_name="dispatch[community.edit_profile]" but_text=__("bba_com.update_community_profile") but_role="submit" but_meta="ty-btn__secondary"}
        {*        {include file="buttons/button.tpl" but_text=__("bba_com.update_community_profile") but_href="dispatch[community.edit_profile]" but_target="" but_meta="ty-btn__secondary" but_role="submit"}*}
    </div>
</form>


{capture name="mainbox_title"}{__("bba_com.community_edit_profile")}{/capture}
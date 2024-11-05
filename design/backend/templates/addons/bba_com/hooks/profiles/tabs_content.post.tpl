{if $user_data.user_id && $user_data.user_type == "V"}

    {assign var="is_my_profile" value=true}
    {if $user_data.user_id != $auth.user_id && $auth.user_type != "A"}
        {assign var="is_my_profile" value=false}
    {/if}
    <div id="content_community_profile"
         class="cm-hide-save-button---- {if $selected_section !== "community_profile"} hidden{/if}{if !$is_my_profile} cm-hide-inputs{/if} ">


        {*個人情報*}
        {include file="common/subheader.tpl" title=__("bba_com.community_profiles_personal") target="#community_profiles_personal"}
        <div id="community_profiles_personal" class="in collapse">
            <div class="alert alert-info">
                {__("bba_com.community_profiles_settings_note")}
                {if $cp_data.user_id == $auth.user_id}
                    {assign var="hash" value=""|fn_bbcmm_get_hashed_time}
                    <a id="preview_as_user"
                       href="{"community.preview_as_user?user_id=`$user_data.user_id`&redirect_url=community.my_profile"|fn_url:"C"}"
                       class="btn cm-new-window-- cm-post" target="_blank">{__("bba_com.preview_profile_page")}</a>
                    <script>
                        //#preview_as_userをクリックした際にハッシュを追加してリンクする
                        $(document).ready(function () {
                            $('#preview_as_user').click(function () {
                                //リンクを一時停止
                                $(this).attr('href', $(this).attr('href') + '&hash={$hash}');
                            });
                        });
                    </script>
                {/if}
            </div>


            {*プロフィール画像*}
            <div class="control-group">
                <label class="control-label"
                       for="elm_community_profile_image">{__("bba_com.community_profile_image")}
                    :</label>
                <div class="controls">
                    {include file="common/attach_images.tpl" image_name="community_profile" image_object_type="community_profile" image_pair=$cp_data.community_profile no_detailed=true hide_titles=true hide_alt=true}
                </div>
            </div>


            {*名前*}
            {capture name="community_profiles_name_default"}
                {$user_data.firstname} {$user_data.lastname}
            {/capture}
            <div class="control-group">
                <label class="control-label cm-required---"
                       for="elm_community_profiles_name">{__("bba_com.community_profiles_name")}
                    :</label>
                <div class="controls">
                    <input type="text" name="cp_data[name]" id="elm_community_profiles_name" size="20"
                           value="{$cp_data.name|default:$smarty.capture.community_profiles_name_default|trim}"
                           class="input-large--"/>
                </div>
            </div>

            {*名前かな*}
            {capture name="community_profiles_name_kana_default"}
                {$user_data.fields.54} {$user_data.fields.56}
            {/capture}
            <div class="control-group">
                <label class="control-label cm-required---"
                       for="elm_community_profiles_name_kana">{__("bba_com.community_profiles_name_kana")}
                    :</label>
                <div class="controls">
                    <input type="text" name="cp_data[name_kana]" id="elm_community_profiles_name_kana" size="20"
                           value="{$cp_data.name_kana|default:$smarty.capture.community_profiles_name_kana_default|fn_bbcmm_convert_kana}"
                           class="input-large--"/>
                </div>
            </div>

            {*キャッチコピー catch_copy textarea*}
            <div class="control-group">
                <label class="control-label"
                       for="elm_community_profiles_catch_copy">{__("bba_com.community_profiles_catch_copy")}
                    :</label>
                <div class="controls">
                <textarea name="cp_data[catch_copy]" id="elm_community_profiles_catch_copy"
                          class="input-large">{$cp_data.catch_copy|default:$company_data.owner_description_short}</textarea>
                </div>
            </div>

            {* BS情報（期生） bis_info textarea*}
            <div class="control-group">
                <label class="control-label"
                       for="elm_community_profiles_bis_info">{__("bba_com.community_profiles_bis_info")}
                    :</label>
                <div class="controls">
                <textarea name="cp_data[bis_info]" id="elm_community_profiles_bis_info"
                          class="input-large--">{$cp_data.bis_info}</textarea>
                </div>
            </div>

            {*携帯電話 mobile_tel*}
            <div class="control-group">
                <label class="control-label"
                       for="elm_community_profiles_mobile_tel">{__("bba_com.community_profiles_mobile_tel")}
                    :</label>
                <div class="controls">
                    <input type="text" name="cp_data[mobile_tel]" id="elm_community_profiles_mobile_tel" size="20"
                           value="{$cp_data.mobile_tel}"
                           class="input-large--"/>
                </div>
            </div>


            {*コミュニティ用自由入力欄 my_profile textarea*}
            <div class="control-group">
                <label class="control-label"
                       for="elm_community_profiles_my_profile">{__("bba_com.community_profiles_my_profile")}
                    :</label>
                <div class="controls">
                <textarea name="cp_data[my_profile]" id="elm_community_profiles_my_profile"
                          rows="10"
                          class="input-large">{$cp_data.my_profile}</textarea>
                </div>
            </div>

            {*タグ tagsアドオン*}
            {include file="addons/bba_com/views/community/components/object_tags.tpl" object=$cp_data input_name="cp_data" allow_save=true object_type="U" object_id=$user_data.user_id}

            

            {*その他画像 3つ*}
            <div class="control-group">
                <label class="control-label"
                       for="elm_community_profile_image">{__("bba_com.community_images")}
                    :</label>
                <div class="controls" style="margin-bottom: 20px">
                    {include file="common/attach_images.tpl" image_name="community_image_1" image_object_type="community_image_1" image_pair=$cp_data.community_image_1 no_detailed=true hide_titles=true}
                </div>
                <div class="controls" style="margin-bottom: 20px">
                    {include file="common/attach_images.tpl" image_name="community_image_2" image_object_type="community_image_2" image_pair=$cp_data.community_image_2 no_detailed=true hide_titles=true}
                </div>
                <div class="controls" style="margin-bottom: 20px">
                    {include file="common/attach_images.tpl" image_name="community_image_3" image_object_type="community_image_3" image_pair=$cp_data.community_image_3 no_detailed=true hide_titles=true}
                </div>
            </div>

            {*ブログ・SNS情報*}
            {include file="common/subheader.tpl" title=__("bba_com.community_profiles_blog_sns") target="#community_profiles_blog_sns"}
            <div id="community_profiles_blog_sns" class="in collapse">

                {*ブログスタート⽇ blog_start required calendar*}
                <div class="control-group">
                    <label class="control-label cm-required---"
                           for="elm_community_profiles_blog_start">{__("bba_com.community_profiles_blog_start")}
                        :</label>
                    <div class="controls">
                        {include file="common/calendar.tpl" date_id="elm_community_profiles_blog_start" date_name="cp_data[blog_start]" date_val=$cp_data.blog_start|strtotime}
                    </div>
                </div>


                {*ブログURL blog_url required*}
                <div class="control-group">
                    <label class="control-label cm-required---"
                           for="elm_community_profiles_blog_url">{__("bba_com.community_profiles_blog_url")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[blog_url]" id="elm_community_profiles_blog_url" size="20"
                               value="{$cp_data.blog_url}"
                               class="input-large"/>

                        <div class="alert alert-info">
                            <p>
                                {__("bba_com.set_blog_url")}
                            </p>
                            {__("bba_com.blog_feed")}: {$company_data.owner_blog_feed}
                        </div>
                    </div>
                </div>

                {*Facebook URL facebook_url*}
                <div class="control-group">
                    <label class="control-label"
                           for="elm_community_profiles_facebook_url">{__("bba_com.community_profiles_facebook_url")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[facebook_url]" id="elm_community_profiles_facebook_url"
                               size="20"
                               value="{$cp_data.facebook_url}"
                               class="input-large"/>
                    </div>
                </div>


                {*X(Twitter)URL x_url*}
                <div class="control-group">
                    <label class="control-label"
                           for="elm_community_profiles_x_url">{__("bba_com.community_profiles_x_url")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[x_url]" id="elm_community_profiles_x_url" size="20"
                               value="{$cp_data.x_url}"
                               class="input-large"/>
                    </div>
                </div>


                {*Instagram URL instagram_url*}
                <div class="control-group">
                    <label class="control-label"
                           for="elm_community_profiles_instagram_url">{__("bba_com.community_profiles_instagram_url")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[instagram_url]" id="elm_community_profiles_instagram_url"
                               size="20"
                               value="{$cp_data.instagram_url}"
                               class="input-large"/>
                    </div>
                </div>

                {*YouTube URL youtube_url*}
                <div class="control-group">
                    <label class="control-label"
                           for="elm_community_profiles_youtube_url">{__("bba_com.community_profiles_youtube_url")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[youtube_url]" id="elm_community_profiles_youtube_url" size="20"
                               value="{$cp_data.youtube_url}"
                               class="input-large"/>
                    </div>
                </div>


            </div>


            {*会社情報*}
            {include file="common/subheader.tpl" title=__("bba_com.community_profiles_company") target="#community_profiles_company"}
            <div id="community_profiles_company" class="in collapse">

                {*会社名 company_name*}
                <div class="control-group">
                    <label class="control-label cm-required---"
                           for="elm_community_profiles_company_name">{__("bba_com.community_profiles_company_name")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[company_name]" id="elm_community_profiles_company_name"
                               size="20"
                               value="{$cp_data.company_name|default:$company_data.company}"
                               class="input-large--"/>
                    </div>
                </div>

                {*役職 company_position*}
                <div class="control-group">
                    <label class="control-label"
                           for="elm_community_profiles_company_position">{__("bba_com.community_profiles_company_position")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[company_position]" id="elm_community_profiles_company_position"
                               size="20"
                               value="{$cp_data.company_position}"
                               class="input-large--"/>
                    </div>
                </div>

                {*会社郵便番号 company_postal_code*}
                <div class="control-group">
                    <label class="control-label"
                           for="elm_community_profiles_company_postal_code">{__("bba_com.community_profiles_company_postal_code")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[company_postal_code]"
                               id="elm_community_profiles_company_postal_code"
                               size="20"
                               value="{$cp_data.company_postal_code|default:$company_data.zipcode}"
                               class="input-short"/>
                    </div>
                </div>

                {*会社住所 company_address*}
                <div class="control-group">
                    {capture name="company_address_default"}
                        {$company_data.state}{$company_data.city}{$company_data.address}
                    {/capture}
                    <label class="control-label"
                           for="elm_community_profiles_company_address">{__("bba_com.community_profiles_company_address")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[company_address]" id="elm_community_profiles_company_address"
                               size="20"
                               value="{$cp_data.company_address|default:$smarty.capture.company_address_default|trim}"
                               class="input-large"/>
                    </div>
                </div>

                {*会社電話番号 company_tel cm-required---*}
                <div class="control-group">
                    <label class="control-label cm-required---"
                           for="elm_community_profiles_company_tel">{__("bba_com.community_profiles_company_tel")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[company_tel]" id="elm_community_profiles_company_tel" size="20"
                               value="{$cp_data.company_tel|default:$company_data.phone}"
                               class="input-large--"/>
                    </div>
                </div>

                {*会社FAX番号 company_fax*}
                <div class="control-group">
                    <label class="control-label"
                           for="elm_community_profiles_company_fax">{__("bba_com.community_profiles_company_fax")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[company_fax]" id="elm_community_profiles_company_fax" size="20"
                               value="{$cp_data.company_fax}"
                               class="input-large--"/>
                    </div>
                </div>

                {*会社URL company_url*}
                <div class="control-group">
                    <label class="control-label"
                           for="elm_community_profiles_company_url">{__("bba_com.community_profiles_company_url")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[company_url]" id="elm_community_profiles_company_url" size="20"
                               value="{$cp_data.company_url}"
                               class="input-large"/>
                    </div>
                </div>

                {*業種 business_category_id*}
                <div class="control-group">
                    <label class="control-label"
                           for="elm_community_profiles_business_category_id">{__("bba_com.community_profiles_business_category")}
                        :</label>
                    <div class="controls">
                        SELECT
                        {*                {include file="addons/bba_com/views/profiles/components/business_category.tpl" selected=$cp_data.business_category_id}*}
                    </div>
                </div>

                {*事業内容 business_content textarea*}
                <div class="control-group">
                    <label class="control-label cm-required---"
                           for="elm_community_profiles_business_content">{__("bba_com.community_profiles_business_content")}
                        :</label>
                    <div class="controls">
                <textarea name="cp_data[business_content]" id="elm_community_profiles_business_content"
                          rows="10"
                          class="input-large">{$cp_data.business_content}</textarea>
                    </div>
                </div>

                {*設⽴年⽉⽇ company_established_date calendar*}
                <div class="control-group">
                    <label class="control-label"
                           for="elm_community_profiles_company_established_date">{__("bba_com.community_profiles_company_established_date")}
                        :</label>
                    <div class="controls">
                        {include file="common/calendar.tpl" date_id="elm_community_profiles_company_established_date" date_name="cp_data[company_established_date]" date_val=$cp_data.company_established_date|strtotime}
                    </div>
                </div>

                {*資本金 company_capital*}
                <div class="control-group">
                    <label class="control-label"
                           for="elm_community_profiles_company_capital">{__("bba_com.community_profiles_company_capital")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[company_capital]" id="elm_community_profiles_company_capital"
                               size="20"
                               value="{$cp_data.company_capital}"
                               class="input-large--"/>
                    </div>
                </div>

                {*社員数 company_employees*}
                <div class="control-group">
                    <label class="control-label"
                           for="elm_community_profiles_company_employees">{__("bba_com.community_profiles_company_employees")}
                        :</label>
                    <div class="controls">
                        <input type="text" name="cp_data[company_employees]"
                               id="elm_community_profiles_company_employees"
                               size="20"
                               value="{$cp_data.company_employees}"
                               class="input-large--"/>
                    </div>
                </div>


                {*            {$company_data|fn_print_r}*}
            </div>
        </div>
    </div>
{/if}

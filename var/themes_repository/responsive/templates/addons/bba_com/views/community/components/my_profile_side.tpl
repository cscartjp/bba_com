{*<a href="{"community.my_profile"|fn_url}"*}
{*   class="ty-btn ty-btn__primary">{__("bba_com.my_profile")}</a>*}

<div class="bba-profile-info">
    <ul>
        {*タグ*}
        {if $cp_data.tags}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.tags")}</span>
                <span class="bba-profile-info-value">
                    {include file="addons/bba_com/views/community/components/tags.tpl" object_type="U" object_id=$cp_data.user_id object=$cp_data}
                    {*                    {$cp_data.tags|fn_print_r}*}
                </span>
            </li>
        {/if}




        {*会社名*}
        {if $cp_data.company_name}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_name")}</span>
                <span class="bba-profile-info-value">{$cp_data.company_name}</span>
            </li>
        {/if}
        {*役職*}
        {if $cp_data.company_position}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_position")}</span>
                <span class="bba-profile-info-value">{$cp_data.company_position}</span>
            </li>
        {/if}
        {*所在地*}
        {if $cp_data.company_address}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_location")}</span>
                <span class="bba-profile-info-value">
                        〒{$cp_data.company_postal_code}<br>
                        {$cp_data.company_address}
                    </span>
            </li>
        {/if}
        {*事業内容*}
        {if $cp_data.business_content}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_business_content")}</span>
                <span class="bba-profile-info-value">{$cp_data.business_content|nl2br nofilter}</span>
            </li>
        {/if}
        {*設⽴年⽉⽇*}
        {if $cp_data.company_established_date != '0000-00-00'}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_established_date")}</span>
                <span class="bba-profile-info-value">{$cp_data.company_established_date}</span>
            </li>
        {/if}
        {*資本金*}
        {if $cp_data.company_capital}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_capital")}</span>
                <span class="bba-profile-info-value">{$cp_data.company_capital}</span>
            </li>
        {/if}
        {*社員数*}
        {if $cp_data.company_employees}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_employees")}</span>
                <span class="bba-profile-info-value">{$cp_data.company_employees}</span>
            </li>
        {/if}
        {*会社URL*}
        {if $cp_data.company_url}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_url")}</span>
                <span class="bba-profile-info-value">
                        <a class="cm-new-window" href="{$cp_data.company_url}"
                           target="_blank">{$cp_data.company_url}</a>
                    </span>
            </li>
        {/if}
        {*このユーザーの友達*}
        {if $relationships}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_friends")}</span>
                <span class="bba-profile-info-value">
                    <span class="bba-community-side-friends">
                        {foreach from=$relationships item=friend}
                            <a href="{"community.view_user?user_id=`$friend.friend_id`"|fn_url}">
                                {assign var="friend_image_size" value=60}
                                {include file="common/image.tpl" image_width=$friend_image_size image_height=$friend_image_size images=$friend.profile_image no_ids=true class="bba-community-friend-image-photo"}
                            </a>
                        {/foreach}
                        {*全ての友達*}
                        <a class="all-friends-link"
                           href="{"community.friends"|fn_url}">{__("bba_com.community_all_friends")}</a>
                    </span>
                </span>
            </li>
        {/if}
    </ul>
</div>
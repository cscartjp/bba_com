<div class="bba-profile-top">
    <div class="bba-profile-image">
        {assign var="profile_image_size" value=280}
        {include file="common/image.tpl" image_width=$profile_image_size image_height=$profile_image_size images=$cp_data.profile_image no_ids=true class="bba-profile-image-photo"}
    </div>

    <div class="bba-profile-name">
        <p class="bba-profile-kana">{$cp_data.name_kana}</p>
        <h2 class="bba-profile-fullname">{$cp_data.name}</h2>
        <p class="bba-profile-catch-copy">{$cp_data.catch_copy}</p>

        <div class="bba-bis-info">
            {if $cp_data.blog_url}
                <p>
                    <span>{__("bba_com.community_profiles_blog_url")}: </span>
                    <a class="cm-new-window" href="{$cp_data.blog_url}" target="_blank">{$cp_data.blog_url}</a>
                </p>
            {/if}
            {if $cp_data.blog_start}
                <p>
                    <span>{__("bba_com.community_profiles_blog_start")}: </span>
                    {$cp_data.blog_start}
                </p>
            {/if}
            {if $cp_data.bis_info}
                <p>
                    <span>{__("bba_com.community_profiles_bis_info")}: </span>
                    {$cp_data.bis_info}
                </p>
            {/if}
        </div>

        <div class="sns-link">
            {if $cp_data.x_url}
                <a href="{$cp_data.sns_twitter}" class="bba-sns-link bba-sns-link-twitter" target="_blank">
                    <i class="ty-icon-twitter"></i>
                </a>
            {/if}
            {if $cp_data.facebook_url}
                <a href="{$cp_data.facebook_url}" class="bba-sns-link bba-sns-link-facebook" target="_blank">
                    <i class="ty-icon-facebook"></i>
                </a>
            {/if}
            {if $cp_data.instagram_url}
                <a href="{$cp_data.instagram_url}" class="bba-sns-link bba-sns-link-instagram" target="_blank">
                    <i class="ty-icon-instagram"></i>
                </a>
            {/if}
            {if $cp_data.youtube_url}
                <a href="{$cp_data.youtube_url}" class="bba-sns-link bba-sns-link-youtube" target="_blank">
                    <i class="ty-icon-youtube"></i>
                </a>
            {/if}
        </div>
    </div>


    <div class="bba-profile-edit">
        <a href="{"community.edit_profile"|fn_url}"
           class="ty-btn ty-btn__primary bba-profile-edit-link">{__("bba_com.edit_profile")}</a>
    </div>
</div>
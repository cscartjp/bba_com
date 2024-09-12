<div class="bba-profile-top">
    <div class="bba-profile-image">
        {assign var="profile_image_size" value=280}
        {include file="common/image.tpl" image_width=$profile_image_size image_height=$profile_image_size images=$cp_data.community_profile no_ids=true class="bba-profile-image-photo"}
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


    <div class="bba-user-profile-right">
        {if !$relationship_data}
            {*友達登録*}
            <a href="{"community.add_friend?friend_id=`$cp_data.user_id`"|fn_url}"
               class="bba-community-add-friend-btn cm-post cm-confirm" data-user-id="{$cp_data.user_id}">
            <i class="ty-icon-heart"></i>
            <span>{__("bba_com.add_friend")}</span>
        </a>
        {else}
            <i class="ty-icon-user"></i>
            <span>{__("bba_com.already_friend")}</span>
        {/if}

        {*DM送信*}
        <a href="{"community.send_dm?user_id=`$cp_data.user_id`"|fn_url}"
           class="bba-community-send-dm-btn cm-ajax cm-post" data-user-id="{$cp_data.user_id}">
            <i class="ty-icon-mail"></i>
            <span>{__("bba_com.send_dm")}</span>
        </a>
    </div>


    {*    {$relationship_data|fn_print_r}*}
</div>
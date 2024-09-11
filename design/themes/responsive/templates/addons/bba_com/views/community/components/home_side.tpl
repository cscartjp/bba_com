<div class="bba-home-side-left">
    <ul>
        <li>
            <div class="bba-profile-image">
                {assign var="profile_image_size" value=50}
                {include file="common/image.tpl" image_width=$profile_image_size image_height=$profile_image_size images=$cp_data.community_profile no_ids=true class="bba-profile-image-photo"}
                <h2 class="bba-profile-fullname"><a href="{"community.my_profile"|fn_url}">{$cp_data.name}</a></h2>
            </div>
        </li>

        {*友達*}
        <li>
            <a href="{"community.friends"|fn_url}">{__("bba_com.community_friends")}</a>
        </li>

        {*グループ*}
        <li>
            <a href="{"community.groups"|fn_url}">{__("bba_com.community_groups")}</a>
        </li>
    </ul>
</div>
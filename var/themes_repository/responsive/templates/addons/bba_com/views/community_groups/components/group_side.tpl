<div class="bba-group-side-left">
    {*    {$group_data|fn_print_r}*}
    <ul>
        <li>
            <div class="bba-profile-image">
                {assign var="side_group_image_size" value=300}
                {include file="common/image.tpl" image_width=$side_group_image_size image_height=$side_group_image_size images=$group_data.group_icon no_ids=true class="bba-group-image-photo"}
                <h4 class="bba-group-name">{$group_data.group}</h4>

                <p class="text-center">
                    {if $is_member}
                        {__("bba_com.member_type_{$is_member|lower}")}
                    {else}
                        {__("bba_com.member_type_g")}
                    {/if}
                </p>
            </div>
        </li>

        {*友達*}
        <li>
            <p class="bba-group-description">
                {$group_data.description}
            </p>
        </li>

        {if $runtime.mode=="view"}
            {*グループメンバー*}
            <li>
                <h4>{__("bba_com.group_members")}</h4>

                <div class="group-member-list">
                    <span class="bba-profile-info-value">
                        <span class="bba-community-side-friends">
                            {assign var="member_image_size" value=60}
                            {foreach from=$group_members item=member}
                                <a href="{"community.view_user?user_id=`$member.user_id`"|fn_url}">
                                {include file="common/image.tpl" image_width=$member_image_size image_height=$member_image_size images=$member.profile_image no_ids=true class="bba-community-friend-image-photo"}
                                </a>
                            {/foreach}
                            {*全てのメンバー*}
                            <a class="all-friends-link"
                               href="{"community_groups.members"|fn_url}">{__("bba_com.community_all_members")}</a>
                        </span>
                    </span>
                </div>

            </li>
        {/if}

        {if $runtime.mode=="view" || $runtime.mode=="edit"}
            {*グループの管理*}
            {if $is_member == "A"}
                <li class="group-manage">
                    <h4>{__("bba_com.group_control")}</h4>
                    {*グループの編集*}
                    <a href="{"community_groups.edit?group_id=`$group_data.group_id`"|fn_url}"
                       class="bba-group-edit-link">{__("bba_com.group_edit")}</a>
                    {*グループメンバーの管理*}
                    <a href="{"community_groups.group_members_manage?group_id=`$group_data.group_id`"|fn_url}"
                       class="bba-group-edit-link">{__("bba_com.group_members_manage")}</a>
                </li>
            {/if}
        {/if}
    </ul>
</div>
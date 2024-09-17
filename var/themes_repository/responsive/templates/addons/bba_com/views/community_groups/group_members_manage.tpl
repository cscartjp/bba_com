{assign var="redirect_url" value=$config.current_url|escape:"url"}

<div class="row-fluid">
    <div class="span5">
        {include file="addons/bba_com/views/community_groups/components/group_side.tpl" cp_data=$cp_data}
    </div>
    <div class="span11">
        {*友達のリスト $group_members*}

        {if $group_members}
            {assign var="friend_image_size" value=60}

            {include file="common/pagination.tpl"}
            <div class="bba-community-members">
                {foreach from=$group_members item=gm}
                    <div class="bba-community-member">
                        <div class="bba-community-role">
                            {if $gm.role == "A"}
                                <span class="alert alert-error">{__("bba_com.group_admin")}</span>
                            {elseif $gm.role == "M"}
                                <span class="alert alert-success">{__("bba_com.group_member")}</span>
                            {/if}
                        </div>

                        <div class="bba-community-member-image">
                            {include file="common/image.tpl" image_width=$friend_image_size image_height=$friend_image_size images=$gm.profile_image no_ids=true class="bba-community-friend-image-photo"}
                        </div>
                        <div class="bba-community-member-profile">
                            <h4>
                                {if $group_data.create_user_id != $gm.user_id}
                                <a class="cm-new-window" href="{"community.view_user?user_id=`$gm.user_id`"|fn_url}">
                                    {/if}
                                    {$gm.name}
                                    {if $group_data.create_user_id != $gm.user_id}
                                </a>
                                {/if}
                            </h4>
                            <p>{$gm.company_name}</p>
                        </div>

                        <div class="bba-community-member-status">
                            {if $group_data.create_user_id != $gm.user_id}
                                {if $gm.status == "A"}
                                    <a href="{"community_groups.change_group_status?group_id=`$group_data.group_id`&user_id=`$gm.user_id`&status_to=D&redirect_url=`$redirect_url`"|fn_url}"
                                       class="cm-confirm cm-post">{__("bba_com.group_member_status_disapprove")}</a>
                                {elseif $gm.status == "D"}
                                    <a href="{"community_groups.change_group_status?group_id=`$group_data.group_id`&user_id=`$gm.user_id`&status_to=A&redirect_url=`$redirect_url`"|fn_url}"
                                       class="cm-confirm cm-post">{__("bba_com.group_member_status_active")}</a>
                                {elseif $gm.status == "P" && $group_data.type == "I"}
                                    <a href="{"community_groups.change_group_status?group_id=`$group_data.group_id`&user_id=`$gm.user_id`&status_to=A&redirect_url=`$redirect_url`"|fn_url}"
                                       class="cm-confirm cm-post">{__("bba_com.group_member_status_accept")}</a>
                                    <a href="{"community_groups.change_group_status?group_id=`$group_data.group_id`&user_id=`$gm.user_id`&status_to=R&redirect_url=`$redirect_url`"|fn_url}"
                                       class="cm-confirm cm-post">{__("bba_com.group_member_status_reject")}</a>
                                {/if}
                            {/if}
                        </div>

                    </div>
                {/foreach}
            </div>
            {include file="common/pagination.tpl"}
        {else}
            <p class="ty-no-items">{__("no_items")}</p>
        {/if}
    </div>
</div>


{capture name="mainbox_title"}{__("bba_com.community_friends")}{/capture}
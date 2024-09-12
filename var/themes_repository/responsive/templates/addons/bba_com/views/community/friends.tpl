<div class="row-fluid">
    <div class="span4">
        {include file="addons/bba_com/views/community/components/home_side.tpl" cp_data=$cp_data}
    </div>
    <div class="span9">
        {*友達のリスト $relationships*}
        {if $relationships}
            <div class="bba-community-friends">
                {foreach from=$relationships item=rel}
                    <div class="bba-community-friend">
                        <a href="{"community.view_user?user_id=`$rel.friend_id`"|fn_url}">
                            <div class="bba-community-friend-image">
                                {assign var="friend_image_size" value=60}
                                {include file="common/image.tpl" image_width=$friend_image_size image_height=$friend_image_size images=$rel.profile_image no_ids=true class="bba-community-friend-image-photo"}
                            </div>
                            <div class="bba-community-friend-profile">
                                <h4>{$rel.name}</h4>
                                <p>{$rel.company_name}</p>
                            </div>
                        </a>
                    </div>
                {/foreach}
            </div>
        {else}
            <p class="ty-no-items">{__("no_items")}</p>
        {/if}


    </div>
    <div class="span3">
        <h4>SIDE</h4>
        運営からのお知らせなどを表示する
    </div>
</div>


{capture name="mainbox_title"}{__("bba_com.community_friends")}{/capture}
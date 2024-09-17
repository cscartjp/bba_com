<div class="row-fluid">
    <div class="span16">
        {*グループの作成*}
        <div class="bba-community-create">
            <a href="{"community_groups.create"|fn_url}"
               class="ty-btn ty-btn__primary bba-group-edit-link">{__("bba_com.create_group")}</a>
        </div>
    </div>
</div>
<div class="row-fluid">
    {*    <div class="span4">*}
    {*        *}{*        {include file="addons/bba_com/views/community_groups/components/group_side.tpl" cp_data=$cp_data}*}
    {*        {include file="addons/bba_com/views/community/components/home_side.tpl" cp_data=$cp_data}*}
    {*    </div>*}
    <div class="span13">
        {*グループのリスト $groups*}
        {if $groups}
            {assign var="group_image_size" value=130}
            <div class="bba-community-groups">
                {foreach from=$groups item=gp}
                    <div class="bba-community-group">
                        <a href="{"community_groups.view?group_id=`$gp.group_id`"|fn_url}">
                            {if $gp.group_icon}
                                <div class="bba-community-group-image">
                                    {include file="common/image.tpl" image_width=$group_image_size image_height=$group_image_size images=$gp.group_icon no_ids=true class="bba-community-group-image-icon"}
                                </div>
                            {/if}
                            <div class="bba-community-group-detail">
                                <div class="bba-community-group-type bba-community-group-type-{$gp.type|strtolower}">
                                    {if $gp.type == "P"}
                                        <span class="bba-community-group-type-public">{__("bba_com.group_type_public")}</span>
                                    {else}
                                        <span class="bba-community-group-type-invite">{__("bba_com.group_type_invite")}</span>
                                    {/if}
                                </div>
                                <h4>{$gp.group}</h4>
                                <p>{$gp.description|mb_strimwidth:0:100:"..."}</p>
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
        {include file="addons/bba_com/views/community/components/general_side.tpl"}
    </div>
</div>


{capture name="mainbox_title"}{__("bba_com.community_groups")}{/capture}
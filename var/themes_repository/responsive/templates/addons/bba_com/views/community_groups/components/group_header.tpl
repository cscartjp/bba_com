{*登録した画像 group_icon*}
{if $group_data.group_icon}
    <div class="bba-community-image">
        {include file="common/image.tpl" images=$group_data.group_icon no_ids=true image_width=200 image_height=200 class="bba-community-group-image-icon"}
    </div>
{/if}
<div class="bba-group-details">
    <h2>{$group_data.group}</h2>
    <p class="text-center">
        {if $is_member}
            {__("bba_com.member_type_{$is_member|lower}")}
        {else}
            {__("bba_com.member_type_g")}
        {/if}
    </p>
</div>

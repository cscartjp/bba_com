<div class="bba-group-side-left">
    {*    {$group_data|fn_print_r}*}
    <ul>
        <li>
            <div class="bba-profile-image">
                {assign var="side_group_image_size" value=300}
                {include file="common/image.tpl" image_width=$side_group_image_size image_height=$side_group_image_size images=$group_data.group_icon no_ids=true class="bba-group-image-photo"}
                <h4 class="bba-group-name">{$group_data.group}</h4>
            </div>
        </li>

        {*友達*}
        <li>
            <p class="bba-group-description">
                {$group_data.description}
            </p>
        </li>

        {*グループメンバー*}
        <li>
            MEMBERS
        </li>
    </ul>
</div>
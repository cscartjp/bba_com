<div class="row-fluid">
    <div class="span16">

        <div class="bba-community-header">
            <h2>{$group_data.group} {__("edit")}</h2>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="span5">
        {include file="addons/bba_com/views/community_groups/components/group_side.tpl" cp_data=$cp_data}
    </div>
    <div class="span11 ty-account bba-com-group">
        <form name="community_profiles_update_form" enctype="multipart/form-data" action="{""|fn_url}" method="post">
            <input type="hidden" name="user_id" value="{$auth.user_id}"/>
            <input type="hidden" name="group_id" value="{$group_data.group_id}"/>

            {*グループのアイコン*}
            <div class="ty-control-group">
                <label for="community_profile"
                       class="ty-control-group__title">{__("bba_com.group_icon")}</label>
                <div>
                    {include file="addons/bba_com/views/community/components/attach_images.tpl" image_name="group_icon" image_object_type="group_icon" image_pair=$group_data.group_icon no_detailed=true hide_titles=true hide_alt=true}
                </div>
            </div>

            {*グループ名*}
            <div class="ty-control-group cm-hide-inputs">
                <label for="group_name"
                       class="ty-control-group__title cm-required cm-trim">{__("bba_com.group_name")}</label>
                <input type="text" id="group_name" name="group_data[group]" size="32" maxlength="128"
                       value="{$group_data.group}"
                       class="ty-input-text" readonly/>

                <p class="alert alert-warning">{__("bba_com.group_name_hint")}</p>
            </div>

            {*グループ詳細 description textarea*}
            <div class="ty-control-group">
                <label for="description"
                       class="ty-control-group__title cm-required">{__("bba_com.group_description")}</label>
                <textarea id="description" name="group_data[description]" class="ty-input-textarea" rows="5"
                          cols="64">{$group_data.description}</textarea>
            </div>


            {*グループのタイプ type radio (P:公開 I:招待制)*}
            <div class="ty-control-group">
                <label for="type"
                       class="ty-control-group__title cm-required">{__("bba_com.group_type")}</label>
                <div class="ty-control cm-radio">
                    <input type="radio" id="type_p" name="group_data[type]" value="P"
                           {if $group_data.type == "" || $group_data.type == "P"}checked="checked"{/if}/>
                    <label for="type_p">{__("bba_com.group_type_public")}</label>

                    <input type="radio" id="type_i" name="group_data[type]" value="I"
                           {if $group_data.type == "I"}checked="checked"{/if}/>
                    <label for="type_i">{__("bba_com.group_type_invite")}</label>

                </div>
            </div>


            <div class="ty-profile-field__buttons buttons-container">
                {include file="buttons/button.tpl" but_name="dispatch[community_groups.edit]" but_text=__("bba_com.update_group") but_role="submit" but_meta="ty-btn__secondary cm-confirm"}
            </div>

        </form>
    </div>
</div>

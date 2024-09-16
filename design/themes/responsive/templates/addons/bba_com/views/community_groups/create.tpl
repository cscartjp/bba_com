<div class="row-fluid">
    <div class="span4">
        {include file="addons/bba_com/views/community/components/home_side.tpl" cp_data=$cp_data}
    </div>
    <div class="span9 ty-account bba-com-group">

        <form name="community_profiles_update_form" enctype="multipart/form-data" action="{""|fn_url}" method="post">
            <input type="hidden" name="user_id" value="{$auth.user_id}"/>

            {*グループ名*}
            <div class="ty-control-group">
                <label for="name_kana"
                       class="ty-control-group__title cm-required cm-trim">{__("bba_com.group_name")}</label>
                <input type="text" id="name_kana" name="group_data[group]" size="32" maxlength="128"
                       value="{$group_data.group}"
                       class="ty-input-text"/>
            </div>

            {*グループ詳細 description textarea*}
            <div class="ty-control-group">
                <label for="description"
                       class="ty-control-group__title cm-required">{__("bba_com.group_description")}</label>
                <textarea id="description" name="group_data[description]" class="ty-input-textarea" rows="5"
                          cols="64">{$group_data.description}</textarea>
            </div>

            {*グループのアイコン*}
            <div class="ty-control-group">
                <label for="community_profile"
                       class="ty-control-group__title">{__("bba_com.group_icon")}</label>
                <div>
                    {include file="addons/bba_com/views/community/components/attach_images.tpl" image_name="group_icon" image_object_type="group_icon" image_pair=$group_data.group_icon no_detailed=true hide_titles=true hide_alt=true}
                </div>
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
                {include file="buttons/button.tpl" but_name="dispatch[community_groups.create]" but_text=__("bba_com.create_group_btn") but_role="submit" but_meta="ty-btn__secondary"}
            </div>

        </form>

    </div>
    <div class="span3">
        {include file="addons/bba_com/views/community/components/general_side.tpl"}
    </div>
</div>


{capture name="mainbox_title"}{__("bba_com.community_group_create")}{/capture}
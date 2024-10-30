<div class="row-fluid">
    <div class="span4">
        {include file="addons/bba_com/views/community/components/home_side.tpl" cp_data=$cp_data}
    </div>
    <div class="span9 ty-account bba-com-dm">


        {if $dm_data.from_user_id == $auth.user_id}
            {assign var="to_user_id" value=$dm_data.to_user_id}
        {else}
            {assign var="to_user_id" value=$dm_data.from_user_id}
        {/if}

        $auth.user_id={$auth.user_id}<br>
        $dm_data.from_user_id={$dm_data.from_user_id}<br>
        $dm_data.to_user_id={$dm_data.to_user_id}<br>
        to_user_id={$to_user_id}


        <form name="community_dm_send_form" enctype="multipart/form-data" action="{""|fn_url}" method="post">
            <input type="hidden" name="from_user_id" value="{$auth.user_id}"/>
            <input type="hidden" name="to_user_id" value="{$to_user_id}"/>

            {*宛先*}
            <div class="ty-control-group">
                <label for="to_user_id"
                       class="ty-control-group__title cm-required">{__("bba_com.community_dm_to_user_name")}</label>


                <div class="bba-com-dm-to-user-info">
                    <div class="bba-community-new-post-user-icon">
                        {assign var="post_user_icon_size" value=60}
                        {include file="common/image.tpl" image_width=$post_user_icon_size image_height=$post_user_icon_size images=$dm_data.community_profile no_ids=true class="bba-post-user-icon"}
                    </div>
                    <div class="bba-community-new-post-header-name">
                        <h4>{$dm_data.to_user_name}</h4>
                    </div>
                </div>

            </div>


            {*件名 subject*}
            <div class="ty-control-group">
                <label for="subject"
                       class="ty-control-group__title cm-required">{__("bba_com.community_dm_subject")}</label>
                <p>{$dm_data.subject}</p>
            </div>

            {*本文 message*}
            <div class="ty-control-group">
                <label for="message"
                       class="ty-control-group__title cm-required">{__("bba_com.community_dm_message")}</label>
                <p>{$dm_data.message}</p>
            </div>
        </form>


        <div class="bba-timeline">
            <form action="{""|fn_url}" method="post" class="posts-form" name="post_new" id="post_new">
                <input type="hidden" name="redirect_url" value="{$config.current_url}"/>
                <input type="hidden" name="dm_data[parent_id]" value="{$dm_data.direct_mail_id}"/>
                <input type="hidden" name="dm_data[to_user_id]" value="{$dm_data.from_user_id}"/>

                <div class="bba-community-new-post-header">
                    <div class="bba-community-new-post-user-icon">
                        {assign var="post_user_icon_size" value=60}
                        {include file="common/image.tpl" image_width=$post_user_icon_size image_height=$post_user_icon_size images=$cp_data.community_profile no_ids=true class="bba-post-user-icon"}
                    </div>
                    <div class="bba-community-new-post-header-name">
                        <h4>{$cp_data.name}</h4>
                    </div>
                </div>
                <div class="bba-community-new-post-body">
                    <div class="ty-control-group">
                        <label for="dm_data_message"
                               class="ty-control-group__title cm-required">{__("bba_com.community_dm_message")}</label>

                        <textarea id="dm_data_message" name="dm_data[message]"
                                  cols="20" rows="8" class="ty-input-text-large"
                                  placeholder="{__("bba_com.community_res_message_ph")}"></textarea>
                    </div>
                </div>
                <div class="bba-community-new-post-footer">
                    <div class="buttons-container">
                        {include file="buttons/button.tpl" but_text=__("bba_com.send_dm") but_meta="ty-btn__secondary bba-community-new-post-btn" but_role="submit" but_name="dispatch[community_dm.send_dm_res]"}
                    </div>
                </div>
            </form>
        </div>


    </div>
    <div class="span3">
        {include file="addons/bba_com/views/community/components/general_side.tpl"}
    </div>
</div>


{capture name="mainbox_title"}{__("bba_com.view_dm_title")} {$dm_data.subject}{/capture}
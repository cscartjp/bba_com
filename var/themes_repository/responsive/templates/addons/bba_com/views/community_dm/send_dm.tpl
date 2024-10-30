<div class="row-fluid">
    <div class="span4">
        {include file="addons/bba_com/views/community/components/home_side.tpl" cp_data=$cp_data}
    </div>
    <div class="span9 ty-account bba-com-dm">

        <form name="community_dm_send_form" enctype="multipart/form-data" action="{""|fn_url}" method="post">
            <input type="hidden" name="from_user_id" value="{$auth.user_id}"/>
            <input type="hidden" name="to_user_id" value="{$to_userdata.user_id}"/>

            {*宛先*}
            <div class="ty-control-group">
                <label for="to_user_id"
                       class="ty-control-group__title cm-required">{__("bba_com.community_dm_to_user_name")}</label>
                <p>{$to_userdata.name}</p>
            </div>


            {*件名 subject*}
            <div class="ty-control-group">
                <label for="subject"
                       class="ty-control-group__title cm-required">{__("bba_com.community_dm_subject")}</label>
                <input type="text" id="subject" name="dm_data[subject]" class="ty-input-text" size="64"
                       value="{$dm_data.subject}"/>
            </div>

            {*本文 message*}
            <div class="ty-control-group">
                <label for="message"
                       class="ty-control-group__title cm-required">{__("bba_com.community_dm_message")}</label>
                <textarea id="message" name="dm_data[message]" class="ty-input-textarea" rows="5"
                          cols="50">{$dm_data.message}</textarea>
            </div>


            <div class="ty-profile-field__buttons buttons-container">
                {include file="buttons/button.tpl" but_name="dispatch[community_dm.send_dm]" but_text=__("bba_com.send_dm") but_role="submit" but_meta="ty-btn__secondary"}
            </div>

        </form>

    </div>
    <div class="span3">
        {include file="addons/bba_com/views/community/components/general_side.tpl"}
    </div>
</div>


{capture name="mainbox_title"}{__("bba_com.send_dm")}{/capture}
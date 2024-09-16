<div class="row-fluid">
    <div class="span16">

        <div class="bba-community-header">

            {*登録した画像 group_icon*}
            {if $group_data.group_icon}
                <div class="bba-community-image">
                    {include file="common/image.tpl" images=$group_data.group_icon no_ids=true image_width=200 image_height=200 class="bba-community-group-image-icon"}
                </div>
            {/if}
            <h2>{$group_data.group}</h2>

        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="span5">
        {include file="addons/bba_com/views/community/components/group_side.tpl" group_data=$group_data}
    </div>
    <div class="span11">
        <div class="bba-timeline">


            {* 書き込む *}
            <div class="bba-community-new-post">
                <form action="{""|fn_url}" method="post" class="posts-form" name="post_new" id="post_new">
                    <input type="hidden" name="redirect_url" value="{$config.current_url}"/>
                    {* G：グループに投稿する*}
                    <input type="hidden" name="new_post[post_type]" value="G"/>
                    {* グループID *}
                    <input type="hidden" name="new_post[object_id]" value="{$group_data.group_id}"/>

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
                            <label for="new_post_article"
                                   class="ty-control-group__title cm-required">{__("bba_com.community_post_article")}</label>

                            <textarea id="new_post_article" name="new_post[article]"
                                      cols="20" rows="8" class="ty-input-text-large"
                                      placeholder="{__("bba_com.group_post_article_ph")}"></textarea>
                        </div>
                    </div>
                    <div class="bba-community-new-post-footer">
                        <div class="buttons-container">
                            {include file="buttons/button.tpl" but_text=__("bba_com.community_post_new") but_meta="ty-btn__secondary bba-community-new-post-btn" but_role="submit" but_name="dispatch[community_groups.add_new_post]"}
                        </div>
                    </div>
                </form>
            </div>


            {* タイムライン*}
            <div class="bba-community-posts">

                {include file="common/pagination.tpl"}

                {if $user_posts}
                    {assign var="post_user_icon_size" value=60}
                    {foreach from=$user_posts item=up}
                        {*コンテンツ*}
                        {include file="addons/bba_com/views/community/components/user_post_content.tpl" group_data=$group_data post_data=$up post_user_icon_size=$post_user_icon_size}
                    {/foreach}

                {else}
                    <p class="ty-no-items">{__("no_items")}</p>
                {/if}

                {*                {include file="common/pagination.tpl" extra_id=",vendors_map_container*" full_render=true }*}
                {include file="common/pagination.tpl" full_render=true }

            </div>
        </div>
    </div>
</div>

<script>
    (function (_, $) {
        {*$.ceEvent('on', 'ce.commoninit', function (context) {});*}

        //いいね数の更新 ajaxでデータがassignされた後に実行される
        $.ceEvent('on', 'ce.ajaxdone', function (elms, inline_scripts, params, data, response_text) {
            //response_textはJSON形式で返ってくるので、JSON.parseでオブジェクトに変換する
            if (data.text) {
                let response_obj = JSON.parse(data.text);
                {*#like_counter_{response_obj.post_id}の中身をresponse_obj.like_countに変更する*}
                if (response_obj.post_id && response_obj.like_count) {
                    $('#like_counter_' + response_obj.post_id).text(response_obj.like_count);
                }
            }
        });

        //.bba-community-post-comment-btnがクリックされたらdata-post-idを取得して、アラートとして表示する
        $(document).on('click', '.bba-community-post-comment-btn', function () {
            const postId = $(this).data('post-id');
            $("#comment_to_" + postId).toggle();
        });
    })(Tygh, Tygh.$);
</script>

{*{capture name="mainbox_title"}{__("bba_com.community_my_profile")}{/capture}*}
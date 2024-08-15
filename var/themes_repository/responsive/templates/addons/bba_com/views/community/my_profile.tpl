<div class="row-fluid">
    <div class="span16">
        {include file="addons/bba_com/views/community/components/my_profile_top.tpl" cp_data=$cp_data}
    </div>
</div>

<div class="row-fluid">
    <div class="span6">
        {include file="addons/bba_com/views/community/components/my_profile_side.tpl" cp_data=$cp_data}
    </div>
    <div class="span10">


        <div class="bba-timeline">
            {* 書き込む *}
            <div class="bba-community-new-post">
                <form action="{""|fn_url}" method="post" class="posts-form" name="post_new" id="post_new">

                    <input type="hidden" name="redirect_url" value="{$config.current_url}"/>
                    {* T：タイムラインに投稿する*}
                    <input type="hidden" name="new_post[post_type]" value="T"/>

                    <div class="bba-community-new-post-header">
                        <div class="bba-community-new-post-user-icon">
                            {assign var="post_user_icon_size" value=60}
                            {include file="common/image.tpl" image_width=$post_user_icon_size image_height=$post_user_icon_size images=$cp_data.profile_image no_ids=true class="bba-post-user-icon"}
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
                                      placeholder="{__("bba_com.community_post_article_ph")}"></textarea>
                        </div>
                    </div>
                    <div class="bba-community-new-post-footer">
                        <div class="buttons-container">
                            {include file="buttons/button.tpl" but_text=__("bba_com.community_post_new") but_meta="ty-btn__secondary bba-community-new-post-btn" but_role="submit" but_name="dispatch[community.add_new_post]"}
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
                        {include file="addons/bba_com/views/community/components/user_post_content.tpl" cp_data=$cp_data post_data=$up post_user_icon_size=$post_user_icon_size}
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

        //.bba-community-post-comment-btnがクリックされたらdata-post-idを取得して、アラートとして表示する
        $(document).on('click', '.bba-community-post-comment-btn', function () {
            const postId = $(this).data('post-id');
            $("#comment_to_" + postId).toggle();
        });
    })(Tygh, Tygh.$);
</script>

{capture name="mainbox_title"}{__("bba_com.community_my_profile")}{/capture}
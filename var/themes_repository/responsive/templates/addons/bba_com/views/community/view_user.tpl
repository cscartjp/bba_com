<div class="row-fluid">
    <div class="span16">
        {include file="addons/bba_com/views/community/components/user_profile_top.tpl" cp_data=$cp_data}


        {*登録した画像 community_image_1、community_image_2、community_image_3*}
        {if $cp_data.community_image_1}
            <div class="bba-community-images">
                {if $cp_data.community_image_1}
                    <div class="bba-community-image">
                        {include file="common/image.tpl" images=$cp_data.community_image_1 no_ids=true}
                    </div>
                {/if}
                {if $cp_data.community_image_2}
                    <div class="bba-community-image">
                        {include file="common/image.tpl" images=$cp_data.community_image_2 no_ids=true}
                    </div>
                {/if}
                {if $cp_data.community_image_3}
                    <div class="bba-community-image">
                        {include file="common/image.tpl" images=$cp_data.community_image_3 no_ids=true}
                    </div>
                {/if}
            </div>
        {/if}


    </div>
</div>

<div class="row-fluid">
    <div class="span6">

        {* このユーザーが出品した商品を確認するボタン companies.products&company_id=X*}
        <a href="{"companies.products&company_id=`$cp_data.company_id`"|fn_url}" class="btn btn-primary">
            {__("bba_com.community_my_profile_products")}
        </a>


        {include file="addons/bba_com/views/community/components/my_profile_side.tpl" cp_data=$cp_data}
    </div>
    <div class="span10">
        <div class="bba-timeline">
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
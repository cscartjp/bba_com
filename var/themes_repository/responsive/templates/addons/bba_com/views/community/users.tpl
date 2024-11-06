<div class="row-fluid">
    <div class="span4">
        {include file="addons/bba_com/views/community/components/home_side.tpl" cp_data=$cp_data}
    </div>
    <div class="span9 bba-com-search-result">


        <div class="bba-community-users">
            {if $search.tag}
                <h3>
                    {__("bba_com.people_search_result")}:
                    <span class="ty-search-block__query">{__("bba_com.tags")}: {$search.tag}</span>
                </h3>
            {/if}


            {if $people}
                <div class="bba-community-friends">
                    {foreach from=$people item=pp}
                        <div class="bba-community-friend">
                            <a href="{"community.view_user?user_id=`$pp.user_id`"|fn_url}">
                                <div class="bba-community-friend-image">
                                    {assign var="friend_image_size" value=60}
                                    {include file="common/image.tpl" image_width=$friend_image_size image_height=$friend_image_size images=$pp.profile_image no_ids=true class="bba-community-friend-image-photo"}
                                </div>
                                <div class="bba-community-friend-profile">
                                    <h4>{$pp.name}</h4>
                                    <p>{$pp.company_name}</p>
                                </div>
                            </a>
                        </div>
                    {/foreach}
                </div>
            {else}
                <p class="ty-no-items">{__("no_items")}</p>
            {/if}
        </div>


    </div>
    <div class="span3">
        {include file="addons/bba_com/views/community/components/general_side.tpl"}
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

        //.bba-community-post-comment-btnがクリックされたらdata-post-idを取得して、コメント欄をtoggleする
        $(document).on('click', '.bba-community-post-comment-btn', function () {
            const postId = $(this).data('post-id');
            $("#comment_to_" + postId).toggle();
        });

        //ページが読み込まれたらキーワードをハイライトする
        $(document).ready(function () {
            const keyword = "{$search.cq}";
            if (keyword) {
                const reg = new RegExp(keyword, "g");
                $(".bba-community-posts").each(function () {
                    const content = $(this).html();
                    $(this).html(content.replace(reg, "<span style='background-color: yellow; font-weight: bold'>" + keyword + "</span>"));
                });
            }
        });

    })(Tygh, Tygh.$);
</script>


{capture name="mainbox_title"}{__("bba_com.users")}{/capture}
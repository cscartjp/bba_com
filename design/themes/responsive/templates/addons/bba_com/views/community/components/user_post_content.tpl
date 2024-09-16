{if $post_data.user_id != $auth.user_id}
    {assign var="not_my_post" value="Y"}
{else}
    {assign var="not_my_post" value="N"}
{/if}
<div id="user_post_{$post_data.post_id}" class="bba-community-post">

    {*    {$post_data|fn_print_r}*}

    <div class="bba-community-post-header">
        <div class="bba-community-post-user-icon">
            {if $not_my_post == "Y"}<a href="{"community.view_user?user_id=`$post_data.user_id`"|fn_url}">{/if}
            {include file="common/image.tpl" image_width=$post_user_icon_size image_height=$post_user_icon_size images=$post_data.profile_image no_ids=true class="bba-post-user-icon"}
                {if $not_my_post == "Y"}</a>{/if}
        </div>
        <div class="bba-community-post-header-name">
            <h4>
                {if $not_my_post == "Y"}<a href="{"community.view_user?user_id=`$post_data.user_id`"|fn_url}">{/if}
                    {$post_data.poster_name}
                    {if $not_my_post == "Y"}</a>{/if}
            </h4>
            <p>{$post_data.timestamp}</p>
        </div>
    </div>
    <div class="bba-community-post-body">
        {*lazyloadをscripts.post.tplで読み込む必要がある*}
        {if $post_data.ogp_info.image}
            <div class="bba-community-post-ogp-info">
                <a href="{$post_data.ogp_info.link}" target="_blank">
                    <div class="bba-community-ogp-image">
                        <img class="lazyload" data-src="{$post_data.ogp_info.image}"
                             src="/images/no_image.png" alt="{$post_data.ogp_info.title}"/>
                    </div>
                    <div class="bba-community-ogp-details">
                        <p class="bba-community-ogp-title">{$post_data.ogp_info.title}</p>
                        <p class="bba-community-ogp-description">{$post_data.ogp_info.description}</p>
                    </div>
                </a>
            </div>
        {/if}

        <div class="bba-community-post-article">
            {$post_data.article nofilter}
        </div>
    </div>
    <div class="bba-community-post-footer">
        {*いいね！*}
        <div class="bba-community-post-control">
            <div class="bba-community-post-footer-like">
                <a href="{"community.like?post_id=`$post_data.post_id`"|fn_url}"
                   class="bba-community-post-like-btn cm-ajax cm-post"
                   data-post-id="{$post_data.post_id}">
                    <i class="ty-icon-heart"></i>
                    <span id="like_counter_{$post_data.post_id}">{$post_data.likes_count|default:"0"}</span>
                    <span>{__("bba_com.like_the_post")}</span>
                </a>
            </div>
            {*コメント*}
            <div class="bba-community-post-footer-comment">
                <a href="javascript:void(0);" class="bba-community-post-comment-btn"
                   data-post-id="{$post_data.post_id}">
                    <i class="ty-icon-bubble"></i>
                    <span>{$post_data.comment_count}</span>
                    <span>{__("bba_com.comment_the_post")}</span>
                </a>
            </div>
        </div>

        {*コメント一覧*}
        {if $post_data.comments}
            {assign var="comment_user_icon_size" value=40}

            <div id="comments_area_{$post_data.post_id}" class="bba-community-post-comment-list">
                {foreach from=$post_data.comments item=comment}
                    {if $comment.user_id != $auth.user_id}
                        {assign var="not_my_post" value="not-my-comment"}
                    {else}
                        {assign var="not_my_post" value=""}
                    {/if}
                    <div class="bba-community-post-comment-item {$not_my_post}">


                        {*アイコン*}
                        <div class="bba-community-post-comment-item-user-icon">
                            {if $not_my_post}<a href="{"community.view_user?user_id=`$comment.user_id`"|fn_url}">{/if}
                            {include file="common/image.tpl" image_width=$comment_user_icon_size image_height=$comment_user_icon_size images=$comment.profile_image no_ids=true class="bba-post-comment-user-icon"}
                                {if $not_my_post}</a>{/if}
                        </div>

                        {*コメント内容*}
                        <div class="bba-community-post-comment-item-body">
                            <div class="bba-community-post-comment-article">
                                <h4>
                                    {if $not_my_post}<a
                                            href="{"community.view_user?user_id=`$comment.user_id`"|fn_url}">{/if}
                                        {$comment.poster_name}
                                        {if $not_my_post}</a>{/if}
                                </h4>
                                <p>{$comment.article nofilter}</p>
                            </div>

                            <div class="bba-community-post-comment-timestamp">
                                {$comment.timestamp}
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
        {/if}



        {*コメント書き込み*}
        <div id="comment_to_{$post_data.post_id}" class="bba-community-post-comment{if $post_data.comments} open{/if}">
            <form action="{""|fn_url}" method="post" class="posts-form cm-ajax-- cm-post-- cm-ajax-full-render--"
                  id="post_comment_form_{$post_data.post_id}"
                  name="post_comment_form_{$post_data.post_id}">

                <input type="hidden" name="result_ids" value="user_post_{$post_data.post_id}"/>
                <input type="hidden" name="new_comment[parent_id]" value="{$post_data.post_id}"/>
                {* C：コメント*}
                <input type="hidden" name="new_comment[post_type]" value="C"/>
                <input type="hidden" name="redirect_url" value="{$config.current_url}#user_post_{$post_data.post_id}"/>

                <div class="bba-community-comment-form">
                    <div class="ty-control-group">
                        <label for="comment_to_{$post_data.post_id}"
                               class="ty-control-group__title bba-community-post-comment-form-title cm-required">{__("bba_com.comment_the_post")}</label>
                        <textarea id="comment_to_{$post_data.post_id}" class="bba-community-post-comment-textarea"
                                  name="new_comment[article]"
                                  placeholder="{__("bba_com.comment_the_post_ph")}"></textarea>
                    </div>

                    <div class="bba-community-post-comment-form-btn">
                        {include file="buttons/button.tpl" but_text=__("bba_com.comment_the_post") but_meta="ty-btn__primary bba-community-post-comment-btn" but_role="submit" but_name="dispatch[community.add_new_comment]"}
                    </div>
                </div>
            </form>

        </div>
    </div>

</div>

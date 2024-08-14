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
                <form action="{""|fn_url}" method="post" class="posts-form">

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
                        <div class="bba-community-post">
                            <div class="bba-community-post-header">
                                <div class="bba-community-post-user-icon">
                                    {include file="common/image.tpl" image_width=$post_user_icon_size image_height=$post_user_icon_size images=$cp_data.profile_image no_ids=true class="bba-post-user-icon"}
                                </div>
                                <div class="bba-community-post-header-name">
                                    <h4>{$cp_data.name}</h4>
                                    <p>{$up.timestamp}</p>
                                </div>
                            </div>
                            <div class="bba-community-post-body">

                                {if $up.ogp_info.image}
                                    <div class="bba-community-post-ogp-info">
                                        <a href="{$up.ogp_info.link}" target="_blank">
                                            <div class="bba-community-ogp-image">
                                                {*                                                <img src="{$up.ogp_info.image}"/>*}

                                                <img class="lazyload" data-src="{$up.ogp_info.image}"
                                                     src="/images/no_image.png" alt=""/>
                                            </div>
                                            <p class="bba-community-ogp-title">{$up.ogp_info.title}</p>
                                            <p class="bba-community-ogp-description">{$up.ogp_info.description}</p>
                                        </a>
                                    </div>
                                {/if}
                                
                                <div class="bba-community-post-article">
                                    {$up.article nofilter}
                                </div>
                            </div>
                            <div class="bba-community-post-footer">
                                <div class="bba-community-post-footer-like">
                                    <i class="ty-icon-heart"></i>
                                    <span>いいね！</span>
                                </div>
                                <div class="bba-community-post-footer-comment">
                                    <i class="ty-icon-bubble"></i>
                                    <span>コメント</span>
                                </div>
                            </div>
                        </div>
                    {/foreach}

                {else}
                    <p class="ty-no-items">{__("no_items")}</p>
                {/if}

                {*                {include file="common/pagination.tpl" extra_id=",vendors_map_container*" full_render=true }*}
                {include file="common/pagination.tpl" full_render=true }

            </div>


            {*            <pre>*}
            {*    {$cp_data|var_dump}*}
            {*</pre>*}

        </div>
    </div>
</div>


{*<pre>*}
{*    {$auth|var_dump}*}
{*</pre>*}




{capture name="mainbox_title"}{__("bba_com.community_my_profile")}{/capture}
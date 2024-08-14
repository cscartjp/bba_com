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
                    <input type="hidden" name="new_post[type]" value="T"/>

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

                <div class="bba-community-post">
                    <div class="bba-community-post-header">
                        <div class="bba-community-post-user-icon">
                            {assign var="post_user_icon_size" value=60}
                            {include file="common/image.tpl" image_width=$post_user_icon_size image_height=$post_user_icon_size images=$cp_data.profile_image no_ids=true class="bba-post-user-icon"}
                        </div>
                        <div class="bba-community-post-header-name">
                            <h4>{$cp_data.name}</h4>
                            <p>2021/01/01 12:00</p>
                        </div>
                    </div>
                    <div class="bba-community-post-body">
                        <p>
                            1935年に日本で創立して以来、当社は技術力を発揮し、常に革新を追求してきました。世界をリードするDXパートナーとして、信頼できるテクノロジー・サービス、ソリューション、製品を幅広く提供して、お客様のDX実現を支援します。</p>
                        <p>
                            同時に、私たちは国連の持続可能な開発目標(SDGs)への貢献に向けて、デジタルの力によって業種間の垣根を越えたエコシステムの形成をリードし、共感していただけるステークホルダーの皆様とスケールある価値創造に踏み出していきたいと考えます。
                        </p>
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
                <div class="bba-community-post">
                    <div class="bba-community-post-header">
                        <div class="bba-community-post-user-icon">
                            {assign var="post_user_icon_size" value=60}
                            {include file="common/image.tpl" image_width=$post_user_icon_size image_height=$post_user_icon_size images=$cp_data.profile_image no_ids=true class="bba-post-user-icon"}
                        </div>
                        <div class="bba-community-post-header-name">
                            <h4>{$cp_data.name}</h4>
                            <p>2021/01/01 12:00</p>
                        </div>
                    </div>
                    <div class="bba-community-post-body">
                        <p>
                            1935年に日本で創立して以来、当社は技術力を発揮し、常に革新を追求してきました。世界をリードするDXパートナーとして、信頼できるテクノロジー・サービス、ソリューション、製品を幅広く提供して、お客様のDX実現を支援します。</p>
                        <p>
                            同時に、私たちは国連の持続可能な開発目標(SDGs)への貢献に向けて、デジタルの力によって業種間の垣根を越えたエコシステムの形成をリードし、共感していただけるステークホルダーの皆様とスケールある価値創造に踏み出していきたいと考えます。
                        </p>
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
                <div class="bba-community-post">
                    <div class="bba-community-post-header">
                        <div class="bba-community-post-user-icon">
                            {assign var="post_user_icon_size" value=60}
                            {include file="common/image.tpl" image_width=$post_user_icon_size image_height=$post_user_icon_size images=$cp_data.profile_image no_ids=true class="bba-post-user-icon"}
                        </div>
                        <div class="bba-community-post-header-name">
                            <h4>{$cp_data.name}</h4>
                            <p>2021/01/01 12:00</p>
                        </div>
                    </div>
                    <div class="bba-community-post-body">
                        <p>
                            1935年に日本で創立して以来、当社は技術力を発揮し、常に革新を追求してきました。世界をリードするDXパートナーとして、信頼できるテクノロジー・サービス、ソリューション、製品を幅広く提供して、お客様のDX実現を支援します。</p>
                        <p>
                            同時に、私たちは国連の持続可能な開発目標(SDGs)への貢献に向けて、デジタルの力によって業種間の垣根を越えたエコシステムの形成をリードし、共感していただけるステークホルダーの皆様とスケールある価値創造に踏み出していきたいと考えます。
                        </p>
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


            </div>


            <pre>
    {$cp_data|var_dump}
</pre>

        </div>
    </div>
</div>


{*<pre>*}
{*    {$auth|var_dump}*}
{*</pre>*}




{capture name="mainbox_title"}{__("bba_com.community_my_profile")}{/capture}
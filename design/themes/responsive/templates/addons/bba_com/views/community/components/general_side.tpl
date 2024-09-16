{*友達を検索する*}
<div class="community-general-side">
    <div class="ty-search-block">
        <form action="{""|fn_url}" name="search_form" method="get">
            <input type="hidden" name="match" value="all"/>
            <input type="hidden" name="search_performed" value="Y"/>

            {strip}
                {assign var="search_title" value=__("search")}
                <input type="text" name="cq" value="{$search.cq}" id="search_input_community"
                       title="{__("bba_com.search_community")}" class="ty-search-block__input cm-hint"/>
                {include file="buttons/magnifier.tpl" but_name="community.search" alt=__("search")}
            {/strip}
        </form>
    </div>
</div>

<div class="community-general-side">
    <h4>{__("bba_com.information")}</h4>
    <div>
        <a href="https://tai-gee.com/blog/30494" target="_blank">
            <img src="/images/community/bn.jpg" alt="">
        </a>
    </div>
</div>
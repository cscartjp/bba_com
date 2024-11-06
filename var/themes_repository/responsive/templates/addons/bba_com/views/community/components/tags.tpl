{if $object.tags}
    <div class="bba-tags">
        <div class="ty-control-group">
            <ul class="ty-tags-list clearfix">
                {foreach from=$object.tags item="tag" name="tags"}
                    {$tag_name = $tag.tag|escape:url}
                    <li class="ty-tags-list__item">
                        <a class="ty-tags-list__a" href="{"community.users?tag=`$tag_name`"|fn_url}">
                            {$tag.tag}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
{/if}
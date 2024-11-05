<div class="" id="tag_cloud">

    {script src="js/addons/bba_com/tags_autocomplete.js"}


    <div class="ty-control-group">
        <label class="ty-control-group__title">{__("tags")}:</label>
        <div class="ty-controls">
            <ul id="my_tags">
                <input type="hidden" id="object_id" value="{$object_id}"/>
                <input type="hidden" id="object_type" value="{$object_type}"/>
                <input type="hidden" name="{$input_name}[tags][]" value=""/>
                <input type="hidden" id="object_name" value="{$input_name}[tags][]"/>
                {foreach from=$object.tags item="tag" name="tags"}
                    <li>{$tag.tag}</li>{/foreach}
            </ul>
        </div>
    </div>
</div>




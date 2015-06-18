{def
	$class_list = $class_attribute.content.selected_class_list
	$all_class_list = fetch( 'class', 'list' )
}

<div class="block">
    <label for="eccaorl_allowed_{$class_attribute.id}">{'Allowed classes'|i18n( 'design/standard/class/datatype' )}:</label>
    <select id="eccaorl_allowed_{$class_attribute.id}" name="ezcca_nxc_world_map_class_list_{$class_attribute.id}[]" multiple="multiple" title="{'Select which classes can be shown on the map'|i18n( 'design/standard/class/datatype' )}">
    <option value="0" {if $class_list|contains( 0 )}selected="selected"{/if}>{'Custom tags'|i18n( 'extension/nxc_world_map' )}</option>
	{foreach $all_class_list as $class}
    <option value="{$class.id|wash}" {if $class_list|contains( $class.id )}selected="selected"{/if}>{$class.name}</option>
    {/foreach}
    </select>
</div>
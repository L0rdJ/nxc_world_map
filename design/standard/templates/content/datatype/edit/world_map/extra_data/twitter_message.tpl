<fieldset>
	<legend>{'Description'|i18n( 'extension/nxc_world_map' )}</legend>
	<textarea class="box" name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_extra_data[description]">{$world_map_point.extra_data['description']}</textarea>
</fieldset>
<fieldset>
	<legend>{'URL'|i18n( 'extension/nxc_world_map' )}</legend>
	<input class="box" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_extra_data[url]" value="{$world_map_point.extra_data['url']}" />
</fieldset>
<fieldset>
	<legend>{'Description'|i18n( 'extension/nxc_world_map' )}</legend>
	<textarea class="box" name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_extra_data[description]">{$world_map_point.extra_data['description']}</textarea>
</fieldset>
<fieldset>
	<legend>{'Link 1 text'|i18n( 'extension/nxc_world_map' )}</legend>
	<input class="box" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_extra_data[link_1_text]" value="{$world_map_point.extra_data['link_1_text']}" />
</fieldset>
<fieldset>
	<legend>{'Link 1 Related Object'|i18n( 'extension/nxc_world_map' )}</legend>
	{def $related_object = fetch( 'content', 'object', hash( 'object_id', $world_map_point.extra_data['link_1_related_object_id'] ))}
	{if $related_object}{content_view_gui view=embed content_object=$related_object}{else}{'Choose an object'|i18n( 'extension/nxc_world_map' )}{/if} <input class="button update_current_point" type="submit" name="CustomActionButton[{$attribute.id}_browse-link_1_related_object_id]" value="{'Browse'|i18n( 'extension/nxc_world_map' )}" id="ezcoa_nxc_world_map_{$attribute.id}_custom_action_browse_link_1_related_object_{$world_map_point.id}" />
	{undef $related_object}
</fieldset>
<fieldset>
	<legend>{'Link 1 URL'|i18n( 'extension/nxc_world_map' )}</legend>
	<input class="box" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_extra_data[link_1_url]" value="{$world_map_point.extra_data['link_1_url']}" />
</fieldset>
<fieldset>
	<legend>{'Link 2 text'|i18n( 'extension/nxc_world_map' )}</legend>
	<input class="box" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_extra_data[link_2_text]" value="{$world_map_point.extra_data['link_2_text']}" />
</fieldset>
<fieldset>
	<legend>{'Link 2 Related Object'|i18n( 'extension/nxc_world_map' )}</legend>
	{def $related_object = fetch( 'content', 'object', hash( 'object_id', $world_map_point.extra_data['link_2_related_object_id'] ))}
	{if $related_object}{content_view_gui view=embed content_object=$related_object}{else}{'Choose an object'|i18n( 'extension/nxc_world_map' )}{/if} <input class="button update_current_point" type="submit" name="CustomActionButton[{$attribute.id}_browse-link_2_related_object_id]" value="{'Browse'|i18n( 'extension/nxc_world_map' )}" id="ezcoa_nxc_world_map_{$attribute.id}_custom_action_browse_link_2_related_object_{$world_map_point.id}" />
	{undef $related_object}
</fieldset>
<fieldset>
	<legend>{'Link 2 URL'|i18n( 'extension/nxc_world_map' )}</legend>
	<input class="box" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_extra_data[link_2_url]" value="{$world_map_point.extra_data['link_2_url']}" />
</fieldset>

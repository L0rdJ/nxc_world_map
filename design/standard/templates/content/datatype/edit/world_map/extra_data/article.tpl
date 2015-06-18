<fieldset>
	<legend>{'Link text'|i18n( 'extension/nxc_world_map' )}</legend>
	<input class="box" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_extra_data[link_text]" value="{$world_map_point.extra_data['link_text']}" />
</fieldset>
<fieldset>
	<legend>{'Link Related Object'|i18n( 'extension/nxc_world_map' )}</legend>
	{def $related_object = fetch( 'content', 'object', hash( 'object_id', $world_map_point.extra_data['link_related_object_id'] ))}
	{if $related_object}{content_view_gui view=embed content_object=$related_object}{else}{'Choose an object'|i18n( 'extension/nxc_world_map' )}{/if} <input class="button update_current_point" type="submit" name="CustomActionButton[{$attribute.id}_browse-link_related_object_id]" value="{'Browse'|i18n( 'extension/nxc_world_map' )}" id="ezcoa_nxc_world_map_{$attribute.id}_custom_action_browse_{$point_class.id}_link_related_object_id_{$world_map_point.id}" />
	{undef $related_object}
</fieldset>
<fieldset>
	<legend>{'Link URL'|i18n( 'extension/nxc_world_map' )}</legend>
	<input class="box" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_extra_data[link_url]" value="{$world_map_point.extra_data['link_url']}" />
</fieldset>
<fieldset>
	<legend>{'Marker color'|i18n( 'extension/nxc_world_map' )}</legend>
	<select name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_extra_data[marker_color]">
		<option value="black">{'Black'|i18n( 'extension/nxc_world_map' )}</option>
		<option value="red" {if eq( $world_map_point.extra_data['marker_color'], 'red')}selected="selected"{/if}>{'Red'|i18n( 'extension/nxc_world_map' )}</option>
	</select>
</fieldset>
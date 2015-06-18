<div class="nxc_world_map_point_body">
{attribute_view_gui attribute=$point.related_object.data_map.intro}
</div>

<div class="nxc_world_map_point_footer">
	{if and( $point.extra_data['link_text'], $point.extra_data['link_url'] )}
	<div>
		<a href="{$point.extra_data['link_url']}">{$point.extra_data['link_text']}</a>
	</div>
	{/if}
	{def $related_object = fetch( 'content', 'object', hash( 'object_id', $point.extra_data['link_related_object_id'] ))}
	{if $related_object}{content_view_gui view=embed content_object=$related_object}
	{undef $related_object}
</div>
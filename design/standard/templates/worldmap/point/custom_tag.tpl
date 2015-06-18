<div class="nxc_world_map_point_header">
{$point.title}
</div>

<div class="nxc_world_map_point_body">
{$point.extra_data.description}
</div>

<div class="nxc_world_map_point_footer">
	{if and( $point.extra_data['link_1_text'], $point.extra_data['link_1_url'] )}
	<div>
		<a href="{$point.extra_data['link_1_url']}">{$point.extra_data['link_1_text']}</a>
	</div>
	{/if}
	{def $related_object = fetch( 'content', 'object', hash( 'object_id', $point.extra_data['link_1_related_object_id'] ))}
	{if $related_object}{content_view_gui view=embed content_object=$related_object}
	{undef $related_object}

	{if and( $point.extra_data['link_2_text'], $point.extra_data['link_2_url'] )}
	<div>
		<a href="{$point.extra_data['link_2_url']}">{$point.extra_data['link_2_text']}</a>
	</div>
	{/if}
	{def $related_object = fetch( 'content', 'object', hash( 'object_id', $point.extra_data['link_1_related_object_id'] ))}
	{if $related_object}{content_view_gui view=embed content_object=$related_object}
	{undef $related_object}
</div>
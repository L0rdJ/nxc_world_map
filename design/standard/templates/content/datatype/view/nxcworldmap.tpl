{def
	$attribute_content = $attribute.content
	$world_map_points  = $attribute_content.points
}

<h1>{$attribute.content.name}</h1>
<div id="ezcoa_nxc_world_map_view_{$attribute.id}" class="ezcoa_nxc_world_map_preview">
</div>

{literal}
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
jQuery( function() {
	var options = {
		'zoom': {/literal}{$attribute_content.zoom_level}{literal},
		'center': new google.maps.LatLng( {/literal}{$attribute_content.latitude}{literal}, {/literal}{$attribute_content.longitude}{literal} ),
		'mapTypeId': google.maps.MapTypeId.{/literal}{$attribute_content.map_type_name}{literal}
    };
	var map = new google.maps.Map(
		document.getElementById( 'ezcoa_nxc_world_map_view_{/literal}{$attribute.id}{literal}' ),
		options
	);
	var infoWindow = new google.maps.InfoWindow();

	{/literal}{foreach $world_map_points as $point}{literal}
	var marker{/literal}{$point.id}{literal} = new google.maps.Marker( {
		'map': map,
		'position': new google.maps.LatLng( {/literal}{$point.latitude}{literal}, {/literal}{$point.longitude}{literal} ),
		'title': '{/literal}{$point.title}{literal}',
		'icon': '{/literal}{$point.map_icon_image}{literal}',
		'clickable': true,
		'draggable': false,
		'flat': true,
		'visible': true
	} );
	google.maps.event.addListener( marker{/literal}{$point.id}{literal}, 'click', function() {
		jQuery.fancybox( {
			'padding' : 10,
			'href' : '{/literal}{concat( 'nxc_world_map/get_point_lightbox_view/', $point.id )|ezurl( 'no' )}{literal}',
			'title' : '{/literal}{$point.title}{literal}',
			'transitionIn' : 'elastic',
			'transitionOut' : 'elastic'
		} );
	} );
	{/literal}{/foreach}{literal}
} );
</script>
{/literal}

{undef $attribute_content $world_map_points}
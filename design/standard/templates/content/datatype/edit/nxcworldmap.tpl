{def
	$map_types = hash(
		1, 'Roadmap'|i18n( 'extension/nxc_world_map' ),
		2, 'Satellite'|i18n( 'extension/nxc_world_map' ),
		3, 'Hybrid'|i18n( 'extension/nxc_world_map' ),
		4, 'Terrain'|i18n( 'extension/nxc_world_map' )
	)
	$points_classes = $attribute.contentclass_attribute.content.selected_class_list
	$attribute_content = $attribute.content
}

<div id="ezcoa_nxc_world_map_{$attribute.id}_container">
	<fieldset>
		<legend>{'Name'|i18n( 'extension/nxc_world_map' )}</legend>
		<input class="box" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_name" value="{$attribute_content.name}" />
	</fieldset>

	<fieldset>
		<legend>{'Start coordinates'|i18n( 'extension/nxc_world_map' )}</legend>
		{'Latitude'|i18n( 'extension/nxc_world_map' )} <input class="nxc_world_map_lat" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_latitude" size="20" value="{$attribute_content.latitude}" />
		{'Longitude'|i18n( 'extension/nxc_world_map' )} <input class="nxc_world_map_lng" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_longitude" size="20" value="{$attribute_content.longitude}" /> <a href="#nxc_world_map_use_map_container_{$attribute.id}" class="nxc_world_map_use_map"><img alt="{'Open the map'|i18n( 'extension/nxc_twitter_api' )}" src="{'worldmap/globe.png'|ezimage( 'no' )}" /></a>
	</fieldset>

	<fieldset>
		<legend>{'Zoom level'|i18n( 'extension/nxc_world_map' )}</legend>
		<select name="ezcoa_nxc_world_map_{$attribute.id}_zoom_level">
			{for 0 to 20 as $zoom}
			<option value="{$zoom}" {if eq( $zoom, $attribute_content.zoom_level )}selected="selected"{/if}>{$zoom}</option>
			{/for}
		</select>
	</fieldset>

	<fieldset>
		<legend>{'Map type'|i18n( 'extension/nxc_world_map' )}</legend>
		{foreach $map_types as $map_type_id => $map_type_name}
		<input type="radio" name="ezcoa_nxc_world_map_{$attribute.id}_map_type" value="{$map_type_id}" {if eq( $map_type_id, $attribute_content.map_type )}checked="checked"{/if}/> {$map_type_name}
		{/foreach}
	</fieldset>

	<fieldset>
		<legend>{'Relations'|i18n( 'extension/nxc_world_map' )}</legend>

		<div class="nxc_world_map_points_list" id="nxc_world_map_points_list_{$attribute.id}">

			<div class="nxc_world_map_point_classes">
				<ul>
					{def $point_class = false()}
					{foreach $points_classes as $class_id}
						{if eq( $class_id, 0 )}
							<li id="nxc_world_map_point_class_0"><a href="#">{'Text'|i18n( 'extension/nxc_world_map' )}</a></li>
						{else}
							{set $point_class = fetch( 'content', 'class', hash( 'class_id', $class_id ) )}
							<li id="nxc_world_map_point_class_{$class_id}"><a href="#">{$point_class.name}</a></li>
						{/if}
					{/foreach}
					{undef $point_class}
				</ul>
			</div>
			<div class="clear"></div>

			{def
				$point_class = false()
				$world_map_points = $attribute_content.points
			}
			<div class="nxc_world_map_points">
				{foreach $points_classes as $class_id}
					<div class="nxc_world_map_points_of_class" style="display: none;">
						{foreach $world_map_points as $world_map_point}
							{if ne( $world_map_point.related_object_class_id, $class_id )}{skip}{/if}

							{if ne( $class_id, 0 )}
							<fieldset>
								<legend>{'Relation'|i18n( 'extension/nxc_world_map' )}</legend>
								{if $world_map_point.related_object}{content_view_gui view=embed content_object=$world_map_point.related_object}{else}{'Choose an object'|i18n( 'extension/nxc_world_map' )}{/if} <input class="button update_current_point" type="submit" name="CustomActionButton[{$attribute.id}_browse-related_object_id]" value="{'Browse'|i18n( 'extension/nxc_world_map' )}" id="ezcoa_nxc_world_map_{$attribute.id}_custom_action_browse_{$class_id}_related_object_id_{$world_map_point.id}" />
							</fieldset>
							{/if}

							<fieldset>
								<legend>{'Title'|i18n( 'extension/nxc_world_map' )}</legend>
								<input class="box" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_title" value="{$world_map_point.title}" />
							</fieldset>

							{if eq( $class_id, 0 )}
								{include uri=concat( 'design:content/datatype/edit/world_map/extra_data/custom_tag.tpl' ) attribute=$attribute world_map_point=$world_map_point}
							{else}
								{set $point_class = fetch( 'content', 'class', hash( 'class_id', $class_id ) )}
								{include uri=concat( 'design:content/datatype/edit/world_map/extra_data/', $point_class.identifier, '.tpl' ) attribute=$attribute world_map_point=$world_map_point point_class=$point_class}
							{/if}

							<fieldset>
								<legend>{'Icon coordinates'|i18n( 'extension/nxc_world_map' )}</legend>
								{'Latitude'|i18n( 'extension/nxc_world_map' )} <input class="nxc_world_map_lat" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_latitude" size="20" value="{$world_map_point.latitude}" />
								{'Longitude'|i18n( 'extension/nxc_world_map' )} <input class="nxc_world_map_lng" type="text" name="ezcoa_nxc_world_map_{$attribute.id}_point_{$world_map_point.id}_longitude" size="20" value="{$world_map_point.longitude}" /> <a href="#nxc_world_map_use_map_container_{$attribute.id}" class="nxc_world_map_use_map"><img alt="{'Open the map'|i18n( 'extension/nxc_twitter_api' )}" src="{'worldmap/globe.png'|ezimage( 'no' )}" /></a>
							</fieldset>

							<div class="nxc_world_map_points_controls">
								<input class="button update_current_point" id="ezcoa_nxc_world_map_{$attribute.id}_custom_action_update_point_{$world_map_point.id}" type="submit" name="CustomActionButton[{$attribute.id}_update_point]" value="{'Update'|i18n( 'extension/nxc_world_map' )}" />
								<input class="button update_current_point" id="ezcoa_nxc_world_map_{$attribute.id}_custom_action_remove_point_{$world_map_point.id}" type="submit" name="CustomActionButton[{$attribute.id}_remove_point]" value="{'Remove'|i18n( 'extension/nxc_world_map' )}" />
							</div>
							<div class="clear"></div>

						{/foreach}
					</div>
				{/foreach}
			</div>
			{undef $point_class $world_map_points}

		</div>

		<div>
			<input type="hidden" name="ezcoa_nxc_world_map_{$attribute.id}_new_point_type" id="ezcoa_nxc_world_map_{$attribute.id}_new_point_type" value="" />
			<input class="button" type="submit" name="CustomActionButton[{$attribute.id}_new_point]" value="{'Add new relation'|i18n( 'extension/nxc_world_map' )}" />
		</div>

		<input type="hidden" id="ezcoa_nxc_world_map_{$attribute.id}_current_point" name="ezcoa_nxc_world_map_{$attribute.id}_current_point" value="" />
	</fieldset>
</div>

<div>
	<fieldset>
		<legend>{'Preview'|i18n( 'extension/nxc_world_map' )}</legend>
		{attribute_view_gui attribute=$attribute}
	</fieldset>
</div>

<div style="display: none;">
	<div id="nxc_world_map_use_map_container_{$attribute.id}">
		<div id="nxc_world_map_use_map_block_{$attribute.id}" class="nxc_world_map_use_map_contiat"></div>
		<div id="nxc_world_map_use_map_geocoder_{$attribute.id}" class="nxc_world_map_use_map_geocoder">
			<input type="text" value="" size="64" id="nxc_world_map_use_map_geocoder_{$attribute.id}_adress" /> <input type="button" value="Search" id="nxc_world_map_use_map_geocoder_{$attribute.id}_button" />
		</div>
	</div>
</div>

{def $new_point_http_var = concat( 'ezcoa_nxc_world_map_', $attribute.id, '_new_point_type' )}
{literal}
<script type="text/javascript">
jQuery( function() {
	installNXCTabs(
		'#nxc_world_map_points_list_{/literal}{$attribute.id}{literal}',
		'#{/literal}{$new_point_http_var}{literal}',
		{/literal}{if ezhttp_hasvariable( $new_point_http_var, 'post' )}'nxc_world_map_point_class_{ezhttp( $new_point_http_var, 'post' )}'{else}false{/if}{literal}
	);

	var currentPointEl = jQuery( '#{/literal}ezcoa_nxc_world_map_{$attribute.id}_current_point{literal}' );
	jQuery( '.update_current_point' ).bind( 'click', function( e ) {
		var tmp = jQuery( this ).attr( 'id' ).split( '_' );
		var pointID = tmp[ tmp.length - 1 ];
		currentPointEl.attr( 'value', pointID );
	} );

	var map, lat, lng, marker;
	var attributeID    = {/literal}{$attribute.id}{literal};
	var mapCoordInpurs = {
		'lat': jQuery( 'input[name=ezcoa_nxc_world_map_' + attributeID + '_latitude]' ),
		'lng': jQuery( 'input[name=ezcoa_nxc_world_map_' + attributeID + '_longitude]' )
	};
	var mapZoomInput = jQuery( 'select[name=ezcoa_nxc_world_map_' + attributeID + '_zoom_level]' );
	jQuery( '#ezcoa_nxc_world_map_{/literal}{$attribute.id}{literal}_container a.nxc_world_map_use_map' ).fancybox( {
		'transitionIn': 'none',
		'transitionOut': 'none',
		'type': 'inline'
	} ).bind( 'click', function( e ) {
		var el = jQuery( this );
		lat = jQuery( 'input.nxc_world_map_lat', el.parent() );
		lng = jQuery( 'input.nxc_world_map_lng', el.parent() );

		var center = new google.maps.LatLng(
			mapCoordInpurs.lat.attr( 'value' ),
			mapCoordInpurs.lng.attr( 'value' )
		);
		map = new google.maps.Map(
			document.getElementById( 'nxc_world_map_use_map_block_{/literal}{$attribute.id}{literal}' ),
			{
				'center': center,
				'mapTypeId': google.maps.MapTypeId.ROADMAP,
				'zoom': 10
			}
		);
		marker = new google.maps.Marker( {
			'map': map,
			'position': center
		} );
		map.setZoom( mapZoomInput.attr( 'value' ) * 1 );
		google.maps.event.addListener( map, 'click', function( e ) {
			lat.attr( 'value', e.latLng.lat() );
			lng.attr( 'value', e.latLng.lng() );
			marker.setPosition( e.latLng );
		} );
	} );

	var geocoder = new google.maps.Geocoder();
	var geocoderAdressInput = jQuery( '#nxc_world_map_use_map_geocoder_' + attributeID + '_adress' );
	var geocoderButton      = jQuery( '#nxc_world_map_use_map_geocoder_' + attributeID + '_button' );
	geocoderButton.click( function( e ) {
		var adress = geocoderAdressInput.attr( 'value' );
		if( adress != '' ) {
			geocoder.geocode(
				{ 'address': adress },
				function( results, status ) {
					if( status == google.maps.GeocoderStatus.OK ) {
						lat.attr( 'value', results[0].geometry.location.lat() );
						lng.attr( 'value', results[0].geometry.location.lng() );
						map.setCenter( results[0].geometry.location );
						marker.setPosition( results[0].geometry.location );
					}
				}
			);
		}
	} );
} );
</script>
{/literal}
{undef $map_types $points_classes $attribute_content $new_point_http_var}
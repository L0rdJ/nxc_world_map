function installNXCTabs( content, newPointTypeEl, openedTabID ) {
	content = jQuery( content );
	newPointTypeEl = jQuery( newPointTypeEl );

	var links = jQuery( 'div.nxc_world_map_point_classes li', content );
	var tabs  = jQuery( 'div.nxc_world_map_points div.nxc_world_map_points_of_class', content );

	var currentIndex = 0;
	if( links.length > 0 ) {
		jQuery( content ).animate( { height: jQuery( tabs[ 0 ] ).height() + 50 }, 300);
		links.each(
			function( index ) {
				jQuery( this ).click(
					function( event ) {
						event.preventDefault();
						if ( index != currentIndex ) {
							showTabs( index );
						}
					}
				);
			}
		);

		var showTabs = function( tabIndex ) {
			var pointID = jQuery( links[ tabIndex ] ).attr( 'id' ).replace( 'nxc_world_map_point_class_', '' );
			newPointTypeEl.attr( 'value', pointID );

			jQuery( content ).animate( { height: jQuery( tabs[ tabIndex ] ).height() + 50 }, 300);
			jQuery( links[ currentIndex ] ).removeClass( 'selected' );
			jQuery( tabs[ currentIndex ] ).fadeOut( 300 );
			jQuery( links[ tabIndex ] ).addClass( 'selected' );
			jQuery( tabs[ tabIndex ] ).fadeIn( 500 );

			currentIndex = tabIndex;
		};

		links.each(
			function( index, el ) {
				if( jQuery( el ).attr( 'id' ) == openedTabID ) {
					currentIndex = index;
				}
			}
		);
		showTabs( currentIndex );
	}
}
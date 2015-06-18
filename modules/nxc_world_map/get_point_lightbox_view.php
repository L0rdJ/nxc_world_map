<?php
/**
 * @package nxcWorldMap
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    10 Dec 2010
 **/

$point = nxcWorldMapPoint::fetch( $Params['pointID'] );
if( $point instanceof nxcWorldMapPoint ) {
	echo $point->attribute( 'map_tip_content' );
}
eZExecution::cleanExit();
?>
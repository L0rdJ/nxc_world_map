<?php
/**
 * @package nxcWorldMap
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    10 Dec 2010
 **/

$Module = array(
	'name'            => 'NXC World Map',
 	'variable_params' => true
);

$ViewList = array();
$ViewList['get_point_lightbox_view'] = array(
	'functions'               => array( 'point_view' ),
	'script'                  => 'get_point_lightbox_view.php',
	'params'                  => array( 'pointID' )
);

$FunctionList = array();
$FunctionList['point_view'] = array();
?>
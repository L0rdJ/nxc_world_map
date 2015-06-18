#!/usr/bin/env php
<?php
require 'autoload.php';
ini_set( 'default_socket_timeout', 3600 );
ini_set( 'memory_limit', '1024M' );

$cli = eZCLI::instance();
$cli->setUseStyles( true );

$scriptSettings = array();
$scriptSettings['description'] = 'Parse RSS feeds';
$scriptSettings['use-session'] = true;
$scriptSettings['use-modules'] = true;
$scriptSettings['use-extensions'] = true;
$scriptSettings['site-access'] = 'siteadmin';

$script = eZScript::instance( $scriptSettings );
$script->startup();
$script->initialize();
$options = $script->getOptions(
	'[map_node_id]',
	'[feed_source]',
	array(
		'map_node_id' => 'Map`s node ID',
		'feed_source' => 'Path to the feed'
	)
);
if ( count( $options['arguments'] ) < 2 ) {
    $cli->error( 'Please set Map`s Node ID and Feed source path' );
    $script->shutdown( 1 );
}

$ini           = eZINI::instance();
$userCreatorID = $ini->variable( 'UserSettings', 'UserCreatorID' );
$user          = eZUser::fetch( $userCreatorID );
if( ( $user instanceof eZUser ) === false ) {
    $cli->error( 'Cannot get user object by userID = "' . $userCreatorID . '". ( See site.ini [UserSettings].UserCreatorID )' );
    $script->shutdown( 1 );
}
eZUser::setCurrentlyLoggedInUser( $user, $userCreatorID );


$mapNode = eZContentObjectTreeNode::fetch( $options['arguments'][0] );
if( $mapNode instanceof eZContentObjectTreeNode === false ) {
    $cli->error( 'Map Node dosen`t exist.' );
    $script->shutdown( 1 );
}
$mapDataMap = $mapNode->attribute( 'data_map' );
$worldMap   = $mapDataMap['world_map']->attribute( 'content' );
if( $worldMap instanceof nxcWorldMap === false ) {
	$cli->error( 'Map Node should have NXC World map attribute' );
	$script->shutdown( 1 );
}
$feedSource = $options['arguments'][1];

$message = $cli->stylize( 'green', 'Parsing "' .  $feedSource . '" feed' );
$cli->output( $message );

$DOMDocument = new DOMDocument();
$DOMDocument->loadXML( @file_get_contents( $feedSource ) );
$feedNodes   = $DOMDocument->getElementsByTagName( 'data' );

$geoAttributes = array( 'geopoint', 'position_of_well', 'gps', 'gpslocation', 'gps_coordinates', 'lokation' );
$videoTags     = array( 'Video', 'video' );
$imageTags     = array( 'image', 'picture', 'picture_of_well', 'indsbillede' );
$titleTags     = array( 'location', 'title', 'indsamlingssted', 'village_name', 'overskrift', 'village' );

$items = array();
foreach( $feedNodes as $node ) {
	$id = $node->parentNode->getAttribute( 'id' ) . '/' . $node->getAttribute( 'id' );

	$locationString = null;
	foreach( $geoAttributes as $tag ) {
		$geoNodes = $node->getElementsByTagName( $tag );
		if( $geoNodes->length > 0 ) {
			$locationString = $geoNodes->item( 0 )->nodeValue;
			break;
		}
	}

	$location = false;
	if( $locationString !== null ) {
		$locationInfo = explode( ' ', $locationString );
		if( count( $locationInfo ) >= 2 ) {
			$location = array(
				'latitude'  => $locationInfo[0],
				'longitude' => $locationInfo[1]
			);
		}
	}

	if( $location === false ) {
		$cli->error( 'Node ' . $id . ' is ignored, because it has no geo location' );
		continue;
	}

	$data = array(
		'remote_id' => $id,
		'latitude'  => $location['latitude'],
		'longitude' => $location['longitude'],
	);

	$video = false;
	foreach( $videoTags as $tag ) {
		$videoNodes = $node->getElementsByTagName( $tag );
		if( $videoNodes->length > 0 ) {
			$video = $videoNodes->item( 0 )->nodeValue;
			break;
		}
	}
	if( strlen( $video ) > 0 ) {
		$data['video'] = $video;
	}

	$image = false;
	foreach( $imageTags as $tag ) {
		$imageNodes = $node->getElementsByTagName( $tag );
		if( $imageNodes->length > 0 ) {
			$image = $imageNodes->item( 0 )->nodeValue;
			break;
		}
	}
	if( strlen( $image ) > 0 ) {
		$data['image'] = $image;
	}

	$title = false;
	foreach( $titleTags as $tag ) {
		$titleNodes = $node->getElementsByTagName( $tag );
		if( $titleNodes->length > 0 ) {
			$title = $titleNodes->item( 0 )->nodeValue;
			break;
		}
	}
	if( strlen( $title ) > 0 ) {
		$data['title'] = $title;
	}

	$items[ $id ] = $data;
}

$imageClass = eZContentClass::fetchByIdentifier( 'image' );
$videoClass = eZContentClass::fetchByIdentifier( 'flash_player' );

$pc = new nxcPowerContent( $cli );
foreach( $items as $remoteID => $item ) {
	$object = eZContentObject::fetchByRemoteID( $remoteID );
	if( $object instanceof eZContentObject ) {
		$cli->error( 'Object with remote ID ' . $remoteID . ' is allready created.' );
		continue;
	}

	$temp     = explode( '/', $remoteID );
	$parentID = $temp[0];
	$class = isset( $item['video'] ) ? $videoClass : $imageClass;
	$attributes = array(
		'name' => $item['title']
	);
	if( isset( $item['image'] ) ) {
		$attributes['image'] = 'http://viewworld.dk/data/' . $parentID . '/'. $item['image'];
	}
	if( isset( $item['video'] ) ) {
		$attributes['file'] = 'http://viewworld.dk/data/' . $parentID . '/' . $item['video'];
	}

	$params = array(
		'class'      => $class,
		'parentNode' => $mapNode,
		'remoteID'   => $remoteID,
		'attributes' => $attributes
	);
	$object = $pc->createObject( $params );

	if( $object ) {
		$point = new nxcWorldMapPoint(
			array(
				'world_map_id'            => $worldMap->attribute( 'id' ),
				'related_object_class_id' => $object->attribute( 'contentclass_id' ),
				'related_object_id'       => $object->attribute( 'id' ),
				'title'                   => $object->attribute( 'name' ),
				'latitude'                => $item['latitude'],
				'longitude'               => $item['longitude']
			)
		);
		$point->store();
	}
}

$script->shutdown( 0 );
?>
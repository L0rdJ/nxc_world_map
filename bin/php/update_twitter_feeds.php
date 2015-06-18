#!/usr/bin/env php
<?php
require 'autoload.php';
ini_set( 'default_socket_timeout', 3600 );
ini_set( 'memory_limit', '1024M' );

$cli = eZCLI::instance();
$cli->setUseStyles( true );

$scriptSettings = array();
$scriptSettings['description'] = 'Updates Twetter Feeds for NXC World Map';
$scriptSettings['use-session'] = true;
$scriptSettings['use-modules'] = true;
$scriptSettings['use-extensions'] = true;
$scriptSettings['site-access'] = 'siteadmin';

$script = eZScript::instance( $scriptSettings );
$script->startup();
$script->initialize();

$ini           = eZINI::instance();
$userCreatorID = $ini->variable( 'UserSettings', 'UserCreatorID' );
$user          = eZUser::fetch( $userCreatorID );
if( ( $user instanceof eZUser ) === false ) {
    $cli->error( 'Cannot get user object by userID = "' . $userCreatorID . '". ( See site.ini [UserSettings].UserCreatorID )' );
    $script->shutdown( 1 );
}
eZUser::setCurrentlyLoggedInUser( $user, $userCreatorID );


$pc = new nxcPowerContent();

$geoBaseURL = 'http://maps.google.com/maps/geo';
$geoParams  = array(
	'output' => 'json',
	'oe'     => 'utf8',
	'sensor' => 'false'
);

$tweeterFeedHandler = new nxcTwitterFeed();
$fetchParams = array(
	'Depth'            => false,
	'ClassFilterType'  => 'include',
	'ClassFilterArray' => array( 'twitter_feed' ),
	'LoadDataMap'      => true,
	'AsObject'         => true,
	'Limitation'       => array()
);
$feeds = eZContentObjectTreeNode::subTreeByNodeID( $fetchParams, 1 );

$tweetClass = eZContentClass::fetchByIdentifier( 'twitter_message' );
foreach( $feeds as $feed ) {
	$i = 0;

	$dataMap = $feed->attribute( 'data_map' );
	$type       = $dataMap['type']->toString();
	$screenName = $dataMap['screen_name']->content();
	$map        = $dataMap['map']->content();

	$message = $cli->stylize( 'green', 'Parsing "' . $type . '" feed for "' . $screenName . '" user' );
	$cli->output( $message );
	if( $map instanceof eZContentObject === false ) {
		$cli->error( 'Skipped, because feed isn`t related to the map' );
		continue;
	}

	$mapDataMap = $map->attribute( 'data_map' );
	$worldMap = $mapDataMap['world_map']->attribute( 'content' );
	if( $worldMap instanceof nxcWorldMap === false ) {
		$cli->error( 'Skipped, because feed isn`t related to valid map' );
	}

	$response = $tweeterFeedHandler->getTimeline(
		$type,
		array(
			'screen_name' => $screenName,
			'count' => 50
		)
	);
	if( isset( $response->error ) ) {
		$cli->error( $response->error );
	}

	foreach( $response['result'] as $message ) {
		$location = isset( $message['place']['full_name'] ) ? $message['place']['full_name'] : $message['user']['location'];
		$geoRequestURL = $geoBaseURL . '?q=' . urlencode( $location );
		foreach( $geoParams as $param => $value ) {
			$geoRequestURL .= '&' . $param . '=' . urlencode( $value );
		}
		$result = json_decode( file_get_contents( $geoRequestURL ), true );

		if( isset( $result['Placemark'] ) && count( $result['Placemark'] ) > 0 ) {
			$coordinates = $result['Placemark'][0]['Point']['coordinates'];

			$remoteID = $message['id_str'];
			$data = array(
				'author' => $message['user']['name'],
				'icon' => $message['user']['profile_image_url'],
				'message' => $message['text'],
				'published' => $message['created_timestamp'],
				'latitude' => $coordinates[1],
				'longitude' => $coordinates[0]
			);

			$object = eZContentObject::fetchByRemoteID( $remoteID );
			if( $object instanceof eZContentObject === false ) {
				$object = $pc->createObject(
					array(
						'class'      => $tweetClass,
						'parentNode' => $feed,
						'attributes' => $data,
						'remoteID'   => $remoteID
					)
				);
				if( $object instanceof eZContentObject ) {
					$mapPoint = new nxcWorldMapPoint(
						array(
							'world_map_id' => $worldMap->attribute( 'id' ),
							'related_object_id' => $object->attribute( 'id' ),
							'related_object_class_id' => $tweetClass->attribute( 'id' ),
							'latitude' => round( $coordinates[1], 5 ) + rand( 1, 9999 ) / 100000,
							'longitude' => round( $coordinates[0], 5 ) + rand( 1, 9999 ) / 100000,
							'title' => $message['user']['name'] . ' [' . date( 'd M Y H:i', $message['created_timestamp'] ). ']',
							'extra_data_string' => serialize( array( 'description' => $message['text'] ) )
						)
					);
					$mapPoint->store();
					$i++;
				}
			}
		}
	}
	$message = $cli->stylize( 'green', 'Imported ' . $i . ' tweets' );
	$cli->output( $message );
}

$script->shutdown( 0 );
?>
<?php
/**
 * @package nxcWorldMap
 * @class   nxcWorldMap
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    08 Dec 2010
 **/

class nxcWorldMap extends eZPersistentObject {

	const MAP_TYPE_ROADMAP   = 1;
	const MAP_TYPE_SATELLITE = 2;
	const MAP_TYPE_HYBRID    = 3;
	const MAP_TYPE_TERRAIN   = 4;

	public function __construct( $row = array() ) {
		$this->eZPersistentObject( $row );
	}

	public static function definition() {
		return array(
			'fields'              => array(
				'id' => array(
					'name'       => 'id',
					'datatype'   => 'integer',
					'default'    => 0,
					'required'   => true
				),
				'object_attribute_id' => array(
					'name'       => 'objectAttributeID',
					'datatype'   => 'integer',
					'default'    => 0,
					'required'   => true
				),
				'name' => array(
					'name'       => 'name',
					'datatype'   => 'string',
					'default'    => null,
					'required'   => false,
					'http_input' => true
				),
				'latitude' => array(
					'name'       => 'latitude',
					'datatype'   => 'float',
					'default'    => 0,
					'required'   => true,
					'http_input' => true
				),
				'longitude' => array(
					'name'       => 'longitude',
					'datatype'   => 'float',
					'default'    => 0,
					'required'   => true,
					'http_input' => true
				),
				'zoom_level' => array(
					'name'       => 'zoomLevel',
					'datatype'   => 'float',
					'default'    => 0,
					'required'   => true,
					'http_input' => true
				),
				'map_type' => array(
					'name'       => 'mapType',
					'datatype'   => 'integer',
					'default'    => self::MAP_TYPE_ROADMAP,
					'required'   => true,
					'http_input' => true
				)
			),
			'function_attributes' => array(
				'points'        => 'getPoints',
				'map_type_name' => 'getTypeName'
			),
			'keys'                => array( 'id' ),
			'sort'                => array( 'id' => 'asc' ),
			'increment_key'       => 'id',
			'class_name'          => 'nxcWorldMap',
			'name'                => 'nxc_world_maps'
		);
	}

	public function getPoints() {
		return eZPersistentObject::fetchObjectList(
			nxcWorldMapPoint::definition(),
			null,
			array( 'world_map_id' => $this->attribute( 'id' ) ),
			true
		);
	}

	public function getTypeName() {
		switch( $this->attribute( 'map_type' ) ) {
			case self::MAP_TYPE_ROADMAP: {
				return 'ROADMAP';
			}
			case self::MAP_TYPE_SATELLITE: {
				return 'SATELLITE';
			}
			case self::MAP_TYPE_HYBRID: {
				return 'HYBRID';
			}
			case self::MAP_TYPE_TERRAIN: {
				return 'TERRAIN';
			}
			default: {
				return 'UNKNOWN';
			}
		}
	}

	public static function getHttpFields() {
		$fields = array();

		$definition = self::definition();
		foreach( $definition['fields'] as $field => $info ) {
			if( isset( $info['http_input'] ) && $info['http_input'] === true ) {
				$fields[] = $field;
			}
		}

		return $fields;
	}
}
?>
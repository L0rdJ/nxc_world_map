<?php
/**
 * @package nxcWorldMap
 * @class   nxcWorldMapPoint
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    08 Dec 2010
 **/

class nxcWorldMapPoint extends eZPersistentObject {

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
				'world_map_id' => array(
					'name'       => 'worldMapID',
					'datatype'   => 'integer',
					'default'    => 0,
					'required'   => true
				),
				'related_object_id' => array(
					'name'       => 'relatedObjectID',
					'datatype'   => 'integer',
					'default'    => 0,
					'required'   => true
				),
				'related_object_class_id' => array(
					'name'       => 'relatedObjectClassID',
					'datatype'   => 'integer',
					'default'    => 0,
					'required'   => true
				),
				'latitude' => array(
					'name'       => 'latitude',
					'datatype'   => 'float',
					'default'    => 0,
					'required'   => true
				),
				'title' => array(
					'name'       => 'title',
					'datatype'   => 'string',
					'default'    => null,
					'required'   => true
				),
				'longitude' => array(
					'name'       => 'longitude',
					'datatype'   => 'float',
					'default'    => 0,
					'required'   => true
				),
				'extra_data_string' => array(
					'name'       => 'extra_data_string',
					'datatype'   => 'string',
					'default'    => null,
					'required'   => false
				)
			),
			'function_attributes' => array(
				'related_object'  => 'getRelatedObject',
				'map_icon_image'  => 'getMapIconImage',
				'map_tip_content' => 'getMapTipContent',
				'extra_data'      => 'getExtraData'
			),
			'keys'                => array( 'id' ),
			'sort'                => array( 'id' => 'asc' ),
			'increment_key'       => 'id',
			'class_name'          => 'nxcWorldMapPoint',
			'name'                => 'nxc_world_map_points'
		);
	}

	public function getRelatedObject() {
		return eZContentObject::fetch( $this->attribute( 'related_object_id' ) );
	}

	public function getMapIconImage() {
		$iconName = 'custom_tag';
		$iconAttribute = 'icon';

		$class = eZContentClass::fetch( $this->attribute( 'related_object_class_id' ) );
		if( $class instanceof eZContentClass ) {
			switch( $class->attribute( 'identifier' ) ) {
				case 'image': {
					$iconAttribute = 'image';
					break;
				}
				default: {
					$iconName = $class->attribute( 'identifier' );
				}
			}
		}

		$object = $this->attribute( 'related_object' );
		if( $object instanceof eZContentObject ) {
			$dataMap = $object->attribute( 'data_map' );
			if( isset( $dataMap[ $iconAttribute ] ) && $dataMap[ $iconAttribute ]->hasContent() ) {
				$imageAliasHadnler = $dataMap[ $iconAttribute ]->content();
				$info = $imageAliasHadnler->attribute( 'world_map_icon' );
				if( isset( $info['url'] ) ) {
					return '/' . $info['url'];
				}
			}
		}

		return self::eZImage( 'worldmap/point_icons/' . $iconName . '.png' );
	}

	private static function eZImage( $image ) {
		$bases = eZTemplateDesignResource::allDesignBases();
		$triedFiles = array();
		$fileInfo = eZTemplateDesignResource::fileMatch( $bases, 'images', $image, $triedFiles );

		if ( !$fileInfo ) {
			$siteDesign = eZTemplateDesignResource::designSetting( 'site' );
			$imgPath = "design/$siteDesign/images/$image";
		} else {
			$imgPath = $fileInfo['path'];
		}

        return htmlspecialchars( eZSys::wwwDir() . '/' . $imgPath );
	}

	public function getMapTipContent() {
		$tpl = eZTemplate::factory();
		$tpl->setVariable( 'point', $this );

		$class = eZContentClass::fetch( $this->attribute( 'related_object_class_id' ) );
		$classIdentifier = ( $class instanceof eZContentClass ) ? $class->attribute( 'identifier' ) : 'custom_tag';
		$content = $tpl->fetch( 'design:worldmap/point/' . $classIdentifier . '.tpl' );
		if( (int) $this->attribute( 'related_object_class_id' ) === 0 ) {
			$content = str_replace( "\n\n", '<br />', $content );
			$content = str_replace( "\n", '', $content );
		}

		return $content;
	}

	protected function getExtraData() {
		return unserialize( $this->attribute( 'extra_data_string' ) );
	}

	public static function fetch( $id ) {
		return eZPersistentObject::fetchObject(
			self::definition(),
			null,
			array( 'id' => $id ),
			true
		);
	}
}
?>
<?php
/**
 * @package nxcWorldMap
 * @class   nxcWorldMapType
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    08 Dec 2010
 **/

class nxcWorldMapType extends eZDataType {

	const DATA_TYPE_STRING = 'nxcworldmap';

	public function __construct() {
		$this->eZDataType(
			self::DATA_TYPE_STRING,
			ezpI18n::tr( 'extension/nxc_world_map', 'NXC World Map' )
		);
	}

	public function initializeClassAttribute( $classAttribute ) {
	}

	public function validateClassAttributeHTTPInput( $http, $base, $attribute ) {
		return eZInputValidator::STATE_ACCEPTED;
	}

	public function fetchClassAttributeHTTPInput( $http, $base, $attribute ) {
		$content  = $attribute->content();
		$variable = 'ezcca_nxc_world_map_class_list_' . $attribute->attribute( 'id' );
		if( $http->hasPostVariable( $variable ) ) {
			$classList = $http->postVariable( $variable );
			$tmp = array();
			foreach( $classList as $classIdentifier ) {
				if ( trim( $classIdentifier ) != '' ) {
					$tmp[] = $classIdentifier;
				}
			}
			$content['selected_class_list'] = array_unique( $tmp );
		}
		$attribute->setAttribute( 'data_text5', serialize( $content ) );
		$attribute->store();
		return true;
	}

	public function classAttributeContent( $attribute ) {
		$text = $attribute->attribute( 'data_text5' );
		if( trim( $text ) == '' ) {
			return array();
		}
		return unserialize( $text );
	}

	/**
	 * @todo add version support
	 */
	public function initializeObjectAttribute( $attribute, $currentVersion, $originalAttribute ) {
		$isCopying = $attribute instanceof eZContentObjectAttribute
			&& $originalAttribute instanceof eZContentObjectAttribute
			&& $attribute->attribute( 'id' ) != $originalAttribute->attribute( 'id' );

		$publishedVersion   = (int) $originalAttribute->attribute( 'object' )->attribute( 'current_version' );
		$isPublishedVersion = (int) $currentVersion === $publishedVersion;

    	if( $currentVersion == null || ( $isCopying && $isPublishedVersion ) ) {
	    	$wordlMap = new nxcWorldMap();
			$wordlMap->store();
			$attribute->setAttribute( 'data_int', $wordlMap->attribute( 'id' ) );

			if( $isCopying ) {
				$originalMap = $originalAttribute->attribute( 'content' );
				if( $originalMap instanceof nxcWorldMap ) {
					$definition = nxcWorldMap::definition();
					$fields     = $definition['fields'];
					unset( $fields['id'] );
					unset( $fields['object_attribute_id'] );
					$attributes = array_keys( $fields );
					foreach( $attributes as $field ) {
						$wordlMap->setAttribute(
							$field,
							$originalMap->attribute( $field )
						);
					}
					$wordlMap->store();

					$originalPoints = $originalMap->attribute( 'points' );
					foreach( $originalPoints as $point ) {
						$copy = clone $point;
						$copy->setAttribute( 'id', null );
						$copy->setAttribute( 'world_map_id', $wordlMap->attribute( 'id' ) );
						$copy->store();
					}
				}
			}
		}
		if( $isCopying && $isPublishedVersion === false ) {
			$attribute->setAttribute( 'data_int', 0 );
		}
	}

	public function postInitializeObjectAttribute( $attribute, $currentVersion, $originalAttribute ) {
		$map = $attribute->attribute( 'content' );
		if( $map instanceof nxcWorldMap ) {
			$map->setAttribute(
				'object_attribute_id',
				$attribute->attribute( 'id' )
			);
			$map->store();
		}
	}

	/**
	 * @todo validate each attribute
	 */
	public function validateObjectAttributeHTTPInput( $http, $base, $attribute ) {
		if( $http->hasPostVariable( 'BrowseActionName' ) ) {
			return eZInputValidator::STATE_ACCEPTED;
		}

		$httpBase   = 'ezcoa_nxc_world_map_' . $attribute->attribute( 'id' ) . '_';
		$httpFields = nxcWorldMap::getHttpFields();
		foreach( $httpFields as $field ) {
			if( $http->hasPostVariable( $httpBase . $field ) === false ) {
				$attribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Input required.' ) );
	            return eZInputValidator::STATE_INVALID;
	       	}
		}

		return eZInputValidator::STATE_ACCEPTED;
	}

	public function fetchObjectAttributeHTTPInput( $http, $base, $attribute ) {
		$httpBase   = 'ezcoa_nxc_world_map_' . $attribute->attribute( 'id' ) . '_';

		if( $http->hasPostVariable( 'BrowseActionName' ) === false ) {
			$httpFields = nxcWorldMap::getHttpFields();
			$worldMap   = $attribute->content();
			foreach( $httpFields as $field ) {
				if( $http->hasPostVariable( $httpBase . $field ) ) {
					$worldMap->setAttribute( $field, $http->postVariable( $httpBase . $field ) );
				}
			}
			$worldMap->store();
		}
	}

	public function objectAttributeContent( $attribute ) {
		return eZPersistentObject::fetchObject(
			nxcWorldMap::definition(),
			null,
			array( 'id' => $attribute->attribute( 'data_int' ) ),
			true
		);
	}

	public function customObjectAttributeHTTPAction( $http, $action, $attribute, $parameters ) {
		$currentPointVar = 'ezcoa_nxc_world_map_' . $attribute->attribute( 'id' ) . '_current_point';
		if( $http->hasPostVariable( $currentPointVar ) ) {
			$currentPoint = nxcWorldMapPoint::fetch( $http->postVariable( $currentPointVar ) );
		}
		$newPointTypeVar = 'ezcoa_nxc_world_map_' . $attribute->attribute( 'id' ) . '_new_point_type';

		$actionInfo  = explode( '-', $action );
		$action      = $actionInfo[0];
		$actionParms = isset( $actionInfo[1] ) ? explode( '|', $actionInfo[1] ) : array();
		switch( $action ) {
			case 'new_point': {
				$classID = $http->postVariable( 'ezcoa_nxc_world_map_' . $attribute->attribute( 'id' ) . '_new_point_type' );
				$point   = new nxcWorldMapPoint(
					array(
						'world_map_id' => $attribute->attribute( 'data_int' ),
						'related_object_class_id' => $classID
					)
				);
				$point->store();
				break;
			}
			case 'update_point': {
				$httpBase = 'ezcoa_nxc_world_map_' . $attribute->attribute( 'id' ) . '_point_' . $currentPoint->attribute( 'id' ). '_';

				$fields = array( 'title', 'latitude', 'longitude' );
				foreach( $fields as $field ) {
					if( $http->hasPostVariable( $httpBase . $field ) ) {
						$currentPoint->setAttribute( $field, $http->postVariable( $httpBase . $field ) );
					}
				}

				if( $http->hasPostVariable( $httpBase . 'extra_data' ) ) {
					$extraData = array_merge(
						(array) $currentPoint->attribute( 'extra_data' ),
						(array) $http->postVariable( $httpBase . 'extra_data' )
					);
					$currentPoint->setAttribute( 'extra_data_string', serialize( $extraData ) );
				}

				$currentPoint->store();
				break;
			}
			case 'browse': {
				if( $http->hasPostVariable( $newPointTypeVar ) ) {
					$http->setSessionVariable( 'new_point_type', $http->postVariable( $newPointTypeVar ) );
				}

				$browseParameters = array(
					'action_name'          => 'AddRelatedObjectToPoint_' . $attribute->attribute( 'id' ),
					'type'                 => 'AddRelatedObjectToDataType',
					'browse_custom_action' => array(
						'name'  => 'CustomActionButton[' . $attribute->attribute( 'id' ) . '_set_point_related_object-' . $actionParms[0] . ']',
						'value' => $currentPoint->attribute( 'id' ),
					),
					'from_page'            => $parameters['current-redirection-uri'],
					'start_node'           => 1
				);
				$class = eZContentClass::fetch( $currentPoint->attribute( 'related_object_class_id' ) );
				if( $class instanceof eZContentClass && isset( $actionParms[0] ) && $actionParms[0] == 'related_object_id' ) {
					$browseParameters['class_array'] = array( $class->attribute( 'identifier' ) );
				}
				eZContentBrowse::browse( $browseParameters, $parameters['module'] );
				break;
			}
			case 'set_point_related_object': {
				if( $http->hasSessionVariable( 'new_point_type' ) ) {
					$http->setPostVariable( $newPointTypeVar, $http->sessionVariable( 'new_point_type' ) );
				}

				$browseCurrentPointVar = $attribute->attribute( 'id' ) . '_set_point_related_object-' . $actionParms[0];
				if( $http->hasPostVariable( 'CustomActionButton' ) ) {
					$customActionButtons = $http->postVariable( 'CustomActionButton' );
					if( isset( $customActionButtons[ $browseCurrentPointVar ] ) ) {
						$currentPoint = nxcWorldMapPoint::fetch( $customActionButtons[ $browseCurrentPointVar ] );
						if( $currentPoint instanceof nxcWorldMapPoint ) {
							$selectedObjectID = false;
							if( $http->hasPostVariable( 'SelectedObjectIDArray' ) ) {
								$selectedObjectIDs = $http->postVariable( 'SelectedObjectIDArray' );
								$selectedObjectID  = $selectedObjectIDs[0];
							}
							if( $selectedObjectID !== false ) {
								$currentContentObject = $attribute->attribute( 'object' );

								$object = eZContentObject::fetch( $selectedObjectID );
								if( $object instanceof eZContentObject ) {
									$relattionPointAttribute = isset( $actionParms[0] ) ? $actionParms[0] : 'related_object_id';

									$oldObject = null;
									if( $relattionPointAttribute == 'related_object_id' ) {
										$oldObject = $currentPoint->attribute( 'related_object' );
									} else {
										$extraData = $currentPoint->attribute( 'extra_data' );
										if( isset( $extraData[ $relattionPointAttribute ] ) ) {
											$object = eZContentObject::fetch( $extraData[ $relattionPointAttribute ] );
										}
									}

									if( $oldObject instanceof eZContentObject ) {
										$currentContentObject->removeContentObjectRelation(
											$oldObject->attribute( 'id' ),
											$attribute->attribute( 'version' ),
											$attribute->attribute( 'contentclassattribute_id' ),
											eZContentObject::RELATION_ATTRIBUTE
										);
									}

									$extraData = (array) $currentPoint->attribute( 'extra_data' );
									if( $relattionPointAttribute == 'related_object_id' ) {
										$currentPoint->setAttribute( 'related_object_id', $object->attribute( 'id' ) );
										$currentPoint->setAttribute( 'title', $object->attribute( 'name' ) );
									} else {
										$extraData[ $relattionPointAttribute ] = $object->attribute( 'id' );
									}
									$currentPoint->setAttribute( 'extra_data_string', serialize( $extraData ) );
									$currentPoint->store();

									$currentContentObject->addContentObjectRelation(
										$object->attribute( 'id' ),
										$attribute->attribute( 'version' ),
										$attribute->attribute( 'contentclassattribute_id' ),
										eZContentObject::RELATION_ATTRIBUTE
									);
								}
							}
						}
					}
				}
				break;
			}
			case 'remove_point': {
				if( $currentPoint instanceof nxcWorldMapPoint ) {
					$currentPoint->remove();
				}
				break;
			}
		}
	}

	public function storeObjectAttribute( $attribute ) {
		$currentContentObject = $attribute->attribute( 'object' );

		$map = $attribute->attribute( 'content' );
		if( $map instanceof nxcWorldMap === false ) {
			return false;
		}

		$points = $map->attribute( 'points' );
		foreach( $points as $point ) {
			$object = $point->attribute( 'related_object' );
			if( $object instanceof eZContentObject ) {
				$currentContentObject->addContentObjectRelation(
					$object->attribute( 'id' ),
					$attribute->attribute( 'version' ),
					$attribute->attribute( 'contentclassattribute_id' ),
					eZContentObject::RELATION_ATTRIBUTE
				);
			}
		}
	}

	public function deleteStoredObjectAttribute( $attribute, $version = null ) {
		if( $version === null ) {
			$db = eZDB::instance();
			$db->begin();

			$db->query(
				'DELETE FROM nxc_world_map_points WHERE world_map_id = "' . $db->escapeString( $attribute->attribute( 'data_int' ) ) . '"'
			);
			$db->query(
				'DELETE FROM nxc_world_maps WHERE id = "' . $db->escapeString( $attribute->attribute( 'data_int' ) ) . '"'
			);

			$db->commit();
		}
	}

	public function title( $attribute, $name = null ) {
		$map = $attribute->attribute( 'content' );
		if( $map instanceof nxcWorldMap ) {
			return
				$map->attribute( 'name' ) . ' (' .
				$map->attribute( 'latitude' ) . ', ' .
				$map->attribute( 'longitude' ) . ')';
		} else {
			return null;
		}
	}
}

eZDataType::register( nxcWorldMapType::DATA_TYPE_STRING, 'nxcWorldMapType' );
?>
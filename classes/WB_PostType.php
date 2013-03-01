<?php
if( !class_exists( "WB_PostType" ) ) {
	require_once( "JW_PostType.php" );
}
/**
 * Generic PostType
 * @extends JW_POST_TYPE
 */
class WB_PostType extends JW_Post_Type{

	function __construct( $name, $supports = array() ) {

		parent::__construct( $name, $supports );

	}

	/**
	* Prepares AlchemyMediaAccess library
	* @return AlchemyMediaAccess
	*/
	public function add_media() {
		return new WPAlchemy_MediaAccess();
	} 

	/**
	* Adds AlchemyMeta box
	* @param array $args Alchemy Arguments
	* @return AlchemyMetaBox
	*/
	public function add_field( $args = array() )
	{

		$args = (array)$args;

		$default_args = array(
			"id" => "",
			"title" => "",
			"types" => array( $this->post_type_name ),
			"template" => "",
			"priority" => "low",
			"context" => "normal",
			"mode" => WPALCHEMY_MODE_EXTRACT
		);



		$args = array_merge( $default_args, $args );
		
		
		$args[ "template" ] = POST_ADMIN_TEMPLATES . $args[ "template" ];

		return new WPAlchemy_MetaBox( $args );

	}

}
<?php 
if( !class_exists( "WB_PostType" ) ) {
	require_once( "WB_PostType.php" );
}

/** 
 * Publication Post Type
 * has_many => services
 *
 * @inherits WB_PostType
 *
 *  Fields
 *  - Title
 *  - excerpt
 */
class Publication extends WB_PostType{
	
	private $file;
	private $year;
	private $authors;
	private $journal;
	
	public  $media;
	
	function __construct() {
		
		parent::__construct( "publication", array( "supports" => array( "title", "excerpt" ) ) );

		$this->_register_taxonomies();
		$this->_register_fields();

		add_filter('query_vars', array( $this, 'publication_query_vars' ) );
		
	}

	/**
	* Filter for authors save event.
	*
	* Trims the author names. For some reason empty strings are appended.
	*/
	public function trim_author( $meta, $post_id ) {
		
		$authors = $meta[ "authors" ];
		$new_authors = array();

		foreach( $authors as $author ) {
			$the_name = $author[ "name" ];
			$author[ "name" ] = trim( $the_name );
			$new_authors[] = $author;
		}
		$meta[ "authors" ] = $new_authors;
		return $meta;
	}

	public function trim_string( $meta, $post_id ) {
		
		foreach( $meta as $key => $val ) {
			$meta[ $key ] = trim($val);
		}

		return $meta;
	}

	/**
	* Add custom query variables to the wp_query list.
	*/
	public function publication_query_vars( $qvars )
	{
	
	$qvars[] = 'condition';
	$qvars[] = 'specialty';
	$qvars[] = 'method';
	$qvars[] = 'list-type';

	return $qvars;
	}


	/**
	* Register publication taxonomies
	*/
	private function _register_taxonomies() {
		/**
		*  Taxonomies
		*  - Disease/Condition
		*  - Speciality
		*  - Study Type/Method
		*/
		$this->add_taxonomy( "Condition", "Condition" );
		$this->add_taxonomy( "Specialty", "Specialties" );
		$this->add_taxonomy( "Method", "Methods" );
	}

	private function _register_fields() {
		// Our media upload object.
		// Called in publication-file template using global
		$this->media = $this->add_media();

		/**--- Publication File */
		$this->file = $this->add_field( array
			( 
				"id" => "_file",
				"title" => "Publication File",
				"template" => "publication-file.php",
			)
		);

		/**--- Publication year */
		$this->year = $this->add_field( array 
			(
				"id" => "_year",
				"title" => "Year Published",
				"template" => "publication-year.php",
				"context" => "side"
			)
		);

		/**--- Publication Authors */
		$this->authors = $this->add_field( array
			(
				"id" => "_authors",
				"title" => "Authors",
				"template" => "publication-authors.php",
				"save_filter" => array( $this, "trim_author" )
			) 
		);

		/**--- Publication journal */
		$this->journal = $this->add_field( array 
			(
				"id" => "_journal",
				"title" => "Journal",
				"template" => "publication-journal.php",
				"save_filter" => array( $this, "trim_string" )
			)
		);
	}

}
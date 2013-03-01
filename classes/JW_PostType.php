<?php
session_start();

/**
 * JW Post Types
 * @author Jeffrey Way
 * @link http://jeffrey-way.com
 */
class JW_Post_Type
{

	/**
	* The name of the post type.
	* @var string
	*/
	public $post_type_name;

	/**
	* A list of user-specific options for the post type.
	* @var array
	*/
	public $post_type_args;


	/**
	* Sets default values, registers the passed post type, and
	* listens for when the post is saved.
	*
	* @param string $name The name of the desired post type.
	* @param array @post_type_args Override the options.
	*/
	function __construct($name, $post_type_args = array())
	{
		if (!isset($_SESSION["taxonomy_data"])) {
			$_SESSION['taxonomy_data'] = array();
		}

		
		$this->post_type_name = strtolower($name);
		$this->post_type_args = (array)$post_type_args;

		// First step, register that new post type
		$this->init(array(&$this, "register_post_type"));
	}

	/**
	* Helper method, that attaches a passed function to the 'init' WP action
	* @param function $cb Passed callback function.
	*/
	function init($cb)
	{
		add_action("init", $cb);
	}

	
	/**
	* Registers a new post type in the WP db.
	*/
	function register_post_type()
	{
		// Get the post type name
		$type = $this->post_type_name;

		// Upercase the name
		$n = ucwords($type);

		// Pluralize
		$plural = $n . 's';

		$args = array(
			
			"labels" => array( 
				'name' => _x( $n, $type ),
				'singular_name' => _x( $n, $type ),
				'menu_name' => _x( $plural, $type ),
				'all_items' => _x( "All $plural", $type ),
				'add_new' => _x( "Add New", $type ),
				'add_new_item' =>  _x( "Add new $n", $type ),
				'edit_item' => _x( "Edit $n", $type ),
				'new_item' => _x( "New $n", $type ),
				'view_item' => _x( "View $n", $type ),
				'items_archive' => _x( "$n Archive", $type ),
				'search_items' => _x( "Search $n", $type ),
				'not_found' => _x( "$plural Not Found", $type ),
				'not_found_in_trash' => _x( "No $plural found in Trash", $type ),
				'parent_item_colon' => _x( "Parent $n", $type )
			),
			
			"public" => true,
			"publicly_queryable" => true,
			"query_var" => true,
			"rewrite" => true,
			"capability_type" => "post",
			"hierarchical" => false,
			"menu_position" => null,
			"supports" => array("title", "editor", "thumbnail"),
			'has_archive' => true
		);

		// Take user provided options, and override the defaults.
		$args = array_merge($args, $this->post_type_args);

		register_post_type($this->post_type_name, $args);
	} // register_post_type


	/**
	* Registers a new taxonomy, associated with the instantiated post type.
	*
	* @param string $taxonomy_name The name of the desired taxonomy
	* @param string $plural The plural form of the taxonomy name. (Optional)
	* @param array $options A list of overrides
	*/
	function add_taxonomy($taxonomy_name, $plural = '', $options = array())
	{
		// Create local reference so we can pass it to the init cb.
		$post_type_name = $this->post_type_name;

		// If no plural form of the taxonomy was provided, do a crappy fix. :)

		if (empty($plural)) {
			$plural = $taxonomy_name . 's';
		}

		// Taxonomies need to be lowercase, but displaying them will look better this way...
		$taxonomy_name = ucwords($taxonomy_name);

		// At WordPress' init, register the taxonomy
		$this->init(
			function() use($taxonomy_name, $plural, $post_type_name, $options)
			{
				// Override defaults with user provided options

				$options = array_merge(
					array(
						"hierarchical" => false,
						"label" => $taxonomy_name,
						"singular_label" => $plural,
						"show_ui" => true,
						"query_var" => true,
						"rewrite" => array("slug" => strtolower($taxonomy_name))
					),
					$options
				);

				// name of taxonomy, associated post type, options
				register_taxonomy(strtolower($taxonomy_name), $post_type_name, $options);
			});
	} // add_taxonomy

} // end class JW_Post_Type

/*********/
/* USAGE */
/*********/

// $product = new PostType("movie");
// $product->add_taxonomy('Actor');
// $product->add_taxonomy('Director');

// ));
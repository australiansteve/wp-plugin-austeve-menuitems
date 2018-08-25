<?php
/**
 * Plugin Name: Menu Items
 * Plugin URI: https://github.com/australiansteve/wp-plugin-austeve-menuitems
 * Description: Add, edit & display Menu Items
 * Version: 1.0.0
 * Author: AustralianSteve
 * Author URI: http://AustralianSteve.com
 * License: GPL2
 */

class AUSteve_MenuItems_CPT {

	function __construct() {

		add_action( 'init', array($this, 'register_post_type') );

		add_action( 'init', array($this, 'register_course_taxonomy') );

		add_action( 'template_redirect', array($this, 'redirect_singular_posts') );

		add_filter('manage_austeve-menuitems_posts_columns', array($this, 'alter_admin_columns_head') );

		add_action('manage_austeve-menuitems_posts_custom_column', array($this, 'alter_admin_columns_content'), 10, 2 );

		add_action('pre_get_posts', array($this, 'always_get_all_menuitems') );
	}

	function register_post_type() {

		// Set UI labels for Custom Post Type
		$labels = array(
			'name'                => _x( 'Menu Items', 'Post Type General Name', 'austeve-menuitems' ),
			'singular_name'       => _x( 'Menu Item', 'Post Type Singular Name', 'austeve-menuitems' ),
			'menu_name'           => __( 'Menu Items', 'austeve-menuitems' ),
			'all_items'           => __( 'All Menu Items', 'austeve-menuitems' ),
			'view_item'           => __( 'View Menu Item', 'austeve-menuitems' ),
			'add_new_item'        => __( 'Add New Menu Item', 'austeve-menuitems' ),
			'add_new'             => __( 'Add New', 'austeve-menuitems' ),
			'edit_item'           => __( 'Edit Menu Item', 'austeve-menuitems' ),
			'update_item'         => __( 'Update Menu Item', 'austeve-menuitems' ),
			'search_items'        => __( 'Search Menu Items', 'austeve-menuitems' ),
			'not_found'           => __( 'Not Found', 'austeve-menuitems' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'austeve-menuitems' ),
		);
		
		// Set other options for Custom Post Type		
		$args = array(
			'label'               => __( 'Menu Items', 'austeve-menuitems' ),
			'description'         => __( 'Menu Items', 'austeve-menuitems' ),
			'labels'              => $labels,
			// Features this CPT supports in Post Editor
			'supports'            => array( 'title', 'author', 'revisions', 'thumbnail'),
			// You can associate this CPT with a taxonomy or custom taxonomy. 
			'taxonomies'          => array( '' ),
			/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/	
			'hierarchical'        => false,
			'rewrite'           => array( 'slug' => 'menuitems' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'menu_icon'           => 'dashicons-carrot',
		);
		
		// Registering your Custom Post Type
		register_post_type( 'austeve-menuitems', $args );
	}

	function register_course_taxonomy() {

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Courses', 'taxonomy general name', 'austeve-menuitems' ),
			'singular_name'     => _x( 'Course', 'taxonomy singular name', 'austeve-menuitems' ),
			'search_items'      => __( 'Search Courses', 'austeve-menuitems' ),
			'all_items'         => __( 'All Courses', 'austeve-menuitems' ),
			'parent_item'       => __( 'Parent Course', 'austeve-menuitems' ),
			'parent_item_colon' => __( 'Parent Course:', 'austeve-menuitems' ),
			'edit_item'         => __( 'Edit Course', 'austeve-menuitems' ),
			'update_item'       => __( 'Update Course', 'austeve-menuitems' ),
			'add_new_item'      => __( 'Add New Course', 'austeve-menuitems' ),
			'new_item_name'     => __( 'New Course Name', 'austeve-menuitems' ),
			'menu_name'         => __( 'Courses', 'austeve-menuitems' ),
			'not_found'         => __( 'No courses found.', 'austeve-menuitems' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'menuitem-course' ),
		);

		register_taxonomy( 'menuitem-course', array( 'austeve-menuitems' ), $args );

	}

    function redirect_singular_posts() {
      if ( is_singular('austeve-menuitems') ) {
      	$terms = wp_get_post_terms( get_the_ID(), 'menuitem-course' );

      	if (isset($terms[0]))
      	{
        	wp_redirect( home_url('menuitem-course/'.$terms[0]->slug), 302 );
      	}
      	else
      	{
        	wp_redirect( home_url('menuitems'), 302 );
      	}

        exit;
      }
      else if ( is_post_type_archive('austeve-menuitems') ) 
      {
		wp_redirect( home_url('menuitem-course/main-course'), 302 );
      }
    }

	function always_get_all_menuitems($query) {
		//if querying a menu item course, get all menu items
		if (!is_admin() && $query->get('menuitem-course') != null)
		{
			$query->set('posts_per_page', -1);
		}
		//if querying menu items, order by the visual order defined by SCPOrder plugin
		if (!is_admin() && is_array($query->get('post_type')) && in_array('austeve-menuitems', $query->get('post_type')))
		{
			$query->set('orderby', 'menu_order');
			$query->set('order', 'ASC');
		}
		return $query;
	}

	function alter_admin_columns_head($defaults) {
		$res = array_slice($defaults, 0, 2, true) +
		    array("menuitem-course" => "Course") +
		    array_slice($defaults, 2, count($defaults) - 1, true) ;
		$defaults = $res;
		//Remove the old date column
		unset($defaults['categories']);
	    return $defaults;
	}
	 
	function alter_admin_columns_content($column_name, $post_ID) {
	    if ($column_name == 'menuitem-course') {
			$term_args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'names');
	    	$term_list = wp_get_post_terms($post_ID, 'menuitem-course', $term_args);
	    	if (count($term_list) > 1)
				echo implode(", ", $term_list);
			else if (count($term_list) > 0)
				echo $term_list[0];
	    }
	}

}

$austeveMenuItems = new AUSteve_MenuItems_CPT();


<?php
/**
 * Plugin Name: Portfolio
 * Plugin URI: https://github.com/australiansteve/wp-plugin-austeve-menuitems
 * Description: Add, edit & display Artists
 * Version: 1.0.0
 * Author: AustralianSteve
 * Author URI: http://AustralianSteve.com
 * License: GPL2
 */

class AUSteve_Portfolio_CPT {

	function __construct() {

		add_action( 'init', array($this, 'register_post_type') );

		add_action( 'pre_get_posts', array($this, 'before_getting_portfolio') );
	}

	function register_post_type() {

		// Set UI labels for Custom Post Type
		$labels = array(
			'name'                => _x( 'Artists', 'Post Type General Name', 'austeve-portfolio' ),
			'singular_name'       => _x( 'Artist', 'Post Type Singular Name', 'austeve-portfolio' ),
			'menu_name'           => __( 'Portfolio', 'austeve-portfolio' ),
			'all_items'           => __( 'All Artists', 'austeve-portfolio' ),
			'view_item'           => __( 'View Artist', 'austeve-portfolio' ),
			'add_new_item'        => __( 'Add New Artist', 'austeve-portfolio' ),
			'add_new'             => __( 'Add New', 'austeve-portfolio' ),
			'edit_item'           => __( 'Edit Artist', 'austeve-portfolio' ),
			'update_item'         => __( 'Update Artist', 'austeve-portfolio' ),
			'search_items'        => __( 'Search Portfolio', 'austeve-portfolio' ),
			'not_found'           => __( 'Not Found', 'austeve-portfolio' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'austeve-portfolio' ),
		);
		
		// Set other options for Custom Post Type		
		$args = array(
			'label'               => __( 'Portfolio', 'austeve-portfolio' ),
			'description'         => __( 'Collection of Artists', 'austeve-portfolio' ),
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
			'rewrite'           => array( 'slug' => 'portfolio' ),
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
			'menu_icon'           => 'dashicons-admin-customizer',
		);
		
		// Registering your Custom Post Type
		register_post_type( 'austeve-portfolio', $args );
	}

	function before_getting_portfolio($query) {
		
		//if querying menu items, order by the visual order defined by SCPOrder plugin
		if (!is_admin() && is_array($query->get('post_type')) && in_array('austeve-portfolio', $query->get('post_type')))
		{
			$query->set('posts_per_page', -1);
			$query->set('orderby', 'menu_order');
			$query->set('order', 'ASC');
		}
		return $query;
	}

}

$austevePortfolio = new AUSteve_Portfolio_CPT();


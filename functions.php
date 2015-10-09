<?php
/**
* This file contains functions to be used in Custom Posts Widget plugin. This file is being imported in custom_posts.php file.
*
* @since 2015-10-09
* 
* @version 2015-10-09 Dhananjay Singh - PMCVIP-243
*
*/

	namespace dj\custom_taxonomy;
	
	/**
	* Function to register custome taxonomy
	*
	* @since 2015-10-09
	*/
	function register_news_type_taxonomy() {
	
		$labels		=	array(
								'name'	=> 'News Types',
								'search_items'	=> 'Search News Type',
								'edit_item'	=>	'Edit News Type',
								'add_new_item'	=>	'Add New News Type'
							);
							
		$args		=	array(
								'labels'	=>	$labels,
								'hierarchical' => true,
								'query_var'    => true,
								'rewrite'      => true,
								'public'		=> true
							);
		
		//WP inbuilt function to register custom taxonomy					
		register_taxonomy( 'type', 'news',  $args );
	}
	
	/**
	* Function to register custom taxonomy widget
	*
	* @since 2015-10-09
	*/
	function register_widgets() {
		//WP inbuilt function to register widget
		register_widget( '\dj\custom_taxonomy\taxonomy_widget' ); 
	}
	
	/**
	* Function to include css file
	*
	* @since 2015-10-09
	*/
	function custom_taxonomy_css() {
		wp_register_style( 'customTaxonomyStyle', plugins_url( 'custom_taxonomy/css/custom_taxonomy.css' ) );
		wp_enqueue_style( 'customTaxonomyStyle' );
	}
	
	
?>
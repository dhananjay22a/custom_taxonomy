<?php
/* 
Plugin Name: Custom Taxonomy & Widget
Plugin URI: 
Description: Plugin to create custom taxonomy and widget to show 5 most recent posts, maximum of 30 days old, for posts with any associated term in that taxonomy
Version: 1.0
Author: Dhananjay Singh
Author URI: 
Text Domain: pmc-plugin
License: GPLv2 
*/


/*  Copyright 2015  Dhananjay Singh  (email : dsingh@pmc.com) 
     This program is free software; you can redistribute it and/or modify
     it under the terms of the GNU General Public License as published by
     the Free Software Foundation; either version 2 of the License, or 
     (at your option) any later version. 
     This program is distributed in the hope that it will be useful, 
     but WITHOUT ANY WARRANTY; without even the implied warranty of 
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
     GNU General Public License for more details.
     You should have received a copy of the GNU General Public License
     along with this program; if not, write to the Free Software
     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

	namespace dj\custom_taxonomy;
	
	/*
		Creating custom Taxonomy
		Created by : DJ
		Date : 25 Sept. 2015
	*/
	
	//Adding action hook to register custome taxonomy
	add_action( 'init', '\dj\custom_taxonomy\register_news_type_taxonomy' );
	
	//Function to register custome taxonomy
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
	
	
	
	/*
		Creating an widget for custom taxonomy
		Created by : DJ
		Date : 25 Sept 2015
	*/
	
	//Adding action hook to register custom taxonomy widget
	add_action( 'widgets_init', '\dj\custom_taxonomy\register_widgets' ); 
	
	//Function to register custom taxonomy widget
	function register_widgets() {
		//WP inbuilt function to register widget
		register_widget( '\dj\custom_taxonomy\taxonomy_widget' ); 
	}
	
	
	//Adding action hook to add css in wp header for this widget.
	add_action( 'wp_head', '\dj\custom_taxonomy\widget_css' );
	function widget_css() { 
		?> 
     		<style type="text/css"> 
				#pmc_customtaxonomy_type_main_div { border:1px solid #2e2e2e; padding:0; }
				#pmc_customtaxonomy_type_main_div .widget_title_div { font-size:20px; background: #cccccc; color:#0088CC; text-align:center; }
				#pmc_customtaxonomy_type_main_div .widget_title_div { font-size:20px; background: #cccccc; color:#0088CC; text-align:center; }
				#pmc_customtaxonomy_type_main_div .news_item_main_div {  font-size:12px; padding:5px; height:62px; }
				#pmc_customtaxonomy_type_main_div .news_item_thumbnail{ width:52px; height:52px; padding:1px; float:left; }
				#pmc_customtaxonomy_type_main_div .news_item_title { width:296px; height:37px; padding:0 5px; float:left; }
				#pmc_customtaxonomy_type_main_div .author_name { float:left; height:15px; font-size:10px; color:#7e7e7e; padding: 0 5px; }
     		</style>
 		<?php 
	}
	
	
	
	//Function to filter upto 30 days old posts
	function filter_where($where = '') {
		//posts in the last 30 days
		$where .= " AND post_date > '" . date('Y-m-d', strtotime('-30 days')) . "'";
		return $where;
	}
	
	
	
	/*
		Class for widget
	*/
	class taxonomy_widget extends \WP_Widget {
		
		function __construct() {
     		
			$widget_ops = array(
					         'classname'   => 'taxonomy_widget_class',
					         'description' => 'Widget to display custom taxonomy type data .' 
						);
		
			parent::__construct( 'taxonomy_widget', 'Custom Taxonomy Widget', $widget_ops ); 
		
		}
		
		
		
		//Function to display up to 5 most recent posts, maximum of 30 days old, for posts of that custom taxonomy type
		function widget( $args, $instance ) {
			
			extract( $args );
			echo $before_widget;
			$currentID = get_the_ID(); //Getting current post id
			
			$args =  array( 
						'posts_per_page' => '5', 
						'post__not_in'	=> array($currentID),
						'tax_query' => array(
     										array(
												'taxonomy' => 'type', 
         										'field'    => 'slug', 
										        'terms'    => 'emmys-news'
											)
						),     
						'orderby' => 'date',
						'order'   => 'DESC'
			); 
			
			//Adding filter hook filter_where.
			add_filter('posts_where', '\dj\custom_taxonomy\filter_where');
			
			//Checking cache if data is available. And if so assign it to $news and avoid if part.
			$news = get_transient('custom_taxonomy_posts_key_'.$currentID);
			if ($news === false) {
				$news = new \WP_Query( $args );
				//Adding data to cache
				set_transient('custom_taxonomy_posts_key_'.$currentID, $news, 3600 * 24);
			}
			
			//Removing filter hook filter_where
			remove_filter('posts_where', '\dj\custom_taxonomy\filter_where');
			
			//calling get_terms to get Term by passing slug 'emmys-news'
			$terms = get_terms('type', array('slug'=>'emmys-news'));
			
			//Main widget ourter div
			echo '<div id="pmc_customtaxonomy_type_main_div">';
				//Widget title div
				echo '<div class="widget_title_div">';
					echo $terms[0]->name;
				echo '</div>';
				//Widget title div ends here
			
				// The Loop 
				$counter	=	0;
				while ( $news->have_posts() ) : $news->the_post();

					if($counter%2 == 0 ) {
						$textColor	=	'#2e2e2e';
						$bgColor	=	'#cceeff';
					} else {
						$textColor	=	'#2e2e2e';
						$bgColor	=	'#FFFFFF';
					}
					
					//News Item main div
					echo '<div style="color:'.$textColor.'; background:'.$bgColor.';" class="news_item_main_div">';
						//New Item thumbnail div
						echo '<div style="color:'.$textColor.'; background:'.$bgColor.';" class="news_item_thumbnail" >';
							?><a href="<?php the_permalink()?>" title="<?php the_title()?> "> <?php
							if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
								the_post_thumbnail( array(50, 50) );
							} else {
								
							}
							echo '</a>';
						echo '</div>';
						//News Item thumbnail div ends here
						
						//News Item title div
						echo '<div style="color:'.$textColor.'; background:'.$bgColor.';" class="news_item_title" >';
							?>
								<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php echo $this->short_title(100); ?></a><br />
							<?php 
						echo '</div>';
						//News item title div ends here
						
						//Authorname div
						echo '<div class="author_name">';
							echo "Author : ";
							the_author();
						echo '</div>';
						//Authername div ends here
						
					$counter++;
					echo '</div>';
					//News item main div ends here
			endwhile;
			
			//Widget main div ends here
			echo "</div>";
			echo $after_widget;
			// Reset Post Data 
			wp_reset_postdata();
		}
		
		
		//Function to short title if larger than $length
		function short_title( $length ) {
			
			$newsTitle	=	get_the_title();
			if(strlen($newsTitle) > $length )
				return $shortNewsTitle	=	substr($newsTitle, 0, $length ). '...';
			else return $newsTitle;
		}
		
	}
?> 
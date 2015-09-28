This widget will show up to 5 most recent posts, maximum of 30 days old, for posts with any associated term in that taxonomy.

------------------------------------------------
			   Description
------------------------------------------------

This widget checks database posts which are associated with a taxonomy and fetches 5 recent records which are not older than 30 days.

You can change it to work for any custom taxinomy. Currently it is set to "emmys-news". e.g. if you want it to work for "oscars-news" taxonomy then change the value of terms to oscars-news in tax_query array.

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



------------------------------------------------
                    INSTALLATION
------------------------------------------------
Just upload the custom_taxonomy folder in your plugin folder and activate from admin section.


------------------------------------------------
			     How to use
------------------------------------------------
After installation, in admin section, go to Appearance -> Widgets. You can find Custom Taxonomy Widget under Available Widgets section. Just click on it and and then click on Add Widget button. And that's all. This widget now will be displayed on side bar of pages.
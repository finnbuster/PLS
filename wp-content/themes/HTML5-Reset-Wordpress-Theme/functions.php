<?php
        // Translations can be filed in the /languages/ directory
        load_theme_textdomain( 'html5reset', TEMPLATEPATH . '/languages' );
 
        $locale = get_locale();
        $locale_file = TEMPLATEPATH . "/languages/$locale.php";
        if ( is_readable($locale_file) )
            require_once($locale_file);
	
	// Add RSS links to <head> section
	automatic_feed_links();
	
	// Load jQuery
	if ( !function_exists(core_mods) ) {
		function core_mods() {
			if ( !is_admin() ) {
				wp_deregister_script('jquery');
				wp_register_script('jquery', ("//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"), false);
				wp_enqueue_script('jquery');
			}
		}
		core_mods();
	}

	// Clean up the <head>
	function removeHeadLinks() {
    	remove_action('wp_head', 'rsd_link');
    	remove_action('wp_head', 'wlwmanifest_link');
    }
    add_action('init', 'removeHeadLinks');
    remove_action('wp_head', 'wp_generator');
    
    if (function_exists('register_sidebar')) {
    	register_sidebar(array(
    		'name' => __('Sidebar Widgets','html5reset' ),
    		'id'   => 'sidebar-widgets',
    		'description'   => __( 'These are widgets for the sidebar.','html5reset' ),
    		'before_widget' => '<div id="%1$s" class="widget %2$s">',
    		'after_widget'  => '</div>',
    		'before_title'  => '<h2>',
    		'after_title'   => '</h2>'
    	));
    }
    
    add_theme_support( 'post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'status', 'audio', 'chat', 'video')); // Add 3.1 post format theme support.
    
    // This theme styles the visual editor with editor-style.css to match the theme style.
    add_editor_style();
    
    // This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
    add_theme_support( 'post-thumbnails' );
    
    register_nav_menu( 'primary', 'Primary Menu' );
    
    register_nav_menu( 'top_menu', 'Top Menu' );
    
    //---------------------------------------------
    # auto generate slideshow 
    //---------------------------------------------
    function do_slideshow($id, $size){
    $args = array("post_type" => "attachment", "post_mime_type" => "image", "post_parent" => $id, "order_by" => "menu_order", "order" => "DESC", "numberposts" => -1);
    //,
    $images = get_posts($args);
    //echo "<div style='display:none'>".count($images)."</div>";
    
    $args = array("post_type" => "attachment", "post_mime_type" => "image", "post_parent" => $id, "order_by" => "menu_order", "order" => "DESC", "numberposts" => -1, "offset" => 0);
    //,
    $images = get_posts($args);
    
    //print_r($images);
    //echo "<br><br>";
    foreach ($images as $key => $row) {
    	//print_r($row);
        //echo "<br><br>";
        $menu[$key]  = $row->menu_order; 
        //echo $menu[$key]." ";
    }
    
    
    if(count($images) >= 2):
    array_multisort($menu, SORT_ASC, $images);
    //print_r($images);
    ?>
    
    <div id="<?php $post->ID ?>-carousel" class="carousel slide <?php echo $size ?>">
    
    	<div class="carousel-inner">
    	
    	<?php 
    		//print_r($images);
    		if ($images){
    			
    			foreach($images as $image){ ?>
    			
    			<div class="item">
    			
        			<?php
        			
        			$i++;
        			
        			$attachment_id = $image->ID; // attachment ID
        			
        			$image_attributes = wp_get_attachment_image_src( $attachment_id, $size ); // returns an array ?>
        				
        			<?php
        			
    				echo "<img src='".$image_attributes[0]."' height='".$image_attributes[2]."' width='".$image_attributes[1]."'>"; ?>
    				
    				<div class="carousel-caption"><h3><?php echo $image->post_title?></h3><p><?php echo $image->post_content; ?></p></div>
    			
    			</div>
    			
    			<?php
    			
    			}
    		}
    		
    		?>
    		
    	</div>
    	
    	<!-- Carousel nav -->
    	<a class="carousel-control left" href="#<?php $post->ID ?>-carousel" data-slide="prev">&lsaquo;</a>
    	<a class="carousel-control right" href="#<?php $post->ID ?>-carousel" data-slide="next">&rsaquo;</a>
    	
    </div>
    
    <?php 
    else: 
    endif;
    }

?>
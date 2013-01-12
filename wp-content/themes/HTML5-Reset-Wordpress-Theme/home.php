<?php get_header(); ?>

	<?php $queryargs = "post_type=page&posts_per_page=1&order=ASC&page_id=26"; ?>
	
	<?php $Query = new WP_Query($queryargs); ?>
	
	<?php if ($Query->have_posts()) : while ($Query->have_posts()) : $Query->the_post(); ?>
			
		<article class="post" id="post-<?php the_ID(); ?>">

			<div class="entry">
				
				<?php
				 
				/*
				*  View array data (for debugging)
				*/
				 
				//var_dump( get_field('relationship') );
				
				/*
				*  Loop through post objects ( don't setup postdata )
				*  Using this method, the $post object is never changed so all functions need a seccond parameter of the post ID in question.
				*/
				 
				$related_posts = get_field('front-slider');
				 
				if( $related_posts ): ?>
					<?php foreach( $related_posts as $related_post_object): ?>
				    	
				    	<?php do_slideshow($related_post_object->ID, 'large'); ?>
				    	
					<?php endforeach; ?>
				<?php endif; ?>
				
				
				<h2><?php the_field('home_title_sentance'); ?></h2>
				
				<?php the_content(); ?>
				
				<a href="<?php the_field('join_link'); ?>"><?php the_field('join_link_text'); ?></a>
				
				<a href="<?php the_field('feature_link_1'); ?>">
					<h2><?php the_field('feature_link_1_title'); ?></h2>
					<p><?php the_field('feature_link_1_description'); ?></p>
				</a>
				
				<a href="<?php the_field('feature_link_2'); ?>">
					<h2><?php the_field('feature_link_2_title'); ?></h2>
					<p><?php the_field('feature_link_2_description'); ?></p>
				</a>
				
				<a href="<?php the_field('feature_link_3'); ?>">
					<h2><?php the_field('feature_link_3_title'); ?></h2>
					<p><?php the_field('feature_link_3_description'); ?></p>
				</a>

			</div>

		</article>

		<?php endwhile; endif; ?>
		
		<div class="home_footer">
		
			<h2>Testimonials</h2>
		
			<?php $queryargs = "post_type=testimonial&posts_per_page=1&order=ASC&orderby=rand"; ?>
			
			<?php $Query = new WP_Query($queryargs); ?>
			
			<?php if ($Query->have_posts()) : while ($Query->have_posts()) : $Query->the_post(); ?>
			
				<p><?php the_field('testimonial'); ?></p>
				
				<p>- <?php the_field('student_name'); ?></p>
			
			<?php endwhile; endif; ?>
			
			<h2>Upcoming Classes</h2>
			
			<h2>Connect with PLS</h2>
		
		</div>
		
	

<?php get_footer(); ?>



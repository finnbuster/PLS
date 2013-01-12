<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
		<article class="post" id="post-<?php the_ID(); ?>">

			<h2><?php the_title(); ?></h2>

			<div class="entry">

				<?php the_content(); ?>

			</div>

		</article>

		<?php endwhile; endif; ?>
		
	<?php $queryargs = "post_type=teacher&posts_per_page=-1&order=ASC"; ?>
	
	<?php $Query = new WP_Query($queryargs); ?>
	
	<?php if ($Query->have_posts()) : while ($Query->have_posts()) : $Query->the_post(); ?>
	
		<?php the_post_thumbnail('large', $img_attr); ?>
		
		<h2><?php the_title(); ?></h2>
		
		<p><?php the_content(); ?></p>
	
	<?php endwhile; endif; ?>

<?php get_footer(); ?>

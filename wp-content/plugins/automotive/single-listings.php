<?php get_header();

global $Listing_Template;

if (have_posts()) : while (have_posts()) : the_post();

	echo $Listing_Template->locate_template("listing_content");
    
endwhile; ?>

<?php else : ?>
<?php _e("Post not found", "listings"); ?>!
<?php endif; ?>

<?php get_footer(); ?>
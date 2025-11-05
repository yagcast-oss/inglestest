<?php
/**
 * Plantilla principal
 *
 * @package InglesCurso
 */

get_header();
?>
<main id="primary" class="site-main">
    <?php
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            get_template_part( 'template-parts/content', get_post_type() );
        }

        the_posts_navigation();
    } else {
        get_template_part( 'template-parts/content', 'none' );
    }
    ?>
</main>
<?php
get_footer();

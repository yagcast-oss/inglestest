<?php
/**
 * Plantilla individual de lección
 *
 * @package InglesCurso
 */

get_header();
?>
<main id="primary" class="site-main">
    <?php
    while ( have_posts() ) :
        the_post();
        get_template_part( 'template-parts/content', 'lesson' );

        the_post_navigation(
            array(
                'prev_text' => '<span class="nav-subtitle">' . __( 'Lección anterior', 'ingles-curso' ) . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . __( 'Siguiente lección', 'ingles-curso' ) . '</span> <span class="nav-title">%title</span>',
            )
        );
    endwhile;
    ?>
</main>
<?php
get_footer();

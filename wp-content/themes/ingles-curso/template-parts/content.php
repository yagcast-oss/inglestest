<?php
/**
 * Contenido por defecto para entradas y páginas
 *
 * @package InglesCurso
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'lesson-card' ); ?>>
    <header class="entry-header">
        <?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
    </header>

    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . __( 'Páginas:', 'ingles-curso' ),
                'after'  => '</div>',
            )
        );
        ?>
    </div>
</article>

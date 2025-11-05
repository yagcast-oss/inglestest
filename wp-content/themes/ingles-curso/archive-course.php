<?php
/**
 * Archivo de cursos
 *
 * @package InglesCurso
 */

get_header();
?>
<main id="primary" class="site-main">
    <header class="page-header">
        <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
        <p><?php esc_html_e( 'Explora los cursos disponibles y selecciona el nivel que mejor se adapte a tus objetivos.', 'ingles-curso' ); ?></p>
    </header>

    <div class="grid grid-2">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article <?php post_class( 'lesson-card' ); ?>>
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="lesson-thumbnail">
                            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
                        </div>
                    <?php endif; ?>
                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="lesson-meta">
                        <?php if ( $duration = get_post_meta( get_the_ID(), 'duracion_curso', true ) ) : ?>
                            <span>⏱️ <?php echo esc_html( $duration ); ?></span>
                        <?php endif; ?>
                        <?php
                        $terms = get_the_terms( get_the_ID(), 'course_level' );
                        if ( $terms && ! is_wp_error( $terms ) ) {
                            echo '<span>🧭 ' . esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) ) . '</span>';
                        }
                        ?>
                    </div>
                    <p><?php the_excerpt(); ?></p>
                    <a class="button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Ver detalles del curso', 'ingles-curso' ); ?></a>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <?php get_template_part( 'template-parts/content', 'none' ); ?>
        <?php endif; ?>
    </div>

    <?php the_posts_pagination(); ?>
</main>
<?php
get_footer();

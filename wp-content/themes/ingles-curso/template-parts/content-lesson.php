<?php
/**
 * Contenido individual para lecciones
 *
 * @package InglesCurso
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'lesson-card' ); ?>>
    <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        <div class="lesson-meta">
            <?php if ( $duration = get_post_meta( get_the_ID(), 'duracion_leccion', true ) ) : ?>
                <span>⏱️ <?php echo esc_html( $duration ); ?></span>
            <?php endif; ?>
            <?php if ( $objective = get_post_meta( get_the_ID(), 'objetivo_principal', true ) ) : ?>
                <span>🎯 <?php echo esc_html( $objective ); ?></span>
            <?php endif; ?>
        </div>
    </header>

    <div class="entry-content">
        <?php the_content(); ?>
    </div>

    <footer class="entry-footer">
        <?php
        $course_id = get_post_meta( get_the_ID(), 'curso_relacionado', true );
        if ( $course_id ) :
            ?>
            <a class="button" href="<?php echo esc_url( get_permalink( $course_id ) ); ?>"><?php esc_html_e( 'Volver al curso', 'ingles-curso' ); ?></a>
        <?php endif; ?>
        <?php edit_post_link( __( 'Editar lección', 'ingles-curso' ), '<span class="edit-link">', '</span>' ); ?>
    </footer>
</article>

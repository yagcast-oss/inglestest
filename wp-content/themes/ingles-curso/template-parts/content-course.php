<?php
/**
 * Contenido individual para cursos
 *
 * @package InglesCurso
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header course-hero">
        <div>
            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            <div class="entry-meta course-meta">
                <?php if ( $duration = get_post_meta( get_the_ID(), 'duracion_curso', true ) ) : ?>
                    <div class="meta-item">
                        <span class="meta-label"><?php esc_html_e( 'Duración', 'ingles-curso' ); ?></span>
                        <span class="meta-value"><?php echo esc_html( $duration ); ?></span>
                    </div>
                <?php endif; ?>

                <?php
                $terms = get_the_terms( get_the_ID(), 'course_level' );
                if ( $terms && ! is_wp_error( $terms ) ) :
                    $level_names = wp_list_pluck( $terms, 'name' );
                    ?>
                    <div class="meta-item">
                        <span class="meta-label"><?php esc_html_e( 'Nivel sugerido', 'ingles-curso' ); ?></span>
                        <span class="meta-value"><?php echo esc_html( implode( ', ', $level_names ) ); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ( $method = get_post_meta( get_the_ID(), 'metodologia_curso', true ) ) : ?>
                    <div class="meta-item">
                        <span class="meta-label"><?php esc_html_e( 'Metodología', 'ingles-curso' ); ?></span>
                        <span class="meta-value"><?php echo esc_html( $method ); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="course-thumbnail">
                <?php the_post_thumbnail( 'large' ); ?>
            </div>
        <?php endif; ?>
    </header>

    <div class="entry-content">
        <?php the_content(); ?>
    </div>

    <?php
    $lessons = ingles_curso_get_lessons_for_course( get_the_ID() );
    if ( $lessons ) :
        ?>
        <section class="lesson-summary">
            <h2><?php esc_html_e( 'Lecciones del curso', 'ingles-curso' ); ?></h2>
            <div class="grid grid-2">
                <?php foreach ( $lessons as $lesson ) : ?>
                    <article class="lesson-card">
                        <h3><a href="<?php echo esc_url( get_permalink( $lesson ) ); ?>"><?php echo esc_html( get_the_title( $lesson ) ); ?></a></h3>
                        <div class="lesson-meta">
                            <?php if ( $duration = get_post_meta( $lesson->ID, 'duracion_leccion', true ) ) : ?>
                                <span>⏱️ <?php echo esc_html( $duration ); ?></span>
                            <?php endif; ?>
                            <?php
                            $skills = get_the_terms( $lesson->ID, 'course_skill' );
                            if ( $skills && ! is_wp_error( $skills ) ) {
                                echo '<span>🧠 ' . esc_html( implode( ', ', wp_list_pluck( $skills, 'name' ) ) ) . '</span>';
                            }
                            ?>
                        </div>
                        <p><?php echo esc_html( wp_trim_words( $lesson->post_excerpt ? $lesson->post_excerpt : wp_strip_all_tags( $lesson->post_content ), 30 ) ); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <footer class="entry-footer">
        <?php edit_post_link( __( 'Editar curso', 'ingles-curso' ), '<span class="edit-link">', '</span>' ); ?>
    </footer>
</article>

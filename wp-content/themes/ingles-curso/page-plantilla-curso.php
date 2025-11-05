<?php
/**
 * Template Name: Plantilla Hub de Curso de Inglés
 * Description: Página hub para presentar el curso completo con módulos, llamadas a la acción y testimonios.
 *
 * @package InglesCurso
 */

get_header();
?>
<main id="primary" class="site-main">
    <article class="course-hero">
        <div>
            <h1><?php the_title(); ?></h1>
            <p class="lead"><?php echo wp_kses_post( get_post_meta( get_the_ID(), 'resumen_curso', true ) ?: __( 'Personaliza esta introducción para destacar la propuesta de valor de tu curso.', 'ingles-curso' ) ); ?></p>
            <div class="course-meta">
                <?php if ( $duration = get_post_meta( get_the_ID(), 'duracion_curso', true ) ) : ?>
                    <div class="meta-item">
                        <span class="meta-label"><?php esc_html_e( 'Duración', 'ingles-curso' ); ?></span>
                        <span class="meta-value"><?php echo esc_html( $duration ); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ( $start = get_post_meta( get_the_ID(), 'fecha_inicio', true ) ) : ?>
                    <div class="meta-item">
                        <span class="meta-label"><?php esc_html_e( 'Inicio', 'ingles-curso' ); ?></span>
                        <span class="meta-value"><?php echo esc_html( $start ); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ( $format = get_post_meta( get_the_ID(), 'formato_curso', true ) ) : ?>
                    <div class="meta-item">
                        <span class="meta-label"><?php esc_html_e( 'Formato', 'ingles-curso' ); ?></span>
                        <span class="meta-value"><?php echo esc_html( $format ); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <a class="button" href="<?php echo esc_url( get_post_meta( get_the_ID(), 'cta_url', true ) ?: '#inscripcion' ); ?>"><?php echo esc_html( get_post_meta( get_the_ID(), 'cta_texto', true ) ?: __( 'Inscríbete ahora', 'ingles-curso' ) ); ?></a>
        </div>
        <div>
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'large' ); ?>
            <?php else : ?>
                <img src="<?php echo esc_url( INGLES_CURSO_URI . 'assets/curso-hero-placeholder.svg' ); ?>" alt="<?php esc_attr_e( 'Curso de inglés', 'ingles-curso' ); ?>">
            <?php endif; ?>
        </div>
    </article>

    <section class="learning-objectives">
        <h2><?php esc_html_e( 'Objetivos de aprendizaje', 'ingles-curso' ); ?></h2>
        <?php
        $objectives_shortcode = get_post_meta( get_the_ID(), 'objetivos_shortcode', true );
        echo do_shortcode(
            $objectives_shortcode ?: '[curso_objetivos][objetivo]' . __( 'Añade tus objetivos usando el shortcode [curso_objetivos].', 'ingles-curso' ) . '[/objetivo][/curso_objetivos]'
        );
        ?>
    </section>

    <section class="module-list">
        <h2><?php esc_html_e( 'Ruta de módulos', 'ingles-curso' ); ?></h2>
        <?php
        $modules_shortcode = get_post_meta( get_the_ID(), 'modulos_shortcode', true );
        echo do_shortcode(
            $modules_shortcode ?: '[curso_timeline][modulo titulo="' . __( 'Módulo 1', 'ingles-curso' ) . '" duracion="3 ' . __( 'semanas', 'ingles-curso' ) . '" nivel="A1-A2"]' . __( 'Describe brevemente el contenido del módulo.', 'ingles-curso' ) . '[/modulo][/curso_timeline]'
        );
        ?>
    </section>

    <section class="course-resources">
        <h2><?php esc_html_e( 'Recursos y soporte', 'ingles-curso' ); ?></h2>
        <?php
        $resources_shortcode = get_post_meta( get_the_ID(), 'recursos_shortcode', true );
        echo do_shortcode(
            $resources_shortcode ?: '[curso_recursos][recurso tipo="PDF" url="#"]' . __( 'Añade un recurso destacado.', 'ingles-curso' ) . '[/recurso][/curso_recursos]'
        );
        ?>
    </section>

    <section id="inscripcion" class="lesson-summary">
        <h2><?php esc_html_e( 'Siguiente paso', 'ingles-curso' ); ?></h2>
        <p><?php echo wp_kses_post( get_post_meta( get_the_ID(), 'cta_descripcion', true ) ?: __( 'Explica cómo se realiza la inscripción y qué soporte recibirán los estudiantes.', 'ingles-curso' ) ); ?></p>
        <a class="button" href="<?php echo esc_url( get_post_meta( get_the_ID(), 'cta_url', true ) ?: '#inscripcion' ); ?>"><?php echo esc_html( get_post_meta( get_the_ID(), 'cta_texto', true ) ?: __( 'Reservar mi plaza', 'ingles-curso' ) ); ?></a>
    </section>
</main>
<?php
get_footer();
?>

<?php
/**
 * Metaboxes para cursos y lecciones
 *
 * @package InglesCurso
 */

add_action( 'add_meta_boxes', 'ingles_curso_register_meta_boxes' );
add_action( 'save_post', 'ingles_curso_save_meta_boxes' );

function ingles_curso_register_meta_boxes() {
    add_meta_box(
        'ingles_curso_detalles',
        __( 'Detalles del curso', 'ingles-curso' ),
        'ingles_curso_render_course_meta_box',
        'course',
        'normal',
        'high'
    );

    add_meta_box(
        'ingles_leccion_detalles',
        __( 'Detalles de la lección', 'ingles-curso' ),
        'ingles_curso_render_lesson_meta_box',
        'lesson',
        'normal',
        'high'
    );

    add_meta_box(
        'ingles_page_detalles',
        __( 'Ajustes del hub de curso', 'ingles-curso' ),
        'ingles_curso_render_page_meta_box',
        'page',
        'side',
        'default'
    );
}

function ingles_curso_render_course_meta_box( $post ) {
    wp_nonce_field( 'ingles_curso_save_meta', 'ingles_curso_meta_nonce' );

    $duration    = get_post_meta( $post->ID, 'duracion_curso', true );
    $methodology = get_post_meta( $post->ID, 'metodologia_curso', true );

    ?>
    <p>
        <label for="duracion_curso"><strong><?php esc_html_e( 'Duración estimada', 'ingles-curso' ); ?></strong></label>
        <input type="text" id="duracion_curso" name="duracion_curso" class="widefat" value="<?php echo esc_attr( $duration ); ?>">
    </p>
    <p>
        <label for="metodologia_curso"><strong><?php esc_html_e( 'Metodología', 'ingles-curso' ); ?></strong></label>
        <input type="text" id="metodologia_curso" name="metodologia_curso" class="widefat" value="<?php echo esc_attr( $methodology ); ?>">
    </p>
    <?php
}

function ingles_curso_render_lesson_meta_box( $post ) {
    wp_nonce_field( 'ingles_curso_save_meta', 'ingles_curso_meta_nonce' );

    $duration = get_post_meta( $post->ID, 'duracion_leccion', true );
    $objective = get_post_meta( $post->ID, 'objetivo_principal', true );
    $course_id = get_post_meta( $post->ID, 'curso_relacionado', true );

    $courses = get_posts(
        array(
            'post_type'      => 'course',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        )
    );
    ?>
    <p>
        <label for="duracion_leccion"><strong><?php esc_html_e( 'Duración estimada', 'ingles-curso' ); ?></strong></label>
        <input type="text" id="duracion_leccion" name="duracion_leccion" class="widefat" value="<?php echo esc_attr( $duration ); ?>">
    </p>
    <p>
        <label for="objetivo_principal"><strong><?php esc_html_e( 'Objetivo principal', 'ingles-curso' ); ?></strong></label>
        <input type="text" id="objetivo_principal" name="objetivo_principal" class="widefat" value="<?php echo esc_attr( $objective ); ?>">
    </p>
    <p>
        <label for="curso_relacionado"><strong><?php esc_html_e( 'Curso asociado', 'ingles-curso' ); ?></strong></label>
        <select id="curso_relacionado" name="curso_relacionado" class="widefat">
            <option value=""><?php esc_html_e( 'Selecciona un curso', 'ingles-curso' ); ?></option>
            <?php foreach ( $courses as $course ) : ?>
                <option value="<?php echo esc_attr( $course->ID ); ?>" <?php selected( $course_id, $course->ID ); ?>><?php echo esc_html( $course->post_title ); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <?php
}

function ingles_curso_render_page_meta_box( $post ) {
    $template = get_page_template_slug( $post );
    if ( 'page-plantilla-curso.php' !== $template ) {
        echo '<p>' . esc_html__( 'Asigna la plantilla "Plantilla Hub de Curso de Inglés" para ver estos ajustes.', 'ingles-curso' ) . '</p>';
        return;
    }

    wp_nonce_field( 'ingles_curso_save_meta', 'ingles_curso_meta_nonce' );

    $fields = array(
        'resumen_curso'       => __( 'Resumen introductorio', 'ingles-curso' ),
        'duracion_curso'      => __( 'Duración total', 'ingles-curso' ),
        'fecha_inicio'        => __( 'Fecha de inicio', 'ingles-curso' ),
        'formato_curso'       => __( 'Formato (online, híbrido...)', 'ingles-curso' ),
        'cta_texto'           => __( 'Texto del botón principal', 'ingles-curso' ),
        'cta_url'             => __( 'URL del botón principal', 'ingles-curso' ),
        'cta_descripcion'     => __( 'Detalle del proceso de inscripción', 'ingles-curso' ),
        'objetivos_shortcode' => __( 'Shortcode de objetivos', 'ingles-curso' ),
        'modulos_shortcode'   => __( 'Shortcode de módulos', 'ingles-curso' ),
        'recursos_shortcode'  => __( 'Shortcode de recursos', 'ingles-curso' ),
    );

    foreach ( $fields as $key => $label ) {
        $value = get_post_meta( $post->ID, $key, true );
        ?>
        <p>
            <label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
            <?php if ( 'cta_descripcion' === $key ) : ?>
                <textarea id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" class="widefat" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
            <?php else : ?>
                <input type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" class="widefat" value="<?php echo esc_attr( $value ); ?>">
            <?php endif; ?>
        </p>
        <?php
    }
}

function ingles_curso_save_meta_boxes( $post_id ) {
    if ( ! isset( $_POST['ingles_curso_meta_nonce'] ) || ! wp_verify_nonce( $_POST['ingles_curso_meta_nonce'], 'ingles_curso_save_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    $text_fields = array(
        'duracion_curso',
        'metodologia_curso',
        'duracion_leccion',
        'objetivo_principal',
        'curso_relacionado',
        'resumen_curso',
        'fecha_inicio',
        'formato_curso',
        'cta_texto',
        'cta_url',
        'objetivos_shortcode',
        'modulos_shortcode',
        'recursos_shortcode',
    );

    foreach ( $text_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
        }
    }

    if ( isset( $_POST['cta_descripcion'] ) ) {
        update_post_meta( $post_id, 'cta_descripcion', sanitize_textarea_field( wp_unslash( $_POST['cta_descripcion'] ) ) );
    }

    if ( empty( $_POST['curso_relacionado'] ) ) {
        delete_post_meta( $post_id, 'curso_relacionado' );
    }
}

<?php
/**
 * Funciones principales del tema Curso de Inglés Progresivo
 */

define( 'INGLES_CURSO_VERSION', '1.1.0' );

define( 'INGLES_CURSO_PATH', get_template_directory() . '/' );
define( 'INGLES_CURSO_URI', get_template_directory_uri() . '/' );

const INGLES_CURSO_REQUIRED_PLUGINS = array(
    array(
        'name' => 'Contact Form 7',
        'file' => 'contact-form-7/wp-contact-form-7.php',
    ),
    array(
        'name' => 'User Registration – Custom Registration Form, Login and User Profile for WordPress',
        'file' => 'user-registration/user-registration.php',
    ),
);

require_once INGLES_CURSO_PATH . 'inc/meta-boxes.php';

add_action( 'after_setup_theme', 'ingles_curso_setup' );
add_action( 'wp_enqueue_scripts', 'ingles_curso_assets' );
add_action( 'init', 'ingles_curso_register_post_types' );
add_action( 'init', 'ingles_curso_register_taxonomies' );
add_action( 'widgets_init', 'ingles_curso_sidebars' );
add_action( 'init', 'ingles_curso_register_shortcodes' );
add_action( 'after_switch_theme', 'ingles_curso_handle_theme_activation' );
add_action( 'admin_notices', 'ingles_curso_missing_plugins_notice' );

/**
 * Soporte básico del tema
 */
function ingles_curso_setup() {
    load_theme_textdomain( 'ingles-curso', INGLES_CURSO_PATH . 'languages' );

    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'wp-block-styles' );

    register_nav_menus(
        array(
            'primary' => __( 'Menú principal', 'ingles-curso' ),
            'footer'  => __( 'Menú del pie de página', 'ingles-curso' ),
        )
    );
}

/**
 * Carga de estilos y scripts
 */
function ingles_curso_assets() {
    wp_enqueue_style( 'ingles-curso-style', INGLES_CURSO_URI . 'style.css', array(), INGLES_CURSO_VERSION );
}

/**
 * Registro de tipos de contenido personalizados para cursos y lecciones
 */
function ingles_curso_register_post_types() {
    $course_labels = array(
        'name'               => _x( 'Cursos', 'post type general name', 'ingles-curso' ),
        'singular_name'      => _x( 'Curso', 'post type singular name', 'ingles-curso' ),
        'menu_name'          => _x( 'Cursos', 'admin menu', 'ingles-curso' ),
        'name_admin_bar'     => _x( 'Curso', 'add new on admin bar', 'ingles-curso' ),
        'add_new'            => _x( 'Añadir nuevo', 'curso', 'ingles-curso' ),
        'add_new_item'       => __( 'Añadir nuevo curso', 'ingles-curso' ),
        'new_item'           => __( 'Nuevo curso', 'ingles-curso' ),
        'edit_item'          => __( 'Editar curso', 'ingles-curso' ),
        'view_item'          => __( 'Ver curso', 'ingles-curso' ),
        'all_items'          => __( 'Todos los cursos', 'ingles-curso' ),
        'search_items'       => __( 'Buscar cursos', 'ingles-curso' ),
        'parent_item_colon'  => __( 'Curso padre:', 'ingles-curso' ),
        'not_found'          => __( 'No se han encontrado cursos.', 'ingles-curso' ),
        'not_found_in_trash' => __( 'No se han encontrado cursos en la papelera.', 'ingles-curso' ),
    );

    $course_args = array(
        'labels'             => $course_labels,
        'public'             => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-welcome-learn-more',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'cursos-ingles' ),
        'show_in_graphql'    => true,
    );

    register_post_type( 'course', $course_args );

    $lesson_labels = array(
        'name'               => _x( 'Lecciones', 'post type general name', 'ingles-curso' ),
        'singular_name'      => _x( 'Lección', 'post type singular name', 'ingles-curso' ),
        'menu_name'          => _x( 'Lecciones', 'admin menu', 'ingles-curso' ),
        'name_admin_bar'     => _x( 'Lección', 'add new on admin bar', 'ingles-curso' ),
        'add_new'            => _x( 'Añadir nueva', 'lección', 'ingles-curso' ),
        'add_new_item'       => __( 'Añadir nueva lección', 'ingles-curso' ),
        'new_item'           => __( 'Nueva lección', 'ingles-curso' ),
        'edit_item'          => __( 'Editar lección', 'ingles-curso' ),
        'view_item'          => __( 'Ver lección', 'ingles-curso' ),
        'all_items'          => __( 'Todas las lecciones', 'ingles-curso' ),
        'search_items'       => __( 'Buscar lecciones', 'ingles-curso' ),
        'parent_item_colon'  => __( 'Lección padre:', 'ingles-curso' ),
        'not_found'          => __( 'No se han encontrado lecciones.', 'ingles-curso' ),
        'not_found_in_trash' => __( 'No se han encontrado lecciones en la papelera.', 'ingles-curso' ),
    );

    $lesson_args = array(
        'labels'             => $lesson_labels,
        'public'             => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-translation',
        'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
        'has_archive'        => false,
        'rewrite'            => array( 'slug' => 'leccion-ingles' ),
        'show_in_graphql'    => true,
    );

    register_post_type( 'lesson', $lesson_args );
}

/**
 * Taxonomías para clasificar el curso según nivel y habilidades
 */
function ingles_curso_register_taxonomies() {
    $level_labels = array(
        'name'              => _x( 'Niveles', 'taxonomy general name', 'ingles-curso' ),
        'singular_name'     => _x( 'Nivel', 'taxonomy singular name', 'ingles-curso' ),
        'search_items'      => __( 'Buscar niveles', 'ingles-curso' ),
        'all_items'         => __( 'Todos los niveles', 'ingles-curso' ),
        'parent_item'       => __( 'Nivel padre', 'ingles-curso' ),
        'parent_item_colon' => __( 'Nivel padre:', 'ingles-curso' ),
        'edit_item'         => __( 'Editar nivel', 'ingles-curso' ),
        'update_item'       => __( 'Actualizar nivel', 'ingles-curso' ),
        'add_new_item'      => __( 'Añadir nuevo nivel', 'ingles-curso' ),
        'new_item_name'     => __( 'Nombre del nuevo nivel', 'ingles-curso' ),
        'menu_name'         => __( 'Niveles', 'ingles-curso' ),
    );

    $level_args = array(
        'hierarchical'      => true,
        'labels'            => $level_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'nivel-curso' ),
    );

    register_taxonomy( 'course_level', array( 'course' ), $level_args );

    $skill_labels = array(
        'name'              => _x( 'Habilidades', 'taxonomy general name', 'ingles-curso' ),
        'singular_name'     => _x( 'Habilidad', 'taxonomy singular name', 'ingles-curso' ),
        'search_items'      => __( 'Buscar habilidades', 'ingles-curso' ),
        'popular_items'     => __( 'Habilidades populares', 'ingles-curso' ),
        'all_items'         => __( 'Todas las habilidades', 'ingles-curso' ),
        'edit_item'         => __( 'Editar habilidad', 'ingles-curso' ),
        'update_item'       => __( 'Actualizar habilidad', 'ingles-curso' ),
        'add_new_item'      => __( 'Añadir nueva habilidad', 'ingles-curso' ),
        'new_item_name'     => __( 'Nombre de la nueva habilidad', 'ingles-curso' ),
        'separate_items_with_commas' => __( 'Separar habilidades con comas', 'ingles-curso' ),
        'add_or_remove_items'        => __( 'Añadir o eliminar habilidades', 'ingles-curso' ),
        'choose_from_most_used'      => __( 'Elegir de las más utilizadas', 'ingles-curso' ),
        'menu_name'                  => __( 'Habilidades', 'ingles-curso' ),
    );

    $skill_args = array(
        'hierarchical'          => false,
        'labels'                => $skill_labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'show_in_rest'          => true,
        'rewrite'               => array( 'slug' => 'habilidad' ),
    );

    register_taxonomy( 'course_skill', array( 'lesson' ), $skill_args );
}

/**
 * Registro de áreas de widgets
 */
function ingles_curso_sidebars() {
    register_sidebar(
        array(
            'name'          => __( 'Barra lateral principal', 'ingles-curso' ),
            'id'            => 'sidebar-1',
            'description'   => __( 'Añade widgets para mostrar recursos complementarios del curso.', 'ingles-curso' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}

/**
 * Shortcodes para mostrar un temario, objetivos y recursos
 */
function ingles_curso_register_shortcodes() {
    add_shortcode( 'curso_objetivos', 'ingles_curso_shortcode_objetivos' );
    add_shortcode( 'curso_timeline', 'ingles_curso_shortcode_timeline' );
    add_shortcode( 'curso_recursos', 'ingles_curso_shortcode_recursos' );
    add_shortcode( 'curso_acceso', 'ingles_curso_shortcode_acceso' );
}

function ingles_curso_shortcode_objetivos( $atts, $content = null ) {
    $atts = shortcode_atts(
        array(
            'titulo' => __( 'Objetivos de aprendizaje', 'ingles-curso' ),
        ),
        $atts,
        'curso_objetivos'
    );

    $items = wp_kses_post( $content );

    return sprintf(
        '<section class="learning-objectives"><h2>%1$s</h2><ul>%2$s</ul></section>',
        esc_html( $atts['titulo'] ),
        do_shortcode( $items )
    );
}

function ingles_curso_shortcode_timeline( $atts, $content = null ) {
    $atts = shortcode_atts(
        array(
            'titulo' => __( 'Progresión del curso', 'ingles-curso' ),
        ),
        $atts,
        'curso_timeline'
    );

    $items = wp_kses_post( $content );

    return sprintf(
        '<section class="module-list"><h2>%1$s</h2><div class="timeline">%2$s</div></section>',
        esc_html( $atts['titulo'] ),
        do_shortcode( $items )
    );
}

function ingles_curso_shortcode_recursos( $atts, $content = null ) {
    $atts = shortcode_atts(
        array(
            'titulo' => __( 'Recursos adicionales', 'ingles-curso' ),
        ),
        $atts,
        'curso_recursos'
    );

    $items = wp_kses_post( $content );

    return sprintf(
        '<section class="course-resources"><h2>%1$s</h2><ul>%2$s</ul></section>',
        esc_html( $atts['titulo'] ),
        do_shortcode( $items )
    );
}

/**
 * Shortcodes anidados para ítems individuales
 */
add_shortcode( 'objetivo', 'ingles_curso_shortcode_objetivo_item' );
add_shortcode( 'modulo', 'ingles_curso_shortcode_modulo_item' );
add_shortcode( 'recurso', 'ingles_curso_shortcode_recurso_item' );

function ingles_curso_shortcode_objetivo_item( $atts, $content = null ) {
    return '<li><span class="badge">🎯</span><div>' . wp_kses_post( $content ) . '</div></li>';
}

function ingles_curso_shortcode_modulo_item( $atts, $content = null ) {
    $atts = shortcode_atts(
        array(
            'titulo' => __( 'Módulo', 'ingles-curso' ),
            'duracion' => '',
            'nivel' => '',
        ),
        $atts,
        'modulo'
    );

    $details = array();

    if ( ! empty( $atts['duracion'] ) ) {
        $details[] = sprintf( '<span>⏱️ %s</span>', esc_html( $atts['duracion'] ) );
    }

    if ( ! empty( $atts['nivel'] ) ) {
        $details[] = sprintf( '<span>🧭 %s</span>', esc_html( $atts['nivel'] ) );
    }

    return sprintf(
        '<div class="timeline-item"><div class="timeline-dot" aria-hidden="true"></div><div><strong>%1$s:</strong> %2$s<div class="lesson-meta">%3$s</div></div></div>',
        esc_html( $atts['titulo'] ),
        wp_kses_post( $content ),
        implode( '', $details )
    );
}

function ingles_curso_shortcode_recurso_item( $atts, $content = null ) {
    $atts = shortcode_atts(
        array(
            'tipo' => __( 'Recurso', 'ingles-curso' ),
            'url'  => '',
        ),
        $atts,
        'recurso'
    );

    $link = ! empty( $atts['url'] )
        ? sprintf( '<a class="button" href="%1$s" target="_blank" rel="noopener">%2$s</a>', esc_url( $atts['url'] ), esc_html__( 'Abrir', 'ingles-curso' ) )
        : '';

    return sprintf(
        '<li><span class="badge">%1$s</span><div>%2$s %3$s</div></li>',
        esc_html( $atts['tipo'] ),
        wp_kses_post( $content ),
        $link
    );
}

/**
 * Helper para obtener lecciones asociadas a un curso mediante un meta campo
 */
function ingles_curso_get_lessons_for_course( $course_id ) {
    $lessons = get_posts(
        array(
            'post_type'      => 'lesson',
            'posts_per_page' => -1,
            'meta_key'       => 'curso_relacionado',
            'meta_value'     => $course_id,
            'orderby'        => array(
                'menu_order' => 'ASC',
                'title'      => 'ASC',
            ),
        )
    );

    return $lessons;
}

/**
 * Plantilla de bloque para el editor
 */
add_action( 'init', function() {
    $pattern_file = INGLES_CURSO_PATH . 'patterns/plan-general.php';

    if ( file_exists( $pattern_file ) ) {
        register_block_pattern_category( 'ingles-curso', array( 'label' => __( 'Curso de Inglés', 'ingles-curso' ) ) );
        register_block_pattern( 'ingles-curso/plan-general', require $pattern_file );
    }
} );

/**
 * Maneja la activación del tema: plugins, páginas y opciones clave.
 */
function ingles_curso_handle_theme_activation() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
        ingles_curso_activate_required_plugins();
    }

    ingles_curso_create_site_structure();
}

/**
 * Activa los plugins necesarios si están instalados y guarda avisos si faltan.
 */
function ingles_curso_activate_required_plugins() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $missing_or_failed = array();

    foreach ( INGLES_CURSO_REQUIRED_PLUGINS as $plugin ) {
        $plugin_file = $plugin['file'];
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;

        if ( file_exists( $plugin_path ) ) {
            if ( ! is_plugin_active( $plugin_file ) ) {
                $result = activate_plugin( $plugin_file );

                if ( is_wp_error( $result ) ) {
                    $missing_or_failed[] = sprintf( '%s (%s)', $plugin['name'], $result->get_error_message() );
                }
            }
        } else {
            $missing_or_failed[] = $plugin['name'];
        }
    }

    if ( ! empty( $missing_or_failed ) ) {
        set_transient( 'ingles_curso_missing_plugins', $missing_or_failed, MINUTE_IN_SECONDS * 30 );
    } else {
        delete_transient( 'ingles_curso_missing_plugins' );
    }
}

/**
 * Muestra un aviso en el escritorio cuando faltan plugins necesarios.
 */
function ingles_curso_missing_plugins_notice() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    $missing = get_transient( 'ingles_curso_missing_plugins' );

    if ( empty( $missing ) ) {
        return;
    }

    $message = sprintf(
        /* translators: %s: comma separated list of plugins */
        __( 'El tema Curso de Inglés Progresivo necesita que instales o actives los siguientes plugins: %s.', 'ingles-curso' ),
        esc_html( implode( ', ', $missing ) )
    );
    ?>
    <div class="notice notice-warning">
        <p><?php echo wp_kses_post( $message ); ?></p>
    </div>
    <?php
}

/**
 * Crea o actualiza la estructura de páginas requerida para el curso.
 */
function ingles_curso_create_site_structure() {
    $structure = array(
        array(
            'title'    => __( 'Inicio', 'ingles-curso' ),
            'slug'     => 'inicio',
            'template' => 'page-plantilla-curso.php',
            'content'  => '',
        ),
        array(
            'title'   => __( 'Registro / Login', 'ingles-curso' ),
            'slug'    => 'registro-login',
            'content' => '<!-- wp:shortcode -->[curso_acceso]<!-- /wp:shortcode -->',
        ),
        array(
            'title'   => __( 'Cursos', 'ingles-curso' ),
            'slug'    => 'cursos',
            'content' => '<!-- wp:paragraph -->' . esc_html__( 'Selecciona el nivel que mejor se adapte a tus objetivos actuales.', 'ingles-curso' ) . '<!-- /wp:paragraph -->',
            'children' => array(
                array(
                    'title'   => __( 'Básico', 'ingles-curso' ),
                    'slug'    => 'basico',
                    'content' => '<!-- wp:paragraph -->' . esc_html__( 'Fundamentos del idioma, vocabulario esencial y estructuras básicas para empezar a comunicarte.', 'ingles-curso' ) . '<!-- /wp:paragraph -->',
                ),
                array(
                    'title'   => __( 'Intermedio', 'ingles-curso' ),
                    'slug'    => 'intermedio',
                    'content' => '<!-- wp:paragraph -->' . esc_html__( 'Profundiza en la gramática, amplía tu vocabulario y practica situaciones de la vida real.', 'ingles-curso' ) . '<!-- /wp:paragraph -->',
                ),
                array(
                    'title'   => __( 'Avanzado', 'ingles-curso' ),
                    'slug'    => 'avanzado',
                    'content' => '<!-- wp:paragraph -->' . esc_html__( 'Perfecciona tus habilidades y domina la comunicación profesional y académica.', 'ingles-curso' ) . '<!-- /wp:paragraph -->',
                ),
            ),
        ),
        array(
            'title'   => __( 'Habilidades', 'ingles-curso' ),
            'slug'    => 'habilidades',
            'content' => '<!-- wp:paragraph -->' . esc_html__( 'Refuerza tus competencias con rutas específicas para cada habilidad comunicativa.', 'ingles-curso' ) . '<!-- /wp:paragraph -->',
            'children' => array(
                array(
                    'title'   => __( 'Reading', 'ingles-curso' ),
                    'slug'    => 'reading',
                    'content' => '<!-- wp:paragraph -->' . esc_html__( 'Estrategias de comprensión lectora y análisis de textos auténticos.', 'ingles-curso' ) . '<!-- /wp:paragraph -->',
                ),
                array(
                    'title'   => __( 'Writing', 'ingles-curso' ),
                    'slug'    => 'writing',
                    'content' => '<!-- wp:paragraph -->' . esc_html__( 'Redacción guiada, feedback y plantillas para distintos tipos de textos.', 'ingles-curso' ) . '<!-- /wp:paragraph -->',
                ),
                array(
                    'title'   => __( 'Listening', 'ingles-curso' ),
                    'slug'    => 'listening',
                    'content' => '<!-- wp:paragraph -->' . esc_html__( 'Actividades auditivas con diferentes acentos y niveles de dificultad.', 'ingles-curso' ) . '<!-- /wp:paragraph -->',
                ),
                array(
                    'title'   => __( 'Speaking', 'ingles-curso' ),
                    'slug'    => 'speaking',
                    'content' => '<!-- wp:paragraph -->' . esc_html__( 'Sesiones de práctica oral, pronunciación y confianza comunicativa.', 'ingles-curso' ) . '<!-- /wp:paragraph -->',
                ),
            ),
        ),
        array(
            'title'   => __( 'Blog', 'ingles-curso' ),
            'slug'    => 'blog',
            'content' => '',
        ),
        array(
            'title'   => __( 'Contacto', 'ingles-curso' ),
            'slug'    => 'contacto',
            'content' => '<!-- wp:paragraph -->' . esc_html__( 'Rellena el formulario para ponerte en contacto con nuestro equipo académico.', 'ingles-curso' ) . '<!-- /wp:paragraph -->\n<!-- wp:shortcode -->[contact-form-7]<!-- /wp:shortcode -->',
        ),
    );

    $page_ids = array();

    foreach ( $structure as $page ) {
        $page_ids[ $page['slug'] ] = ingles_curso_ensure_page( $page );
    }

    if ( ! empty( $page_ids['inicio'] ) ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $page_ids['inicio'] );
    }

    if ( ! empty( $page_ids['blog'] ) ) {
        update_option( 'page_for_posts', $page_ids['blog'] );
    }

    update_option( 'users_can_register', 1 );
}

/**
 * Crea una página si no existe y gestiona hijos recursivamente.
 *
 * @param array $page Datos de la página.
 * @param int   $parent_id ID del padre si existe.
 *
 * @return int ID de la página creada o existente.
 */
function ingles_curso_ensure_page( $page, $parent_id = 0 ) {
    $defaults = array(
        'title'    => '',
        'slug'     => '',
        'content'  => '',
        'template' => '',
        'children' => array(),
    );

    $page = wp_parse_args( $page, $defaults );
    $slug = sanitize_title( $page['slug'] );

    $existing = get_page_by_path( $slug, OBJECT, 'page' );
    $page_id  = 0;
    $created  = false;

    if ( $existing ) {
        $page_id = (int) $existing->ID;

        $update_args = array( 'ID' => $page_id );
        $needs_update = false;

        if ( (int) $existing->post_parent !== (int) $parent_id ) {
            $update_args['post_parent'] = $parent_id;
            $needs_update               = true;
        }

        if ( empty( $existing->post_content ) && ! empty( $page['content'] ) ) {
            $update_args['post_content'] = $page['content'];
            $needs_update               = true;
        }

        if ( $needs_update ) {
            wp_update_post( $update_args );
        }
    } else {
        $page_id = wp_insert_post(
            array(
                'post_title'   => $page['title'],
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_parent'  => $parent_id,
                'post_content' => $page['content'],
            )
        );

        if ( is_wp_error( $page_id ) ) {
            return 0;
        }

        $created = true;
    }

    if ( $page_id && ! empty( $page['template'] ) ) {
        update_post_meta( $page_id, '_wp_page_template', $page['template'] );
    }

    if ( ! empty( $page['children'] ) && $page_id ) {
        foreach ( $page['children'] as $child ) {
            ingles_curso_ensure_page( $child, $page_id );
        }
    }

    if ( $created && ! empty( $page['children'] ) ) {
        clean_post_cache( $page_id );
    }

    return (int) $page_id;
}

/**
 * Shortcode para mostrar el portal de acceso con formularios de login y registro.
 */
function ingles_curso_shortcode_acceso( $atts ) {
    $atts = shortcode_atts(
        array(
            'mostrar_titulo' => 'yes',
        ),
        $atts,
        'curso_acceso'
    );

    $output  = '<section class="course-access-portal">';

    if ( 'yes' === strtolower( $atts['mostrar_titulo'] ) ) {
        $output .= '<h2>' . esc_html__( 'Accede a tu aprendizaje', 'ingles-curso' ) . '</h2>';
    }

    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        $output      .= '<p class="course-access-status">' . sprintf(
            /* translators: %s: current user display name */
            esc_html__( 'Hola %s, ya estás autenticado. Explora tus cursos disponibles.', 'ingles-curso' ),
            esc_html( $current_user->display_name )
        ) . '</p>';
        $output .= '<p><a class="button" href="' . esc_url( admin_url( 'profile.php' ) ) . '">' . esc_html__( 'Ver mi perfil', 'ingles-curso' ) . '</a> ';
        $output .= '<a class="button button-secondary" href="' . esc_url( wp_logout_url( home_url() ) ) . '">' . esc_html__( 'Cerrar sesión', 'ingles-curso' ) . '</a></p>';
        $output .= '</section>';

        return $output;
    }

    $output .= '<div class="course-access-grid">';

    $login_form = wp_login_form(
        array(
            'echo'           => false,
            'label_username' => __( 'Correo electrónico o usuario', 'ingles-curso' ),
            'label_password' => __( 'Contraseña', 'ingles-curso' ),
            'label_remember' => __( 'Recordarme', 'ingles-curso' ),
            'label_log_in'   => __( 'Iniciar sesión', 'ingles-curso' ),
        )
    );

    $output .= '<div class="course-access-card">';
    $output .= '<h3>' . esc_html__( 'Iniciar sesión', 'ingles-curso' ) . '</h3>';
    $output .= $login_form;
    $output .= '</div>';

    $registration = '<p>' . esc_html__( 'Activa la casilla "Cualquiera puede registrarse" en Ajustes > Generales para permitir el registro de nuevos estudiantes.', 'ingles-curso' ) . '</p>';

    if ( shortcode_exists( 'user_registration_form' ) ) {
        $registration = do_shortcode( '[user_registration_form]' );
    } elseif ( function_exists( 'user_registration_form' ) ) {
        $registration = user_registration_form();
    } elseif ( function_exists( 'ur_render_form' ) ) {
        $registration = ur_render_form();
    } elseif ( get_option( 'users_can_register' ) ) {
        $registration = '<form class="course-register-form" action="' . esc_url( site_url( 'wp-login.php?action=register', 'login_post' ) ) . '" method="post">';
        $registration .= '<p><label for="user_login">' . esc_html__( 'Nombre de usuario', 'ingles-curso' ) . '</label><input type="text" name="user_login" id="user_login" autocomplete="username" required></p>';
        $registration .= '<p><label for="user_email">' . esc_html__( 'Correo electrónico', 'ingles-curso' ) . '</label><input type="email" name="user_email" id="user_email" autocomplete="email" required></p>';
        $registration .= '<p class="description">' . esc_html__( 'Recibirás un enlace para establecer tu contraseña.', 'ingles-curso' ) . '</p>';
        $registration .= wp_nonce_field( 'register', 'register_nonce', true, false );
        $registration .= '<input type="hidden" name="redirect_to" value="' . esc_url( home_url( '/' ) ) . '">';
        $registration .= '<p><button type="submit" name="wp-submit" class="button">' . esc_html__( 'Crear cuenta', 'ingles-curso' ) . '</button></p>';
        $registration .= '</form>';
    }

    $output .= '<div class="course-access-card">';
    $output .= '<h3>' . esc_html__( 'Crear cuenta', 'ingles-curso' ) . '</h3>';
    $output .= $registration;
    $output .= '</div>';

    $output .= '</div>';
    $output .= '<p class="course-access-links"><a href="' . esc_url( wp_lostpassword_url() ) . '">' . esc_html__( '¿Olvidaste tu contraseña?', 'ingles-curso' ) . '</a></p>';
    $output .= '</section>';

    return $output;
}

<?php
/**
 * Plantilla para contenido no encontrado
 *
 * @package InglesCurso
 */
?>
<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e( 'No encontramos resultados', 'ingles-curso' ); ?></h1>
    </header>

    <div class="page-content">
        <p><?php esc_html_e( 'Prueba a realizar una nueva búsqueda o explora los cursos disponibles.', 'ingles-curso' ); ?></p>
        <?php get_search_form(); ?>
    </div>
</section>

<?php
/**
 * Pie del tema
 *
 * @package InglesCurso
 */
?>
    <footer class="site-footer">
        <div class="footer-widgets">
            <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
                <?php dynamic_sidebar( 'sidebar-1' ); ?>
            <?php endif; ?>
        </div>
        <div class="site-info">
            <p>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> · <?php esc_html_e( 'Curso de inglés diseñado con pasión por el aprendizaje continuo.', 'ingles-curso' ); ?></p>
            <nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Menú del pie de página', 'ingles-curso' ); ?>">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'footer',
                        'menu_class'     => 'footer-menu',
                        'container'      => false,
                    )
                );
                ?>
            </nav>
        </div>
    </footer>
</div><!-- .site -->
<?php wp_footer(); ?>
</body>
</html>

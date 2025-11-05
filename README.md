# Curso de Inglés Progresivo – Tema de WordPress

Este repositorio contiene un tema de WordPress listo para desplegar un curso de inglés con estructura modular. Incluye:

- Tipos de contenido personalizados para **Cursos** y **Lecciones**.
- Taxonomías para clasificar niveles y habilidades.
- Shortcodes para maquetar objetivos, módulos, recursos y el portal de acceso.
- Plantillas y patrones de bloques para construir hubs de curso rápidamente.
- Metaboxes en el administrador para capturar información clave del curso y las lecciones.

## Instalación rápida

1. Copia la carpeta `wp-content/themes/ingles-curso` en la instalación de WordPress.
2. Activa el tema desde Apariencia → Temas.
3. Al activarlo se comprobará la disponibilidad de los plugins requeridos y se crearán automáticamente las páginas "Inicio", "Registro / Login", "Cursos" (con sus niveles), "Habilidades" (con cada skill), "Blog" y "Contacto". Además se definirá "Inicio" como portada estática y "Blog" como página de entradas.
4. Crea un curso desde Cursos → Añadir nuevo y completa los campos personalizados (duración, metodología, etc.).
5. Crea lecciones y asócialas al curso mediante el selector "Curso asociado".
6. Ajusta los contenidos de las páginas generadas según tus necesidades y edita el hub utilizando la plantilla **Plantilla Hub de Curso de Inglés**.

## Plugins requeridos

El tema intentará activar automáticamente estos complementos al activarse. Si no se encuentran instalados se mostrará un aviso en el escritorio para que puedas instalarlos manualmente.

- [Contact Form 7](https://es.wordpress.org/plugins/contact-form-7/) para gestionar el formulario de la página de contacto.
- [User Registration – Custom Registration Form, Login and User Profile for WordPress](https://es.wordpress.org/plugins/user-registration/) para ofrecer un flujo de alta y acceso más completo en la página de registro.

## Shortcodes disponibles

- `[curso_objetivos]` con elementos `[objetivo]`.
- `[curso_timeline]` con elementos `[modulo titulo="" duracion="" nivel=""]`.
- `[curso_recursos]` con elementos `[recurso tipo="" url=""]`.
- `[curso_acceso mostrar_titulo="yes"]` para mostrar el portal de acceso con login y registro en la página "Registro / Login".

Estos shortcodes permiten crear módulos, objetivos y recursos tanto en cursos, páginas o bloques reutilizables.

## Patrón de bloques

El patrón **Plan General del Curso de Inglés** aparece en el inserter de patrones dentro de la categoría "Curso de Inglés" para maquetar secciones de manera visual desde el editor de bloques.

## Requisitos

- WordPress 6.0 o superior.
- PHP 7.4 o superior.

## Personalización

- Edita los estilos globales en `style.css`.
- Sustituye el recurso `assets/curso-hero-placeholder.svg` por imágenes de marca.
- Amplía los metaboxes en `inc/meta-boxes.php` para añadir más campos (precio, cupos, etc.).

¡Listo! Con este tema puedes lanzar un curso de inglés completo, coherente y fácil de actualizar.

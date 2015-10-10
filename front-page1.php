<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package kamoha
 */
?><!DOCTYPE html>
<head>
    <title><?php wp_title( '|', true, 'right' ); ?></title>
</head>
<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package kamoha
 */
wp_head();
?>
<body <?php body_class( 'front-page' );  // clear class is added so the footer and its bottom margin fit into the body         ?>>
    <img src="<?php echo get_template_directory_uri() ?>/images/under_construction.jpg" alt="האתר בבנייה. מיד נשוב">
    <?php wp_footer(); ?>
</body>
</html>
<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <main id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package New2SATX
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<!-- Google tag (gtag.js) -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-CJ4WHW7LJS"></script>
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'G-CJ4WHW7LJS');
		</script>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
		<link rel="profile" href="http://gmpg.org/xfn/11">

		<?php wp_head(); ?>
		<!-- Google tag (gtag.js) -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-8G2EWXZDRY"></script>
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'G-8G2EWXZDRY');
		</script>
	</head>

	<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
		<a class='skip-link screen-reader-text' href='#content'><?php esc_html_e( 'Skip to content', 'new2satx' ); ?></a>

		<main id="content" role="main" class="main">

		<header id="nav" class="nav" role="banner">
		</header>
		

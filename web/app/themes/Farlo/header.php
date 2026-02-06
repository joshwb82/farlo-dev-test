<!doctype html>
<html <?php language_attributes(); ?><?php if (function_exists('farlo_the_html_classes')) { echo ' '; farlo_the_html_classes(); } ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" media="(prefers-color-scheme: light)" content="white">
        <meta name="theme-color" media="(prefers-color-scheme: dark)" content="black">
        <link rel="icon" href="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/favicon.ico">

        <?php // Add a pingback url auto-discovery header for singularly identifiable articles. ?>
        <?php if (is_singular() && pings_open()) : ?>
            <link rel="pingback" href="<?php echo esc_url(get_bloginfo('pingback_url')); ?>">
        <?php endif; ?>
        <title>Farlo | Theatre Shows</title>

        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
        <?php wp_body_open(); ?>

        <div id="page" class="site">

            <!-- Skip link -->
            <a class="skip-link screen-reader-text" href="#site-main">
                <?php esc_html_e('Skip to content', 'farlo'); ?>
            </a>

            <!-- Header -->
            <header>
                <a href="/" target="_self">
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/dist/images/farlo-logo.png" class="logo" alt="<?php bloginfo('name'); ?>">
                </a>

                <h1><?php the_title(); ?></h1>
            </header>
            
            <!-- Main Content -->
            <main id="site-main" class="site-main" role="main">

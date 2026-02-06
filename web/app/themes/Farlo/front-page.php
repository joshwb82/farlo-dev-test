<?php get_header();?>

    <!-- Show Grid -->
    <div class="show-list">
        <?php
            get_template_part('modules/show-grid');
        ?>
    </div>

    <!-- Gutenberg block content -->    
    <?php the_content(); ?>


<?php get_footer();

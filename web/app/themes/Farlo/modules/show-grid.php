<?php

    /**
     * Shows Grid
     */

    $args = [
        'post_type'      => 'shows',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'paged'          => max(1, get_query_var('paged'), get_query_var('page')),
    ];

    $query = new WP_Query($args);

    /** Display Shows */ 
    if ($query->have_posts()) : ?>

        <!-- Shows grid --> 
        <section class="shows-grid">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <article <?php post_class('show-card'); ?>>

                    <!-- Title --> 
                    <h2 class="show-title"><?php the_title(); ?></h2>

                    <!-- Image --> 
                    <?php
                    $image = get_field('show_image');
                    if ($image): ?>
                        <a href="<?php the_permalink(); ?>">
                            <img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>">
                        </a>
                    <?php endif; ?>

                    <!-- Start / End date --> 
                    <?php
                    $start_raw = get_field('start_date');
                    $end_raw   = get_field('end_date');

                    $start = '';
                    $end   = '';

                    if ($start_raw) {
                        $dt = DateTime::createFromFormat('Ymd', $start_raw);
                        if ($dt) {
                            $start = $dt->format('j M Y');
                        }
                    }

                    if ($end_raw) {
                        $dt = DateTime::createFromFormat('Ymd', $end_raw);
                        if ($dt) {
                            $end = $dt->format('j M Y');
                        }
                    }

                    if ($start || $end): ?>
                        <p class="show-dates">
                            <?php if ($start) echo esc_html($start); ?>
                            <?php if ($start && $end) echo ' – '; ?>
                            <?php if ($end) echo esc_html($end); ?>
                        </p>
                    <?php endif; ?>


                    <!-- Minimum Price --> 
                    <?php
                    $price = get_field('min_price');
                    if ($price): ?>
                        <p class="show-price">
                            From £<?php echo esc_html($price); ?>
                        </p>
                    <?php endif; ?>

                    <!-- More Info --> 
                    <a href="<?php the_permalink(); ?>" class="btn">More Info</a>

                    <!-- Book Now --> 
                    <?php
                    $booklink = get_field('booking_link');
                    if ($booklink): ?>
                        <a href="<?php echo esc_html($booklink); ?>" class="btn" target="_blank">Book Now</a>
                    <?php endif; ?>

                </article>
            <?php endwhile; ?>
        </section>

        <!-- Pagination --> 
        <?php
        echo paginate_links([
            'total'   => $query->max_num_pages,
            'current' => max(1, get_query_var('paged'), get_query_var('page')),
        ]);
        ?>

    <!-- No Shows found --> 
    <?php else : ?>

        <p>No shows found.</p>

    <?php endif;

    wp_reset_postdata();
?>

<?php
get_header();

if (have_posts()) :
	while (have_posts()) : the_post(); ?>

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
            <h3>SHOW DATES</h3>
            <p class="show-dates">
                <?php if ($start) echo esc_html($start); ?>
                <?php if ($start && $end) echo ' – '; ?>
                <?php if ($end) echo esc_html($end); ?>
            </p>
        <?php endif; ?>

        <!-- Show Content --> 
		<article <?php post_class(); ?>>

			<div class="show-content">
				<?php the_content(); ?>
			</div>

            <!-- Book Now --> 
            <?php
            $booklink = get_field('booking_link');
            if ($booklink): ?>
                <a href="<?php echo esc_html($booklink); ?>" class="btn book-now" target="_blank">Book Now</a>
            <?php endif; ?>

		</article>

        <!-- Back Button  --> 
        <a href="/" class="btn back-btn" target="_self">Back</a>

	<?php endwhile;
endif;

get_footer();

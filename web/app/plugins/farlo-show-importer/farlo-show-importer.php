<?php
/**
 * Plugin Name: Farlo Show Importer
 * Description: Imports shows from the Offcial London Theatre API into the local Show CPT.
 * Version: 1.0.0
 * Author: Josh
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

// CLASS - Farlo_Show_Importer_Plugin
// Adds importer page to CMS, handles import btn, and confirmation message
//---------------------------------------------
final class Farlo_Show_Importer_Plugin {
	private const CAPABILITY = 'manage_options';
	private const NONCE_ACTION = 'farlo_run_show_import';

    // Setup hooks for admin page and form submission
	public static function init(): void {
		add_action('admin_menu', [self::class, 'register_admin_page']);
		add_action('admin_post_farlo_run_show_import', [self::class, 'handle_run_import']);
	}

    // Register admin page
	public static function register_admin_page(): void {
		add_management_page(
			'Import Shows',
			'Import Shows',
			self::CAPABILITY,
			'farlo-import-shows',
			[self::class, 'render_admin_page']
		);
	}

    // Create admin page
	public static function render_admin_page(): void {

		if (!current_user_can(self::CAPABILITY)) {
			wp_die('You do not have permission to access this page.');
		}

		$status = isset($_GET['farlo_import_status']) ? sanitize_text_field((string) $_GET['farlo_import_status']) : '';
		$message = isset($_GET['farlo_import_message']) ? sanitize_text_field((string) $_GET['farlo_import_message']) : '';

		?>
            <div class="wrap">
                <h1>Import Shows</h1>

                <?php if ($status && $message): ?>
                    <div class="notice notice-<?php echo esc_attr($status); ?> is-dismissible">
                        <p><?php echo esc_html($message); ?></p>
                    </div>
                <?php endif; ?>

                <p>Click the button below to import/update shows from the Official London Theatre API.</p>
                <p>Once clicked wait for confirmation of succesful import or update</p>
                <p><strong>Example:</strong> Import complete. Created: 0, Updated: 154, Skipped: 0</p> 

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field(self::NONCE_ACTION); ?>
                    <input type="hidden" name="action" value="farlo_run_show_import" />
                    <?php submit_button('Run Import', 'primary'); ?>
                </form>
            </div>
		<?php
	}

    // Run import function, create confirmation message
	public static function handle_run_import(): void {
		if (!current_user_can(self::CAPABILITY)) {
			wp_die('You do not have permission to run this import.');
		}

		check_admin_referer(self::NONCE_ACTION);

		try {
			$result = Farlo_Show_Importer::run();
			$status = 'success';
			$message = sprintf('Import complete. Created: %d, Updated: %d, Skipped: %d', $result['created'], $result['updated'], $result['skipped']);
		} catch (Throwable $e) {
			$status = 'error';
			$message = 'Import failed: ' . $e->getMessage();
		}

		wp_safe_redirect(add_query_arg([
			'page' => 'farlo-import-shows',
			'farlo_import_status' => $status,
			'farlo_import_message' => rawurlencode($message),
		], admin_url('tools.php')));
		exit;
	}
}


// CLASS - Farlo_Show_Importer
// Calls API, loops shows creating or updating a Show CPT for each, updates shows ACF fields and fetches show image
//---------------------------------------------
final class Farlo_Show_Importer {
	private const API_URL = 'https://officiallondontheatre.com/wp-json/wp/v2/show';
	private const META_REMOTE_ID = '_olt_show_id';
	private const POST_TYPE = 'shows';
    private static array $media_cache = [];

    // Fetch all the shows from the API, and collect the data from each one
	public static function run(): array {
		$shows = self::fetch_shows();
		$created = 0;
		$updated = 0;
		$skipped = 0;

		foreach ($shows as $show) {
			if (!isset($show['id'], $show['title']['rendered'])) {
				$skipped++;
				continue;
			}

			$remote_id = (int) $show['id'];
			$post_id = self::find_existing_post_id($remote_id);

			$post_data = [
				'post_type' => self::POST_TYPE,
				'post_status' => 'publish',
				'post_title' => wp_strip_all_tags((string) $show['title']['rendered']),
				'post_content' => isset($show['content']['rendered']) ? (string) $show['content']['rendered'] : '',
			];

			if ($post_id) {
                $post_data['ID'] = $post_id;

                $result = wp_update_post($post_data, true);

                if ($result instanceof WP_Error) {
                    $skipped++;
                    continue;
                }
                $updated++;
            } else {
                $new_id = wp_insert_post($post_data, true);

                if ($new_id instanceof WP_Error) {
                    $skipped++;
                    continue;
                }

                $post_id = (int) $new_id;
                add_post_meta($post_id, self::META_REMOTE_ID, $remote_id, true);
                $created++;
            }


			if (function_exists('update_field') && isset($show['acf']) && is_array($show['acf'])) {
                $acf = $show['acf'];

                // Dates
                if (!empty($acf['show_opening_night'])) {
                    update_field('start_date', (string) $acf['show_opening_night'], $post_id);
                }

                // End date
                $end_date = '';

                if (!empty($acf['show_booking_until'])) {
                    $end_date = (string) $acf['show_booking_until'];
                } elseif (!empty($acf['show_closing_night'])) {
                    $end_date = (string) $acf['show_closing_night'];
                }

                if ($end_date !== '') {
                    update_field('end_date', $end_date, $post_id);
                }

                // Booking link: take the first ticket URL
                $booking_url = '';

                if (!empty($acf['show_ticket_urls']) && is_array($acf['show_ticket_urls'])) {
                    foreach ($acf['show_ticket_urls'] as $ticket) {
                        if (!empty($ticket['show_ticket_url'])) {
                            $booking_url = (string) $ticket['show_ticket_url'];
                            break;
                        }
                    }
                }

                if ($booking_url !== '') {
                    update_field('booking_link', esc_url_raw($booking_url), $post_id);
                }

                // Min price - Set as float data type
                if (isset($acf['minimum_price']) && $acf['minimum_price'] !== '' && $acf['minimum_price'] !== null) {
                    update_field('min_price', (float) $acf['minimum_price'], $post_id);
                }
            }

            // Featured image - we are only collecting the URL not storing the actual image
            $featured_media_id = isset($show['featured_media']) ? (int) $show['featured_media'] : 0;

            if ($featured_media_id > 0 && function_exists('update_field')) {
                $image_url = self::get_media_source_url($featured_media_id);

                if ($image_url !== '') {
                    update_field('show_image', esc_url_raw($image_url), $post_id);
                }
            }


		}

		return compact('created', 'updated', 'skipped');
	}

    // Makes sure to fetch all the pages of shows in the API
	private static function fetch_shows(): array {
        $all = [];
        $page = 1;
        $per_page = 100; // WP REST max is often 100

        while (true) {
            $url = add_query_arg([
                'per_page' => $per_page,
                'page'     => $page,
            ], self::API_URL);

            $response = wp_remote_get($url, ['timeout' => 20]);

            if (is_wp_error($response)) {
                throw new RuntimeException($response->get_error_message());
            }

            $code = (int) wp_remote_retrieve_response_code($response);

            // Check for 400 error
            if ($code === 400) {
                break;
            }

            // Check for 200 error
            if ($code !== 200) {
                throw new RuntimeException('API request failed with HTTP ' . $code);
            }

            $body = (string) wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (!is_array($data) || empty($data)) {
                break;
            }

            $all = array_merge($all, $data);

            // Simple test to see if there are anymore posts
            if (count($data) < $per_page) {
                break;
            }

            $page++;
        }

        return $all;
    }

    // Uses the media ID to get the shows image URL
    private static function get_media_source_url(int $media_id): string {
        if ($media_id <= 0) {
            return '';
        }

        // In-memory cache for this import run
        if (isset(self::$media_cache[$media_id])) {
            return self::$media_cache[$media_id];
        }

        $url = 'https://officiallondontheatre.com/wp-json/wp/v2/media/' . $media_id;
        $response = wp_remote_get($url, ['timeout' => 20]);

        if (is_wp_error($response)) {
            self::$media_cache[$media_id] = '';
            return '';
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        if ($code !== 200) {
            self::$media_cache[$media_id] = '';
            return '';
        }

        $body = (string) wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        $source_url = '';
        if (is_array($data) && !empty($data['source_url'])) {
            $source_url = (string) $data['source_url'];
        }

        self::$media_cache[$media_id] = $source_url;

        return $source_url;
    }

    // Stops shows from duplicating bychecking post ID
	private static function find_existing_post_id(int $remote_id): int {
		$posts = get_posts([
			'post_type' => self::POST_TYPE,
			'post_status' => 'any',
			'fields' => 'ids',
			'meta_key' => self::META_REMOTE_ID,
			'meta_value' => (string) $remote_id,
			'posts_per_page' => 1,
			'no_found_rows' => true,
		]);

		return !empty($posts) ? (int) $posts[0] : 0;
	}
}

// init
Farlo_Show_Importer_Plugin::init();

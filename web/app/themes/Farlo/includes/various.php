<?php

declare(strict_types=1);

// DISABLE GUTENBERG FROM CPT
add_filter('use_block_editor_for_post_type', 'disable_gutenberg', 10, 2);

function disable_gutenberg($current_status, $post_type)
{
    if (
        $post_type === 'shows'
    ) {
        return false;
    }
    return $current_status;
}

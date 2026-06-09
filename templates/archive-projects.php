<?php

namespace TSJIPPY\PROJECTS;

use TSJIPPY;

/**
 * The layout specific for the page with the slug 'projects' i.e. org/projects.
 * Displays all the post of the project type
 *
 */
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $wp_query;

$skipWrapper    = false;
if ($wp_query->is_embed) {
    $skipWrapper    = true;
}

wp_enqueue_style('tsjippy_taxonomy_style');

if ($skipWrapper) {
    displayProjectArchive();
} else {
    if (!isset($skipHeader) || !$skipHeader) {
        get_header();
    }

    wp_enqueue_style('tsjippy_template');

?>
    <div id="primary">
        <main id="main" class='inside-article'>
            <?php displayProjectArchive(); ?>
        </main>
    </div>
    <?php
    get_sidebar();

    if (!isset($skipFooter) || !$skipFooter) {
        get_footer();
    }
}

function displayProjectArchive()
{
    //Variable containing the current projects page we are on
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    $query = new \WP_Query(
        array(
            'post_type'          => 'project',
            'post_status'        => 'publish',
            'paged'              => $paged,
            'posts_per_page'     => 10
        )
    );

    if ($query->have_posts()) {
        do_action('tsjippy_before_archive', 'project');

        while ($query->have_posts()) :
            $query->the_post();
            include(__DIR__ . '/content.php');
        endwhile;

        //Add pagination
        $totalPages = $query->max_num_pages;

        if ($totalPages > 1) {
            $currentPage = max(1, get_query_var('paged'));

            echo paginate_links(array(
                'base'         => get_pagenum_link(1) . '%_%',
                'format'     => '/page/%#%',
                'current'     => $currentPage,
                'total'     => $totalPages,
                'prev_text' => __('« prev', 'tsjippy'),
                'next_text' => __('next »', 'tsjippy'),
            ));
        }
    } else {
        //No projects to show yet
    ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="no-results not-found">
                <div class="inside-article">
                    <div class="entry-content">
                        <?php echo apply_filters('tsjippy-empty-taxonomy', 'There are no projects submitted yet. ', 'project'); ?>
                    </div>
                </div>
            </div>
        </article>
<?php
    }
}

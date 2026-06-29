<?php

namespace TSJIPPY\PROJECTS;

use TSJIPPY;

/**
 * The content of a project shared between a single post, archive or the recipes page.
 **/

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$archive    = false;
if (is_tax() || is_archive()) {
    $archive    = true;
}

$class    = '';
if (!$archive) {
    $class    = '';
}

wp_enqueue_style('tsjippy_projects_template', TSJIPPY\pathToUrl(TSJIPPY\PLUGINPATH . 'css/template.min.css'), array(), PLUGINVERSION);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="cat-card<?php if ($archive) echo ' inside-article'; ?>">

        <?php
        if ($archive) {
            $url = get_permalink(get_the_ID());
 echo the_title("<h3 class='archivetitle'><a href='$url'>", '</a></h3>');
        } else {
            do_action('tsjippy-before-content');
        }
        ?>
        <div class='entry-content<?php if ($archive) echo ' archive'; ?>'>
            <?php
            if (is_user_logged_in()) {
            ?>
                <div class='author'>
                    Shared by: <a href='<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>'><?php the_author(); ?></a>
                </div>
                <?php
                if ($archive) {
                ?>
                    <div class='picture' style='margin-top:10px;'>
                        <?php
                        the_post_thumbnail([250, 200]);
                        ?>
                    </div>
            <?php
                }
            }
            ?>

            <div class='project metas'>
                <div class='category project meta'>
                    <?php
                    $categories = wp_get_post_terms(
                        get_the_ID(),
                        'projects',
                        array(
                            'orderby'   => 'name',
                            'order'     => 'ASC',
                            'fields'    => 'id=>name'
                        )
                    );

                    //First loop over the cat to see if any parent cat needs to be removed
                    foreach ($categories as $id => $category) {
                        //Get the child categories of this category
                        $children = get_term_children($id, 'projects');

                        //Loop over the children to see if one of them is also in he cat array
                        foreach ($children as $child) {
                            if (isset($categories[$child])) {
                                unset($categories[$id]);
                                break;
                            }
                        }
                    }

                    //now loop over the array to print the categories
                    $lastKey     = array_key_last($categories);
                    foreach ($categories as $id => $category) {
                        //Only show the category if all of its subcats are not there
                        $url = get_term_link($id);
                        $category = ucfirst($category);
                        ?>
                        <a href='<?php echo esc_url($url);?>'>
                            <?php echo esc_html($category);?>
                        </a>
                        <?php
                        if ($id != $lastKey) {
                            echo ', ';
                        }
                    }
                    ?>
                </div>

                <div class='number project meta'>
                    <?php
                    $url    = TSJIPPY\pathToUrl(PLUGINPATH . 'pictures/project.png');
                    ?>
                    <img src='<?php echo esc_url($url);?>' alt='category' loading='lazy' class='project-icon'>
                    echo <?php echo esc_html(get_post_meta(get_the_ID(), 'tsjippy_number', true));?>;
                </div>
                <?php

                    $ministry = get_post_meta(get_the_ID(), 'tsjippy_ministry', true);

                    if (!empty($ministry)) {
                        ?>
                        <div class='ministry project meta'>
                            <a href='<?php echo esc_url(get_permalink($ministry));?>'>
                                <img src='<?php echo esc_url(TSJIPPY\pathToUrl(PLUGINPATH . 'pictures/ministry.png'));?>' alt='email' loading='lazy' class='project-icon'>
                                 <?php echo esc_html(get_the_title($ministry));?>
                            </a>
                            <br>
                        </div>
                        <?php
                    }

                    $manager        = get_post_meta(get_the_ID(), 'tsjippy_manager', true);

                    if (!is_array($manager)) {
                        $manager    = json_decode($manager, true);
                    }
                    ?>
                    <div class='number project meta'>
                        <?php
                        if (!empty($manager['user-id'])) {
                            ?>
                            <a href='<?php echo esc_url(get_author_posts_url($manager['user-id']));?>'>
                            <?php
                        }
                        ?>
                                <img src='<?php echo esc_url(TSJIPPY\pathToUrl(PLUGINPATH . 'pictures/manager.png'));?>' alt='manager' loading='lazy' class='project-icon'>
                                <?php echo esc_html($manager['name']);
                        if (!empty($manager['user-id'])) {
                            ?>
                            </a>
                            <?php
                        }
                        ?>
                    </div>

                    <?php
                    if (!empty($manager['tel'])) {
                        ?>
                        <div class='tel project meta'>
                            <a href='tel:<?php echo esc_url($manager['tel']);?>'>
                                <img src='<?php echo esc_url(TSJIPPY\pathToUrl(PLUGINPATH . 'pictures/tel.png'));?>' alt='telephone' loading='lazy' class='project-icon'> <?php echo esc_html($manager['tel']);?>
                            </a>
                        </div>
                        <?php
                    }

                    if (!empty($manager['email'])) {
                        ?>
                        <div class='email project meta'>
                            <a href='mailto:<?php echo esc_url($manager['email']);?>'>
                                <img src='<?php echo esc_url(TSJIPPY\pathToUrl(PLUGINPATH . 'pictures/email.png'));?>' alt='email' loading='lazy' class='project-icon'> <?php echo esc_html($manager['email']);?>
                            </a>
                        </div>
                        <?php
                    }
                    ?>

                    <div class='url project meta'>
                        <?php
                        $url        = get_post_meta(get_the_ID(), 'tsjippy_url', true);
                        if (!empty($url)) {
                            ?>
                            <a href='<?php echo esc_url($url);?>'>
                                <img src='<?php echo esc_url(TSJIPPY\pathToUrl(PLUGINPATH . 'pictures/url.png'));?>' alt='project' loading='lazy' class='project-icon'> Visit website  »
                            </a>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <div class="description project">
                    <?php
                    //Only show summary on archive pages
                    if ($archive) {
                        $excerpt =  force_balance_tags(wp_kses_post(get_the_excerpt()));
                        if (empty($excerpt)) {
                            ?>
                            <br>
                            <a href='<?php echo esc_url(get_permalink());?>'>
                                View description »
                            </a>
                            <?php
                        } else {
                            echo wp_kses_post($excerpt);
                        }
                        //Show everything including category specific content
                    } else {
                        if (empty($post->post_content)) {
                            /** @disregard */
                            echo wp_kses_post(apply_filters('tsjippy-empty-description', 'No content found... ', $post));
                        }

                        the_content();
                    }

                    wp_link_pages(
                        array(
                            'before' => '<div class="page-links">Pages:',
                            'after'  => '</div>',
                        )
                    );
                    ?>
                </div>
            </div>
        </div>
</article>
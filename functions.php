<?php
/**
 * Author: Ole Fredrik Lie
 * URL: http://olefredrik.com
 *
 * FoundationPress functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

/** Various clean up functions */
require_once( 'library/cleanup.php' );

/** Required for Foundation to work properly */
require_once( 'library/foundation.php' );

/** Format comments */
require_once( 'library/class-foundationpress-comments.php' );

/** Register all navigation menus */
require_once( 'library/navigation.php' );

/** Add menu walkers for top-bar and off-canvas */
require_once( 'library/class-foundationpress-top-bar-walker.php' );
require_once( 'library/class-foundationpress-mobile-walker.php' );

/** Create widget areas in sidebar and footer */
require_once( 'library/widget-areas.php' );

/** Return entry meta information for posts */
require_once( 'library/entry-meta.php' );

/** Enqueue scripts */
require_once( 'library/enqueue-scripts.php' );

/** Add theme support */
require_once( 'library/theme-support.php' );

/** Add Nav Options to Customer */
require_once( 'library/custom-nav.php' );

/** Change WP's sticky post class */
require_once( 'library/sticky-posts.php' );

/** Configure responsive image sizes */
require_once( 'library/responsive-images.php' );

/** Gutenberg editor support */
require_once( 'library/gutenberg.php' );

/** If your site requires protocol relative url's for theme assets, uncomment the line below */
// require_once( 'library/class-foundationpress-protocol-relative-theme-assets.php' );

if (!function_exists("get_the_archive_type")) {
    function get_the_archive_type(WP_Query $query) {
        if ($query->is_tag()) return "Tag";
        if ($query->is_category()) return "Category";
        if ($query->is_date()) return "Date";
        return "Archive";
    }
}

if (!function_exists("get_related_resources")) {
    function get_related_resources(WP_Post $post) {
        /** @var WP_Term[] $categories */
        $categories = array_reduce(get_categories(), function (array $acc, WP_Term $category) {
            return $acc + [$category->term_id => $category];
        }, []);

        $categorySlugs = array_map(function (WP_Term $category) {
            return $category->slug;
        }, $categories);

        $categoryHierarchy = array_reduce($categories, function (array $acc, WP_Term $category) {
            if (!array_key_exists($category->parent, $acc)) {
                $acc[$category->parent] = [];
            }
            if (!in_array($category->term_id, $acc[$category->parent])) {
                $acc[$category->parent][] = $category->term_id;
            }
            return $acc;
        }, []);

        $leafCategories = array_filter(array_keys($categories), function ($id) use ($categoryHierarchy) {
            return empty($categoryHierarchy[$id]);
        });

        $resourceLeafCategories = array_filter($leafCategories, function ($categoryId) use ($categories) {
            $term = $categories[$categoryId];
            while(true) {
                if ($term->slug === 'resources') {
                    return true;
                }
                if (!array_key_exists($term->parent, $categories)) {
                    break;
                }

                $term = $categories[$term->parent];
            }

            return false;
        });

        return (new WP_Query())->query([
            'cat' => implode(',', $resourceLeafCategories),
            'post__not_in' => [$post->ID],
        ]);

    }
}

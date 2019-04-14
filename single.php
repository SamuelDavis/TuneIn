<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

get_header(); ?>

<?php get_template_part('template-parts/featured-image'); ?>
    <div class="main-container">
        <div class="main-grid">
            <main class="main-content">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/content', ''); ?>
                    <?php if ($related_resources = get_related_resources($post)): ?>
                    <h5>Related Resources</h5>
                    <ul>
                        <?php foreach ($related_resources as $post): ?>
                        <li>
                            <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <?php comments_template(); ?>
                <?php endwhile; ?>
            </main>
            <?php get_sidebar(); ?>
        </div>
    </div>
<?php get_footer();

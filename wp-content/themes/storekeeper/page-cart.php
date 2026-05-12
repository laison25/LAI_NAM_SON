<?php
/**
 * Cart page without the default blog sidebar.
 *
 * @package Storekeeper
 */

get_header();
?>

<div id="primary" class="content-area aft-no-sidebar lns-commerce-page lns-cart-page">
    <main id="main" class="site-main">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('lns-commerce-shell'); ?>>
                <header class="entry-header lns-commerce-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                </header>

                <div class="entry-content lns-commerce-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </main>
</div>

<?php
get_footer();

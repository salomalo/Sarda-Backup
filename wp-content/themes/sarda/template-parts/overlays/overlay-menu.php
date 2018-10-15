<!-- Mobile Sidebar -->
<div id="main-menu" class="mobile-sidebar no-scrollbar mfp-hide">
    <!-- Logo -->
    <div class="mobile-logo-center">
        <div id="logo" class="mobile-logo">
            <?php get_template_part('template-parts/header/partials/element','logo'); ?>
        </div>
    </div>

    <div class="sidebar-menu no-scrollbar <?php if(get_theme_mod('mobile_overlay') == 'center') echo 'text-center';?>">
        <ul class="nav nav-sidebar <?php if(get_theme_mod('mobile_overlay') == 'center') echo 'nav-anim';?> nav-vertical nav-uppercase">
              <?php flatsome_header_elements('mobile_sidebar','sidebar'); ?>
        </ul>
    </div><!-- inner -->
</div><!-- #mobile-menu -->

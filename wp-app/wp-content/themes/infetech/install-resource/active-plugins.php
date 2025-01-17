<?php

require_once (INFETECH_URL.'/install-resource/class-tgm-plugin-activation.php');

add_action( 'tgmpa_register', 'infetech_register_required_plugins' );


function infetech_register_required_plugins() {
   
    $plugins = array(

        array(
            'name'                     => esc_html__('Elementor','infetech'),
            'slug'                     => 'elementor',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('Contact Form 7','infetech'),
            'slug'                     => 'contact-form-7',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('Mailchimp for wp','infetech'),
            'slug'                     => 'mailchimp-for-wp',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('CMB2','infetech'),
            'slug'                     => 'cmb2',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('Widget importer exporter','infetech'),
            'slug'                     => 'widget-importer-exporter',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('One click demo import','infetech'),
            'slug'                     => 'one-click-demo-import',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('OvaTheme Framework','infetech'),
            'slug'                     => 'ova-framework',
            'required'                 => true,
            'source'                   => get_template_directory() . '/install-resource/plugins/ova-framework.zip',
            'version'                  => '1.0.1',
        ),
        array(
            'name'                     => esc_html__('OvaTheme Project','infetech'),
            'slug'                     => 'ova-project',
            'required'                 => true,
            'source'                   => get_template_directory() . '/install-resource/plugins/ova-project.zip',
            'version'                  => '1.0.8',
        ),
        array(
            'name'                     => esc_html__('OvaTheme Team','infetech'),
            'slug'                     => 'ova-team',
            'required'                 => true,
            'source'                   => get_template_directory() . '/install-resource/plugins/ova-team.zip',
            'version'                  => '1.0.8',    
        ),
        array(
            'name'                     => esc_html__('OvaTheme Career','infetech'),
            'slug'                     => 'ova-career',
            'required'                 => true,
            'source'                   => get_template_directory() . '/install-resource/plugins/ova-career.zip',
            'version'                  => '1.0.2'  
        ),
        array(
            'name'                     => esc_html__('Revolution Slider','infetech'),
            'slug'                     => 'revslider',
            'required'                 => true,
            'source'                   => get_template_directory() . '/install-resource/plugins/revslider.zip',
            'version'                  => '6.6.16',
        ),
        array(
            'name'                     => esc_html__('WooCommerce','infetech'),
            'slug'                     => 'woocommerce',
            'required'                 => true,
        ),
        

    );

   
    $config = array(
        'id'           => 'infetech',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.   
    );

    infetech_tgmpa( $plugins, $config );
}

add_action( 'pt-ocdi/after_import', 'infetech_after_import_setup' );
function infetech_after_import_setup() {
    // Assign menus to their locations.
    $primary = get_term_by( 'name', 'Primary Menu', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
            'primary' => $primary->term_id,
        )
    );

    // Assign front page and posts page (blog page).
    $front_page_id = infetech_get_page_by_title( 'Home 1' );
    $blog_page_id  = infetech_get_page_by_title( 'Blog' );


    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );

}


add_filter( 'pt-ocdi/import_files', 'infetech_import_files' );
function infetech_import_files() {
    return array(
        array(
            'import_file_name'             => 'Demo Import',
            'categories'                   => array( 'Category 1', 'Category 2' ),
            'local_import_file'            => trailingslashit( get_template_directory() ) . 'install-resource/demo-import/demo-content.xml',
            'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'install-resource/demo-import/widgets.wie',
            'local_import_customizer_file'   => trailingslashit( get_template_directory() ) . 'install-resource/demo-import/customize.dat',
            // 'import_preview_image_url'     => 'http://demo.ovathemes.com/documentation/demo-import.jpg',
        )
    );
}

// Get page by title
if ( ! function_exists( 'infetech_get_page_by_title' ) ) {
    function infetech_get_page_by_title( $page_title, $output = OBJECT, $post_type = 'page' ) {
        global $wpdb;

        if ( is_array( $post_type ) ) {
            $post_type           = esc_sql( $post_type );
            $post_type_in_string = "'" . implode( "','", $post_type ) . "'";
            $sql                 = $wpdb->prepare(
                "
                SELECT ID
                FROM $wpdb->posts
                WHERE post_title = %s
                AND post_type IN ($post_type_in_string)
            ",
                $page_title
            );
        } else {
            $sql = $wpdb->prepare(
                "
                SELECT ID
                FROM $wpdb->posts
                WHERE post_title = %s
                AND post_type = %s
            ",
                $page_title,
                $post_type
            );
        }

        $page = $wpdb->get_var( $sql );

        if ( $page ) {
            return get_post( $page, $output );
        }

        return null;
    }
}
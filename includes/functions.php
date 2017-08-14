<?php
/**
 * Holds functions used by the Agent Evolution Profiles Plugin
 */

add_action( 'pre_get_posts', 'aeprofiles_change_sort_order' );
/**
 * Add pagination and sort by menu order for aeprofiles archives
 */
function aeprofiles_change_sort_order( $query ) {

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    
    if( $query->is_main_query() && !is_admin() && is_post_type_archive( 'aeprofiles' ) || is_tax() ) {
        $query->set( 'orderby', 'menu_order' );
        $query->set( 'order', 'ASC' );
        $query->set( 'paged', $paged );
    } 
}

add_action( 'p2p_init', 'aeprofiles_connection_types' );
/**
 * Connects aeprofiles to AgentPress listings
 */
function aeprofiles_connection_types() {

    if ( ! post_type_exists('listing') || ! post_type_exists( 'aeprofiles' ) ) {
        return;
    }
    
    p2p_register_connection_type( array(
        'name' => 'agents_to_listings',
        'from' => 'aeprofiles',
        'to' => 'listing'
    ) );
}


add_filter( 'template_include', 'aep_template_include' );
/**
 * Display based on templates in plugin, or override with same name template in theme directory
 */
function aep_template_include( $template ) {

    $post_type = 'aeprofiles';

    if ( aeprofiles_is_taxonomy_of($post_type) ) {
        if ( file_exists(get_stylesheet_directory() . '/archive-' . $post_type . '.php' ) ) {
            return get_stylesheet_directory() . '/archive-' . $post_type . '.php';
        } else {
            return dirname( __FILE__ ) . '/views/archive-' . $post_type . '.php';
        }
    }

    if ( is_post_type_archive( $post_type ) ) {
        if ( file_exists(get_stylesheet_directory() . '/archive-' . $post_type . '.php') ) {
            return $template;
        } else {
            return dirname( __FILE__ ) . '/views/archive-' . $post_type . '.php';
        }
    }

    if ( $post_type == get_post_type() ) {
        if ( file_exists(get_stylesheet_directory() . '/single-' . $post_type . '.php') ) {
            return $template;
        } else {
            return dirname( __FILE__ ) . '/views/single-' . $post_type . '.php';
        }
    }

    if ( get_post_type() == 'listing' ) {
        if ( file_exists(get_stylesheet_directory() . '/single-listing.php') ) {
            return $template;
            //return dirname( __FILE__ ) . '/views/single-listing.php';
        } else {
            return dirname( __FILE__ ) . '/views/single-listing.php';
        }
    }

    return $template;
}

function do_agent_details() {

    $output = '';

    if (genesis_get_custom_field('_agent_title') != '')
        $output .= sprintf('<p class="title" itemprop="jobTitle">%s</p>', genesis_get_custom_field('_agent_title') );
    
    if (genesis_get_custom_field('_agent_license') != '')
        $output .= sprintf('<p class="license">%s</p>', genesis_get_custom_field('_agent_license') );

    if (genesis_get_custom_field('_agent_designations') != '')
        $output .= sprintf('<p class="designations" itemprop="awards">%s</p>', genesis_get_custom_field('_agent_designations') );

    if (genesis_get_custom_field('_agent_phone') != '')
        $output .= sprintf('<p class="tel" itemprop="telephone"><span class="type">Office</span>: <span class="value">%s</span></p>', genesis_get_custom_field('_agent_phone') );

    if (genesis_get_custom_field('_agent_mobile') != '')
        $output .= sprintf('<p class="tel" itemprop="telephone"><span class="type">Cell</span>: <span class="value">%s</span></p>', genesis_get_custom_field('_agent_mobile') );

    if (genesis_get_custom_field('_agent_fax') != '')
        $output .= sprintf('<p class="tel fax" itemprop="faxNumber"><span class="type">Fax</span>: <span class="value">%s</span></p>', genesis_get_custom_field('_agent_fax') );

    if (genesis_get_custom_field('_agent_email') != '')
        $email = genesis_get_custom_field('_agent_email');
        $output .= sprintf('<p><a class="email" itemprop="email" href="mailto:%s">%s</a></p>', antispambot($email), antispambot($email) );

    if (genesis_get_custom_field('_agent_website') != '')
        $output .= sprintf('<p><a class="website" itemprop="url" href="http://%s">%s</a></p>', genesis_get_custom_field('_agent_website'), genesis_get_custom_field('_agent_website') );

    if (genesis_get_custom_field('_agent_city') != '' || genesis_get_custom_field('_agent_address') != '' || genesis_get_custom_field('_agent_state') != '' || genesis_get_custom_field('_agent_zip') != '' ) {

        $address = '<p class="adr" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';

        if (genesis_get_custom_field('_agent_address') != '') {
            $address .= '<span class="street-address" itemprop="streetAddress">' . genesis_get_custom_field('_agent_address') . '</span><br />';
        }

        if (genesis_get_custom_field('_agent_city') != '') {
            $address .= '<span class="locality" itemprop="addressLocality">' . genesis_get_custom_field('_agent_city') . '</span>, ';
        }

        if (genesis_get_custom_field('_agent_state') != '') {
            $address .= '<abbr class="region" itemprop="addressRegion">' . genesis_get_custom_field('_agent_state') . '</abbr> ';
        }

        if (genesis_get_custom_field('_agent_zip') != '') {
            $address .= '<span class="postal-code" itemprop="postalCode">' . genesis_get_custom_field('_agent_zip') . '</span>';
        }

        $address .= '</p>';

        if (genesis_get_custom_field('_agent_address') != '' || genesis_get_custom_field('_agent_city') != '' || genesis_get_custom_field('_agent_state') != '' || genesis_get_custom_field('_agent_zip') != '' ) {
            $output .= $address;
        }
    }

    return $output;
}

function do_agent_social() {

    if (genesis_get_custom_field('_agent_facebook') != '' || genesis_get_custom_field('_agent_twitter') != '' || genesis_get_custom_field('_agent_linkedin') != '' || genesis_get_custom_field('_agent_googleplus') != '' || genesis_get_custom_field('_agent_pinterest') != '' || genesis_get_custom_field('_agent_youtube') != '' || genesis_get_custom_field('_agent_instagram') != '') {

        $output = '<div class="agent-social-profiles">';

        if (genesis_get_custom_field('_agent_facebook') != '') {
            $output .= sprintf('<a class="icon-facebook" rel="me" itemprop="sameAs" href="%s" title="Facebook Profile"></a>', genesis_get_custom_field('_agent_facebook'));
        }

        if (genesis_get_custom_field('_agent_twitter') != '') {
            $output .= sprintf('<a class="icon-twitter" rel="me" itemprop="sameAs" href="%s" title="Twitter Profile"></a>', genesis_get_custom_field('_agent_twitter'));
        }

        if (genesis_get_custom_field('_agent_linkedin') != '') {
            $output .= sprintf('<a class="icon-linkedin" rel="me" itemprop="sameAs" href="%s" title="LinkedIn Profile"></a>', genesis_get_custom_field('_agent_linkedin'));
        }

        if (genesis_get_custom_field('_agent_googleplus') != '') {
            $output .= sprintf('<a class="icon-gplus" rel="me" itemprop="sameAs" href="%s" title="Google+ Profile"></a>', genesis_get_custom_field('_agent_googleplus'));
        }

        if (genesis_get_custom_field('_agent_pinterest') != '') {
            $output .= sprintf('<a class="icon-pinterest" rel="me" itemprop="sameAs" href="%s" title="Pinterest Profile"></a>', genesis_get_custom_field('_agent_pinterest'));
        }

        if (genesis_get_custom_field('_agent_youtube') != '') {
            $output .= sprintf('<a class="icon-youtube" rel="me" itemprop="sameAs" href="%s" title="YouTube Profile"></a>', genesis_get_custom_field('_agent_youtube'));
        }

        if (genesis_get_custom_field('_agent_instagram') != '') {
            $output .= sprintf('<a class="icon-instagram" rel="me" itemprop="sameAs" href="%s" title="Instagram Profile"></a>', genesis_get_custom_field('_agent_instagram'));
        }

        $output .= '</div><!-- .agent-social-profiles -->';

        return $output;
    }
}


/**
 * This function redirects the user to an admin page, and adds query args
 * to the URL string for alerts, etc.
 *
 * This is just a temporary function, until WordPress fixes add_query_arg(),
 * or Genesis 1.8 is released, whichever comes first.
 *
 */
function aep_admin_redirect( $page, $query_args = array() ) {

    if ( ! $page )
        return;

    $url = html_entity_decode( menu_page_url( $page, 0 ) );

    foreach ( (array) $query_args as $key => $value ) {
        if ( isset( $key ) && isset( $value ) ) {
            $url = add_query_arg( $key, $value, $url );
        }
    }

    wp_redirect( esc_url_raw( $url ) );

}
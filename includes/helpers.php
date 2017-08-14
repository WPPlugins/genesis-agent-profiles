<?php

/**
 * Returns true if the queried taxonomy is a taxonomy of the given post type
 */
function aeprofiles_is_taxonomy_of($post_type) {
	$taxonomies = get_object_taxonomies($post_type);
	$queried_tax = get_query_var('taxonomy');

	if ( in_array($queried_tax, $taxonomies) ) {
		return true;
	}

	return false;
}

/**
 * Returns an array of posts of connected $type
 *
 * @param string $type the connected_type
 * @return array|bool array of posts if any else false
 */
function aeprofiles_get_connected_posts_of_type($type) {

    $connected = get_posts( array(
        'connected_type'  => $type,
        'connected_items' => get_queried_object(),
        'nopaging'        => true
    ) );

    if ( empty($connected) ) {
        return false;
    }

    return $connected;
}

/**
 * Outputs markup for the connected listings on single agents
 */
function aeprofiles_connected_listings_markup() {

	echo '<h3><a name="agent-listings">My Listings</a></h3>';

	$count = 0;

	$listings = aeprofiles_get_connected_posts_of_type('agents_to_listings');

	if ( empty($listings) ) {
		echo 'No Current Listings';
		return;
	}

	global $post;

	foreach ($listings as $listing) {

		setup_postdata($listing);

		$post = $listing;

		$thumb_id = get_post_thumbnail_id();
		$thumb_url = wp_get_attachment_image_src($thumb_id, 'medium', true);

		$count++;

		if ( 4 == $count ) {
			$count = 1;
		}

		$class = ($count == 1) ? ' first' : '';

		echo '
		<div class="one-third ', $class, ' connected-listings" itemscope itemtype="http://schema.org/Offer">
			<a href="', get_permalink($listing->ID), '"><img src="', $thumb_url[0], '" alt="', get_the_title(), ' photo" class="attachment-agent-profile-photo wp-post-image" itemprop="image" /></a>
			<h4 itemprop="itemOffered"><a class="listing-title" href="', get_permalink($listing->ID), '" itemprop="url">', get_the_title($listing->ID), '</a></h4>
			<p class="listing-price"><span class="label-price">Price: </span><span itemprop="price">', get_post_meta($listing->ID, '_listing_price', true), '</span></p>
			<p class="listing-beds"><span class="label-beds">Beds: </span>', get_post_meta($listing->ID, '_listing_bedrooms', true), '</p><p class="listing-baths"><span class="label-baths">Baths: </span>', get_post_meta($listing->ID, '_listing_bathrooms', true),'</p>
		</div><!-- .connected-listings -->';
	}

	echo '<div class="clearfix"></div>';

	wp_reset_postdata();
}

/**
 * Outputs markup for the connected agents on single listings
 */
function aeprofiles_connected_agents_markup() {

	$profiles = aeprofiles_get_connected_posts_of_type('agents_to_listings');

	if ( empty($profiles) ) {
		return;
	}

	echo '<h4>Listing Presented by:</h4>';

	global $post;

	foreach ($profiles as $profile) {

		setup_postdata($profile);

		$post = $profile;
		$thumb_id = get_post_thumbnail_id();
		$thumb_url = wp_get_attachment_image_src($thumb_id, 'agent-profile-photo', true);

		echo '
		<div ', post_class('connected-agents vcard'), ' itemscope itemtype="http://schema.org/Person">
			<div class="agent-thumb"><a href="', get_permalink($profile->ID), '"><img src="', $thumb_url[0], '" alt="', get_the_title(), ' photo" class="attachment-agent-profile-photo wp-post-image alignleft" itemprop="image" /></a></div><!-- .agent-thumb -->
			<div class="agent-details"><h5><a class="fn agent-name" itemprop="name" href="', get_permalink($profile->ID), '">', get_the_title($profile->ID), '</a></h5>';
			echo do_agent_details();
			echo do_agent_social();
		echo '</div><!-- .agent-details --></div><!-- .connected-agents .vcard -->';
	}

	echo '<div class="clearfix"></div>';

	wp_reset_postdata();
}
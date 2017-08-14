<?php
remove_action( 'genesis_before_post_content', 'genesis_post_info' );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 ); // HTML5
remove_action( 'genesis_after_post_content', 'genesis_post_meta' );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' ); // HTML5
remove_action( 'genesis_after_post', 'genesis_do_author_box_single' );
remove_action( 'genesis_after_entry', 'genesis_do_author_box_single' ); // HTML5
remove_action( 'genesis_post_content' , 'genesis_do_post_content' );
remove_action( 'genesis_entry_content' , 'genesis_do_post_content' ); // HTML5
remove_action( 'genesis_after_post_content', 'agentevo_post_meta' );
remove_action( 'genesis_entry_footer', 'agentevo_post_meta' ); // HTML5
remove_action( 'genesis_after_post', 'genesis_get_comments_template' );
remove_action( 'genesis_after_entry', 'genesis_get_comments_template' ); // HTML5
remove_action( 'genesis_after_post', 'genesis_do_author_box_single' );
remove_action( 'genesis_after_entry', 'genesis_do_author_box_single', 8 ); // HTML5
add_action( 'genesis_post_content' , 'agent_post_content' );
add_action( 'genesis_entry_content' , 'agent_post_content' ); // HTML5

function agent_post_content() { ?>

	<div class="agent-wrap" itemscope itemtype="http://schema.org/Person">
		<?php
			$thumb_id = get_post_thumbnail_id();
			$thumb_url = wp_get_attachment_image_src($thumb_id, 'agent-profile-photo', true);
			echo '<img src="' . $thumb_url[0] . '" alt="' . get_the_title() . ' photo" class="attachment-agent-profile-photo wp-post-image" itemprop="image" />';
		?>
		<div class="agent-details vcard">
			<span class="fn" style="display:none;" itemprop="name"><?php the_title(); ?></span>
			<?php echo do_agent_details(); ?>
			<?php echo do_agent_social(); ?>
		</div> <!-- .agent-details -->
	</div> <!-- .agent-wrap -->

	<div class="agent-bio">
		<?php the_content(); ?>
	</div><!-- .agent-bio -->

	<?php if (function_exists('_p2p_init') && function_exists('agentpress_listings_init') || function_exists('_p2p_init') && function_exists('wp_listings_init')) {
		echo'
		<div class="connected-agent-listings">';
		aeprofiles_connected_listings_markup();
		echo '</div>';
	}

}

genesis();
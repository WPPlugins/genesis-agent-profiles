=== Genesis Agent Profiles ===
Contributors: agentevolution, davebonds, chadajohnson
Tags: real estate, agent directory, agentpress, wp listings, wplistings, genesis, genesiswp, agent
Requires at least: 3.2
Tested up to: 4.4
Stable tag: 1.2.1

This plugin creates a real estate agent directory for Genesis child themes.

== Description ==

**THIS PLUGIN IS NO LONGER SUPPORTED**

**For compatibility with any WordPress theme, use our new [IMPress Agents plugin](https://wordpress.org/plugins/impress-agents/)**

The Genesis Agent Profiles plugin uses custom post types and templates to create a real estate agent directory for Genesis child themes. Includes sidebar widget to display a featured agent.

== Installation ==

1. Upload the entire `genesis-agent-profiles` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Start entering agents into the agent directory.

= Shortcode usage = 

To display all agents:
`[agent_profiles]`

To selectively display only certain agents:
`[agent_profiles id="$post_id"]`

`$post_id` is the ID number of each agent post, separated by comma if more than one. You can find the ID by editing the agent and looking in your browser's address bar for `post=##`.

= Optional configuration =

The following steps will allow you to connect your Agent Profiles to listings added using the WP Listings or AgentPress Listings plugin.

1. Install and activate the [Posts 2 Posts plugin](http://wordpress.org/plugins/posts-to-posts/). Also make sure the either [WP Listings](http://wordpress.org/plugins/wp-listings/) or [AgentPress Listings plugin](http://wordpress.org/plugins/agentpress-listings/) is installed, activated, and listings exist.
2. Once activated each listing and each agent profile will have a "Connected..." sidebar widget on the Edit screen. Use this to connect them by clicking the plus sign next to each post you want to connect to the current post. This is reciprocal and only has to be done on one of the two post types.
3. Single Agent Profiles will automatically display connected listings. 
4. The plugin includes a single-listing.php template that will be used if no single-listing.php exists in the child theme. If your theme already includes a single-listing.php template, to display connected agents on single listings, you must do one of two things:

= Option 1 =
1. Delete the single-listing.php in your child theme and the plugin's template will be used instead.

= Option 2 = 
2. Edit your child theme's single-listing.php and include this code to display connected agents:

`add_action( 'genesis_after_post', 'aeprofiles_show_connected_agent' ); // XHTML
add_action( 'genesis_after_entry', 'aeprofiles_show_connected_agent' ); // HTML5

function aeprofiles_show_connected_agent() {
	if (function_exists('_p2p_init') && function_exists('agentpress_listings_init') || function_exists('_p2p_init') && function_exists('wp_listings_init')) {
		echo'
		<div class="connected-agents">';
		aeprofiles_connected_agents_markup();
		echo '</div>';
	}
}`

== Frequently Asked Questions ==

You'll find the [FAQ on agentevolution.com](http://www.agentevolution.com/genesis-agent-profiles-faq/).

== Screenshots ==

1. Genesis Agent Profiles all agents archive screen

2. Genesis Agent Profiles single agent screen

3. Edit single agent screen

4. All agents Dashboard screen

5. Connected Listings on Single Agent profile

6. Connected Agent Profiles on Single listing

7. Connected Listings widget on Agent Profile Edit screen

8. Connected Agent widget on Listing Edit screen

9. Register Taxonomy screen for Agent Profiles

== Changelog ==

= 1.2.1 =
* Return shortcode output instead of echo

= 1.2 =
* Improve shortcode output
* Add Genesis custom post type archive setting support to allow for unique titles and descriptions on archive pages

= 1.1.7 =
* Add shortcode to display one or more agents in a post or page
* Add View Listings link for connected listings in single agent profiles and featured agent widget

= 1.1.6 =
* Cleanup, remove unused functions

= 1.1.5 =
* Add support for connecting profiles to listings using WP Listings plugin.
* Add connected agents support for XHTML Genesis themes

= 1.1.4 =
* Improve/add/remove markup on agent archive page to include post classes and improve display
* Improve/add/remove markup for connected agents on single listings to improve display

= 1.1.3 =
* Fix for no agent profile image in featured agent widget
* Allow editable widget title when only single agent is selected

= 1.1.2 =
* Updated schema.org markup to include image for agents and connected listings

= 1.1.1 =
* Include schema.org markup to agent details and connected listings
* Improve existing microformat data markup (tel & social profiles)

= 1.1 =
* Connect Agent Profiles to Agent Press Listings using [Posts 2 Posts plugin](http://wordpress.org/plugins/posts-to-posts/)
* Display connected agent listings on single agent profile pages
* Add custom single-listing.php template to display connected agent on single listings
* Add link to Agent's listings in sidebar Featured Agent widget
* Add antispambot function to email output to prevent spambots

= 1.0.9 =
* Add ability to register taxonomies. Used to segment agents/employees (i.e. Position/Agent or Position/Broker or Location/Here Location/There)
* Add field for Cell
* Add type labels to agent details phone output
* Add zip code to agent details output
* Use pre_get_posts for ordering on archive and taxonomy pages

= 1.0.8 =
* Add YouTube and Instagram social icons

= 1.0.7 =
* Fix for icons not showing in some cases

= 1.0.6 =
* Renamed icon font to prevent conflict with plugins including the same named font

= 1.0.5 =
* Remove fixed height for agent details

= 1.0.4 =
* Remove unused function

= 1.0.3 =
* Allow widget title to be user editable when Show all agents is selected

= 1.0.2 =
* Remove author box (if enabled) on single agent profiles

= 1.0.1 =
* Fix to show all agents in backend widget select

= 1.0 =
* Public release

= 0.9.1 =
* Localize default thumbnails

= 0.9 =
* Include icon font
* Added new fields
* CSS fixes
* Genesis 2.0 compatibility

= 0.1.3 =
* Include agentevo helpers

= 0.1.2 =
* Taxonomy template include checks for archive-ae-profiles.php instead of individual taxonomy templates.
* If all the address fields are empty no address is shown

= 0.1.1 =
* Uses template_includes instead of template_redirect
* Relies on helper methods provided by agentevo framework

= 0.1.0 =
* Initial beta release
<div id="icon-options-general" class="icon32"></div>
<div class="wrap">
	<h2>Genesis Agent Profiles Settings</h2>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div id="side-info-column" class="inner-sidebar">
		<?php do_meta_boxes('aep-options', 'side', null); ?>
		</div>

        <div id="post-body">
            <div id="post-body-content" class="has-sidebar-content">
				<p>If you would like to move the plugin's CSS to your theme's css file for purposes of avoiding an additional HTTP request or for ease of customization, check the box below.</p>
				<?php

				$aep_options = get_option('plugin_ae_profiles_settings');

				if ( !isset($aep_options['stylesheet_load']) ) {
					$aep_options['stylesheet_load'] = 0;
				}

				if ($aep_options['stylesheet_load'] == 1) {
					echo '<p style="color:red; font-weight: bold;">The plugin stylesheet has been deregistered<p>';
				}

				?>
				<form action="options.php" method="post" id="aep-stylesheet-options-form">
					<?php settings_fields('aep_options'); ?>
					<?php echo '<h4><input name="plugin_ae_profiles_settings[stylesheet_load]" type="checkbox" value="1" class="code" ' . checked(1, $aep_options['stylesheet_load'], false ) . ' /> Deregister Genesis Agent Profiles CSS?</h4>'; ?>

					<?php echo '<h4>Slug of the agent directory page: <input type="text" name="plugin_ae_profiles_settings[slug]" value="' . $aep_options['slug'] . '" /></h4>'; ?>
					<p>Don't forget to <a href="../wp-admin/options-permalink.php">reset your permalinks</a> if you change the slug.</p>
					<input name="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Settings'); ?>" />
				</form>
				<br /><br />
				<h3>Your agent directory is located at <a href="<?php echo site_url('/' . $aep_options['slug'] ); ?>"><?php echo site_url('/' . $aep_options['slug']); ?></a></h3>
            </div>
        </div>
    </div>
</div>
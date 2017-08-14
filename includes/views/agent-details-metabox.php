<?php wp_nonce_field( 'aep_metabox_save', 'aep_metabox_nonce' ); ?>
<?php
$pattern = '<p><label>%s<br /><input type="text" name="aep[%s]" value="%s" size="24" /></label></p>';

echo '<div style="width: 45%; float: left">';

	foreach ( (array) $this->agent_details['col1'] as $label => $key ) {
		printf( $pattern, esc_html( $label ), $key, esc_attr( genesis_get_custom_field( $key ) ) );
	}

echo '</div>';

echo '<div style="width: 45%; float: left">';

	foreach ( (array) $this->agent_details['col2'] as $label => $key ) {
		printf( $pattern, esc_html( $label ), $key, esc_attr( genesis_get_custom_field( $key ) ) );
	}

echo '</div><br style="clear: both;" /><br /><br />';
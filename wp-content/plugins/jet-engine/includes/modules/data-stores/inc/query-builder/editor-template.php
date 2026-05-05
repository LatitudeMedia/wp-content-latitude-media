<?php
/**
 * WC product query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php esc_html_e( 'Data Store Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-select
			label="<?php esc_html_e( 'Data Store', 'jet-engine' ); ?>"
			description="<?php esc_html_e( 'Select data store to get items from.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="stores"
			size="fullwidth"
			v-model="query.store_slug"
		></cx-vui-select>
		<cx-vui-component-wrapper
			v-if="frontStores && frontStores.includes( query.store_slug )"
			:wrapper-css="[ 'fullwidth' ]"
			label="<?php  _e( 'Please note:', 'jet-engine' ); ?>"
			description="<?php _e( 'Preview for the front stores is not available in the editor. Also please note that front stores <b style=\'color:#111;\'>doesn\'t support filtering</b>.', 'jet-engine' ); ?>"
		></cx-vui-component-wrapper>
		<cx-vui-input
			v-if="! frontStores || ! frontStores.includes( query.store_slug )"
			label="<?php esc_html_e( 'Max Items to Get', 'jet-engine' ); ?>"
			description="<?php esc_html_e( 'Maximum number of items to retrieve. Leave empty to get all items from store.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="query.max_items"
		></cx-vui-input>
	</div>
</div>

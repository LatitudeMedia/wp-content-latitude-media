<?php
/**
 * WC product query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php esc_html_e( 'Relations Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-component-wrapper
			:wrapper-css="[ 'fullwidth' ]"
			label="<?php esc_attr_e( 'Note:', 'jet-engine' ); ?>"
			description="<?php echo esc_attr( sprintf(
				__( '<br>The Relation Query type is the quickest and most hassle-free way to retrieve all related items for a specific object. Use it when you need a straightforward output of connected posts or terms. <br><br>However, if you need to combine relations with additional filters (such as sorting, date ranges, or meta fields), we recommend using the corresponding query type (e.g., Posts Query or Terms Query, etc.). In that case, simply add a dynamic &quot;Related Items&quot; parameter to your query. This approach gives you full flexibility to layer any extra query arguments on top of the relationship results. <br><br>Read more: %s', 'jet-engine' ),
				'<a href="https://crocoblock.com/knowledge-base/jetengine/jetengine-how-to-create-relationships-between-posts/#create-a-query" target="_blank" rel="noopener noreferrer">How to get Related Items</a>'
			) ); ?>"
		></cx-vui-component-wrapper>
		<cx-vui-select
			label="<?php esc_html_e( 'Relation', 'jet-engine' ); ?>"
			description="<?php esc_html_e( 'Select relation to get related items for.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="relations"
			size="fullwidth"
			v-model="query.rel_id"
		></cx-vui-select>
		<cx-vui-select
			label="<?php esc_html_e( 'Items To Get', 'jet-engine' ); ?>"
			description="<?php esc_html_e( 'What items we want to retrieve - parent or child.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="[
				{
					value: 'child_object',
					label: 'Get Children Items For Fixed Parent',
				},
				{
					value: 'parent_object',
					label: 'Get Parent Items For Fixed Child',
				},
			]"
			size="fullwidth"
			v-model="query.rel_object"
		></cx-vui-select>
		<cx-vui-select
			label="<?php esc_html_e( 'Initial Object From', 'jet-engine' ); ?>"
			description="<?php esc_html_e( 'Object to get selected related items for. It`s a parent object when you want to get the children and a child object when you want to get the parents.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:options-list="sources"
			v-model="query.rel_object_from"
		></cx-vui-select>
		<cx-vui-input
			label="<?php esc_html_e( 'Variable Name', 'jet-engine' ); ?>"
			description="<?php esc_html_e( 'Name of the variable to get object ID from. Used only if "Initial Object From" is set to "Query Variable" or "Current Object Variable".', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="query.rel_object_var"
			:conditions="[
				{
					input: query.rel_object_from,
					compare: 'in',
					value: [ 'query_var', 'object_var' ],
				}
			]"
		></cx-vui-input>
		<cx-vui-input
			label="<?php esc_html_e( 'Max Items to Get', 'jet-engine' ); ?>"
			description="<?php esc_html_e( 'Maximum number of items to retrieve. Leave empty to get all related items.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="query.max_items"
		></cx-vui-input>
	</div>
</div>

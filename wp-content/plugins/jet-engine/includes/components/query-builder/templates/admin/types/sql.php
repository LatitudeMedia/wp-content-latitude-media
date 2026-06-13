<?php
/**
 * Posts query component template
 */

// phpcs:disable
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php _e( 'Custom SQL Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-switcher
			label="<?php _e( 'Advanced/AI mode', 'jet-engine' ); ?>"
			description="<?php _e( 'Enable this to reset all settings and write SQL query manually or with help of AI.', 'jet-engine' ); ?>"
			:wrapper-css="isFetchingControl( 'query_manual_query' ) ? [ 'equalwidth', 'disabled' ] : [ 'equalwidth' ]"
			name="query_advanced_mode"
			v-model="query.advanced_mode"
			@input="maybeConvertToAdvanced( 'query_manual_query' )"
		></cx-vui-switcher>
		<template v-if="!query.advanced_mode">
			<cx-vui-f-select
				label="<?php _e( 'From table', 'jet-engine' ); ?>"
				description="<?php _e( 'Select data from the given table', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="tablesList"
				size="fullwidth"
				v-model="fromTableValue"
				not-found-meassge="No matching tables found"
			></cx-vui-f-select>
			<cx-vui-switcher
				label="<?php _e( 'Use Join', 'jet-engine' ); ?>"
				description="<?php _e( 'Join data from other DB tables.', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				name="query_use_join"
				v-model="query.use_join"
			></cx-vui-switcher>
			<cx-vui-component-wrapper
				:wrapper-css="[ 'fullwidth-control' ]"
				v-if="query.use_join"
			>
				<div class="cx-vui-inner-panel query-panel">
					<div class="cx-vui-component__label"><?php _e( 'Join Tables', 'jet-engine' ); ?></div>
					<div class="cx-vui-component__desc"><?php _e( '<b>Note:</b> If you planning to use multiple joins by same table, you need to define queried columns with <b>Include columns</b>. Without this some data may be lost in the returned object.', 'jet-engine' ); ?></div>
					<cx-vui-repeater
						button-label="<?php _e( 'Add new', 'jet-engine' ); ?>"
						button-style="accent"
						button-size="mini"
						v-model="query.join_tables"
						@add-new-item="addNewField( $event, [], query.join_tables )"
					>
						<cx-vui-repeater-item
							v-for="( clause, index ) in query.join_tables"
							:collapsed="isCollapsed( clause )"
							:index="index"
							:title="getJoinTitle( clause, index )"
							@clone-item="cloneField( $event, clause._id, query.join_tables )"
							@delete-item="deleteField( $event, clause._id, query.join_tables )"
							:key="query.table + '_' + clause._id"
						>
							<cx-vui-select
								label="<?php _e( 'Join Type', 'jet-engine' ); ?>"
								description="<?php _e( 'Select join type. If not set will be used Inner Join', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:options-list="[
									{
										value: 'INNER JOIN',
										label: 'Inner Join',
									},
									{
										value: 'LEFT JOIN',
										label: 'Left Join',
									},
									{
										value: 'RIGHT JOIN',
										label: 'Right Join',
									},
									{
										value: 'OUTER JOIN',
										label: 'Outer Join',
									},
								]"
								size="fullwidth"
								:value="query.join_tables[ index ].type"
								@input="setFieldProp( clause._id, 'type', $event, query.join_tables )"
							></cx-vui-select>
							<cx-vui-f-select
								label="<?php _e( 'Join Table', 'jet-engine' ); ?>"
								description="<?php _e( 'Select DB table to get joined data from', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:options-list="tablesList"
								size="fullwidth"
								:value="getJoinTableValue( index )"
								@on-input="setJoinTableValue( index, $event )"
								not-found-meassge="No matching tables found"
							></cx-vui-f-select>
							<cx-vui-select
								label="<?php _e( 'When current table column', 'jet-engine' ); ?>"
								description="<?php _e( 'Select column from the current table to find match between two tables on value of this columns', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:options-list="getColumns( query.join_tables[ index ].table )"
								size="fullwidth"
								:value="query.join_tables[ index ].on_current"
								@input="setFieldProp( clause._id, 'on_current', $event, query.join_tables )"
							></cx-vui-select>
							<cx-vui-select
								label="<?php _e( 'Is equal to other table column', 'jet-engine' ); ?>"
								description="<?php _e( 'Select columns from the initial or previously added table to find match between two tables on value of this columns', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:groups-list="getJoinColumns( index )"
								size="fullwidth"
								:value="query.join_tables[ index ].on_base"
								@input="setFieldProp( clause._id, 'on_base', $event, query.join_tables )"
							></cx-vui-select>
						</cx-vui-repeater-item>
					</cx-vui-repeater>
				</div>
			</cx-vui-component-wrapper>
			<cx-vui-component-wrapper
				:wrapper-css="[ 'fullwidth-control' ]"
			>
				<div class="cx-vui-inner-panel query-panel">
					<div class="cx-vui-component__label"><?php _e( 'Where (query clauses)', 'jet-engine' ); ?></div>
					<cx-vui-repeater
						button-label="<?php _e( 'Add new', 'jet-engine' ); ?>"
						button-style="accent"
						button-size="mini"
						v-model="query.where"
						@add-new-item="addNewField( $event, [], query.where, newDynamicWhere )"
						:custom-actions="[
							{
								buttonLabel: '<?php _e( 'Add new group', 'jet-engine' ); ?>',
								buttonStyle: 'accent-border',
								callback: addNewWhereGroup,
							}
						]"
					>
						<cx-vui-repeater-item
							v-for="( whereClause, index ) in query.where"
							:collapsed="isCollapsed( whereClause )"
							:index="index"
							@clone-item="cloneField( $event, whereClause._id, query.where, newDynamicWhere )"
							@delete-item="deleteField( $event, whereClause._id, query.where, deleteDynamicWhere )"
							:key="whereClause._id"
						>
							<jet-engine-sql-query-field
								:field="whereClause"
								:meta-query="query.where"
								:dynamic-query="dynamicQuery.where[ whereClause._id ]"
								:available-columns="availableColumns"
								@input="setFieldData( whereClause._id, $event, query.where )"
								@dynamic-input="setDynamicWhere( whereClause._id, $event )"
							></jet-engine-sql-query-field>
						</cx-vui-repeater-item>
					</cx-vui-repeater>
				</div>
			</cx-vui-component-wrapper>
			<cx-vui-select
				v-if="1 < query.where.length"
				label="<?php _e( 'Where Relation', 'jet-engine' ); ?>"
				description="<?php _e( 'The logical relationship between where clauses', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="[
					{
						value: 'and',
						label: '<?php _e( 'And', 'jet-engine' ); ?>',
					},
					{
						value: 'or',
						label: '<?php _e( 'Or', 'jet-engine' ); ?>',
					},
				]"
				size="fullwidth"
				v-model="query.where_relation"
			></cx-vui-select>
			<cx-vui-switcher
				label="<?php _e( 'Group Results', 'jet-engine' ); ?>"
				description="<?php _e( 'Group query result by selected column', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				name="query_group_results"
				v-model="query.group_results"
			></cx-vui-switcher>
			<cx-vui-select
				label="<?php _e( 'Group By', 'jet-engine' ); ?>"
				name="query_group_by"
				description="<?php _e( 'Select column to group results by', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="availableColumns"
				v-if="query.group_results"
				size="fullwidth"
				v-model="query.group_by"
			></cx-vui-select>
			<cx-vui-component-wrapper
				:wrapper-css="[ 'fullwidth-control' ]"
			>
				<div class="cx-vui-inner-panel query-panel">
					<div class="cx-vui-component__label"><?php _e( 'Order & Order By', 'jet-engine' ); ?></div>
					<cx-vui-repeater
						button-label="<?php _e( 'Add new sorting parameter', 'jet-engine' ); ?>"
						button-style="accent"
						button-size="mini"
						v-model="query.orderby"
						@add-new-item="addNewField( $event, [], query.orderby )"
					>
						<cx-vui-repeater-item
							v-for="( order, index ) in query.orderby"
							:title="query.orderby[ index ].orderby"
							:subtitle="query.orderby[ index ].order"
							:collapsed="isCollapsed( order )"
							:index="index"
							@clone-item="cloneField( $event, order._id, query.orderby )"
							@delete-item="deleteField( $event, order._id, query.orderby )"
							:key="order._id"
						>
							<cx-vui-select
								label="<?php _e( 'Order By', 'jet-engine' ); ?>"
								description="<?php _e( 'Sort retrieved items by selected parameter', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:options-list="availableOrderByColumns"
								size="fullwidth"
								:value="query.orderby[ index ].orderby"
								@input="setFieldProp( order._id, 'orderby', $event, query.orderby )"
							></cx-vui-select>
							<cx-vui-select
								label="<?php _e( 'Order', 'jet-engine' ); ?>"
								description="<?php _e( 'Designates the ascending or descending order of the `Order By` parameter', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:options-list="[
									{
										value: 'ASC',
										label: 'From lowest to highest values (1, 2, 3; a, b, c)',
									},
									{
										value: 'DESC',
										label: 'From highest to lowest values (3, 2, 1; c, b, a)',
									},
								]"
								size="fullwidth"
								:value="query.orderby[ index ].order"
								@input="setFieldProp( order._id, 'order', $event, query.orderby )"
							></cx-vui-select>
							<cx-vui-select
								label="<?php _e( 'Type', 'jet-engine' ); ?>"
								description="<?php _e( 'Data type stored in the given column', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:options-list="dataTypes"
								size="fullwidth"
								:value="query.orderby[ index ].type"
								@input="setFieldProp( order._id, 'type', $event, query.orderby )"
							></cx-vui-select>
						</cx-vui-repeater-item>
					</cx-vui-repeater>
				</div>
			</cx-vui-component-wrapper>
			<cx-vui-input
				label="<?php _e( 'Show/Per Page Limit', 'jet-engine' ); ?>"
				description="<?php _e( 'If using with JetSmartFilters pagination - its number of returned items per page. If without pagination - its number of visible items in the listing grid. To count all possible results count, but show only N items in the grid - set N into this option and leave empty <b>Total Query Limit</b>', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth', 'has-macros' ]"
				size="fullwidth"
				v-model="query.limit_per_page"
			><jet-query-dynamic-args v-model="dynamicQuery.limit_per_page"></jet-query-dynamic-args></cx-vui-input>
			<cx-vui-input
				label="<?php _e( 'Total Query Limit', 'jet-engine' ); ?>"
				description="<?php _e( 'Maximum allowed number of queried items. If using with JetSmartFilters pagination - defines total number of found items. If without pagination - its a total number that will be shown in the Query Count dynamic tag, Listings Grid still will show number of items set in the <b>Show/Per Page Limit</b> option. To count all possible results count, but show only N items in the grid - leave this option empty and set N into <b>Show/Per Page Limit</b> option', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth', 'has-macros' ]"
				size="fullwidth"
				v-model="query.limit"
			><jet-query-dynamic-args v-model="dynamicQuery.limit"></jet-query-dynamic-args></cx-vui-input>
			<cx-vui-input
				label="<?php _e( 'Offset', 'jet-engine' ); ?>"
				description="<?php _e( 'Number of items to skip from start. <b>Note:</b> This option doesn`t work without <b>Limit</b>. If you need unlimited query results with offset, please set some extra large number into Limit option.', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth', 'has-macros' ]"
				size="fullwidth"
				v-model="query.offset"
			><jet-query-dynamic-args v-model="dynamicQuery.offset"></jet-query-dynamic-args></cx-vui-input>
			<cx-vui-f-select
				label="<?php _e( 'Include columns', 'jet-engine' ); ?>"
				name="query_post_type"
				description="<?php _e( 'Select what columns should be included into the query results. Leave empty to return all possible columns', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="availableColumns"
				:autocomplete="'autocomplete_' + randID()"
				size="fullwidth"
				:multiple="true"
				v-model="query.include_columns"
			></cx-vui-f-select>
			<cx-vui-switcher
				label="<?php _e( 'Set Column Aliases', 'jet-engine' ); ?>"
				description="<?php _e( 'You may set aliases for some columns - names under which they will appear in the results. Be sure not to override existing column names.', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				name="query_group_results"
				v-model="query.set_column_aliases"
			></cx-vui-switcher>
			<cx-vui-component-wrapper
				:wrapper-css="[ 'fullwidth-control' ]"
				v-if="query.set_column_aliases"
			>
				<div class="cx-vui-inner-panel query-panel">
					<div class="cx-vui-component__label"><?php _e( 'Column Aliases', 'jet-engine' ); ?></div>
					<cx-vui-repeater
						button-label="<?php _e( 'Add new', 'jet-engine' ); ?>"
						button-style="accent"
						button-size="mini"
						v-model="query.calc_cols"
						@add-new-item="addNewField( $event, [], query.column_aliases )"
					>
						<cx-vui-repeater-item
							v-for="( colClause, index ) in query.column_aliases"
							:collapsed="isCollapsed( colClause )"
							:index="index"
							@clone-item="cloneField( $event, colClause._id, query.column_aliases )"
							@delete-item="deleteField( $event, colClause._id, query.column_aliases )"
							:key="colClause._id"
						>
						<div style="display: grid; grid-template-columns: 1fr 2fr;">
							<cx-vui-select
								label="<?php _e( 'Column', 'jet-engine' ); ?>"
								:wrapper-css="[ 'in-row' ]"
								:options-list="availableColumnsForAlias"
								size="fullwidth"
								:value="query.column_aliases[ index ].column"
								@input="setFieldProp( colClause._id, 'column', $event, query.column_aliases )"
							></cx-vui-select>
							<cx-vui-input
								label="<?php _e( 'Alias', 'jet-engine' ); ?>"
								:wrapper-css="[ 'in-row' ]"
								size="fullwidth"
								:value="query.column_aliases[ index ].column_alias"
								@input="setFieldProp( colClause._id, 'column_alias', $event, query.column_aliases )"
							></cx-vui-input>
						</div>
						</cx-vui-repeater-item>
					</cx-vui-repeater>
				</div>
			</cx-vui-component-wrapper>
			<cx-vui-switcher
				label="<?php _e( 'Include Calculated Columns', 'jet-engine' ); ?>"
				description="<?php _e( 'Add columns with calculated results. Such columns could be usable when you grouping query results by some column.', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				name="query_group_results"
				v-model="query.include_calc"
			></cx-vui-switcher>
			<cx-vui-component-wrapper
				:wrapper-css="[ 'fullwidth-control' ]"
				v-if="query.include_calc"
			>
				<div class="cx-vui-inner-panel query-panel">
					<div class="cx-vui-component__label"><?php _e( 'Calculated Columns', 'jet-engine' ); ?></div>
					<cx-vui-repeater
						button-label="<?php _e( 'Add new', 'jet-engine' ); ?>"
						button-style="accent"
						button-size="mini"
						v-model="query.calc_cols"
						@add-new-item="addNewField( $event, [], query.calc_cols )"
					>
						<cx-vui-repeater-item
							v-for="( colClause, index ) in query.calc_cols"
							:collapsed="isCollapsed( colClause )"
							:index="index"
							@clone-item="cloneField( $event, colClause._id, query.calc_cols )"
							@delete-item="deleteField( $event, colClause._id, query.calc_cols )"
							:key="colClause._id"
						>
							<cx-vui-select
								label="<?php _e( 'Column', 'jet-engine' ); ?>"
								description="<?php _e( 'Select column to calculate value by', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:options-list="availableColumns"
								size="fullwidth"
								:value="query.calc_cols[ index ].column"
								@input="setFieldProp( colClause._id, 'column', $event, query.calc_cols )"
							></cx-vui-select>
							<cx-vui-select
								label="<?php _e( 'Function', 'jet-engine' ); ?>"
								description="<?php _e( 'Calculation function', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								:options-list="[
									{
										value: 'COUNT',
										label: 'COUNT',
									},
									{
										value: 'MAX',
										label: 'MAX',
									},
									{
										value: 'MIN',
										label: 'MIN',
									},
									{
										value: 'SUM',
										label: 'SUM',
									},
									{
										value: 'AVG',
										label: 'AVG',
									},
									{
										value: 'custom',
										label: 'Custom',
									}
								]"
								size="fullwidth"
								:value="query.calc_cols[ index ].function"
								@input="setFieldProp( colClause._id, 'function', $event, query.calc_cols )"
							></cx-vui-select>
							<cx-vui-textarea
								v-if="'custom' === query.calc_cols[ index ].function"
								label="<?php _e( 'Define custom column', 'jet-engine' ); ?>"
								description="<?php _e( 'SQL definition of custom calculated column. Use %1$s to pass selected table column name. Also you can use macros as part of custom column definition.', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								size="fullwidth"
								:value="query.calc_cols[ index ].custom_col"
								@input="setFieldProp( colClause._id, 'custom_col', $event, query.calc_cols )"
								rows="3"
							></cx-vui-textarea>
							<cx-vui-input
								label="<?php _e( 'Alias', 'jet-engine' ); ?>"
								description="<?php _e( 'You may set an alias for the column, a name under which it will appear in the results. Be sure not to set it to the already existing column, as it will then override its value.', 'jet-engine' ); ?>"
								:wrapper-css="[ 'equalwidth' ]"
								size="fullwidth"
								:value="query.calc_cols[ index ].column_alias"
								@input="setFieldProp( colClause._id, 'column_alias', $event, query.calc_cols )"
							></cx-vui-input>
						</cx-vui-repeater-item>
					</cx-vui-repeater>
				</div>
			</cx-vui-component-wrapper>
			<cx-vui-component-wrapper
				:wrapper-css="[ 'equalwidth' ]"
				label="<?php _e( 'Columns for filters', 'jet-engine' ); ?>"
				description="<?php _e( 'Available columns list to filter with <b>JetSmartFilters</b> plugin. To filter query results by selected column, copy column name and paste it into <b>Query Variable</b> option of selected filter.', 'jet-engine' ); ?>"
			>
				<div>
					<code v-for="column in availableColumns" :style="{ display: 'inline-block', marginBottom: '2px' }">{{ column.label }}</code>
				</div>
			</cx-vui-component-wrapper>
		</template>
		<cx-vui-component-wrapper
			:wrapper-css="[ 'fullwidth' ]"
			v-if="query.advanced_mode"
			label="<?php _e( 'Please note:', 'jet-engine' ); ?>"
			description="<?php _e( 'You need to add WordPress Database Table prefix to all tables used in query. You can find it config.php file. Or you can use string {prefix} before table name and it will be automatically replaced with your actual prefix. For example <code>SELECT * FROM {prefix}posts</code>', 'jet-engine' ); ?>"
		></cx-vui-component-wrapper>
		<cx-vui-textarea
			label="<?php _e( 'SQL Query', 'jet-engine' ); ?>"
			name="query_manual_query"
			ref="query_manual_query"
			description="<?php _e( 'Write your SQL query here. You can use JetEngine macros inside you query.', 'jet-engine' ); ?>"
			:wrapper-css="isFetchingControl( 'query_manual_query' ) ? [ 'equalwidth', 'has-ai-popup', 'disabled' ] : [ 'equalwidth', 'has-ai-popup' ]"
			v-if="query.advanced_mode"
			size="fullwidth"
			rows="10"
			v-model="query.manual_query"
			v-on:hook:mounted="initEditor( 'query_manual_query', 'manual_query' )"
		>
			<jet-query-ai-popup
				v-model="query.manual_query"
				v-on:input="setEditorValue( $event, 'query_manual_query' )"
			></jet-query-ai-popup>
		</cx-vui-textarea>
		<cx-vui-textarea
			label="<?php _e( 'Count SQL Query', 'jet-engine' ); ?>"
			name="query_count_query"
			ref="query_count_query"
			description="<?php _e( 'Optional. Additional SQL query to count all available result. If not set, count will be get by initial query items count. In this case this query can`t be used with JetSmartFilters pagination', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			v-if="query.advanced_mode"
			size="fullwidth"
			rows="10"
			v-model="query.count_query"
			v-on:hook:mounted="initEditor( 'query_count_query', 'count_query' )"
		></cx-vui-textarea>
		<cx-vui-select
			label="<?php _e( 'Cast result to instance of object', 'jet-engine' ); ?>"
			description="<?php _e( 'With this option you can use query results as regular posts, users, terms queries etc.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="castObjectsList"
			size="fullwidth"
			v-model="query.cast_object_to"
		></cx-vui-select>
	</div>
</div>

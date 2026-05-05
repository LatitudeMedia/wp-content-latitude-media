<div class="jet-engine-query-builder__field">
	<div
		class="jet-engine-query-builder__fields-group"
		v-if="currentField.is_group"
	>
		<cx-vui-component-wrapper
			size="fullwidth"
		>
			<cx-vui-button
				button-style="link-accent"
				size="link"
				@click="switchPopup( true )"
			><span slot="label">
				<?php _e( 'Edit group clauses' ); ?> ({{ field.args.length }})
			</span></cx-vui-button>
		</cx-vui-component-wrapper>
		<cx-vui-select
			label="<?php _e( 'Relation', 'jet-engine' ); ?>"
			description="<?php _e( 'The logical relationship between clauses inside current group', 'jet-engine' ); ?>"
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
			:value="field.relation"
			@input="setFieldProp( 'relation', $event )"
		></cx-vui-select>
		<cx-vui-popup
			v-model="showPopup"
			cancel-label="<?php _e( 'Close', 'jet-engine' ) ?>"
			:showOk="false"
			v-if="showPopup"
			@on-cancel="switchPopup( false )"
			body-width="800px"
		>
			<cx-vui-repeater
				button-label="<?php _e( 'Add new clause', 'jet-engine' ); ?>"
				button-style="accent"
				button-size="mini"
				v-model="field.args"
				slot="content"
				@add-new-item="addNewField( $event, [], field.args, onAddNewField )"
			>
				<cx-vui-repeater-item
					v-for="( clause, index ) in field.args"
					:collapsed="isCollapsed( clause )"
					:index="index"
					@clone-item="cloneField( $event, clause._id, field.args, onAddNewField )"
					@delete-item="deleteField( $event, clause._id, field.args, onDeleteField )"
					:key="clause._id"
				>
					<jet-engine-query-meta-field
						:field="clause"
						:meta-query="field.args"
						:dynamic-query="dynamicQuery[ clause._id ]"
						@input="setFieldData( clause._id, $event, field.args )"
						@dynamic-input="setDynamicMeta( clause._id, $event )"
					></jet-engine-query-meta-field>
				</cx-vui-repeater-item>
			</cx-vui-repeater>
		</cx-vui-popup>
	</div>
	<div
		class="jet-engine-query-builder__fields"
		v-else
	>
		<cx-vui-input
			label="<?php _e( 'Field key/name', 'jet-engine' ); ?>"
			description="<?php _e( 'You can use `JetEngine meta field` macros to get the name of the field created by JetEngine', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'has-macros' ]"
			size="fullwidth"
			:value="currentField.key"
			@input="setFieldProp( 'key', $event )"
		>
			<jet-query-dynamic-args
				:value="dynamicQuery.key"
				@input="setDynamicQueryProp( 'key', $event )"
			></jet-query-dynamic-args>
		</cx-vui-input>
		<cx-vui-select
			label="<?php _e( 'Compare', 'jet-engine' ); ?>"
			description="<?php _e( 'Operator to test', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="operators"
			size="fullwidth"
			:value="currentField.compare"
			@input="setFieldProp( 'compare', $event )"
		></cx-vui-select>
		<cx-vui-input
			label="<?php _e( 'Value', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'has-macros', 'has-empty-toggle' ]"
			size="fullwidth"
			:value="currentField.value"
			@input="setFieldProp( 'value', $event )"
		>
			<jet-query-dynamic-args
				:value="dynamicQuery.value"
				@input="setDynamicQueryProp( 'value', $event )"
				@on-delete="setFieldProp( 'exclude_empty', false )"
			></jet-query-dynamic-args>
			<label class="jet-engine-exclude-empty-toggle" v-if="dynamicQuery.value">
				<input
					type="checkbox"
					:value="currentField.exclude_empty"
					:checked="currentField.exclude_empty"
					@input="setFieldProp( 'exclude_empty', $event.target.checked )"
				>
				<?php _e( 'Exclude this clause from the query if the dynamic value is empty', 'jet-engine' ) ?>
			</label>
		</cx-vui-input>
		<cx-vui-select
			label="<?php _e( 'Type', 'jet-engine' ); ?>"
			description="<?php _e( 'Data type stored in the given field', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="dataTypes"
			size="fullwidth"
			:value="currentField.type"
			@input="setFieldProp( 'type', $event )"
		></cx-vui-select>
		<cx-vui-input
			label="<?php _e( 'Clause name', 'jet-engine' ); ?>"
			description="<?php _e( 'Set the current clause name to be used as Order By parameter', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:value="currentField.clause_name"
			@input="setFieldProp( 'clause_name', $event )"
		></cx-vui-input>
	</div>
</div>
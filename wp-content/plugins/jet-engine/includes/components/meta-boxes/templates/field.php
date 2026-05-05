<div>
	<cx-vui-input
		label="<?php _e( 'Label', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Meta field label. It will be displayed on Post edit page', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.title"
		@input="setFieldProp( 'title', $event )"
		@on-input-change="preSetFieldName()"
	></cx-vui-input>
	<cx-vui-input
		label="<?php _e( 'Name/ID', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Meta field name/key/ID. Under this name field will be stored in the database. Should contain only Latin letters, numbers, `-` or `_` chars', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.name"
		:error="fieldError"
		@on-keyup="fieldError = false"
		@input="setFieldProp( 'name', $event )"
		@on-input-change="sanitizeFieldName()"
	></cx-vui-input>
	<cx-vui-select
		label="<?php _e( 'Object type', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Current meta box object type: field or layout element. To close the action of previously selected Tab or Accordion group, use the `Endpoint` option', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="getFilteredObjectTypes( [
			{
				value: 'field',
				label: '<?php _e( 'Field', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'tab',
				label: '<?php _e( 'Tab', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'accordion',
				label: '<?php _e( 'Accordion', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'endpoint',
				label: '<?php _e( 'Endpoint', 'jet-engine' ); // phpcs:ignore ?>'
			},
		] )"
		:value="field.object_type"
		@input="setFieldProp( 'object_type', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   'object_type',
				'compare': 'not_in',
				'value':   hideOptions,
			},
			{
				'input':    'object_type',
				'compare': 'not_in',
				'value':    disabledFields,
			},
		], 'object_type' )"
	></cx-vui-select>
	<cx-vui-select
		:key="field.id + '-' + typeSelectRenderKey"
		label="<?php _e( 'Field type', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Meta field type. Defines the way field to be displayed on Post edit page', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="getFilteredFieldTypes( fieldTypes )"
		:value="field.type"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'type' )"
		@input="handleFieldTypeChange( $event )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Layout', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Select tab layout. Note, layout selected on first tab in set will be automatically applied to all other tabs', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'horizontal',
				label: '<?php _e( 'Horizontal', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'vertical',
				label: '<?php _e( 'Vertical', 'jet-engine' ); // phpcs:ignore ?>'
			},
		]"
		:value="field.tab_layout"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'tab',
			}
		], 'tab_layout' )"
		@input="setFieldProp( 'tab_layout', $event )"
	></cx-vui-select>
	<cx-vui-switcher
		label="<?php _e( 'Allow Custom', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Allow \'custom\' values to be added', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.allow_custom"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio' ],
			},
			{
				'input':    'allow_custom',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':   field.options_source,
				'compare': 'not_equal',
				'value':   'taxonomy',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   'allow_custom',
				'compare': 'not_in',
				'value':   hideOptions,
			}
		], 'allow_custom' )"
		@input="setFieldProp( 'allow_custom', $event )"
	></cx-vui-switcher>
	<cx-vui-switcher
		label="<?php _e( 'Save Custom', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Save \'custom\' values to the field\'s options', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.save_custom"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio' ],
			},
			{
				'input':    'save_custom',
				'compare': 'not_in',
				'value':    disabledFields,
			},,
			{
				'input':   field.options_source,
				'compare': 'not_equal',
				'value':   'taxonomy',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.allow_custom,
				'compare': 'equal',
				'value':   true,
			},
			{
				'input':   'save_custom',
				'compare': 'not_in',
				'value':   hideOptions,
			}
		], 'save_custom' )"
		@input="setFieldProp( 'save_custom', $event )"
	></cx-vui-switcher>
	<cx-vui-select
		label="<?php _e( 'Taxonomy', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Select taxonomy terms for this field', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="taxonomiesList"
		:value="field.taxonomy"
		@input="setFieldProp( 'taxonomy', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'taxonomy',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'taxonomy' )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Appearance', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Choose control type to select taxonomy terms', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'select',
				label: '<?php _e( 'Select', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'radio',
				label: '<?php _e( 'Radio', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'checkboxes',
				label: '<?php _e( 'Checkboxes', 'jet-engine' ); // phpcs:ignore ?>'
			},
		]"
		:value="field.appearance || 'select'"
		@input="setFieldProp( 'appearance', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'taxonomy',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'appearance' )"
	></cx-vui-select>
	<?php
		/**
		 * Please note.
		 * There is one more 'check_radio_layout' option, for regular check-radio fields
		 */
	?>
	<cx-vui-select
		label="<?php _e( 'Layout', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Select layout orientation of inputs', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'vertical',
				label: '<?php _e( 'Vertical', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'horizontal',
				label: '<?php _e( 'Horizontal', 'jet-engine' ); // phpcs:ignore ?>'
			},
		]"
		:value="field.check_radio_layout"
		@input="setFieldProp( 'check_radio_layout', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'taxonomy',
			},
			{
				'input':   field.appearance || 'select',
				'compare': 'in',
				'value':   [ 'radio', 'checkboxes' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'check_radio_layout' )"
	></cx-vui-select>
	<cx-vui-switcher
		label="<?php _e( 'Multiple', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Allow selecting multiple terms (for Select appearance)', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.is_multiple"
		@input="setFieldProp( 'is_multiple', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'taxonomy',
			},
			{
				'input':   field.appearance || 'select',
				'compare': 'in',
				'value':   [ 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'is_multiple' )"
	></cx-vui-switcher>
	<cx-vui-switcher
		label="<?php _e( 'Allow Null', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Adds empty option for single selection (`Select term...` for select, `No term` for radio)', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.allow_null"
		@input="setFieldProp( 'allow_null', $event )"
		v-if="field.type === 'taxonomy' && ( ( ( field.appearance === 'select' || ! field.appearance ) && ! field.is_multiple ) || field.appearance === 'radio' ) && field.object_type === 'field'"
	></cx-vui-switcher>
	<cx-vui-component-wrapper
		v-if="field.allow_null && field.is_required && field.object_type === 'field' && field.type === 'taxonomy'"
		:wrapper-css="[ 'fullwidth' ]"
		label="<?php  _e( 'Please note:', 'jet-engine' ); ?>"
		description="<?php _e( 'You have enabled both `Allow Null` and `Required` options. This combination may lead to unexpected behavior. Please review your settings.', 'jet-engine' ); ?>"
	></cx-vui-component-wrapper>
	<cx-vui-select
		label="<?php _e( 'Return value', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Store selected term as ID, slug, or name', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'term_id',
				label: '<?php _e( 'Term ID', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'term_slug',
				label: '<?php _e( 'Term slug', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'term_name',
				label: '<?php _e( 'Term name', 'jet-engine' ); // phpcs:ignore ?>'
			},
		]"
		:value="field.return_value || 'term_id'"
		@input="setFieldProp( 'return_value', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'taxonomy',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'return_value' )"
	></cx-vui-select>
	<cx-vui-switcher
		label="<?php _e( 'Load Terms', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( '<b>Works only when field is attached to CPT.</b><br/> Prefill value from assigned post terms if meta is empty. Example: If a post has Category `News` assigned, it will be prefilled to the field value.', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.load_terms"
		@input="setFieldProp( 'load_terms', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'taxonomy',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'load_terms' )"
	></cx-vui-switcher>
	<cx-vui-switcher
		label="<?php _e( 'Save Terms', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( '<b>Works only when field is attached to CPT.</b><br/> Sync selected value with post taxonomy relationships. Example: If Category `News` is selected as the field value, it will be also saved to the post categories.', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.save_terms"
		@input="setFieldProp( 'save_terms', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'taxonomy',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'save_terms' )"
	></cx-vui-switcher>
	<cx-vui-select
		label="<?php _e( 'Order By', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Sort taxonomy terms by selected property', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'name',
				label: '<?php _e( 'Name', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'slug',
				label: '<?php _e( 'Slug', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'term_id',
				label: '<?php _e( 'Term ID', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'count',
				label: '<?php _e( 'Count', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'description',
				label: '<?php _e( 'Description', 'jet-engine' ); // phpcs:ignore ?>'
			},
		]"
		:value="field.terms_orderby || 'name'"
		@input="setFieldProp( 'terms_orderby', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'taxonomy',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'terms_orderby' )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Order', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Set sort direction', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'ASC',
				label: '<?php _e( 'ASC', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'DESC',
				label: '<?php _e( 'DESC', 'jet-engine' ); // phpcs:ignore ?>'
			},
		]"
		:value="field.terms_order || 'ASC'"
		@input="setFieldProp( 'terms_order', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'taxonomy',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'terms_order' )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Source', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Select source to get field options from', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="allowedSources"
		:value="field.options_source"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'options_source' )"
		@input="setFieldProp( 'options_source', $event )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Glossary', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Select exact glossary to get options from', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="glossariesList"
		:value="field.glossary_id"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'glossary',
			},
		], 'glossary_id' )"
		@input="setFieldProp( 'glossary_id', $event )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Query', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Select exact query to get options from', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="queriesList"
		:value="field.query_id"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'query',
			},
		], 'query_id' )"
		@input="setFieldProp( 'query_id', $event )"
	></cx-vui-select>
	<cx-vui-input
		label="<?php _e( 'Value Field', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Query result field to use as option value. If empty, we`ll try to use most obvious value. For example ID for posts.', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.query_value_field"
		@input="setFieldProp( 'query_value_field', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'query',
			},
		], 'query_value_field' )"
	></cx-vui-input>
	<cx-vui-input
		label="<?php _e( 'Label Field', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Query result field to use as option label. If empty, we`ll try to use most obvious value. For example post_title for posts.', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.query_label_field"
		@input="setFieldProp( 'query_label_field', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'query',
			},
		], 'query_label_field' )"
	></cx-vui-input>
	<cx-vui-select
		label="<?php _e( 'Taxonomy', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Select taxonomy to get options from', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="taxonomiesList"
		:value="field.taxonomy"
		@input="setFieldProp( 'taxonomy', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'taxonomy',
			},
		], 'taxonomy' )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Return value', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Store term ID, slug, or name as option value', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'term_id',
				label: '<?php _e( 'Term ID', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'term_slug',
				label: '<?php _e( 'Term slug', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'term_name',
				label: '<?php _e( 'Term name', 'jet-engine' ); // phpcs:ignore ?>'
			},
		]"
		:value="field.return_value || 'term_id'"
		@input="setFieldProp( 'return_value', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'taxonomy',
			},
		], 'return_value' )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Order By', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Sort taxonomy terms by selected property', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'name',
				label: '<?php _e( 'Name', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'slug',
				label: '<?php _e( 'Slug', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'term_id',
				label: '<?php _e( 'Term ID', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'count',
				label: '<?php _e( 'Count', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'description',
				label: '<?php _e( 'Description', 'jet-engine' ); // phpcs:ignore ?>'
			},
		]"
		:value="field.terms_orderby || 'name'"
		@input="setFieldProp( 'terms_orderby', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'taxonomy',
			},
		], 'terms_orderby' )"
	></cx-vui-select>
	<cx-vui-select
		label="<?php _e( 'Order', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Set sort direction', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'ASC',
				label: '<?php _e( 'ASC', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'DESC',
				label: '<?php _e( 'DESC', 'jet-engine' ); // phpcs:ignore ?>'
			},
		]"
		:value="field.terms_order || 'ASC'"
		@input="setFieldProp( 'terms_order', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'taxonomy',
			},
		], 'terms_order' )"
	></cx-vui-select>
	<cx-vui-textarea
		label="<?php _e( 'Bulk Options', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'One option per line. Allowed formats:<br><i>value</i> - value and label will be the same<br><i>value::label</i> - separate value and label<br><i>value::label::checked</i> - separate value and label, checked by default', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:rows="Number(8)"
		:value="field.bulk_options"
		@input="setFieldProp( 'bulk_options', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio', 'select' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'manual_bulk',
			},
		], 'bulk_options' )"
	></cx-vui-textarea>
	<cx-vui-component-wrapper
		:wrapper-css="[ 'fullwidth-control' ]"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'in',
				'value':    [ 'checkbox', 'select', 'radio' ],
			},
			{
				'input':   field.options_source,
				'compare': 'equal',
				'value':   'manual',
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'options_wrapper' )"
	>
		<div class="cx-vui-inner-panel">
			<jet-meta-field-options
				:value="field.options"
				@input="setFieldProp( 'options', $event )"
				:field="field"
			/>
		</div>
	</cx-vui-component-wrapper>
	<?php
		/**
		 * Please note.
		 * There is one more 'check_radio_layout' option, for taxonomy field
		 * (because of different visibility conditions)
		 */
	?>
	<cx-vui-select
		label="<?php _e( 'Layout', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Select layout orientation of inputs', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'vertical',
				label: '<?php _e( 'Vertical', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'horizontal',
				label: '<?php _e( 'Horizontal', 'jet-engine' ); // phpcs:ignore ?>'
			},
		]"
		:value="field.check_radio_layout"
		@input="setFieldProp( 'check_radio_layout', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'checkbox', 'radio' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'check_radio_layout' )"
	></cx-vui-select>
	<cx-vui-switcher
		label="<?php _e( 'Save as timestamp', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'If this option is enabled date will be saved in database Unix timestamp. Toggle it if you need to sort or query posts by date', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.is_timestamp"
		@input="setFieldProp( 'is_timestamp', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'in',
				'value':    [ 'date', 'datetime-local' ],
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'is_timestamp' )"
	></cx-vui-switcher>
	<cx-vui-input
		label="<?php _e( 'Placeholder', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Placeholder text', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.placeholder"
		@input="setFieldProp( 'placeholder', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'equal',
				'value':    'select',
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.is_multiple,
				'compare': 'not_equal',
				'value':   true,
			},
		], 'placeholder' )"
	></cx-vui-input>
	<cx-vui-switcher
		label="<?php _e( 'Save as array', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'If this option is enabled checked values will be stored as plain PHP array. Use this option if this meta value will be edited from front-end form', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.is_array"
		@input="setFieldProp( 'is_array', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    'is_array',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':    field.type,
				'compare': 'equal',
				'value':    'checkbox',
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'is_array' )"
	></cx-vui-switcher>
	<cx-vui-f-select
		label="<?php _e( 'Search in post types', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Select post types available to search in', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:options-list="postTypes"
		size="fullwidth"
		:multiple="true"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'posts',
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'search_post_type' )"
		:value="field.search_post_type"
		@input="setFieldProp( 'search_post_type', $event )"
	></cx-vui-f-select>
	<cx-vui-input
		label="<?php _e( 'Min characters to search', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Set a minimum number of characters that will trigger post search', 'jet-engine' ); // phpcs:ignore ?>"
		type="number"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		min="1"
		:value="field.min_input ?? 3"
		@input="setFieldProp( 'min_input', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'in',
				'value':    [ 'select', 'posts' ],
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'min_input' )"
	></cx-vui-input>
	<cx-vui-switcher
		label="<?php _e( 'Multiple', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Allow to select multiple values', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.is_multiple"
		@input="setFieldProp( 'is_multiple', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'in',
				'value':    [ 'select', 'posts' ],
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'is_multiple' )"
	></cx-vui-switcher>
	<cx-vui-input
		label="<?php _e( 'Min value', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Set a minimum value for a number field', 'jet-engine' ); // phpcs:ignore ?>"
		type="number"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.min_value"
		@input="setFieldProp( 'min_value', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'number',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'min_value' )"
	></cx-vui-input>
	<cx-vui-input
		label="<?php _e( 'Max value', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Set a maximum value for a number field', 'jet-engine' ); // phpcs:ignore ?>"
		type="number"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.max_value"
		@input="setFieldProp( 'max_value', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'number',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'max_value' )"
	></cx-vui-input>
	<cx-vui-input
		label="<?php _e( 'Step value', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Set a stepping interval for a number field', 'jet-engine' ); // phpcs:ignore ?>"
		type="number"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.step_value"
		@input="setFieldProp( 'step_value', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'number',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'step_value' )"
	></cx-vui-input>
	<cx-vui-select
		label="<?php _e( 'Value format', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Set the format of the value will be stored in the database', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: 'id',
				label: '<?php _e( 'Media ID', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'url',
				label: '<?php _e( 'Media URL', 'jet-engine' ); // phpcs:ignore ?>'
			},
			{
				value: 'both',
				label: '<?php _e( 'Array with media ID and URL', 'jet-engine' ); // phpcs:ignore ?>'
			},
		]"
		:value="field.value_format"
		@input="setFieldProp( 'value_format', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'media', 'gallery' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'value_format' )"
	></cx-vui-select>
	<cx-vui-component-wrapper
		:wrapper-css="[ 'fullwidth-control' ]"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'equal',
				'value':    'repeater',
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'repeater_wrapper' )"
	>
		<div class="cx-vui-inner-panel">
			<cx-vui-repeater
				button-label="<?php _e( 'New Repeater Field', 'jet-engine' ); // phpcs:ignore ?>"
				button-style="accent"
				button-size="mini"
				:value="field['repeater-fields']"
				@input="setFieldProp( 'repeater-fields', $event )"
				@add-new-item="addNewRepeaterField( $event )"
			>
				<cx-vui-repeater-item
					v-for="( rField, rFieldIndex ) in field['repeater-fields']"
					:title="field['repeater-fields'][ rFieldIndex ].title"
					:subtitle="field['repeater-fields'][ rFieldIndex ].name + ' (' + field['repeater-fields'][ rFieldIndex ].type + ')'"
					:collapsed="isCollapsed( rField )"
					:index="rFieldIndex"
					@clone-item="cloneRepeaterField( $event )"
					@delete-item="deleteRepeaterField( $event )"
					:key="rField.id ? rField.id : rField.id = getRandomID()"
				>
					<div
						slot="before-actions"
						v-if="showCondition( rField )"
						@click="showRepeaterConditionPopup( rFieldIndex )"
						:class="{
							'jet-engine-conditional-field': true,
							'cx-vui-repeater-item__copy': true,
							'jet-engine-conditional-field--active': hasConditions( rField ),
							'jet-engine-conditional-field--invalid': repeaterFieldConditionsInvalid( rField, field ),
						}"
					>
						<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414">
							<path d="M11.375 20.844c-1.125 0-1.875.75-1.875 1.875s.75 1.875 1.875 1.875c3.75 0 7.5 1.5 10.125 4.125.75.75 1.875.75 2.625 0s.75-1.875 0-2.625c-3.375-3.375-8.063-5.25-12.75-5.25z" fill-rule="nonzero"/>
							<path d="M53.938 21.219l-5.25-5.25c-.376-.375-.938-.563-1.313-.563-.563 0-.938.188-1.312.563-.75.75-.75 1.875 0 2.625l2.062 2.062h-4.313c-4.875 0-9.375 1.875-12.75 5.25l-9.375 9.375c-2.625 2.625-6.375 4.125-10.125 4.125-1.125 0-1.875.75-1.875 1.875s.75 1.875 1.875 1.875c4.688 0 9.375-1.875 12.75-5.25l9.375-9.375c2.813-2.625 6.375-4.125 10.125-4.125h4.313l-2.062 2.063c-.75.75-.75 1.875 0 2.625s1.875.75 2.625 0l5.25-5.25c.75-.563.75-1.875 0-2.625z" fill-rule="nonzero"/>
							<path d="M53.938 40.156l-5.25-5.25c-.376-.375-.938-.562-1.313-.562-.563 0-.938.187-1.312.562-.75.75-.75 1.875 0 2.625l2.062 2.063h-4.313c-3.75 0-7.5-1.5-10.125-4.125-.374-.375-.937-.563-1.312-.563-.563 0-.938.188-1.312.563-.75.75-.75 1.875 0 2.625 3.374 3.375 7.874 5.25 12.75 5.25h4.312l-2.063 2.062c-.75.75-.75 1.875 0 2.625s1.876.75 2.625 0l5.25-5.25c.75-.562.75-1.875 0-2.625z" fill-rule="nonzero"/>
						</svg>

						<div class="cx-vui-tooltip"><?php _e( 'Conditional Logic', 'jet-engine' ); // phpcs:ignore ?></div>
					</div>

					<cx-vui-input
						label="<?php _e( 'Label', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Repeater field label. Will be displayed on Post edit page', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:value="field['repeater-fields'][ rFieldIndex ].title"
						@input="setRepeaterFieldProp( rFieldIndex, 'title', $event )"
						@on-input-change="preSetRepeaterFieldName( rFieldIndex )"
					></cx-vui-input>
					<cx-vui-input
						label="<?php _e( 'Name', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Repeater field name/ID. Should contain only latin letters, numbers, `-` or `_` chars', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:value="field['repeater-fields'][ rFieldIndex ].name"
						@input="setRepeaterFieldProp( rFieldIndex, 'name', $event )"
						@on-input-change="sanitizeRepeaterFieldName( rFieldIndex )"
					></cx-vui-input>
					<cx-vui-select
						label="<?php _e( 'Type', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Repeater field type. Defines the way field be displayed on Post edit page', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="repeaterFieldTypes"
						:value="field['repeater-fields'][ rFieldIndex ].type"
						@input="setRepeaterFieldProp( rFieldIndex, 'type', $event )"
					></cx-vui-select>
					<cx-vui-select
						label="<?php _e( 'Source', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Select source to get field options from', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="allowedSources"
						:value="field['repeater-fields'][ rFieldIndex ].options_source"
						@input="setRepeaterFieldProp( rFieldIndex, 'options_source', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':    [ 'checkbox', 'select', 'radio' ],
							}
						]"
					></cx-vui-select>
					<cx-vui-select
						label="<?php _e( 'Glossary', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Select exact glossary to get options from', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="glossariesList"
						v-model="field['repeater-fields'][ rFieldIndex ].glossary_id"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':    [ 'checkbox', 'select', 'radio' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'glossary',
							}
						]"
					></cx-vui-select>
					<cx-vui-select
						label="<?php _e( 'Query', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Select exact query to get options from', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="queriesList"
						v-model="field['repeater-fields'][ rFieldIndex ].query_id"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'checkbox', 'radio', 'select' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'query',
							},
						]"
					></cx-vui-select>
					<cx-vui-input
						label="<?php _e( 'Value Field', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Query result field to use as option value. If empty, we`ll try to use most obvious value. For example ID for posts.', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-model="field['repeater-fields'][ rFieldIndex ].query_value_field"
						:conditions="getFilteredFieldConditions( [
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'checkbox', 'radio', 'select' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'query',
							},
						], 'query_value_field' )"
					></cx-vui-input>
					<cx-vui-input
						label="<?php _e( 'Label Field', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Query result field to use as option label. If empty, we`ll try to use most obvious value. For example post_title for posts.', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-model="field['repeater-fields'][ rFieldIndex ].query_label_field"
						:conditions="getFilteredFieldConditions( [
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'checkbox', 'radio', 'select' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'query',
							},
						], 'query_label_field' )"
					></cx-vui-input>
					<cx-vui-select
						label="<?php _e( 'Taxonomy', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Select taxonomy to get options from', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="taxonomiesList"
						:value="field['repeater-fields'][ rFieldIndex ].taxonomy"
						@input="setRepeaterFieldProp( rFieldIndex, 'taxonomy', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'checkbox', 'radio', 'select' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'taxonomy',
							},
						]"
					></cx-vui-select>
					<cx-vui-select
						label="<?php _e( 'Return value', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Store term ID, slug, or name as option value', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="[
							{
								value: 'term_id',
								label: '<?php _e( 'Term ID', 'jet-engine' ); // phpcs:ignore ?>'
							},
							{
								value: 'term_slug',
								label: '<?php _e( 'Term slug', 'jet-engine' ); // phpcs:ignore ?>'
							},
							{
								value: 'term_name',
								label: '<?php _e( 'Term name', 'jet-engine' ); // phpcs:ignore ?>'
							},
						]"
						:value="field['repeater-fields'][ rFieldIndex ].return_value || 'term_id'"
						@input="setRepeaterFieldProp( rFieldIndex, 'return_value', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'checkbox', 'radio', 'select' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'taxonomy',
							},
						]"
					></cx-vui-select>
					<cx-vui-select
						label="<?php _e( 'Order By', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Sort taxonomy terms by selected property', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="[
							{
								value: 'name',
								label: '<?php _e( 'Name', 'jet-engine' ); // phpcs:ignore ?>'
							},
							{
								value: 'slug',
								label: '<?php _e( 'Slug', 'jet-engine' ); // phpcs:ignore ?>'
							},
							{
								value: 'term_id',
								label: '<?php _e( 'Term ID', 'jet-engine' ); // phpcs:ignore ?>'
							},
							{
								value: 'count',
								label: '<?php _e( 'Count', 'jet-engine' ); // phpcs:ignore ?>'
							},
							{
								value: 'description',
								label: '<?php _e( 'Description', 'jet-engine' ); // phpcs:ignore ?>'
							},
						]"
						:value="field['repeater-fields'][ rFieldIndex ].terms_orderby || 'name'"
						@input="setRepeaterFieldProp( rFieldIndex, 'terms_orderby', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'checkbox', 'radio', 'select' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'taxonomy',
							},
						]"
					></cx-vui-select>
					<cx-vui-select
						label="<?php _e( 'Order', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Set sort direction', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="[
							{
								value: 'ASC',
								label: '<?php _e( 'ASC', 'jet-engine' ); // phpcs:ignore ?>'
							},
							{
								value: 'DESC',
								label: '<?php _e( 'DESC', 'jet-engine' ); // phpcs:ignore ?>'
							},
						]"
						:value="field['repeater-fields'][ rFieldIndex ].terms_order || 'ASC'"
						@input="setRepeaterFieldProp( rFieldIndex, 'terms_order', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'checkbox', 'radio', 'select' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'taxonomy',
							},
						]"
					></cx-vui-select>
					<cx-vui-textarea
						label="<?php _e( 'Bulk Options', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'One option per line. Allowed formats:<br><i>value</i> - value and label will be the same<br><i>value::label</i> - separate value and label<br><i>value::label::checked</i> - separate value and label, checked by default', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:rows="Number(8)"
						v-model="field['repeater-fields'][ rFieldIndex ].bulk_options"
						:conditions="getFilteredFieldConditions( [
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'checkbox', 'radio', 'select' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'manual_bulk',
							},
						], 'bulk_options' )"
					></cx-vui-textarea>
					<cx-vui-component-wrapper
						:wrapper-css="[ 'fullwidth-control' ]"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':    [ 'checkbox', 'select', 'radio' ],
							},
							{
								'input':   field['repeater-fields'][ rFieldIndex ].options_source,
								'compare': 'equal',
								'value':   'manual',
							},
						]"
					>
						<div class="cx-vui-inner-panel">
							<jet-meta-field-options
								v-model="field['repeater-fields'][ rFieldIndex ].options"
								:field="field['repeater-fields'][ rFieldIndex ]"
							/>
						</div>
					</cx-vui-component-wrapper>
					<cx-vui-select
						label="<?php _e( 'Layout', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Select layout orientation of inputs', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="[
							{
								value: 'vertical',
								label: '<?php _e( 'Vertical', 'jet-engine' ); // phpcs:ignore ?>'
							},
							{
								value: 'horizontal',
								label: '<?php _e( 'Horizontal', 'jet-engine' ); // phpcs:ignore ?>'
							},
						]"
						:value="field['repeater-fields'][ rFieldIndex ].check_radio_layout"
						@input="setRepeaterFieldProp( rFieldIndex, 'check_radio_layout', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':    [ 'checkbox', 'radio' ],
							},
						]"
					></cx-vui-select>
					<cx-vui-switcher
						label="<?php _e( 'Save as timestamp', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'If this option is enabled date will be saved in database Unix timestamp. Toggle it if you need to sort or query posts by date', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:value="field['repeater-fields'][ rFieldIndex ].is_timestamp"
						@input="setRepeaterFieldProp( rFieldIndex, 'is_timestamp', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'date', 'datetime-local' ],
							}
						]"
					></cx-vui-switcher>
					<cx-vui-input
						label="<?php _e( 'Min value', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Set a minimum value for a number field', 'jet-engine' ); // phpcs:ignore ?>"
						type="number"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:value="field['repeater-fields'][ rFieldIndex ].min_value"
						@input="setRepeaterFieldProp( rFieldIndex, 'min_value', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'number',
							}
						]"
					></cx-vui-input>
					<cx-vui-input
						label="<?php _e( 'Max value', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Set a maximum value for a number field', 'jet-engine' ); // phpcs:ignore ?>"
						type="number"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:value="field['repeater-fields'][ rFieldIndex ].max_value"
						@input="setRepeaterFieldProp( rFieldIndex, 'max_value', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'number',
							}
						]"
					></cx-vui-input>
					<cx-vui-input
						label="<?php _e( 'Step value', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Set a stepping interval for a number field', 'jet-engine' ); // phpcs:ignore ?>"
						type="number"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:value="field['repeater-fields'][ rFieldIndex ].step_value"
						@input="setRepeaterFieldProp( rFieldIndex, 'step_value', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'number',
							}
						]"
					></cx-vui-input>
					<cx-vui-f-select
						label="<?php _e( 'Search in post types', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Select post types available to search in', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:options-list="postTypes"
						size="fullwidth"
						:multiple="true"
						:value="field['repeater-fields'][ rFieldIndex ].search_post_type"
						@input="setRepeaterFieldProp( rFieldIndex, 'search_post_type', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'posts',
							},
						]"
						v-model="field['repeater-fields'][ rFieldIndex ].search_post_type"
					></cx-vui-f-select>
					<cx-vui-input
						label="<?php _e( 'Placeholder', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Placeholder text', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-model="field['repeater-fields'][ rFieldIndex ].placeholder"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'select',
							}
						]"
					></cx-vui-input>
					<cx-vui-switcher
						label="<?php _e( 'Multiple', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Allow to select multiple values', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:value="field['repeater-fields'][ rFieldIndex ].is_multiple"
						@input="setRepeaterFieldProp( rFieldIndex, 'is_multiple', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':    [ 'select', 'posts' ],
							},
						]"
					></cx-vui-switcher>
					<cx-vui-switcher
						label="<?php _e( 'Save as array', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'If this option is enabled checked values will be stored as plain PHP array. Use this option if this meta value will be edited from front-end form', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:value="field['repeater-fields'][ rFieldIndex ].is_array"
						@input="setRepeaterFieldProp( rFieldIndex, 'is_array', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'checkbox',
							},
						]"
					></cx-vui-switcher>
					<cx-vui-select
						label="<?php _e( 'Value format', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Set the format of the value will be stored in the database', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="[
							{
								value: 'id',
								label: '<?php _e( 'Media ID', 'jet-engine' ); // phpcs:ignore ?>'
							},
							{
								value: 'url',
								label: '<?php _e( 'Media URL', 'jet-engine' ); // phpcs:ignore ?>'
							},
							{
								value: 'both',
								label: '<?php _e( 'Array with media ID and URL', 'jet-engine' ); // phpcs:ignore ?>'
							},
						]"
						:value="field['repeater-fields'][ rFieldIndex ].value_format"
						@input="setRepeaterFieldProp( rFieldIndex, 'value_format', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'in',
								'value':   [ 'media', 'gallery' ],
							}
						]"
					></cx-vui-select>
					<cx-vui-switcher
						label="<?php _e( 'Alpha mode', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Toggle this option to enabled Alpha channel in Colorpicker', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						:value="field['repeater-fields'][ rFieldIndex ].alpha_mode"
						@input="setRepeaterFieldProp( rFieldIndex, 'alpha_mode', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'colorpicker',
							}
						]"
					></cx-vui-switcher>
					<cx-vui-select
						label="<?php _e( 'Icon Library', 'jet-engine' ); // phpcs:ignore ?>"
						description="<?php _e( 'Select an icon library for iconpicker field', 'jet-engine' ); // phpcs:ignore ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="iconsLibraries"
						:value="field['repeater-fields'][ rFieldIndex ].iconpicker_library"
						@input="setRepeaterFieldProp( rFieldIndex, 'iconpicker_library', $event )"
						:conditions="[
							{
								'input':   field['repeater-fields'][ rFieldIndex ].type,
								'compare': 'equal',
								'value':   'iconpicker',
							}
						]"
					></cx-vui-select>

					<?php do_action( 'jet-engine/meta-boxes/templates/fields/repeater/controls' ); ?>

				</cx-vui-repeater-item>
			</cx-vui-repeater>
		</div>
	</cx-vui-component-wrapper>

	<cx-vui-switcher
		label="<?php _e( 'Save as separate fields', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Toggle this option to store the value of each repeater field as a separate field', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.repeater_save_separate"
		@input="setFieldProp( 'repeater_save_separate', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'repeater',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   'repeater_save_separate',
				'compare': 'not_in',
				'value':   disabledFields,
			},
			{
				'input':   'repeater_save_separate',
				'compare': 'not_in',
				'value':   hideOptions,
			}
		], 'repeater_save_separate' )"
	></cx-vui-switcher>
	<cx-vui-switcher
		label="<?php _e( 'Collapsed', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Toggle this option to collapse repeater items on page load', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.repeater_collapsed"
		@input="setFieldProp( 'repeater_collapsed', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'repeater',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'repeater_collapsed' )"
	></cx-vui-switcher>
	<cx-vui-select
		label="<?php _e( 'Title Field', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Select a repeater field to show as a repeater item title', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="getRepeaterTitleFields( index )"
		:value="field.repeater_title_field"
		@input="setFieldProp( 'repeater_title_field', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'repeater',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'repeater_title_field' )"
	></cx-vui-select>
	<cx-vui-switcher
		label="<?php _e( 'Alpha mode', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Toggle this option to enabled Alpha channel in Colorpicker', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.alpha_mode"
		@input="setFieldProp( 'alpha_mode', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'colorpicker',
			}
		], 'alpha_mode' )"
	></cx-vui-switcher>
	<cx-vui-select
		label="<?php _e( 'Icon Library', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Select an icon library for iconpicker field', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="iconsLibraries"
		:value="field.iconpicker_library"
		@input="setFieldProp( 'iconpicker_library', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'equal',
				'value':   'iconpicker',
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'iconpicker_library' )"
	></cx-vui-select>
	<cx-vui-textarea
		label="<?php _e( 'Description', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Meta field description to be shown on Post edit page', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.description"
		@input="setFieldProp( 'description', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':    field.type,
				'compare': 'not_equal',
				'value':    'html',
			},
		], 'description' )"
	></cx-vui-textarea>
	<cx-vui-textarea
		label="<?php _e( 'HTML Code', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.html"
		@input="setFieldProp( 'html', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':    field.type,
				'compare': 'equal',
				'value':    'html',
			},
		], 'html' )"
	></cx-vui-textarea>
	<cx-vui-input
		label="<?php _e( 'CSS Classes', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.html_css_class"
		@input="setFieldProp( 'html_css_class', $event )"
		@on-input-change="preSetFieldName()"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':    field.type,
				'compare': 'equal',
				'value':    'html',
			},
		], 'html_css_class' )"
	></cx-vui-input>
	<cx-vui-select
		label="<?php _e( 'Field width', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Select meta field width from the dropdown list for Post edit page', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="[
			{
				value: '100%',
				label: '100%',
			},
			{
				value: '75%',
				label: '75%',
			},
			{
				value: '66.66666%',
				label: '66.6%',
			},
			{
				value: '50%',
				label: '50%',
			},
			{
				value: '33.33333%',
				label: '33.3%',
			},
			{
				value: '25%',
				label: '25%',
			},
		]"
		:value="field.width"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    'width',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'width' )"
		@input="setFieldProp( 'width', $event )"
	></cx-vui-select>
	<cx-vui-input
		label="<?php _e( 'Character limit', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Max field value length. Leave empty for no limit', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.max_length"
		@input="setFieldProp( 'max_length', $event )"
		@on-input-change="preSetFieldName( index )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'in',
				'value':    [ 'text', 'textarea' ],
			},
			{
				'input':    'max_length',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'max_length' )"
	></cx-vui-input>
	<cx-vui-input
		label="<?php _e( 'Default value', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Set default value for current field. <b>Note</b> For date field you can set any value could be processed by strtotime() function.', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:value="field.default_val"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'in',
				'value':    [ 'text', 'date', 'textarea', 'iconpicker', 'wysiwyg', 'number', 'colorpicker' ],
			},
			{
				'input':    'default_val',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'default_val' )"
		@input="setFieldProp( 'default_val', $event )"
	></cx-vui-input>
	<cx-vui-switcher
		label="<?php _e( 'On by default', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Make switcher On by default', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.on_by_default"
		@input="setFieldProp( 'on_by_default', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'equal',
				'value':   'switcher',
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'on_by_default' )"
	></cx-vui-switcher>
	<cx-vui-switcher
		label="<?php _e( 'Is required', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Toggle this option to make this field as required one', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.is_required"
		@input="setFieldProp( 'is_required', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    field.type,
				'compare': 'not_in',
				'value':    [ 'switcher', 'html' ],
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			}
		], 'is_required' )"
	></cx-vui-switcher>
	<template
		v-if="( 'checkbox' !== field.type ) || ( 'checkbox' === field.type && field.is_array )"
	>
		<cx-vui-switcher
			label="<?php _e( 'Quick edit support', 'jet-engine' ); // phpcs:ignore ?>"
			description="<?php _e( 'Toggle this option to make this field available in the Quick Edit section', 'jet-engine' ); // phpcs:ignore ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:value="field.quick_editable"
			@input="setFieldProp( 'quick_editable', $event )"
			:conditions="getFilteredFieldConditions( [
				{
					'input':    field.type,
					'compare': 'in',
					'value':    quickEditSupports,
				},
				{
					'input':    'quick_editable',
					'compare': 'not_in',
					'value':    disabledFields,
				},
				{
					'input':    field.object_type,
					'compare': 'equal',
					'value':   'field',
				},
				{
					'input':   'quick_editable',
					'compare': 'not_in',
					'value':   hideOptions,
				}
			], 'quick_editable' )"
		></cx-vui-switcher>
	</template>
	<cx-vui-switcher
		label="<?php _e( 'Revision support', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Toggle this option to make this field available in the Revisions', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.revision_support"
		@input="setFieldProp( 'revision_support', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   'revision_support',
				'compare': 'not_in',
				'value':   disabledFields,
			},
			{
				'input':   'revision_support',
				'compare': 'not_in',
				'value':   hideOptions,
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.type,
				'compare': 'not_equal',
				'value':   'html',
			},
		], 'revision_support' )"
	></cx-vui-switcher>

	<cx-vui-switcher
		label="<?php _e( 'Restrict MIME Types', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Toggle this option to set a list of MIME types allowed in this field', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.restrict_mime_types"
		@input="setFieldProp( 'restrict_mime_types', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'media', 'gallery' ],
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'restrict_mime_types' )"
	></cx-vui-switcher>

	<cx-vui-f-select
		label="<?php _e( 'Allowed MIME Types', 'jet-engine' ); // phpcs:ignore ?>"
		name="allowed_mime_types"
		description="<?php _e( 'Leave empty to allow all MIME types.', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:options-list="window.JetEngineMediaConfig.mime_types"
		size="fullwidth"
		:multiple="true"
		:value="field.allowed_mime_types"
		:conditions="getFilteredFieldConditions( [
			{
				'input':   field.type,
				'compare': 'in',
				'value':   [ 'media', 'gallery' ],
			},
			{
				'input':   field.restrict_mime_types,
				'compare': 'equal',
				'value':   true,
			},
			{
				'input':   field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
		], 'value_format' )"
		@input="setFieldProp( 'allowed_mime_types', $event )"
	></cx-vui-f-select>

	<?php do_action( 'jet-engine/meta-boxes/templates/fields/controls' ); ?>

	<cx-vui-switcher
		label="<?php _e( 'Show in Rest API', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Allow to get/update this field with WordPress Rest API', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="field.show_in_rest"
		@input="setFieldProp( 'show_in_rest', $event )"
		:conditions="getFilteredFieldConditions( [
			{
				'input':    'show_in_rest',
				'compare': 'not_in',
				'value':    disabledFields,
			},
			{
				'input':   'show_in_rest',
				'compare': 'not_in',
				'value':   hideOptions,
			},
			{
				'input':    field.object_type,
				'compare': 'equal',
				'value':   'field',
			},
			{
				'input':   field.type,
				'compare': 'not_equal',
				'value':   'html',
			},
		], 'show_in_rest' )"
	></cx-vui-switcher>
	<cx-vui-component-wrapper
		label="<?php _e( 'Conditional Logic', 'jet-engine' ); // phpcs:ignore ?>"
		description="<?php _e( 'Click on button to set meta field display rules.', 'jet-engine' ); // phpcs:ignore ?>"
		:wrapper-css="[ 'equalwidth' ]"
		v-if="showCondition( field )"
	>
		<cx-vui-button
			size="mini"
			button-style="accent-border"
			@click="showConditionPopup()"
		>
			<span
				slot="label"
				v-html="hasConditions( field ) ? '<?php _e( 'Edit conditional rules', 'jet-engine' ); // phpcs:ignore ?>' : '<?php _e( 'Set up conditional rules', 'jet-engine' ); // phpcs:ignore ?>'"
			></span>
		</cx-vui-button>
	</cx-vui-component-wrapper>
</div>

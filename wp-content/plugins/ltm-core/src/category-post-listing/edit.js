import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	CheckboxControl,
	PanelBody,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';

export default function Edit( { attributes, setAttributes, name } ) {
	const { categories, onlyThematicPageTagged, layout } = attributes;
	const blockProps = useBlockProps();

	const allCategories = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'taxonomy', 'category', {
			per_page: -1,
		} );
	}, [] );

	const currentPostType = useSelect( ( select ) => {
		const editor = select( 'core/editor' );
		return editor ? editor.getCurrentPostType() : null;
	}, [] );

	const toggleCategory = ( categoryId, checked ) => {
		setAttributes( {
			categories: checked
				? [ ...categories, categoryId ]
				: categories.filter( ( id ) => id !== categoryId ),
		} );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Category Post Listing', 'ltm' ) }>
					<p>{ __( 'Category', 'ltm' ) }</p>
					{ ( allCategories ?? [] ).map( ( category ) => (
						<CheckboxControl
							key={ category.id }
							label={ category.name }
							checked={ categories.includes( category.id ) }
							onChange={ ( checked ) =>
								toggleCategory( category.id, checked )
							}
						/>
					) ) }
					{ currentPostType === 'thematic-pages' && (
						<ToggleControl
							label={ __(
								'Only render posts tagged to show up on this thematic page',
								'ltm'
							) }
							checked={ onlyThematicPageTagged }
							onChange={ ( value ) =>
								setAttributes( {
									onlyThematicPageTagged: value,
								} )
							}
						/>
					) }
					<SelectControl
						label={ __( 'Layout', 'ltm' ) }
						value={ layout }
						options={ [
							{
								label: __( '5 Post Feature', 'ltm' ),
								value: 'five-post-feature',
							},
							{
								label: __( 'Paginated List', 'ltm' ),
								value: 'paginated-list',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { layout: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender block={ name } attributes={ attributes } />
			</div>
		</>
	);
}

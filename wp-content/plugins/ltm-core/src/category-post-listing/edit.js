import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	CheckboxControl,
	PanelBody,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';
import { preventLinkNavigationProps } from '../shared/prevent-link-navigation';

export default function Edit( { attributes, setAttributes, name } ) {
	const { categories, onlyThematicPageTagged, layout } = attributes;
	const blockProps = useBlockProps();

	// Preview page for the paginated-list pager. Links are blocked from
	// navigating in the editor, so pager clicks re-render the preview with
	// the clicked page instead.
	const [ page, setPage ] = useState( 1 );

	useEffect( () => {
		setPage( 1 );
	}, [ categories, onlyThematicPageTagged, layout ] );

	const goToPagerPage = ( event ) => {
		const link = event.target.closest( '.pager a[href]' );

		if ( link ) {
			const url = new URL( link.href, window.location.href );
			setPage( Number( url.searchParams.get( 'cpl-page' ) ) || 1 );
		}
	};

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
								label: __( '4 Post Feature', 'ltm' ),
								value: 'four-post-feature',
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
			{ /* onClick only delegates clicks from the pager's own <a> links, which are already keyboard-operable. */ }
			{ /* eslint-disable-next-line jsx-a11y/click-events-have-key-events, jsx-a11y/no-static-element-interactions */ }
			<div
				{ ...blockProps }
				{ ...preventLinkNavigationProps }
				onClick={ goToPagerPage }
			>
				<ServerSideRender
					block={ name }
					attributes={ attributes }
					urlQueryArgs={ { 'cpl-page': page } }
				/>
			</div>
		</>
	);
}

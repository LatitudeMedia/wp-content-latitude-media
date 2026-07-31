import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	ComboboxControl,
	Placeholder,
	PanelBody,
	ToggleControl,
} from '@wordpress/components';
import {
	createElement,
	Fragment,
	useEffect,
	useMemo,
	useState,
} from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';

export default function Edit( { attributes, setAttributes, name } ) {
	const { postId, isSponsored, excludeFromOtherBlocks } = attributes;
	const blockProps = useBlockProps();

	const currentPostId = useSelect( ( select ) => {
		const editor = select( 'core/editor' );
		return editor ? editor.getCurrentPostId() : 0;
	}, [] );

	const [ search, setSearch ] = useState( '' );
	const [ results, setResults ] = useState( [] );
	const [ hasSponsor, setHasSponsor ] = useState( true );
	const [ isSearching, setIsSearching ] = useState( false );
	const [ selectedTitle, setSelectedTitle ] = useState( '' );

	// Resolve the currently selected post's title so the combobox has a
	// label to show even before the user has typed a search term.
	useEffect( () => {
		if ( ! postId ) {
			setSelectedTitle( '' );
			return;
		}

		apiFetch( { path: `/wp/v2/posts/${ postId }?_fields=id,title` } )
			.then( ( post ) => setSelectedTitle( post?.title?.rendered ?? '' ) )
			.catch( () => setSelectedTitle( '' ) );
	}, [ postId ] );

	// Search for candidate posts, scoped to the page's sponsor when requested.
	useEffect( () => {
		setIsSearching( true );

		apiFetch( {
			path: addQueryArgs( '/wp/v2/featured-post/search', {
				search,
				sponsored: isSponsored ? 1 : 0,
				page_id: currentPostId,
			} ),
		} )
			.then( ( response ) => {
				setResults( response.items ?? [] );
				setHasSponsor( response.hasSponsor ?? true );
				setIsSearching( false );
			} )
			.catch( () => {
				setResults( [] );
				setIsSearching( false );
			} );
	}, [ search, isSponsored, currentPostId ] );

	const options = useMemo( () => {
		const opts = results.map( ( post ) => ( {
			value: String( post.id ),
			label: post.title,
		} ) );

		if (
			postId &&
			selectedTitle &&
			! opts.some( ( option ) => option.value === String( postId ) )
		) {
			opts.unshift( { value: String( postId ), label: selectedTitle } );
		}

		return opts;
	}, [ results, postId, selectedTitle ] );

	let comboHelp = __( 'Search by post title.', 'ltm' );
	if ( isSearching ) {
		comboHelp = __( 'Searching…', 'ltm' );
	} else if ( isSponsored && ! hasSponsor ) {
		comboHelp = __(
			'This page has no sponsor assigned, so no posts are available.',
			'ltm'
		);
	} else if ( isSponsored ) {
		comboHelp = __( "Showing posts from this page's sponsor.", 'ltm' );
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Featured Post', 'ltm' ) }>
					<ToggleControl
						label={ __( 'Is from sponsor?', 'ltm' ) }
						checked={ isSponsored }
						onChange={ ( value ) =>
							setAttributes( { isSponsored: value } )
						}
					/>
					<ComboboxControl
						label={ __( 'Post', 'ltm' ) }
						value={ postId ? String( postId ) : null }
						options={ options }
						onFilterValueChange={ setSearch }
						onChange={ ( value ) =>
							setAttributes( {
								postId: value ? Number( value ) : 0,
							} )
						}
						help={ comboHelp }
					/>
					<ToggleControl
						label={ __(
							'Stop this post from showing up in other blocks on this same page?',
							'ltm'
						) }
						checked={ excludeFromOtherBlocks }
						onChange={ ( value ) =>
							setAttributes( { excludeFromOtherBlocks: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ postId ? (
					<ServerSideRender block={ name } attributes={ attributes } />
				) : (
					<Placeholder
						icon="star-filled"
						label={ __( 'Featured Post', 'ltm' ) }
						instructions={ __(
							'Select a post in the block settings to feature it here.',
							'ltm'
						) }
					/>
				) }
			</div>
		</>
	);
}

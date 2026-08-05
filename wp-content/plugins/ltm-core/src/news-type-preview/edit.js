import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';

// Mirrors the ACF "News Type" select field's choices (inc/acf/acf-export.php,
// field_670d30ec995d3) — not a taxonomy, so there's nothing to fetch over REST.
const NEWS_TYPES = [
	{ label: __( 'Analysis', 'ltm' ), value: 'analysis' },
	{ label: __( 'Explainer', 'ltm' ), value: 'explainer' },
	{ label: __( 'News', 'ltm' ), value: 'news' },
	{ label: __( 'Feature', 'ltm' ), value: 'feature' },
	{ label: __( 'Interview', 'ltm' ), value: 'interview' },
	{ label: __( 'Opinion', 'ltm' ), value: 'opinion' },
	{ label: __( 'Newsletter', 'ltm' ), value: 'newsletter' },
	{ label: __( 'Commentary', 'ltm' ), value: 'commentary' },
	{ label: __( 'Podcast', 'ltm' ), value: 'podcast' },
	{ label: __( 'Blog', 'ltm' ), value: 'blog' },
];

export default function Edit( { attributes, setAttributes, name } ) {
	const { newsType, podcastId, numberOfPosts } = attributes;
	const blockProps = useBlockProps();

	const allPodcasts = useSelect(
		( select ) => {
			if ( newsType !== 'podcast' ) {
				return null;
			}

			return select( 'core' ).getEntityRecords( 'postType', 'podcasts', {
				per_page: -1,
			} );
		},
		[ newsType ]
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'News Type Preview', 'ltm' ) }>
					<SelectControl
						label={ __( 'News Type', 'ltm' ) }
						value={ newsType }
						options={ NEWS_TYPES }
						onChange={ ( value ) =>
							setAttributes( { newsType: value } )
						}
					/>
					{ newsType === 'podcast' && (
						<SelectControl
							label={ __( 'Podcast', 'ltm' ) }
							value={ podcastId }
							options={ [
								{ label: __( 'Any podcast', 'ltm' ), value: 0 },
								...( allPodcasts ?? [] ).map( ( podcast ) => ( {
									label: podcast.title.rendered,
									value: podcast.id,
								} ) ),
							] }
							onChange={ ( value ) =>
								setAttributes( { podcastId: Number( value ) } )
							}
						/>
					) }
					<TextControl
						label={ __( 'Number of posts', 'ltm' ) }
						type="number"
						min={ 1 }
						value={ numberOfPosts }
						onChange={ ( value ) =>
							setAttributes( { numberOfPosts: Number( value ) } )
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

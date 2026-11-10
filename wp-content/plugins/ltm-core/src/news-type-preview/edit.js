import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	BaseControl,
	Button,
	CheckboxControl,
	PanelBody,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';
import { preventLinkNavigationProps } from '../shared/prevent-link-navigation';

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
	// Not an ACF choice — selects posts from the `events` CPT instead.
	{ label: __( 'Events', 'ltm' ), value: 'events' },
];

export default function Edit( { attributes, setAttributes, name } ) {
	const {
		newsType,
		newsTypes,
		podcastId,
		numberOfPosts,
		defaultImageId,
		defaultImageUrl,
	} = attributes;
	const blockProps = useBlockProps();
	// Blocks saved before multiselect only have the legacy `newsType` string.
	const selected = newsTypes ?? [ newsType ];
	const hasPodcast = selected.includes( 'podcast' );
	const hasEvents = selected.includes( 'events' );

	const toggleType = ( value, checked ) =>
		setAttributes( {
			newsTypes: checked
				? [ ...selected, value ]
				: selected.filter( ( type ) => type !== value ),
		} );

	const allPodcasts = useSelect(
		( select ) => {
			if ( ! hasPodcast ) {
				return null;
			}

			return select( 'core' ).getEntityRecords( 'postType', 'podcasts', {
				per_page: -1,
			} );
		},
		[ hasPodcast ]
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'News Type Preview', 'ltm' ) }>
					<fieldset style={ { marginBottom: '15px' } }>
						<BaseControl.VisualLabel as="legend">
							{ __( 'News Types', 'ltm' ) }
						</BaseControl.VisualLabel>
						{ NEWS_TYPES.map( ( { label, value } ) => (
							<CheckboxControl
								key={ value }
								label={ label }
								checked={ selected.includes( value ) }
								onChange={ ( checked ) =>
									toggleType( value, checked )
								}
								__nextHasNoMarginBottom
							/>
						) ) }
						{ hasEvents && (
							<BaseControl
								id="ltm-news-type-preview-default-image"
								label={ __(
									'Choose Default Featured Image',
									'ltm'
								) }
								help={ __(
									'Some events do not have featured images. Choose what image to use if no featured image exists.',
									'ltm'
								) }
							>
								<MediaUploadCheck>
									<MediaUpload
										onSelect={ ( media ) =>
											setAttributes( {
												defaultImageId: media.id,
												defaultImageUrl: media.url,
											} )
										}
										allowedTypes={ [ 'image' ] }
										value={ defaultImageId }
										render={ ( { open } ) => (
											<Button
												variant="secondary"
												onClick={ open }
											>
												{ defaultImageUrl
													? __(
															'Replace Default Featured Image',
															'ltm'
														)
													: __(
															'Set Default Featured Image',
															'ltm'
														) }
											</Button>
										) }
									/>
								</MediaUploadCheck>
								{ defaultImageUrl && (
									<Button
										variant="link"
										isDestructive
										onClick={ () =>
											setAttributes( {
												defaultImageId: 0,
												defaultImageUrl: '',
											} )
										}
									>
										{ __(
											'Remove Default Featured Image',
											'ltm'
										) }
									</Button>
								) }
							</BaseControl>
						) }
					</fieldset>
					{ hasPodcast && (
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
			<div { ...blockProps } { ...preventLinkNavigationProps }>
				<ServerSideRender block={ name } attributes={ attributes } />
			</div>
		</>
	);
}

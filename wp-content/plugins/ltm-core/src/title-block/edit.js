import {
	useBlockProps,
	InspectorControls,
	InnerBlocks,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	BaseControl,
	PanelBody,
	TextControl,
	SelectControl,
	Button,
	Notice,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis -- core's own inspector panels use VStack for control spacing.
	__experimentalVStack as VStack,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis -- no stable spacer component exists yet.
	__experimentalSpacer as Spacer,
} from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import metadata from './block.json';

// Editor-only fallbacks shown while a field is empty; never saved or rendered
// on the front end.
const PLACEHOLDER = metadata.example.attributes;

const TEMPLATE = [ [ 'core/post-title', { level: 1 } ] ];

export default function Edit( { attributes, setAttributes } ) {
	const {
		layout,
		kicker,
		backgroundImageId,
		backgroundImageUrl,
		sponsorLogoOverrideId,
		sponsorLogoOverrideUrl,
	} = attributes;

	const savedSponsor = useSelect( ( select ) => {
		const editor = select( 'core/editor' );
		const postType = editor && editor.getCurrentPostType();
		const postId = editor && editor.getCurrentPostId();

		if ( ! postType || ! postId ) {
			return null;
		}

		const record = select( 'core' ).getEntityRecord(
			'postType',
			postType,
			postId
		);
		return record ? record.ltm_sponsor : null;
	}, [] );

	// The "Is Sponsored By" classic meta box isn't part of the editor store, so
	// its REST field only refreshes on reload. Track the select directly so the
	// preview follows changes immediately. `undefined` = not changed yet.
	const [ selectedSponsor, setSelectedSponsor ] = useState();

	useEffect( () => {
		const onChange = ( event ) => {
			const select = event.target;

			if ( select.id !== 'ltm_sponsor' ) {
				return;
			}

			if ( ! Number( select.value ) ) {
				setSelectedSponsor( null );
				return;
			}

			const option = select.selectedOptions[ 0 ];
			setSelectedSponsor( {
				name: option.text,
				logo: option.dataset.logo || null,
			} );
		};

		// Delegated, since the meta box can render after this block mounts.
		document.addEventListener( 'change', onChange );
		return () => document.removeEventListener( 'change', onChange );
	}, [] );

	const sponsor =
		selectedSponsor !== undefined ? selectedSponsor : savedSponsor;

	// The "Unsponsored" layout shows only the title and background image.
	const isSponsored = layout !== 'unsponsored';

	const { openGeneralSidebar } = useDispatch( 'core/edit-post' );

	// Switch to the document ("Thematic Page") sidebar tab and focus the
	// classic "Is Sponsored By" meta box once it has rendered.
	const goToSponsorSetting = () => {
		openGeneralSidebar( 'edit-post/document' );
		window.setTimeout( () => {
			const select = document.getElementById( 'ltm_sponsor' );
			if ( select ) {
				select.scrollIntoView( { block: 'center' } );
				select.focus();
			}
		} );
	};

	const blockProps = useBlockProps( {
		className: `ltm-title-block ltm-title-block--${ layout }`,
		style: {
			'--ltm-title-block-bg': `url(${
				backgroundImageUrl || PLACEHOLDER.backgroundImageUrl
			})`,
		},
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Title Block', 'ltm' ) }>
					<VStack>
						{ isSponsored && (
							<Notice status="info" isDismissible={ false }>
								{ __(
									'The "Presented By" disclaimer is generated from the "Is Sponsored By" setting in the Thematic Page tab.',
									'ltm'
								) }{ ' ' }
								<Button
									variant="link"
									onClick={ goToSponsorSetting }
								>
									{ __( 'Go to setting', 'ltm' ) }
								</Button>
							</Notice>
						) }
						{ isSponsored && (
							<TextControl
								label={ __( 'Kicker', 'ltm' ) }
								help={ __(
									'Small subheader shown above the title.',
									'ltm'
								) }
								value={ kicker }
								onChange={ ( value ) =>
									setAttributes( { kicker: value } )
								}
							/>
						) }
						<BaseControl
							id="ltm-title-block-background-image"
							label={ __( 'Background Image', 'ltm' ) }
						>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ ( media ) =>
										setAttributes( {
											backgroundImageId: media.id,
											backgroundImageUrl: media.url,
										} )
									}
									allowedTypes={ [ 'image' ] }
									value={ backgroundImageId }
									render={ ( { open } ) => (
										<Button
											id="ltm-title-block-background-image"
											variant="secondary"
											onClick={ open }
											className="ltm-title-block__media-button"
										>
											{ backgroundImageUrl
												? __(
														'Replace Background Image',
														'ltm'
													)
												: __(
														'Set Background Image',
														'ltm'
													) }
										</Button>
									) }
								/>
							</MediaUploadCheck>
						</BaseControl>
						{ backgroundImageUrl && (
							<>
								<Button
									variant="link"
									isDestructive
									onClick={ () =>
										setAttributes( {
											backgroundImageId: 0,
											backgroundImageUrl: '',
										} )
									}
								>
									{ __( 'Remove Background Image', 'ltm' ) }
								</Button>
								<Spacer />
							</>
						) }
						<SelectControl
							label={ __( 'Layout', 'ltm' ) }
							value={ layout }
							options={ [
								{
									label: __( 'Sponsored Content', 'ltm' ),
									value: 'sponsored-content',
								},
								{
									label: __( 'Unsponsored', 'ltm' ),
									value: 'unsponsored',
								},
							] }
							onChange={ ( value ) =>
								setAttributes( { layout: value } )
							}
						/>
					</VStack>
				</PanelBody>
				{ isSponsored && sponsor && (
					<PanelBody title={ __( 'Sponsor', 'ltm' ) }>
						<VStack spacing="15px">
							<div>
								{ __( 'Sponsored by', 'ltm' ) }{ ' ' }
								<strong>{ sponsor.name }</strong>
							</div>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ ( media ) =>
										setAttributes( {
											sponsorLogoOverrideId: media.id,
											sponsorLogoOverrideUrl: media.url,
										} )
									}
									allowedTypes={ [ 'image' ] }
									value={ sponsorLogoOverrideId }
									render={ ( { open } ) => (
										<Button
											variant="secondary"
											onClick={ open }
											className="ltm-title-block__media-button"
										>
											{ sponsorLogoOverrideUrl
												? __(
														'Replace Sponsor Logo Override',
														'ltm'
													)
												: __(
														'Set Sponsor Logo Override',
														'ltm'
													) }
										</Button>
									) }
								/>
							</MediaUploadCheck>
							{ sponsorLogoOverrideUrl && (
								<Button
									variant="link"
									isDestructive
									onClick={ () =>
										setAttributes( {
											sponsorLogoOverrideId: 0,
											sponsorLogoOverrideUrl: '',
										} )
									}
								>
									{ __( 'Remove Override', 'ltm' ) }
								</Button>
							) }
						</VStack>
					</PanelBody>
				) }
			</InspectorControls>
			<div { ...blockProps }>
				<div className="ltm-title-block__inner">
					<div className="ltm-title-block__content">
						{ isSponsored && (
							<p
								className={ `ltm-title-block__kicker${
									kicker ? '' : ' is-placeholder'
								}` }
							>
								{ kicker || PLACEHOLDER.kicker }
							</p>
						) }
						<InnerBlocks template={ TEMPLATE } templateLock="all" />
					</div>
					{ isSponsored && sponsor && (
						<div className="ltm-title-block__sponsor">
							<span className="ltm-title-block__sponsor-label">
								{ __( 'Presented By', 'ltm' ) }
							</span>
							{ ( sponsorLogoOverrideUrl || sponsor.logo ) && (
								<img
									className="ltm-title-block__sponsor-logo"
									src={
										sponsorLogoOverrideUrl || sponsor.logo
									}
									alt={ sponsor.name }
								/>
							) }
						</div>
					) }
				</div>
			</div>
		</>
	);
}

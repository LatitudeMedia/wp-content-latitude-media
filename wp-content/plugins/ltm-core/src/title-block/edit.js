import {
	useBlockProps,
	InspectorControls,
	InnerBlocks,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, Button } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

const TEMPLATE = [ [ 'core/post-title', { level: 1 } ] ];

export default function Edit( { attributes, setAttributes } ) {
	const {
		kicker,
		backgroundImageId,
		backgroundImageUrl,
		sponsorLogoOverrideId,
		sponsorLogoOverrideUrl,
	} = attributes;

	const sponsor = useSelect( ( select ) => {
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

	const blockProps = useBlockProps( {
		className: 'ltm-title-block',
		style: backgroundImageUrl
			? { '--ltm-title-block-bg': `url(${ backgroundImageUrl })` }
			: undefined,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Title Block', 'ltm' ) }>
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
									variant="secondary"
									onClick={ open }
									className="ltm-title-block__media-button"
								>
									{ backgroundImageUrl
										? __(
												'Replace Background Image',
												'ltm'
										  )
										: __( 'Set Background Image', 'ltm' ) }
								</Button>
							) }
						/>
					</MediaUploadCheck>
					{ backgroundImageUrl && (
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
					) }
				</PanelBody>
				{ sponsor && (
					<PanelBody title={ __( 'Sponsor', 'ltm' ) }>
						<p>
							{ __( 'Sponsored by', 'ltm' ) }{ ' ' }
							<strong>{ sponsor.name }</strong>
						</p>
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
					</PanelBody>
				) }
			</InspectorControls>
			<div { ...blockProps }>
				<div className="ltm-title-block__inner">
					<div className="ltm-title-block__content">
						{ kicker && (
							<p className="ltm-title-block__kicker">
								{ kicker }
							</p>
						) }
						<InnerBlocks template={ TEMPLATE } templateLock="all" />
					</div>
					{ sponsor && (
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

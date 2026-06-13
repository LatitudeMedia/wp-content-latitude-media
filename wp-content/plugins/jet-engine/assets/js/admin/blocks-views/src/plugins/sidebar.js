const { registerPlugin } = wp.plugins;
const { useEffect, useState } = wp.element;

const objectID = window.JetEngineListingData.object_id || 0;

/**
 * Get preview settings for the listing from local storage or default values.
 * In the local storage, the settings are stored under the key `jet-engine-listing-preview-settings`.
 * `jet-engine-listing-preview-settings` is an object with the following structure:
 * {
 * 	[objectID]: {
 * 		previewWidth: 800, // Example value
 * 		previewBG: '#ffffff', // Example value
 * 	},
 * }
 * @returns {Object} Preview settings for the listing.
 */
const getPreviewSettings = () => {

	const settings = JSON.parse( localStorage.getItem( 'jet-engine-listing-preview-settings' ) ) || {};

	return settings[ objectID ] || {
		width: 600, // Default width
		previewBG: '#f5f5f5', // Default background color
	};
};

const updatePreviewSettings = ( newSettings ) => {

	const settings = JSON.parse( localStorage.getItem( 'jet-engine-listing-preview-settings' ) ) || {};

	settings[ objectID ] = {
		...settings[ objectID ],
		...newSettings,
	};

	localStorage.setItem( 'jet-engine-listing-preview-settings', JSON.stringify( settings ) );
};

const ListingSidebar = () => {

	if ( ! wp.editPost ) {
		return;
	}

	const { PluginSidebar } = wp.editPost;
	const { RangeControl } = wp.components;

	const [ previewSettings, setPreviewSettings ] = useState( getPreviewSettings() );

	useEffect( () => {
		const previewContent = document.querySelector( '.jet-engine-blocks-views-editor .editor-styles-wrapper .is-root-container' );
		const previewContainer = document.querySelector( '.jet-engine-blocks-views-editor .editor-styles-wrapper' );

		if ( previewContent && previewContainer ) {
			previewContent.style.width = `${ previewSettings.width }px`;
			previewContent.style.maxWidth = `${ previewSettings.width }px`;
			previewContainer.style.backgroundColor = previewSettings.previewBG;
		}
	}, [] );

	return (
		<PluginSidebar
			name="jet-engine-listing-sidebar"
			title="Preview Settings"
			icon="admin-generic"
		>
			<div style={ { padding: '16px' } }>
				<RangeControl
					label="Preview Width"
					value={ previewSettings.width }
					min={ 200 }
					max={ 1200 }
					onChange={ ( value ) => {

						setPreviewSettings( {
							...previewSettings,
							...{ width: value },
						} );

						updatePreviewSettings( {
							...previewSettings,
							...{ width: value },
						} );
					} }
				/>
			</div>
		</PluginSidebar>
	);
};

if ( window.JetEngineListingData.isJetEnginePostType ) {
	registerPlugin( 'jet-engine-listing-sidebar', {
		render: ListingSidebar,
		icon: 'admin-generic',
	} );
}

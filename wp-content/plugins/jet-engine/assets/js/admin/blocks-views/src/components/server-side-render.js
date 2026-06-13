const {
	serverSideRender: WPServerSideRender
} = wp;

const {
	applyFilters
} = wp.hooks;

function ServerSideRender( props ) {

	let sanitizedProps = { ...props };

	if ( ! sanitizedProps.httpMethod ) {
		sanitizedProps.httpMethod = 'POST';
	}

	let attributes = { ...sanitizedProps.attributes };

	if ( attributes.crocoblock_styles ) {
		const className = attributes.crocoblock_styles._uniqueClassName;
		attributes.crocoblock_styles = { _uniqueClassName: className };
	}

	sanitizedProps.attributes = attributes;

	sanitizedProps = applyFilters(
		'jet-engine.blocks-views.server-side-render.props',
		sanitizedProps
	);

	return (
		<WPServerSideRender { ...sanitizedProps }/>
	);
}

window.JetEngineBlocksComponents = window.JetEngineBlocksComponents || {};
window.JetEngineBlocksComponents.ServerSideRender = ServerSideRender;

export default ServerSideRender;
const {
	ServerSideRender
} = window.JetEngineBlocksComponents;

const {
	Disabled
} = wp.components;

const {
	useBlockProps,
} = wp.blockEditor;

const Edit = function( props ) {

	const {
		attributes,
	} = props;

	return <div { ...useBlockProps() }>
		<Disabled>
			<ServerSideRender
				block="jet-engine/profile-content"
				attributes={ attributes }
				urlQueryArgs={ {} }
			/>
		</Disabled>
	</div>;
}

export default Edit;

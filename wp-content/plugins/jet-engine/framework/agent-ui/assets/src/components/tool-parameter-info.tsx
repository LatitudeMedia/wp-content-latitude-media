export const ToolParameterInfo = ( {
	name,
	schema
} : { name: string; schema: Record<string, any>; } ) => {
	return <div className="cmd-cnt-tool-param-info">
		<h5 className="cmd-cnt-tool-param-info__name">{ name }</h5>
		<div className="cmd-cnt-tool-param-info__description">{ schema.description || 'No description' }</div>
		{ schema.type && <div className="cmd-cnt-tool-param-info__type">Type: { Array.isArray( schema.type ) ? schema.type.join( ', ' ) : schema.type }</div> }
		{ schema.enum && <div className="cmd-cnt-tool-param-info__enum">Allowed values: { schema.enum.join( ', ' ) }</div> }
		{ schema.default && <div className="cmd-cnt-tool-param-info__default">Default value: { String( schema.default ) }</div> }
		{ schema.items && <div className="cmd-cnt-tool-param-info__props">Items schema: <pre>{ JSON.stringify( schema.items, null, 2 ) }</pre></div> }
		{ schema.properties && <div className="cmd-cnt-tool-param-info__props">Properties schema: <pre>{ JSON.stringify( schema.properties, null, 2 ) }</pre></div> }
	</div>;
}
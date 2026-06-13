import { AngieMcpSdk } from '@elementor/angie-sdk';
import { McpServer } from '@modelcontextprotocol/sdk/server/mcp.js';
import { z } from "zod";

interface jetEngineCompatibilityAngie {
	features: any[];
	api_base: string;
	nonce: string;
}

declare global {
	interface Window {
		jetEngineCompatibilityAngie?: jetEngineCompatibilityAngie;
	}
}

export type ApiResponse = Record<string, unknown>;

async function makeApiRequest( endpoint: string, data: Record<string, unknown> ): Promise<ApiResponse> {

	if ( ! window.jetEngineCompatibilityAngie?.api_base ) {
		throw new Error( 'API base URL is not defined' );
	}

	const response = await fetch(
		window.jetEngineCompatibilityAngie?.api_base + endpoint,
		{
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': window.jetEngineCompatibilityAngie?.nonce || '',
			},
			body: JSON.stringify( { input: data } ),
		}
	);

	if ( ! response.ok ) {
		throw new Error( `HTTP error! status: ${ response.status }` );
	}

	return await response.json();
}

function buildSchemaFromObject( inputObject: any ): any {
	const schema: any = {};

	for ( const key in inputObject ) {
		schema[ key ] = buildItemSchema( inputObject[ key ] );
	}

	return schema;
}

const buildItemSchema = (item: any): any => {

	let itemSchema: any;

	switch ( item.type ) {
		case 'string':
			if ( item.enum ) {
				itemSchema = z.enum(item.enum);
			} else {
				itemSchema = z.string();
			}
			break;
		case 'number':
			itemSchema = z.number();
			break;
		case 'boolean':
			itemSchema = z.boolean();
			break;
		case 'array':
			itemSchema = z.array(buildItemSchema(item.items));
			break;
		case 'object':
			if ( item.properties ) {
				itemSchema = z.object(buildSchemaFromObject(item.properties));
			} else {
				itemSchema = z.record(z.string(), z.any());
			}

			break;
		default:
			itemSchema = z.string();
	}

	if ( item.description ) {
		itemSchema = itemSchema.describe( item.description );
	}

	return itemSchema;
};

function createMcpServer() {
	const server = new McpServer(
		{
			name: 'croco-angie-mcp-server',
			version: '1.0.0',
		},
		{
			capabilities: {
				tools: {},
			},
		}
	);

	for ( const feature of window.jetEngineCompatibilityAngie?.features || [] ) {

		let featureID = feature.id;

		featureID = featureID.replace( /\//g, '-' );
		featureID = featureID.replace( /-/g, '_' );

		server.registerTool(
			featureID,
			{
				title: feature.label,
				description: feature.description,
				inputSchema: buildSchemaFromObject( feature.input_schema.properties ),
			},
			async ( args: any, extra: any ) => {

				const response = await makeApiRequest( feature.name, args );

				console.log( extra );

				return {
					content: [ {
						type: 'text',
						text: JSON.stringify( response, null, 2 ),
					} ],
				};
			}
		);
	}

	return server;
}

const init = async () => {
	try {
		const server = createMcpServer();
		const sdk = new AngieMcpSdk();

		await sdk.registerServer( {
			name: 'croco-angie-mcp-server',
			version: '1.0.0',
			description: 'Crocoblock MCP Server for Angie AI assistant',
			server,
		} );

		console.log( 'Crocoblock MCP Server registered with Angie successfully' );
	} catch ( error ) {
		console.error( 'Failed to register Crocoblock MCP Server with Angie:', error );
	}
};

init();

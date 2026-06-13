import { defineConfig } from 'vite';

export default defineConfig( () => {
	return {
		build: {
			outDir: 'js',
			emptyOutDir: false,
			lib: {
				entry: './src/croco-angie-mcp-server.ts',
				name: 'Croco Angie MCP Server',
				fileName: 'croco-angie-mcp-server',
			},
			minify: true,
		},
	};
} );

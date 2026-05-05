import { ChatApp } from "./chat-app";
import { ChatHelper } from "./chat-helper";
import { Setup } from "./setup";

// TypeScript interfaces for window globals
interface crocoAgentUI {
	hasKey: boolean;
	nonce: string;
	api_key_endpoint: string;
	base_endpoint: string;
	models_endpoint: string;
	save_model_endpoint: string;
	model: string;
	site_key: string;
}

declare global {
	interface Window {
		crocoAgentUI: crocoAgentUI;
	}
}

export const Main = () => {

	const hasKey = window.crocoAgentUI.hasKey;

	return (
		<div className="cmd-cnt-main">
			{ ! hasKey && <Setup /> }
			{ hasKey && <div className="cmd-cnt-chat-app">
				<ChatApp />
				<ChatHelper />
			</div> }
		</div>
	);
};

import { createRoot } from '@wordpress/element';

import { ConversationProvider } from './context/conversation-provider';
import { Main } from './components/main';

import './agent-ui.scss';

const root = createRoot( document.getElementById( 'croco_agent_ui' ) );

root.render(
	<ConversationProvider>
		<Main />
	</ConversationProvider>
);

import './echo';

import Alpine from 'alpinejs';
import dropdown from './components/dropdown';
import chatRoom from './components/chatRoom';

Alpine.data('dropdown', dropdown);
Alpine.data('chatRoom', chatRoom);

window.Alpine = Alpine;
Alpine.start();

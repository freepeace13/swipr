import Alpine from 'alpinejs';
import dropdown from './components/dropdown';

Alpine.data('dropdown', dropdown);

window.Alpine = Alpine;
Alpine.start();

import './echo';

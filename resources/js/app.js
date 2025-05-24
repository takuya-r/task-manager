import './bootstrap';

import Alpine from 'alpinejs';
import notificationDropdown from './notifications';

window.Alpine = Alpine;

Alpine.data('notificationDropdown', notificationDropdown);

Alpine.start();

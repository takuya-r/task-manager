import './bootstrap';

import Alpine from 'alpinejs';
import notificationDropdown from './notifications';
import { updateStatus } from './api/task-status';
import { deleteTask } from './api/task-delete';

window.deleteTask = deleteTask;
window.updateStatus = updateStatus;
window.Alpine = Alpine;

Alpine.data('notificationDropdown', notificationDropdown);

Alpine.start();

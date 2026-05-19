import './bootstrap';

import { initSidebar } from './modules/sidebar';
import { initTableFilter } from './modules/tableFilter';
import './modules/modal';

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initTableFilter();
});

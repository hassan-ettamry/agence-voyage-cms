import './bootstrap';

import { initSidebar } from './modules/sidebar';
import { initTableFilter } from './modules/tableFilter';

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initTableFilter();
});
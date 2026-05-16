import './bootstrap';

import { initSidebar } from './modules/sidebar';
import { initTableFilter } from './modules/tableFilter';
import './modules/modal';

import './builder';

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initTableFilter();
});
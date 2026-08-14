import './bootstrap';

import { initSidebar } from './modules/sidebar';
import { initTableFilter } from './modules/tableFilter';
import { initPageSettingsTabs } from './modules/pageSettingsTabs';
import { initCountdownWidgets } from './modules/countdown';
import { initDashboardCharts } from './modules/dashboardCharts';
import { initPublicNavigation } from './modules/public-navigation';
import { initPublicInteractions } from './modules/public-interactions';
import './modules/modal';

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initTableFilter();
    initPageSettingsTabs();
    initCountdownWidgets();
    initDashboardCharts();
    initPublicNavigation();
    initPublicInteractions();
});

/*
|--------------------------------------------------------------------------
| Core State & App Infrastructure
|--------------------------------------------------------------------------
| Global state, persistence, UI shells, schema, editing
*/

import './state/editor-state';
import './state/structure-state';
import './state/state-utils';
import './state/builder-state';

import './viewport';
import './storage';
import './sidebar';
import './right-sidebar';
import './schema';
import './inline-editing';

import './debug/logger';
import './debug/debug-config';
import './debug/debug-validator';
import './debug/debug-render';
import './debug/debug-snapshot';

import './core/store';
import './core/render-manager';
import './core/event-bus';
import './core/commands/update-node-props';

/*
|--------------------------------------------------------------------------
| Canvas Engine
|--------------------------------------------------------------------------
| Rendering, interaction, focus, hover, selection
*/

import './canvas/canvas-utils';
import './canvas/canvas-hover';
import './canvas/canvas-selection';
import './canvas/canvas-focus';
import './canvas/canvas-events';
import './canvas/canvas-renderer';

/*
|--------------------------------------------------------------------------
| Drag & Drop Engine
|--------------------------------------------------------------------------
| Drag lifecycle, validation, previews, reorder
*/

import './drag/drag-state';

import './drag/drag-utils';

import './drag/drag-validate';

import './drag/drag-hitbox';

import './drag/drag-preview';

import './drag/drag-start';

import './drag/drag-reorder';

import './drag/drag-drop';

import './drag/drag-engine';

/*
|--------------------------------------------------------------------------
| History System
|--------------------------------------------------------------------------
| Undo / Redo + keyboard bindings
*/

import './history/history-manager';
import './history/history-keyboard';

/*
|--------------------------------------------------------------------------
| Component System
|--------------------------------------------------------------------------
| Registry, factories, actions, helpers
*/

import './components/component-utils';
import './components/component-factory';
import './components/component-registry';
import './components/component-actions';

/*
|--------------------------------------------------------------------------
| Selection System
|--------------------------------------------------------------------------
| Active selection state & actions
*/

import './selection/selection-manager';
import './selection/selection-actions';

/*
|--------------------------------------------------------------------------
| Node Operations
|--------------------------------------------------------------------------
| Traversal, mutations, cloning, moving
*/

import './nodes/node-traversal';

import './nodes/node-actions';

import './nodes/node-clone';

import './nodes/node-move';

/*
|--------------------------------------------------------------------------
| Overlay System
|--------------------------------------------------------------------------
| Visual overlays, positioning, controls
*/

import './overlay/overlay-elements';
import './overlay/overlay-position';
import './overlay/overlay-ui';
import './overlay/overlay-actions';
import './overlay/overlay';

/*
|--------------------------------------------------------------------------
| Settings System
|--------------------------------------------------------------------------
| Settings UI, fields, and updates
*/

import './settings/settings-panel';
import './settings/settings-fields';
import './settings/settings-updater';

/*
|--------------------------------------------------------------------------
| Builder Boot
|--------------------------------------------------------------------------
*/

document.addEventListener(

    'DOMContentLoaded',

    () => {

        /*
        |--------------------------------------------------------------------------
        | TEMP EVENT TEST
        |--------------------------------------------------------------------------
        */

        BuilderEventBus.on(

            'component.added',

            (component) => {

                console.log(
                    'EVENT: COMPONENT ADDED',
                    component
                );

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Node Updated
        |--------------------------------------------------------------------------
        */

        BuilderEventBus.on(

            'node.updated',

            (payload) => {

                if (
                    payload?.render === false
                ) {

                    return;

                }

                BuilderRenderManager.requestRender();

            }

        );

        /*
        |--------------------------------------------------------------------------
        | State
        |--------------------------------------------------------------------------
        */

        if (window.BuilderStructureState) {

            BuilderStructureState.init();

        }

        /*
        |--------------------------------------------------------------------------
        | Sidebar
        |--------------------------------------------------------------------------
        */

        if (window.BuilderSidebar) {

            BuilderSidebar.switchTab(
                'widgets'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Right Sidebar
        |--------------------------------------------------------------------------
        */

        if (window.BuilderRightSidebar) {

            BuilderRightSidebar.render();

        }

        /*
        |--------------------------------------------------------------------------
        | Overlay
        |--------------------------------------------------------------------------
        */

        if (window.BuilderOverlay) {

            BuilderOverlay.init();

        }

        /*
        |--------------------------------------------------------------------------
        | History
        |--------------------------------------------------------------------------
        */

        if (window.BuilderHistory) {

            BuilderHistory.init();

        }

        /*
        |--------------------------------------------------------------------------
        | Inline Editing
        |--------------------------------------------------------------------------
        */

        if (window.BuilderInlineEditing) {

            BuilderInlineEditing.init();

        }

        /*
        |--------------------------------------------------------------------------
        | Canvas
        |--------------------------------------------------------------------------
        */

        if (window.BuilderCanvas) {

            BuilderCanvas.render();

        }

        /*
        |--------------------------------------------------------------------------
        | Test Event Bus
        |--------------------------------------------------------------------------
        */

        BuilderEventBus.on(

            'test',

            () => {

                console.log(
                    'EVENT BUS WORKS'
                );

            }

        );

        console.log(
            'Builder Engine Started'
        );

    }

);

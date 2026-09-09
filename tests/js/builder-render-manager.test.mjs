import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

const source = readFileSync(
    new URL('../../resources/js/builder/core/render-manager.js', import.meta.url),
    'utf8'
);

function fixture(render) {
    const frames = [];
    const requests = [];
    let invalidations = 0;

    const context = vm.createContext({
        console,
        Date,
        Promise,
        requestAnimationFrame: callback => {
            frames.push(callback);
            return frames.length;
        },
        BuilderEvents: { CANVAS_RENDER_REQUESTED: 'canvas.render.requested' },
        BuilderEventBus: { emitSafe: (event, payload) => requests.push({ event, payload }) },
        BuilderLogger: { shouldLog: () => false, log() {} },
        BuilderCanvas: {
            render,
            invalidateCurrentRender() {
                invalidations++;
            }
        }
    });

    context.window = context;
    vm.runInContext(`'use strict';\n${source}`, context, { filename: 'render-manager.js' });

    return {
        manager: context.BuilderRenderManager,
        frames,
        requests,
        invalidations: () => invalidations
    };
}

test('grouped render requests return promises resolved by the effective render', async () => {
    const calls = [];
    const { manager, frames, requests } = fixture(async (sourceName, reason) => {
        calls.push({ sourceName, reason });
        return true;
    });

    const first = manager.requestRender('first', 'a');
    const second = manager.requestRender('second', 'b');

    assert.equal(frames.length, 1);
    assert.equal(typeof first?.then, 'function');
    assert.equal(typeof second?.then, 'function');

    await frames.shift()();

    assert.deepEqual(calls, [{ sourceName: 'second', reason: 'b' }]);
    assert.equal(await first, true);
    assert.equal(await second, true);
    assert.equal(requests.length, 2);
    assert.equal(manager.completionWaiters.length, 0);
});

test('requests received in flight wait for the last grouped render', async () => {
    const releases = [];
    const { manager, frames, invalidations } = fixture(() => new Promise(resolve => {
        releases.push(resolve);
    }));

    const first = manager.requestRender('first');
    const firstFrame = frames.shift()();
    await Promise.resolve();

    const second = manager.requestRender('second');
    assert.equal(manager.pendingAfterCurrent, true);
    assert.equal(invalidations(), 1);

    releases.shift()(false);
    await firstFrame;
    assert.equal(frames.length, 1);

    const secondFrame = frames.shift()();
    await Promise.resolve();
    releases.shift()(true);
    await secondFrame;

    assert.equal(await first, true);
    assert.equal(await second, true);
    assert.equal(manager.scheduled, false);
    assert.equal(manager.inFlight, false);
});

test('failed effective render resolves every grouped request as false', async () => {
    const { manager, frames } = fixture(async () => false);
    const first = manager.requestRender('failure');
    const second = manager.requestRender('also-failure');

    await frames.shift()();

    assert.equal(await first, false);
    assert.equal(await second, false);
});

"use strict";

class Clock {
    constructor({frequency, sTO} = {}) {
        if (!frequency) {
            throw RangeError(`Invalid argument range for parameter frequency! Got ${frequency}`);
        }

        if (typeof frequency !== 'number') {
            throw TypeError(`Invalid argument type for parameter frequency! Got ${typeof frequency}`);
        }

        if (frequency < 0) {
            throw RangeError(`Invalid argument range for parameter frequency! Got ${frequency}`);
        }

        this.frequency = frequency;
        this.sTO = sTO || setTimeout;

        this.state = 0;
        this.stateLabel = ['paused', 'play'];
        this.meta = {
            tickCount: 0
        };
    }

    start() {
        this.state = 1;
        this.__tick();
    }

    stop() {
        this.state = 0;
    }

    __tick() {
        this.meta.tickCount = ++this.meta.tickCount;
        this.sTO(() => {
            this.__tick();
        }, 1);
    }
}

module.exports = Clock;
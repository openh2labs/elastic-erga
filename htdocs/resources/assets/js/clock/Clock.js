"use strict";

var Subscribable = require("./../Subscribable");

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

        this.delegates = this.__makeDelegates(["start","stop","tick"]);
    }

    start() {
        this.state = 1;
        this.__tick();
    }

    stop() {
        this.state = 0;
    }

    onStart(callback) {
        this.delegates.start.subscribe(callback);
    }

    onStop(callback) {
        this.delegates.stop.subscribe(callback);
    }

    onTick(callback) {
        this.delegates.tick.subscribe(callback);
    }

    __tick() {
        this.meta.tickCount = ++this.meta.tickCount;
        this.sTO(() => {
            this.__tick();
        }, 1);
    }

    __makeDelegates(keys) {
        if (Object.prototype.toString.call( keys ) !== '[object Array]') {
            throw RangeError(`keys is not an array! got :${keys}!`)
        }

        let delegates = {};

        keys.forEach((key)=>{
            delegates[key] = new Subscribable();
        });

        return delegates;
    }
}

module.exports = Clock;
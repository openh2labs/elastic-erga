"use strict";

var Subscribable = require("./../Subscribable");

class Clock {
    constructor({frequency, systemTimers} = {}) {

        if (!frequency) {
            throw new RangeError(`Invalid argument range for parameter frequency! Got ${frequency}`);
        }

        if (typeof frequency !== 'number') {
            throw new TypeError(`Invalid argument type for parameter frequency! Got ${typeof frequency}`);
        }

        if (frequency < 0) {
            throw new RangeError(`Invalid argument range for parameter frequency! Got ${frequency}`);
        }

        this.systemTimers = systemTimers;
        this.frequency = frequency;

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
        let self = this;

        this.meta.tickCount = ++this.meta.tickCount;
        this.delegates.tick.notify();

        //set the next tick
        this.systemTimers.setTimeout.call(this.systemTimers, () => {
            self.__tick();
        }, this.frequency*1000);
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

let Clock = require('./Clock');

class ClockFactory {

    constructor(config = {}) {
        this.config = config;
        this.__Clock = config.Clock || Clock;
        this.__instances = config.instances || new Map();
    }

    create({name, frequency} = {}) {

        if (name === undefined) {
            name = this.__instances.size; //TODO change it to hash because it will be bugged when we add destroy clock
        }
        if (frequency === undefined) {
            frequency = 5;
        }

        let newClock = new this.__Clock({frequency:frequency, systemTimers:this.config.systemTimers});
        this.__instances.set(name, newClock);
        return this.__instances.get(name);
    }

    getInstance(key) {
        return this.__instances.get(key) || null;
    }
}


module.exports = ClockFactory;

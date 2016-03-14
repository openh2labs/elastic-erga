class Clock {

}

class ClockFactory {
    constructor(config = {}) {
        this.__Clock = config.Clock || Clock;
        this.__instances = config.instances || new Map();
    }

    create(name) {
        let newClock = new this.__Clock();
        let key = name || this.__instances.size; //TODO instead of size use a hashTag it's going to cause bugs when we will be able to destroy Clocks
        this.__instances.set(key, newClock);
        return this.__instances.get(key);
    }
}


module.exports = ClockFactory;
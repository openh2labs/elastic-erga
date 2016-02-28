"use strict"

class Event {
    constructor(rawData) {
        this.model = rawData;
    }

    set model(rawData) {
        this.id             = rawData.id;
        this.description    = rawData.description;
        this.timestamp      = rawData.timestamp;
        this.service        = rawData.service;
    }
}

module.exports = Event;
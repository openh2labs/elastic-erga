"use strict";

let Subscribable = require('./../Subscribable');
let EventModel =require('./Event')

class MetaEvents {
    //TODO error handling, validation & tests
    constructor() {
        this._min = null; //Date
        this._max = null; //Date
        this._total = 0;
        this._total_events_return = [];
    }

    get event_min() {
        return this._min;
    }

    set event_min(value) {
        this._min = value;
    }

    get event_max() {
        return this._max;
    }

    set event_max(value) {
        this._max = value;
    }

    get total() {
        return this._total;
    }

    set total(value) {
        this._total = value;
    }

    get total_events_return() {
        return this._total_events_return.reduce(function(previousValue, currentValue) {
            return previousValue + currentValue;
        });
    }

    set total_events_return(value) {
        this._total_events_return.push(value);
    }
}


class Events extends Subscribable {

    /**
     * d = dependencies
     * http is based on Jquery API https://api.jquery.com/jquery.get/ at the moment
     */
    constructor(d) {

        super();
        this.d = {
            http    : (d) ? d.http : null,
            Event   : (d && d.Event) ? d.Event : EventModel
        };

        this.__serviceUrl = "/elastic_fake";
        this.items = [];
        this.meta = new MetaEvents();
    }


    __request(params = {}) {
        return new Promise((resolve, reject) => {
            this.d.http.getJSON(this.__serviceUrl, params)
                .done((result)=> {
                    let events = null;

                    //parse events
                    try {
                        events = result.hits.map((events) => {
                            return new this.d.Event(events);
                        });
                    }
                    catch (error) {
                        reject(error);
                        return
                    }

                    //parse meta //TODO keep metadata for min & max event
                    try {
                        this.meta.event_min = result.meta.event_timestamp_min;
                        this.meta.event_max = result.meta.event_timestamp_max;
                        this.meta.total = result.meta.total
                        this.meta.total_events_return = result.meta.total_hits_returned
                    }
                    catch (error) {
                        reject(error);
                        return
                    }

                    resolve(events);
                })
                .fail((error) => {
                    reject(error);
                });
        });
    }

    load(params) {
        return new Promise((resolve, reject) => {
            this.__request(params)
                .then((events)=> {
                    resolve(this._update(events));
                })
                .catch((error) => {
                    reject(error);
                });
        });
    }

    loadTail() {
        return new Promise((resolve, reject) => {
            let parameters = {};
            if (this.items.length) {
                parameters = {event_min: this.items[this.items.length-1].timestamp};
            }

            this.__request(parameters)
                .then((newEvents) => {
                    resolve(this._update(this.items.concat(newEvents)));
                })
                .catch((error)=>{
                    reject(error);
                });
        });
    }

    set serviceUrl(newUrl) {
        this.__serviceUrl = newUrl;
    }

    _update(events) {
        this.items = events;
        this._onChanged(this.items);
        return this.items;
    }

    _onChanged() {
        this.notify(this.items);
    }
}


module.exports = Events;

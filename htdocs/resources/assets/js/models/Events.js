"use strict";

let Subscribable = require('./../Subscribable');
let EventModel =require('./Event')


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
    }


    request(params = {}) {
        return new Promise((resolve, reject) => {
            this.d.http.get(this.__serviceUrl, params)
                .done((result)=> {

                    let events = result.events.map((events) => {
                        return new this.d.Event(events);
                    });

                    resolve(events);
                })
                .fail((error) => {
                    reject(error);
                });
        });
    }

    load(params) {
        return new Promise((resolve, reject) => {
            this.request(params)
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

            this.request(parameters)
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

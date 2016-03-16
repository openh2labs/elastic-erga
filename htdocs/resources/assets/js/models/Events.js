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

        this.serviceUrl = "/elastic_fake";
        this.items = [];
    }

    load(params) {
        return new Promise((resolve, reject) => {
            this.d.http.get(this.serviceUrl, params)
                .done((result)=> {
                    console.log(result);

                    let events = result.events.map((events) => {
                        return new this.d.Event(events);
                    });

                    resolve(events);
                    this._update(events);
                })
                .fail((error) => {
                    reject(error);
                });
        });
    }

    _update(events) {
        this.items = events;
        this._onChanged(this.items);
    }

    _onChanged() {
        this.notify(this.items);
    }
}


module.exports = Events;
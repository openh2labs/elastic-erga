"use strict";


class Events {

    /**
     * d = dependencies
     * http is based on Jquery API https://api.jquery.com/jquery.get/ at the moment
     */
    constructor(d){
        this.d = {
            http    : d.http,
            Event   : d.Event
        };

        this.serviceUrl = "http://www.fake.com";
        this.items = [];
    }

    load(params) {
        return new Promise((resolve, reject) => {
            this.d.http.get(this.serviceUrl, params)
                .done((result)=> {
                    let events = result.data.map((rawEvent) => {
                        return new this.d.Event(rawEvent);
                    });

                    resolve(events);
                })
                .fail((error) => {
                    reject(error);
                });
        });
    }

    fakeData(n = 3) {
        let data = [];

        for (let i = 0; i < n; i++) {
            var fid     = i + this.items.length + 1;
            var fstamp  = new Date().getTime();

            data.push( new this.d.Event({
                                    id:fid,
                                    description:'fake item:'+fid,
                                    timestamp:fstamp,
                                    service: this.fakeService(i)
            }));
        }

        return data;
    }

    fakeService(n = Math.floor((Math.random() * 3))) {

        let fakeServices = ['www.fake1.com', 'www.fake2.com', 'www.fake3.com'];

        return fakeServices[n];
    }
}


module.exports = Events;
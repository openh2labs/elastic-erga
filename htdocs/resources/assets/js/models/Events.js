"use strict";

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



class Events {
    constructor(){
        this.items = [];
    }

    load() {
        return new Promise((resolve,reject) => {
            this.items = this.fakeData();
            resolve(this.items);
        });
    }

    fakeData(n = 3) {
        var data = []

        for (let i = 0; i < n; i++) {
            var fid     = i + this.items.length + 1;
            var fstamp  = new Date().getTime();

            data.push( new Event({
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
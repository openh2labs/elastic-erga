"use strict";

/**
 *
 *
 *
 */



class Subscription {
    constructor( callback ){
        if (typeof callback !== 'function') {
            throw new Error('Subscription must always initialised with a callback function!');
        }

        this.callback = callback;
    }
}


class Subscribable {

    constructor() {
        this.__Subscription = Subscription;
        this.subscriptions = [];
    }

    subscribe(callback) {
        let sub = new Subscription(callback);
        this.subscriptions.push(sub);
        return sub;
    }

    notify(message) {
        this.subscriptions.forEach((subscription) => {
            subscription.callback(message);
        });
    }

    unsubscribe(subscription) {
        let index = this.subscriptions.findIndex((item) => {
            return item === subscription;
        });

        this.subscriptions.splice(index, 1);
    }

}

module.exports = Subscribable;
class CallMock {

    constructor (method, uri, params) {
        this.method = method;
        this.uri = uri;
        this.params = params;
        this.resolve = function(){};
        this.reject = function(){};
        this.done =function(callback) {
            this.resolve = callback;
            return this;
        };
        this.fail = function(callback) {
            this.reject = callback;
            return this;
        };
    }
}

class HttpServiceMock {

    /**
     * based on Jquery API https://api.jquery.com/jquery.get/
     */

    constructor () {
        this.calls = [];
        this.resolve = function(){};
    }

    get (uri, params) {
        let callmock = new CallMock('GET', uri, params);
        this.calls.push(callmock);
        return callmock;
    }

    getJSON (uri, params) {
        let callmock = new CallMock('GET', uri, params);
        this.calls.push(callmock);
        return callmock;
    }
}

module.exports = HttpServiceMock;
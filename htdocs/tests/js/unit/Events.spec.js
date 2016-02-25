
let chai = require('chai').use(require('chai-as-promised'));
let expect = chai.expect;
let should = chai.should();
let Events = require('./../../../resources/assets/js/models/Events');

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
}

describe('ErgaTerminal', function() {
    "use strict";
    let unit;
    let httpServiceMock;

    class Event {
        constructor(rawData){
            this.id = rawData.id;
            this.message = rawData.message;
            this.timestamp = new Date('1821-3-25');
        }
    }

    beforeEach(() => {
        httpServiceMock = new HttpServiceMock();
        unit = new Events({Event: Event, http: httpServiceMock});
    });

    afterEach(() => {

    });

    describe('__consructor()', () => {
        describe('list:[Event]', () => {
            it('Events.item should initialised as an empty array', function () {
                expect(unit.items).to.deep.equal([]);
            });
        });
    });

    describe('load( params, (data)=>{} ).self || future', () => {


        it('Should make an ajax call with params', () => {
            let fakeParams = {"fake":"params"};
            unit.load(fakeParams);
            expect(httpServiceMock.calls[0].params).to.equal(fakeParams);
        });

        describe('promise', () => {
            describe('success', () => {
                it('Should return promise that resolves to [Event]', () => {
                    let fakeRawData = {
                        "data": [
                            {"id":"item1", "message":"moooooo"},
                            {"id":"item2", "message":"moooooo I said"}
                        ],
                        "meta": {"fake" : "meta"}
                    };

                    let fakeParams = {"fake":"params"};
                    let fakeResult = fakeRawData.data.map((rawEvent) => {
                        return new Event(rawEvent);
                    });

                    let expectation = expect(unit.load(fakeParams)).to.eventually.deep.equal(fakeResult);
                    httpServiceMock.calls[0].resolve(fakeRawData);

                    return expectation;
                });
            });

            describe('failure', () => {
                it('Should return promise that rejects with Error', () => {
                    let fakeParams = {"fake":"params"};
                    let promise = unit.load(fakeParams);
                    let fakeError = new Error('Server says NO!');

                    httpServiceMock.calls[0].reject(fakeError);

                    return promise.should.be.rejectedWith(fakeError);
                });
            });

        });
    });
});
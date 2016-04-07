"use strict";

let sinon = require('sinon');
let chai = require('chai')
                .use(require('chai-as-promised'))
                .use(require('sinon-chai'));
let expect = chai.expect;
chai.should();
let Events = require('./../../../resources/assets/js/models/Events');
let HttpServiceMock = require('./../mocks/HttpServiceMock');


describe('ErgaTerminal', function() {
    let unit;
    let httpServiceMock;
    let fakeDate = new Date('1821-3-25');

    class Event {
        constructor(rawData){
            this.id = rawData.id;
            this.message = rawData.message;
            this.timestamp = fakeDate;
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

    describe('__request(params:{})', () => {
        describe('http request Success', () => {
            describe('failures', ()=> {
                it('it should reject if the response does not contain hits (Events) key', ()=> {
                    expect(unit.__request).to.not.throw();
                    let fakeRawData = {};
                    let promise = unit.__request();

                    httpServiceMock.calls[0].resolve(fakeRawData);

                    return promise.should.be.rejected;
                });
            });
        });
    });

    describe('load( params, (data)=>{} ).future:[Event]', () => {

        it('Should make an ajax call with params', () => {
            let fakeParams = {"fake":"params"};
            unit.load(fakeParams);
            expect(httpServiceMock.calls[0].params).to.equal(fakeParams);
        });

        describe('promise', () => {
            describe('success', () => {
                it('Should return promise that resolves to [Event]', () => {
                    let fakeRawData = {
                        "hits": [
                            {"id":"item1", "message":"moooooo"},
                            {"id":"item2", "message":"moooooo I said"}
                        ],
                        "meta": {"fake" : "meta"}
                    };

                    let fakeParams = {"fake":"params"};
                    let fakeResult = fakeRawData.hits.map((rawEvent) => {
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

    describe('loadTail()', ()=>{
        let lastEventTimestamp;

        beforeEach(()=>{
            lastEventTimestamp = new Date().getTime();
            unit.items = [{timestamp: lastEventTimestamp }];
        });

        it('should make a load request with tail parameters', ()=>{
            unit.__request = sinon.spy();
            unit.loadTail();
            expect(unit.__request).to.have.been.calledWith({event_min:lastEventTimestamp});
        });

        it('should call an empty load() if no item in this.items', ()=>{
            unit.__request = sinon.spy();
            unit.items = [];
            unit.loadTail();
            expect(unit.__request).to.have.been.calledWith({});
        });

        describe('failure', ()=>{
           it('should reject the promise with error when load rejects',()=>{
               let future = unit.loadTail();
               let fakeError = new Error('server says No');

               httpServiceMock.calls[0].reject(fakeError);

               return future.should.be.rejectedWith(fakeError);
           });
        });

        describe('success', ()=>{
            it('updates the tail of the items = [Events]', ()=>{
                let items = [new Event({id:"1",message:"a"}), new Event({id:"2",message:"b"})];
                let fakeNewItems = [new Event({id:"3",message:"c"})];
                let fakeResponse = {
                    "hits": [
                        {"id":"3", "message":"c"}
                    ],
                    "meta": {"fake" : "meta"}
                };

                unit.items = items;
                let future = unit.loadTail();
                httpServiceMock.calls[0].resolve(fakeResponse);

                return future.should.eventually.deep.equal(items.concat(fakeNewItems));
            });
        });
    });

    describe('_update([Event])', () => {
       it('Should update the Events.list with the new events', () => {
           let fakeEventList = [new Event({id:1,message:"lol"})];
           unit._update(fakeEventList);

           expect(unit.items).to.equal(fakeEventList);
       });

        it('Should trigger the _onChanged method with [Event]', () => {
            let stub = unit._onChanged = sinon.stub();
            let fakeEventList = [new Event({id:1,message:"lol"})];
            unit._update(fakeEventList);

            expect(stub).to.have.been.calledWith(fakeEventList);
        });
    });

    describe('_onChanged([Event])', () => {
        it('should notify observers', function(){
            let fakeEventList = [new Event({id:1,message:"lol"})];
            let stub = sinon.stub();

            unit.subscribe(stub);
            unit.items = fakeEventList;
            unit._onChanged();

            expect(stub).to.have.been.calledWith(fakeEventList);
        });

        it('Should notify all subsciptions', () => {
            let stubA = sinon.stub();
            let stubB = sinon.stub();
            let fakeEventList = [new Event({id:1,message:"lol"})];

            unit.subscribe(stubA);
            unit.subscribe(stubB);

            unit.items = fakeEventList;

            unit._onChanged(stubA);
            unit._onChanged(stubB);

            expect(stubA).to.have.been.calledWith(fakeEventList);
            expect(stubB).to.have.been.calledWith(fakeEventList);
        });
    });
});

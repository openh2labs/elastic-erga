"use strict";

let sinon = require('sinon');
let chai = require('chai')
    .use(require('chai-as-promised'))
    .use(require('sinon-chai'));
let expect = chai.expect;
let Clock = require('./../../../resources/assets/js/clock/Clock');



class WindowTimersMock {
    constructor(){
        WindowTimersMock.callback = function(){console.log('default');}
    }

    setTimeout(cb, interval) {
        WindowTimersMock.callback = cb;
    }

    flush() {
        WindowTimersMock.callback();
    }
}

describe('Clock', ()=>{

    describe('constructor()', ()=>{
        it('sets frequency to 10', ()=>{
            let myclock = new Clock({frequency:10});
            expect(myclock.frequency).equal(10);
        });

        it('throws when undefined frequency is provided', ()=> {
            let throws = false;

            try {
                new Clock();
            }
            catch (error) {
                throws = true;
            }

            expect(throws).to.equal(true);
        });

        it('throws when invalid type frequency is provided', ()=> {
            let throws = false;

            try {
                new Clock({frequency:"Gama"});
            }
            catch (error) {
                throws = true;
            }

            expect(throws).to.equal(true);
        });

        it('throws when frequency 0', ()=> {
            let throws = false;

            try {
                new Clock({frequency:-1});
            }
            catch (error) {
                console.log(error);
                throws = true;
            }

            expect(throws).to.equal(true);
        });

        it('throws when frequency less than 0', ()=> {
            let throws = false;

            try {
                new Clock({frequency:-1});
            }
            catch (error) {
                throws = true;
            }

            expect(throws).to.equal(true);
        });
    });

    describe('public methods ', ()=>{
        let tStub = null;
        let unit = null;

        beforeEach(()=>{
            unit = new Clock({frequency:1});
            tStub = sinon.stub(unit, "__tick");
        });


        describe('start()', ()=>{

            it('should set status to 1', ()=>{
                unit.start();
                expect(unit.state).to.equal(1);
            });

            it('it should initiate a clock calling tick', ()=>{
                unit.start();
                expect(tStub.callCount).to.equal(1);
            });
        });

        describe('stop()', ()=>{

            it('should set status to 0', ()=>{
                unit.stop();
                expect(unit.state).to.equal(0);
            });
        });

        describe('delegates', ()=>{

            describe('onStart()', ()=>{
                it('should subscribe the argument callback to a startEvent', ()=>{
                    let stub = sinon.stub(unit.delegates.start, 'subscribe');
                    let fake = sinon.stub();
                    unit.onStart(fake);
                    expect(stub).to.have.been.calledWith(fake);
                });
            });

            describe('onStop()', ()=>{
                it('should subscribe the argument callback to a startEvent', ()=>{
                    let stub = sinon.stub(unit.delegates.stop, 'subscribe');
                    let fake = sinon.stub();
                    unit.onStop(fake);
                    expect(stub).to.have.been.calledWith(fake);
                });
            });

            describe('onTick()', ()=>{
                it('should subscribe the argument callback to a tick event', ()=>{
                    let stub = sinon.stub(unit.delegates.tick, 'subscribe');
                    let fake = sinon.stub();
                    unit.onTick(fake);
                    expect(stub).to.have.been.calledWith(fake);
                });
            });
        });

    });

    describe('private methods', ()=>{
        let unit = null;

        describe('__tick()', ()=>{
            let wtm;

            beforeEach(()=>{
                wtm = new WindowTimersMock();
                unit = new Clock({frequency:1, systemTimers:wtm});
            });

            it('should set the tickCount +1', ()=>{
                unit.__tick();
                expect(unit.meta.tickCount).to.equal(1);
            });

            it('should call itself  when setTimeout calls back', () => {
                let spy = sinon.spy(unit, '__tick');
                unit.__tick();
                wtm.flush();
                expect(spy.callCount).to.equal(2);
            });

            it("should call setTimeout with the right context", ()=>{
                let stub = sinon.stub(wtm,"setTimeout");
                unit.__tick();
                wtm.flush();
                expect(stub).to.be.calledOn(wtm);
            });

            it("should notify the subscribers of a tick event", ()=>{
                let stub = sinon.stub(unit.delegates.tick, "notify");
                unit.__tick();
                wtm.flush();
                expect(stub).to.have.been.calledTwice;
            });
        });

        describe('__makeDelegates', ()=> {
           it('should create and return delegates registry object using an array of keys', ()=>{
               let fakeKeys = ["lola", "here", "one", "apple"];

               let delegates = unit.__makeDelegates(fakeKeys);

               fakeKeys.forEach((key)=>{
                   expect(delegates).to.have.property(key);
                   expect(delegates[key]).to.have.property("subscribe");
               });
           });
        });
    });
});

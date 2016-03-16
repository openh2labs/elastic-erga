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
    });

    describe('private methods', ()=>{
        let unit = null;

        describe('__tick()', ()=>{
            let wtm;

            beforeEach(()=>{
                wtm = new WindowTimersMock();
                unit = new Clock({frequency:1, sTO:wtm.setTimeout});
            });

            it('should set the tickCount +1', ()=>{
                unit.__tick();
                expect(unit.meta.tickCount).to.equal(1);
            });

            it('should call itself after 1 second', () => {
                let spy = sinon.spy(unit, '__tick');
                unit.__tick();
                wtm.flush();
                expect(spy.callCount).to.equal(2);
            });
        });
    });
});
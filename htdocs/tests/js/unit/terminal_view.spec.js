"use strict";

let sinon = require('sinon');
let chai = require('chai')
    .use(require('chai-as-promised'))
    .use(require('sinon-chai'));
let expect = chai.expect;
let TerminalViewFactory = require('./../../../resources/assets/js/terminal/terminal_view');


describe.only('TerminalView ', () => {

    let jsdom = null;

    let fakeElement = null;
    let fakeModel = null;
    let fakeConfig = null;
    let fakeDependencies = null;
    let unit = null;

    /** Mock model dependecy.
    You can effectively use it as documentation
     */
    class FakeModel {
        constructor() {
            //Provide handles for the load Promise
            this.loadResolve = null;
            this.loadReject = null;

            //Provide sinon spies for the methods you want
            this.spies = {
                load :sinon.spy(this, "load")
            }
        }

        load() {
            return new Promise((resolve, reject) => {
                this.loadResolve = resolve;
                this.loadReject = reject;
            });
        }
    }

    class FakeClock {
        constructor() {

            this.spies = {
                start: sinon.spy(this, "start"),
                stop: sinon.spy(this, "stop")
            }
        }

        start() {

        }

        stop() {

        }
    }

    beforeEach(() => {
        jsdom = require('jsdom-global')();

        fakeDependencies = {
            clock : new FakeClock()
        };

        fakeModel = new FakeModel();

        fakeElement = document.createElement('div');

        fakeConfig = {};

        unit = new TerminalViewFactory.__Class(fakeDependencies, fakeElement, fakeModel, fakeConfig);
    });

    afterEach(() => {
        jsdom();
    });

    describe('constructor' , () => {
        it('should request model.load', () => {
            expect(fakeModel.spies.load.called).to.equal(true);
        });

        it('should default config', ()=>{
            expect(unit._config).to.deep.equal({polling:true});
        });

        it('should have a clock', () => {
            expect(unit._clock).to.equal(fakeDependencies.clock);
        });

        describe('config.polling == true', ()=> {
            it('should start the clock', ()=> {
                expect(unit._clock.spies.start.called).to.equal(true);
            });
        });
    });

    describe('__configure', () => {
        it('should not alter config when called with undefined', () => {
            unit.__configure();
            expect(unit._config).to.deep.equal({polling:true});
        });

        it('should "extend" configuration when __configure is called with hash', () => {
            unit.__configure({polling:false});
            expect(unit._config).to.deep.equal({polling:false});
        });

        it('should call __configObserveHandler with changed', () => {
            unit.__configObserveHandler = sinon.stub();
            unit.__configure({polling:false});

            expect(unit.__configObserveHandler).to.have.been.calledWith({polling:{ old_value:true, value:false}});
        });
    });

    describe('__onTick', () => {
        it('should call load', ()=> {
            unit.load = sinon.stub();
            unit.__onTick();
            expect(unit.load.called).to.equal(true);
        });
    });

    describe('__configObserveHandler', () => {

        it('is triggered when config is changed', ()=>{
            unit.__configObserveHandler = sinon.stub();
            unit.__configure({whatever:"wherever"});
            expect(unit.__configObserveHandler.called).to.equal(true);
        });

        it('calls __onPollingChanged when polling is changed', ()=> {
            unit.__onPollingChanged = sinon.stub();
            unit.__configure({polling:false});
            unit.__configure({polling:true});
            expect(unit.__onPollingChanged.called).to.equal(true)
        });

    });

    describe('__onPollingChanged', () => {
        describe('polling:true', () => {
            it('should start the clock', () => {
                unit._config.polling = false;
                unit._clock.spies.start.reset();
                unit.__configure({polling:true});
                unit.__onPollingChanged();
                expect(unit._clock.spies.start.called).to.equal(true);
            });
        });
        describe('polling:false', () => {
            it('should stop the clock', () => {
                unit._config.polling = true;
                unit._clock.spies.start.reset();
                unit.__configure({polling:false});
                unit.__onPollingChanged();
                expect(unit._clock.spies.stop.called).to.equal(true);
            });
        });
    });

});
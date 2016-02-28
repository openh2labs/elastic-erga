"use strict";

let sinon = require('sinon');
let chai = require('chai')
    .use(require('chai-as-promised'))
    .use(require('sinon-chai'));
let expect = chai.expect;
let should = chai.should();
let Subscribable = require('./../../../resources/assets/js/Subscribable');


/**
 *
 *
 *
 */

describe('Subscribable', () => {

    let unit;

    beforeEach(() => {
        unit = new Subscribable();
    });

    describe('.subscriptions = [Subscriptions]', () => {
        it('inits as an empty array', () => {
            expect(unit.subscriptions).to.deep.equal([]);
        });
    });


    describe('subscribe()', () => {
        it('it creates a new Subscription and store it in Subscribers', () => {
            let stub = sinon.stub();
            unit.subscribe(stub);
            expect(unit.subscriptions).to.deep.equal([{callback: stub}]);
        });

        it ('returns a subscription', () => {
            let stub = sinon.stub();

            expect(unit.subscribe(stub)).to.deep.equal({callback: stub});
        });
    });

    describe('notify()', () => {
        it('Should fire all subscription callbacks', () => {
            let stub = sinon.stub();
            let stubB = sinon.stub();
            let notification = "Ta daaaah!";

            unit.subscribe(stub);
            unit.subscribe(stubB);
            unit.notify(notification);

            stub.should.have.been.calledWith(notification);
            stubB.should.have.been.calledWith(notification);
        });
    });


    describe('unsubscribe(subscription)', () => {
        it('Should remove the subscription from subscriptions', () => {
            let stub = sinon.stub();
            let subscription = unit.subscribe(stub);

            unit.unsubscribe(subscription);

            expect(unit.subscriptions).to.deep.equal([]);
        });

        it('subscription callback should not be triggered', () => {
            let stub = sinon.stub();
            let notification = "Ta daaaah!";

            let subscription = unit.subscribe(stub);

            unit.unsubscribe(subscription);
            unit.notify(notification);

            stub.should.have.not.been.called;
        });

        it('should unsubscribe only the targeted subscription', () => {
           let stubs = [sinon.stub(), sinon.stub(), sinon.stub()];
            let notification = "2 out of three must fire";
            let subs = [];

            stubs.forEach((stub) => {
                subs.push(unit.subscribe(stub));
            });

            unit.unsubscribe(subs[1]);

            unit.notify(notification);

            stubs[0].should.have.been.called;
            stubs[1].should.have.not.been.called;
            stubs[2].should.have.been.called;
        });
    });


    describe(' > Subscription subclass', () => {
        let Subscription;
        beforeEach(()=> {
            Subscription = unit.__Subscription;
        });

        describe('__constructor(callback)', () => {
            it('sets a callback:callback', () => {
                let stub = sinon.stub();
                let sub = new Subscription(stub);

                expect(sub.callback).to.equal(stub);
            });

            it('throws when no callback is provided', () => {
                let throws = false;

                try {
                    new Subscription();
                }
                catch (error) {
                    throws = true;
                }

                expect(throws).to.equal(true);
            });
        });
    });
});
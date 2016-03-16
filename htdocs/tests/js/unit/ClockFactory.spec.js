"use strict";

let sinon = require('sinon');
let chai = require('chai')
    .use(require('chai-as-promised'))
    .use(require('sinon-chai'));
let expect = chai.expect;
let ClockFactory = require('./../../../resources/assets/js/clock/ClockFactory');


describe('ClockFactory ', ()=>{

    class ClockMock {

    }

    let unit = null;

    beforeEach(()=>{
       unit = new ClockFactory({
            Clock : ClockMock
       });
    });

    describe('create', ()=>{
        it('Should return an instance of Clock', ()=>{
            expect(unit.__Clock).to.equal(ClockMock);
            expect(unit.create()).to.be.an.instanceof(ClockMock);
        });

        it('should push the new instance to ClockFactory.__instances hash', ()=>{
            unit.create();
            expect(unit.__instances.size).to.equal(1);
        });

        it('should be able to create named instances of Clocks', ()=>{
            let spy = sinon.spy(unit, "__Clock");
            let clockInstance = unit.create({name:'awesomeClock', frequency:3});
            expect(unit.__instances.get('awesomeClock')).to.be.an.instanceof(ClockMock);
            expect(unit.__instances.get('awesomeClock')).to.equal(clockInstance);
            expect(spy).to.have.been.calledWith({frequency:3});
        });

        it ('should be able to create Clocks with frequency', ()=>{
            //stub the constructor of __Class
            let stub = sinon.stub(unit, "__Clock");
            unit.create({name:'awesomeFrequencyClock', frequency:1.1});
            expect(stub).to.have.been.calledWith({frequency:1.1});
        });

        describe('default parameters', () => {
            it('name param: should default to the number of instances', ()=>{
                unit.create();
                expect(unit.__instances.get(0)).to.be.an.instanceof(ClockMock);
            });

            it('frequency param should default to 5', () => {
                let spy = sinon.spy(unit, "__Clock");
                unit.create();
                expect(spy).to.have.been.calledWith({frequency:5});
            });
        });
    });

    describe('getInstance', ()=>{
        describe('Failure: when clock with key does not exist', ()=>{
            it('should return null', ()=>{
                expect(unit.getInstance("whatever")).to.be.a("null");
                expect(unit.getInstance(0)).to.be.a("null");
            });
        });

        describe('Success: when clock with key does exist', ()=>{
            it('should return the clock instance by key', ()=>{
                unit.create({name:0});
                expect(unit.getInstance(0)).to.be.an.instanceof(ClockMock);
                unit.create({name:'LOLZ', frequency:1.1});
                expect(unit.getInstance("LOLZ")).to.be.an.instanceof(ClockMock);
            });
        });
    });
});


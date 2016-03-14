"use strict";

let sinon = require('sinon');
let chai = require('chai')
    .use(require('chai-as-promised'))
    .use(require('sinon-chai'));
let expect = chai.expect;
let ClockFactory = require('./../../../resources/assets/js/clock/ClockFactory');


describe('ClockFactory ', () => {

    class ClockMock {

    }

    let unit = null;
    let cMock = null;

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
            let clockInstance = unit.create('awesomeClock');
            expect(unit.__instances.get('awesomeClock')).to.be.an.instanceof(ClockMock);
            expect(unit.__instances.get('awesomeClock')).to.equal(clockInstance);
        });

        it ('should be able to create Clocks with frequency', ()=>{
            let stub = sinon.stub();
            unit.create('awesomeFrequencyClock', 1.1);
            expect(stub).to.have.been.calledWith({frequency:1.1});
        });
    });

});
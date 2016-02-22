
var lastMockJInstance = null
var jqueryMock = function() {
    "use strict";

    if (!lastMockJInstance) {
        lastMockJInstance = {
            length : 0,
            reset : function () {
                lastMockJInstance = null
            }
        }
    }
    return lastMockJInstance;
};

var consoleMock =  {
    log: function (toLog) {}
};

var assert = require('assert');
var unit = require('./../../../resources/assets/js/erga_terminal/erga_terminal_view');

describe('ErgaTerminalView', function() {
    var something

    beforeEach(() => {
        something = new unit.create(jqueryMock, consoleMock);
    });

    afterEach(() => {
        lastMockJInstance.reset();
    });

    describe('#indexOf()', () => {
        it('unit.$element must equal to juery object', function () {
            //assert.equal(-1, [1,2,3].indexOf(5));
            //assert.equal(-1, [1,2,3].indexOf(0));
            assert.equal(something.$element, jqueryMock());
        });
    });
});
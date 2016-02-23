
var lastMockJInstance = null;
var jqueryMock = function() {
    "use strict";

    if (!lastMockJInstance) {
        lastMockJInstance = {
            length : 0,
            data : function() {

            },
            reset : function () {
                lastMockJInstance = null;
            }
        };
    }
    return lastMockJInstance;
};

var consoleMock =  {
    log: function () {}
};

var assert = require('assert');
var unit = require('./../../../resources/assets/js/terminal/terminal_factory');

describe('ErgaTerminal', function() {
    "use strict";
    var something;

    beforeEach(() => {
        something = new unit.create(jqueryMock, consoleMock);
    });

    afterEach(() => {
        lastMockJInstance.reset();
    });

    describe('#indexOf()', () => {
        it('unit.$container must equal to juery object', function () {
            assert.equal(something.$container, jqueryMock());
        });
    });
});
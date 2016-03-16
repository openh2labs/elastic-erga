
var data = [require('./response_0'), require('./response_1'), require('./response_2')];


function create($) {
    console.log('create mockjax', $);
    $.mockjax({
        url: "/elastic_fake",
        contentType: "application/json",
        responseText: data[0],
    });
}


module.exports = {
    create: create
}
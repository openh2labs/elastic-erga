
var data = [require('./response_0'), require('./response_1'), require('./response_2')];


class EventsHandler {
  constructor(responses){
    this.eventsBucket = [];

    responses.forEach((response)=>{
      this.eventsBucket = this.eventsBucket.concat(response.events);
    });
  }

  getNextBatch () {
    return {
      events: this.eventsBucket.splice(0, 10)
    };
  }
}

function create($) {
    let handler = new EventsHandler(data);

    $.mockjax({
        url: "/elastic_fake",
        contentType: "application/json",
        response: function(setting) {
          this.responseText = handler.getNextBatch();
        }
    });
}

module.exports = {
    create: create
}

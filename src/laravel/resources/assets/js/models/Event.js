"use strict"

/**
 *
 *

 {
     "id": "639235547134754823",
     "source_ip": "87.255.57.157",
     "program": "docker",
     "message": "time=\"2016-02-24T19:40:52.079220977Z\" level=info msg=\"GET /containers/json\"",
     "event_message": "time=&quot;2016-02-24T19:40:52.079220977Z&quot; level=info msg=&quot;GET /containers/json&quot;",
     "received_at": "2016-02-24T19:40:53+00:00",
     "received_at_iso8601": "2016-02-24T19:40:53+00:00",
     "received_at_syslog": "Feb 24 19:40:53",
     "source_id": 184746163,
     "source_name": "ams1-dmz-msm-kubenode-01",
     "hostname": "ams1-dmz-msm-kubenode-01",
     "severity": "Info",
     "facility": "Daemon",
     "centered_source_events_url": "https://papertrailapp.com/systems/ams1-dmz-msm-kubenode-01/events?centered_on_id=639235547134754823",
     "centered_program_events_url": "https://papertrailapp.com/groups/2272443/events?centered_on_id=639235547134754823&q=program%3Adocker",
     "html_class": "event"
 }

 */

function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}

class Event {
    constructor(rawData) {
        this.model = rawData;
    }

    set model(rawData) {

        this.id             = rawData.id;
        this.source_ip      = rawData.source_ip;
        this.hostname       = rawData.hostname;
        this.program        = rawData.program;
        this.message        = decodeHtml(rawData.message);
        this.event_message  = decodeHtml(rawData.event_message);
        this.severity       = rawData.severity.toLowerCase();
        this.facility       = rawData.facility;
        this.html_class     = rawData.html_class;
        this.timestamp      = rawData.received_at;
    }
}

module.exports = Event;
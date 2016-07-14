*Terminal Component*

***The purpose of this component is to fetch and display log entries from and endpoint. 

_Current features:_
* fetch logs from api endpoint
* list entries to screen
* keyword filtering on ENTER or search button
* polling every 3 seconds
* paused polling on user scrolling back current logs
* auto resuming of polling when user scrolls down entries

The very basic operation of how this works is upon load, the entry point (`../../index.js`) kicks the process off by initiating the fetch for data via `getTerminalData()` defined in the `../../actions/index.js` file.
After data is retrieved, all control of handling the data is done by the component. All data is passed to the redux store, read and manipulated by the component which in this instance is a single file. in the components directory.





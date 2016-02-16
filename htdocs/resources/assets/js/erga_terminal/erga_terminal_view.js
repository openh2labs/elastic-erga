/**
 *
 */

var ErgaTerminalView = function($, console){
    var view=  $(".erga-terminal-view")
    if(view.length) {
        view.append("Hello World, This is Terminal!");
    }
    else {
        console.log("Beware the lack of terminal view element");
    }
}(jQuery, window.console);


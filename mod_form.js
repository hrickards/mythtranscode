M.mod_mythtranscode_form = {};

// Y is YUI, change is HTML to change the curent choice
M.mod_mythtranscode_form.init = function(Y, changeLink) {
  this.Y = Y;
  this.changeLink = changeLink;

  // Set the name of the hidden property and the change event for visibility
  var hidden, visibilityChange; 
  if (typeof document.hidden !== "undefined") { // Opera 12.10 and Firefox 18 and later support 
    hidden = "hidden";
    visibilityChange = "visibilitychange";
  } else if (typeof document.mozHidden !== "undefined") {
    hidden = "mozHidden";
    visibilityChange = "mozvisibilitychange";
  } else if (typeof document.msHidden !== "undefined") {
    hidden = "msHidden";
    visibilityChange = "msvisibilitychange";
  } else if (typeof document.webkitHidden !== "undefined") {
    hidden = "webkitHidden";
    visibilityChange = "webkitvisibilitychange";
  }

  // Remove any previous recording title cookie
  Y.Cookie.remove('recording', {path: '/'});

  function handleVisibilityChange() {
    if (!document[hidden]) {
      // Called when the page gains focus
      var recording = Y.Cookie.get('recording');

      // If a recording title has been set
      if (recording) {
        // Change link to choose a television programme to the title of the chosen
        // one and a link to change
        Y.one('div#mythtranscode_choose_recording').setHTML(recording.replace(/\+/g, ' ') + '   ' + changeLink);
      }
    }
  }

  if (!(typeof document.addEventListener === "undefined") && !(typeof hidden === "undefined")) {
    document.addEventListener(visibilityChange, handleVisibilityChange, false);
  }
}

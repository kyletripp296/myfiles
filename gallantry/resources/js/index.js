(function() {
  // IE 11 Polyfill
  if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}
  let Website = {};


  //Bring in our modules

  let Menu = require('./components/menu.js');



  //Invocations
  window.addEventListener("DOMContentLoaded",function() {
    Website.Menu = Menu.init();

  });

})();
// Website.ScrollDetection = ScrollDetection.init();
// let Sample = require("./componenets/sample.js");
// Sample.init();

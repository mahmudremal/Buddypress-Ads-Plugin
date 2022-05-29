window.onload = function() {
  let myiFrame = document.getElementById("myiFrame");
  let doc = myiFrame.contentDocument;
  doc.body.innerHTML = doc.body.innerHTML + '<style>/******* Put your styles here *******</style>';
}
window.onload = function() {
  let link = document.createElement("link");
  link.href = "style.css";      /**** your CSS file ****/ 
  link.rel = "stylesheet"; 
  link.type = "text/css"; 
  frames[0].document.head.appendChild(link); /**** 0 is an index of your iframe ****/ 
}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> <html> <head> <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> <title>php</title>
<script type="text/javascript">
  var treeData = [{ id: 1, name: "学术和教育", children: [ {id: 2, name: "自然科学", children: null}, { id: 3, name: "社会科学", children: [ {id: 4, name: "建筑学", children: null} ] }, { id: 4, name: "哲学", children: [ {id: 4, name: "建筑学", children: null} ] } ] }, { id: 5, name: "科技与发明", children: [{ id: 6, name: "导弹", children: [ {id: 4, name: "流体力学", children: null} ] }] }];
 (function() { var isIE = /msie/i.test(navigator.userAgent) && !window.opera; function createElement(tagName, styles, props) { var tag = document.createElement(tagName); if (styles) { for (var styleName in styles) { if (isIE && styleName == "cssFloat") {styleName = "styleFloat";} tag.style[styleName] = styles[styleName]; } } if (props) { for (var prop in props) {tag[prop] = props[prop];} } return tag; } function addNode(currentObj, parentNode) { var dlTag = createElement("dl"); var ddTag = createElement("dd", {cursor: "pointer"}, {id: currentObj.id}); var textNode = document.createTextNode(currentObj.name); var childTag = createElement("div", {display: "none"}); var children = currentObj.children; if (children) { for (var index = 0; index < children.length; index++) {addNode(children[index], childTag);} } ddTag.onclick = function(e) { var event = e || window.event; if (event.stopPropagation) {event.stopPropagation();} else {event.cancelBubble = true;} var childrenDivs = this.getElementsByTagName("div"); if (childrenDivs[0] && childrenDivs[0].style.display == "none") {childrenDivs[0].style.display = "block";} else { for (var index = 0; index < childrenDivs.length; index++) {childrenDivs[index].style.display = "none";} } }; ddTag.appendChild(textNode); ddTag.appendChild(childTag); dlTag.appendChild(ddTag); parentNode.appendChild(dlTag); } 
 JTree = function(containerId, datas) {
 	var container = document.getElementById(containerId); for (var index = 0; index < datas.length; index++) {addNode(datas[index], container);} }; })(); window.onload = function() { new JTree("container", treeData); 
 	}; 
</script> 
 </head> <body> <div id="container"></div> 
 </body> </html>
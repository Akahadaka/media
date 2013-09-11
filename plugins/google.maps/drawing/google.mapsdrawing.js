var drawingManager;
var selectedShape;
var recordedShape = {
  circle: [],
  polygon: [],
  polyline: [],
  rectangle: []
};
var colors = ['#1E90FF', '#FF1493', '#32CD32', '#FF8C00', '#4B0082'];
var selectedColor;
var colorButtons = {};

var polyOptions = {
  strokeWeight: 0,
  fillOpacity: 0.45,
  editable: true
};
    
// Loads existing polygons/drawings
var polyDisplay = function(){
  
}

function setScrollFocus(map, value) {
map.setOptions({'scrollwheel': value});
//console.log('scrollwheel: '+value);
}

function clearSelection() {
  if (selectedShape) {
    selectedShape.setEditable(false);
    selectedShape = null;
  }
}

function setSelection(shape) {
  clearSelection();
  selectedShape = shape;
  shape.setEditable(true);
  selectColor(shape.get('fillColor') || shape.get('strokeColor'));
}

function deleteSelectedShape() {	      	
  if (selectedShape && confirm("Are you sure want to delete the selected shape")) {
  	shapes = recordedShape[selectedShape.type];
  	for (var i = 0; i < shapes.length; i++) {
  		if (selectedShape == shapes[i]) {
  			recordedShape[selectedShape.type].splice(i, 1);
  	}
  	}
    	selectedShape.setMap(null);
    	clearSelection();
  }
}

function saveOverlay() {
  document.getElementById("savedata-json").innerHTML = "";

  // Circles
  circles = recordedShape.circle;
  for (var i = 0; i < circles.length; i++) {
    var DIV = document.createElement("div");
    var circleCenter = circles[i].getCenter();
    var circleRadius = circles[i].getRadius();
    DIV.innerHTML += '{"circle": {';
    DIV.innerHTML += '"center": {';
    DIV.innerHTML += '"LatLng": [' + circleCenter.lat() + ', ' + circleCenter.lng() + ']';
    DIV.innerHTML += '}, ';
    DIV.innerHTML += '"radius": ' + circleRadius + ', ';
    DIV.innerHTML += '"fillColor": "' + circles[i].fillColor + '"';
    DIV.innerHTML += '}},';
    //document.getElementById("savedata-json").appendChild(DIV);
    document.getElementById("savedata-json").innerHTML += DIV.innerHTML;
  }

  // Polygons
  polygons = recordedShape.polygon;
  for (var i = 0; i < polygons.length; i++) {
  	var DIV = document.createElement("div");
  	DIV.innerHTML += '{"polygon": {';
    	DIV.innerHTML += '"paths": {';
    	DIV.innerHTML += '"MVCArray": [';
    	var polypaths = polygons[i].getPath();
    	for (var k = 0; k < polypaths.length; k++) {
    		DIV.innerHTML += '{"LatLng": [' + polypaths.getAt(k).lat() + ', ' + polypaths.getAt(k).lng() + ']}';
    		if (k < polypaths.length-1) DIV.innerHTML += ', ';
  }
  DIV.innerHTML += ']}, '; 
    	DIV.innerHTML += '"fillColor": "' + polygons[i].fillColor + '" ';
   	DIV.innerHTML += '}},';
    	//document.getElementById("savedata-json").appendChild(DIV);
      document.getElementById("savedata-json").innerHTML += DIV.innerHTML;
  }

  // PolyLines
  polylines = recordedShape.polyline;
  for (var i = 0; i < polylines.length; i++) {
  	var DIV = document.createElement("div");
  	DIV.innerHTML += '{"polyline": {';
    	DIV.innerHTML += '"path": {';
    	DIV.innerHTML += '"MVCArray": [';
    	var linepaths = polylines[i].getPath();
    	for (var k = 0; k < linepaths.length; k++) {
    		DIV.innerHTML += '{"LatLng": [' + linepaths.getAt(k).lat() + ', ' + linepaths.getAt(k).lng() + ']}';
    		if (k < linepaths.length-1) DIV.innerHTML += ', ';
  }
  DIV.innerHTML += ']}, '; 
    	DIV.innerHTML += '"strokeColor": "' + polylines[i].strokeColor + '" ';
   	DIV.innerHTML += '}},';
    	//document.getElementById("savedata-json").appendChild(DIV);
      document.getElementById("savedata-json").innerHTML += DIV.innerHTML;
  }

  // Rectangles
  rectangles = recordedShape.rectangle;
  for (var i = 0; i < rectangles.length; i++) {
  var DIV = document.createElement("div");
  var rectangleSW = rectangles[i].getBounds().getSouthWest();
  var rectangleNE = rectangles[i].getBounds().getNorthEast();
    	DIV.innerHTML += '{"rectangle": {';
    	DIV.innerHTML += '"bounds": {';
    	DIV.innerHTML += '"LatLngBounds": [';
    	DIV.innerHTML += '{"LatLng": [' + rectangleSW.lat() + ', ' + rectangleSW.lng() + ']}, ';
    	DIV.innerHTML += '{"LatLng": [' + rectangleNE.lat() + ', ' + rectangleNE.lng() + ']}';
    	DIV.innerHTML += ']},';
    	DIV.innerHTML += '"fillColor": "' + rectangles[i].fillColor + '" ';
    	DIV.innerHTML += '}},';
    	//document.getElementById("savedata-json").appendChild(DIV);
      document.getElementById("savedata-json").innerHTML += DIV.innerHTML;
  }
}
/**
[
{
    "circle": {
        "center": {
            "LatLng": [
                -29.42,
                19.479
            ]
        },
        "radius": 47068.116,
        "fillColor": "#FF1493"
    }
},
{
    "polygon": {
        "paths": {
            "MVCArray": [
                {
                    "LatLng": [
                        -30.411,
                        23.983
                    ]
                },
                {
                    "LatLng": [
                        -29.783,
                        24.774
                    ]
                },
                {
                    "LatLng": [
                        -29.841,
                        23.28
                    ]
                },
                {
                    "LatLng": [
                        -31.297,
                        23.346
                    ]
                }
            ]
        },
        "fillColor": "#32CD32"
    }
},
{
    "polyline": {
        "paths": {
            "MVCArray": [
                {
                    "LatLng": [
                        -29.459,
                        18.248
                    ]
                },
                {
                    "LatLng": [
                        -29.363,
                        25.082
                    ]
                },
                {
                    "LatLng": [
                        -28.44,
                        23.917
                    ]
                },
                {
                    "LatLng": [
                        -30.392,
                        23.983
                    ]
                }
            ]
        },
        "strokeColor": "#1E90FF"
    }
},
{
    "rectangle": {
        "bounds": {
            "LatLngBounds": [
                {
                    "LatLng": [
                        -31.597,
                        21.302
                    ]
                },
                {
                    "LatLng": [
                        -27.45,
                        21.632
                    ]
                }
            ]
        },
        "fillColor": "#4B0082"
    }
}
]
*/

function loadOverlay(map, saveData) {
  var loadData = {};
  var jsonData = document.getElementById(saveData).innerHTML;
  //var kmlData  = document.getElementById("savedata-kml").innerHTML;

  //console.debug(jsonData);
  if (jsonData != '') {
    loadData = JSON.parse(jsonData);
    
    for (i in loadData) {
  	  //console.debug(loadData[i]);
  	  if (loadData[i]['circle'] !== undefined) {
  		  shape = loadData[i]['circle'];
  		  point = new google.maps.LatLng(shape['center'].LatLng[0], shape['center'].LatLng[1]);
  		  var circle = new google.maps.Circle({
  			  "center": point,
  			  "radius": shape['radius'],
  			  "fillColor": shape['fillColor'],
  			  "strokeWeight": 0,
  			  "fillOpacity": 0.45
  		  });
  		  circle.setMap(map);
  	  }
  	  
  	  if (loadData[i]['polygon'] !== undefined) {
  		  shape = loadData[i]['polygon'];
  		  var path = [];
  		  for (j in shape['paths']['MVCArray']) {
  			  segment = shape['paths']['MVCArray'][j];
  			  point = new google.maps.LatLng(segment.LatLng[0], segment.LatLng[1]);
  			  path.push(point);
  		  }
  		  paths = new google.maps.MVCArray(path);
  		  var polygon = new google.maps.Polygon({
  			  "paths": paths,
  			  "fillColor": shape['fillColor'],
  			  "strokeWeight": 0,
  			  "fillOpacity": 0.45
  		  });
  		  polygon.setMap(map);
  	  }
  	  
  	  if (loadData[i]['polyline'] !== undefined) {
  		  shape = loadData[i]['polyline'];
  		  var path = [];
  		  for (j in shape['path']['MVCArray']) {
  			  segment = shape['path']['MVCArray'][j];
  			  point = new google.maps.LatLng(segment.LatLng[0], segment.LatLng[1]);
  			  path.push(point);
  		  }
  		  paths = new google.maps.MVCArray(path);
  		  var polyline = new google.maps.Polyline({
  			  "path": paths,
  			  "strokeColor": shape['strokeColor']
  		  });
  		  //console.debug(polyline);
  		  polyline.setMap(map);
  	  }
  	  
  	  if (loadData[i]['rectangle'] !== undefined) {
  		  shape = loadData[i]['rectangle'];
  		  point_tr = new  google.maps.LatLng(shape['bounds'].LatLngBounds[0].LatLng[0], shape['bounds'].LatLngBounds[0].LatLng[1]);
  		  point_br = new  google.maps.LatLng(shape['bounds'].LatLngBounds[1].LatLng[0], shape['bounds'].LatLngBounds[1].LatLng[1]);
  	      bounds = new  google.maps.LatLngBounds(point_tr, point_br);
  	      var rectangle = new google.maps.Rectangle({
  			  "bounds": bounds,
  			  "fillColor": shape['fillColor'],
  			  "strokeWeight": 0,
  			  "fillOpacity": 0.45
  		  });
  	      rectangle.setMap(map);
  	  }
    }
  }

  /*
  if (kmlData != '') {
    //var layer = new google.maps.KmlLayer('http://dev.snowball.co.za/wireframe/dea/test.kml?mvh5204&data='+kmlData.replace(/<(?:.|\n)*?>/gm, ''));
    //var layer = new google.maps.KmlLayer('http://dev.snowball.co.za/wireframe/dea/test.kml?mvh5203');
    var layer = new google.maps.KmlLayer('http://dev.snowball.co.za/dea/dynamic.kml?reserve=1');
    layer.setMap(map);
    //document.getElementById("savedata-kml").innerHTML = 'http://dev.snowball.co.za/wireframe/dea/test.kml?data='+kmlData.replace(/<(?:.|\n)*?>/gm, '');
  }
  */
}

function selectColor(color) {
  selectedColor = color;
  for (var i = 0; i < colors.length; ++i) {
    var currColor = colors[i];
    colorButtons[currColor].style.border = currColor == color ? '2px solid #EEE' : '2px solid #FFF';
  }

  // Retrieves the current options from the drawing manager and replaces the
  // stroke or fill color as appropriate.
  var polylineOptions = drawingManager.get('polylineOptions');
  polylineOptions.strokeColor = color;
  drawingManager.set('polylineOptions', polylineOptions);

  var rectangleOptions = drawingManager.get('rectangleOptions');
  rectangleOptions.fillColor = color;
  drawingManager.set('rectangleOptions', rectangleOptions);

  var circleOptions = drawingManager.get('circleOptions');
  circleOptions.fillColor = color;
  drawingManager.set('circleOptions', circleOptions);

  var polygonOptions = drawingManager.get('polygonOptions');
  polygonOptions.fillColor = color;
  drawingManager.set('polygonOptions', polygonOptions);
}

function setSelectedShapeColor(color) {
  if (selectedShape) {
    if (selectedShape.type == google.maps.drawing.OverlayType.POLYLINE) {
      selectedShape.set('strokeColor', color);
    } else {
      selectedShape.set('fillColor', color);
    }
  }
}

function makeColorButton(color) {
  var button = document.createElement('span');
  button.className = 'color-button';
  button.style.backgroundColor = color;
  google.maps.event.addDomListener(button, 'click', function() {
    selectColor(color);
    setSelectedShapeColor(color);
  });

  return button;
}

function deleteButtonStyle(element, position){
  if (position == 'down') {
  element.style.backgroundPosition = '4px 4px';
  } else {
    element.style.backgroundPosition = '3px 3px';
  }
}


function setDrawingTools(controlDiv) {
 
 controlDiv.style.paddingTop = '8px';
 
 // Set CSS for the control border
 var controlUI = document.createElement('div');
 
 controlDiv.appendChild(controlUI);
 
 // Set CSS for the control interior
 //var controlText = document.createElement('div');
 //controlText.innerHTML = 'Test';
 //controlUI.appendChild(controlText);
 
 // Build a delete button
 var controlDelete = document.createElement('input');
 controlDelete.setAttribute("id", "delete-button");
 controlDelete.setAttribute("type", "button");
 controlDelete.setAttribute("title", "Delete the selected shape drawing");
 controlDelete.style.float = 'left';
 controlDelete.style.margin = '-2px 3px 0 0px';
 controlDelete.style.background = '#EEE url(/dea/plugins/google.maps/drawing/trash-icon.png) no-repeat 3px 3px';
 controlDelete.style.height = '26px';
 controlDelete.style.width  = '26px';
 controlUI.appendChild(controlDelete);
 
 // Build the Colour Palette
 var controlPalette = document.createElement('span');
 controlPalette.setAttribute("id", "color-palette");
 for (var i = 0; i < colors.length; ++i) {
     var currColor = colors[i];
     var colorButton = makeColorButton(currColor);
     controlPalette.appendChild(colorButton);
     colorButtons[currColor] = colorButton;
 }
 selectColor(colors[0]);
 controlUI.appendChild(controlPalette);
 
 // Setup event listeners
 google.maps.event.addDomListener(controlDelete, 'click', deleteSelectedShape);
 google.maps.event.addDomListener(controlDelete, 'mousedown', function(){deleteButtonStyle(controlDelete, 'down');});
 google.maps.event.addDomListener(controlDelete, 'mouseup', function(){deleteButtonStyle(controlDelete, 'up');});
}
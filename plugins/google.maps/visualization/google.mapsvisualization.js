// Shows layer, and ensures the checkbox is checked
function show(layer, map) {
	//console.log('showing '+layer);
	//console.debug(layer);
	layer.setMap(map);
	$(layer).attr('checked', true);
}

// Hides layer, and ensures the checkbox is cleared
function hide(layer) {
	//console.log('hiding '+layer);
	layer.setMap(null);
	$(layer).attr('checked', false);
}

// Has the layers chackbox been clicked?
function boxclick(box, layer, map) {
	if (box.checked) {
		show(layer, map);
	} else {
		hide(layer);
	}
}

function toggleLayersMenu(layerDiv, mode) {
	if (mode == 'show') {
		layerDiv.style.display = 'block';
	} else {
		layerDiv.style.display = 'none';
	}
}

function setLayerControls(controlDiv, mapsEngineLayer, map) {
	controlDiv.style.paddingTop = '5px';

	// Set CSS for the control border
	var controlUI = document.createElement('div');
	controlUI.style.position = 'absolute';
	controlUI.style.right = 0;
	controlUI.style.backgroundColor = '#FFF';
	controlUI.style.fontFamily = '';
	controlUI.style.fontSize = '13px';
	controlUI.style.padding = '1px 6px';
	controlUI.style.border = '1px solid #717B87';
	controlUI.style.boxShadow = 'rgba(0, 0, 0, 0.398438) 0px 2px 4px';
	controlUI.style.minWidth = '29px';
	controlUI.style.cursor = 'pointer';
	controlUI.style.zIndex = '10000002';
	controlDiv.appendChild(controlUI);

	// Set CSS for the control interior
	var controlText = document.createElement('div');
	controlText.innerHTML = 'Layers';
	controlUI.appendChild(controlText);

	// Set CSS for the control sub-menu
	var controlDropdown = document.createElement('div');
	controlDropdown.style.position = 'absolute';
	controlDropdown.style.right = 0;
	controlDropdown.style.marginTop = '19px';
	controlDropdown.style.padding = '1px 6px';
	controlDropdown.style.backgroundColor = '#FFF';
	controlDropdown.style.fontFamily = '';
	controlDropdown.style.fontSize = '13px';
	controlDropdown.style.textAlign = 'left';
	controlDropdown.style.border = '1px solid #717B87';
	controlDropdown.style.boxShadow = 'rgba(0, 0, 0, 0.398438) 0px 2px 4px';
	controlDropdown.style.minWidth = '105px';
	controlDropdown.style.cursor = 'pointer';
	controlDropdown.style.zIndex = '10000000';
	controlDropdown.style.display = 'none';
	controlDiv.appendChild(controlDropdown);

	for (i in mapsEngineLayer) {
	 layer = mapsEngineLayer[i];
	 controlCheckWrapper = document.createElement('div');
	 controlCheckLabel = document.createElement('label');
	 controlCheckbox = document.createElement('input');
	 controlCheckbox.setAttribute('id', 'layer_000'+i);
	 controlCheckbox.setAttribute("type", "checkbox");
	 controlCheckbox.style.float = 'left';
	 if (layer.map) controlCheckbox.setAttribute('checked', 'checked');
	 controlCheckWrapper.style.float = 'left';
	 controlCheckLabel.style.cursor = 'pointer';
	 controlCheckLabel.style.unselectable  = true;
	 controlCheckLabel.innerHTML = layer.name;
	 controlDropdown.appendChild(controlCheckWrapper);
	 controlCheckWrapper.appendChild(controlCheckLabel);
	 controlCheckLabel.appendChild(controlCheckbox);
	 controlCheckbox.setAttribute('onclick', "boxclick(this, mapsEngineLayer[" + i +"], map);");
	 //google.maps.event.addDomListener(controlCheckbox, 'change', function(){boxclick(document.getElementById('layer_000'+i), mapsEngineLayer[i]);});
	}

	// Setup event listeners
	google.maps.event.addDomListener(controlDiv, 'mouseover', function(){toggleLayersMenu(controlDropdown, 'show');});
	google.maps.event.addDomListener(controlDiv, 'mouseout', function(){toggleLayersMenu(controlDropdown, 'hide');});
}
<?php
class Plugin_Google_Maps {
	
	private $dom;
	private $map_bf;
	private $map_af;
	private $options;
	private $libraries;
	private $layer = 0;
	private $markerclusterer;
	
	public $code;
	public $name;
	public $width;
	public $height;
	
	
	/**
	 * 
	 */
	public function __construct($parent=NULL) {
		
		$htm = Ashtree_Html_Page::instance();
		
		$htm->jss = ASH_PLUGINS . 'google.maps/google.maps.js';
		$htm->css = ASH_PLUGINS . 'google.maps/google.maps.css';
		
		$this->dom = (isset($parent)) ? $parent : new DOMDocument();
		
		// Set defaults
		$this->options['center'] = 'new google.maps.LatLng(-30.883369, 24.290771)';
		$this->options['zoom'] = '8';
		$this->options['mapTypeId'] = 'google.maps.MapTypeId.TERRAIN';
		$this->options['disableDefaultUI'] = 'false';
		$this->options['zoomControl'] = 'true';
		$this->options['scrollwheel'] = 'false';
		
		$this->map_af = array();
		$this->map_bf = array();

	}
	
	/**
	 * center, zoom, mapTypeId, disableDefaultUI, zoomControl, canvas;
	 */
	public function setOption($option, $value) {
		// Special cases
		switch($option) {
			case '': break;
			case 'center':     $this->options['center'] = "new google.maps.LatLng({$value})"; break;
			case 'mapTypeId':  $this->options['mapTypeId'] = "google.maps.MapTypeId.{$value}"; break;
			default: $this->options[$option] = $value;
		}	
	}
	
	/**
	 *
	 */
	public function setMap($map) {
		$this->map_af[] = $map;
	}
	
	/**
	 *
	 */
	public function setDrawing() {
		$htm = Ashtree_Html_Page::instance();
		
		$this->libraries[] = 'drawing';
		
		$htm->jss = ASH_PLUGINS . 'google.maps/drawing/google.mapsdrawing.js';
		$htm->css = ASH_PLUGINS . 'google.maps/drawing/google.mapsdrawing.css';
		
		$this->map_bf[] = "
				
					//loadOverlay(map);
			        
			        // Creates a drawing manager attached to the map that allows the user to draw
			        // lines, and shapes.
			        // Markers have been disabled: markerOptions: {draggable: true},
			        drawingManager = new google.maps.drawing.DrawingManager({
			          drawingMode: null,
			          drawingControlOptions: {
					  position: google.maps.ControlPosition.TOP_LEFT,
					  drawingModes: [
					      google.maps.drawing.OverlayType.POLYGON,
					      google.maps.drawing.OverlayType.POLYLINE
					    ]
					  },
			          polylineOptions: {
			            editable: true
			          },
			          rectangleOptions: polyOptions,
			          circleOptions: polyOptions,
			          polygonOptions: polyOptions,
			          map: map
			        });
			        
			        google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
			            if (e.type != google.maps.drawing.OverlayType.MARKER) {
			            // Switch back to non-drawing mode after drawing a shape.
			            drawingManager.setDrawingMode(null);
			
			            // Add an event listener that selects the newly-drawn shape when the user
			            // mouses down on it.
			            var newShape = e.overlay;
			            newShape.type = e.type;
			            recordedShape[e.type].push(e.overlay);
			            google.maps.event.addListener(newShape, 'click', function() {
			              setSelection(newShape);
			            });
			            setSelection(newShape);
			            saveOverlay();
			          }
			        });
		
			        // Clear the current selection when the drawing mode is changed, or when the
			        // map is clicked.
			        google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
			        //google.maps.event.addDomListener(document.getElementById('body'), 'click', function(){setScrollFocus(map, false);});
			        google.maps.event.addListener(map, 'click', function(e){
			        	clearSelection();
			        	saveOverlay();
			        	setScrollFocus(map, true);
			        	e.stop();
					});   
			        
			        //google.maps.event.addDomListener(document.getElementById('save-button'), 'click', saveOverlay);
			
			        //buildColorPalette();
			        
			        // Place custom controls on the map
			        var customDrawingToolsDiv = document.createElement('div');
			        var customDrawingTools = new setDrawingTools(customDrawingToolsDiv);
			        
			        map.controls[google.maps.ControlPosition.TOP_LEFT].push(customDrawingToolsDiv);
		";
		
		$dom = $this->dom;
		
		$json_div = $dom->createElement('div');
		$json_div->setAttribute('id', 'savedata-json');
		$dom->appendChild($json_div);
		
		$kml_div = $dom->createElement('div');
		$kml_div->setAttribute('id', 'savedata-kml');
		$dom->appendChild($kml_div);
	}
	
	/**
	 *
	 */
	public function setVisualization() {
		
		$htm = Ashtree_Html_Page::instance();
		
		$this->libraries[] = 'visualization';
		
		$htm->jss = ASH_PLUGINS . 'google.maps/visualization/google.mapsvisualization.js';
		
		$this->map_bf[] = "
			 google.maps.event.addListener(map, 'click', function(e){
		        	map.setOptions({'scrollwheel': true});
		        	e.stop();
				});   
			
		";
		
		$this->map_af[] = "
			
			// Place custom controls on the map
	        var customLayersDiv = document.createElement('div');
	        var customLayers = new setLayerControls(customLayersDiv, mapsEngineLayer, map);
		        
  			map.controls[google.maps.ControlPosition.TOP_RIGHT].push(customLayersDiv);
		";
	}
	
	
	/**
	 *
	 */
	public function setLayer($name, $mapid, $layerkey) {
		$this->map_bf[] = "
			mapsEngineLayer[{$this->layer}] = new google.maps.visualization.MapsEngineLayer({
    			mapId: '{$mapid}',
				layerKey: '{$layerkey}',
    			map: map,
    			name: '{$name}'
  			});
		";
		
		$this->layer++;
	}
	
	/**
	 *
	 */
	public function setMarkerClusterer() {
		
		$this->markerclusterer = true;
		
		/*$this->map_bf[] = "
			var mc = new MarkerClusterer(map);
		";*/
	}
	
	/**
	 * 
	 */
	public function loadDrawing($drawing) {
		$htm = Ashtree_Html_Page::instance();
		
		$this->map_af[] = "
			loadOverlay(map, '{$drawing}');
		";
	}
	
	/**
	 * 
	 */
	function loadKML($kml) {
		$this->map_af[] = "
			var layer = new google.maps.KmlLayer('{$kml}');
			layer.setMap(map);
		";
	}
						
	/**
	 *
	 */
	public function __invoke($print=TRUE) {
		
		$htm = Ashtree_Html_Page::instance();
		
		$library_sav = @implode(',', $this->libraries);
		$htm->jss = "//maps.googleapis.com/maps/api/js?v=3&key={$this->code}&region=ZA&sensor=false&libraries={$library_sav}";
		if ($this->markerclusterer) $htm->jss = ASH_PLUGINS . 'google.maps/markerclusterer/markerclusterer.js';
		
		$options = array();
		foreach($this->options as $opt=>$val) {
			$options[] = "{$opt}: {$val}";
		}
		$options_sav = implode(",\n\t\t\t\t", $options);
		$map_bf_sav = implode("\n", $this->map_bf);
		$map_af_sav = implode("\n", $this->map_af);
		
		$htm->javascript = "
			var map;
			var mapsEngineLayer = [];
		
			function initialize() {
				map = new google.maps.Map(document.getElementById('{$this->name}'), {{$options_sav}});
				
				{$map_bf_sav}
				{$map_af_sav}
			}
			google.maps.event.addDomListener(window, 'load', initialize);
		";
		
		$dom = $this->dom;
		
		$div = $dom->createElement('div');
		$div->setAttribute('id', $this->name);
		$div->setAttribute('style', "width:{$this->width};height:{$this->height};");

		$dom->appendChild($div);
		
		return $print ? $dom->saveHTML() : $dom;

	}
	
}
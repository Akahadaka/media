<?php 
/**
 * 
 */
class KML {
	protected $dom;
	protected $document;
	
	public function __construct() {
		$this->dom = new DOMDocument();
		$this->dom->formatOutput = true;
		$this->document = $this->dom->createElement('Document');

	}
	
	public function polygon($shape) {
		$paths     = $shape['paths']['MVCArray'];
		$fillcolor = str_replace('#', '99', $shape['fillColor']);
		
		$path_str = array();
		
		$placemark = $this->dom->createElement('Placemark');
		$polygon = $this->dom->createElement('Polygon');
		$boundary = $this->dom->createElement('outerBoundaryIs');
		$linearring = $this->dom->createElement('LinearRing');
		$coordinates = $this->dom->createElement('coordinates');
		
		foreach((array)$paths as $path) {
			$path_str[] = "{$path['LatLng'][1]},{$path['LatLng'][0]},0.0";
		}
		$path_str[] = "{$paths[0]['LatLng'][1]},{$paths[0]['LatLng'][0]},0.0";
		$coordinates->nodeValue = implode(' ', $path_str);
		
		$linearring->appendChild($coordinates);
		$boundary->appendChild($linearring);
		$polygon->appendChild($boundary);
		$placemark->appendChild($polygon);
		
		$style = $this->dom->createElement('Style');
		$polystyle = $this->dom->createElement('PolyStyle');
		$color = $this->dom->createElement('color');
		$color->nodeValue = $fillcolor;
		$outline = $this->dom->createElement('outline');
		
		$polystyle->appendChild($color);
		$polystyle->appendChild($outline);
		$style->appendChild($polystyle);
		
		$placemark->appendChild($style);
		
		return $placemark;
	}
	
	public function polyline($shape) {
		$paths     = $shape['path']['MVCArray'];
		$fillcolor = str_replace('#', 'FF', $shape['strokeColor']);
		
		$path_str = array();
		
		$placemark = $this->dom->createElement('Placemark');
		$linestring = $this->dom->createElement('LineString');
		$coordinates = $this->dom->createElement('coordinates');
		
		foreach((array)$paths as $path) {
			$path_str[] = "{$path['LatLng'][1]},{$path['LatLng'][0]},0.0";
		}
		$coordinates->nodeValue = implode(' ', $path_str);
		
		$linestring->appendChild($coordinates);
		$placemark->appendChild($linestring);
		
		$style = $this->dom->createElement('Style');
		$linestyle = $this->dom->createElement('LineStyle');
		$color = $this->dom->createElement('color');
		$color->nodeValue = $fillcolor;
		$width = $this->dom->createElement('width');
		$width->nodeValue = 2;
		
		$linestyle->appendChild($color);
		$linestyle->appendChild($width);
		$style->appendChild($linestyle);
		
		$placemark->appendChild($style);
		
		return $placemark;
	}
	
	public function placemark($fragment) {
		$this->document->appendChild($fragment);
	}
	
	public function kml() {
	
		$kml = $this->dom->createElement('kml');
		$kml->setAttribute('xmlns', 'http://www.opengis.net/kml/2.2');
	
		$kml->appendChild($this->document);
		$this->dom->appendChild($kml);
	
		//echo dump(htmlentities($this->dom->saveXML()), 1);
	
		return $this->dom->saveXML();
	}
}





/**
 *
 */
class placemark extends KML {

	
	public function circle() {
		$output = "
		  <Placemark>\n
		    <name>circle</name>\n
			<visibility>1</visibility>\n
			<Style>\n
			  <geomColor>ff0000ff</geomColor>\n
			     <geomScale>1</geomScale>\N
			  </Style>
		";
		
		$output .= $this->coords;
	}
	
	
}

/**
 * source: http://dev.bt23.org/keyhole/circlegen/output.phps
 */
class circle extends KML {
	private $center_x;
	private $center_y;
	private $radius;
	
	private $center_x_rad;
	private $center_y_rad;
	private $radius_rad;
	
	public function __contstruct($cx, $cy, $r) {
		$this->center_x = $cx;
		$this->center_y = $cy;
		$this->radius = $r;
		
	}
	
	private function _to_radians(){
		$this->center_x_rad = deg2rad($this->center_x);
		$this->center_y_rad = deg2rad($this->center_y);
		$this->radius_rad = $this->radius/6378137;
	}
	
	public function kml(){
		$output = "<LineString>\n<coordinates>\n";
		for($i=0; $i<=360; $i++) {
			$radial = deg2rad($i);
			$lat_rad = asin(sin($lat1)*cos($d_rad) + cos($lat1)*sin($d_rad)*cos($radial));
			$dlon_rad = atan2(sin($radial)*sin($d_rad)*cos($lat1),cos($d_rad)-sin($lat1)*sin($lat_rad));
			$lon_rad = fmod(($long1+$dlon_rad + M_PI), 2*M_PI) - M_PI;
			$output .= rad2deg($lon_rad).",".rad2deg($lat_rad).",0 ";
		}
		$output .= "</coordinates>\n</LineString>";
		
		return $output;
	}
}
?>
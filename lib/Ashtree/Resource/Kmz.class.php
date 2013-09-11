<?php
/**
 * Comments go here
 *
 * @package Ashtree_Resource_Kmz
 * @author andrew.nash
 * @version 1.0
 * 
 * @param
 * 
 * TODO
 *
 */

/**
 * constants
 */

class Ashtree_Resource_Kmz 
{
	
	/**
	 * private
	 */
	private $params = array();
	
	private $debug;
	
	private $debugmode;
	
	/**
	 * protected
	 */
	
	/**
	 * public
	 */
	
	/**
	 * Comments go here
	 *
	 * @param
	 * 
	 * TODO
	 *
	 */
	public function __construct()               //Line comments
	{
		$this->debug = new Ashtree_Common_Debug(__CLASS__);
		
		$this->output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$this->output .= "<kml xmlns=\"http://www.opengis.net/kml/2.2\" xmlns:gx=\"http://www.google.com/kml/ext/2.2\">\n";
		$this->output .= "<Document>\n";

		$this->params['style']['kml'] = "";
		$this->params['folder']['kml'] = "";
		$this->params['placemark']['kml'] = "";
		$this->params['placemark']['icon']['size'] = "0.8";
		$this->params['extendeddata']['kml'] = "";
		$this->params['folder_icon'] = FALSE;
		
		$this->debugmode = (isset($_GET['debugmode'])) ? is($_GET['debugmode']) : FALSE;
	} //method __construct
	

	/**
	 * Magic get with $params private array
	 *
	 * @param $key
	 * @return $params[$key]
	 * 
	 * TODO
	 *
	 */
	public function __get($key)                  //Line comments
	{
		return array_key_exists($key, $this->params) ? $this->params[$key] : 0;
	} //method __get
	

	/**
	 * Magic set with $params private array
	 *
	 * @param $key
	 * @param $value
	 * 
	 * TODO
	 *
	 */
	public function __set($key, $value)           //Line comments
	{
		$this->params[$key] = $value;
	} //method __set
	

	/**
	 * Magic isset to see if $key is in array and has a value
	 *
	 * @param $key
	 * @return Boolean
	 * 
	 * TODO
	 *
	 */
	public function __isset($key)                  //Line comments
	{
		return isset($this->params[$key]);
	} //method __isset
	

	/**
	 * Magig unset removes the $key and corresponding value from the array
	 *
	 * @param $key
	 * 
	 * TODO
	 *
	 */
	public function __unset($key)                  //Line comments
	{
		unset($this->params[$key]);
	} //method __unset
	
	
	/**
	 * Magic object to string returns some string value defined within the object
	 *
	 * @example $obj = new Object(); echo $obj;
	 *
	 * @return String
	 * TODO
	 *
	 */
	public function __toString()                     //Line comments
	{
		return "";
	} //method __toString
	

	/**
	 * Comments go here
	 *
	 * @param
	 * 
	 * TODO
	 *
	 */
	public function __destruct()                      //Line comments
	{
		/*if (!$this->is($_GET['debugmode']))
		{
			echo $this->invoke();
		}
		else
		{
			echo dump($this->invoke(), 1);
		}*/
	} //method __destruct
	

	
	/**
	 * invoke
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public function invoke()                   //Line comments
	{

	    $this->output .= $this->params['style']['kml'];
		$this->output .= ($this->params['folder']['kml']) ? $this->params['folder']['kml'] : $this->params['placemark']['kml'];
		$this->params['style']['kml'] = "";
		$this->params['folder']['kml'] = "";
		$this->params['placemark']['kml'] = "";
		$this->params['folder_icon'] = FALSE;
	} //method invoke

	
	public function setHeader($name='mapa', $type='kml')
	{
	    if (!$this->debugmode)
		{
		    if ($type == 'kmz')
		    {
		        header("Content-Type: application/zip");
                header("Content-disposition: attachment; filename={$name}.kmz");
		    }
		    else
		    {
		        header("Content-Type: application/vnd.google-earth.kml+xml");
			    header("Content-Disposition: attachment; filename={$name}.kml");
		    }
		}
	}

	/**
	 * setKmlBalloon
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public function setKmlBalloon($filename, $template=FALSE)                   //Line comments
	{
	    if ($template)
	    {
	        $this->params['style']['balloon'] = $filename;
	    }
	    else
	    {
    		$this->params['style']['filename'] = $filename;
    		if (!$this->params['style']['balloon'] = file_get_contents($filename))
    		{
    			$this->debug->title = "ERR:: Cannot include the balloon style for '{$this->name}'. File '{$filename}' was not found.";
    			return FALSE;
    		}
	    }
	} //method setKmlBalloon
	

	/**
	 * setKmlStyle
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public function setKmlStyle($suffix='')                   //Line comments
	{
		if (!$this->folder_icon)
		{
			$style_kml = <<<HEREDOC

    <Style id="{$this->name}_folder"> 
        <ListStyle> 
            <ItemIcon><href>{$this->icon}</href></ItemIcon>
        </ListStyle> 
    </Style> 
HEREDOC;
			$this->folder_icon = TRUE;
		}
		
		$name = ($suffix != '') ? $suffix : $this->name;
		
		$style_kml .= <<<HEREDOC

    <Style id="{$name}_normal">
        <BalloonStyle>
            <text><![CDATA[{$this->params['style']['balloon']}]]></text>
        </BalloonStyle> 
        <LabelStyle>
            <color>00ffffff</color>
            <scale>0</scale>		
        </LabelStyle>
        <IconStyle>
            <scale>{$this->params['icon_size']}</scale>
            <Icon><href>{$this->icon}</href></Icon>
        </IconStyle>
    </Style>
    <Style id="{$name}_highlight">
        <BalloonStyle>
            <text><![CDATA[{$this->params['style']['balloon']}]]></text>
        </BalloonStyle> 
		<LabelStyle>
            <scale>1.2</scale>
        </LabelStyle>  
        <IconStyle>
            <scale>{$this->params['icon_size']}</scale>
            <Icon><href>{$this->icon}</href></Icon>
        </IconStyle>
    </Style>
    <StyleMap id="{$name}">
        <Pair>
            <key>normal</key>
            <styleUrl>#{$name}_normal</styleUrl>
        </Pair>
        <Pair>
            <key>highlight</key>
            <styleUrl>#{$name}_highlight</styleUrl>
        </Pair>
    </StyleMap>
HEREDOC;
		if ($suffix)
		{
			$this->params['style']['kml'] .= $style_kml;
		}
		else
		{
			$this->params['style']['kml'] = $style_kml;
		}
		$this->params['icon_size'] = $this->params['placemark']['icon']['size'];
	}//method setKmlStyle
	
	
	/**
	 * setKmlFolder
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public function setKmlFolder($foldername=NULL)                   //Line comments
	{
		$this->params['folder']['name'] = ($foldername != NULL) ? $foldername : ucwords($this->name);
		$this->params['folder']['kml'] = <<<HEREDOC


    <Folder>
        <name>{$this->params['folder']['name']}</name>
		{$this->params['folder']['region']}
        {$this->params['placemark']['kml']}
        <styleUrl>#{$this->name}_folder</styleUrl>
    </Folder>
HEREDOC;
	}//method setKmlFolder
	
	
	/**
	 * setKmlPlacemark
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public function setKmlPlacemark($row, $prefix='', $suffix='')                   //Line comments
	{
		$this->params['placemark']['kml'] .= <<<HEREDOC
	
        <Placemark id="{$prefix}{$row['id']}">
            <name><![CDATA[{$row['name']}]]></name>
            <LookAt>
                <longitude>{$row['lon']}</longitude>
                <latitude>{$row['lat']}</latitude>
                <altitude>10000</altitude>
                <range>17000</range>
                <altitudeMode>relativeToGround</altitudeMode>
                <minAltitude>0</minAltitude> 
                <maxAltitude>0</maxAltitude>
            </LookAt>
            <NetworkLink>
                <open>1</open>
                <Style>
                    <ListStyle>
                        <listItemType>checkHideChildren</listItemType>
                    </ListStyle>
                </Style>
            </NetworkLink>
            <ExtendedData>
                 {$this->params['extendeddata']['kml']}
            </ExtendedData>     
            <styleUrl>#{$this->name}{$suffix}</styleUrl>
            <Point>
                <coordinates>{$row['lon']},{$row['lat']}</coordinates>
            </Point>
        </Placemark>
HEREDOC;
		$this->params['extendeddata']['kml'] = "";
	} //method setKmlPlacemark
	

	/**
	 * setExtendedData
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public function setExtendedData($name, $value)                   //Line comments
	{
		$this->params['extendeddata']['kml'] .= <<<HEREDOC

                 <Data name="{$name}">
                     <value><![CDATA[{$value}]]></value>
                 </Data>
HEREDOC;
		//$this->debug->message = dump($this->params['extendeddata']['kml'], 1);
	} //method setExtendedData


	/**
	 * Set the region area of the KML
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public function setKmlRegion($min, $max)                   //Line comments
	{
		$this->params['folder']['region'] = <<<HEREDOC

		<Region> 
		  <LatLonAltBox> 
			<north>39.80</north>
			<south>-39.93</south>
			<east>54.96</east>
			<west>-21.17</west>
		  </LatLonAltBox> 
		  <Lod>
			<minLodPixels>{$min}</minLodPixels>
			<maxLodPixels>{$max}</maxLodPixels>  
		  </Lod>
		</Region> 
HEREDOC;
	} //method copyAndPasteMe


	/**
	 * Set an overlay
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public function setOverlay($img)                   //Line comments
	{
		$this->output .= <<<HEREDOC

		<ScreenOverlay>
			<name>Legend</name>
			<Icon>
				<href>{$img}</href>
			</Icon>
			<overlayXY x="0" y="0" xunits="fraction" yunits="fraction"/>
			<screenXY x="0" y="0" xunits="fraction" yunits="fraction"/>
			<rotationXY x="0" y="0" xunits="fraction" yunits="fraction"/>
			<size x="0.17" y="1" xunits="fraction" yunits="fraction"/>
		</ScreenOverlay>
HEREDOC;
	} //method copyAndPasteMe	
	
	
	/**
	 * Set an overlay
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public function setResizeableOverlay($bg, $top, $bot)                   //Line comments
	{
		$this->output .= <<<HEREDOC

		<Folder>
            <name>Legend</name>
            <ScreenOverlay>
                <name>LegendTop</name>
                <Icon><href>{$bg}</href></Icon>
                <overlayXY x="0" y="1" xunits="fraction" yunits="fraction"/>
                <screenXY x="0" y="1" xunits="fraction" yunits="fraction"/>
                <rotationXY x="0" y="0" xunits="pixels" yunits="pixels"/>
                <size x="200" y="100" xunits="pixels" yunits="fraction"/>
            </ScreenOverlay>
            <ScreenOverlay>
                <name>LegendBottom</name>
                <Icon><href>{$bot}</href></Icon>
                <overlayXY x="0" y="0" xunits="fraction" yunits="fraction"/>
                <screenXY x="0" y="0" xunits="fraction" yunits="fraction"/>
                <rotationXY x="0" y="0" xunits="pixels" yunits="pixels"/>
                <size x="200" y="468" xunits="pixels" yunits="pixels"/>
            </ScreenOverlay>
            <ScreenOverlay>
                <name>LegendTop</name>
                <Icon><href>{$top}</href></Icon>
                <overlayXY x="0" y="1" xunits="fraction" yunits="fraction"/>
                <screenXY x="0" y="1" xunits="fraction" yunits="fraction"/>
                <rotationXY x="0" y="0" xunits="pixels" yunits="pixels"/>
                <size x="200" y="158" xunits="pixels" yunits="pixels"/>
            </ScreenOverlay>
        </Folder>
HEREDOC;
	} //method copyAndPasteMe

	/**
	 * getKml
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public function getKml()                   //Line comments
	{
		$this->output .= "\n</Document>\n</kml>";
		return $this->output;
	} //method getKml
	
	
	/**
	 * getKmz
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public function getKmz()                   //Line comments
	{
		$this->output .= $this->params['style']['kml'];
		$this->output .= ($this->params['folder']['kml']) ? $this->params['folder']['kml'] : $this->params['placemark']['kml'];
		$this->output .= "\n</Document>\n</kml>";
		return $this->output;
	} //method getKmz
	
	
	/**
	 * Copy and paste me
	 *
	 * @access
	 * @param
	 * @return
	 *
	 * TODO
	 *
	 */
	public function copyAndPasteMe()                   //Line comments
	{
	
	} //method copyAndPasteMe


} //class Ashtree_Resource_Kmz

?>

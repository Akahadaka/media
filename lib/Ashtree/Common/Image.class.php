<?php
/**
 * Class to handle image uploads, resizing and conversions
 *
 * @package Ashtree_Common_Image
 * @author andrew.nash
 * @version 1.0
 * 
 * @changelog
 */
 
class Ashtree_Common_Image
{
	//PRIVATE
	/**
	 * Array used to store all the magic members created on the fly
	 * @example $this->quickvar is equivalent to $rhis->params['quickvar']
	 * @param Array $_params
	 */
	private $_params = array(); 

	/**
	 * @param Object $_debug
	 */
	private $_debug;
	
	/**
	 * @param int $_width
	 */
	private $_width;
	
	/**
	 * @param int $_height
	 */
	private $_height;
	
	/**
	 * @param String $_error
	 */
	private $_error;


	//PROTECTED
	/**
	 * Comments go here
	 * @param
	 */


	// PUBLIC
	/**
	 * @param Array $fileinfo
	 */
	public $fileinfo = array();
	
	/**
	 * Comments go here
	 * @param
	 */
	public $filepath = 'upload/';
	
	/**
	 * Comments go here
	 * @param
	 */
	public $filename;
	
	/**
	 * Desired (max) width of the output image
	 * @param int $width
	 */
	public $width = 1024;
	
	/**
	 * Desired (max) height of the output image
	 * @param int $height
	 */
	public $height = 768;
	
	/**
	 * <box|fit> <center> <crop> <stretch>
	 * @param
	 */
	public $model = 'box';
	
	/**
	 * Comments go here
	 * @param
	 */
	public $source;
	
	/**
	 * Comments go here
	 * @param
	 */
	public $image;
	
	
	/**
	 * @param String $source
	 * @param String $destination
	 */
	public function __construct($source=NULL, $destination=NULL, $width=NULL, $height=NULL, $model=NULL)
	{
		$this->_debug = Ashtree_Common_Debug::instance();
		
		if ($width)  $this->width  = $width;
		if ($height) $this->height = $height;
		if ($model)  $this->model  = $model;
		
		if ($destination) 
		{
			$this->filepath = pathinfo($destination, PATHINFO_DIRNAME);
			$this->filename = pathinfo($destination, PATHINFO_BASENAME);
			
			$this->store($source);
		}
		else if ($source) 
		{
			$this->set_image($source);
		}
	}
	
	
	/**
	 * @param $key
	 */
	public function __get($key)
	{
		return array_key_exists($key, $this->_params) ? $this->_params[$key] : FALSE;
	}
	
	
	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value)
	{
		$this->_params[$key] = $value;
	}
	
	
	/**
	 * @param $key
	 */
	public function __isset($key)
	{
		return isset($this->_params[$key]);
	}
	
	
	/**
	 * @param $key
	 */	
	public function __unset($key)
	{
		unset($this->_params[$key]);
	}
	
	
	/**
	 * @param
	 */
	public function __destruct()
	{
	
	}
	
	/**
	 * Copy and paste me
	 * @access
	 * @param
	 * @return
	 */
	public function resize($model, $width=NULL, $height=NULL)
	{
		if ($width)  $this->width  = $width;
		if ($height) $this->height = $height;
		
		$scale['width']  = $this->width/$this->_width;
		$scale['height'] = $this->height/$this->_height;
		
		$dominant = ($scale['width'] >= $scale['height']) ? 'height' : 'width';
		
		//if ($scale[$dominant]) 
		
		switch($model)
		{
			case 'box' :
			case 'fit' :
				$w = $this->_width*$scale[$dominant];
				$h = $this->_height*$scale[$dominant];
				break;
		}
		
		$image = imageCreateTrueColor($w, $h);
		imageCopyResampled($image, $this->image, 0, 0, 0, 0, $w, $h, $this->_width, $this->_height);
		
		return $image;
	}
	
	/**
	 * thumbnail
	 * @access
	 * @param
	 * @return
	 */
	public function store($from=NULL, $to=NULL)
	{
		$done = FALSE;
		
		if ($from) 
		{
			if (!$this->set_image($from)) return array('success'=>FALSE, 'error'=>$this->_error);
		}
		
		$destination = ($to) ? $to : ((isset($this->filename)) ? $this->filepath.$this->filename.'.'.$this->fileinfo['extension'] : $this->filepath.$this->fileinfo['filename']);
		if (!substr_count($destination, ASH_ROOTPATH)) $destination = ASH_ROOTPATH . $destination;
		$source = str_replace(ASH_ROOTPATH, ASH_BASENAME, $destination);
		
		Ashtree_Common::file_force_contents($destination);
		
		/*
		// Action to perform if the file already exists
		if (file_exists($destination)) 
		{
			if (substr_count($destination, '['))
			{
				preg_match('/\[(\d+)\]/', $destination, $match);
				$count = $match[1];
				echo dump($match);
				$destination = preg_replace('/\/(.*)\[\d+\]\./i', '${1}[' . $count . '].');
			}
			else
			{
				$destination = preg_replace('/\/(.*)\.' . $this->fileinfo['extension'] . '/i', '${1}[1].' . $this->fileinfo['extension'], $destination);
			}
		}*/
		
		
        
        if (($this->_width > $this->width) || ($this->_height > $this->height)) $this->image = $this->resize('box');
		
		//echo "move_uploaded_file('{$this->fileinfo['filepath']}', '{$destination}');";
		//if (move_uploaded_file($this->fileinfo['filepath'], $destination)) return $destination;
		//else return FALSE;
        
        switch ($this->fileinfo["extension"]) 
		{
			case "gif" : $done = imageGIF($this->image, $destination);  break;
			case "jpg" :
			case "jpeg": $done = imageJPEG($this->image, $destination, 100); break;
			case "png" : $done = imagePNG($this->image, $destination);  break;
			case "pdf" : $done = imagePDF($this->image, $destination);  break;
		}//switch
		
		if ($done)
		{
			//unlink($this->fileinfo['filepath']);
			$this->source = $destination;
			return array('success'=>TRUE, 'source'=>$source);
		}
		else
		{
			return array('success'=>FALSE, 'error'=>'Could not store file ' . $destination);
		}
		
	}
	
	/**
	 * convert
	 * @access
	 * @param
	 * @return
	 */
	private function set_image($from=NULL)
	{
		if (is_array($from))
		{
			if ($from['error'])
			{
				switch($from['error'])
				{
					case 1: $this->_error = 'Server Error:: The server only allows uploading of file sizes up to ' . Ashtree_Common::byte_suffix(ini_get('upload_max_filesize')); break;
					case 2: $this->_error = 'Configuration Error:: The form only allows uploading of file sizes up to ' . Ashtree_Common::byte_suffix(ini_get('post_max_size')); break;
					case 3: $this->_error = 'Communication Error:: Upload could not be completed succesffully'; break;
					case 4: $this->_error = 'File not found: ' . $from['name']; break;
					case 6: $this->_error = 'Configuration Error:: Temp upload directory not specified '; break;
					case 7: $this->_error = 'Configuration Error:: Cannot write to temp upload directory ' . pathinfo($from['tmp_name'], PATHINFO_DIRNAME); break;
					case 8: $this->_error = 'Unrecognised filetype: ' . pathinfo($from['name'], PATHINFO_EXTENSION); break; 
				}
				
				return FALSE;
			}
			$this->fileinfo['filepath']  = $this->source = $from['tmp_name'];
			$this->fileinfo['filename']  = pathinfo($from['name'], PATHINFO_BASENAME);
			$this->fileinfo['extension'] = pathinfo($from['name'], PATHINFO_EXTENSION);
		}
		else
		{
			if ($from) $this->source = $from;
			$this->fileinfo['filepath']  = $this->source;
			$this->fileinfo['filename']  = pathinfo($this->source, PATHINFO_BASENAME);
			$this->fileinfo['extension'] = pathinfo($this->source, PATHINFO_EXTENSION);
		}
		
		switch ($this->fileinfo["extension"]) 
		{
			case "gif" : $this->image = imageCreateFromGIF($this->fileinfo['filepath']);  break;
			case "jpg" :
			case "jpeg": $this->image = imageCreateFromJPEG($this->fileinfo['filepath']); break;
			case "png" : $this->image = imageCreateFromPNG($this->fileinfo['filepath']);  break;
			case "pdf" : $this->image = imageCreateFromPDF($this->fileinfo['filepath']);  break;
		}//switch
		
		$this->_width  = imagesx($this->image);
        $this->_height = imagesy($this->image);
		
		$this->_debug->title = "INFO:: Image parameters set...";
		$this->_debug->dump  = $this->fileinfo;
		
		return TRUE;
	}
	
	/**
	 * thumbnail
	 * @access
	 * @param
	 * @return
	 */
	public function thumbnail($width, $height, $from=NULL)
	{
		$done = FALSE;
		
		if ($from) $this->source = $from;
		if (!$this->set_image($this->source)) return array('success'=>FALSE, 'error'=>$this->_error);
		
		$suffix = "_{$width}x{$height}.{$this->fileinfo['extension']}";
		$destination = pathinfo($this->fileinfo['filepath'], PATHINFO_DIRNAME) . '/thumbnail/' . pathinfo($this->fileinfo['filename'], PATHINFO_FILENAME) . $suffix;
		$source = str_replace(ASH_ROOTPATH, ASH_ROOTNAME, $destination);
		
		if (!file_exists($destination))
		{
			
			Ashtree_Common::file_force_contents($destination);

			$this->image = $this->resize('box', $width, $height);
			
			switch ($this->fileinfo["extension"]) 
			{
				case "gif" : $done = imageGIF($this->image, $destination);  break;
				case "jpg" :
				case "jpeg": $done = imageJPEG($this->image, $destination, 100); break;
				case "png" : $done = imagePNG($this->image, $destination);  break;
				case "pdf" : $done = imagePDF($this->image, $destination);  break;
			}//switch
			
			if ($done) return array('success'=>TRUE, 'source'=>$source);
			else array('success'=>FALSE, 'error'=>'Could not create thumbnail: ' . $destination);
		
		}
		
		return array('success'=>TRUE, 'source'=>$source);
	}

	
	/**
	 * Delete old thumbnail
	 * This call is necessary when uploading new images and generating new thumbnails
	 * Thumbnails only need to be generated the first time if they don't exist
	 * So uploading new images must delete old thumbnails
	 * @access public
	 * @param int $width
	 * @param int $height
	 * @param [String $from]
	 */
	public function clear($width, $height, $from=NULL)
	{
		if ($from) $this->source = $from;
		
		$suffix = "_{$width}x{$height}." . pathinfo($this->source, PATHINFO_EXTENSION);
		$destination = pathinfo($this->source, PATHINFO_DIRNAME) . '/thumbnail/' . pathinfo($this->source, PATHINFO_FILENAME) . $suffix;
	
		if (file_exists($destination)) unlink($destination);
	}
	
	/**
	 * trim
	 * @access
	 * @param
	 * @return
	 */
	public function trim()
	{
	
	}
	
	
	/**
	 * Copy and paste me
	 * @access
	 * @param
	 * @return
	 */
	public function copyAndPasteMe()
	{
	
	}
	
	
}
?>
<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
/*
====================================================================================================
 Author: Andy Hebrank
====================================================================================================
 This file must be placed in the system/expressionengine/third_party/pdf_thumbs folder in your ExpressionEngine installation.
 package 		PDF Thumbs (EE2 Version)
 copyright 		Copyright (c) 2013 Andy Hebrank
----------------------------------------------------------------------------------------------------
 Purpose: Thumbnails from uploaded PDFs
====================================================================================================
*/

if ( ! defined('PDF_THUMBS_VERSION') )
{
	include( PATH_THIRD . 'pdf_thumbs/config.php' );
}


$plugin_info = array(
	'pi_name'			=> 'PDF Thumbs',
	'pi_version'		=> PDF_THUMBS_VERSION,
	'pi_author'			=> 'Andy Hebrank',
	'pi_description'	=> 'Thumbnails from uploaded PDFs.',
	'pi_usage'			=> Pdf_thumbs::usage()
);

class Pdf_thumbs
{
	//---------- you can change the values for the following variables ----------
	/* Thumbnail output directory */
	// this is the actual server directory to your thumbnail cache directory
	private $thumb_dir = '/var/www/html/images/pdf_thumbs';
	// this is the absolute or relative browser URL to the same directory
	private $thumb_url = '/images/pdf_thumbs';

	/* The default quality for images saved to jpeg format.
	This script will first try to use the quality= parameter from the tag (highest priority),
	then try and use the 'ce_image_quality' item in config.php (medium priority),
	and then try the below $quality value if the other two are not present (lowest priority). */
	private $quality = 100;

	//---------- don't change anything below here ----------

	//plugin parameter
	private $valid_params = array('filename' => '', 'default' => '', 'height' => 100, 'width' => 100);

	var $return_data = null;

	function __construct() {
		//EE super global
		$this->EE =& get_instance();

		// set params
		foreach ($this->valid_params as $k => $v) {
			$this->valid_params[$k] = $this->EE->TMPL->fetch_param($k)? $this->EE->TMPL->fetch_param($k):$v;
		}

		$this->return_data = $this->generate_pdf();
	}


	function generate_pdf() {
		// check if file exists
		$input = $this->valid_params['filename'];
		if (!file_exists($input)) return (!empty($this->valid_params['default'])? $this->valid_params['default']:null);

		$outputname = sprintf("%s_%dx%d.jpg", 
			hash('md5', $input),
			$this->valid_params['width'],
			$this->valid_params['height']);
		$outputpath = $this->thumb_dir.'/'.$outputname;
		$outputurl = $this->thumb_url.'/'.$outputname;

		// already made? great!
		if (file_exists($outputpath)) return $outputurl;

		// convert
		$cmd = sprintf("convert \"%s[0]\" -quality %s -colorspace RGB -geometry %dx%d %s",
			$input, 
			$this->quality,
			$this->valid_params['width'],
			$this->valid_params['height'],
			$outputpath);
		exec($cmd, $output, $return_val);

		if ($return_val != 0) {
			return (!empty($this->valid_params['default'])? $this->valid_params['default']:null);
		}

		return $outputurl;
	}
	

	/**
	 * Simple plugin examples and link to documentation. Called by EE.
	 * @return string
	 */
	public static function usage() {
		ob_start();
?>
{exp:pdf_thumbs filename="{server_path}" height="100" width="100" default="/images/no-thumbnail-available.jpg"}

returns the URL of the generated image (if any)

Requires ImageMagick convert.

<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	} /* End of usage() function */

} /* End of class */
?>

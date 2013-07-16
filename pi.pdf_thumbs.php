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

License:
    CE Image is licensed under the Commercial License Agreement found at http://www.causingeffect.com/software/expressionengine/ce-image/license-agreement
	Here are a couple of specific points from the license to note again:
    * One license grants the right to perform one installation of CE Image. Each additional installation of CE Image requires an additional purchased license.
    * You may not reproduce, distribute, or transfer CE Image, or portions thereof, to any third party.
	* You may not sell, rent, lease, assign, or sublet CE Image or portions thereof.
	* You may not grant rights to any other person.
	* You may not use CE Image in violation of any United States or international law or regulation.
	The only exceptions to the above four (4) points are any methods clearly designated as having an MIT-style license. Those portions of code specifically designated as having an MIT-style license, and only those portions, will remain bound to the terms of that license.
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
	private $thumb_dir = '/home/rabiesal/public_html/images/pdf_thumbs';
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
		exec($cmd, &$output, &$return_val);

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
{exp:pdf_thumbs filename="{filesystem_path_to_pdf}" height="100" width="100"}

returns the URL of the generated image (if any)

Requires ImageMagick convert.

<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	} /* End of usage() function */

} /* End of class */
?>
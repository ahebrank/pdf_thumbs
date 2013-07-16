pdf_thumbs
==========

PDF thumbnailing for Expression Engine

ImageMagick ("convert") must be installed and be runnable by the web server.

You'll need to create the cache directory (e.g., images/pdf_thumbs), make sure it's web-writable, and set the server path and URL for that directory in the pi*.php file.

Usage:

{exp:pdf_thumbs filename="{server_path}" height="100" width="100" default="/images/no-thumbnail-available.jpg"}

The tag will be replaced by the URL of the generated image (if successful), the default image (if not), or null (if no default).

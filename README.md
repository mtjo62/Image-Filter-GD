App:       Image Filter GD
Version:   2.0
Author:    MT Jordan <mtjo62@gmail.com>
Copyright: 2026
License:   MIT

**********************************************************************************

Image Filter GD: Image class to apply effects filters to local and remote images

**********************************************************************************

Image Filter GD is PHP class that can apply image effects filters to local and 
remotely linked images. The class is applied using a JavaScript function or called
directly.

brighten: Lightens a darker image.
brush: Adds a slight dabbed, brushed effect to an image.
darken: Darkens an overly bright image.
edgedetect: Highlights borders between colors on an image. Similar to emboss.
emboss: Adds a stamped metal effect to an image.
flip: Flips an image vertically.
grayscale: Converts color image to black & white.
larger: Increase an image dimension by 2x.
mirror: Produces a mirrored reflection of an image.
negative: Produces a photo negative of an image.
pixelate: Pixilates an image.
sephia: Adds an aged photo effect to an image.
sharpen: Sharpens an blurred image.
sketch: Adds a sketched, comic book effect to an image.
smaller: Reduces an image's dimension size by half.
smooth: Slightly blurs an overly sharpened image.

*********************************************************************************

Image Filter GD Features:

    * Effects filters are non destructive to original image.

Image Filter GD Restrictions:

    * Only supports GIF, PNG and JPG/JPEG images.
    * Only the first frame of animated GIFs are processed.
  
Image Filter GD Requirements:

    * PHP 7.2+
    * Enabled GD extension
    * Enabled fopen wrappers or cURL extension

*********************************************************************************

Installation:

1. Upload imageFilter.php and image_filter.js to a readable directory. 

*********************************************************************************
For a standalone img tag

Usage: 

`<img src="http://mysite.com/path/to/imageFilter.php?file=http://mysite.com/path/to/image.png&filter=sharpen" alt="">`

*********************************************************************************
Using the image_filter.js file with a class name filter and a data attribute as the effect filter

Note: See filter.html for working example

Open image_filter.js and edit the url variable to: `http://mysite.com/path/to/imageFilter.php`

Add the following script include to the footer of your page or template:

`<script src="http://mysite.com/path/to/image_filter.js"></script>`

Usage:

`<img src="http://mysite.com/path/to/image.png" class="filter-gd" data-filter="sharpen" alt="">` 


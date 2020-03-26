<?php

/*
 * PHP QR Code encoder
 *
 * Common constants
 *
 */
// Encoding modes

define('QR_MODE_NUL', -1);
define('QR_MODE_NUM', 0);
define('QR_MODE_AN', 1);
define('QR_MODE_8', 2);
define('QR_MODE_KANJI', 3);
define('QR_MODE_STRUCTURE', 4);

// Levels of error correction.

define('QR_ECLEVEL_L', 0);
define('QR_ECLEVEL_M', 1);
define('QR_ECLEVEL_Q', 2);
define('QR_ECLEVEL_H', 3);

// Supported output formats

define('QR_FORMAT_TEXT', 0);
define('QR_FORMAT_PNG',  1);

/*
 * PHP QR Code encoder
 *
 * Config file, tuned-up for merged verion
 */

define('QR_CACHEABLE', false);       // use cache - more disk reads but less CPU power, masks and format templates are stored there
define('QR_CACHE_DIR', false);       // used when QR_CACHEABLE === true
define('QR_LOG_DIR', false);         // default error logs dir

define('QR_FIND_BEST_MASK', true);                                                          // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
define('QR_FIND_FROM_RANDOM', 2);                                                       // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
define('QR_DEFAULT_MASK', 2);                                                               // when QR_FIND_BEST_MASK === false

define('QR_PNG_MAXIMUM_SIZE',  1024);                                                       // maximum allowed png image width (in pixels), tune to make sure GD and PHP can handle such big images

/*
 * PHP QR Code encoder
 *
 * QR Code specifications
 *
 *
 */

define('QRSPEC_VERSION_MAX', 40);
define('QRSPEC_WIDTH_MAX',   177);

define('QRCAP_WIDTH',        0);
define('QRCAP_WORDS',        1);
define('QRCAP_REMINDER',     2);
define('QRCAP_EC',           3);

/*
 * PHP QR Code encoder
 *
 * Image output of code using GD2
 *
 */
define('QR_IMAGE', true);

/*
 * PHP QR Code encoder
 *
 * Input encoding class
 *
 *
 */
define('STRUCTURE_HEADER_BITS',  20);
define('MAX_STRUCTURED_SYMBOLS', 16);

/*
 * PHP QR Code encoder
 *
 * Masking
 *
 */
define('N1', 3);
define('N2', 3);
define('N3', 40);
define('N4', 10);

define('ROOT_PATH', dirname(__DIR__));

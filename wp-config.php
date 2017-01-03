<?php

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'evocrm_co_uk');

/** MySQL database username */
define('DB_USER', 'evocrmcouk');

/** MySQL database password */
define('DB_PASSWORD', 'kLk992Gz');

/** MySQL hostname */
define('DB_HOST', 'mysql.evocrm.co.uk');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'yAZdM?C^@iFnR)sr%?Py1DBJgLv`ngklsy%OS!yMk+t$VLMWF;B3GSWx3VL/5~Bh');
define('SECURE_AUTH_KEY',  'jL%3tPyw9Pa^E2h1P2;?i4bPS+3^~fOgXub^WDZ(ZN)m:UoFwT&??:A1C5VttY7/');
define('LOGGED_IN_KEY',    '?@a"8(3mT&Abq8f;nq^WzxcDg`hPrQcW5qcfua;IQ#E`W7bN;F)`~@jLwgEhx5pu');
define('NONCE_KEY',        '++OBHpF+z90#o9cKue68_ufBjJ)x2snpJmbqk+ezJ!l|kn4tH?d72R0Kqd7lmfTe');
define('AUTH_SALT',        'n_O09VK^ARNj1u"WKVRanx+d81|ianECHptK85coi#fJ~6zc;IY1V6*sWm^f(Icp');
define('SECURE_AUTH_SALT', '%t4PBxPz$zaKyU"Sv^CGB4iEnP3(Mpc@$PcBhOKC6~qo@MgNTcMMjAApCTyk^a$M');
define('LOGGED_IN_SALT',   'z%oeJJKj:DqUBpu1!a/FZA@w+#j5ePQX%J~wtuICMWc$:ndTtRSA/U5mr;f08g:%');
define('NONCE_SALT',       'st2mp3JoD*qS+&r"dev!RpsTExrP#UG&b)3(@0$RmgLm3u:YqoG$g7MtlyxxT$ox');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_8fimnf_';

/**
 * Limits total Post Revisions saved per Post/Page.
 * Change or comment this line out if you would like to increase or remove the limit.
 */
define('WP_POST_REVISIONS',  10);

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');


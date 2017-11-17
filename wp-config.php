<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'imarkcli_negotiation');

/** MySQL database username */
define('DB_USER', 'imarkcli_nat');

/** MySQL database password */
define('DB_PASSWORD', '666BelM}OMvq');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'O3xro/ra,HB?g~nsC|O!L`sQL(q]%VZTov(KA`C8:~JcP*^, :y#Ue,)$x;Ov{|}');
define('SECURE_AUTH_KEY',  '@V`ZRuMPR:@t<eZ80EQ4-$MFw6CgQp$jT5Va<#_7f_pB4[<[2}WQ/;y|s?L;ix6:');
define('LOGGED_IN_KEY',    'q^eyE6;GEd2M fnfE1hDf/E#1JKJ=n^qtY%G)ya OvUTp/ME>hXZaI$im0Ii]5Oc');
define('NONCE_KEY',        '*uJ[+G@HFKzNeM=[NF4<&*r }@R6QAx+UH9A&-Cv 31:Wd2Mr*~n=vmbt](#Z9t:');
define('AUTH_SALT',        'Y_ObHHOhZf}Zz+hP=@(f&@|>#r`v1lRzgj&9NBZc{56Mp-^V8Z.1.ZMa[q3j)Y4!');
define('SECURE_AUTH_SALT', '4D#$@Wdk1}bjv8MCc+-;c7u1dS^.){~V^v-I*@py~D8Qa.DytGyooYwwd|&puUbf');
define('LOGGED_IN_SALT',   '%;L1u.{L&_uRy6?yoPH3n[^i(^7Y), ]J.)PR05U{SmA`E9U&aESxPY0xOb-X1E|');
define('NONCE_SALT',       'uB:+(Io]}y`:pA]hwY?mZ/yT`+1$4PI{h.7biX;z8$kVhXP5Ku,fmh%Zx2:7PoC[');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'nat_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

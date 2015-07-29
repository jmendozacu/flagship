<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'zUqoSuySck');

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
define('AUTH_KEY',         'GjC68lS-I/[zO^0o.|xr%Ub(>Jh=0|>dl#a5%U}r&$CMW% VO!k(/j-u4-+GKbSa');
define('SECURE_AUTH_KEY',  'fJSHT>!q5?2YG^},-mf^G&;M ~B}Xt?]D@#CijIh([tA}OQnul9PIB aF|s.ip~w');
define('LOGGED_IN_KEY',    'J(+NuB -[@%5i-|yx>]-&aZp&/-)Yh+)#/AZ|)bZw36bH7P?7G!3+IC~u;%TVE+R');
define('NONCE_KEY',        'tC|[-=d1 JROzV]a{G|dWWHl*UYQIC;fJyu8x/%Aw!QJQY=C@Cc+)a5B~SFH!og@');
define('AUTH_SALT',        'KN{~&XR|jNZ:zE,a~x4G(K5Zg-)PNP-+_R[+L-#fM@Bf} +|uq[*E %aD*-qza:D');
define('SECURE_AUTH_SALT', 'Dn-gacL mNatkOy=b|~r*x=U{8IL:AHt^}|_-{K<t?2 -U1[;20tY_Pd|$y0*KL)');
define('LOGGED_IN_SALT',   'h-LQ4-T>rG-}.U:2!^>~|_+?K[<Fn1G;x$CwQ:p{KUivIqvB+38O,k7@hSW|<8pB');
define('NONCE_SALT',       '=8dhf(T|.[}-8pp~iY(UozZ}?Gt~WyDd|^]/5_MFr6(G!]yQ)$)|4x3JzB0yB>kn');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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

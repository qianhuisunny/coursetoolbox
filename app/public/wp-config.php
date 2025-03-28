<?php
define( 'WP_CACHE', true );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'r%73iI^21g#(.#eu%vjku*zP&%<ohF<gK9xVT^oO dGvD<RWU,o%g|/hUrbGWJ*r' );
define( 'SECURE_AUTH_KEY',   'I;KC:i??,nJSgvi/ya P 8CugN,$k|`!kG%.xesfG.hW{ctN+TIH,&~x 4C6iSqo' );
define( 'LOGGED_IN_KEY',     ' R~V8,a?M3xK=`.rmP{_0trMHQOj]~D(=KY4&ZZ}W/v?`c>TeG?g,WJ-AV~af7a*' );
define( 'NONCE_KEY',         '_kzfJT&98jQ9It$@!vNo<@5d}-V{+b<Lm4#<+wL se2(8bmp{T)U%/A^*FOIep`<' );
define( 'AUTH_SALT',         '4]=Ht?g75h<bfq5k<YnM<-(u;/[e5t|?up/-DxS4AYW]((;7!|@6mDjLt;IkJu!f' );
define( 'SECURE_AUTH_SALT',  '+cxkZ01Foq;)i7w=+S=l[45TDEma%<8BSK=[5[g.sD/v7cmZ+@<-6MINHP_%P&7C' );
define( 'LOGGED_IN_SALT',    'yAH#=/ yE%mrKv0}iLkXk{H;Kl*DQ]!u=vx6%bF4LP0t<p?DLa#I@ca;q-ax1/R(' );
define( 'NONCE_SALT',        ':-Z3<URaa&^r0z|VY1XdL+,F=Klj5)EJ({3ooU.s#,}TSm[G-QFUGQ:+7@*UHFO=' );
define( 'WP_CACHE_KEY_SALT', 'M$W]Sq{B#7e/PG2Jb/6$6=UbdXaAM%BDewk+Qfga<Y,/{92#J1wI4]`OGx_M0l?S' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

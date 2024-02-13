<?php
/**
 * Il file base di configurazione di WordPress.
 *
 * Questo file viene utilizzato, durante l’installazione, dallo script
 * di creazione di wp-config.php. Non è necessario utilizzarlo solo via web
 * puoi copiare questo file in «wp-config.php» e riempire i valori corretti.
 *
 * Questo file definisce le seguenti configurazioni:
 *
 * * Impostazioni del database
 * * Chiavi segrete
 * * Prefisso della tabella
 * * ABSPATH
 *
 * * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Impostazioni database - È possibile ottenere queste informazioni dal proprio fornitore di hosting ** //
/** Il nome del database di WordPress */
define( 'DB_NAME', 'progetto_wp' );

/** Nome utente del database */
define( 'DB_USER', 'root' );

/** Password del database */
define( 'DB_PASSWORD', '' );

/** Hostname del database */
define( 'DB_HOST', 'localhost' );

/** Charset del Database da utilizzare nella creazione delle tabelle. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Il tipo di collazione del database. Da non modificare se non si ha idea di cosa sia. */
define( 'DB_COLLATE', '' );

/**#@+
 * Chiavi univoche di autenticazione e di sicurezza.
 *
 * Modificarle con frasi univoche differenti!
 * È possibile generare tali chiavi utilizzando {@link https://api.wordpress.org/secret-key/1.1/salt/ servizio di chiavi-segrete di WordPress.org}
 *
 * È possibile cambiare queste chiavi in qualsiasi momento, per invalidare tutti i cookie esistenti.
 * Ciò forzerà tutti gli utenti a effettuare nuovamente l'accesso.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'iXYBu@3gb*0gHs&#l_C)Q!$;)y|i{`<+>JkzX,f7r{w+J!30VceD7#F106y-&s}n' );
define( 'SECURE_AUTH_KEY',  '&VB|1[@dkd>K:> Y+w2ID[H.Ch-+B)KpCqwV.-7#,N-M1[;H?^$ aak&6JO4}JLI' );
define( 'LOGGED_IN_KEY',    'XvpiyG_dAxv9+X<l~cTC-,)fc!S$~Y8;hetfy:6q>mv&XG0S. ;d=Lp^HTobM4?<' );
define( 'NONCE_KEY',        'RU@yia(Y$)A$.T;FU@W~bcJk_nF9AKw9]gE!y^nk&k822L7cW7t#m!c/<6`x; yv' );
define( 'AUTH_SALT',        '>B(#5)O6w+-}*K8C/s0Lep*eC419SQcs~/&V5p-B<7oaYT?UjcCV-U*>^c/iRa|7' );
define( 'SECURE_AUTH_SALT', '[3pH]-mtBc+;oDBWMLu;Q8z|/kq 7v2R2tXA,@0x%g9MmXWr`i{oo#q=M`uK>)8d' );
define( 'LOGGED_IN_SALT',   '66eP}cQ0Pa/pg> ]k:)hQ<K4*`~|L%+P+K;+v*^?&/f`.+}BA8gObx%L1R}%75Ic' );
define( 'NONCE_SALT',       ';,2d`w9DB`6/Xk0nn?+fsbF[Pe6QPxTWT]KfpmpDzKU|Mmz)x+.j}v[+:A71ANTs' );

/**#@-*/

/**
 * Prefisso tabella del database WordPress.
 *
 * È possibile avere installazioni multiple su di un unico database
 * fornendo a ciascuna installazione un prefisso univoco. Solo numeri, lettere e trattini bassi!
 */
$table_prefix = 'wp_';

/**
 * Per gli sviluppatori: modalità di debug di WordPress.
 *
 * Modificare questa voce a TRUE per abilitare la visualizzazione degli avvisi durante lo sviluppo
 * È fortemente raccomandato agli svilupaptori di temi e plugin di utilizare
 * WP_DEBUG all’interno dei loro ambienti di sviluppo.
 *
 * Per informazioni sulle altre costanti che possono essere utilizzate per il debug,
 * leggi la documentazione
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Aggiungere qualsiasi valore personalizzato tra questa riga e la riga "Finito, interrompere le modifiche". */



/* Finito, interrompere le modifiche! Buona pubblicazione. */

/** Path assoluto alla directory di WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Imposta le variabili di WordPress ed include i file. */
require_once ABSPATH . 'wp-settings.php';

<?php

/*
Definisco la costante per il locale in italiano
e per configurare la connessione al database.
Per vedere quali altre costanti ci sono a disposizione
puoi guardare il file p4a/p4a/config.php (ma queste 2 sono
le piu' usate)
*/

define("P4A_LOCALE", 'it_IT');
define("P4A_DSN", 'mysql://root:@localhost/sample_it');

// Includo p4a
require_once( dirname(__FILE__) . '/../../p4a.php' );

/*
Istanzio la mia applicazione con il metodo singleton
e richiamo il metodo  main dell'applicazione che viene
eseguito ogni volta.
L'applicazione deve essere una classe presente dentro la
dir objects dell'applicazione corrente e deve estendere p4a
Attenzione agli oggetti: dentro p4a tutti gli oggetti devono
essere assegnati usando "=&" e non "=" altrimenti vengono perse
le rerefenze; per questo stesso motivo quando creo una classe
tutti i metodi della classe che restituiscono un oggetto
devono essere preceduti dall'& quindi anche il costruttore della
classe stessa (guarda la classe sample_it per capire meglio)
*/

$sample_it =& sample_it::singleton("sample_it");
$sample_it->main();

?>
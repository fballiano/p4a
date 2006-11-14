<?php

/*
Questo file è utilizzato come file "di servizio", tutte le chiamate
verranno eseguite ad esso, nascondendo tutti gli altri file che
compongono l'applicazione. Qui definiamo le costanti atte a configurare
la partenza dell'applicazione e andiamo ad includere la libreria di sistema.
Al termine di queste poche operazioni non facciamo altro che istanziare e
lanciare l'applicazione.
*/

/*
Definisco la costante per il locale in italiano
e per configurare la connessione al database.
Per vedere quali altre costanti ci sono a disposizione
puoi guardare il file p4a/config.php (queste 2 sono
le più usate)
In questo caso la connessione al database non è impostata,
vedremo solo un piccolo esempio senza interrogazioni su
basi di dati.
*/

define("P4A_LOCALE", 'it_IT');
//define("P4A_DSN", 'mysql://root:@localhost/sample_it');

// Includo p4a
require_once dirname(__FILE__) . '/../../p4a.php';

/*
Istanzio l'applicazione con il metodo singleton
e richiamo il metodo main dell'applicazione che viene
eseguito ogni volta (click o reload).
L'applicazione deve essere una classe presente dentro la
dir objects dell'applicazione corrente e deve estendere p4a
Attenzione agli oggetti: dentro p4a tutti gli oggetti devono
essere assegnati usando "=&" e non "=" altrimenti vengono perse
le rerefenze; per questo stesso motivo quando creo una classe
tutti i metodi della classe che restituiscono un oggetto
devono essere preceduti dall'& quindi anche il costruttore della
classe stessa (guarda la classe sample_it per capire meglio)
*/

$sample_it =& p4a::singleton("Sample_It");
$sample_it->main();
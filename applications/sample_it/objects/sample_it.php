<?php

/*
Questa classe è il punto d'ingresso principale dell'applicazione,
potremmo definirla il contenitore di tutte le maschere, la classe
principale che gestire gli oggetti condivisi da tutta l'applicazione
e che lancia la prima maschera che dovrà essere visualizzata.
*/
class Sample_it extends P4A
{
	function Sample_It()
	{
		//Prima di tutto richiamo il costruttore di p4a
		parent::p4a();

		/*
		Per istanziare un oggetto è necessario usare il metodo
		build. Qualsiasi oggetto è in grado di costruire un
		qualsiasi altro oggetto "figlio" attraverso il metodo build
		che prende 2 parametri, il tipo di oggetto (p4a_menu)e il nome
		che ho deciso di assegnare all'oggetto (menu_master).
		Il "figlio" si posiziona in una variabile che si chiama
		come il nome (menu_master) all'interno del parent;
		nell'esempio potrei quindi accedere al menu all'interno
		della classe corrente digitando $this->menu_master o dovunque
		nel progetto così:
		$p4a =& sample_it::singleton();
		$p4a->menu_master....
		Il metodo build restituisce anche l'oggetto appena creato.
		*/
		$menu =& $this->build("p4a_menu", "menu_master");

		/*
		Il metodo additem del menu permette di aggiungere
		voci al menu stesso. Una voce aggiunta al menu la ritrovo
		all'interno della collection items del menu:
		$menu->items->utenti.
		Volessi creare sottovoci di utenti potrei fare così:
		$menu->items->utenti->addItem("sottovoce") e quindi accedervi
		con: $menu->items->utenti->items->sottovoce
		*/
		$menu->addItem("utenti");

		/*
		Una delle funzionalità principali di p4a è la
		possibilità di intercettare gli eventi. Qualsiasi oggetto
		di p4a è in grado di intercettare un evento attraverso
		il metodo intercept che prende tre parametri:
		l'oggetto che causa l'evento, l'envento che si intende
		intercettare e il metodo che deve venire richiamato al
		generarsi dell'evento;
		nell'esempio l'applicazione sta intercettando l'evento
		onClick della voce utenti del menu attraverso la
		funzione utenti_click
		*/
		$this->intercept($menu->items->utenti, "onClick", "utenti_click");

		/*
		openMask e' un metodo di p4a. La maschera è un oggetto che
		deve essere prensente dentro la dir objects dell'applicazione
		corrente e deve estendere la classe p4a_mask
		*/
		$this->openMask("login");
	}

	function utenti_click()
	{
		$this->openMask("utenti");
	}
}

?>
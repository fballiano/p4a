<?php

/*
Con questa maschera non facciamo altro che segnalare all'utente
che il login è avvenuto con successo.
D'altra parte questo è solo un esempio ;-)
*/
class Finished extends P4A_Mask
{
	function &Finito()
	{
		parent::P4A_Mask();

		$this->setTitle("Autenticazione riuscita");

		$this->build("p4a_message", "message");

		$this->build("p4a_button", "restart");
		$this->restart->setLabel("Ricomincia");
		$this->intercept($this->restart, "onClick", "restart");

		$this->build("p4a_frame", "frame");
		$this->frame->setWidth(300);

		$this->frame->anchorCenter($this->message);
		$this->frame->anchorCenter($this->restart);

		$this->display("main", $this->frame);
	}

	/*
	Questo metodo viene richiamato ad ogni accesso alla maschera.
	Qui andiamo a impostare il messaggio dell'oggetto "message" che,
	come abbiamo visto in precedenza, viene cancellato subito dopo
	il rendering. In questo caso sarebbe stato più corretto utilizzare
	l'oggetto "label" che si occupa di stampare un messaggio (come il
	message) ma non elimina il messaggio dopo il rendering. Siccome
	però label non supporta l'icona abbiamo deciso di rendere più
	carino il messaggio utilizzando appunto l'oggetto message.
	*/
	function main()
	{
		$this->message->setValue("Complimenti, ti sei appena autenticato!");
		$this->message->setIcon("info");

		/*
		Ricordiamoci di richiamare il main principale altrimenti non
		vedremo nulla sullo schermo.
		*/
		parent::main();
	}

	function restart()
	{
		$p4a =& p4a::singleton();

		/*
		Questo metodo distrugge le sessioni memorizzate sul server e permette
		il "riavvio" dell'applicazione.
		*/
		$p4a->restart();
	}
}

?>
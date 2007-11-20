<?php

/*
Lo scopo di questa classe è quello di visualizzare i classici
campi "username" e "passoword" e di gestire una basilare
autenticazione.
*/
class Login extends P4A_Mask
{
	function Login()
	{
		/*
		Richiamiamo il costruttore della classe estesa, questa
		linea è fondamentale!
		*/
		parent::P4A_Mask();

		/*
		Con questa istruzione possiamo impostare un titolo alla maschera,
		nel caso decidiate di non impostarlo, verrà automaticamente
		generato a partire dal nome della classe.
		*/
		$this->setTitle("Applicazione di esempio");

		/*
		Costruiamo un widget "message", questo elemento di interfaccia
		è in grado di visualizzare dei messaggi con un'icona. La sua
		particolarità è che il messaggio viene visualizzato e poi
		rimosso, molto utile per i classici messaggi di sistema.
		*/
		$this->build("p4a_message", "message");

		/*
		Ora istanziamo un "field", il classico campo di input per ricevere
		dati dall'utente.
		I field possono essere di molti tipi (text, textarea, rich_textarea,
		password, image, file), per cambiare tipo è necessario eseguire
		il metodo setType. Questa metodologia è molto comoda per variare il
		tipo di un field a runtime.
		*/
		$this->build("p4a_field", "username");

		/*
		Vorremmo anche che sia possibile, cliccando il tasto "invio", eseguire
		una funzione a nostra scelta. Per fare questo aggiungiamo al field
		l'azione "onReturnPress" e dichiariamo l'intercettazione.
		*/
		$this->username->addAction("onReturnPress");
		$this->intercept($this->username, "onReturnPress", "check");

		/*
		Creiamo il field per ricevere la password e impostiamolo di tipo
		"password", così facendo i dati saranno automaticamente criptati
		mediante algoritmo md5 (opzione disabilitabile) e il contenuto
		del campo sarà offuscato dal classico carattere "*".
		*/
		$this->build("p4a_field", "password");
		$this->password->setType("password");
		$this->password->addAction("onReturnPress");
		$this->intercept($this->password, "onReturnPress", "check");

		/*
		Alla nostra applicazione serve ancora un tasto "login", da cliccare
		per procedere oltre (anche se è possibile premere il tasto invio
		quando si ha il focus su un field)
		*/
		$this->build("p4a_button", "login");
		$this->intercept($this->login, "onClick", "check");

		/*
		Dopo aver costruito tutti gli elementi dobbiamo ancorarli
		(nelle posizioni desiderate) in un apposito contenitore.
		P4A ha diversi contenitori (sheet per l'ancoraggio a griglia
		tabellare, canvas per l'ancoraggio a posizione assoluta e relativa,
		frame per il moderno layout tableless, fieldset come estensione del frame)
		*/
		$this->build("p4a_frame", "frame");
		$this->frame->anchorCenter($this->message);
		$this->frame->anchor($this->username);
		$this->frame->anchor($this->password);
		$this->frame->newRow();
		$this->frame->anchorCenter($this->login);
		$this->frame->setWidth(300);

		/*
		Ora dobbiamo fare sapere al sistema come vogliamo visualizzare gli
		elementi di interfaccia. Il template della maschera di default
		contiene alcune zone diverse: menu, top, main. Desideriamo visualizzare
		nella parte centrale il frame principale:
		*/
		$this->display("main", $this->frame);

		/*
		Gestiamo anche il focus per l'ingresso nella maschera dicendo al
		sistema quale campo vogliamo sia selezionato.
		*/
		$this->setFocus($this->username);
	}

	function check()
	{
		$p4a = p4a::singleton();

		/*
		Recuperiamo i valori digitati dall'utente con il metodo getNewValue.
		Questo metodo differisce da getValue perché il field è grado di gestire
		due valori, quello con cui è stato creato e quello succesivo che viene
		modificato dall'utente. La getValue viene utilizzata ad esempio per annullare
		un'operazione.
		*/
		$username = $this->username->getNewValue();
		$password = $this->password->getNewValue();

		/*
		Verifichiamo molto semplicemente i dati (facendo attenzione al fatto
		che la password viene criptata in md5) e apriamo la maschera successiva
		in caso di successo oppure impostiamo un messaggio d'errore in caso
		di fallimento.
		*/
		if ($username == "root" and $password == md5("test")) {
			$p4a->openMask("finito");
		} else {
			$this->message->setValue("Username o password errati (prova username \"root\", password \"test\")");
		}
	}
}
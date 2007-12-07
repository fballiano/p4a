<?php

/*
This class is the main entrance point of the application,
we could call it the container of all the masks, the main
class that will manage all the shared object (application
globals) and that will call the first mask that will be
visualized.
*/
class Sample_En extends P4A
{
	function Sample_En()
	{
		// First of all let's call p4a constructor
		parent::p4a();

		// Setting the title of the whole application (window's title tag)
		$this->setTitle("Sample EN");

		/*
		To instance an object you've to use the "build" method.
		Every object can build children objects using the build
		method. That method takes 2 parameters, the object type
		(the class to instance, eg: p4a_menu) and the name that
		you want for it (eg: menu:master).
		New you can call the child object with $parent->son_name
		(eg: $this->menu_master)-
		The build method also return the created object.
		*/
		$menu =& $this->build("p4a_menu", "menu_master");

		/*
		Now we call the addItem method of the menu, we'll find
		the new item into the "items" collection:
		$menu->items->users.
		If you want to create children of "user" you would do
		it in this way:
		$menu->items->users->addItem("child") and than
		$menu->items->users->items->child
		*/
		$menu->addItem("users");

		/*
		One of the main feature of p4a is to intercept events.
		Every p4a object can itercept events using the "intercept"
		method, that takes three parameters:
		- the object that causes the event
		- the events that you want to intercept
		- the method that will be called
		In the example the application want to intercept the
		onClick events on the menu item, calling the users_click
		method.
		*/
		$this->intercept($menu->items->users, "onClick", "users_click");

		/*
		openMask is a p4a method. The mask is a class that must be
		in the objects directory and that have to extends the class
		p4a_mask.
		*/
		$this->openMask("login");
	}

	function users_click()
	{
		$this->openMask("users");
	}
}
DROP TABLE IF EXISTS users;
CREATE TABLE users (
	id int PRIMARY KEY AUTO_INCREMENT,
	user text,
	pass text,
	level int,
	default_mask text,
	name text,
	surname	text,
	country	text,
	address text,
	city	text,
	tel1	text,
	tel2	text,
	fax		text,
	mobile	text,
	email	text,
	note	text
);

# pass admin
INSERT INTO users (user,pass,level,default_mask) VALUES ("admin","21232f297a57a5a743894a0e4a801fc3","10","p4a_menu_mask");

DROP TABLE IF EXISTS menu;
CREATE TABLE menu (
	id int PRIMARY KEY AUTO_INCREMENT,
	parent_id		int,
	name			text,
	label			text,
	position 		int,
	visible 		tinyint DEFAULT 1,
	access_level	text,
	action			text,
	param1			text
);

INSERT INTO menu (id, name,position,access_level,action,param1)
	VALUES	(1, 'admin',1,10,'','');
INSERT INTO menu (id,parent_id,name,position,access_level,action)
	VALUES	(2, 1,'p4a_users',1,10,'openMask');
INSERT INTO menu (id,parent_id,name,position,access_level,action)
	VALUES	(3, 1,'p4a_menu_mask',1,10,'openMask');
--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `visible` tinyint(1) NOT NULL,
  PRIMARY KEY  (`brand_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` VALUES (1,'Brand 1',1),(2,'Brand 2',1),(3,'Brand 3',1);

--
-- Table structure for table `brands_brand_id_seq`
--

DROP TABLE IF EXISTS `brands_brand_id_seq`;
CREATE TABLE `brands_brand_id_seq` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=4;

--
-- Dumping data for table `brands_brand_id_seq`
--

INSERT INTO `brands_brand_id_seq` VALUES (3);

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `visible` tinyint(1) NOT NULL,
  PRIMARY KEY  (`category_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` VALUES (1,'Category 1',1),(2,'Category 2',1),(3,'Category 3',1);

--
-- Table structure for table `categories_category_id_seq`
--

DROP TABLE IF EXISTS `categories_category_id_seq`;
CREATE TABLE `categories_category_id_seq` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=4;

--
-- Dumping data for table `categories_category_id_seq`
--

INSERT INTO `categories_category_id_seq` VALUES (3);

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `product_id` varchar(50) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `model` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` int(11) NOT NULL,
  `picture` text,
  `is_new` tinyint(1) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`product_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `products`
--

INSERT INTO `products` VALUES ('1',1,1,'P4A sample model 1','10.00',2,'{p4a.png,/p4a.png,21299,image/png,550,204}',0,1,'<p>This is a description</p>'),('2',2,2,'P4A sample model 2','20.00',4,NULL,0,1,'<p>This is another description</p>');

--
-- Table structure for table `products_product_id_seq`
--

DROP TABLE IF EXISTS `products_product_id_seq`;
CREATE TABLE `products_product_id_seq` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=6;

--
-- Dumping data for table `products_product_id_seq`
--

INSERT INTO `products_product_id_seq` VALUES (2);
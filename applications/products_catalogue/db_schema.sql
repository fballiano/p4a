CREATE TABLE categories (
  category_id INTEGER UNSIGNED NOT NULL,
  parent_id INTEGER UNSIGNED NULL,
  description_en TEXT NOT NULL,
  description_it TEXT NOT NULL,
  visible BOOL NOT NULL,
  PRIMARY KEY(category_id)
)
TYPE=InnoDB;

CREATE TABLE brands (
  brand_id INTEGER UNSIGNED NOT NULL,
  description_en TEXT NOT NULL,
  description_it TEXT NOT NULL,
  visible BOOL NOT NULL,
  PRIMARY KEY(brand_id)
)
TYPE=InnoDB;

CREATE TABLE products (
  product_id VARCHAR(50) NOT NULL,
  brand_id INTEGER UNSIGNED NOT NULL,
  category_id INTEGER UNSIGNED NOT NULL,
  model TEXT NOT NULL,
  purchasing_price DECIMAL(10,2) NOT NULL,
  selling_price DECIMAL(10,2) NOT NULL,
  discount INTEGER UNSIGNED NOT NULL,
  delivery_cost_index INTEGER UNSIGNED NOT NULL,
  little_photo TEXT NULL,
  big_photo TEXT NULL,
  is_new BOOL NOT NULL,
  in_home BOOL NOT NULL,
  visible BOOL NOT NULL,
  description_en TEXT NOT NULL,
  description_it TEXT NOT NULL,
  PRIMARY KEY(product_id),
  INDEX products_FKIndex1(category_id),
  INDEX products_FKIndex2(brand_id),
  FOREIGN KEY(category_id)
    REFERENCES categories(category_id)
      ON DELETE NO ACTION
      ON UPDATE CASCADE,
  FOREIGN KEY(brand_id)
    REFERENCES brands(brand_id)
      ON DELETE NO ACTION
      ON UPDATE CASCADE
)
TYPE=InnoDB;



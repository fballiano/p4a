CREATE TABLE brands (
  brand_id INTEGER NOT NULL,
  description TEXT NOT NULL,
  visible BOOL NOT NULL,
  PRIMARY KEY(brand_id)
);

CREATE TABLE categories (
  category_id INTEGER NOT NULL,
  description TEXT NOT NULL,
  visible BOOL NOT NULL,
  PRIMARY KEY(category_id)
);

CREATE TABLE products (
  product_id VARCHAR(50) NOT NULL,
  brand_id INTEGER NOT NULL,
  category_id INTEGER NOT NULL,
  model TEXT NOT NULL,
  purchasing_price DECIMAL(10,2) NOT NULL,
  selling_price DECIMAL(10,2) NOT NULL,
  discount INTEGER NOT NULL,
  little_photo TEXT NULL,
  big_photo TEXT NULL,
  is_new BOOL NOT NULL,
  visible BOOL NOT NULL,
  description TEXT NOT NULL,
  PRIMARY KEY(product_id)
);
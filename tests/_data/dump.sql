PRAGMA foreign_keys = on;
BEGIN TRANSACTION;

-- Table: product
DROP TABLE IF EXISTS product;
CREATE TABLE product(
  id INTEGER PRIMARY KEY AUTOINCREMENT
);

-- Table: product_translation
DROP TABLE IF EXISTS product_translation;
CREATE TABLE product_translation(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL,
  language STING NOT NULL,
  title STRING NOT NULL,
  description TEXT NOT NULL
);

COMMIT TRANSACTION;
PRAGMA foreign_keys = on;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(160) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE articles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  excerpt TEXT NULL, -- extrait court pour les listes
  content_html MEDIUMTEXT NOT NULL,
  hero_image_path VARCHAR(255) NULL,
  hero_image_alt VARCHAR(180) NULL,

  meta_title VARCHAR(255) NULL,
  meta_description VARCHAR(255) NULL,
  canonical_url VARCHAR(255) NULL,
  meta_robots VARCHAR(50) NULL DEFAULT 'index,follow',

  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  deleted_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_articles_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE RESTRICT,

  INDEX idx_articles_status_published (status, published_at),
  INDEX idx_articles_category_published (category_id, status, published_at),
  INDEX idx_articles_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE article_slug_history (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  article_id INT UNSIGNED NOT NULL,
  old_slug VARCHAR(200) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_slughist_article
    FOREIGN KEY (article_id) REFERENCES articles(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

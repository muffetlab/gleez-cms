DROP TABLE IF EXISTS {posts};
CREATE TABLE {posts} (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  version BIGINT UNSIGNED NOT NULL DEFAULT '0',
  author BIGINT UNSIGNED NOT NULL DEFAULT '0',
  title TEXT NOT NULL,
  body LONGTEXT NOT NULL,
  teaser TEXT,
  status VARCHAR(20) NOT NULL DEFAULT 'draft',
  promote TINYINT UNSIGNED NOT NULL DEFAULT '0',
  moderate TINYINT UNSIGNED NOT NULL DEFAULT '0',
  sticky TINYINT UNSIGNED NOT NULL DEFAULT '0',
  type VARCHAR(20) NOT NULL DEFAULT 'post',
  format TINYINT UNSIGNED NOT NULL DEFAULT '1',
  created INT UNSIGNED NOT NULL DEFAULT '0',
  updated INT UNSIGNED NOT NULL DEFAULT '0',
  deleted INT UNSIGNED NOT NULL DEFAULT '0',
  pubdate INT UNSIGNED NOT NULL DEFAULT '0',
  password VARCHAR(20) DEFAULT '',
  comment TINYINT UNSIGNED NOT NULL DEFAULT '0',
  lang VARCHAR(12) NOT NULL DEFAULT 'en',
  layout VARCHAR(255) NOT NULL DEFAULT '',
  image VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY `post_type` (`type`),
  KEY `post_type_id` (`type`, `id`),
  KEY `post_type_moderate` (`type`, `moderate`),
  KEY `type_status_date` (`type`, `status`, `created`, `id`),
  KEY `post_frontpage` (`promote`, `status`, `sticky`, `created`),
  KEY `post_author` (`author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO {posts} (`id`, `version`, `author`, `title`, `body`, `teaser`, `status`, `promote`, `moderate`, `sticky`, `type`, `format`, `created`, `updated`, `pubdate`, `password`, `comment`, `lang`) VALUES
(1, 0, 2, 'Welcome to Gleez - Content Management System!', 'What Is Gleez CMS?\r\n\r\nGleez CMS is a user-friendly website content management system. With Gleez CMS you can easily build dynamic websites within a matter of minutes with just the click of your mouse! Maintain your web content, navigation and even limit what groups or specific users can access, from anywhere in the world with just a web browser! \r\n\r\nWith an emphasis on security and functionality, Gleez CMS is a professional and robust system suitable for any business or organization website. Built on the PHP programming language and the MySQL database, Gleez CMS delivers superb performance on any size website.\r\n\r\nDownload:\r\nwww.gleezcms.org', 'What Is Gleez CMS?\r\n\r\nGleez CMS is a user-friendly website content management system. With Gleez CMS you can easily build dynamic websites within a matter of minutes with just the click of your mouse! Maintain your web content, navigation and even limit what groups or specific users can access, from anywhere in the world with just a web browser!', 'publish', 0, 0, 0, 'page', 1, 1304978011, 1305488194, 1304978011, '', 0, 'en');

DROP TABLE IF EXISTS {tags};
CREATE TABLE {tags} (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(64) NOT NULL,
  type VARCHAR(64) NOT NULL DEFAULT 'post',
  count INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY type (type),
  UNIQUE KEY name_type (name, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {terms};
CREATE TABLE {terms} (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(64) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  image VARCHAR(255) DEFAULT NULL,
  type VARCHAR(64) NOT NULL DEFAULT 'post',
  pid INT UNSIGNED NOT NULL DEFAULT '0',
  lft INT UNSIGNED DEFAULT NULL,
  rgt INT UNSIGNED DEFAULT NULL,
  lvl INT UNSIGNED DEFAULT NULL,
  scp INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO {terms} (`id`, `name`, `description`, `image`, `type`, `pid`, `lft`, `rgt`, `lvl`, `scp`) VALUES
(1, 'Pages', 'Use to group pages on similar topics into categories.', NULL, 'page', 0, 1, 2, 1, 1),
(2, 'Blogs', 'Use to group articles on similar topics into categories.', NULL, 'blog', 0, 3, 4, 1, 1);

DROP TABLE IF EXISTS {comments};
CREATE TABLE {comments} (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  post_id BIGINT UNSIGNED NOT NULL DEFAULT '0',
  author BIGINT UNSIGNED NOT NULL DEFAULT '1',
  pid BIGINT UNSIGNED NOT NULL DEFAULT '0',
  title VARCHAR(128) DEFAULT NULL,
  body LONGTEXT NOT NULL,
  hostname VARCHAR(255) DEFAULT NULL,
  created INT UNSIGNED NOT NULL DEFAULT '0',
  updated INT UNSIGNED NOT NULL DEFAULT '0',
  status VARCHAR(20) NOT NULL DEFAULT 'draft',
  format TINYINT UNSIGNED NOT NULL DEFAULT '1',
  thread VARCHAR(255) DEFAULT NULL,
  type VARCHAR(20) NOT NULL DEFAULT 'post',
  guest_name VARCHAR(128) DEFAULT NULL,
  guest_email VARCHAR(128) DEFAULT NULL,
  guest_url VARCHAR(255) DEFAULT NULL,
  karma INT NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY comment_status_pid (`status`, pid),
  KEY comment_num_new (post_id, `status`, created, id, thread),
  KEY comment_author (author),
  KEY comment_post_type (post_id, `type`),
  KEY comment_type (`type`),
  KEY comment_post_id (`post_id`),
  FOREIGN KEY (`post_id`) REFERENCES {posts} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {users};
CREATE TABLE {users} (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(60) NOT NULL DEFAULT '',
  pass VARCHAR(128) NOT NULL DEFAULT '',
  mail VARCHAR(254) NOT NULL DEFAULT '',
  homepage VARCHAR(255) DEFAULT NULL,
  bio VARCHAR(800) DEFAULT NULL,
  nick VARCHAR(255) DEFAULT NULL,
  gender TINYINT UNSIGNED DEFAULT NULL,
  dob INT UNSIGNED NOT NULL DEFAULT '0',
  theme VARCHAR(255) DEFAULT NULL,
  signature VARCHAR(255) DEFAULT NULL,
  signature_format INT UNSIGNED DEFAULT '1',
  logins INT UNSIGNED NOT NULL DEFAULT '0',
  created INT UNSIGNED NOT NULL DEFAULT '0',
  updated INT UNSIGNED NOT NULL DEFAULT '0',
  deleted INT UNSIGNED NOT NULL DEFAULT '0',
  access INT UNSIGNED NOT NULL DEFAULT '0',
  login INT UNSIGNED NOT NULL DEFAULT '0',
  status TINYINT UNSIGNED NOT NULL DEFAULT '0',
  timezone VARCHAR(32) DEFAULT 'UTC',
  language VARCHAR(12) DEFAULT 'en_US',
  picture VARCHAR(255) DEFAULT NULL,
  init VARCHAR(254) DEFAULT NULL,
  `hash` CHAR(32) DEFAULT NULL,
  `data` LONGBLOB,
  PRIMARY KEY (id),
  UNIQUE KEY mail (mail),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO {users} (`id`, `name`, `pass`, `mail`, `nick`, `gender`, `dob`, `theme`, `signature`, `signature_format`, `logins`, `created`, `updated`, `login`, `status`, `timezone`, `language`, `picture`, `init`, `hash`, `data`) VALUES
(1, 'guest', '', 'guest@example.com', 'Guest', NULL, 0, '', '', NULL, 0, 0, 0, 0, 1, 'UTC', 'en_US', '', '', NULL, NULL),
(2, 'admin', 'f06b94fb0479f5596399aa962d9d9f8904d3e09a', 'webmaster@example.com', 'Gleez Administrator', NULL, 0, '', '', NULL, 1, 1393236002, 1393236002, 1393236002, 1, 'UTC', 'en_US', '', 'webmaster@example.com', NULL, NULL);

DROP TABLE IF EXISTS {messages};
CREATE TABLE {messages} (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  sender BIGINT UNSIGNED NOT NULL DEFAULT '0',
  recipient BIGINT UNSIGNED NOT NULL DEFAULT '0',
  subject VARCHAR(128) NOT NULL,
  body LONGTEXT NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'unread',
  format TINYINT UNSIGNED NOT NULL DEFAULT '1',
  created INT UNSIGNED NOT NULL DEFAULT '0',
  sent INT UNSIGNED NOT NULL DEFAULT '0',
  lang VARCHAR(12) NOT NULL DEFAULT 'en',
  PRIMARY KEY (id),
  KEY `message_status_date` (`status`, `created`, `id`),
  KEY `message_author` (`sender`),
  FOREIGN KEY (`sender`) REFERENCES {users} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {config};
CREATE TABLE {config} (
  `group_name` VARCHAR(128) NOT NULL,
  `config_key` VARCHAR(128) NOT NULL,
  `config_value` TEXT NOT NULL,
  PRIMARY KEY (`group_name`, `config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO {config} (`group_name`, `config_key`, `config_value`) VALUES
('site', 'admin_theme', 's:6:"cerber";'),
('site', 'date_first_day', 's:1:"1";'),
('site', 'date_format', 's:9:"l, F j, Y";'),
('site', 'date_time_format', 's:15:"l, F j, Y - H:i";'),
('site', 'front_page', 's:7:"welcome";'),
('site', 'maintenance_mode', 's:1:"0";'),
('site', 'mission', 's:0:"";'),
('site', 'offline_message', 's:0:"";'),
('site', 'seo_url', 's:1:"1";'),
('site', 'site_email', 's:19:"unknown@unknown.com";'),
('site', 'site_favicon', 's:24:"/media/icons/favicon.ico";'),
('site', 'site_logo', 's:22:"/media/images/logo.png";'),
('site', 'site_name', 's:9:"Gleez CMS";'),
('site', 'site_slogan', 's:49:"Light, Simple, Flexible Content Management System";'),
('site', 'theme', 's:6:"cerber";'),
('site', 'timezone', 's:12:"Asia/Kolkata";'),
('site', 'time_format', 's:5:"H:i:s";'),
('site', 'gleez_private_key', 's:72:"d6b7050911d1fa78e8f8eb648feacbb61a03805fa62126cbc303cab12dba77067655674c";'),
('site', 'auth_hash_key', 's:72:"d6b7050911d1fa78e8f8eb648feacbb61a03805fa62126cbc303cab12dba77067655674c";');

DROP TABLE IF EXISTS {menus};
CREATE TABLE {menus} (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(128) NOT NULL,
  name VARCHAR(128) NOT NULL,
  descp VARCHAR(255) DEFAULT NULL,
  image VARCHAR(255) DEFAULT NULL,
  url VARCHAR(255) DEFAULT NULL,
  params TEXT,
  active TINYINT UNSIGNED NOT NULL DEFAULT '1',
  pid INT UNSIGNED NOT NULL DEFAULT '0',
  lft INT UNSIGNED DEFAULT NULL,
  rgt INT UNSIGNED DEFAULT NULL,
  lvl INT UNSIGNED DEFAULT NULL,
  scp INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO {menus} (`id`, `title`, `name`, `descp`, `image`, `url`, `params`, `active`, `pid`, `lft`, `rgt`, `lvl`, `scp`) VALUES
(1, 'Main Menu', 'main-menu', 'The Main menu is used on many sites to show the major sections of the site, often in a top navigation bar.', NULL, NULL, '', 1, 0, 1, 18, 1, 1),
(2, 'Management', 'management', 'The Management menu contains links for administrative tasks.', NULL, NULL, '', 1, 0, 1, 30, 1, 2),
(3, 'Navigation', 'navigation', 'The Navigation menu contains links intended for site visitors. Links are added to the Navigation menu automatically by some modules.', NULL, NULL, '', 1, 0, 1, 2, 1, 3),
(4, 'User Menu', 'user-menu', 'The User menu contains links related to the user''s account, as well as the ''Log out'' link.', NULL, NULL, '', 1, 0, 1, 4, 1, 4),
(8, 'Home', 'home', '', 'fas fa-home', '', NULL, 1, 1, 2, 3, 2, 1),
(10, 'Pages', 'pages', '', 'fas fa-book', 'page', NULL, 1, 1, 4, 9, 2, 1),
(11, 'Add Page', 'add-page', '', NULL, 'page/add', NULL, 1, 10, 7, 8, 3, 1),
(12, 'Contact', 'contact', '', 'fas fa-envelope', 'contact', NULL, 1, 1, 18, 19, 2, 1),
(13, 'Administer', 'administer', '', 'fas fa-cog', 'admin', NULL, 1, 2, 2, 3, 2, 2),
(14, 'Menus', 'menus', '', 'fas fa-bookmark', 'admin/menus', NULL, 1, 2, 6, 7, 2, 2),
(15, 'Blogs', 'blogs', '', 'fas fa-beer', 'admin/blogs', NULL, 1, 2, 10, 11, 2, 2),
(16, 'Input Formats', 'input-formats', '', 'fas fa-magnet', 'admin/formats', NULL, 1, 2, 18, 19, 2, 2),
(17, 'Settings', 'settings', '', 'fas fa-cogs', 'admin/settings', NULL, 1, 2, 28, 29, 2, 2),
(18, 'Path Alias', 'path-alias', '', 'fas fa-link', 'admin/paths', NULL, 1, 2, 20, 21, 2, 2),
(19, 'Widgets', 'widgets', '', 'fas fa-asterisk', 'admin/widgets', NULL, 1, 2, 26, 27, 2, 2),
(20, 'Categories', 'taxonomy', '', 'fas fa-folder-open', 'admin/taxonomy', NULL, 1, 2, 14, 15, 2, 2),
(21, 'Tags', 'tags', '', 'fas fa-tags', 'admin/tags', NULL, 1, 2, 16, 17, 2, 2),
(22, 'Modules', 'modules', '', 'fas fa-list-alt', 'admin/modules', NULL, 1, 2, 4, 5, 2, 2),
(23, 'Users', 'users', '', 'fas fa-user', 'admin/users', NULL, 1, 2, 22, 23, 2, 2),
(24, 'Roles', 'roles', '', 'fas fa-lock', 'admin/roles', NULL, 1, 2, 24, 25, 2, 2),
(25, 'Pages', 'admin-pages', '', 'fas fa-book', 'admin/pages', NULL, 1, 2, 8, 9, 2, 2),
(26, 'Comments', 'admin-comment', '', 'fas fa-comment', 'admin/comments', NULL, 1, 2, 12, 13, 2, 2),
(27, 'Login', 'user-login', '', NULL, '', NULL, 1, 4, 2, 3, 2, 4),
(28, 'Blogs', 'blogs-1', '', 'fas fa-beer', '#', NULL, 1, 1, 10, 15, 2, 1),
(29, 'Add Blog', 'add-blog', '', NULL, 'blog/add', NULL, 1, 28, 13, 14, 3, 1),
(30, 'List', 'list', '', NULL, 'page', NULL, 1, 10, 5, 6, 3, 1),
(31, 'List', 'list-1', '', NULL, 'blog', NULL, 1, 28, 11, 12, 3, 1),
(32, 'Contact', 'contact-1', '', 'fas fa-envelope-o', 'contact', NULL, 1, 1, 16, 17, 2, 1);

DROP TABLE IF EXISTS {modules};
CREATE TABLE {modules} (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(128) NOT NULL,
  type ENUM('module', 'theme') NOT NULL DEFAULT 'module',
  active TINYINT UNSIGNED NOT NULL DEFAULT '0',
  weight INT NOT NULL DEFAULT '0',
  version VARCHAR(20) NOT NULL DEFAULT '1.0',
  path VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO {modules} (`id`, `name`, `type`, `active`, `weight`, `version`, `path`) VALUES
(1, 'user', 'module', 1, 0, '2.0', NULL);

DROP TABLE IF EXISTS {paths};
CREATE TABLE {paths} (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  source VARCHAR(255) NOT NULL,
  alias VARCHAR(255) NOT NULL,
  lang VARCHAR(12) NOT NULL DEFAULT 'und',
  route_name VARCHAR(255) DEFAULT NULL,
  route_directory VARCHAR(255) DEFAULT NULL,
  route_controller VARCHAR(255) DEFAULT NULL,
  route_action VARCHAR(255) DEFAULT NULL,
  route_id VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY id_lang_alias (lang, alias, id),
  KEY id_source (`source`),
  KEY id_lang_path (lang, `source`, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO {paths} (`id`, `source`, `alias`, `lang`, `route_name`, `route_directory`, `route_controller`, `route_action`, `route_id`) VALUES
(1, 'rss', 'rss.xml', 'und', 'rss', 'feeds', 'base', 'index', NULL),
(2, 'welcome', '<front>', 'und', 'default', NULL, 'welcome', 'index', NULL),
(3, 'user/login', 'login', 'und', 'user', NULL, 'user', 'login', NULL),
(4, 'page/list', 'pages', 'und', 'page', NULL, 'page', 'list', NULL),
(5, 'blog/list', 'blogs', 'und', 'blog', NULL, 'blog', 'list', NULL);

DROP TABLE IF EXISTS {permissions};
CREATE TABLE {permissions} (
  rid INT UNSIGNED NOT NULL,
  permission VARCHAR(64) NOT NULL,
  module VARCHAR(255) NOT NULL,
  PRIMARY KEY (rid, permission),
  KEY permission (permission)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO {permissions} (`rid`, `permission`, `module`) VALUES
(1, 'access content', 'content'),
(1, 'sending mail', 'contact'),
(3, 'access content', 'content'),
(3, 'access profiles', 'user'),
(3, 'create page', 'content'),
(3, 'edit own comment', 'comment'),
(3, 'edit own page', 'content'),
(3, 'edit profile', 'user'),
(3, 'post comment', 'comment'),
(3, 'view own unpublished content', 'content'),
(3, 'sending mail', 'contact'),
(4, 'access comment', 'comment'),
(4, 'access content', 'content'),
(4, 'access profiles', 'user'),
(4, 'administer comment', 'comment'),
(4, 'administer content', 'content'),
(4, 'administer logs', 'site'),
(4, 'administer page', 'content'),
(4, 'administer paths', 'site'),
(4, 'administer permissions', 'user'),
(4, 'administer site', 'site'),
(4, 'administer tags', 'site'),
(4, 'administer terms', 'site'),
(4, 'administer users', 'user'),
(4, 'change own username', 'user'),
(4, 'create page', 'content'),
(4, 'delete any page', 'content'),
(4, 'delete own page', 'content'),
(4, 'edit any page', 'content'),
(4, 'edit own comment', 'comment'),
(4, 'edit own page', 'content'),
(4, 'edit profile', 'user'),
(4, 'post comment', 'comment'),
(4, 'skip comment approval', 'comment'),
(4, 'view own unpublished content', 'content'),
(4, 'sending mail', 'contact');

DROP TABLE IF EXISTS {posts_versions};
CREATE TABLE {posts_versions} (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `version` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `author` BIGINT UNSIGNED NOT NULL DEFAULT '1',
  `title` TEXT NOT NULL,
  `body` LONGTEXT NOT NULL,
  teaser TEXT,
  status VARCHAR(20) NOT NULL DEFAULT 'draft',
  promote TINYINT UNSIGNED NOT NULL DEFAULT '0',
  moderate TINYINT UNSIGNED NOT NULL DEFAULT '0',
  sticky TINYINT UNSIGNED NOT NULL DEFAULT '0',
  type VARCHAR(20) NOT NULL DEFAULT 'post',
  format TINYINT UNSIGNED NOT NULL DEFAULT '1',
  created INT UNSIGNED NOT NULL DEFAULT '0',
  updated INT UNSIGNED NOT NULL DEFAULT '0',
  deleted INT UNSIGNED NOT NULL DEFAULT '0',
  pubdate INT UNSIGNED NOT NULL DEFAULT '0',
  password VARCHAR(20) DEFAULT '',
  comment TINYINT UNSIGNED NOT NULL DEFAULT '0',
  lang VARCHAR(12) NOT NULL DEFAULT 'en',
  layout VARCHAR(255) NOT NULL DEFAULT '',
  image VARCHAR(255) DEFAULT NULL,
  version_log VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `post_author` (`author`),
  FOREIGN KEY (`post_id`) REFERENCES {posts} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {posts_tags};
CREATE TABLE {posts_tags} (
  post_id BIGINT UNSIGNED NOT NULL DEFAULT '0',
  tag_id INT UNSIGNED NOT NULL DEFAULT '0',
  author BIGINT UNSIGNED NOT NULL DEFAULT '1',
  type VARCHAR(20) NOT NULL DEFAULT 'post',
  created INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (post_id, tag_id),
  KEY fk_tag_id (tag_id),
  FOREIGN KEY (`post_id`) REFERENCES {posts} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES {tags} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {posts_terms};
CREATE TABLE {posts_terms} (
  post_id BIGINT UNSIGNED NOT NULL DEFAULT '0',
  term_id INT UNSIGNED NOT NULL DEFAULT '0',
  type VARCHAR(20) NOT NULL DEFAULT 'post',
  parent_id INT UNSIGNED NOT NULL DEFAULT '0',
  term_order INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (post_id, term_id),
  KEY fk_term_id (term_id),
  KEY `type` (`type`),
  KEY posts_terms_ibfk_1 (post_id, `type`),
  FOREIGN KEY (`post_id`) REFERENCES {posts} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`term_id`) REFERENCES {terms} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {roles};
CREATE TABLE {roles} (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(32) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  special TINYINT UNSIGNED DEFAULT '0',
  deleted INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_name (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO {roles} (`id`, `name`, `description`, `special`) VALUES
(1, 'Anonymous', 'Guests can only view content. Anyone browsing the site who is not signed in is considered to be a "Guest".', 1),
(2, 'login', 'Login privileges, pending account confirmation.', 1),
(3, 'user', 'Member privileges, granted after account confirmation.', 1),
(4, 'admin', 'Administrative user, has access to everything.', 1);

DROP TABLE IF EXISTS {roles_users};
CREATE TABLE {roles_users} (
  user_id BIGINT UNSIGNED NOT NULL,
  role_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, role_id),
  KEY fk_role_id (role_id),
  FOREIGN KEY (`user_id`) REFERENCES {users} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES {roles} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO {roles_users} (`user_id`, `role_id`) VALUES
(1, 1),
(2, 2),
(2, 3),
(2, 4);

DROP TABLE IF EXISTS {sessions};
CREATE TABLE {sessions} (
  session_id VARCHAR(24) NOT NULL,
  last_active INT UNSIGNED NOT NULL,
  contents LONGTEXT NOT NULL,
  PRIMARY KEY (session_id),
  KEY last_active (last_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {user_tokens};
CREATE TABLE {user_tokens} (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  user_agent VARCHAR(40) NOT NULL,
  token VARCHAR(40) NOT NULL,
  `type` VARCHAR(100) DEFAULT NULL,
  created INT UNSIGNED NOT NULL DEFAULT '0',
  expires INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_token (token),
  KEY fk_user_id (user_id),
  FOREIGN KEY (`user_id`) REFERENCES {users} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {widgets};
CREATE TABLE {widgets} (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `module` VARCHAR(64) NOT NULL,
  `theme` VARCHAR(64) DEFAULT NULL,
  `status` TINYINT UNSIGNED DEFAULT '0',
  `region` VARCHAR(64) DEFAULT '-1',
  `weight` INT NOT NULL DEFAULT '0',
  `cache` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  `visibility` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  `pages` TEXT DEFAULT NULL,
  `roles` VARCHAR(255) DEFAULT NULL,
  `show_title` TINYINT UNSIGNED DEFAULT '1',
  `body` LONGTEXT,
  `format` TINYINT UNSIGNED NOT NULL DEFAULT '1',
  `icon` VARCHAR(255) DEFAULT 'fa-none',
  PRIMARY KEY (`id`),
  KEY `fk_name` (`name`),
  KEY `fk_module` (`module`),
  KEY `fk_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO {widgets} (`id`, `name`, `title`, `module`, `theme`, `status`, `region`, `weight`, `cache`, `visibility`, `pages`, `roles`, `show_title`, `body`, `format`, `icon`) VALUES
(1, 'admin/donate', 'Donate', 'gleez', NULL, 1, 'right', -5, 0, 0, '', '4', 1, NULL, 1, 'fas fa-gift'),
(2, 'menu/main-menu', 'Main Menu', 'gleez', NULL, 1, '-1', -3, 0, 0, NULL, NULL, 1, NULL, 0, 'fas fa-retweet'),
(3, 'menu/management', 'Management', 'gleez', NULL, 1, 'right', -2, 0, 0, '', '4', 1, NULL, 0, 'fas fa-tachometer'),
(4, 'menu/navigation', 'Navigation', 'gleez', NULL, 0, '-1', -6, 0, 0, NULL, NULL, 1, NULL, 0, 'fas fa-asterisk'),
(5, 'menu/user-menu', 'User Menu', 'gleez', NULL, 0, '-1', -5, 0, 0, NULL, NULL, 1, NULL, 0, 'fa-none'),
(6, 'admin/welcome', 'Welcome', 'gleez', NULL, 1, 'dashboard', -6, 0, 0, NULL, NULL, 1, NULL, 0, 'fas fa-flag'),
(7, 'admin/info', 'System', 'gleez', NULL, 1, 'dashboard', -3, 0, 0, NULL, NULL, 1, NULL, 0, 'fas fa-thumb-tack'),
(8, 'user/login', 'Login', 'user', NULL, 1, 'right', -4, 0, 0, NULL, NULL, 1, NULL, 0, 'fas fa-lock'),
(9, 'comment/recent', 'Comments', 'gleez', NULL, 0, '-1', -4, 0, 0, NULL, NULL, 1, NULL, 0, 'fas fa-comment'),
(10, 'admin/shortcut', 'Quick Shortcuts', 'gleez', NULL, 1, 'dashboard', -5, 0, 0, NULL, NULL, 1, NULL, 0, 'fas fa-bookmark'),
(11, 'blog/recent', 'Recent Blogs', 'gleez', NULL, 0, '-1', 0, 0, 0, NULL, NULL, 1, NULL, 1, 'fas fa-book'),
(12, 'blog/announce', 'Announce of Recent Blogs', 'gleez', NULL, 0, '-1', 0, 0, 0, NULL, NULL, 1, NULL, 1, 'fas fa-book');

DROP TABLE IF EXISTS {identities};
CREATE TABLE {identities} (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `provider` VARCHAR(32) NOT NULL,
  `provider_id` VARCHAR(128) NOT NULL,
  `refresh_token` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `provider` (`provider`),
  KEY `provider_id` (`provider`, `provider_id`),
  UNIQUE KEY `user_provider_id` (`user_id`, `provider`, `provider_id`),
  FOREIGN KEY (`user_id`) REFERENCES {users} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {sitemaps};
CREATE TABLE {sitemaps} (
  id INT UNSIGNED NOT NULL DEFAULT '0',
  loc VARCHAR(255) NOT NULL,
  lastmod INT UNSIGNED NOT NULL DEFAULT '0',
  priority FLOAT NOT NULL DEFAULT '0.5',
  changefreq INT UNSIGNED NOT NULL DEFAULT '0',
  status TINYINT UNSIGNED NOT NULL DEFAULT '1',
  type VARCHAR(20) NOT NULL DEFAULT 'post',
  PRIMARY KEY (`id`, `type`),
  KEY `loc` (`loc`),
  KEY `status_loc` (`status`, `loc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {buddies};
CREATE TABLE IF NOT EXISTS {buddies} (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_from` BIGINT UNSIGNED NOT NULL,
    `request_to` BIGINT UNSIGNED NOT NULL,
    `accepted` TINYINT UNSIGNED NOT NULL DEFAULT '0',
    `date_requested` INT UNSIGNED NOT NULL DEFAULT '0',
    `date_accepted` INT UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `buddy_requests_fk_1` (`request_from`),
    KEY `buddy_requests_fk_2` (`request_to`),
    FOREIGN KEY (`request_from`) REFERENCES {users} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`request_to`) REFERENCES {users} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {oauth_clients};
CREATE TABLE IF NOT EXISTS {oauth_clients} (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `client_id` VARCHAR(255) NOT NULL,
    `client_secret` VARCHAR(255) NOT NULL,
    `redirect_uri` VARCHAR(255) NOT NULL,
    `grant_types` VARCHAR(255) DEFAULT 'authorization_code',
    `status` TINYINT UNSIGNED NOT NULL DEFAULT '0',
    `description` TEXT NOT NULL,
    `logo` VARCHAR(255) NOT NULL,
    `created` INT UNSIGNED NOT NULL DEFAULT '0',
    `updated` INT UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `oauth_clients_fk_1` (`client_id`),
    KEY `oauth_clients_fk_2` (`client_secret`),
    KEY `oauth_clients_fk_3` (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES {users} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {oauth_codes};
CREATE TABLE IF NOT EXISTS {oauth_codes} (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(255) NOT NULL,
    `client_id` VARCHAR(255) NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `redirect_uri` VARCHAR(255) NOT NULL,
    `scope` VARCHAR(255) DEFAULT NULL,
    `expires` INT UNSIGNED NOT NULL DEFAULT '0',
    `created` INT UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `oauth_codes_fk_1` (`client_id`),
    KEY `oauth_codes_fk_2` (`user_id`),
    KEY `oauth_codes_fk_23` (`code`),
    FOREIGN KEY (`user_id`) REFERENCES {users} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`client_id`) REFERENCES {oauth_clients} (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS {oauth_tokens};
CREATE TABLE IF NOT EXISTS {oauth_tokens} (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `access_token` VARCHAR(255) NOT NULL,
    `refresh_token` VARCHAR(255) DEFAULT NULL,
    `client_id` VARCHAR(255) NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `scope` VARCHAR(255) DEFAULT NULL,
    `access_expires` INT UNSIGNED NOT NULL DEFAULT '0',
    `refresh_expires` INT UNSIGNED NOT NULL DEFAULT '0',
    `created` INT UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `oauth_tokens_fk_1` (`client_id`),
    KEY `oauth_tokens_fk_2` (`user_id`),
    KEY `oauth_tokens_fk_3` (`access_token`),
    FOREIGN KEY (`user_id`) REFERENCES {users} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`client_id`) REFERENCES {oauth_clients} (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS {mail_queue} (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  from_name VARCHAR(64) DEFAULT NULL,
  from_email VARCHAR(128) NOT NULL,
  to_email VARCHAR(128) NOT NULL,
  to_name VARCHAR(128) DEFAULT NULL,
  subject VARCHAR(255) NOT NULL,
  body TEXT NOT NULL,
  hash VARCHAR(128) NOT NULL,
  status TINYINT UNSIGNED NOT NULL DEFAULT '0',
  priority TINYINT UNSIGNED NOT NULL DEFAULT '0',
  max_attempts TINYINT UNSIGNED NOT NULL DEFAULT '3',
  attempts TINYINT UNSIGNED NOT NULL DEFAULT '0',
  last_attempt INT UNSIGNED NOT NULL DEFAULT '0',
  created INT UNSIGNED NOT NULL DEFAULT '0',
  pubdate INT UNSIGNED NOT NULL DEFAULT '0',
  sentdate INT UNSIGNED NOT NULL DEFAULT '0',
  timezone VARCHAR(32) DEFAULT 'UTC',
  language VARCHAR(12) DEFAULT 'en_US',
  template VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `to_email` (`to_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

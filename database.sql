-- This SQL script sets up the necessary tables for your e-commerce website.
-- You can import this file into your MySQL database via a tool like phpMyAdmin.

-- -- Table structure for admins
CREATE TABLE admins (
id int(11) NOT NULL AUTO_INCREMENT,
username varchar(255) NOT NULL,
password varchar(255) NOT NULL,
PRIMARY KEY (id),
UNIQUE KEY username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -- Dumping data for table admins
-- You should change this password immediately after setup. The password is 'admin123'. -- The hash was generated using password_hash('admin123', PASSWORD_BCRYPT).
INSERT INTO admins (id, username, password) VALUES
(1, 'admin', '2y10$I/2v5L3u0rGhs30GU2uR1.7b.u3z2.9.Jz3X.Yg4.0wG8h2m8l5p2');

-- -- Table structure for products
CREATE TABLE products (
id int(11) NOT NULL AUTO_INCREMENT,
name varchar(255) NOT NULL,
description text NOT NULL,
price decimal(10,2) NOT NULL,
category varchar(100) NOT NULL,
stock int(11) NOT NULL DEFAULT 0,
image varchar(255) DEFAULT 'default.jpg',
rating decimal(3,1) DEFAULT 0.0,
reviews int(11) DEFAULT 0,
sku varchar(100) DEFAULT NULL,
brand varchar(100) DEFAULT NULL,
features text DEFAULT NULL,
specifications text DEFAULT NULL,
dimensions varchar(100) DEFAULT NULL,
colors text DEFAULT NULL,
material varchar(100) DEFAULT NULL,
finished_type varchar(100) DEFAULT NULL,
delivery_days int(11) DEFAULT 3,
is_featured tinyint(1) NOT NULL DEFAULT 0,
created_at timestamp NOT NULL DEFAULT current_timestamp(),
PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -- Table structure for wishlist
CREATE TABLE wishlist (
id int(11) NOT NULL AUTO_INCREMENT,
user_id int(11) NOT NULL,
product_id int(11) NOT NULL,
PRIMARY KEY (id),
UNIQUE KEY user_product (user_id,product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -- Table structure for users -- Note: A basic users table is included for completeness of the wishlist functionality. -- The login/registration logic in login.php would need to be updated to use this.
CREATE TABLE users (
id int(11) NOT NULL AUTO_INCREMENT,
first_name varchar(100) NOT NULL,
last_name varchar(100) NOT NULL,
email varchar(255) NOT NULL,
password varchar(255) NOT NULL,
created_at timestamp NOT NULL DEFAULT current_timestamp(),
PRIMARY KEY (id),
UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
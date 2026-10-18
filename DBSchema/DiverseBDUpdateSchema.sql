-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2026 at 03:19 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `diversebd`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password_hash`, `created_at`) VALUES
(1, 'DiverseAdmin', 'alam.office.my@gmail.com', '$2y$10$yOiaQpKA30BaoYjO1y0DmuxcxGn7Cq.DfmB9O7DkrSrkiMwuv19OK', '2026-10-18 10:25:09');

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_logs`
--

CREATE TABLE `admin_login_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email_attempted` varchar(180) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_login_logs`
--

INSERT INTO `admin_login_logs` (`id`, `admin_id`, `email_attempted`, `ip_address`, `user_agent`, `status`, `created_at`) VALUES
(1, 1, 'alam.office.my@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'success', '2026-10-18 13:17:28');

-- --------------------------------------------------------

--
-- Table structure for table `admin_sessions`
--

CREATE TABLE `admin_sessions` (
  `id` varchar(128) NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_sessions`
--

INSERT INTO `admin_sessions` (`id`, `admin_id`, `ip_address`, `user_agent`, `last_activity`) VALUES
('pne7hdt6b99pgli8dt7rjieoov', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-18 13:18:29');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Women', 'women', '2026-10-18 10:22:06'),
(2, 'Men', 'men', '2026-10-18 10:22:06'),
(3, 'Accessories', 'accessories', '2026-10-18 10:22:06'),
(4, 'New Arrivals', 'new-arrivals', '2026-10-18 10:22:06');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `email` varchar(190) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied','closed') NOT NULL DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(60) NOT NULL,
  `discount_type` enum('percent','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(10) UNSIGNED DEFAULT NULL,
  `used_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_order`, `max_discount`, `usage_limit`, `used_count`, `starts_at`, `ends_at`, `active`, `created_at`) VALUES
(1, 'WELCOME10', 'percent', 10.00, 1000.00, 500.00, 100, 0, NULL, NULL, 1, '2026-10-18 10:22:06'),
(2, 'SAVE200', 'fixed', 200.00, 2000.00, NULL, 50, 0, NULL, NULL, 1, '2026-10-18 10:22:06'),
(3, 'SUMMER15', 'percent', 10.00, 1.00, 1.00, 100, 0, NULL, '2026-10-19 16:52:00', 1, '2026-10-18 10:52:58');

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(180) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `eyebrow` varchar(100) DEFAULT NULL,
  `button_text` varchar(80) DEFAULT 'Explore Collection',
  `button_url` varchar(255) DEFAULT 'index.php',
  `image_url` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(40) NOT NULL,
  `customer_name` varchar(160) NOT NULL,
  `customer_email` varchar(190) NOT NULL,
  `customer_phone` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(120) NOT NULL,
  `notes` text DEFAULT NULL,
  `payment_method` enum('COD') NOT NULL DEFAULT 'COD',
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled','returned') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `coupon_code` varchar(60) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(180) NOT NULL,
  `sku` varchar(80) DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(30) NOT NULL,
  `note` varchar(500) DEFAULT NULL,
  `changed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(180) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `sku` varchar(80) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `compare_price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `sku`, `description`, `price`, `compare_price`, `image_url`, `stock`, `active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Relaxed Linen Shirt', 'relaxed-linen-shirt', 'TW-W-001', 'A breathable everyday shirt with a relaxed fit. Pair it with denim or tailored trousers.', 1890.00, 2290.00, 'https://images.unsplash.com/photo-1598554747436-c9293d6a588f?w=900', 16, 1, '2026-10-18 10:22:06', '2026-10-18 11:43:07'),
(2, 2, 'Essential Cotton Tee', 'essential-cotton-tee', 'TW-M-001', 'A soft cotton T-shirt made for everyday layering and easy weekend styling.', 790.00, 990.00, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=900', 34, 1, '2026-10-18 10:22:06', '2026-10-18 10:27:52'),
(3, 1, 'Everyday Oversized Blazer', 'everyday-oversized-blazer', 'TW-W-002', 'An easy oversized silhouette that dresses up your everyday wardrobe.', 3290.00, 3890.00, 'uploads/prod_1792320179_1738a0b8.jpg', 8, 1, '2026-10-18 10:22:06', '2026-10-18 10:53:33'),
(4, 3, 'Minimal Crossbody Bag', 'minimal-crossbody-bag', 'TW-A-001', 'A clean, versatile crossbody bag for the essentials you carry every day.', 1490.00, NULL, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=900', 14, 1, '2026-10-18 10:22:06', '2026-10-18 10:22:06'),
(5, 2, 'dkim._domainkey', 'dkim-domainkey', '3', '', 33.00, NULL, 'uploads/prod_1792325002_f01d7b9e.png', 1, 1, '2026-10-18 12:03:22', '2026-10-18 12:03:55');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `created_at`) VALUES
(2, 3, 'uploads/gal_1792321618_d00412.jpg', '2026-10-18 11:06:58');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('admin_notification_email', 'alam.office.my@gmail.com', '2026-10-18 12:02:43'),
('announcement', 'New season, new essentials — free delivery offers may apply', '2026-10-18 10:22:06'),
('currency_symbol', '৳', '2026-10-18 12:02:43'),
('hero_description', 'Modern wardrobe staples. Easy fits, thoughtful details, everyday confidence.', '2026-10-18 10:22:06'),
('hero_eyebrow', 'Made for your everyday', '2026-10-18 10:22:06'),
('hero_title', 'Wear what feels like you.', '2026-10-18 10:22:06'),
('invoice_color', '#a22e11', '2026-10-18 12:12:02'),
('invoice_footer_note', 'Thank you for shopping with us!', '2026-10-18 12:12:02'),
('logo_url', 'storage/uploads/logo-88039f6688f8422e29706804.jpg', '2026-10-18 12:12:19'),
('mail_encryption', 'tls', '2026-10-18 12:02:43'),
('mail_from_address', 'alam.office.my@gmail.com', '2026-10-18 12:02:43'),
('mail_from_name', 'DiverseBD Store', '2026-10-18 12:02:43'),
('mail_host', 'smtp.gmail.com', '2026-10-18 12:02:43'),
('mail_password', 'mhsb jkly zwif dkig', '2026-10-18 12:02:43'),
('mail_port', '587', '2026-10-18 12:02:43'),
('mail_username', 'alam.office.my@gmail.com', '2026-10-18 12:02:43'),
('site_name', 'Diverse', '2026-10-18 10:22:06');

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

CREATE TABLE `social_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `platform` varchar(50) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `post_url` varchar(255) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_logs`
--

CREATE TABLE `stock_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `change_type` enum('add','subtract','set') NOT NULL,
  `quantity` int(11) NOT NULL,
  `old_stock` int(11) NOT NULL,
  `new_stock` int(11) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(120) NOT NULL,
  `review_text` text NOT NULL,
  `rating` tinyint(1) UNSIGNED DEFAULT 5,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_login_logs_admin` (`admin_id`);

--
-- Indexes for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_sessions_admin` (`admin_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contacts_status` (`status`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_created` (`created_at`),
  ADD KEY `idx_orders_email` (`customer_email`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_item_order` (`order_id`),
  ADD KEY `fk_order_item_product` (`product_id`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_status_order` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `fk_product_category` (`category_id`),
  ADD KEY `idx_products_active` (`active`),
  ADD KEY `idx_products_created` (`created_at`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_images_product` (`product_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `social_links`
--
ALTER TABLE `social_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_stock_logs_product` (`product_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `social_links`
--
ALTER TABLE `social_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_logs`
--
ALTER TABLE `stock_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  ADD CONSTRAINT `fk_login_logs_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD CONSTRAINT `fk_admin_sessions_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `fk_status_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD CONSTRAINT `fk_stock_logs_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

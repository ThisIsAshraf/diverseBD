-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2026 at 09:58 AM
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
(8, 1, 'alam.office.my@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'success', '2026-10-19 19:15:07'),
(9, 1, 'alam.office.my@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'failed', '2026-10-19 19:16:29'),
(10, 1, 'alam.office.my@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'success', '2026-10-19 19:16:52'),
(11, 1, 'alam.office.my@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'success', '2026-10-20 07:01:59');

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
('7p34ncte74vnja9sfcqbuip0km', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 07:41:36'),
('hc852loeuogdkaqe4f1ikjv0m9', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-19 20:06:52');

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
(1, 'Print', 'print', '2026-10-19 19:21:10'),
(2, 'Panjabi', 'panjabi', '2026-10-19 19:55:25');

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

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Ashraful Alam Shihab', 'alamshihab526@gmail.com', 'System Mail', 'casocbhkolasbcksbkcvbsklbckbk', 'replied', '2026-10-19 19:33:18');

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
(3, 'SUMMER15', 'percent', 10.00, 1.00, 1.00, 100, 0, NULL, '2026-10-19 16:52:00', 0, '2026-10-18 10:52:58'),
(4, 'JIHAD10', 'fixed', 100.00, 500.00, 0.00, 10, 0, NULL, '2026-10-21 01:32:00', 1, '2026-10-19 19:32:06');

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

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `title`, `subtitle`, `eyebrow`, `button_text`, `button_url`, `image_url`, `sort_order`, `active`, `created_at`) VALUES
(1, 'Notun Titlre', 'fghfhfhfhfh', 'SUMMEr Sales', 'Explore Collection', '?category=panjabi', 'uploads/hero_1792439216_c7e42b.jpg', 1, 1, '2026-10-19 19:46:22');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
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

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `customer_name`, `customer_email`, `customer_phone`, `address`, `city`, `notes`, `payment_method`, `status`, `subtotal`, `discount_amount`, `shipping_amount`, `total`, `coupon_code`, `created_at`, `updated_at`) VALUES
(1, NULL, 'DVBD261020AC8D66', 'Ashraful Alam Shihab', 'alamshihab526@gmail.com', '01982401992', 'Test Address', 'Dhaka', 'rrrrrrr', 'COD', 'confirmed', 150.00, 0.00, 0.00, 150.00, NULL, '2026-10-19 19:22:59', '2026-10-19 19:26:43'),
(2, 1, 'DVBD261020406A41', 'Ashraful Alam Shihab', 'ashrafulalam4083@gmail.com', '01760812657', 'Girls School Road, Dakskin Khan, Dhaka-1230', 'Dhaka', '', 'COD', 'delivered', 300.00, 0.00, 0.00, 300.00, NULL, '2026-10-20 07:39:33', '2026-10-20 07:41:36');

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

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `quantity`, `unit_price`, `line_total`) VALUES
(1, 1, 1, 'Drop Shooulder', 'DS001', 1, 150.00, 150.00),
(2, 2, 1, 'Drop Shooulder', 'DS001', 2, 150.00, 300.00);

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

--
-- Dumping data for table `order_status_history`
--

INSERT INTO `order_status_history` (`id`, `order_id`, `status`, `note`, `changed_by`, `created_at`) VALUES
(1, 1, 'pending', 'Order placed by customer', NULL, '2026-10-19 19:22:59'),
(2, 1, 'processing', 'Status updated in admin panel to Processing', 1, '2026-10-19 19:26:16'),
(3, 1, 'confirmed', 'Status updated in admin panel to Confirmed (Official Invoice sent to customer)', 1, '2026-10-19 19:26:43'),
(4, 2, 'pending', 'Order placed by customer', NULL, '2026-10-20 07:39:33'),
(5, 2, 'confirmed', 'Status updated in admin panel to Confirmed (Official Invoice sent to customer)', 1, '2026-10-20 07:40:18'),
(6, 2, 'cancelled', 'Status updated in admin panel to Cancelled', 1, '2026-10-20 07:41:22'),
(7, 2, 'delivered', 'Status updated in admin panel to Delivered', 1, '2026-10-20 07:41:36');

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
(1, 1, 'Drop Shooulder', 'drop-shooulder', 'DS001', '', 150.00, NULL, 'uploads/prod_1792437670_c6bf399d.png', 12, 1, '2026-10-19 19:21:10', '2026-10-20 07:39:33'),
(2, 2, 'New Panjabi', 'new-panjabi', 'PNH555', 'CVSACVSVSV', 50.00, NULL, 'uploads/prod_1792439778_ac3168cf.jpg', 10, 1, '2026-10-19 19:56:18', '2026-10-19 19:56:18');

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
(1, 1, 'uploads/gal_1792437670_39ff3a.jpg', '2026-10-19 19:21:10'),
(2, 1, 'uploads/gal_1792437670_bc0090.jpg', '2026-10-19 19:21:10'),
(3, 1, 'uploads/gal_1792437670_de9ecc.jpg', '2026-10-19 19:21:10'),
(4, 2, 'uploads/gal_1792439778_137162.jpeg', '2026-10-19 19:56:18'),
(5, 2, 'uploads/gal_1792439778_8af2c5.jpeg', '2026-10-19 19:56:18'),
(6, 2, 'uploads/gal_1792439778_546986.jpeg', '2026-10-19 19:56:18');

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
('admin_notification_email', 'hello.divers.almarez@gmail.com', '2026-10-18 17:02:39'),
('announcement', 'Test Header Announcement', '2026-10-19 19:36:37'),
('currency_symbol', '৳', '2026-10-18 12:02:43'),
('footer_about', 'Modern wardrobe staples, everyday fits, and premium quality crafted for style.dddddddeee', '2026-10-19 19:41:10'),
('footer_address', 'Dhaka, Bangladesh', '2026-10-18 17:41:41'),
('footer_copyright', '© 2026 Diverse. All rights reserved.', '2026-10-18 17:41:41'),
('footer_email', 'hello.divers.almarez@gmail.com', '2026-10-18 17:41:41'),
('footer_phone', '+880 1700-000000', '2026-10-18 17:41:41'),
('hero_description', 'Modern wardrobe staples. Easy fits, thoughtful details, everyday confidence.', '2026-10-18 10:22:06'),
('hero_eyebrow', 'Made for your everyday', '2026-10-18 10:22:06'),
('hero_title', 'Wear what feels like you.', '2026-10-18 10:22:06'),
('invoice_color', '#81a0a2', '2026-10-19 19:38:52'),
('invoice_footer_note', 'Thank you for shopping with us!555633', '2026-10-19 19:38:52'),
('logo_url', '', '2026-10-19 19:37:06'),
('mail_encryption', 'tls', '2026-10-18 17:14:14'),
('mail_from_address', 'hello.divers.almarez@gmail.com', '2026-10-18 17:14:14'),
('mail_from_name', 'DiverseBD Store', '2026-10-18 17:14:14'),
('mail_host', 'smtp.gmail.com', '2026-10-18 17:14:14'),
('mail_password', 'hopw tezg rfvx njcu', '2026-10-18 17:14:14'),
('mail_port', '587', '2026-10-18 17:14:14'),
('mail_username', 'hello.divers.almarez@gmail.com', '2026-10-18 17:14:14'),
('site_name', 'Diverse Clothings', '2026-10-19 19:37:28'),
('social_facebook', 'https://www.facebook.com/ashraful.alam.984813', '2026-10-19 19:42:08'),
('social_instagram', '', '2026-10-19 19:39:42'),
('social_tiktok', '', '2026-10-19 19:39:42'),
('social_whatsapp', '', '2026-10-19 19:39:42'),
('social_youtube', '', '2026-10-19 19:39:42'),
('theme_announcement_text', '#f39c12', '2026-10-19 19:40:19'),
('theme_body_bg', '#fcf8f2', '2026-10-19 19:40:19'),
('theme_border_color', '#f0e6d2', '2026-10-19 19:40:19'),
('theme_card_bg', '#ffffff', '2026-10-19 19:40:13'),
('theme_footer_bg', '#2c3e50', '2026-10-19 19:40:19'),
('theme_footer_text', '#fcf8f2', '2026-10-19 19:40:19'),
('theme_header_bg', '#2c3e50', '2026-10-19 19:40:19'),
('theme_primary_color', '#d35400', '2026-10-19 19:40:19');

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

--
-- Dumping data for table `social_links`
--

INSERT INTO `social_links` (`id`, `platform`, `title`, `post_url`, `image_url`, `sort_order`, `active`, `created_at`) VALUES
(1, 'Facebook', 'vgsvgsdgv', 'https://www.facebook.com/ashraful.alam.984813', 'uploads/social_1792439608_0be254.jpg', 0, 1, '2026-10-19 19:52:31');

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

--
-- Dumping data for table `stock_logs`
--

INSERT INTO `stock_logs` (`id`, `product_id`, `admin_id`, `change_type`, `quantity`, `old_stock`, `new_stock`, `note`, `created_at`) VALUES
(1, 1, 1, 'add', 10, 9, 19, 'Notun Mal Aysa', '2026-10-19 19:43:57'),
(2, 1, 1, 'subtract', 5, 19, 14, '5 ta nosto', '2026-10-19 19:44:33');

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
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `customer_name`, `review_text`, `rating`, `sort_order`, `active`, `created_at`) VALUES
(1, 'Shihab', 'onek valo', 4, 0, 1, '2026-10-19 19:54:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `status` enum('active','blocked') NOT NULL DEFAULT 'active',
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `phone`, `address`, `city`, `status`, `reset_token`, `reset_token_expires`, `created_at`, `updated_at`) VALUES
(1, 'Ashraful Alam Shihab', 'ashrafulalam4083@gmail.com', '$2y$10$A9UaYh22NIPHiRfdxsGWJ.Qfgn68kFuQ1iHq97ehhhqPJFl12W0D6', '01760812657', 'Girls School Road, Dakskin Khan, Dhaka-1230', 'Dhaka', 'active', NULL, NULL, '2026-10-20 07:29:43', '2026-10-20 07:45:04'),
(2, 'Ashraful Alam Shihab', 'hello.ash.ge@gmail.com', '$2y$10$dden/h9aRLD7XqSUYG5Wh.e9LHbiB9uTPEZIqHBa.AVb7nQa8zJ0i', '01982401992', 'Test Address', 'Dhaka', 'active', NULL, NULL, '2026-10-20 07:35:19', '2026-10-20 07:35:19');

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `social_links`
--
ALTER TABLE `social_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_logs`
--
ALTER TABLE `stock_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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

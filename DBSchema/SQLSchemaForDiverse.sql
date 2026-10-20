-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2026 at 09:27 PM
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
  `role` varchar(50) NOT NULL DEFAULT 'admin',
  `is_super_admin` tinyint(1) NOT NULL DEFAULT 0,
  `permissions` text DEFAULT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `role`, `is_super_admin`, `permissions`, `email`, `password_hash`, `created_at`) VALUES
(1, 'DiverseAdmin', 'Super Admin', 1, NULL, 'alam.office.my@gmail.com', '$2y$10$yOiaQpKA30BaoYjO1y0DmuxcxGn7Cq.DfmB9O7DkrSrkiMwuv19OK', '2026-10-18 10:25:09'),
(2, 'Shihab', 'UserOnly', 0, '[\"products\",\"orders\"]', 'shihab@az.com', '$2y$10$wXsorNAYQN/5s11ZWSYtruBKKLzAXpSnUS6fov9wGuDz9tJ7Mb7Xe', '2026-10-20 12:43:38'),
(4, 'Ashraful Alam Shihab', 'Order Processor', 0, '[\"products\",\"categories\"]', 'alamshihab526@gmail.com', '$2y$10$fWyq3vrkf1pQAWM61Vbzq.dELG11NdOb5WMmaJWxsUbQXeL40Dss6', '2026-10-20 18:35:47');

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
(12, 1, 'alam.office.my@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'success', '2026-10-20 08:37:59'),
(13, 1, 'alam.office.my@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'success', '2026-10-20 11:42:11'),
(14, 1, 'alam.office.my@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'success', '2026-10-20 11:53:12'),
(15, 1, 'alam.office.my@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'success', '2026-10-20 12:32:44'),
(16, 2, 'shihab@az.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'success', '2026-10-20 12:44:05'),
(17, 2, 'shihab@az.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'success', '2026-10-20 12:45:22'),
(18, 2, 'shihab@az.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'success', '2026-10-20 12:52:14'),
(19, 1, 'alam.office.my@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'success', '2026-10-20 14:06:08'),
(20, 1, 'alam.office.my@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'success', '2026-10-20 17:38:25'),
(21, 1, 'alam.office.my@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'success', '2026-10-20 17:38:53'),
(22, 1, 'alam.office.my@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'success', '2026-10-20 17:38:58'),
(23, 1, 'alam.office.my@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'success', '2026-10-20 17:41:52'),
(24, 1, 'alam.office.my@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'success', '2026-10-20 17:50:10'),
(25, 1, 'alam.office.my@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'success', '2026-10-20 17:50:29'),
(26, 4, 'alamshihab526@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'success', '2026-10-20 18:36:24'),
(27, 4, 'alamshihab526@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'success', '2026-10-20 18:41:30');

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
('505vd5kblur71cohsohijq0o21', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 19:20:26'),
('a5nn1c0k78uqitk7pd8gpqklpd', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', '2026-10-20 18:41:30');

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
(1, 'Test', 'test', '2026-10-20 11:54:47');

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
(1, 'Aiatul', 'aiatulador75@gmail.com', 'Ki Obosota', 'Valo na. Access lagbo', 'replied', '2026-10-20 14:20:05');

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
(1, 1, 'DVBD26102066BF94', 'Ashraful Alam Shihab', 'alamshihab526@gmail.com', '01982401992', 'Test Address', 'Dhaka', '', 'COD', 'pending', 12.00, 0.00, 0.00, 12.00, NULL, '2026-10-20 11:59:50', '2026-10-20 11:59:50'),
(2, 2, 'DVBD261020C8D5EF', 'Aiatul', 'aiatulador75@gmail.com', '01799589913', 'Test Address', 'Dhaka', 'Twat ioess', 'COD', 'shipped', 12.00, 0.00, 0.00, 12.00, NULL, '2026-10-20 14:10:04', '2026-10-20 14:13:59'),
(3, 3, 'DVBD26102017A033', 'CloudOrder', 'learn.azsystem@gmail.com', '01982401992', 'Test Address', 'Dhaka', '', 'COD', 'confirmed', 12.00, 0.00, 0.00, 12.00, NULL, '2026-10-20 17:53:34', '2026-10-20 17:54:33');

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
(1, 1, 1, 'Everyday Oversized Blazer', 'FGE', 1, 12.00, 12.00),
(2, 2, 1, 'Everyday Oversized Blazer', 'FGE', 1, 12.00, 12.00),
(3, 3, 1, 'Everyday Oversized Blazer', 'FGE', 1, 12.00, 12.00);

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
(1, 1, 'pending', 'Order placed by customer', NULL, '2026-10-20 11:59:50'),
(2, 2, 'pending', 'Order placed by customer', NULL, '2026-10-20 14:10:04'),
(3, 2, 'confirmed', 'Status updated in admin panel to Confirmed (Official Invoice sent to customer)', 1, '2026-10-20 14:12:11'),
(4, 2, 'shipped', 'Status updated in admin panel to Shipped', 1, '2026-10-20 14:13:59'),
(5, 3, 'pending', 'Order placed by customer', NULL, '2026-10-20 17:53:34'),
(6, 3, 'confirmed', 'Status updated in admin panel to Confirmed (Official Invoice sent to customer)', 1, '2026-10-20 17:54:33');

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
(1, 1, 'Everyday Oversized Blazer', 'everyday-oversized-blazer', 'FGE', 'sss', 12.00, NULL, 'uploads/prod_1792497287_5d57e7bd.png', 17, 1, '2026-10-20 11:54:47', '2026-10-20 17:53:34');

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
('admin_email', 'hello.divers.almarez@gmail.com', '2026-10-20 17:53:38'),
('admin_notification_email', 'hello.divers.almarez@gmail.com', '2026-10-18 17:02:39'),
('announcement', 'Test Header Announcement', '2026-10-19 19:36:37'),
('currency_symbol', '৳', '2026-10-18 12:02:43'),
('email_invoice_subject', 'Official Tax Invoice - Order #{order_number}', '2026-10-20 08:34:08'),
('email_order_placed_intro', 'We have received your order #{order_number}. Our team is reviewing the details and will confirm your order shortly.', '2026-10-20 08:33:26'),
('email_order_placed_subject', 'Order Received - #{order_number}', '2026-10-20 08:33:26'),
('font_badge_family', 'Inter, sans-serif', '2026-10-20 19:07:12'),
('font_badge_size', '14', '2026-10-20 19:07:12'),
('font_catalog_family', 'Inter, sans-serif', '2026-10-20 19:07:12'),
('font_catalog_size', '15', '2026-10-20 19:07:12'),
('font_family', 'system-ui, -apple-system, sans-serif', '2026-10-20 18:54:34'),
('font_footer_family', 'Inter, sans-serif', '2026-10-20 19:07:12'),
('font_footer_size', '14', '2026-10-20 19:07:12'),
('font_hero_family', 'Playfair Display, serif', '2026-10-20 19:07:12'),
('font_hero_size', '32', '2026-10-20 19:07:12'),
('font_nav_family', 'Inter, sans-serif', '2026-10-20 19:07:12'),
('font_nav_size', '15', '2026-10-20 19:07:12'),
('font_size_base', '12', '2026-10-20 18:54:34'),
('font_social_family', 'Playfair Display, serif', '2026-10-20 19:07:12'),
('font_social_size', '28', '2026-10-20 19:07:12'),
('footer_about', 'Modern wardrobe staples, everyday fits, and premium quality crafted for style.dddddddeee', '2026-10-19 19:41:10'),
('footer_address', 'Dhaka, Bangladesh', '2026-10-18 17:41:41'),
('footer_copyright', '© 2026 Diverse. All rights reserved.', '2026-10-18 17:41:41'),
('footer_email', 'hello.divers.almarez@gmail.com', '2026-10-18 17:41:41'),
('footer_phone', '+880 1700-000000', '2026-10-18 17:41:41'),
('hero_description', 'Modern wardrobe staples. Easy fits, thoughtful details, everyday confidence.', '2026-10-18 10:22:06'),
('hero_eyebrow', 'Made for your everyday', '2026-10-18 10:22:06'),
('hero_title', 'Wear what feels like you.', '2026-10-18 10:22:06'),
('invoice_color', '#a94f28', '2026-10-20 14:11:51'),
('invoice_footer_note', 'Thank you for shopping with us!555633', '2026-10-19 19:38:52'),
('logo_url', '', '2026-10-19 19:37:06'),
('mail_encryption', 'tls', '2026-10-18 17:14:14'),
('mail_from_address', 'hello.divers.almarez@gmail.com', '2026-10-18 17:14:14'),
('mail_from_name', 'DiverseBD Store', '2026-10-18 17:14:14'),
('mail_host', 'smtp.gmail.com', '2026-10-18 17:14:14'),
('mail_password', 'hopw tezg rfvx njcu', '2026-10-18 17:14:14'),
('mail_port', '587', '2026-10-18 17:14:14'),
('mail_username', 'hello.divers.almarez@gmail.com', '2026-10-18 17:14:14'),
('max_admin_sessions', '1', '2026-10-20 17:49:54'),
('site_name', 'Diverse Clothings', '2026-10-19 19:37:28'),
('social_facebook', '', '2026-10-20 14:22:15'),
('social_instagram', '', '2026-10-19 19:39:42'),
('social_tiktok', '', '2026-10-19 19:39:42'),
('social_whatsapp', '', '2026-10-19 19:39:42'),
('social_youtube', '', '2026-10-19 19:39:42'),
('theme_announcement_text', '#ffffff', '2026-10-20 14:21:43'),
('theme_body_bg', '#f8f9fa', '2026-10-20 19:20:20'),
('theme_border_color', '#e0e0e0', '2026-10-20 19:20:20'),
('theme_card_bg', '#ffffff', '2026-10-20 14:21:49'),
('theme_footer_bg', '#111111', '2026-10-20 19:20:20'),
('theme_footer_text', '#ffffff', '2026-10-20 14:21:43'),
('theme_header_bg', '#1a252f', '2026-10-20 19:20:20'),
('theme_primary_color', '#2c3e50', '2026-10-20 19:20:20'),
('trust_badge_1_sub', 'Fast delivery across Bangladesh', '2026-10-20 08:33:26'),
('trust_badge_1_title', 'Nationwide Shipping', '2026-10-20 08:33:26'),
('trust_badge_2_sub', 'Pay safely at your doorstep', '2026-10-20 08:33:26'),
('trust_badge_2_title', 'Cash on Delivery', '2026-10-20 08:33:26'),
('trust_badge_3_sub', 'Crafted for long-lasting comfort', '2026-10-20 17:53:24'),
('trust_badge_3_title', 'Premium Fabric', '2026-10-20 17:53:24'),
('trust_badge_4_sub', 'Easy returns & order support', '2026-10-20 17:53:24'),
('trust_badge_4_title', 'Hassle-Free Exchange', '2026-10-20 17:53:24');

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
(1, 'Facebook', 'xdcszvsvs', 'https://www.facebook.com/ashraful.alam.984813', 'uploads/social_1792506200_bf198c.png', 0, 1, '2026-10-20 14:23:20');

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
(1, 1, 1, 'add', 10, 10, 20, 'Notun Mal Aysa', '2026-10-20 14:24:11'),
(2, 1, 1, 'subtract', 2, 20, 18, '2 ta nosto', '2026-10-20 14:24:54');

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
(1, 'Ashraful Alam Shihab', 'alamshihab526@gmail.com', '$2y$10$PHhvDhGBXfraX5axL31nA.1Vfah6vu1zUNXVcNsC6FKEpd1KV9m7u', '01982401992', 'Test Address', 'Dhaka', 'active', NULL, NULL, '2026-10-20 11:53:59', '2026-10-20 11:53:59'),
(2, 'Aiatul', 'aiatulador75@gmail.com', '$2y$10$balhzB4Oju5jMtG3SqSxqOC1QWyy34gKSFhcVDv1LC0uOLz66wL.q', '01799589913', 'Test Address', 'Dhaka', 'active', NULL, NULL, '2026-10-20 14:08:32', '2026-10-20 14:16:05'),
(3, 'CloudOrder', 'learn.azsystem@gmail.com', '$2y$10$vwllGHjsN2u7tbnEqaj0neDNCzr5.rry/D/Ak36fWvHTMswRLCspy', '01982401992', 'Test Address', 'Dhaka', 'active', NULL, NULL, '2026-10-20 17:53:01', '2026-10-20 17:53:01');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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

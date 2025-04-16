-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2025 at 07:10 PM
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
-- Database: `planet_victoria`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `created_at`) VALUES
(1, 'Nairobi Branch', '2025-04-14 09:01:53'),
(2, 'Kisumu Branch', '2025-04-14 09:01:53'),
(3, 'Mombasa Branch', '2025-04-14 09:01:53'),
(4, 'Migori Branch', '2025-04-14 09:01:53'),
(5, 'Kisii Branch', '2025-04-14 09:01:53'),
(6, 'Kakamega Branch', '2025-04-14 09:01:53'),
(7, 'Vihiga Branch', '2025-04-14 09:01:53');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `subcategory_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `subcategory_name`) VALUES
(1, 'Building and Construction', 'Iron Sheets'),
(2, 'Building and Construction', 'Cement'),
(3, 'Building and Construction', 'Floor Tiles'),
(4, 'Building and Construction', 'Paints'),
(5, 'Building and Construction', 'Doors and Windows'),
(6, 'Building and Construction', 'Fencing Wire'),
(7, 'Building and Construction', 'Timber'),
(8, 'Food and Household Basics', 'Cooking Oil'),
(9, 'Food and Household Basics', 'Soaps'),
(10, 'Food and Household Basics', 'Flour'),
(11, 'Food and Household Basics', 'Sugar'),
(12, 'Food and Household Basics', 'Rice'),
(13, 'Farming Equipment', 'Fertilizers'),
(14, 'Farming Equipment', 'Seeds'),
(15, 'Farming Equipment', 'Farm Tools'),
(16, 'Household Equipment', 'Furniture'),
(17, 'Household Equipment', 'Utensils / Cookings'),
(18, 'Household Equipment', 'Electronics'),
(19, 'Household Equipment', 'Bedroom Equipment'),
(20, 'Household Equipment', 'Floor Carpets'),
(21, 'Household Equipment', 'Wall Nets'),
(22, 'Household Equipment', 'Seat Covers'),
(23, 'School Requirements', 'Shoes'),
(24, 'School Requirements', 'Bags'),
(25, 'School Requirements', 'Cloth Box'),
(26, 'School Requirements', 'Stationery (Books)'),
(27, 'School Requirements', 'Stationery (Pens)'),
(28, 'School Requirements', 'School Mattress'),
(29, 'Water Harvesting', 'Water Tanks');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `income` decimal(10,2) DEFAULT NULL,
  `education` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `officer_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `first_name`, `last_name`, `gender`, `dob`, `id_number`, `phone`, `email`, `address`, `city`, `occupation`, `income`, `education`, `notes`, `group_id`, `branch`, `officer_id`, `status`, `created_at`) VALUES
(14, 'electa', 'akinyi', 'Female', '1990-03-01', '21234673', '0743526172', 'ea@gmaiil.com', '40400', 'migori', 'farmer', 0.00, 'secondary', 'raneni', 17, '4', 3, 'approved', '2025-04-16 06:47:24'),
(15, 'mbaja', 'atieno', 'Female', '1981-06-19', '53627815', '0753627389', 'ma@gmail.com', '40400', 'migori', 'farmer', 0.00, 'secondary', 'mbonde', 16, '5', 3, 'approved', '2025-04-16 06:57:31');

-- --------------------------------------------------------

--
-- Table structure for table `dda`
--

CREATE TABLE `dda` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `deposit_amount` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `deposit_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dda`
--

INSERT INTO `dda` (`id`, `client_id`, `deposit_amount`, `balance`, `deposit_date`) VALUES
(10, 15, 700.00, 700.00, '2025-04-16 07:33:50'),
(11, 14, 600.00, 600.00, '2025-04-16 07:34:03');

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `savings_amount` decimal(10,2) NOT NULL,
  `loan_payment` decimal(10,2) NOT NULL,
  `deposit_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deposits`
--

INSERT INTO `deposits` (`id`, `client_id`, `savings_amount`, `loan_payment`, `deposit_date`) VALUES
(17, 15, 100.00, 700.00, '2025-04-16 07:33:50'),
(18, 14, 200.00, 600.00, '2025-04-16 07:34:03');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `formation_date` date DEFAULT NULL,
  `type` enum('farming','trading','artisan','women','youth') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `officer_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `location`, `formation_date`, `type`, `created_at`, `officer_id`, `branch_id`) VALUES
(1, 'Katek', 'migori', '2023-01-01', '', '2025-04-11 09:41:48', NULL, 4),
(2, 'Tujiunge', 'nairobi', '2023-02-01', '', '2025-04-11 09:41:48', 3, 1),
(3, 'Amani', 'mombasa', '2023-03-01', '', '2025-04-11 09:41:48', 3, 3),
(4, 'Ngi gi mari', 'vihiga', '2023-04-01', '', '2025-04-11 09:41:48', 3, 7),
(16, 'Alego', 'bonde', '2025-04-21', '', '2025-04-14 12:29:21', 3, 4),
(17, 'Blessed', 'bonde', '2025-04-14', '', '2025-04-14 14:51:28', 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `guarantors`
--

CREATE TABLE `guarantors` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `guarantor_name` varchar(255) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `village` varchar(100) DEFAULT NULL,
  `town` varchar(100) DEFAULT NULL,
  `sub_county` varchar(100) DEFAULT NULL,
  `county` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `relationship` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guarantors`
--

INSERT INTO `guarantors` (`id`, `client_id`, `guarantor_name`, `id_number`, `village`, `town`, `sub_county`, `county`, `country`, `phone`, `relationship`, `created_at`) VALUES
(1, 14, 'john', 'odhiambo', 'raneni', 'raneni', 'awendo', 'migori', 'Kenya', '0712324543', 'husband', '2025-04-16 06:47:24'),
(2, 15, 'wicklif', '53627382', 'bonde', 'awendo', 'awendo', 'migori', 'Kenya', '0724536215', 'husband', '2025-04-16 06:57:31');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `principal_amount` decimal(10,2) NOT NULL,
  `loan_term` int(11) NOT NULL,
  `purpose` text DEFAULT NULL,
  `interest_rate` decimal(5,2) DEFAULT NULL,
  `service_charge` decimal(10,2) DEFAULT NULL,
  `total_repayment` decimal(10,2) DEFAULT NULL,
  `monthly_payment` decimal(10,2) DEFAULT NULL,
  `officer_id` int(11) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `status` enum('Pending','Approved','Declined') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `placed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `total_amount`, `description`, `status`, `placed_at`, `total_quantity`) VALUES
(1, 3, 425.75, 'ann okumu,amani grp,21,migori,bonde,3m', 'Pending', '2025-04-12 17:40:39', 4);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `created_at`, `updated_at`, `category_id`) VALUES
(1, 'Galvanized Iron Sheet', 'Durable galvanized iron sheet for roofing and construction.', 12.50, 'https://via.placeholder.com/300?text=Galvanized+Iron+Sheet', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 1),
(2, 'Colored Iron Sheet', 'High-quality colored iron sheet for roofing.', 15.00, 'https://via.placeholder.com/300?text=Colored+Iron+Sheet', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 1),
(3, 'Portland Cement (50kg)', 'Strong Portland cement for construction and concrete work.', 8.50, 'https://via.placeholder.com/300?text=Portland+Cement', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 2),
(4, 'Quick Dry Cement', 'Fast-drying cement ideal for quick repairs and construction projects.', 9.25, 'https://via.placeholder.com/300?text=Quick+Dry+Cement', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 2),
(5, 'Vegetable Oil (5L)', 'Pure vegetable oil for cooking, 5L.', 8.00, 'https://via.placeholder.com/300?text=Vegetable+Oil', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 8),
(6, 'Laundry Soap', 'Powerful laundry soap for tough stains.', 2.50, 'https://via.placeholder.com/300?text=Laundry+Soap', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 9),
(7, 'Organic Fertilizer', 'Natural organic fertilizer for healthy crops.', 5.75, 'https://via.placeholder.com/300?text=Organic+Fertilizer', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 13),
(8, 'Tomato Seeds', 'High-quality tomato seeds for planting.', 3.00, 'https://via.placeholder.com/300?text=Tomato+Seeds', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 14),
(9, 'Wooden Dining Table', 'Stylish wooden dining table for 6 people.', 120.00, 'https://via.placeholder.com/300?text=Wooden+Dining+Table', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 16),
(10, 'LED Television (40\")', 'High-definition LED television for your living room.', 350.00, 'https://via.placeholder.com/300?text=LED+Television', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 18),
(11, 'School Shoes', 'Durable school shoes for boys and girls.', 25.00, 'https://via.placeholder.com/300?text=School+Shoes', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 23),
(12, 'Math Textbook', 'Comprehensive math textbook for high school students.', 15.00, 'https://via.placeholder.com/300?text=Math+Textbook', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 26),
(13, '2000L Water Tank', 'Durable 2000L water storage tank for rainwater harvesting.', 150.00, 'https://via.placeholder.com/300?text=2000L+Water+Tank', '2025-04-12 07:57:58', '2025-04-12 07:57:58', 29);

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `subcategory_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `role` enum('Admin','Branch Manager','Officer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `phone`, `role`, `created_at`, `updated_at`) VALUES
(3, 'guru', '$2y$10$qkB.dcztXzmkklHoiE/OTOPaIz.p2FrDu9PgNdEO7UKohYt84B/xu', 'guru', 'tech', 'guru@gmail.com', '0712345678', 'Officer', '2025-04-11 10:06:42', '2025-04-11 10:39:55'),
(11, 'eric', '$2y$10$g8lLbpq/ubYD5d.MEvjsq.zipQzxdeSn5iU0W1/UhJ5RsuXfQGdKG', 'eric', 'mc', 'eric@gmail.com', '0721232145', 'Admin', '2025-04-14 08:09:40', '2025-04-14 08:09:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dda`
--
ALTER TABLE `dda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `fk_officer` (`officer_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `guarantors`
--
ALTER TABLE `guarantors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_id` (`category_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `dda`
--
ALTER TABLE `dda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `guarantors`
--
ALTER TABLE `guarantors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `dda`
--
ALTER TABLE `dda`
  ADD CONSTRAINT `dda_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deposits`
--
ALTER TABLE `deposits`
  ADD CONSTRAINT `deposits_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `fk_officer` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `guarantors`
--
ALTER TABLE `guarantors`
  ADD CONSTRAINT `guarantors_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

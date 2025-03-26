-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2025 at 11:25 AM
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
-- Database: `photobooth_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_date` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `event_type` varchar(100) NOT NULL,
  `estimated_price` decimal(10,2) DEFAULT NULL,
  `final_price` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `reference_image` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected','paid','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `event_date`, `location`, `start_time`, `end_time`, `event_type`, `estimated_price`, `final_price`, `notes`, `reference_image`, `status`, `created_at`) VALUES
(1, 1, '2025-03-17', 'Ilaya, Carmen, Zone 5, Balay ni brader, Cagayan de Oro, misamis_oriental', '13:00:00', '14:00:00', 'wedding', 23000.00, 500.00, 'wala', 'ref_1_1742094980.png', 'completed', '2025-03-16 03:16:20'),
(2, 1, '2025-03-17', ', , , Select City, Select Province/City', '08:00:00', '20:00:00', 'wedding', 123000.00, 500.00, 'paapil ko lechon baka', 'ref_2_1742100129.jpg', 'completed', '2025-03-16 04:42:09'),
(3, 1, '2025-03-17', 'Ilaya, Carmen, Zone 5, Balay ni brader, Cagayan de Oro, misamis_oriental', '06:00:00', '08:00:00', 'corporate', 13000.00, 99.00, 'lechon nga zebra', 'ref_3_1742108175.jpg', 'completed', '2025-03-16 06:56:15'),
(4, 1, '2025-03-18', 'Ilaya, Carmen, Zone 5, Balay ni brader, Cagayan de Oro, misamis_oriental', '10:00:00', '15:00:00', 'corporate', 25000.00, 99999.00, 'walaa', 'ref_4_1742205214.jpg', 'approved', '2025-03-17 09:53:34');

-- --------------------------------------------------------

--
-- Table structure for table `booking_packages`
--

CREATE TABLE `booking_packages` (
  `booking_package_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_packages`
--

INSERT INTO `booking_packages` (`booking_package_id`, `booking_id`, `package_id`) VALUES
(1, 1, 8),
(2, 2, 8),
(3, 2, 9),
(4, 2, 1),
(5, 2, 2),
(6, 2, 3),
(7, 2, 4),
(8, 2, 5),
(9, 2, 6),
(10, 2, 7),
(11, 3, 8),
(12, 4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `booking_services`
--

CREATE TABLE `booking_services` (
  `booking_service_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_services`
--

INSERT INTO `booking_services` (`booking_service_id`, `booking_id`, `service_id`, `quantity`, `price`) VALUES
(1, 1, 3, 1, 0.00),
(2, 1, 4, 1, 0.00),
(3, 2, 3, 1, 0.00),
(4, 2, 4, 1, 0.00),
(5, 2, 2, 1, 0.00),
(6, 2, 1, 1, 0.00),
(7, 3, 3, 1, 0.00),
(8, 4, 3, 1, 0.00),
(9, 4, 4, 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `image_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `service_type` enum('wedding','birthday','christening','thanksgiving') NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`image_id`, `filename`, `service_type`, `uploaded_at`) VALUES
(1, 'bday1.jpg', 'birthday', '2025-03-16 02:44:16'),
(2, 'bday2.jpg', 'birthday', '2025-03-16 02:44:16'),
(3, 'bday3.jpg', 'birthday', '2025-03-16 02:44:16'),
(4, 'bday4.jpg', 'birthday', '2025-03-16 02:44:16'),
(5, 'bday5.jpg', 'birthday', '2025-03-16 02:44:16'),
(6, 'chr1.jpg', 'christening', '2025-03-16 02:44:16'),
(7, 'chr2.jpg', 'christening', '2025-03-16 02:44:16'),
(8, 'chr3.jpg', 'christening', '2025-03-16 02:44:16'),
(9, 'chr4.jpg', 'christening', '2025-03-16 02:44:16'),
(10, 'chr5.jpg', 'christening', '2025-03-16 02:44:16'),
(11, 'thg1.jpg', 'thanksgiving', '2025-03-16 02:44:16'),
(12, 'thg2.jpg', 'thanksgiving', '2025-03-16 02:44:16'),
(13, 'thg3.jpg', 'thanksgiving', '2025-03-16 02:44:16'),
(14, 'thg4.jpg', 'thanksgiving', '2025-03-16 02:44:16'),
(15, 'thg5.jpg', 'thanksgiving', '2025-03-16 02:44:16'),
(16, 'wed1.jpg', 'wedding', '2025-03-16 02:44:16'),
(17, 'wed2.jpg', 'wedding', '2025-03-16 02:44:16'),
(18, 'wed3.jpg', 'wedding', '2025-03-16 02:44:16'),
(19, 'wed4.jpg', 'wedding', '2025-03-16 02:44:16'),
(20, 'wed5.jpg', 'wedding', '2025-03-16 02:44:16');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `service_type` enum('photobooth','360booth','magazinebooth','partycart') NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(11) NOT NULL,
  `features` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`package_id`, `name`, `service_type`, `description`, `price`, `duration`, `features`, `created_at`) VALUES
(1, 'Party Cart Package A', 'partycart', 'Includes Hotdog/French Fries for 50 pax, Pop Corn for 50 pax, Cotton Candy for 50 pax', 6500.00, 2, 'Hotdog/French Fries: 50 pax\nPop Corn: 50 pax\nCotton Candy: 50 pax', '2025-03-16 02:44:16'),
(2, 'Party Cart Package B', 'partycart', 'Includes Pop Corn 50 pax, Cotton Candy 50 pax and choice of Premium Soft Serve Ice Cream or Premium Slush Drink for 60 pax', 8000.00, 2, 'Pop Corn: 50 pax\nCotton Candy: 50 pax\nPremium Soft Serve Ice Cream OR Premium Slush Drink: 60 pax', '2025-03-16 02:44:16'),
(3, 'Party Cart Package C', 'partycart', 'Includes Hotdog/French Fries 50 pax, Pop Corn 50 pax, Cotton Candy 50 pax, and choice of Premium Soft Serve Ice Cream or Premium Slush Drink for 60 pax', 10000.00, 2, 'Hotdog/French Fries: 50 pax\nPop Corn: 50 pax\nCotton Candy: 50 pax\nPremium option: 60 pax', '2025-03-16 02:44:16'),
(4, 'Party Cart Package D', 'partycart', 'Includes Hotdog/French Fries, Pop Corn and Cotton Candy for 50 pax each plus both Premium Soft Serve Ice Cream and Premium Slush Drink for 60 pax each', 14000.00, 2, 'Hotdog/French Fries: 50 pax\nPop Corn: 50 pax\nCotton Candy: 50 pax\nPremium Soft Serve Ice Cream: 60 pax\nPremium Slush Drink: 60 pax', '2025-03-16 02:44:16'),
(5, 'Magazine Booth Basic Package', 'magazinebooth', '4 hrs basic setup with customized decals, elegant setup, whole event rental, lightings', 10000.00, 4, '', '2025-03-16 02:44:16'),
(6, 'Magazine Booth with Photographer', 'magazinebooth', 'With Photographer (Soft Copies Only)', 15000.00, 4, '', '2025-03-16 02:44:16'),
(7, 'Magazine Booth Premium Package', 'magazinebooth', 'With Photographer and Unlimited Prints (includes 4R prints, FB online gallery, soft copies enhanced)', 20000.00, 4, '', '2025-03-16 02:44:16'),
(8, '360 Glam Booth Package', '360booth', 'Includes 2 hours of 360 booth operation, HD 20-30 sec video, customized overlay, professional setup with props and an iPad sharing station. Available sizes: 2 for 8-10 pax platform or 1 for 4-6 pax platform.', 8000.00, 2, 'Operation: 2 hrs\nVideo: 20-30 sec\nBackground Music: up to 3 choices\nSetup: Elegant studio lights, stanchions, red carpet, Black Sequins Backdrop\nIncludes: iPad Sharing Station', '2025-03-16 02:44:16'),
(9, '2-Hours Basic Package', 'photobooth', 'Includes unlimited photo shots, a customized layout (3-4 shots per print), use of props, backdrop, studio lights, live screen viewing, HD shots using a DSLR camera, and high quality 4R prints. Note: Transportation fee applies depending on the area.', 5000.00, 2, 'Unlimited Photo Shots; Customized Layout (3-4 shots per print); Props, Backdrop, Studio Lights; Live Screen Viewing; HD DSLR Shots; High Quality 4R Prints (waterproof, quick-dry and smudge-free)', '2025-03-16 02:44:16'),
(10, '2-Hours MAGNETIC Package', 'photobooth', 'All features of the Basic Package plus prints with magnet. Note: Transportation fee applies depending on the area.', 6500.00, 2, 'Unlimited Photo Shots and Prints with Magnet; Customized Layout (3-4 shots per print); Props, Backdrop, Studio Lights; Live Screen Viewing; HD DSLR Shots; High Quality 4R Prints (waterproof, quick-dry and smudge-free)', '2025-03-16 02:44:16');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_type` enum('full','down_payment') NOT NULL,
  `proof_of_payment` varchar(255) DEFAULT NULL,
  `status` enum('pending','verified','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `amount`, `payment_type`, `proof_of_payment`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 99999.00, 'full', 'payment_67d8fac48ffd3.png', 'verified', '2025-03-18 04:24:39', '2025-03-18 04:52:23'),
(2, 3, 99.00, 'full', NULL, 'rejected', '2025-03-18 05:03:28', '2025-03-18 05:03:45'),
(3, 2, 500.00, 'full', 'payment_67d9463f4713b.png', 'verified', '2025-03-18 09:34:20', '2025-03-18 10:09:26');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `name`, `description`, `price`, `duration`, `created_at`) VALUES
(1, 'Photobooth', 'Capture fun and memorable moments with a classic photobooth. Equipped with professional lighting, instant prints, and a variety of fun props—perfect for weddings, birthdays, corporate events, and more.', 5000.00, 3, '2025-03-16 02:44:16'),
(2, 'Partycart', 'Make your event extra special with a customizable party food cart. From sweet treats to savory snacks, this food cart offers a fun and interactive experience that guests will love.', 6500.00, 4, '2025-03-16 02:44:16'),
(3, '360 Booth', 'Step into a 360° video platform and capture your best angles in motion! The setup includes stunning slow-motion, boomerang, and HD video options to make guests feel extra special.', 5000.00, 2, '2025-03-16 02:44:16'),
(4, 'Magazine Booth', 'Ever dreamed of being on the cover of a magazine? The Magazine Booth lets guests step into the spotlight—pose like a celebrity and get an instant magazine-style printout featuring the event theme and custom designs.', 10000.00, 3, '2025-03-16 02:44:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('client','admin') DEFAULT 'client',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `first_name`, `last_name`, `phone`, `role`, `created_at`, `updated_at`) VALUES
(1, 'almo.corcuera.coc@phinmaed.com', '$2y$10$Pkm0a56PGdKaDt6DUv7pYuPRIfv2FKFLxrvBsozD.xrvcws2C0Mly', 'Alter Lloyd', 'Corcuera', '0981 107 9692', 'client', '2025-03-16 02:37:52', '2025-03-16 02:37:52'),
(2, 'corcueraalter@gmail.com', '$2y$10$mGNZDNUaxG0QkCY7n53P4OaTuLZm2QWaK0V4Jid8g2MBSW.GX7Nka', 'Alter Lloyd', 'Corcuera', '09811079692', 'admin', '2025-03-16 03:17:03', '2025-03-16 03:18:25'),
(3, 'alter@gmail.com', '$2y$10$ta7sandEsFXjAV1YPa5VQeTmXN4SQVEmCHoMk3lL97gD2NWr.7ov2', 'lo', 'lo', '09227777777', 'client', '2025-03-18 09:54:45', '2025-03-18 09:54:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_booking_status` (`status`);

--
-- Indexes for table `booking_packages`
--
ALTER TABLE `booking_packages`
  ADD PRIMARY KEY (`booking_package_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `booking_services`
--
ALTER TABLE `booking_services`
  ADD PRIMARY KEY (`booking_service_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `idx_message_read` (`is_read`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_notification_read` (`is_read`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`),
  ADD KEY `idx_service_type` (`service_type`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `idx_payment_status` (`status`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `booking_packages`
--
ALTER TABLE `booking_packages`
  MODIFY `booking_package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `booking_services`
--
ALTER TABLE `booking_services`
  MODIFY `booking_service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `booking_packages`
--
ALTER TABLE `booking_packages`
  ADD CONSTRAINT `booking_packages_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `booking_packages_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`);

--
-- Constraints for table `booking_services`
--
ALTER TABLE `booking_services`
  ADD CONSTRAINT `booking_services_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `booking_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

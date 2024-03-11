-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2024 at 02:37 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shoecart`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `product_id`, `quantity`, `user_id`) VALUES
(1, 2, 3, 1),
(2, 3, 1, 1),
(3, 1, 2, 1),
(4, 3, 1, 1),
(6, 1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `product_id`, `user_id`, `rating`, `image_url`, `comment`) VALUES
(5, 2, 3, 5, '/images/img-red-1.jpg', 'This running shoe is amazing! It provides excellent support and comfort for long runs.'),
(6, 3, 4, 4, '/images/img-yellow-1.jpg', 'The arch support in this walking shoe is fantastic. Great for long walks!'),
(7, 4, 3, 5, '/images/img-blue-1.jpg', 'Love these athletic shoes! They are lightweight and comfortable, perfect for workouts.'),
(8, 5, 4, 3, '/images/img-grey-1.jpg', 'These casual shoes are stylish and comfortable. I wear them every day.'),
(9, 1, 5, 4, '/images/img-white-blue-1.jpg', 'Great shoes! They are very comfortable and durable. I highly recommend them for tennis players.'),
(10, 2, 1, 5, 'http://example.com/image.jpg', 'Great product!'),
(12, 2, 2, 5, 'http://example.com/image.jpg', 'Great product! very comfortable to use.'),
(16, 1, 1, 5, 'http://example.com/image.jpg', 'Great product!'),
(17, 1, 1, 5, 'http://example.com/image.jpg', 'Great product!');

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `product_size` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `product_size`) VALUES
(1, 1, 1, 2, '8'),
(2, 1, 2, 1, '7'),
(3, 2, 3, 3, '9'),
(4, 2, 4, 1, '10'),
(5, 3, 5, 1, '6'),
(11, 10, 1, 2, '10'),
(12, 10, 2, 1, '11');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `delivery_status` varchar(50) DEFAULT NULL,
  `return_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `order_date`, `payment_method`, `delivery_status`, `return_status`) VALUES
(1, 3, 150.97, '2024-03-08', 'Credit Card', 'Pending', 'Not Returned'),
(2, 4, 190.96, '2024-03-08', 'PayPal', 'Pending', 'Not Returned'),
(3, 5, 40.99, '2024-03-08', 'Cash on Delivery', 'Pending', 'Not Returned'),
(10, 1, 100.00, '2022-01-01', 'Credit Card', 'Delivered', 'No Return');

-- --------------------------------------------------------

--
-- Table structure for table `productimages`
--

CREATE TABLE `productimages` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productimages`
--

INSERT INTO `productimages` (`image_id`, `product_id`, `image_url`) VALUES
(1, 1, '/images/img-white-blue-1.jpg'),
(2, 1, '/images/img-white-blue-2.jpg'),
(3, 1, '/images/img-white-blue-3.jpg'),
(4, 2, '/images/img-red-1.jpg'),
(5, 2, '/images/img-red-2.jpg'),
(6, 2, '/images/img-red-3.jpg'),
(7, 3, '/images/img-yellow-1.jpg'),
(8, 3, '/images/img-yellow-2.jpg'),
(9, 3, '/images/img-yellow-3.jpg'),
(10, 4, '/images/img-blue-1.jpg'),
(11, 4, '/images/img-blue-2.jpg'),
(12, 4, '/images/img-blue-3.jpg'),
(13, 5, '/images/img-grey-1.jpg'),
(14, 5, '/images/img-grey-2.jpg'),
(15, 5, '/images/img-grey-3.jpg'),
(20, 10, '/images/new-shoe-1.jpg'),
(21, 10, '/images/new-shoe-2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `shipping_cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `title`, `description`, `price`, `shipping_cost`) VALUES
(1, 'Air Tennis Shoe', 'White Blue coloured Shoe for Men provides cushioning and support effect for the feet with Non Slip Sport Tennis, Gym, Walking Shoes Sneakers.', 46.99, 0.00),
(2, 'Air Running Shoe', 'Red coloured Shoe for Men extremely lightweight running shoes great for running, walking, fitness, jogging, outdoor sports, workout, and hiking.', 56.99, 0.00),
(3, 'Air Walking Shoe', 'Yellow coloured Arch Support Designed shoes have good arch support with the memory foam insole and cloudfoam cushioning perfect for long walking.', 44.99, 0.00),
(4, 'Air Athletic Shoe', 'Black coloured unisex shoe with double layered knitted fabric, which is lightweight, breathable and comfortable, keep your feet dry and cool.', 55.99, 0.00),
(5, 'Air Casual Shoe', 'Grey coloured unisex sneakers made from ultra light natural rubber material, flexible grooves, anti-skid and grip, adapts to any road condition', 40.99, 0.00),
(10, 'New Tennis Shoe', 'New Shoe Description', 50.99, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `productsizes`
--

CREATE TABLE `productsizes` (
  `size_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `size_us` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productsizes`
--

INSERT INTO `productsizes` (`size_id`, `product_id`, `size_us`, `quantity`) VALUES
(1, 1, 6, 8),
(2, 1, 7, 8),
(3, 1, 8, 4),
(4, 1, 9, 8),
(5, 1, 10, 4),
(6, 1, 11, -8),
(7, 1, 12, 8),
(8, 2, 6, 9),
(9, 2, 7, 7),
(10, 2, 8, 9),
(11, 2, 9, 9),
(12, 2, 10, 9),
(13, 2, 11, 1),
(14, 2, 12, 9),
(15, 3, 6, 7),
(16, 3, 7, 7),
(17, 3, 8, 7),
(18, 3, 9, 1),
(19, 3, 10, 7),
(20, 3, 11, 7),
(21, 3, 12, 7),
(22, 4, 6, 9),
(23, 4, 7, 9),
(24, 4, 8, 9),
(25, 4, 9, 9),
(26, 4, 10, 7),
(27, 4, 11, 9),
(28, 4, 12, 9),
(29, 5, 6, 7),
(30, 5, 7, 9),
(31, 5, 8, 9),
(32, 5, 9, 9),
(33, 5, 10, 9),
(34, 5, 11, 9),
(35, 5, 12, 9),
(49, 10, 6, 12);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `purchase_history` text DEFAULT NULL,
  `shipping_address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `username`, `purchase_history`, `shipping_address`) VALUES
(1, 'admin', 'admin@shoecart.com', 'password123', 'admin', '0', '365 Lester Main St'),
(2, 'user1', 'user1@shoecart.com', 'user123', 'user1', '0', '350 Albert St'),
(3, 'Amarnath', 'amarnath@example.com', 'password123', 'amarnath', '0', '123 Main St'),
(4, 'Rajinikanth', 'rajinikanth@example.com', 'password456', 'rajinikanth', '0', '456 Elm St'),
(5, 'Ajithkumar', 'ajithkumar@example.com', 'password789', 'ajithkumar', '0', '789 Oak St'),
(8, 'Annamalai', 'annamalai@shoecart.com', 'password123', 'annamalai', '0', '123 Main St');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `fk_order_id` (`order_id`),
  ADD KEY `fk_product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `productimages`
--
ALTER TABLE `productimages`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `productsizes`
--
ALTER TABLE `productsizes`
  ADD PRIMARY KEY (`size_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orderdetails`
--
ALTER TABLE `orderdetails`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `productimages`
--
ALTER TABLE `productimages`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `productsizes`
--
ALTER TABLE `productsizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `fk_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `productimages`
--
ALTER TABLE `productimages`
  ADD CONSTRAINT `productimages_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `productsizes`
--
ALTER TABLE `productsizes`
  ADD CONSTRAINT `productsizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

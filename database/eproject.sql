-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2026 at 06:19 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `created_at`) VALUES
(2, 3, '2026-04-20 20:54:35');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `p_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `scard_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `p_id`, `quantity`, `scard_id`) VALUES
(5, 2, 32, 1, 20),
(6, 2, 27, 1, NULL),
(7, 2, 61, 1, 16),
(9, 2, 68, 1, NULL),
(10, 2, 47, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `child_cat`
--

CREATE TABLE `child_cat` (
  `ccat_id` int(11) NOT NULL,
  `ccat_name` varchar(100) NOT NULL,
  `scat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `child_cat`
--

INSERT INTO `child_cat` (`ccat_id`, `ccat_name`, `scat_id`) VALUES
(1, 'sunblockkk', 3),
(2, 'serum', 3),
(3, 'sunblock', 10),
(4, 'serum', 10),
(5, 'foundation', 11),
(6, 'lipstick', 11),
(7, 'toner', 10),
(8, 'eye cream', 10),
(9, 'face mask', 10),
(10, 'blush', 11),
(11, 'highligters', 11),
(12, 'mascara', 11),
(13, 'liner', 11),
(15, 'concealer', 11),
(16, 'face wash', 10),
(17, 'lip balm', 10);

-- --------------------------------------------------------

--
-- Table structure for table `main_cat`
--

CREATE TABLE `main_cat` (
  `mcat_id` int(11) NOT NULL,
  `mcat_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `main_cat`
--

INSERT INTO `main_cat` (`mcat_id`, `mcat_name`) VALUES
(3, 'Jewellery'),
(5, 'Cosmetic');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `status`) VALUES
(2, 3, '2026-04-20 20:56:45', 3000, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `p_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `scard_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `p_id`, `quantity`, `price`, `scard_id`) VALUES
(2, 2, 29, 2, 1500, 2);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `p_id` int(11) NOT NULL,
  `p_name` varchar(100) NOT NULL,
  `pro_description` varchar(100) NOT NULL,
  `p_price` int(11) NOT NULL,
  `p_image` varchar(255) DEFAULT NULL,
  `scat_id` int(11) DEFAULT NULL,
  `ccat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`p_id`, `p_name`, `pro_description`, `p_price`, `p_image`, `scat_id`, `ccat_id`) VALUES
(14, 'Charm Bracelet', 'Rapunzel Inspired Charm Bracelet', 1000, 'Rapunzel Inspired Charm Bracelet.jpg', 6, NULL),
(15, 'swan pendent', 'Effortlessly chic and delicate', 1200, 'swan ring.jpg', 7, NULL),
(16, 'Elegant Pink Floral Butterfly Choker Necklace', 'Blush blooms meets shimmering golden wings', 2000, 'Elegant Pink Floral Butterfly Choker Necklace.jpg', 7, NULL),
(17, 'Enchanted Flora Bow Necklace', 'Timeless floral grace with shimmering stones.', 1200, 'Enchanted Flora Bow Necklace.jpg', 7, NULL),
(18, 'floral bangles', 'Nature-inspired elegance in a bold design.', 1500, 'floral bangles.jpg', 9, NULL),
(20, 'Mermaid Tail Claw Clip', 'Enchanted sea style for your hair.', 500, 'Mermaid Tail Claw Clip.jpg', 14, NULL),
(21, 'tulip claw clip', 'Blooming tulips for a graceful hold.', 500, 'tulip claw clip.jpg', 14, NULL),
(22, 'Chinese hair pin', 'Classic artistry for your modern updo.', 500, 'Chinese hair pin.jpg', 14, NULL),
(24, 'arm cuff gold', 'Bold golden curves for your arm.', 1000, 'Arm cuff gold.jpg', 14, NULL),
(25, 'arm cuff ', 'Sleek, hammered copper for effortless style.', 999, 'Arm cuff copper.jpg', 14, NULL),
(26, 'Birthstone Flower Ring', 'Meaningful crystals and delicate petal details.', 1000, 'Birthstone Flower Ring.jpg', 5, NULL),
(27, 'Pink ear ring', 'Soft pink stones with elegant sparkle.', 800, 'earring.jpg', 8, NULL),
(28, 'pearl hair pins', 'Timeless pearls for a graceful finish.', 700, 'pearl hair pins.jpg', 14, NULL),
(29, 'vitamic C serum', 'Radiant glow in every single drop.', 1500, 'vit c  serun.jpg', 10, 4),
(30, 'Anua Glass Skin Brightening Serum', 'Anua 10% Niacinamide+ 4% Tranexamic Acid Serum, Ceramide, Hyaluronic Acid Korean Skincare Glass Skin', 1500, 'Anua 10% Niacinamide+ 4% Tranexamic Acid Serum, Ceramide, Hyaluronic Acid Korean Skincare Glass Skin.jpg', 10, 4),
(31, 'serum for active acne', 'The Derma Co 2% Salicylic Acid Serum for Active Acne', 1500, 'The Derma Co 2% Salicylic Acid Serum for Active Acne.jpg', 10, 4),
(32, 'foundation', 'flawless full coverage foundation', 5000, 'foundation.jpg', 11, 5),
(33, 'lipstick', 'matt lipstick', 2000, 'lip stick.jpg', 11, 6),
(35, '1pair Bow Line Butterfly Earrings', 'Elegant Butterfly Bow Studs', 3000, '1777215984_1pair Bow Line Butterfly Earrings.jpg', 8, NULL),
(36, '1 Pair Liquid Metal Design Heart Shape ear ring', 'Minimalist Metallic Heart Hoops', 2000, '1777216349_1 Pair Liquid Metal Design Heart Shape ear ring.jpg', 8, NULL),
(37, 'Anua 10% Niacinamide+ 4% Tranexamic Acid Serum, Ceramide, Hyaluronic Acid Korean Skincare Glass Skin', 'Glowing Skin Barrier Booster', 5000, '1777216699_Anua 10% Niacinamide+ 4% Tranexamic Acid Serum, Ceramide, Hyaluronic Acid Korean Skincare Glass Skin.jpg', 10, 4),
(38, 'eye liner', 'Long-Lasting Intense Liquid Liner', 1000, '1777216759_eye liner.jpg', 11, 13),
(39, 'Anua Rice 70 Glow Milky Toner, Glass & Dewy Skin, Hydrating & Barrier Care', 'Radiant Dewy Skin Essence', 4000, '1777217161_Anua Rice 70 Glow Milky Toner, Glass & Dewy Skin, Hydrating & Barrier Care.jpg', 10, 7),
(40, 'Arm cuff gold', 'Minimalist Bold Arm Band', 2000, '1777217283_Arm cuff gold.jpg', 14, NULL),
(41, 'chunkey bangels', 'Vibrant Glossy Acrylic Bangles', 6000, '1777217334_chunkey bangels.jpg', 9, NULL),
(42, 'Biodance Bio Collagen Real Deep Mask', 'Overnight Bio-Collagen Glow', 800, '1777217582_Biodance Bio Collagen Real Deep Mask.jpg', 10, NULL),
(43, 'CeraVe Foaming Facial Cleanser – Oil-Free Glow for Oily Skin', 'Refreshing Foaming Face Wash', 2000, '1777217702_CeraVe Foaming Facial Cleanser – Oil-Free Glow for Oily Skin.jpg', 10, 16),
(44, 'Chinese hair pin', 'Ornate Traditional Hanfu Jewelry', 4000, '1777217825_Chinese hair pin.jpg', 14, NULL),
(45, 'collegen night mask', 'Overnight Firming Collagen Boost', 3000, '1777217992_collegen night mask.jpg', 10, 9),
(47, 'cream blush', 'Velvety Radiant Cream Blush', 5600, '1777218163_cream blush.jpg', 11, 10),
(49, 'earring', 'Minimalist Dainty Crystal Drops', 1000, '1777218224_earring.jpg', 8, NULL),
(53, 'Elegant Pink Floral Butterfly Choker Necklace', 'Elegant Spring Blossom Choker', 7000, '1777218424_Elegant Pink Floral Butterfly Choker Necklace.jpg', 7, NULL),
(54, 'Enchanted Flora Bow Necklace', 'Dainty Enchanted Bloom Wrap', 5000, '1777218546_Enchanted Flora Bow Necklace.jpg', 7, NULL),
(56, 'highlighter', 'Seamless Dewy Finish Glow', 7000, '1777218926_highlighter.jpg', 11, 11),
(57, 'stars orbit bracelet', 'Cosmic Shimmer Linked Bracelet', 5000, '1777221230_stars orbit bracelet.jpg', 6, NULL),
(58, 'luxury flower zircon', 'Luxe Crystal Bloom bracelet', 4000, '1777221774_luxury flower zircon adjustable female prom party bracelet - Light Yellow Gold Color.jpg', 6, NULL),
(59, 'Bracelet with green colour', 'Minimalist Mint Green Cuff', 4000, '1777221868_Bracelet with green colour.jpg', 6, NULL),
(60, 'Red Flower Diamante Headband', 'Elegant Red Blossom Diamante Band', 6000, '1777221945_Red Flower Diamante Headband.jpg', 14, NULL),
(61, 'Lip Tints', 'Rhode Peptide Lip Tints', 5000, '1777222066_Rhode Peptide Lip Tints.jpg', 11, 6),
(62, 'Rhinestone Cuff', 'Opal Flowers Rhinestone Cuff', 5000, '1777222114_Opal Flowers Rhinestone Cuff.jpg', 6, NULL),
(63, 'Ultra-Length Defined Mascara', 'Deep Carbon Black Finish', 2000, '1777275015_mascara.jpg', 11, 12),
(64, 'Biodance Bio Collagen Real Deep Mask.jpg', 'Deep Absorption \"Glass Skin\" Mask', 2000, '1777275301_Biodance Bio Collagen Real Deep Mask.jpg', 10, 9),
(65, 'Retinol eye cream', 'Awake & Radiant Eye Treatment', 2500, '1777275416_Retinol eye cream.jpg', 10, 8),
(66, 'Invisible Broad Spectrum Shield', 'Provides a powerful barrier against both UVA and UVB rays to prevent sun damage and premature aging.', 5000, '1777275525_sun block.jpg', 10, 3),
(67, 'Tear-Free Kids\' Sun Defense', 'Specifically designed for delicate skin, using hypoallergenic ingredients that won\'t cause irritatio', 4000, '1777275603_sun block kids.jpg', 10, 3),
(68, 'Velvet Petal Soft-Touch Blush', 'A lightweight, silky formula that melts into the skin for a natural, \"lit-from-within\" flush.', 4000, '1777276206_rare blush.jpg', 11, 10),
(69, 'Matcha Biome Amino Acne Cleansing Foam', 'Balancing Matcha Biome Daily Wash', 2000, '1777276352_Matcha Biome Amino Acne Cleansing Foam.jpg', 10, 16),
(70, 'Glow-Boosting Rice Bran Mask', 'Instant Brightening Rice Cream Mask', 2000, '1777276545_rice toner.jpg', 10, 16),
(71, 'Hydrating Rice Water Essence', 'A unique milky formula that balances oil production while providing a deep moisture boost.', 2000, '1777276503_1777276439_rice mask.jpg', 10, 9),
(72, 'Soft Microfiber Bow Spa Band', 'Made from premium, skin-friendly fabrics that won\'t pull on your hair or irritate your forehead.', 2000, '1777276640_skin care acosories.jpg', 14, NULL),
(73, 'Luxe High-Shine Glaze Oil', 'Infused with natural oils (like Jojoba or Rosehip) to treat dry lips while adding a beautiful tint.', 2000, '1777276737_Lip Oil Moisturizing.jpg', 10, 17),
(74, 'The Ordinary Amino Acids Lip Balm', 'Creates a protective moisture barrier that keeps lips soft and supple even in harsh weather.', 1500, '1777276785_The Ordinary Amino Acids Lip Balm.jpg', 10, 17);

-- --------------------------------------------------------

--
-- Table structure for table `product_ratings`
--

CREATE TABLE `product_ratings` (
  `rating_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating_value` int(11) DEFAULT NULL CHECK (`rating_value` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_ratings`
--

INSERT INTO `product_ratings` (`rating_id`, `p_id`, `user_id`, `rating_value`, `created_at`) VALUES
(1, 3, 5, 4, '2026-04-24 20:46:56'),
(2, 26, 5, 3, '2026-04-24 20:46:56'),
(3, 14, 1, 5, '2026-04-24 20:46:56'),
(4, 15, 3, 3, '2026-04-24 20:46:56'),
(5, 16, 4, 4, '2026-04-24 20:46:56'),
(6, 17, 1, 4, '2026-04-24 20:46:56'),
(7, 27, 1, 5, '2026-04-24 20:46:56'),
(8, 18, 4, 5, '2026-04-24 20:46:56'),
(9, 19, 3, 3, '2026-04-24 20:46:56'),
(10, 29, 4, 3, '2026-04-24 20:46:56'),
(11, 30, 3, 3, '2026-04-24 20:46:56'),
(12, 31, 4, 5, '2026-04-24 20:46:56'),
(13, 32, 5, 4, '2026-04-24 20:46:56'),
(14, 33, 4, 5, '2026-04-24 20:46:56'),
(15, 34, 5, 5, '2026-04-24 20:46:56'),
(16, 20, 1, 3, '2026-04-24 20:46:56'),
(17, 21, 2, 5, '2026-04-24 20:46:56'),
(18, 22, 1, 4, '2026-04-24 20:46:56'),
(19, 23, 4, 5, '2026-04-24 20:46:56'),
(20, 24, 3, 5, '2026-04-24 20:46:56'),
(21, 25, 5, 4, '2026-04-24 20:46:56'),
(22, 28, 5, 5, '2026-04-24 20:46:56');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'ADMIN'),
(2, 'CUSTOMERS');

-- --------------------------------------------------------

--
-- Table structure for table `shade_cards`
--

CREATE TABLE `shade_cards` (
  `scard_id` int(11) NOT NULL,
  `group_name` varchar(50) DEFAULT NULL,
  `shade_name` varchar(50) DEFAULT NULL,
  `shade_color_code` varchar(10) DEFAULT NULL,
  `shade_image` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shade_cards`
--

INSERT INTO `shade_cards` (`scard_id`, `group_name`, `shade_name`, `shade_color_code`, `shade_image`) VALUES
(1, 'Highlighter', 'MM043 Champagne', '#E7D1B1', 'high_shades.jpg'),
(2, 'Highlighter', 'MM044 Rose Gold', '#C69383', 'high_shades.jpg'),
(3, 'Highlighter', 'MM045 Bronze', '#9F634F', 'high_shades.jpg'),
(4, 'Lipstick', 'Nude 01', '#E2B0A3', 'lip_shades.jpg'),
(5, 'Lipstick', 'Nude 02', '#CB8D7D', 'lip_shades.jpg'),
(6, 'Lipstick', 'Nude 03', '#B8786B', 'lip_shades.jpg'),
(7, 'Lipstick', 'Nude 04', '#D69176', 'lip_shades.jpg'),
(8, 'Lipstick', 'Nude 05', '#A56A5B', 'lip_shades.jpg'),
(9, 'Lipstick', 'Nude 06', '#985D52', 'lip_shades.jpg'),
(10, 'Lipstick', 'Nude 07', '#A3605F', 'lip_shades.jpg'),
(11, 'Lipstick', 'Nude 08', '#7E463E', 'lip_shades.jpg'),
(12, 'Lipstick', 'Nude 09', '#6D3934', 'lip_shades.jpg'),
(13, 'Lipstick', 'Nude 10', '#4A2521', 'lip_shades.jpg'),
(14, 'Lip Tint', 'Ribbon (Sheer Pink)', '#F6A7B8', 'rode_shades.jpg'),
(15, 'Lip Tint', 'Toast (Rose Taupe)', '#B6887D', 'rode_shades.jpg'),
(16, 'Lip Tint', 'Raspberry Jelly (Crushed Berry)', '#9F1D49', 'rode_shades.jpg'),
(17, 'Lip Tint', 'PBJ (Warm Berry Brown)', '#7D3B36', 'rode_shades.jpg'),
(18, 'Lip Tint', 'Espresso (Rich Brown)', '#50312B', 'rode_shades.jpg'),
(19, 'Base Makeup', 'Ivory Silk', '#EED4B9', 'base_shades.jpg'),
(20, 'Base Makeup', 'Classic Beige', '#D6AF8A', 'base_shades.jpg'),
(21, 'Base Makeup', 'Golden Sand', '#C39160', 'base_shades.jpg'),
(22, 'Base Makeup', 'Honey Caramel', '#A97546', 'base_shades.jpg'),
(23, 'Base Makeup', 'Roasted Mocha', '#754A34', 'base_shades.jpg'),
(24, 'Blush 1', '01 Muted Beige', '#D29788', 'blush_shadde_2.jpg'),
(25, 'Blush 1', '02 Rose Beige', '#D58673', 'blush_shadde_2.jpg'),
(26, 'Blush 1', '03 Blessing Coral', '#E46D54', 'blush_shadde_2.jpg'),
(27, 'Blush 1', '04 Nut Brown', '#B65B43', 'blush_shadde_2.jpg'),
(28, 'Blush 1', '05 India Rose', '#CC847D', 'blush_shadde_2.jpg'),
(29, 'Blush 1', '06 Raw Pink', '#D47B8E', 'blush_shadde_2.jpg'),
(30, 'Blush 1', '07 Mauve Pink', '#B87282', 'blush_shadde_2.jpg'),
(31, 'Blush 1', '08 Berry Plum', '#8B3851', 'blush_shadde_2.jpg'),
(32, 'Blush 2', 'Rosa', '#B87C64', 'blush_shade.jpg'),
(33, 'Blush 2', 'Mulberry', '#953D43', 'blush_shade.jpg'),
(34, 'Blush 2', 'Ruby', '#8B2C2F', 'blush_shade.jpg'),
(35, 'Blush 2', 'Maple', '#9A5B49', 'blush_shade.jpg'),
(36, 'Blush 2', 'Mocha', '#7B3F38', 'blush_shade.jpg'),
(37, 'Blush 2', 'Morello', '#50242B', 'blush_shade.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `sub_cat`
--

CREATE TABLE `sub_cat` (
  `sub_cat_id` int(11) NOT NULL,
  `sub_cat_name` varchar(100) NOT NULL,
  `mcat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_cat`
--

INSERT INTO `sub_cat` (`sub_cat_id`, `sub_cat_name`, `mcat_id`) VALUES
(5, 'rings', 3),
(6, 'bracelet', 3),
(7, 'pendent', 3),
(8, 'ear ring', 3),
(9, 'bangles', 3),
(10, 'skin care', 5),
(11, 'makeup', 5),
(12, 'lenses', 3),
(14, 'accessories', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role_id`, `address`, `phone`, `dob`, `remarks`) VALUES
(1, 'amna', 'amna@gmail.com', 'laiba@12345687', 2, 'karachii', '0322257355', NULL, NULL),
(2, 'ADMIN', 'admin@gmail.com', 'admin1234', 1, 'karachi', '03222333027', NULL, NULL),
(3, 'customer', 'customer1@gmail.com', 'customer12', 2, 'karachi', '03222333027', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `p_id`, `created_at`) VALUES
(1, 3, 33, '2026-04-20 18:56:33'),
(2, 3, 14, '2026-04-20 19:20:17'),
(3, 3, 32, '2026-04-20 19:20:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `p_id` (`p_id`),
  ADD KEY `fk_cart_shade_link` (`scard_id`);

--
-- Indexes for table `child_cat`
--
ALTER TABLE `child_cat`
  ADD PRIMARY KEY (`ccat_id`),
  ADD KEY `scat_fk` (`scat_id`);

--
-- Indexes for table `main_cat`
--
ALTER TABLE `main_cat`
  ADD PRIMARY KEY (`mcat_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `p_id` (`p_id`),
  ADD KEY `fk_order_shade` (`scard_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`p_id`),
  ADD KEY `fk_product_subcat` (`scat_id`),
  ADD KEY `ccat_id` (`ccat_id`);

--
-- Indexes for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD PRIMARY KEY (`rating_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shade_cards`
--
ALTER TABLE `shade_cards`
  ADD PRIMARY KEY (`scard_id`);

--
-- Indexes for table `sub_cat`
--
ALTER TABLE `sub_cat`
  ADD PRIMARY KEY (`sub_cat_id`),
  ADD KEY `mcat_id` (`mcat_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `child_cat`
--
ALTER TABLE `child_cat`
  MODIFY `ccat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `main_cat`
--
ALTER TABLE `main_cat`
  MODIFY `mcat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `product_ratings`
--
ALTER TABLE `product_ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shade_cards`
--
ALTER TABLE `shade_cards`
  MODIFY `scard_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `sub_cat`
--
ALTER TABLE `sub_cat`
  MODIFY `sub_cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`p_id`) REFERENCES `product` (`p_id`),
  ADD CONSTRAINT `fk_cart_shade_link` FOREIGN KEY (`scard_id`) REFERENCES `shade_cards` (`scard_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_order_shade` FOREIGN KEY (`scard_id`) REFERENCES `shade_cards` (`scard_id`),
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`p_id`) REFERENCES `product` (`p_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_subcat` FOREIGN KEY (`scat_id`) REFERENCES `sub_cat` (`sub_cat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`ccat_id`) REFERENCES `child_cat` (`ccat_id`) ON DELETE SET NULL;

--
-- Constraints for table `sub_cat`
--
ALTER TABLE `sub_cat`
  ADD CONSTRAINT `sub_cat_ibfk_1` FOREIGN KEY (`mcat_id`) REFERENCES `main_cat` (`mcat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

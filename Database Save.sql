/**!
 * @fileOverview List View of Database elements
 * @version 1.0
 * @license
 * Copyright (c) 2017 Tom Grozev and contributors (get listed here by pull requests)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software with author's permission, and to permit persons to whom
 * the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice (ALL ABOVE THIS LINE) and this permission notice
 * shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `afl`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `addressLine1` varchar(50) NOT NULL,
  `addressLine2` varchar(50) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postalCode` varchar(15) DEFAULT NULL,
  `country` varchar(50) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL,
  `sex` enum('male','female') NOT NULL,
  `favTeam` varchar(30) NOT NULL,
  `dob` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=478 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `LastName`, `FirstName`, `phone`, `addressLine1`, `addressLine2`, `city`, `state`, `postalCode`, `country`, `sex`, `favTeam`, `dob`) VALUES
(112, 'Jean King', 'King', 'Jean', '7025551838', '8489 Strong St.', NULL, 'Las Vegas', 'NV', '83030', 'USA', 'male', 'Western Bulldogs', '0000-00-00'),
(119, 'Janine Labrune', 'Labrune', 'Janine ', '40.67.8555', '67, rue des Cinquante Otages', NULL, 'Nantes', NULL, '44000', 'France', 'male', 'stkilda', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `id` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `productID` int(15) NOT NULL,
  `qty` int(11) NOT NULL,
  `subTotal` float NOT NULL,
  `cost` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3033 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`id`, `orderID`, `productID`, `qty`, `subTotal`, `cost`) VALUES
(1, 1, 1, 100, 25000, 10000),
(3, 1, 2, 2, 20, 8),
(4, 2, 1, 49, 35.29, 0),
(5, 3, 3, 25, 1724.75, 0),
(6, 4, 2, 26, 0, 0),
(7, 5, 2, 45, 0, 0),
(8, 6, 2, 46, 0, 0),
(9, 7, 0, 39, 95.55, 0);

--
-- Triggers `orderdetails`
--
DELIMITER $$
CREATE TRIGGER `subTotal` BEFORE UPDATE ON `orderdetails`
 FOR EACH ROW BEGIN 
    SET NEW.subTotal=(SELECT products.price FROM products WHERE products.productID=NEW.productID)*NEW.qty;

    SET NEW.cost=(SELECT products.cost FROM products WHERE products.productID=NEW.productID)*NEW.qty;
    
    UPDATE orders SET orders.totalPrice=(SELECT sum(subTotal)
                   FROM orderdetails WHERE orderdetails.orderID=orders.orderID)-OLD.subTotal+NEW.subTotal WHERE orders.orderID=NEW.orderID;
                   
    UPDATE orders SET orders.totalQty=(SELECT sum(qty)
                   FROM orderdetails WHERE orderdetails.orderID=orders.orderID)-OLD.qty+NEW.qty WHERE orders.orderID=NEW.orderID;
                   
    UPDATE orders SET orders.profit=(SELECT sum(subTotal)
                   FROM orderdetails WHERE orderdetails.orderID=orders.orderID)-OLD.subTotal+NEW.subTotal-(SELECT sum(cost)
                   FROM orderdetails WHERE orderdetails.orderID=orders.orderID)-OLD.cost+NEW.cost WHERE orders.orderID=NEW.orderID;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderID` int(10) NOT NULL,
  `customerID` int(6) NOT NULL,
  `date` date NOT NULL,
  `buyMethod` text NOT NULL,
  `totalQty` int(5) NOT NULL,
  `totalPrice` float NOT NULL,
  `profit` float NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`orderID`, `customerID`, `date`, `buyMethod`, `totalQty`, `totalPrice`, `profit`) VALUES
(1, 119, '2017-06-14', 'Credit Card', 102, 25020, 15020),
(2, 119, '2017-06-07', 'Cash', 5, 1550, 90),
(3, 112, '2017-05-03', 'Cash', 7, 523, 4),
(4, 112, '2017-04-12', 'PayPal', 8, 43, 1),
(5, 121, '2016-08-18', 'Credit Card', 9, 5, 100),
(6, 121, '2017-04-03', 'PayPal', 2, 5, 20),
(7, 121, '2017-05-25', 'Credit Card', 1, 25, 10);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `productID` int(11) NOT NULL,
  `name` varchar(70) NOT NULL,
  `description` text NOT NULL,
  `qtyInStock` int(6) NOT NULL,
  `reorderLevel` int(6) NOT NULL,
  `onOrder` int(6) NOT NULL,
  `cost` double NOT NULL,
  `price` double NOT NULL,
  `purchaseDate` date NOT NULL,
  `team` varchar(30) NOT NULL DEFAULT 'Adelaide Crows',
  `supplierID` int(6) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`productID`, `name`, `description`, `qtyInStock`, `reorderLevel`, `onOrder`, `cost`, `price`, `purchaseDate`, `team`, `supplierID`) VALUES
(1, 'Adelaide Crows Cap', 'Yellow Crows Cap', 100, 50, 0, 100, 250, '2017-05-22', 'Sydney Swans', 1),
(2, 'Brisbane Lions Beanie', 'Blue and Brown Lions Beanie', 4, 10, 20, 4, 10, '2017-05-22', 'Adelaide', 2);

--
-- Triggers `products`
--
DELIMITER $$
CREATE TRIGGER `cost_sell_price` BEFORE UPDATE ON `products`
 FOR EACH ROW BEGIN
  SET NEW.price=NEW.cost * 2.5;
    
    UPDATE suppliers SET suppliers.productsInStock=(SELECT count(supplierID) FROM products WHERE products.supplierID=suppliers.id)*NEW.qtyInStock WHERE suppliers.id=NEW.supplierID;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `productsInStock` int(8) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1703 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `productsInStock`) VALUES
(1, 'Murphy', 2),
(2, 'Patterson', 4),
(3, 'Firrelli', 0),
(1088, 'Patterson', 0),
(1102, 'Bondur', 0),
(1143, 'Bow', 0),
(1165, 'Jennings', 0),
(1166, 'Thompson', 0),
(1188, 'Firrelli', 0),
(1216, 'Patterson', 0),
(1286, 'Tseng', 0),
(1323, 'Vanauf', 0),
(1337, 'Bondur', 0),
(1370, 'Hernandez', 0),
(1401, 'Castillo', 0),
(1501, 'Bott', 0),
(1504, 'Jones', 0),
(1611, 'Fixter', 0),
(1612, 'Marsh', 0),
(1619, 'King', 0),
(1621, 'Nishi', 0),
(1625, 'Kato', 0),
(1702, 'Gerard', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productID` (`productID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`productID`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=478;
--
-- AUTO_INCREMENT for table `orderdetails`
--
ALTER TABLE `orderdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3033;
--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orderID` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `productID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=117;
--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1703;
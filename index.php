<!--/**!
 * @fileOverview Index View
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
 */-->

<html>
	<head>
		<title>Data Entry</title>

		<link rel="stylesheet" type="text/css" href="assets/css/style.css">
		<link rel="stylesheet" type="text/css" href="assets/css/uikit.almost-flat.css" />
		<link rel="stylesheet" type="text/css" href="assets/css/datepicker.almost-flat.css" />
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
		<script src="assets/js/uikit.js"></script>
		<script src="assets/js/datepicker.js"></script>
		<script src="assets/js/print.min.js"></script>
	</head>
	<body>
		<nav>
			<h1>Home</h1>
			<ul>
				<li><a href="portal.php">Portal</a></li>
				<li><a href="list.php">List</a></li>
			</ul>
		</nav>

		<div class="mcontainer">
			<h2>Data Entry</h2>
			<div class="container">
				<a href="portal.php?table=customers" class="button">Customers</a>
				<a href="portal.php?table=orders" class="button">Orders</a>
				<a href="portal.php?table=products" class="button">Products</a>
				<a href="portal.php?table=orderDetails" class="button">OrderDetails</a>
				<a href="portal.php?table=Suppliers" class="button">Suppliers</a>
			</div>
		</div>
		<div class="mcontainer">
			<h2>Lists</h2>
			<div class="container">
				<a href="list.php?table=customers" class="button">List of Customers</a>
				<a href="list.php?table=orders" class="button">List of Orders</a>
				<a href="list.php?table=products" class="button">List of Products</a>
				<a href="list.php?table=orderDetails" class="button">List of OrderDetails</a>
				<a href="list.php?table=Suppliers" class="button">List of Suppliers</a>
			</div>
		</div>
		<div class="mcontainer">
			<h2>Reports</h2>
			<div class="container">
				<a href="list.php?table=customers&sort=1" class="button">Customers By LastName</a>
				<a href="list.php?table=orders&sort=4" class="button">Orders By LastName</a>
				<a href="list.php?table=products&sort=2" class="button">Products In Stock By Date</a>
				<a href="list.php?table=orders&sort=3" class="button">Order Profit per Month</a>
				<a href="list.php?table=products&sort=5" class="button">Most popular product per Month</a>
			</div>
		</div>
	</body>
</html>
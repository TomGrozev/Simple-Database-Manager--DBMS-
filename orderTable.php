<?php
/**!
 * @fileOverview Get the order's table
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
 
	if(isset($_POST['orderID'])) {
	    $orderID = $_POST['orderID'];

	    $servername = "localhost";
		$username = "root";
		$password = "root";
		$dbname = "afl";

		$conn = mysqli_connect($servername, $username, $password, $dbname);

		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		}

		$orderData=mysqli_query($conn,"SELECT * from orderdetails WHERE orderID='$orderID'");
		while ($row = $orderData->fetch_assoc()) {
			$productID=$row['productID'];
			$product=mysqli_query($conn,"SELECT name from products WHERE productID='$productID'");
			$row1 = $product->fetch_assoc();

			printf('<tr name="%s"><th><select id="products"><option value="hide">%s</option>', $row['id'], $row1['name']);
			$products=mysqli_query($conn,"SELECT * from products");
			while ($row2 = $products->fetch_assoc()) {
				printf('<option name="%s" value="%s">%s</option>', $row2['productID'], $row2['name'], $row2['name']);
			}
			printf('</select></th><th><input class="portalInput" type="text" name="qty" placeholder="1" value="%s" /></th><th><input class="portalInput" type="text" name="subTotal" placeholder="10" value="%s" /></th></tr>', $row['qty'], $row['subTotal']);
		}

		$conn->close();
	}
?>
<?php
/**!
 * @fileOverview Create Record
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
 
	if(isset($_POST['table']) && isset($_POST['pkey'])) {
	    $table = $_POST['table'];
	    $pkey = $_POST['pkey'];

	    $servername = "localhost";
		$username = "root";
		$password = "root";
		$dbname = "afl";

		$conn = mysqli_connect($servername, $username, $password, $dbname);

		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		} 

		$sql = "INSERT INTO $table ($pkey,productID) VALUES (NULL,'1')";

		if ($conn->query($sql) === TRUE) {
			$result = mysqli_query($conn,"SELECT * FROM $table ORDER BY $pkey DESC LIMIT 1");
		    $row = $result->fetch_assoc();

			if(isset($_POST['orderID'])) {
				$orderID = $_POST['orderID'];

				$sql2 = "UPDATE orderdetails SET orderID='$orderID' WHERE $pkey='$row[$pkey]'";
				if ($conn->query($sql2) === TRUE) {
				    echo $row[$pkey];
				} else {
				    echo "Error";
				}
			}
		} else {
		    echo "Error";
		}

		$conn->close();
	}
?>
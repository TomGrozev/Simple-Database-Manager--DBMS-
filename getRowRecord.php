<?php
/**!
 * @fileOverview Get Record Row
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
	if(isset($_POST['table']) && isset($_POST['id']) && isset($_POST['pkey'])) {
	    $table = $_POST['table'];
	    $id = $_POST['id'];
	    $pkey = $_POST['pkey'];

	    $servername = "localhost";
		$username = "root";
		$password = "root";
		$dbname = "afl";

		$conn = mysqli_connect($servername, $username, $password, $dbname);

		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		} 

		if ($result=mysqli_query($conn,"SELECT * from $table")) {
			$i = 0;
			$record = 0;
			while ($row = mysqli_fetch_assoc($result)) {
				$i += 1;
				if ($row[$pkey] == $id) {
					$record = $i;
				}
			}
			echo $record;
		}

		$conn->close();
	}
?>
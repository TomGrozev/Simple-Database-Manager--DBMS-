<!--/**!
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
 */-->

<?php
	
	$servername = "localhost";
	$username = "root";
	$password = "root";
	$dbname = "afl";

	$tableName = "";
	if (isset($_GET['table'])) {
		$tableName = $_GET['table'];
	} else {
		$tableName = "customers";
	}

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);

	// Check connection
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	} 

	// trigger_error("Cannot divide by zero", E_USER_ERROR);
	
	$sql="SELECT * FROM $tableName";
	$fieldcount = "";

	if ($result=mysqli_query($conn,$sql))
	{
	  // Return the number of fields in result set
	  $fieldcount=mysqli_num_fields($result);
	  // Free result set
	  mysqli_free_result($result);
	}

	$record = "0";

	$recordID = "";

	$primaryKey=mysqli_query($conn,"SHOW INDEX FROM $tableName WHERE Key_name = 'PRIMARY'");
	$pkeys = $primaryKey->fetch_assoc();
	$pkey = $pkeys['Column_name'];

	$data=mysqli_query($conn,"SELECT * from $tableName");
	$num_rows = mysqli_num_rows($data);

	$title = "";
	$sortKeys = [];
	$sumMonths = [];
	if (isset($_GET['sort'])) {
		if ($_GET['sort'] == "1") {
			$title = "List of Customers sorted by LastName";
			if ($r1=mysqli_query($conn,"SELECT * from customers ORDER BY LastName, addressLine1")) {
				$lastName = "";
				$tempArray = [];
				while ($row = $r1->fetch_assoc()) {
					if ($lastName != $row['LastName']) {
						if ($lastName == "") {
							$lastName = $row['LastName'];
							array_push($tempArray, $row['id']);
						}
						$sortKeys[$lastName] = $tempArray;
						$tempArray = [];
					}
					array_push($tempArray, $row['id']);
					$lastName = $row['LastName'];
				}
				$sortKeys[$lastName] = $tempArray;
			}
			$num_rows = count($sortKeys);
		} elseif ($_GET['sort'] == "2") {
			$productsInStock = [];
			$title = "List of products in stock sorted by date and team";
			if ($r1=mysqli_query($conn,"SELECT * from products WHERE qtyInStock > 0")) {
				while ($row = $r1->fetch_assoc()) {
					array_push($productsInStock, $row['productID']);
				}
				$ids = join("','",$productsInStock);
				if ($r2=mysqli_query($conn,"SELECT * from products WHERE productID IN ('$ids') ORDER BY purchaseDate, team")) {
					$lastProduct = "";
					$tempArray = [];
					while ($row = $r2->fetch_assoc()) {
						if ($lastProduct != $row['purchaseDate']) {
							if ($lastProduct == "") {
								$lastProduct = $row['purchaseDate'];
								array_push($tempArray, $row['productID']);
							}
							$sortKeys[date("M jS, Y", strtotime($lastProduct))] = $tempArray;
							$tempArray = [];
						}
						array_push($tempArray, $row['productID']);
						$lastProduct = $row['purchaseDate'];
					}
					$sortKeys[date("M jS, Y", strtotime($lastProduct))] = $tempArray;
				}
			}
			$num_rows = count($sortKeys);
		} elseif ($_GET['sort'] == "3") {
			$title = "List profit per month";
			if ($r1=mysqli_query($conn,"SELECT orderID,profit,MONTH(date) AS OrderMonth from $tableName WHERE YEAR(date)=YEAR(NOW()) AND profit>0 ORDER BY MONTH(date), profit DESC")) {
				$lastMonth = "00";
				$tempArray = [];
				$tempProfit = 0;
				while ($row = $r1->fetch_assoc()) {
					if ($lastMonth != $row['OrderMonth']) {
						if ($lastMonth == "00") {
							$lastMonth = $row['OrderMonth'];
							$tempProfit += (int)$row['profit'];
							array_push($tempArray, $row['orderID']);
						}
						$textMonth = wordMonth($lastMonth);
						$sortKeys[$textMonth] = $tempArray;
						$sumMonths[$textMonth] = $tempProfit;
						$tempArray = [];
						$tempProfit = 0;
					}
					array_push($tempArray, $row['orderID']);
					$lastMonth = $row['OrderMonth'];
					$tempProfit += (int)$row['profit'];
				}
				$textMonth = wordMonth($lastMonth);
				$sortKeys[$textMonth] = $tempArray;
				$sumMonths[$textMonth] = $tempProfit;
			}
			$num_rows = count($sortKeys);
		} elseif ($_GET['sort'] == "4") {
			$orderKeys = [];
			$title = "List of orders sorted by Customer LastName";
			if ($r3=mysqli_query($conn,"SELECT customerID from orders")) {
				while ($row = $r3->fetch_assoc()) {
					array_push($orderKeys, $row['customerID']);
				}
				$ids = join("','",$orderKeys);
				$customerKeys = [];
				$customerlName = [];
				if ($r2=mysqli_query($conn,"SELECT * from customers WHERE id IN ('$ids') ORDER BY LastName")) {
					while ($row = $r2->fetch_assoc()) {
						array_push($customerKeys, $row['id']);
						$customerlName[$row['id']] = $row['LastName'];
					}
					$ids = join("','",$customerKeys);
					$lastName = "";
					$tempArray = [];
					for ($i=0; $i < count($customerKeys); $i++) { 
						if ($r4=mysqli_query($conn,"SELECT * from orders WHERE customerID='$customerKeys[$i]'")) {
							while ($row = $r4->fetch_assoc()) {
								if ($lastName == "") {
									$lastName = $customerlName[$row['customerID']];
									array_push($tempArray, $row['orderID']);
								} else if ($lastName != $customerlName[$row['customerID']]) {
									$sortKeys[$lastName] = $tempArray;
									$tempArray = [];
								}
								array_push($tempArray, $row['orderID']);
								$lastName = $customerlName[$row['customerID']];
							}
							$sortKeys[$lastName] = $tempArray;
						}
					}
				}
			}
			$num_rows = count($sortKeys);
		} elseif ($_GET['sort'] == "5") {
			$title = "List of Most Popular products by Month";
			if ($r3=mysqli_query($conn,"SELECT * from orderdetails WHERE orderID != 0 AND productID != 0 ORDER BY orderID")) {
				$orders = [];
				$order = [];
				$lastID = "";
				$tempArray = [];
				while ($row = $r3->fetch_assoc()) {
					$f = 0;
					array_push($order, $row['orderID']);
					if ($lastID != $row['orderID']) {
						if ($lastID == "") {
							$lastID = $row['orderID'];
							array_push($tempArray, $row['productID']);
							$f = 1;
						} else {
							$orders[$lastID] = $tempArray;
							$tempArray = [];
						}
					}
					if ($f == 0) {
						array_push($tempArray, $row['productID']);
					} else {
						$f = 0;
					}
					$lastID = $row['orderID'];
				}
				$orders[$lastID] = $tempArray;
				$ids = join("','",$order);
				if ($r4=mysqli_query($conn,"SELECT orderID,MONTH(date) AS OrderMonth from orders WHERE orderID IN ('$ids') ORDER BY MONTH(date)")) {
					$date = [];
					$lastMonth = "00";
					$productMonth = [];
					while ($row = $r4->fetch_assoc()) {
						if ($lastMonth != $row['OrderMonth']) {
							if ($lastMonth == "00") {
								$lastMonth = $row['OrderMonth'];
								array_push($tempArray, $orders[$row['orderID']]);
							}
							$textMonth = wordMonth($lastMonth);
							$productMonth[$textMonth] = $tempArray;
							$tempArray = [];
						}
						array_push($tempArray, $orders[$row['orderID']]);
						$lastMonth = $row['OrderMonth'];
					}
					$textMonth = wordMonth($lastMonth);
					$productMonth[$textMonth] = $tempArray;

					foreach ($productMonth as $keys) {
						$tempArray = [];
						foreach ($keys as $keyss) {
							foreach ($keyss as $key => $value) {
								array_push($tempArray, $value);
							}
						}
						$productMonth[array_search($keys, $productMonth)] = $tempArray;
					}

					if ($r5=mysqli_query($conn,"SELECT productID from products")) {
				    	$num_rows = mysqli_num_rows($r5);
				    	while ($row = $r5->fetch_assoc()) {
				    		$newArray[] = $row['productID'];
				    	}
				    }
					foreach ($productMonth as $key => $value) {
						$acv=array_count_values($value);
					    arsort($acv);
					    $val=array_keys($acv);
					    foreach ($val as $keyy => $value) {
					    	$exists = in_array($value, $newArray);
					    	// echo $value.':'.$exists.':,';
					    }
					    if ($exists) {
					    	$NEWproductMonth[$key] = $val;
					    }
					}
					$sortKeys = $NEWproductMonth;

				}
			}
		} else {
			$title = "List of ".$tableName;
			if ($r1=mysqli_query($conn,"SELECT * from $tableName")) {
				while ($row = $r1->fetch_assoc()) {
					array_push($sortKeys, $row[$pkey]);
				}
			}
		}
	} else {
		$title = "List of ".$tableName;
		if ($r1=mysqli_query($conn,"SELECT * from $tableName")) {
			while ($row = $r1->fetch_assoc()) {
				array_push($sortKeys, $row[$pkey]);
			}
		}
	}

	function wordMonth($month) {
		switch ($month) {
			case '01':
				return "January";
				break;
			case '02':
				return "February";
				break;
			case '03':
				return "March";
				break;
			case '04':
				return "April";
				break;
			case '05':
				return "May";
				break;
			case '06':
				return "June";
				break;
			case '07':
				return "July";
				break;
			case '08':
				return "August";
				break;
			case '09':
				return "September";
				break;
			case '10':
				return "October";
				break;
			case '11':
				return "November";
				break;
			case '12':
				return "December";
				break;
			
			default:
				return "";
				break;
		}
	}

	$firstrowQ=mysqli_query($conn,"SELECT $pkey from $tableName ORDER BY $pkey ASC LIMIT 1");
	$lastrowQ=mysqli_query($conn,"SELECT $pkey from $tableName ORDER BY $pkey DESC LIMIT 1");
	$firstrow;
	$lastrow;

	while ($row = mysqli_fetch_assoc($firstrowQ)) {
		$firstrow = $row[$pkey];
	}
	while ($row = mysqli_fetch_assoc($lastrowQ)) {
		$lastrow = $row[$pkey];
	}

	// mysqli_close($conn);
?>

<html>
	<head>
		<title>List</title>

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
			<?php
				if (isset($_GET['sort']) && $_GET['sort'] == "3") {
					echo '<button class="graphBtn"><img src="assets/images/graph.png"></button>';
				}
				echo '<h1>'.$title.'</h1>';
			?>
			<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="portal.php">Portal</a></li>
			</ul>
		</nav>

		<?php
			if (isset($_GET['sort']) && $_GET['sort'] == "3") {
				$yearProfit = 0;
				foreach ($sumMonths as $key => $value) {
					$yearProfit += (int)$value;
				}
				echo '<div class="separator" style="width: 96%;"><h3>Year Profit: '.$yearProfit.'</h3><h2 class="extra">'.date("Y").'</h2></div>';
			}
			foreach ($sortKeys as $keys => $values) {
				if (isset($_GET['sort'])) {
					if ($_GET['sort'] == "3") {
						echo '<div class="separator"><h3>Month Profit: '.$sumMonths[$keys].'</h3><h2 class="extra">'.$keys.'</h2></div>';
					} elseif (isset($_GET['sort']) && $_GET['sort'] == "5") {
						if ($result2=mysqli_query($conn,"SELECT name FROM products WHERE productID='$values[0]'"))
						{
							$row = $result2->fetch_assoc();
							echo '<div class="separator"><h3>Most Popular: '.$row['name'].'</h3><h2 class="extra">'.$keys.'</h2></div>';
						}
					} else {
						echo '<div class="separator"><h2>'.$keys.'</h2></div>';
					}
					foreach ($values as $key => $value) {
						echo '<div class="listBox">';
						if ($result=mysqli_query($conn,"SELECT * from $tableName"))
						{
							if ($result2=mysqli_query($conn,"SELECT * FROM $tableName WHERE $pkey='$value'"))
							{
								$row = $result2->fetch_assoc();
								$recordID = $row[$pkey];

							    while($finfo = mysqli_fetch_field($result)) {
								    $fieldType = "";
								    switch ($finfo->type) {
								    	case 3:
								    		$fieldType = "text";
								    		break;

								    	case 253:
								    		$fieldType = "text";
								    		break;

								    	case 252:
								    		$fieldType = "text";
								    		break;

								    	case 254:
								    		$fieldType = "text";
								    		break;

								    	case 10:
								    		$fieldType = "date";
								    		break;
								    	
								    	default:
								    		$fieldType = "text";
								    		break;
								    }
								    $fieldName = preg_replace('/([a-z])([A-Z])/s','$1 $2', $finfo->name);

									printf('<div class="listField" id="%s" style="width: %d;">', $finfo->name, (strlen($row[$finfo->name]) * 5) + 150);
								    if ($fieldType == "date") {
								    	printf('<input class="clean-slide click" type="%s" name="%s" data-uk-datepicker="%s" placeholder="Date of Birth" value="%s" style="width: %d;" /><label>%s</label><div class="circle-loader"><div class="checkmark draw"></div></div>', $fieldType, $finfo->name, "{weekstart:0, format:'YYYY-MM-DD'}", $row[$finfo->name], (strlen($row[$finfo->name]) * 5) + 150, $fieldName);
								    } else {
								    	printf('<input class="clean-slide click" type="%s" name="%s" placeholder="Go for the high score!" value="%s" style="width: %d;" /><label>%s</label><div class="circle-loader"><div class="checkmark draw"></div></div>', $fieldType, $finfo->name, $row[$finfo->name], (strlen($row[$finfo->name]) * 5) + 150, $fieldName);
								    }
								    echo "</div>";
								    if (isset($_GET['sort'])) {
										if ($_GET['sort'] == "4") {
											if ($finfo->name == "customerID") {
												$custID = $row['customerID'];
												if ($result3=mysqli_query($conn,"SELECT LastName FROM customers WHERE id='$custID'"))
												{
													$row1 = $result3->fetch_assoc();
													printf('<div class="listField" id="LastName" style="width: %d;">', (strlen($row1['LastName']) * 5) + 150);
													printf('<input class="clean-slide" type="text" name="LastName" placeholder="LastName" value="%s" style="width: %d;" /><label>Last Name</label><div class="circle-loader"><div class="checkmark draw"></div></div>', $row1['LastName'], (strlen($row1['LastName']) * 5) + 150);
													echo "</div>";
												}
											}
										}
									}
								}
							}
						}
						echo '</div>';
					}
				} else {
					echo '<div class="listBox">';
					if ($result=mysqli_query($conn,"SELECT * from $tableName"))
					{
						if ($result2=mysqli_query($conn,"SELECT * FROM $tableName WHERE $pkey='$values'"))
						{
							$row = $result2->fetch_assoc();
							$recordID = $row[$pkey];

						    while($finfo = mysqli_fetch_field($result)) {
							    $fieldType = "";
							    switch ($finfo->type) {
							    	case 3:
							    		$fieldType = "text";
							    		break;

							    	case 253:
							    		$fieldType = "text";
							    		break;

							    	case 252:
							    		$fieldType = "text";
							    		break;

							    	case 254:
							    		$fieldType = "text";
							    		break;

							    	case 10:
							    		$fieldType = "date";
							    		break;
							    	
							    	default:
							    		$fieldType = "text";
							    		break;
							    }
							    $fieldName = preg_replace('/([a-z])([A-Z])/s','$1 $2', $finfo->name);

								printf('<div class="listField" id="%s" style="width: %d;">', $finfo->name, (strlen($row[$finfo->name]) * 5) + 150);
							    if ($fieldType == "date") {
							    	printf('<input class="clean-slide click" type="%s" name="%s" data-uk-datepicker="%s" placeholder="Date of Birth" value="%s" /><label>%s</label><div class="circle-loader"><div class="checkmark draw"></div></div>', $fieldType, $finfo->name, "{weekstart:0, format:'YYYY-MM-DD'}", $row[$finfo->name], $fieldName);
							    } else {
							    	printf('<input class="clean-slide click" type="%s" name="%s" placeholder="Go for the high score!" value="%s" style="width: %d;" /><label>%s</label><div class="circle-loader"><div class="checkmark draw"></div></div>', $fieldType, $finfo->name, $row[$finfo->name], (strlen($row[$finfo->name]) * 5) + 150, $fieldName);
							    }
							    echo "</div>";
							    if (isset($_GET['sort'])) {
									if ($_GET['sort'] == "4") {
										if ($finfo->name == "customerID") {
											$custID = $row['customerID'];
											if ($result3=mysqli_query($conn,"SELECT LastName FROM customers WHERE id='$custID'"))
											{
												printf('<div class="listField" id="LastName" style="width: %d;">', (strlen($row[$finfo->name]) * 5) + 150);
												$row1 = $result3->fetch_assoc();
												printf('<input class="clean-slide" type="text" name="LastName" placeholder="LastName" value="%s" style="width: %d;" /><label>Last Name</label><div class="circle-loader"><div class="checkmark draw"></div></div>', $row1['LastName'], (strlen($row1['LastName']) * 5) + 150);
												echo "</div>";
											}
										}
									} else if ($_GET['sort'] == "5") {
										if ($finfo->name == "productID") {
											$prodID = $row['productID'];
											if ($result3=mysqli_query($conn,"SELECT LastName FROM customers WHERE id='$custID'"))
											{
												printf('<div class="listField" id="LastName" style="width: %d;">', (strlen($row[$finfo->name]) * 5) + 150);
												$row1 = $result3->fetch_assoc();
												printf('<input class="clean-slide" type="text" name="LastName" placeholder="LastName" value="%s" style="width: %d;" /><label>Last Name</label><div class="circle-loader"><div class="checkmark draw"></div></div>', $row1['LastName'], (strlen($row1['LastName']) * 5) + 150);
												echo "</div>";
											}
										}
									}
								}
							}
						}
					}
					echo '</div>';
				}
			}
		?>
		<div id="overlay-back"></div>
		<div class="graph">
		  <button onclick="reset();" class="reload"><img src="assets/images/reload.png"></button>
		  <button class="close"><img src="assets/images/close.png"></button>
		  <h2 class="title">Profit Per Month</h2>
		</div>

		<script type="text/javascript">

			var ajaxManager = (function() {
		     var requests = [];

		     return {
		        addReq:  function(opt) {
		            requests.push(opt);
		        },
		        removeReq:  function(opt) {
		            if( $.inArray(opt, requests) > -1 )
		                requests.splice($.inArray(opt, requests), 1);
		        },
		        run: function() {
		            var self = this,
		                oriSuc;

		            if( requests.length ) {
		                oriSuc = requests[0].complete;

		                requests[0].complete = function() {
		                     if( typeof(oriSuc) === 'function' ) oriSuc();
		                     requests.shift();
		                     self.run.apply(self, []);
		                };   

		                $.ajax(requests[0]);
		            } else {
		              self.tid = setTimeout(function() {
		                 self.run.apply(self, []);
		              }, 1000);
		            }
		        },
		        stop:  function() {
		            requests = [];
		            clearTimeout(this.tid);
		        }
		     };
		}());

			ajaxManager.run();
			
			$('.click').focusout(function() {
				$(this).siblings('.circle-loader').addClass('visible');
				var variable = $(this);
				ajaxManager.addReq({
					url: 'updateTable.php',
				    data: {table: '<?php echo $tableName; ?>', id: $(this).parent().siblings('#<?php echo $pkey; ?>').children('input').val(), field: $(this).attr('name'), value: $(this).val(), pkey: '<?php echo $pkey; ?>'},
				    type: 'post',
				    success: function(output) {
				        variable.siblings('.circle-loader').addClass('load-complete');
  						variable.siblings('.circle-loader').find('.checkmark').toggle();
  						setTimeout(function () {
  							variable.siblings('.circle-loader').removeClass('visible', 500);
  							variable.siblings('.circle-loader').removeClass('load-complete');
  							variable.siblings('.circle-loader').find('.checkmark').toggle();
  						}, 1000);
				    },
				    failed: function() {
				    	variable.siblings('.circle-loader').removeClass('visible', 500);
				    }
				});
			});

			function getRow() {
				ajaxManager.addReq({
					url: 'getRowRecord.php',
				    data: {table: '<?php echo $tableName; ?>', id: $('input[name="<?php echo $pkey; ?>"]').val(), pkey: '<?php echo $pkey; ?>'},
				    dataType: 'json',
				    type: 'post',
				    success: function(output) {
				        $('.record').html(output + '/<?php echo $num_rows; ?>');
				    }
				});
			}
			var LineChart = function( options ) {

			var data = options.data;
			var canvas = document.getElementsByClassName("graph")[0].appendChild( document.createElement( 'canvas' ) );
			var context = canvas.getContext( '2d' );

			var rendering = false,
			  paddingX = 40,
			  paddingY = 40,
			  width = options.width || window.innerWidth,
			  height = options.height || window.innerHeight,
			  progress = 0;

			canvas.width = width;
			canvas.height = height;

			var maxValue,
			  minValue;

			var y1 = paddingY + ( 0.05 * ( height - ( paddingY * 2 ) ) ),
			  y2 = paddingY + ( 0.50 * ( height - ( paddingY * 2 ) ) ),
			  y3 = paddingY + ( 0.95 * ( height - ( paddingY * 2 ) ) );

			format();
			render();

			function format( force ) {

			maxValue = 0;
			minValue = Number.MAX_VALUE;

			data.forEach( function( point, i ) {
			  maxValue = Math.max( maxValue, point.value );
			  minValue = Math.min( minValue, point.value );
			} );

			data.forEach( function( point, i ) {
			  point.targetX = paddingX + ( i / ( data.length - 1 ) ) * ( width - ( paddingX * 2 ) );
			  point.targetY = paddingY + ( ( point.value - minValue ) / ( maxValue - minValue ) * ( height - ( paddingY * 2 ) ) );
			  point.targetY = height - point.targetY;

			  if( force || ( !point.x && !point.y ) ) {
			    point.x = point.targetX + 30;
			    point.y = point.targetY;
			    point.speed = 0.04 + ( 1 - ( i / data.length ) ) * 0.05;
			  }
			} );

			}

			function render() {

			if( !rendering ) {
			  requestAnimationFrame( render );
			  return;
			}

			context.font = '10px sans-serif';
			context.clearRect( 0, 0, width, height );

			context.fillStyle = '#222';
			context.fillRect( paddingX, y1, width - ( paddingX * 2 ), 1 );
			context.fillRect( paddingX, y2, width - ( paddingX * 2 ), 1 );
			context.fillRect( paddingX, y3, width - ( paddingX * 2 ), 1 );

			if( options.yAxisLabel ) {
			  context.save();
			  context.globalAlpha = progress;
			  context.translate( paddingX - 15, height - paddingY - 10 );
			  context.rotate( -Math.PI / 2 );
			  context.fillStyle = '#fff';
			  context.fillText( options.yAxisLabel, 0, 0 );
			  context.restore();
			}

			var progressDots = Math.floor( progress * data.length );
			var progressFragment = ( progress * data.length ) - Math.floor( progress * data.length );

			data.forEach( function( point, i ) {
			  if( i <= progressDots ) {
			    point.x += ( point.targetX - point.x ) * point.speed;
			    point.y += ( point.targetY - point.y ) * point.speed;

			    context.save();
			    
			    var wordWidth = context.measureText( point.label ).width;
			    context.globalAlpha = i === progressDots ? progressFragment : 1;
			    context.fillStyle = point.future ? '#aaa' : '#999';
			    context.fillText( point.label, point.x - ( wordWidth / 2 ), height - 18 );

			    if( i < progressDots && !point.future ) {
			      context.beginPath();
			      context.arc( point.x, point.y, 4, 0, Math.PI * 2 );
			      context.fillStyle = '#1baee1';
			      context.fill();
			    }

			    context.restore();
			  }

			} );

			context.save();
			context.beginPath();
			context.strokeStyle = '#1baee1';
			context.lineWidth = 2;

			var futureStarted = false;

			data.forEach( function( point, i ) {

			  if( i <= progressDots ) {

			    var px = i === 0 ? data[0].x : data[i-1].x,
			        py = i === 0 ? data[0].y : data[i-1].y;

			    var x = point.x,
			        y = point.y;

			    if( i === progressDots ) {
			      x = px + ( ( x - px ) * progressFragment );
			      y = py + ( ( y - py ) * progressFragment );
			    }

			    if( point.future && !futureStarted ) {
			      futureStarted = true;

			      context.stroke();
			      context.beginPath();
			      context.moveTo( px, py );
			      context.strokeStyle = '#aaa';

			      if( typeof context.setLineDash === 'function' ) {
			        context.setLineDash( [2,3] );
			      }
			    }

			    if( i === 0 ) {
			      context.moveTo( x, y );
			    }
			    else {
			      context.lineTo( x, y );
			    }

			  }

			} );

			context.stroke();
			context.restore();

			progress += ( 1 - progress ) * 0.02;

			requestAnimationFrame( render );

			}

			this.start = function() {
			rendering = true;
			}

			this.stop = function() {
			rendering = false;
			progress = 0;
			format( true );
			}

			this.restart = function() {
			this.stop();
			this.start();
			}

			this.append = function( points ) {    
			progress -= points.length / data.length;
			data = data.concat( points );

			format();
			}

			this.populate = function( points ) {    
			progress = 0;
			data = points;

			format();
			}

			};

			var chart = new LineChart({ data: [] });

			reset();

			chart.start();

			function append() {
			chart.append([
			{ label: 'Rnd', value: 1300 + ( Math.random() * 1500 ), future: true }
			]);
			}

			function restart() {
				chart.restart();
			}

			$('.graphBtn').on('click', function () {
				restart();
			    $('.graph, #overlay-back').fadeIn(500);
			});

			$('.close').on('click', function () {
			    $('.graph, #overlay-back').fadeOut(500);
			});

			function reset() {
				chart.populate([
				<?php
					$total = 0;
					for ($i=1; $i < 13; $i++) {
						$temp = "";
						if ($i < 10) {
							$temp = "0".(string)$i;
						} else {
							$temp = (string)$i;
						}
						$found = 0;
						foreach ($sortKeys as $key => $value) {
							if (wordMonth($temp) == $key) {
								$found = 1;
								printf("{ label: '%s', value: %d },", wordMonth($temp), $sumMonths[$key]);
								$total += $sumMonths[$key];
							}
						}
						if ($found == 0) {
							printf("{ label: '%s', value: 0 },", wordMonth($temp));
						} else {
							$found = 0;
						}
					}
					$total = $total/12;
					printf("{ label: 'January', value: %f, future: true },", $total);
					printf("{ label: 'February', value: %f, future: true }", $total*3);
				?>
				]);
			}
		</script>
	</body>
</html>
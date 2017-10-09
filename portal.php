<!--/**!
 * @fileOverview Portal View of Database elements
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

	$data=mysqli_query($conn,"SELECT * from $tableName");
	$num_rows = mysqli_num_rows($data);

	$record = "";
	if (isset($_GET['record'])) {
		if ($_GET['record'] == "0") {
			$record = "0";
		} elseif ($_GET['record'] > $num_rows) {
			$record = $num_rows-1;
		} else {
			$tempR = (int)$_GET['record'] - 1;
			$record = (string)$tempR;
		}
	} else {
		$record = "0";
	}

	$recordID = "";

	$orderdetails = array();

	$primaryKey=mysqli_query($conn,"SHOW INDEX FROM $tableName WHERE Key_name = 'PRIMARY'");
	$pkeys = $primaryKey->fetch_assoc();
	$pkey = $pkeys['Column_name'];

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
			<h1>Data Entry</h1>
			<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="list.php">List</a></li>
			</ul>
		</nav>

		<div class="navButtons">
			<div class="counter"></div>

			<button class="paginate left"><i></i><i></i></button>
			<button class="paginate right"><i></i><i></i></button>
		</div>
			<?php
				if ($tableName == "orders") {
					echo '<div class="controls" style="margin: -30px 17%;">';
					echo '<a class="object" href="#" id="createBtn"><img src="assets/images/add.png"></a>';
					echo '<a class="object" href="#" id="printBtn"><img src="assets/images/printer.png"></a>';
					echo '<a class="object" href="#" id="deleteBtn"><img src="assets/images/bin.png"></a>';
				} else {
					echo '<div class="controls">';
					echo '<a class="object" href="#" id="createBtn"><img src="assets/images/add.png"></a>';
					echo '<a class="object" href="#" id="deleteBtn"><img src="assets/images/bin.png"></a>';
				}
			?>
		</div>

		<div class="box">
			<div class="dropdown">
			  <button class="dropbtn"><?php echo ucfirst($tableName);?></button>
			  <div class="dropdown-content">
			  <?php
			  	$tablesList=mysqli_query($conn,"SHOW TABLES FROM $dbname");
			  	while ($row = mysqli_fetch_row($tablesList)) {
				    printf('<a href="portal.php?record=0&table=%s">%s</a>', $row[0], $row[0]);
				}
			  ?>
			  </div>
			</div>
			<?php
				if ($result=mysqli_query($conn,"SELECT * from $tableName ORDER BY $pkey"))
				{
					if ($result2=mysqli_query($conn,"SELECT * FROM $tableName ORDER BY $pkey LIMIT $record,1"))
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

							printf('<div class="field" id="%s">', $finfo->name);
							if ($tableName == "orders" && $finfo->name == "customerID") {
								$customerID=$row[$finfo->name];
								$cust=mysqli_query($conn,"SELECT name from customers WHERE id='$customerID'");
								$row1 = $cust->fetch_assoc();

							    printf('<label class="fieldLabel">Customers</label><select id="customers"><option value="hide">%s</option>', $row1['name']);
							    $customers=mysqli_query($conn,"SELECT * from customers");
								while ($row2 = $customers->fetch_assoc()) {
									printf('<option name="%s" value="%s">%s</option>', $row2['id'], $row2['name'], $row2['name']);
								}
								echo "</select>";
							} elseif ($tableName == "orders" && $finfo->name == "totalQty") {
								printf("<h2>Qty - %sx</h2>", $row[$finfo->name]);
							} elseif ($tableName == "orders" && $finfo->name == "totalPrice") {
								printf("<h1>Total - $%s</h1>", $row[$finfo->name]);
							} elseif ($tableName == "orders" && $finfo->name == "profit") {
								printf("<h2>Profit - $%s</h2>", $row[$finfo->name]);
							} elseif ($tableName == "products" && $finfo->name == "supplierID") {
								$supplierID=$row[$finfo->name];
								$suppliers=mysqli_query($conn,"SELECT name from suppliers WHERE id='$supplierID'");
								$row1 = $suppliers->fetch_assoc();

							    printf('<label class="fieldLabel">Suppliers</label><select id="suppliers"><option value="hide">%s</option>', $row1['name']);
							    $supp=mysqli_query($conn,"SELECT * from suppliers");
								while ($row2 = $supp->fetch_assoc()) {
									printf('<option name="%s" value="%s">%s</option>', $row2['id'], $row2['name'], $row2['name']);
								}
								echo "</select>";
							} elseif ($tableName == "products" && $finfo->name == "team") {
								printf('<label class="fieldLabel">Team</label><select id="team"><option value="hide">%s</option><option name="Adelaide Crows" value="Adelaide Crows">Adelaide Crows</option><option name="Brisbane Lions" value="Brisbane Lions">Brisbane Lions</option><option name="Carlton" value="Carlton">Carlton</option><option name="Collingwood" value="Collingwood">Collingwood</option><option name="Essendon Lions" value="Essendon">Essendon</option><option name="Fremantle" value="Fremantle">Fremantle</option><option name="Geelong Cats" value="Geelong Cats">Geelong Cats</option><option name="Gold Coast Suns" value="Gold Coast Suns">Gold Coast Suns</option><option name="Greater Western Sydney" value="Greater Western Sydney">Greater Western Sydney</option><option name="Hawthorn" value="Hawthorn">Hawthorn</option><option name="Melbourne" value="Melbourne">Melbourne</option><option name="North Melbourne" value="North Melbourne">North Melbourne</option><option name="Port Power" value="Port Power">Port Power</option><option name="Richmond" value="Richmond">Richmond</option><option name="St Kilda" value="St Kilda">St Kilda</option><option name="Sydney Swans" value="Sydney Swans">Sydney Swans</option><option name="West Coast Eagles" value="West Coast Eagles">West Coast Eagles</option><option name="Western Bulldogs" value="Western Bulldogs">Western Bulldogs</option></select>', $row['team']);
							} elseif ($tableName == "customers" && $finfo->name == "favTeam") {
								printf('<label class="fieldLabel">Favorite Team</label><select id="favTeam"><option value="hide">%s</option><option name="Adelaide Crows" value="Adelaide Crows">Adelaide Crows</option><option name="Brisbane Lions" value="Brisbane Lions">Brisbane Lions</option><option name="Carlton" value="Carlton">Carlton</option><option name="Collingwood" value="Collingwood">Collingwood</option><option name="Essendon Lions" value="Essendon">Essendon</option><option name="Fremantle" value="Fremantle">Fremantle</option><option name="Geelong Cats" value="Geelong Cats">Geelong Cats</option><option name="Gold Coast Suns" value="Gold Coast Suns">Gold Coast Suns</option><option name="Greater Western Sydney" value="Greater Western Sydney">Greater Western Sydney</option><option name="Hawthorn" value="Hawthorn">Hawthorn</option><option name="Melbourne" value="Melbourne">Melbourne</option><option name="North Melbourne" value="North Melbourne">North Melbourne</option><option name="Port Power" value="Port Power">Port Power</option><option name="Richmond" value="Richmond">Richmond</option><option name="St Kilda" value="St Kilda">St Kilda</option><option name="Sydney Swans" value="Sydney Swans">Sydney Swans</option><option name="West Coast Eagles" value="West Coast Eagles">West Coast Eagles</option><option name="Western Bulldogs" value="Western Bulldogs">Western Bulldogs</option></select>', $row['favTeam']);
							} else {
								if ($fieldType == "date") {
							    	printf('<input class="clean-slide" type="%s" name="%s" data-uk-datepicker="%s" placeholder="Date of Birth" value="%s" /><label>%s</label><div class="circle-loader"><div class="checkmark draw"></div></div>', $fieldType, $finfo->name, "{weekstart:0, format:'YYYY-MM-DD'}", $row[$finfo->name], $fieldName);
							    } else {
							    	printf('<input class="clean-slide" type="%s" name="%s" placeholder="Go for the high score!" value="%s" /><label>%s</label><div class="circle-loader"><div class="checkmark draw"></div></div>', $fieldType, $finfo->name, $row[$finfo->name], $fieldName);
							    }
							}
						    echo "</div>";
						}
						if ($tableName == "orders") {
							echo '<div class="lField"><label class="portalLabel">Name</label><label class="portalLabel">Qty</label><label class="portalLabel">SubTotal</label></div>';
					    	echo '<div class="portal">';
					    	$orderData=mysqli_query($conn,"SELECT * from orderdetails WHERE orderID='$recordID'");
					    	echo '<table style="width:100%" class="productsTable"><col width="300px" /><col width="90px" /><col width="125px" />';
							while ($row = $orderData->fetch_assoc()) {
								$productID=$row['productID'];
								$product=mysqli_query($conn,"SELECT name from products WHERE productID='$productID'");
								$row1 = $product->fetch_assoc();
								// echo $row1['name'];
								printf('<tr name="%s"><th><select id="products"><option value="hide">%s</option>', $row['id'], $row1['name']);
								$products=mysqli_query($conn,"SELECT * from products");
								while ($row2 = $products->fetch_assoc()) {
									printf('<option name="%s" value="%s">%s</option>', $row2['productID'], $row2['name'], $row2['name']);
								}
								printf('</select></th><th><input class="portalInput" type="text" name="qty" placeholder="1" value="%s" /></th><th>%s</th></tr>', $row['qty'], $row['subTotal']);

								$tempArray = array();
								$tempArray['id'] = $row['id'];
								$tempArray['name'] = $row1['name'];
								$tempArray['qty'] = $row['qty'];
								$tempArray['subTotal'] = $row['subTotal'];

								array_push($orderdetails, $tempArray);
							}
							echo '</table></div><a class="addBtn" href="#" id="addBtn"><img src="assets/images/add.png"></a>';
					    }
					}

				    mysqli_free_result($result);

				}
			?>
		</div>

		<script type="text/javascript">

			var locked = 0;

			var pr = document.querySelector( '.paginate.left' );
			var pl = document.querySelector( '.paginate.right' );

			pr.onclick = slide.bind( this, -1 );
			pl.onclick = slide.bind( this, 1 );

			var index = <?php echo $record; ?>, total = <?php echo $num_rows; ?>;

			function slide(offset) {
				if (locked == 0) {
				  index = Math.min( Math.max( index + offset, 0 ), total - 1 );

				  document.querySelector( '.counter' ).innerHTML = ( index + 1 ) + ' / ' + total;

				  pr.setAttribute( 'data-state', index === 0 ? 'disabled' : '' );
				  pl.setAttribute( 'data-state', index === total - 1 ? 'disabled' : '' );
				}
			}

			slide(0);

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

			dropDown();

			function dropDown() {
				$('select').each(function(){
				    var $this = $(this), numberOfOptions = $(this).children('option').length;

				    $(this).unwrap('.select');
				    $(this).siblings('.select-styled').remove();
				    $(this).siblings('.select-options').remove();

				    $this.addClass('select-hidden');
				    if ($(this).attr('id') == "customers" || $(this).attr('id') == "suppliers" || $(this).attr('id') == "team" || $(this).attr('id') == "favTeam") {
				    	$this.wrap('<div class="select fieldSelect"></div>');
				    } else {
				    	$this.wrap('<div class="select"></div>');
				    }
				    $this.after('<div class="select-styled"></div>');

				    var $styledSelect = $this.next('div.select-styled');
				    $styledSelect.text($this.children('option').eq(0).text());
				  
				    var $list = $('<ul />', {
				        'class': 'select-options'
				    }).insertAfter($styledSelect);
				  
				    for (var i = 0; i < numberOfOptions; i++) {
				        $('<li />', {
				            text: $this.children('option').eq(i).text(),
				            name: $this.children('option').eq(i).attr('name'),
				            rel: $this.children('option').eq(i).val()
				        }).appendTo($list);
				    }
				  
				    var $listItems = $list.children('li');
				  
				    $styledSelect.click(function(e) {
				        e.stopPropagation();
				        $('div.select-styled.active').not(this).each(function(){
				            $(this).removeClass('active').next('ul.select-options').hide();
				        });
				        $(this).toggleClass('active').next('ul.select-options').toggle();
				    });
				  
				    $listItems.click(function(e) {
				        e.stopPropagation();
				        $styledSelect.text($(this).text()).removeClass('active');
				        $this.val($(this).attr('rel'));
				        $list.hide();
				        //console.log($this.val());

				        $styledSelect.css('background-color', 'orange');
				        if ($(this).parent().siblings('select').attr('id') == "customers") {
				        	ajaxManager.addReq({
								url: 'updateTable.php',
							    data: {table: 'orders', id: $(this).parent().parent().parent().siblings('#orderID').children('input').val(), field: 'customerID', value: $(this).attr('name'), pkey: 'orderID'},
							    type: 'post',
							    success: function(output) {
							        $styledSelect.css('background-color', 'green');
			  						setTimeout(function () {
			  							$styledSelect.css('background-color', '#c0392b');
			  						}, 2000);
							    },
							    failed: function() {
							    	$("input[name='" + output + "']").siblings('.circle-loader').removeClass('visible', 500);
							    }
							});
				        } else if ($(this).parent().siblings('select').attr('id') == "suppliers") {
				        	ajaxManager.addReq({
								url: 'updateTable.php',
							    data: {table: 'products', id: $(this).parent().parent().parent().siblings('#productID').children('input').val(), field: 'supplierID', value: $(this).attr('name'), pkey: 'productID'},
							    type: 'post',
							    success: function(output) {
							        $styledSelect.css('background-color', 'green');
			  						setTimeout(function () {
			  							$styledSelect.css('background-color', '#c0392b');
			  						}, 2000);
							    },
							    failed: function() {
							    	$("input[name='" + output + "']").siblings('.circle-loader').removeClass('visible', 500);
							    }
							});
				        } else if ($(this).parent().siblings('select').attr('id') == "team") {
				        	ajaxManager.addReq({
								url: 'updateTable.php',
							    data: {table: 'products', id: $(this).parent().parent().parent().siblings('#productID').children('input').val(), field: 'team', value: $(this).attr('name'), pkey: 'productID'},
							    type: 'post',
							    success: function(output) {
							        $styledSelect.css('background-color', 'green');
			  						setTimeout(function () {
			  							$styledSelect.css('background-color', '#c0392b');
			  						}, 2000);
							    },
							    failed: function() {
							    	$("input[name='" + output + "']").siblings('.circle-loader').removeClass('visible', 500);
							    }
							});
				        } else if ($(this).parent().siblings('select').attr('id') == "favTeam") {
				        	ajaxManager.addReq({
								url: 'updateTable.php',
							    data: {table: 'customers', id: $(this).parent().parent().parent().siblings('#id').children('input').val(), field: 'favTeam', value: $(this).attr('name'), pkey: 'id'},
							    type: 'post',
							    success: function(output) {
							        $styledSelect.css('background-color', 'green');
			  						setTimeout(function () {
			  							$styledSelect.css('background-color', '#c0392b');
			  						}, 2000);
							    },
							    failed: function() {
							    	$("input[name='" + output + "']").siblings('.circle-loader').removeClass('visible', 500);
							    }
							});
				        } else {
				        	ajaxManager.addReq({
								url: 'updateTable.php',
							    data: {table: 'orderdetails', id: $(this).parent().parent().parent().parent().attr('name'), field: 'productID', value: $(this).attr('name'), pkey: 'id'},
							    type: 'post',
							    success: function(output) {
							        $styledSelect.css('background-color', 'green');
			  						setTimeout(function () {
			  							$styledSelect.css('background-color', '#c0392b');
			  						}, 2000);
							    },
							    failed: function() {
							    	$("input[name='" + output + "']").siblings('.circle-loader').removeClass('visible', 500);
							    }
							});
				        }
				    });
				  
				    $(document).click(function() {
				        $styledSelect.removeClass('active');
				        $list.hide();
				    });

				});
			}

			$('.paginate.right').click(function() {
				if (locked == 0) {
					locked = 1;

					ajaxManager.addReq({
						url: 'getRecord.php',
					    data: {table: '<?php echo $tableName; ?>', id: $('input[name="<?php echo $pkey; ?>"]').val(), pkey: '<?php echo $pkey; ?>', direction: 'next'},
					    dataType: 'json',
					    type: 'post',
					    success: function(output) {
					        if (output.length > 0) {
					        	var keys = [];
					        	for(i=0; i<output.length; i++) {
					        		Object.keys(output[i]).forEach(function(key){
								        if(keys.indexOf(key) == -1)
								        {
								            keys.push(key);
								            // alert(output[i][key]);
								            $("input[name='" + key + "']").val(output[i][key]);
								            if ($('.portal').length) {
								            	switch(key) {
												    case "totalQty":
												    	$("#" + key).children('h2').text('Qty - ' + output[i][key] + 'x');
												        break;
												    case "totalPrice":
												        $("#" + key).children('h1').text('Total - $' + output[i][key]);
												        break;
												    case "profit":
												        $("#" + key).children('h2').text('Profit - $' + output[i][key]);
												        break;
												    default:
												        
												}
								            }
								        }
								    });
					        	}
					        	if ($('.portal').length) {
					        		$('table tbody').empty();
									ajaxManager.addReq({
										url: 'orderTable.php',
									    data: {orderID: $('#orderID').children('input').val()},
									    type: 'post',
									    success: function(output) {
									        $('table tbody').append(output);
									        dropDown();
									    }
									});
					        	}
					        	locked = 0;
					        }
					        // getRow();
					    }
					});
				}
			});
			$('.paginate.left').click(function() {
				if (locked == 0) {
					locked = 1;
				
					ajaxManager.addReq({
						url: 'getRecord.php',
					    data: {table: '<?php echo $tableName; ?>', id: $('input[name="<?php echo $pkey; ?>"]').val(), pkey: '<?php echo $pkey; ?>', direction: 'previous'},
					    dataType: 'json',
					    type: 'post',
					    success: function(output) {
					        if (output.length > 0) {
					        	var keys = [];
					        	for(i=0; i<output.length; i++) {
					        		Object.keys(output[i]).forEach(function(key){
								        if(keys.indexOf(key) == -1)
								        {
								            keys.push(key);
								            $("input[name='" + key + "']").val(output[i][key]);
								            if ($('.portal').length) {
								            	switch(key) {
												    case "totalQty":
												    	$("#" + key).children('h2').text('Qty - ' + output[i][key] + 'x');
												        break;
												    case "totalPrice":
												        $("#" + key).children('h1').text('Total - $' + output[i][key]);
												        break;
												    case "profit":
												        $("#" + key).children('h2').text('Profit - $' + output[i][key]);
												        break;
												    default:
												        
												}
								            }
								        }
								    });
					        	}
					        	if ($('.portal').length) {
					        		$('table tbody').empty();
									ajaxManager.addReq({
										url: 'orderTable.php',
									    data: {orderID: $('#orderID').children('input').val()},
									    type: 'post',
									    success: function(output) {
									        $('table tbody').append(output);
									        dropDown();
									    }
									});	
					        	}
					        	locked = 0;
					        }
					        // getRow();
					    }
					});
				}
			});
			$('#addBtn').click(function() {
				ajaxManager.addReq({
					url: 'createRecord.php',
				    data: {table: 'orderdetails', pkey: 'id', orderID: $('#orderID').children('input').val()},
				    type: 'post',
				    success: function(output) {
				        if (output != 'Error') {
				        	var temp = "";
				        	var product = "";
				        	<?php
				        		$products=mysqli_query($conn,"SELECT * from products");
								while ($row2 = $products->fetch_assoc()) {
									if ($row2['productID'] == '1') {
										printf("product = '%s';", $row2['name']);
									}
									printf('temp += "');
									printf("<option name='%s' value='%s'>%s</option>", $row2['productID'], $row2['name'], $row2['name']);
									printf('";');
								}
				        	?>
				        	var markup = '<tr name="' + output + '"><th><select id="products"><option value="hide">' + product + '</option>' + temp + '</th><th><input class="portalInput" type="text" name="qty" placeholder="1" value="0" /></th><th><input class="portalInput" type="text" name="subTotal" placeholder="10" value="0" /></th></tr>';
				        	$('.productsTable tbody').append(markup);
				        	dropDown();
				        }
				    }
				});
			});

			ajaxManager.run();
			
			$('.clean-slide').focusout(function() {
				$(this).siblings('.circle-loader').addClass('visible');
				var variable = $(this);
				$(this).delay(1000).queue(function() {

					ajaxManager.addReq({
						url: 'updateTable.php',
					    data: {table: '<?php echo $tableName; ?>', id: $('input[name="<?php echo $pkey; ?>"]').val(), field: variable.attr('name'), value: variable.val(), pkey: '<?php echo $pkey; ?>'},
					    type: 'post',
					    success: function(output) {
					        $("input[name='" + output + "']").siblings('.circle-loader').addClass('load-complete');
	  						$("input[name='" + output + "']").siblings('.circle-loader').find('.checkmark').toggle();

	  						ajaxManager.addReq({
									url: 'getRecord.php',
								    data: {table: '<?php echo $tableName; ?>', id: $('input[name="<?php echo $pkey; ?>"]').val(), pkey: '<?php echo $pkey; ?>', direction: 'current'},
								    dataType: 'json',
								    type: 'post',
								    success: function(output) {
								        if (output.length > 0) {
								        	var keys = [];
								        	for(i=0; i<output.length; i++) {
								        		Object.keys(output[i]).forEach(function(key){
											        if(keys.indexOf(key) == -1)
											        {
											            keys.push(key);
											            // alert(output[i][key]);
											            $("input[name='" + key + "']").val(output[i][key]);
											            if ($('.portal').length) {
											            	switch(key) {
															    case "totalQty":
															    	$("#" + key).children('h2').text('Qty - ' + output[i][key] + 'x');
															        break;
															    case "totalPrice":
															        $("#" + key).children('h1').text('Total - $' + output[i][key]);
															        break;
															    case "profit":
															        $("#" + key).children('h2').text('Profit - $' + output[i][key]);
															        break;
															    default:
															        
															}
											            }
											        }
											    });
								        	}
								        	if ($('.portal').length) {
								        		$('table tbody').empty();
												ajaxManager.addReq({
													url: 'orderTable.php',
												    data: {orderID: $('#orderID').children('input').val()},
												    type: 'post',
												    success: function(output) {
												        $('table tbody').append(output);
												        dropDown();
												    }
												});
								        	}
								        	locked = 0;
								        }
								        // getRow();
								    }
								});
	  						setTimeout(function () {
	  							$("input[name='" + output + "']").siblings('.circle-loader').removeClass('visible', 500);
	  							$("input[name='" + output + "']").siblings('.circle-loader').removeClass('load-complete');
	  							$("input[name='" + output + "']").siblings('.circle-loader').find('.checkmark').toggle();
	  						}, 1000);
					    },
					    failed: function() {
					    	$("input[name='" + output + "']").siblings('.circle-loader').removeClass('visible', 500);
					    }
					});				     

				     $(this).dequeue();

				  });
			});
			$(document).on("focusout",".portalInput",function(){
			    $(this).parent().css('border-color', 'orange');
				var variable = $(this);
				ajaxManager.addReq({
					url: 'updateTable.php',
				    data: {table: 'orderdetails', id: $(this).parent().parent().attr('name'), field: $(this).attr('name'), value: $(this).val(), pkey: 'id'},
				    type: 'post',
				    success: function(output) {
				        variable.parent().css('border-color', 'green');

				        ajaxManager.addReq({
									url: 'getRecord.php',
								    data: {table: '<?php echo $tableName; ?>', id: $('input[name="<?php echo $pkey; ?>"]').val(), pkey: '<?php echo $pkey; ?>', direction: 'current'},
								    dataType: 'json',
								    type: 'post',
								    success: function(output) {
								        if (output.length > 0) {
								        	var keys = [];
								        	for(i=0; i<output.length; i++) {
								        		Object.keys(output[i]).forEach(function(key){
											        if(keys.indexOf(key) == -1)
											        {
											            keys.push(key);
											            // alert(output[i][key]);
											            $("input[name='" + key + "']").val(output[i][key]);
											            if ($('.portal').length) {
											            	switch(key) {
															    case "totalQty":
															    	$("#" + key).children('h2').text('Qty - ' + output[i][key] + 'x');
															        break;
															    case "totalPrice":
															        $("#" + key).children('h1').text('Total - $' + output[i][key]);
															        break;
															    case "profit":
															        $("#" + key).children('h2').text('Profit - $' + output[i][key]);
															        break;
															    default:
															        
															}
											            }
											        }
											    });
								        	}
								        	if ($('.portal').length) {
								        		$('table tbody').empty();
												ajaxManager.addReq({
													url: 'orderTable.php',
												    data: {orderID: $('#orderID').children('input').val()},
												    type: 'post',
												    success: function(output) {
												        $('table tbody').append(output);
												        dropDown();
												    }
												});	
								        	}
								        	locked = 0;
								        }
								        // getRow();
								    }
								});
  						setTimeout(function () {
  							variable.parent().css('border-color', '#efefef');
  						}, 2000);
				    },
				    failed: function() {
				    	$("input[name='" + output + "']").siblings('.circle-loader').removeClass('visible', 500);
				    }
				});
			});
			$('#deleteBtn').click(function() {
				ajaxManager.addReq({
					url: 'deleteRecord.php',
				    data: {table: '<?php echo $tableName; ?>', id: '<?php echo $recordID; ?>', pkey: '<?php echo $pkey; ?>'},
				    type: 'post',
				    success: function(output) {
				        if (output == '1') {
				        	window.location.replace("portal.php?record=<?php echo $record+1; ?>&table=<?php echo $tableName; ?>");
				        }
				    }
				});
			});
			$('#createBtn').click(function() {
				ajaxManager.addReq({
					url: 'createRecord.php',
				    data: {table: '<?php echo $tableName; ?>', pkey: '<?php echo $pkey; ?>'},
				    type: 'post',
				    success: function(output) {
				        if (output != "Error") {
				        	window.location.replace("portal.php?record=<?php echo $num_rows+1; ?>&table=<?php echo $tableName; ?>");
				        }
				    }
				});
			});
			$('#printBtn').click(function() {
				var items = new Array();
				var prices = new Array();
				<?php foreach($orderdetails as $key => $val){ ?>
			        items.push('<?php echo $val["name"]; ?>');
			        prices.push('<?php echo $val["subTotal"]; ?>');
			    <?php } ?>
				ajaxManager.addReq({
					url: 'invoice/createInvoice.php',
				    data: {company: 'AFL', address: '123 Fake St, Fakington 1234', email: 'aflshop@afl.com', telephone: '812345678', number: '<?php echo $recordID; ?>', item: items.join(','), price: prices.join(',')},
				    type: 'post',
				    success: function() {
				        printJS('invoice/invoice.pdf');
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
		</script>
	</body>
</html>
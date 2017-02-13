<html>
		
	<head>
        <link rel="stylesheet" type="text/css" href="layout.css">
		<link rel = "icon" href = "img/favicon.ico"/>
		<title> Cq-analyser </title>
	</head>
		
	<body>
		<header>
			<div>
				<img src="img/biogazelle_header.jpg" class="headerImg"/>
			</div>
		</header>
			
		<div class="main">
				
			<a href="http://localhost/biogazelle/main.php">
				<img src="img/logo.png" height="28" border="0"> 
			</a>
				
			<h1> Welcome to the C<sub>q</sub>-analyser. </h1>
			<p class="text"> Please upload a data-file. This file has to be in <strong>csv-, xsl- or xlsx-</strong>format. 
			Since the data is derived from qPCR-analysis, the file must contain only <strong>two columns</strong>: 
			one with the <strong>sample names</strong> and one with the <strong>data</strong> itself. 
			You can also choose which dimensions your graph should have. 
			The default dimensions are <strong>500x800</strong>. </p>
			
			<form action="" method="post" enctype = "multipart/form-data">
				<table>
					<tr>
						<td><label> Select a file: </label></td>
						<td><input type = "file" name = "file"  id = "file"></td>
					</tr>
					<tr>
						<td><label> Height: </label></td>
						<td> <input type = "text" name = "hight" id = "hight" value = 500></td>
					</tr>
					<tr>
						<td><label> Width: </label></td>
						<td> <input type = "text" name = "width" id = "width" value = 800> <br></td>
					</tr>
					<tr>
						<td colspan="2"><input type = "submit" name = "submit" value = "Submit"></td>
					</tr>
				</table>
			</form>

			<br> <hr> <br>
				
			<?php
				if (!empty($_FILES["file"]) && isset($_FILES["file"])) {
					$allowedExts = array  ("csv", "xls", "xlsx");
					$temp        = explode(".", $_FILES["file"]["name"]);
					$extension   = end    ($temp);
						
					/** Check file format */
					if ((($_FILES["file"]["type"] == "text/csv")
					||   ($_FILES["file"]["type"] == "application/vnd.ms-excel")
					||   ($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")))
					{
						if ($_FILES["file"]["error"] > 0) {
							echo "Error: " . $_FILES["file"]["error"] . "<br>";
						} else {
							move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
						}
							
						$pathinfo = pathinfo($_FILES["file"]["name"]);
						$file_name = $pathinfo['filename'];

						$path  = "upload/" . $_FILES["file"]["name"];
						$image = "plots/"  . $file_name . ".png";
												
						//echo "Path to uploaded file: " . $path . ". <br>"; 
						//echo "The file is " . $file_name;
						//echo "Path to generated graphic: " . $image . ". <br><br>"; 
							
						/** Count number of columns in csv-file */
						require_once './PHPExcel/Classes/PHPExcel.php';
						function checkExcelFile($path){
							$workbook  = PHPExcel_IOFactory::load($path);
							$column    = $workbook -> getActiveSheet() -> getHighestDataColumn();
							$colNumber = PHPExcel_Cell :: columnIndexFromString($column);
							return $colNumber;
						}
						$colCount = checkExcelFile($path);
						//echo "The number of columns is: " . $colCount . ". <br><br>";
							
						if ($colCount == 2) {
							
							// echo "The width is " . $_POST["width"] . ". <br>";
							// echo "The hight is " . $_POST["hight"] . ". <br>";
							
							/** Initialize variables for width and height */
							$width  = $_POST["width"];
							$height = $_POST["hight"];
							
							/** Execute R-script, including 3 variables */
							exec("Rscript R-script.R $path $width $height --no-save");
									
							//echo "The path to the file you uploaded is: ". $path . "<br>";
							//echo "The path to the image is: " . $image . "<br>";
							
							echo "<center><img src=$image  border=1 ></center><br>";
							echo "<center>(Right click the link and choose &quot;Save As...&quot; to download this file)</center><br>";
						} else {
							echo "The number of columns in your file is not correct. The file must only contain 2 columns. ";
						}
					} else {
						echo "Your file is in the wrong format. <br>";
					}
				} else {
					echo "Please select a file. <br>";
				}
			?>
		</div>
		
		<footer>
			<center>
				<br><br>
				&copy; Sander Claus - 
				<a href="http://www.biogazelle.com/" target="_blank">Biogazelle</a>
				- 2013
			</center>
		</footer>
	</body>
</html>
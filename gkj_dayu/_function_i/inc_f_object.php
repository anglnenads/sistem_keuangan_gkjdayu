<?php
function _myobject($object, $name, $class, $id, $value, $caption, $maxlength, $size, $rows, $cols, $required, $placeholder, $disabled, $extra = '')
{
	switch ($object) {

		case 1: //input text required
?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?><span style="color: red"> *</span></label>
				<input class="form-control" type="text" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>" <?= $disabled; ?> <?= $required; ?>>
			</div>
		<?php
			break;

		case 17: //input text not required
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input class="form-control" type="text" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>" <?= $disabled; ?>>
			</div>
		<?php
			break;

		case 19: //input text required (hanya huruf dan spasi)
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?><span style="color: red"> *</span></label>
				<input class="form-control" type="text" name="<?= $name; ?>" id="<?= $id; ?>"
					value="<?= $value; ?>" placeholder="<?= $placeholder; ?>" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>"
					<?= $disabled; ?> <?= $required; ?>
					pattern="[A-Za-z\s]+" title="Hanya boleh huruf dan spasi">
			</div>
		<?php
			break;

		case 11:	// textbox disabled
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input class="form-control" type="text" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>" disabled <?= $required; ?>>
				<input type="hidden" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= $value; ?>">
			</div>
		<?php
			break;

		case 111:	// textbox for number required
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?><span style="color: red"> *</span></label>
				<input type="number" name="<?= $name; ?>" value="<?= $value; ?>" id="<?= $id; ?>" class="form-control" placeholder="<?= $placeholder; ?>" aria-label="Username" aria-describedby="basic-addon1" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>" <?= $required; ?>>
			</div>
		<?php
			break;

		case 112:	// number (javascript)
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?><span style="color: red"> *</span></label>
				<input type="number" name="<?= $name; ?>" value="<?= $value; ?>" id="<?= $id; ?>" class="form-control" placeholder="<?= $placeholder; ?>" aria-label="Username" aria-describedby="basic-addon1" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>" <?= $required; ?> <?= $extra; ?>>
			</div>
		<?php
			break;

		case 13:	// upload file
		?>
			<div class="mb-3">
				<label for="formFile" class="form-label"><?= $caption; ?></label>
				<input class="form-control" type="file" name="<?= $name; ?>" id="<?= $id; ?>">
			</div>
		<?php
			break;

		case 14:	// date date date('d-m-Y', strtotime($placeholder))
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?><span style="color: red"> *</span></label>
				<input class="form-control" type="date" id="<?= $id; ?>" name="<?= $name; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>" <?= $required; ?>>
			</div>
		<?php
			break;

		case 16:	// date date date('d-m-Y', strtotime($placeholder))
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input class="form-control" type="date" id="<?= $id; ?>" name="<?= $name; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>">
			</div>
		<?php
			break;

		case 15:	// datetime
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input class="form-control" type="datetime-local" id="<?= $id; ?>" name="<?= $name; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>" <?= $required; ?>>
			</div>
		<?php
			break;

		case 2:		// textbox hidden	
		?>
			<input type="hidden" name="<?= $name; ?>" value="<?= $value; ?>" id="<?= $id; ?>" class="form-control" placeholder="<?= $placeholder; ?>">
			<p></p>
		<?php
			break;

		case 23:	// upload file
			echo '<input type="file" name="' . $name . '" class="' . $class . '" id="' . $id . '" value="' . $value . '">';
			break;

		case 3:		// textarea
		?>
			<label for="<?= $id; ?>"><?= $caption; ?></label>
			<textarea class="form-control" id="<?= $id; ?>" name="<?= $name; ?>" placeholder="<?= $placeholder; ?>" rows="<?= $rows; ?>" cols="<?= $cols; ?>" id="floatingTextarea"><?= $value; ?></textarea>
		<?php
			break;

		case 4:		// radio
		?>
			<?php
			//$sql = "SELECT keterangan field1, keterangan field2 FROM akunjenis";
			$view = new cView();
			$arraypilihan = $view->vViewData($placeholder);
			$x = 0;
			$alreadyChecked = false;
			foreach ($arraypilihan as $datapilihan) {
				$x = $x + 1;
				if ($value == $datapilihan["field1"]) {
					$checked = "checked";
				} else {
					$checked = "";
				}
				if ($checked) $alreadyChecked = true;

				// Hanya tambahkan required di radio pertama DAN jika belum ada yang checked
				$required_attr = ($x == 1 && !$alreadyChecked) ? "required" : "";

				if ($x == 1) {
			?>
					<label for="<?= $id; ?>"><?= $caption; ?><span style="color: red"> *</span></label>
					<br>
				<?php
				}
				?>
				<div class="form-check">
					<input class="form-check-input" id="<?= $id . $x; ?>" name="<?= $name; ?>" type="radio" value="<?= $datapilihan['field1']; ?>" <?= $checked; ?><?= $required_attr; ?>>
					<label class="form-check-label" for="inlineCheckbox1"><?= $datapilihan["field2"]; ?></label>
				</div>
			<?php
			}
			?>
			<p></p>
		<?php
			break;

		case 41:	// radio inline
		?>
			<?php
			//$sql = "SELECT keterangan field1, keterangan field2 FROM akunjenis";
			$view = new cView();
			$arraypilihan = $view->vViewData($placeholder);
			$x = 0;
			foreach ($arraypilihan as $datapilihan) {
				$x = $x + 1;
				if ($value == $datapilihan["field1"]) {
					$checked = "checked";
				} else {
					$checked = "";
				}

				if ($x == 1) {
			?>
					<label for="<?= $id; ?>"><?= $caption; ?></label>
					<br>
				<?php
				}
				?>
				<div class="form-check form-check-inline">
					<input class="form-check-input" id="<?= $id; ?>" name="<?= $name; ?>" type="radio" id="inlineCheckbox1" value="<?= $datapilihan["field2"]; ?>" <?= $checked; ?><?= $required; ?>>
					<label class="form-check-label" for="inlineCheckbox1"><?= $datapilihan["field1"]; ?></label>
				</div>
			<?php
			}
			?>
			<p></p>
		<?php
			break;

		case 24:	// textbox disabled
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input class="form-control" type="text" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>" disabled <?= $required; ?>>
				<input type="hidden" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= $value; ?>">
			</div>
		<?php
			break;

		case 5:  //select combo required
			$view = new cView();
			$view->vViewData($caption);
			$arraypilihan = $view->vViewData($caption);
			$x = 0;
			$data[0][0] = "";
			$data[0][1] = "- pilihan -";
			foreach ($arraypilihan as $datapilihan) {
				$x = $x + 1;
				$data[$x][0] = $datapilihan["field1"];
				$data[$x][1] = $datapilihan["field2"];
			}
		?>
			<select name="<?php echo $name; ?>" class="<?php echo $class; ?>" id="<?php echo $id; ?>" <?= $required; ?>>
				<?php
				for ($i = 0; $i <= $x; $i++) {
					if ($data[$i][0] == $value) {
				?>
						<option value="<?php echo $data[$i][0]; ?>" selected><?php echo $data[$i][1]; ?></option>
					<?php } else { ?>
						<option value="<?php echo $data[$i][0]; ?>"><?php echo $data[$i][1]; ?></option>
				<?php }
				} ?>
			</select>

		<?php
			break;

		case 51:  //select combo not required
			$view = new cView();
			$view->vViewData($caption);
			$arraypilihan = $view->vViewData($caption);
			$x = 0;
			$data[0][0] = "";
			$data[0][1] = "- pilihan -";
			foreach ($arraypilihan as $datapilihan) {
				$x = $x + 1;
				$data[$x][0] = $datapilihan["field1"];
				$data[$x][1] = $datapilihan["field2"];
			}
		?>
			<select name="<?php echo $name; ?>" class="<?php echo $class; ?>" id="<?php echo $id; ?>">
				<?php
				for ($i = 0; $i <= $x; $i++) {
					if ($data[$i][0] == $value) {
				?>
						<option value="<?php echo $data[$i][0]; ?>" selected><?php echo $data[$i][1]; ?></option>
					<?php } else { ?>
						<option value="<?php echo $data[$i][0]; ?>"><?php echo $data[$i][1]; ?></option>
				<?php }
				} ?>
			</select>

		<?php
			break;

		case 12:  //select combo
			$view = new cView();
			$view->vViewData($caption);
			$arraypilihan = $view->vViewData($caption);
			$x = 0;
			$data[0][0] = "-";
			$data[0][1] = "-";
			foreach ($arraypilihan as $datapilihan) {
				$x = $x + 1;
				$data[$x][0] = $datapilihan["field1"];
				$data[$x][1] = $datapilihan["field2"];
			}
		?>
			<select name="<?php echo $name; ?>" class="<?php echo $class; ?>" id="<?php echo $id; ?>" <?php echo $required; ?> disabled>
				<?php
				for ($i = 0; $i <= $x; $i++) {
					if ($data[$i][0] == $value) {
				?>
						<option value="<?php echo $data[$i][0]; ?>" selected><?php echo $data[$i][1]; ?></option>
					<?php } else { ?>
						<option value="<?php echo $data[$i][0]; ?>"><?php echo $data[$i][1]; ?></option>
				<?php }
				} ?>
			</select>

		<?php
			break;


		case 224: //combobox with add option
			$view = new cView();
			$view->vViewData($caption);
			$arraypilihan = $view->vViewData($caption);
			$x = 0;
			$data[0][0] = "";
			$data[0][1] = "- pilihan -";
			foreach ($arraypilihan as $datapilihan) {
				$x = $x + 1;
				$data[$x][0] = $datapilihan["field1"];
				$data[$x][1] = $datapilihan["field2"];
			}
		?>

			<select name="<?php echo $name; ?>" class="<?php echo $class; ?>" id="<?php echo $id; ?>" <?= $required; ?> onchange="toggleInput(this, 'input_jenis_akun', 'hidden_jenis_akun')">
				<?php
				for ($i = 0; $i <= $x; $i++) {
					$selected = ($data[$i][0] == $value) ? "selected" : "";
					echo '<option value="' . $data[$i][0] . '" ' . $selected . '>' . $data[$i][1] . '</option>';
				}
				?>
				<option value="tambah_baru">+ Tambahkan Baru</option>
			</select>

			<input type="text" class="form-control" name="jenis_akun_input" id="input_jenis_akun" placeholder="Masukkan jenis akun baru" style="display:none;" />

			<input type="hidden" name="<?= $name; ?>" id="hidden_jenis_akun" value="<?= $value; ?>">

			<script>
				function toggleInput(selectElement, inputId, hiddenFieldId) {
					var inputField = document.getElementById(inputId);
					var hiddenField = document.getElementById(hiddenFieldId);
					if (selectElement.value === "tambah_baru") {
						inputField.style.display = "block"; // Tampilkan input field
						inputField.required = true;
						inputField.focus(); // Fokuskan input field

						// Update the hidden field with the value of the input field
						inputField.addEventListener('input', function() {
							hiddenField.value = inputField.value + "-dataBaru";
							// Set hidden field value to input field value
						});
					} else {
						inputField.style.display = "none";
						inputField.required = false; // Sembunyikan input field
						hiddenField.value = selectElement.value; // Set nilai hidden field dengan value dari dropdown
					}
				}
			</script>

		<?php

			break;

		case 7:  //select combo Ya/Tidak required

			$view = new cView();
			// Mengambil data pilihan (misalnya: 'Ya' dan 'Tidak')
			$arraypilihan = array(
				array("field1" => 1, "field2" => "Ya"),
				array("field1" => 0, "field2" => "Tidak")
			);

			$x = 0;
			$data[0][0] = "";
			$data[0][1] = "- pilihan -";

			foreach ($arraypilihan as $datapilihan) {
				$x = $x + 1;
				$data[$x][0] = $datapilihan["field1"];
				$data[$x][1] = $datapilihan["field2"];
			}
		?>
			<select name="<?php echo $name; ?>" class="<?php echo $class; ?>" id="<?php echo $id; ?>" <?php echo $required; ?>>
				<?php
				for ($i = 0; $i <= $x; $i++) {
					if ($data[$i][0] == $value) {
				?>
						<option value="<?php echo $data[$i][0]; ?>" selected><?php echo $data[$i][1]; ?></option>
					<?php } else { ?>
						<option value="<?php echo $data[$i][0]; ?>"><?php echo $data[$i][1]; ?></option>
				<?php }
				} ?>
			</select>
		<?php
			break;


		case 8:		// checkbox
		?>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="checkbox" id="inlineCheckbox1" name="<?= $name; ?>" value="">
				<label class="form-check-label" for="inlineCheckbox1"><?= $caption; ?></label>
			</div>
		<?php
			break;

		case 9:		// label
		?>
			<label for=""><?= $caption; ?></label>
			<br>
		<?php
			break;

		case 91:	// break
		?>
			<br>
	<?php
			break;
	}
}

function _myHeader($iconName, $header, $footer)
{
	?>
	<figure class="my-header">
		<blockquote class="blockquote">
			<p>
				<ion-icon name="<?= $iconName; ?>"></ion-icon> &nbsp;
				<?= $header; ?>
			</p>
		</blockquote>
		<figcaption class="blockquote-footer">
			<?= $footer; ?>
		</figcaption>
	</figure>
<?php
}

//function _mytable($object,$class,$id,$width,$align,$valign,$value,$rowspan,$colspan) {
function _mytable($object, $class, $id, $width, $align, $valign, $value)
{
	switch ($object) {
		case "table":
			echo '<div class="table-responsive">';
			echo '<table class="' . $class . '" id="' . $id . '" width="' . $width . '" ' . $value . '>';
			break;
		case "th":
			if (empty($colspan)) {
				echo '<th class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '">' . $value . '</th>';
			} else {
				echo '<th class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '" colspan=' . $colspan . '>' . $value . '</th>';
			}
			break;
		case "tr":		// open tr
			echo '<tr class="' . $class . '">';
			break;
		case "td":		// td
			switch ($align) {
				case "c":
					$align = "center";
					break;
				case "r":
					$align = "right";
					break;
				case "":
					$align = "left";
					break;
			}
			switch ($valign) {
				case "":
					$valign = "top";
					break;
				case "m":
					$align = "midle";
					break;
				case "b":
					$align = "bottom";
					break;
			}
			if (empty($colspan)) {
				if (empty($rowspan)) {
					echo '<td class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '">' . $value . '</td>';
				}
			} else {
				if (empty($rowspan)) {
					echo '<td class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '" colspan=' . $colspan . '>' . $value . '</td>';
				} else {
					echo '<td class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '" rowspan=' . $rowspan . '>' . $value . '</td>';
				}
			}
			break;
		case "/tr":		// close tr
			echo '</tr>';
			break;
		case "/table":	// close table
			echo '</table>';
			echo '</div>';
			break;
	}
}

// mytable_coloumn
function _mytable_coloumn($object, $class, $id, $width, $align, $valign, $value, $caption)
{
	switch ($object) {
		case "table":
			echo '<div class="table-responsive">';
			echo '<table class="' . $class . '" id="' . $id . '" width="' . $width . '" ' . $value . '>';
			break;
		case "th":
			echo '<th class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '">' . $value . '</th>';
			break;
		case "tr":		// open tr
			echo '<tr class="' . $class . '">';
			break;
		case "td":		// td
			switch ($align) {
				case "c":
					$align = "center";
					break;
				case "r":
					$align = "right";
					break;
				case "":
					$align = "left";
					break;
			}
			switch ($valign) {
				case "":
					$valign = "top";
					break;
				case "m":
					$align = "midle";
					break;
				case "b":
					$align = "bottom";
					break;
			}
			echo '<tr>';
			$width_value = $width - 5;
			echo '<td class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '">' . $caption . '</td>';
			echo '<td class="' . $class . '" id="' . $id . '" width="2%" align="center" valign="' . $valign . '">' . ':' . '</td>';
			echo '<td class="' . $class . '" id="' . $id . '" width="' . $width_value . '" align="' . $align . '" valign="' . $valign . '">' . $value . '</td>';
			echo '</tr>';
			break;
		case "/tr":		// close tr
			echo '</tr>';
			break;
		case "/table":	// close table
			echo '</table>';
			echo '</div>';
			break;
	}
}

// create window modal insert
function _CreateModalInsert($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl)
{
	//$footer_field = count($footer) - 1;
	$count_field = count($afield) - 1;
	$idmodal  = $name . $number;
	$opwindow = $button . $number;
	//$gicons = "glyphicon glyphicon-plus-sign";
	$clsbtn = "btn btn-primary btn-sm";
?>

	<button type="button" class="<?= $clsbtn; ?>" data-bs-toggle="modal" data-bs-target="#exampleModal" style="border-radius: 25px; background-color: #41406D; height: 40px; width: 180px;">
		<ion-icon name="add-circle"></ion-icon> &nbsp; <?= $title; ?>&nbsp;
	</button>
	</button>

	<!-- Modal -->

	<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title fs-5" id="exampleModalLabel">
						<blockquote class="blockquote">
							<p><?= $acaption[1]; ?></p>
						</blockquote>
					</h3>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form class="" method="post" action="<?php echo $linkurl; ?>" enctype="multipart/form-data">
					<div class="modal-body">

						<?php

						for ($i = 0; $i <= $count_field; $i++) {
							echo '<p>';


							switch ($afield[$i][3]) {
								case 1:		// textbox text required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;
								case 17:	// textbox text not required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;
								case 19:		// textbox text required (validasi huruf dan spasi saja)
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 11:	// disable
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 111:	// numeric
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 13:	// updload
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][0], "", "", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 14:	// date date('d-m-Y', strtotime($afield[$i][3]))

									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][1], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;
								case 15:	// datetime

									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][1], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 2:		// hidden value
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 3:		// textarea
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "", "", ($afield[$i][0]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 4:		// radio
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 41:	// radio inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 5:		// select combobox required
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '<span style="color: red"> *</span></label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 51:		// select combobox not required
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][4]), "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 224: // Select Combobox (ubah ke case 5 jika "+ Tambah Baru" dipilih)
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '<span style="color: red"> *</span></label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 7:		// select combobos yes/n required
									echo '<label for="caption' . $i . '">' . ($afield[$i][0]) . '<span style="color: red"> *</span></label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][0]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 8:		// checkbox inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;


								case 23:	// upload
									echo '<div class="form-group">';
									echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", $afield[$i][4], strtolower($afield[$i][0]), "");
									echo '</div>';
									break;

								case 9:		// label
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;

								case 91:	// bre
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;
							} // end of switch
							echo '</p>';
						} // end of for 						
						?>
					</div>

					<div class="modal-footer">
						<button type="submit" class="btn btn-primary btn-sm" style="border-radius: 25px;" name="savebtn" value="true">Simpan</button>
						<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">Tutup</button>
					</div>
				</form>
			</div>
		</div>
	</div>


<?php

}
?>
<?php
function _CreateWindowModal($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl, $footer)
{
	$footer_field = count($footer) - 1;
	$count_field = count($afield) - 1;
	$idmodal  = $name . $number;
	$opwindow = $button . $number;
	$gicons = "glyphicon glyphicon-plus-sign";
	$clsbtn = "btn btn-primary btn-sm";
?>
	<!-- Button trigger modal -->
	<button type="button" class="<?php echo $clsbtn; ?>" data-toggle="modal" data-target="<?php echo '#' . $idmodal; ?>">
		<ion-icon name="add-circle" style="font-size:18px"></ion-icon>&nbsp;<?php echo $title; ?>
	</button>
	<p></p>
	<!-- MODAL -->
	<FORM class="" method="post" action="<?php echo $linkurl; ?>" enctype="multipart/form-data">
		<div class="modal fade" id="<?php echo $idmodal; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<blockquote class="blockquote">
							<p class="mb-0"><?php echo $title; ?></p>
							<?php
							for ($baris = 0; $baris <= $footer_field; $baris++) {
								echo '<footer class="blockquote-footer">' . $footer[$baris] . '</footer>';
							}
							?>
						</blockquote>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<?php
						switch ($type) {
							case "view":
								break;
							case "insert";
								for ($i = 0; $i <= $count_field; $i++) {
									switch ($afield[$i][3]) {
										case 1:
											echo '<div class="form-group">';
											echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
											_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
											echo '</div>';
											break;
										case 2:
											echo '<div class="form-group">';
											_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
											echo '</div>';
											break;
										case 3:
											echo '<div class="form-group">';
											echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
											_myobject($afield[$i][3], $afield[$i][1], "form-control", "", "", "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
											echo '</div>';
											break;
										case 4:
											echo '<div class="form-group">';
											echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
											_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
											echo '</div>';
											break;

										case 5:
											echo '<div class="form-group">';
											echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
											_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
											echo '</div>';
											break;
										case 51:
											echo '<div class="form-group">';
											echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
											_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
											echo '</div>';
											break;
										case 52:
											echo '<div class="form-group" id="response">';

											_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
											echo '</div>';
											break;

										case 6:
											echo '<div class="form-grup">';
											echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
											_myobject($afield[$i][3], $afield[$i][1], "form-control", "", trim($afield[0][4]), "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
											echo '</div>';
											break;
										case 8:
											echo '<label class="checkbox-inline">';
											_myobject($afield[$i][3], $afield[$i][1], "", $afield[$i][1], "", strtoupper($afield[$i][0]), "", "", "", "", "", strtolower($afield[$i][0]), "", "RENCANA  PELAKSANAAN", "");
											echo '</label>';
											break;
										case 11:
											echo '<div class="form-group">';
											echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
											_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
											echo '</div>';
											break;
										case 14:
											echo '<div class="form-group">';
											echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
											_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "", strtolower($afield[$i][0]), "");
											echo '</div>';
											break;
										case 111:
											echo '<div class="form-group">';
											echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
											_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], "", "", "20", "", "", "", strtolower($afield[$i][0]), "");
											echo '</div>';
											break;
										case 1111:
											echo '<div class="form-group">';
											echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
											_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "", strtolower($afield[$i][0]), "");
											echo '</div>';
											break;

										case 13:
											echo '<div class="form-group">';
											echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
											//_myobject($object,$name,$class,$id,$value,$caption,$maxlength,$size,$rows,$cols,$required,$placeholder,$disabled)
											_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", $afield[$i][4], strtolower($afield[$i][0]), "");
											echo '</div>';
											break;
									}  // end of switch
								} // end of for 
								break;
						}
						?>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary btn-sm" name="save" value="save">
							<ion-icon name="save-outline"></ion-icon>&nbsp;SIMPAN
						</button>&nbsp;
						<button type="reset" class="btn btn-info btn-sm">ULANG</button>&nbsp;&nbsp;&nbsp;
						<input type="hidden" name="url" value="<?php echo $linkurl; ?>">
						<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">TUTUP</button>
					</div>
				</div>
			</div>
		</div>
	</FORM>
	<!-- End Modal -->
<?php
}

// create window detil
function _CreateWindowModalDetil($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl, $footer)
{
	// get title
	$titledetil = explode('#', $title);
	$countcolum = count($titledetil);

	// get width
	if (empty($width)) {
		$modalsize = '';
	} elseif ($width == 'xl') {
		$modalsize = 'modal-xl';
	} elseif ($width == 'lg') {
		$modalsize = 'modal-lg';
	} elseif ($width == 'sm') {
		$modalsize = 'modal-sm';
	} elseif ($width == 'xs') {
		$modalsize = 'modal-xs';
	}

	$number = $type . $name . $number;
	$count_field = count($afield) - 1;


?>
	<!-- Button trigger modal -->
	<button type="button" class="btn btn-info w-50 h-50" data-bs-toggle="modal" data-bs-target="#formview<?= $number; ?>" style=" border-radius:100%;  padding:6px">
		<ion-icon name="eye-outline"></ion-icon>
	</button>

	<!-- Modal -->
	<div class="modal fade" id="formview<?= $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog <?= $modalsize; ?> text-start">
			<div class="modal-content">
				<div class="modal-header">
					<figure class="text-left">
						<blockquote class="blockquote">
							<p><?= $titledetil[0]; ?></p>
						</blockquote>
						<?php
						if (!empty($titledetil[1])) {
							for ($k = 1; $k < $countcolum; $k++) {
						?>
								<figcaption class="blockquote-footer">
									<?= $titledetil[$k]; ?>
								</figcaption>
						<?php
							}
						}
						?>
					</figure>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<?php
					_mytable("table", "table table-condensed", "", "100%", "", "", "");
					for ($i = 0; $i <= $count_field; $i++) {
						_mytable("tr", "", "", "", "", "", "");
						_mytable("td", "", "", "35%", "l", "", $afield[$i][0]);
						_mytable("td", "", "", "1%", "c", "", $afield[$i][1]);
						_mytable("td", "", "", "70%", "l", "", $afield[$i][2]);
						_mytable("/tr", "", "", "", "", "", "");
					}
					_mytable("/table", "", "", "", "", "", "");
					?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">Tutup</button>
				</div>
			</div>
		</div>
	</div>
<?php

}

function _CreateWindowModalPencairan($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $disabled = false, $linkurl, $btnText)
{
	// get title
	$titledetil = explode('#', $title);
	$countcolum = count($titledetil);

	// get width
	if (empty($width)) {
		$modalsize = '';
	} elseif ($width == 'xl') {
		$modalsize = 'modal-xl';
	} elseif ($width == 'lg') {
		$modalsize = 'modal-lg';
	} elseif ($width == 'sm') {
		$modalsize = 'modal-sm';
	} elseif ($width == 'xs') {
		$modalsize = 'modal-xs';
	}
	$number = $type . $name . $number;
	$count_field = count($afield) - 1;
?>

	<?php
	// Cek apakah tombol disabled atau tidak
	// $disabledAttr = $disabled ? 'disabled' : '';
	// $btnClass = $disabled ? 'btn-secondary' : 'btn-success'; // Warna abu-abu jika disabled


	// Cek apakah tombol disabled atau tidak
	$disabledAttr = $disabled ? 'disabled' : '';
	$btnClass = $disabled ? 'btn-secondary' : 'btn-primary'; // Warna abu-abu jika disabled
	?>

	<button type="button" class="btn <?= $btnClass; ?> btn-sm" data-bs-toggle="modal" data-bs-target="#formedit<?= $number; ?>" style="border-radius: 25px; width:100%" <?= $disabledAttr; ?>>
		<?= $btnText; ?>
	</button>




	<!-- Modal -->
	<div class="modal fade" id="formedit<?= $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog <?= $modalsize; ?> text-start">
			<div class="modal-content text-left">
				<div class="modal-header">
					<figure class="text-left">
						<blockquote class="blockquote">
							<p><?= $titledetil[0]; ?></p>
						</blockquote>
						<?php
						if (!empty($titledetil[1])) {
							for ($k = 1; $k < $countcolum; $k++) {
						?>
								<figcaption class="blockquote-footer">
									<?= $titledetil[$k]; ?>
								</figcaption>
						<?php
							}
						}
						?>
					</figure>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>

				<FORM method="post" enctype="multipart/form-data" action="<?= $linkurl; ?>">
					<div class="modal-body">
						<?php
						for ($i = 0; $i <= $count_field; $i++) {
							echo '<p>';
							switch ($afield[$i][3]) {

								// $object, $name, $class, $id, $value, $caption, $maxlength, $size, $rows, $cols, $required, $placeholder, $disabled

								case 1:		// textbox
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 11:	// disable
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 111:	// numeric
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 13:	// updload
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 14:	// date
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 15:	// datetime
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 16:	// date w/ required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 2:		// hidden value
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 3:		// textarea
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], ($afield[$i][0]), "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 4:		// radio
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 41:	// radio inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 5:		// select
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 12:		// select
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 7:		// select combobos yes/n
									echo '<label for="caption' . $i . '">' . ($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;



								case 8:		// checkbox inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 23:	// upload
									echo '<div class="form-group">';
									echo '<label for="caption' . $i . '">' . ($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", $afield[$i][4], strtolower($afield[$i][0]), "");
									echo '</div>';
									break;

								case 9:		// label
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;

								case 91:	// bre
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;
							}
						}
						?>
					</div>
					<div class="modal-footer">
						<button type="submit" name="btncair" value="true" class="btn btn-success btn-sm" style="border-radius: 25px; width: 70px;">Simpan</button>
						<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px; width: 70px;">Tutup</button>
						<!-- <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button> -->
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}

// create window upload
function _CreateWindowModalUpload($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl)
{
	$number = $type . $name . $number;
	$count_field   = count($afield) - 1;
	echo '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal' . $number . '"><ion-icon name="cloud-upload-outline"></ion-icon>';
	echo '</button>';
	echo '<FORM method="post" enctype="multipart/form-data" action="' . $linkurl . '">';
?>
	<div class="modal fade" id="myModal<?php echo $number; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">
						<blockquote><?php echo $title; ?></blockquote>
					</h4>
				</div>
				<div class="modal-body">
					<?php
					//echo "jumlah field".$count_field."<br>";
					for ($i = 0; $i <= $count_field; $i++) {
						//echo "0".$afield[$i][0]." 1".$afield[$i][1]." 2".$afield[$i][2]." 3".$afield[$i][3];
						switch ($afield[$i][3]) {
							case 1:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								//_myobject($object,$name,$class,$id,$value,$caption,$maxlength,$size,$rows,$cols,$required,$placeholder,$disabled)
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 2:
								echo '<div class="form-group">';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;

							case 5:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 13:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								//_myobject($object,$name,$class,$id,$value,$caption,$maxlength,$size,$rows,$cols,$required,$placeholder,$disabled)
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", $afield[$i][4], strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 14:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
						} // end of switch
					} // end of for
					?>
				</div>
				<div class="modal-footer">
					<?php
					echo '<button class="btn btn-primary btn-sm" type="submit" name="upload" value="upload">UPLOAD</button>';
					echo '<button type="reset" class="btn btn-primary btn-sm" name="edit" value="RESET">RESET</button>&nbsp;&nbsp;';
					echo '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">CLOSE</button>';
					?>
				</div>
			</div>
		</div>
	</div>
<?php
	echo '</FORM>';
}



// create window 
function _CreateWindowModalAdd($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl)
{
	$number = $type . $name . $number;
	$count_field   = count($afield) - 1;
	echo '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal' . $number . '">&nbsp;<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>&nbsp;';
	echo '</button>';
	echo '<FORM method="post" enctype="multipart/form-data" action="' . $linkurl . '">';
?>
	<div class="modal fade" id="myModal<?php echo $number; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">
						<blockquote>
							<p><?php echo $title; ?></p>
						</blockquote>
					</h4>
				</div>
				<div class="modal-body">
					<?php
					for ($i = 0; $i <= $count_field; $i++) {
						switch ($afield[$i][3]) {
							case 1:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 13:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								//_myobject($object,$name,$class,$id,$value,$caption,$maxlength,$size,$rows,$cols,$required,$placeholder,$disabled)
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", $afield[$i][4], strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 2:
								echo '<div class="form-group">';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 3:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 4:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 5:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 6:
								echo '<div class="form-grup">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "", trim($afield[0][4]), "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 8:
								echo '<label class="checkbox-inline">';
								//_myobject($object,$name,$class,$id,$value,$caption,$maxlength,$size,$rows,$cols,$required,$placeholder,$disabled) {
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");

								//_myobject($afield[$i][3],$afield[$i][1],"","inlineCheckbox1","1",strtoupper($afield[$i][0]),"","","","","",strtolower($afield[$i][0]),"","RENCANA  PELAKSANAAN","")		;
								echo '</label>';
								break;
							case 11:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 14:  // date
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 111:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 1111:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
						} // end of switch
					} // end of for
					?>
				</div>
				<div class="modal-footer">
					<?php
					echo '<button class="btn btn-primary btn-sm" type="submit" name="save" value="INSERT">SAVE</button>';
					echo '<button type="reset" class="btn btn-primary btn-sm" name="edit" value="RESET">RESET</button>&nbsp;&nbsp;';
					echo '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">CLOSE</button>';
					?>
				</div>
			</div>
		</div>
	</div>
<?php
	echo '</FORM>';
}
// end of window modal update
function _CreateWindowModalUpdate($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl, $disabled = false)
{
	// get title
	$titledetil = explode('#', $title);
	$countcolum = count($titledetil);

	// get width
	if (empty($width)) {
		$modalsize = '';
	} elseif ($width == 'xl') {
		$modalsize = 'modal-xl';
	} elseif ($width == 'lg') {
		$modalsize = 'modal-lg';
	} elseif ($width == 'md') {
		$modalsize = 'modal-md';
	} elseif ($width == 'sm') {
		$modalsize = 'modal-sm';
	} elseif ($width == 'xs') {
		$modalsize = 'modal-xs';
	}
	$number = $type . $name . $number;
	$count_field = count($afield) - 1;

	$disabledAttr = $disabled ? 'disabled' : '';
?>

	<button type="button" class="btn btn-primary w-50 h-50" data-bs-toggle="modal" data-bs-target="#formedit<?= $number; ?>" style=" border-radius:100%;  padding:6px" <?= $disabledAttr; ?>>
		<ion-icon name="create-outline"></ion-icon>
	</button>

	<!-- Modal -->
	<div class="modal fade" id="formedit<?= $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog <?= $modalsize; ?> text-start">
			<div class="modal-content text-left">
				<div class="modal-header">
					<figure class="text-left">
						<blockquote class="blockquote">
							<p><?= $titledetil[0]; ?></p>
						</blockquote>
						<?php
						if (!empty($titledetil[1])) {
							for ($k = 1; $k < $countcolum; $k++) {
						?>
								<figcaption class="blockquote-footer">
									<?= $titledetil[$k]; ?>
								</figcaption>
						<?php
							}
						}
						?>
					</figure>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>

				<FORM method="post" enctype="multipart/form-data" action="<?= $linkurl; ?>">
					<div class="modal-body">
						<?php
						for ($i = 0; $i <= $count_field; $i++) {
							$extraAttr = isset($afield[$i][4]) ? $afield[$i][4] : '';
							echo '<p>';
							switch ($afield[$i][3]) {

								// $object, $name, $class, $id, $value, $caption, $maxlength, $size, $rows, $cols, $required, $placeholder, $disabled

								case 1:		// textbox text required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;
								case 17:		// textbox text not required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;
								case 19:		// textbox text required (validasi huruf dan spasi)
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 11:	// disable
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 111:	// numeric
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 112:	// numeric w/js
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "", $extraAttr);
									break;

								case 13:	// updload
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 14:	// date
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 15:	// datetime
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 16:	// date w/ required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 2:		// hidden value
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 3:		// textarea
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], ($afield[$i][0]), "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 4:		// radio
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 41:	// radio inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 5:		// select combobox required
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '<span style="color: red"> *</span></label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;


								case 51:		// select combobox not required
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][4]), "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;


								case 224: // Select Combobox (ubah ke case 5 jika "+ Tambah Baru" dipilih)
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 12:		// select
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 7:		// select combobos yes/n
									echo '<label for="caption' . $i . '">' . ($afield[$i][0]) . '<span style="color: red"> *</span></label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;



								case 8:		// checkbox inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 23:	// upload
									echo '<div class="form-group">';
									echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", $afield[$i][4], strtolower($afield[$i][0]), "");
									echo '</div>';
									break;

								case 9:		// label
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;

								case 91:	// bre
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;
							}
						}
						?>
					</div>
					<div class="modal-footer">
						<button type="submit" name="editbtn" value="true" class="btn btn-primary btn-sm" style="border-radius: 25px;">SIMPAN</button>
						<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script>
		// function hitungOtomatis() {
		//     const jumlah = parseFloat(document.getElementById('volume')?.value) || 0;
		//     const harga = parseFloat(document.getElementById('harga_satuan')?.value) || 0;
		//     document.getElementById('jumlah').value = jumlah * harga;
		// }
		function hitungOtomatis(el) {
			const modal = el.closest('.modal');

			const volume = parseFloat(modal.querySelector('input[name="volume"]')?.value) || 0;
			const harga = parseFloat(modal.querySelector('input[name="harga_satuan"]')?.value) || 0;
			const jumlah = modal.querySelector('input[name="jumlah"]');

			if (jumlah) {
				jumlah.value = volume * harga;
			}
		}
	</script>
<?php

}

function _CreateModalSisaDana($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl, $disabled = false, $buttonCaption)
{
	// get title
	$titledetil = explode('#', $title);
	$countcolum = count($titledetil);

	// get width
	if (empty($width)) {
		$modalsize = '';
	} elseif ($width == 'xl') {
		$modalsize = 'modal-xl';
	} elseif ($width == 'lg') {
		$modalsize = 'modal-lg';
	} elseif ($width == 'sm') {
		$modalsize = 'modal-sm';
	} elseif ($width == 'xs') {
		$modalsize = 'modal-xs';
	}
	$number = $type . $name . $number;
	$count_field = count($afield) - 1;
?>
	<?php
	// Cek apakah tombol disabled atau tidak
	$disabledAttr = $disabled ? 'disabled' : '';
	$btnStyle = $disabled ? 'background-color: gray; color: white;' : 'background-color: #447ed1; color: white;';
	?>

	<!-- <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#formedit<?= $number; ?>" style=" padding:6px; font-size: 15px; border-radius: 20px;">
		Pengembalian Dana
	</button> -->

	<button type="button" class="btn-sm" data-bs-toggle="modal" data-bs-target="#formedit<?= $number; ?>" style="<?= $btnStyle ?> padding:6px; font-size: 15px; border-radius: 20px; width:180px; border:1px solid #3a529d" <?= $disabledAttr; ?>>
		<?= $buttonCaption ?>
	</button>

	<!-- Modal -->
	<div class="modal fade" id="formedit<?= $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog <?= $modalsize; ?> text-start">
			<div class="modal-content text-left">
				<div class="modal-header">
					<figure class="text-left">
						<blockquote class="blockquote">
							<p><?= $titledetil[0]; ?></p>
						</blockquote>
						<?php
						if (!empty($titledetil[1])) {
							for ($k = 1; $k < $countcolum; $k++) {
						?>
								<figcaption class="blockquote-footer">
									<?= $titledetil[$k]; ?>
								</figcaption>
						<?php
							}
						}
						?>
					</figure>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>

				<FORM method="post" enctype="multipart/form-data" action="<?= $linkurl; ?>">
					<div class="modal-body">
						<?php
						for ($i = 0; $i <= $count_field; $i++) {
							echo '<p>';
							switch ($afield[$i][3]) {

								// $object, $name, $class, $id, $value, $caption, $maxlength, $size, $rows, $cols, $required, $placeholder, $disabled

								case 1:		// textbox required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;
								case 17:		// textbox not required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 11:	// disable
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 111:	// numeric
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 13:	// updload
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 14:	// date
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 15:	// datetime
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 16:	// date w/ required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 2:		// hidden value
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 3:		// textarea
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], ($afield[$i][0]), "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 4:		// radio
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 41:	// radio inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 5:		// select required
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '<span style="color: red"> *</span></label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;
								case 51:		// select not required
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][4]), "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 12:		// select
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 7:		// select combobos yes/n
									echo '<label for="caption' . $i . '">' . ($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;



								case 8:		// checkbox inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 23:	// upload
									echo '<div class="form-group">';
									echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", $afield[$i][4], strtolower($afield[$i][0]), "");
									echo '</div>';
									break;

								case 9:		// label
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;

								case 91:	// bre
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;
							}
						}
						//insert

						?>
					</div>
					<div class="modal-footer">
						<button type="submit" name="savebtn" value="true" class="btn btn-primary btn-sm" style="border-radius: 25px;">SIMPAN</button>
						<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}

function _CreateWindowModalStatus($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl)
{
	// get title
	$titledetil = explode('#', $title);
	$countcolum = count($titledetil);

	// get width
	if (empty($width)) {
		$modalsize = '';
	} elseif ($width == 'xl') {
		$modalsize = 'modal-xl';
	} elseif ($width == 'lg') {
		$modalsize = 'modal-lg';
	} elseif ($width == 'sm') {
		$modalsize = 'modal-sm';
	} elseif ($width == 'xs') {
		$modalsize = 'modal-xs';
	}
	$number = $type . $name . $number;
	$count_field = count($afield) - 1;
?>

	<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#formedit<?= $number; ?>" style="border-radius: 25px; width:50%; height: 100%;">
		<ion-icon name="create"></ion-icon>
	</button>

	<!-- Modal -->
	<div class="modal fade" id="formedit<?= $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog <?= $modalsize; ?> text-start">
			<div class="modal-content text-left">
				<div class="modal-header">
					<figure class="text-left">
						<blockquote class="blockquote">
							<p><?= $titledetil[0]; ?></p>
						</blockquote>
						<?php
						if (!empty($titledetil[1])) {
							for ($k = 1; $k < $countcolum; $k++) {
						?>
								<figcaption class="blockquote-footer">
									<?= $titledetil[$k]; ?>
								</figcaption>
						<?php
							}
						}
						?>
					</figure>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>

				<FORM method="post" enctype="multipart/form-data" action="<?= $linkurl; ?>">
					<div class="modal-body">
						<?php
						for ($i = 0; $i <= $count_field; $i++) {
							switch ($afield[$i][3]) {

								// $object, $name, $class, $id, $value, $caption, $maxlength, $size, $rows, $cols, $required, $placeholder, $disabled

								case 1:		// textbox
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 11:	// disable
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 111:	// numeric
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 13:	// updload
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 14:	// date
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 15:	// datetime
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 2:		// hidden value
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 3:		// textarea
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], strtoupper($afield[$i][0]), "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 4:		// radio
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 41:	// radio inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 5:		// select
									echo '<label for="caption' . $i . '">' . $afield[$i][0] . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 7:		// select combobos yes/n
									echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 8:		// checkbox inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 23:	// upload
									echo '<div class="form-group">';
									echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", $afield[$i][4], strtolower($afield[$i][0]), "");
									echo '</div>';
									break;

								case 9:		// label
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;

								case 91:	// bre
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;
							}
						}
						?>
					</div>
					<div class="modal-footer">
						<button type="submit" name="statusbtn" value="true" class="btn btn-primary btn-sm" style="border-radius: 25px;">SIMPAN</button>
						<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}
?>

<!-- create window delete -->
<!-- $on,"del","del-form","del-button",400,200,"HAPUS DATA".'<footer class="blockquote-footer">'.'xx'.' '.'yy'.'</footer>',"",$datadelete,"","?l=".$_GET["l"]); -->
<?php
function _CreateWindowModalDelete($number, $type, $name, $button, $width, $height, $title, $acaption, $value, $linkurl, $disabled = false)
{
	// get title
	$titledetil = explode('#', $title);
	$countcolum = count($titledetil);
	$number = $type . $name . $number;

	// get width
	if (empty($width)) {
		$modalsize = '';
	} elseif ($width == 'xl') {
		$modalsize = 'modal-xl';
	} elseif ($width == 'lg') {
		$modalsize = 'modal-lg';
	} elseif ($width == 'sm') {
		$modalsize = 'modal-sm';
	} elseif ($width == 'xs') {
		$modalsize = 'modal-xs';
	}

	$disabledAttr = $disabled ? 'disabled' : '';

?>
	<!-- <button type="button" class="btn btn-danger btn-md " data-bs-toggle="modal" data-bs-target="#formdelete<?= $number; ?>" style=" border-radius:100% ">
		<ion-icon name="trash-bin-outline"></ion-icon>
	</button> -->

	<button type="button" class="btn btn-danger w-50 h-50 " data-bs-toggle="modal" data-bs-target="#formdelete<?= $number; ?>" style=" border-radius:100%;  padding:6px" <?= $disabledAttr; ?>>
		<ion-icon name="trash-bin-outline"></ion-icon>
	</button>





	<div class="modal fade" id="formdelete<?= $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog <?= $modalsize; ?> text-start">
			<div class="modal-content">
				<div class="modal-header">
					<figure>
						<blockquote class="blockquote">
							<p><?= $titledetil[0]; ?></p>
						</blockquote>
						<?php
						if (!empty($titledetil[1])) {
							for ($k = 1; $k < $countcolum; $k++) {
						?>
								<figcaption class="blockquote-footer">
									<?= $titledetil[$k]; ?>
								</figcaption>
						<?php
							}
						} ?>
					</figure>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form method="post" action="<?= $linkurl; ?>">
					<div class="modal-body">
						<h6 class="">Yakin akan menghapus? </h6>
						<?php
						for ($k = 0; $k <= 2; $k++) {
							echo '<input type="hidden" name="hiddendeletevalue' . $k . '" value="' . $value[0][$k] . '">';
						}
						?>
					</div>
					<div class="modal-footer">
						<button type="submit" name="btnhapus" value="true" class="btn btn-danger btn-sm" style="border-radius: 25px;">HAPUS</button>
						<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}
// end of window modal delete

function _CreateWindowModalValid($number, $type, $name, $button, $width, $height, $title, $value, $linkurl, $disabled = false, $btnText, $caption)
{
	// get title
	$titledetil = explode('#', $title);
	$countcolum = count($titledetil);
	$number = $type . $name . $number;

	// get width
	if (empty($width)) {
		$modalsize = '';
	} elseif ($width == 'xl') {
		$modalsize = 'modal-xl';
	} elseif ($width == 'lg') {
		$modalsize = 'modal-lg';
	} elseif ($width == 'sm') {
		$modalsize = 'modal-sm';
	} elseif ($width == 'xs') {
		$modalsize = 'modal-xs';
	}

?>
	<?php
	// Cek apakah tombol disabled atau tidak
	$disabledAttr = $disabled ? 'disabled' : '';
	$btnClass = $disabled ? 'btn-secondary' : 'btn-success'; // Warna abu-abu jika disabled
	?>

	<button type="button" class="btn <?= $btnClass; ?> btn-sm" data-bs-toggle="modal" data-bs-target="#formvalid<?= $number; ?>" style="border-radius: 25px; width:100%" <?= $disabledAttr; ?>>
		<?= $btnText; ?>
	</button>

	<!-- <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#formvalid<?= $number; ?>" style="border-radius: 25px; width:100%">
		<ion-icon name="checkmark-circle-outline"></ion-icon>
	Konfirmasi
	</button> -->

	<div class="modal fade" id="formvalid<?= $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog <?= $modalsize; ?> text-start">
			<div class="modal-content">
				<div class="modal-header">
					<figure>
						<blockquote class="blockquote">
							<p><?= $titledetil[0]; ?></p>
						</blockquote>
						<?php
						if (!empty($titledetil[1])) {
							for ($k = 1; $k < $countcolum; $k++) {
						?>
								<figcaption class="blockquote-footer">
									<?= $titledetil[$k]; ?>
								</figcaption>
						<?php
							}
						} ?>
					</figure>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form method="post" action="<?= $linkurl; ?>">
					<div class="modal-body">
						<h6 class=""><?= $caption ?> </h6>
						<?php
						for ($k = 0; $k <= 2; $k++) {
							echo '<input type="hidden" name="hiddenupdatevalue' . $k . '" value="' . $value[0][$k] . '">';
						}
						?>
					</div>
					<div class="modal-footer">
						<button type="submit" name="btnsetuju" value="true" class="btn btn-success btn-sm" style="border-radius: 25px; width: 70px;">YA</button>
						<button type="submit" name="btntolak" value="false" class="btn btn-danger btn-sm" style="border-radius: 25px; width: 70px;">TIDAK</button>
						<!-- <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button> -->
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}

function _CreateWindowModalReset($number, $type, $name, $button, $width, $height, $title, $acaption, $value, $linkurl)
{
	// get title
	$titledetil = explode('#', $title);
	$countcolum = count($titledetil);
	$number = $type . $name . $number;

	// get width
	if (empty($width)) {
		$modalsize = '';
	} elseif ($width == 'xl') {
		$modalsize = 'modal-xl';
	} elseif ($width == 'lg') {
		$modalsize = 'modal-lg';
	} elseif ($width == 'sm') {
		$modalsize = 'modal-sm';
	} elseif ($width == 'xs') {
		$modalsize = 'modal-xs';
	}

?>

	<button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#formreset<?= $number; ?>" style="font-weight:bold; border-radius: 25px; width:100%; background-color: #b1c4df; color: #24426d">
		Reset Password
	</button>

	<!-- <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#formvalid<?= $number; ?>" style="border-radius: 25px; width:100%">
		<ion-icon name="checkmark-circle-outline"></ion-icon>
	Konfirmasi
	</button> -->

	<div class="modal fade" id="formreset<?= $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog <?= $modalsize; ?> text-start">
			<div class="modal-content">
				<div class="modal-header">
					<figure>
						<blockquote class="blockquote">
							<p><?= $titledetil[0]; ?></p>
						</blockquote>
						<?php
						if (!empty($titledetil[1])) {
							for ($k = 1; $k < $countcolum; $k++) {
						?>
								<figcaption class="blockquote-footer">
									<?= $titledetil[$k]; ?>
								</figcaption>
						<?php
							}
						} ?>
					</figure>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form method="post" action="<?= $linkurl; ?>">
					<div class="modal-body">
						<h6 class="">Yakin akan reset password? </h6>
						<?php
						for ($k = 0; $k <= 2; $k++) {
							echo '<input type="hidden" name="hiddenresetvalue' . $k . '" value="' . $value[0][$k] . '">';
						}
						?>
					</div>
					<div class="modal-footer">
						<button type="submit" name="btnreset" value="true" class="btn btn-primary btn-sm" style="border-radius: 25px; width: 80px;">RESET</button>
						<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px; width: 80px">TUTUP</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}

function _CreateWindowModalProses($number, $type, $name, $button, $width, $height, $title, $value, $linkurl, $disabled = false, $btnText, $caption)
{
	// get title
	$titledetil = explode('#', $title);
	$countcolum = count($titledetil);
	$number = $type . $name . $number;

	// get width
	if (empty($width)) {
		$modalsize = '';
	} elseif ($width == 'xl') {
		$modalsize = 'modal-xl';
	} elseif ($width == 'lg') {
		$modalsize = 'modal-lg';
	} elseif ($width == 'sm') {
		$modalsize = 'modal-sm';
	} elseif ($width == 'xs') {
		$modalsize = 'modal-xs';
	}

?>
	<?php
	// Cek apakah tombol disabled atau tidak
	$disabledAttr = $disabled ? 'disabled' : '';
	$btnClass = $disabled ? 'btn-secondary' : 'btn-primary'; // Warna abu-abu jika disabled
	?>

	<button type="button" class="btn <?= $btnClass; ?> btn-sm" data-bs-toggle="modal" data-bs-target="#formvalid<?= $number; ?>" style="border-radius: 25px; width:100%" <?= $disabledAttr; ?>>
		<?= $btnText; ?>
	</button>


	<div class="modal fade" id="formvalid<?= $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog <?= $modalsize; ?> text-start">
			<div class="modal-content">
				<div class="modal-header">
					<figure>
						<blockquote class="blockquote">
							<p><?= $titledetil[0]; ?></p>
						</blockquote>
						<?php
						if (!empty($titledetil[1])) {
							for ($k = 1; $k < $countcolum; $k++) {
						?>
								<figcaption class="blockquote-footer">
									<?= $titledetil[$k]; ?>
								</figcaption>
						<?php
							}
						} ?>
					</figure>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form method="post" action="<?= $linkurl; ?>">
					<div class="modal-body">
						<h6 class=""><?= $caption ?> </h6>
						<?php
						for ($k = 0; $k <= 2; $k++) {
							echo '<input type="hidden" name="hiddenupdatevalue' . $k . '" value="' . $value[0][$k] . '">';
						}
						?>
					</div>
					<div class="modal-footer">
						<button type="submit" name="btnproses" value="true" class="btn btn-success btn-sm" style="border-radius: 25px;">Proses</button>
						<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">Tutup</button>
						<!-- <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button> -->
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}
// create window view
function _CreateWindowModalViewFile($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl)
{
	//echo $linkurl;
	$number = $type . $name . $number;
	$count_field   = count($afield) - 1;
	echo $count_field;
	echo '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal' . $number . '">&nbsp;<span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;';
	echo '</button>';
	echo '<FORM method="post" action="' . $linkurl . '">';
?>
	<div class="modal fade" id="myModal<?php echo $number; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel"><?php echo $title; ?></h4>
				</div>
				<div class="modal-body">
					Yakin akan menghapus data ini ?
				</div>
				<div class="modal-footer">
					<?php
					echo "-----" . $value[0][3] . "<br>";

					for ($k = 0; $k <= $count_field; $k++) {
						echo "-----" . $value[0][$k] . "<br>";
						echo '<input type="hidden" name="hiddendeletevalue' . $k . '" value="' . $value[0][$k] . '">';
					}
					echo '<button class="btn btn-danger btn-sm" type="submit" name="del" value="DELETE">DELETE</button>&nbsp;&nbsp;';
					echo '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">CLOSE</button>';
					?>
				</div>
			</div>
		</div>
	</div>
<?php
	echo '</form>';
}
// end of window modal delete

// Fungsi untuk mendapatkan nama berdasarkan ID menggunakan cView
function getNameFromId($table, $columnId, $columnName, $id)
{
	$sql = "SELECT $columnName FROM $table WHERE $columnId = '$id'";
	$view = new cView();
	$result = $view->vViewData($sql);

	// Memeriksa apakah data ditemukan
	if (count($result) > 0) {
		return $result[0][$columnName]; // Mengembalikan nama yang ditemukan
	}
	return "Tidak ditemukan"; // Jika tidak ada data
}

function UploadTruePdf($field, $path)
{
	$docfilename    = $_FILES[$field]["name"];
	$docfilesize    = $_FILES[$field]["size"];
	$docfileerror   = $_FILES[$field]["error"];
	$docfiletype    = strtolower(pathinfo($docfilename, PATHINFO_EXTENSION));
	$docfilecheck   = getimagesize($_FILES[$field]['tmp_name']);
	$docfilenewname = $_POST[$field] . "." . $docfiletype;
	if ($docfiletype == "pdf") {
		$status_upload = 1;
	} else {
		$status_upload = 0;
	}

	if ($docfilesize > 0 || $docfileerror == 0) {
		$status_size = 1;
	} else {
		$status_size = 0;
	}
	$statustrue = $status_upload . $status_size;

	$status_all = array($field, $statustrue, $path, $docfilenewname, $docfilename);
	return $status_all;
}


function _createpage($l, $range, $page, $table, $rpp)
{
	$res = "SELECT count(*) as recnumber FROM " . $table . " ";
	//echo $res."<br>";
	$view = new cView();
	$view->vViewData($res);
	$arrayView = $view->vViewData($res);
	foreach ($arrayView as $dataarray) {
		$maxrows = $dataarray["recnumber"];
		$pages = ceil($maxrows / $rpp);
	}

	//$maxrows = (int)@mysqli_fetch_array($res, 0);
	//$pages = ceil($maxrows/$rpp);

	if ($maxrows > $rpp) {
		echo '<nav>';
		echo '<ul class="pagination">';
		echo '<li><a href="?l=' . $l . '&pg=1" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
		for ($i = ($page - $range); $i < (($page + $range) + 1); $i++) {
			if (($i > 0) && ($i <= $pages)) {
				if ($i == $page + 1) {
					echo '<li class="active"><a href="#">' . ($i) . '</a></li>';
				} else {
					echo '<li><a href="?l=' . $l . '&pg=' . $i . '">' . ($i) . '</a></li>';
				}
			}
		}
		$last = $pages;
		echo '<li><a href="?l=' . $l . '&pg=' . $last . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
		echo '</ul>';
		echo '</nav>';
	}
}

function _BackButton($url)
{
	echo '<ul class="pagination pagination-sm">';
	echo '<li class="page-item"><a class="page-link" href="' . $url . '" style="border-radius:30px; border:0.5px solid #c0c0c0">
      <ion-icon name="caret-back-sharp"></ion-icon>&nbsp;BACK&nbsp;</a></li>';
	echo '</ul>';
}

function _DateFormat($dformat, $ddate)
{
	if (empty(trim($ddate))) {
		$returndate = "";
	} else {
		$returndate = date('d-m-Y', strtotime($ddate));
	}
	if ($returndate == '30-11--0001') {
		$returndate = "";
	}
	return $returndate;
}

function _ShowHeader($header, $smallheader)
{
	if (empty(trim($smallheader))) {
		echo '<blockquote>' . $header . '</blockquote>';
	} else {
		echo '<blockquote>' . $header . '<small>' . $smallheader . '</small></blockquote>';
	}
}

function _CreateTabs($caption, $link, $active)
{
	echo '<li role="presentation" class="' . $active . '"><a href="' . $link . '">' . $caption . '</a></li>';
}

function _CreateNavsData($sql, $type)
{
	echo "<br>" . $sql . "<br>";
	$view = new cView();
	$arraysql = $view->vViewData($sql);
	if (empty($_GET["id"])) {
		$_GET["id"] = "0";
	} else {
		$_GET["id"] = $_GET["id"];
	}
	echo $_GET["id"];
?>
	<ul class="nav nav-tabs">
		<?php
		if ($_GET["id"] == 0) {
			$cactive = "active";
		} else {
			$cactive = "";
		}
		?>
		<?php
		if (empty($type)) {
		?>
			<li role="presentation" class="<?php echo $cactive; ?>"><a href="?l=<?php echo $_GET["l"]; ?>&id=0">Semua</a></li>
		<?php
		}
		?>
		<?php
		foreach ($arraysql as $datasql) {
			if ($_GET["id"] == $datasql["field1"]) {
				$cactive = "active";
			} else {
				$cactive = "";
			}
		?>
			<li role="presentation" class="<?php echo $cactive; ?>"><a href="?l=<?php echo $_GET["l"]; ?>&id=<?php echo $datasql["field1"]; ?>"><?php echo $datasql["field2"]; ?></a></li>
		<?php } ?>
	</ul>
	<p><br></p>
<?php
}


function _DefaultValueCheckBox($variabel)
{

	if (!isset($_POST[$variabel])) {
		$_POST[$variabel] = "";
	} else {
		$_POST[$variabel] = "on";
	}

	if (trim($_POST[$variabel]) == 'on') {
		$returnvalue = 1;
	} else {
		$returnvalue = 0;
	}

	return $returnvalue;
}

function _ShowHari($value)
{
	$hari = substr($value, 3, strlen($value));
	return $hari;
}



function UploadTrue($field, $path)
{
	$docfilename    = $_FILES[$field]["name"];
	$docfilesize    = $_FILES[$field]["size"];
	$docfileerror   = $_FILES[$field]["error"];
	$docfiletype    = strtolower(pathinfo($docfilename, PATHINFO_EXTENSION));
	$docfilecheck   = getimagesize($_FILES[$field]['tmp_name']);
	$docfilenewname = $_POST[$field] . ".pdf";

	if ($docfiletype == "pdf") {
		$status_upload = 1;
	} else {
		$status_upload = 0;
	}

	if ($docfilesize > 0 || $docfileerror == 0) {
		$status_size = 1;
	} else {
		$status_size = 0;
	}
	$statustrue = $status_upload . $status_size;

	$status_all = array($field, $statustrue, $path, $docfilenewname);
	return $status_all;
}


function _CreateButtonViewPdf($id, $name)
{
	$name = $name . $id;
?>

<?php
	echo '<button id="' . $id . '" onclick="reply_click(this.id)" class="btn btn-info" data-toggle="modal" data-target="' . $name . '" data-placement="bottom" title="" value="del"><span class="glyphicon glyphicon-download"></span></button>';

	//echo '<button id="'.$dataarray["namapath"].$dataarray["namafile"].'" onclick="reply_click(this.id)" class="btn btn-danger" data-toggle="modal" data-target="#exampleModalDelete-JAFA-'.$cnourut.'" data-placement="bottom" title="DELETE SK"><span class="glyphicon glyphicon-trash"></span></button>'
}


function _CreateModalPDF($id, $pdf)
{
	//echo $id." ".$pdf;
	//echo $pdf;
	//echo $id."<br>";
?>
	<div class="modal fade" id="exampleModal<?php echo $id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<embed alt='Alt text for embed' src="<?php echo $pdf ?>" width="100%" height="500" type='application/pdf'>
					<!--  <div id="pdf"></div>  -->
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(function() {
			$('[data-toggle="tooltip"]').tooltip()
		})
	</script>
<?php
}


function _CreateModalRemoveFilePDF($id, $pdf, $linkurl, $idpk, $jenis)
{
?>

	<div class="modal fade" id="exampleModal<?php echo $id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">DELETE FILE</h4>
				</div>
				<div class="modal-body">
					<form method="post" action="<?php echo $linkurl ?>">
						<div class="modal-body">
							Yakin akan menghapus File ini ?
							<input type="hidden" name="file" value="<?php echo $pdf ?>">
							<input type="hidden" name="jenis" value="<?php echo $jenis ?>">
							<input type="hidden" name="idpk" value="<?php echo $idpk ?>">

						</div>
						<div class="modal-footer">
							<button class="btn btn-danger btn-sm" type="submit" name="del" value="DELETE">DELETE</button>&nbsp;
							<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">CLOSE</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(function() {
			$('[data-toggle="tooltip"]').tooltip()
		})
	</script>
<?php
}

function _CariValue($sql)
{
	//echo "<br>".$sql."<br>";
	$view = new cView();
	$arrayView = $view->vViewData($sql);
	foreach ($arrayView as $dataView) {
		$foundvalue = $dataView["field2"];
	}
	if (!empty($foundvalue)) {
		$foundvalue = $foundvalue;
	} else {
		$foundvalue = null;
	}
	return $foundvalue;
}

function _CreateDropDown($caption, $sql, $url, $addurl, $all)
{
	//echo "<br>".$sql."<br>";
	$view = new cView();
	$arraysql = $view->vViewData($sql);
?>
	<p>
	<div class="btn-group" role="group">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $caption; ?>&nbsp;<span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			<?php
			if ($all == 1) {
				echo '<li><a href="' . $url . '">Semua</a></li>';
			}
			foreach ($arraysql as $datasql) {
				echo '<li><a href="' . $url . '&' . $addurl . '=' . $datasql["field1"] . '">' . $datasql["field2"] . '</a></li>';
			}
			?>
		</ul>
	</div>
	</p>
	<?php
}


function _CountOfNumber($table, $field, $value1)
{
	$sql = "SELECT count(" . $field . ") as nJumlahnya FROM " . $table . " WHERE " . $value1 . "";
	//echo $sql."<br>";
	$view = new cView();
	$arrayView = $view->vViewData($sql);
	foreach ($arrayView as $dataView) {
		$nReturnValue = $dataView["nJumlahnya"];
	}
	return $nReturnValue;
}

function _ProgressBar($width, $value)
{
	echo '<div class="progress">';
	echo '<div class="progress-bar" role="progressbar" aria-valuenow="' . $width . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $width . '%;">' . $value . '';
	echo '</div>';
	echo '</div>';
}

function _ButtonSubmit($class, $caption, $name, $value, $hiddenname, $hiddenvalue, $url, $type)
{
	echo '<form method="post" action="' . $url . '">';
	echo '<button class="btn btn-' . $class . '" type="submit" name="' . $name . '" value="' . $value . '">' . $caption . '</button>';
	echo '<input type="hidden" name="' . $hiddenname . '" value="' . $hiddenvalue . '">';
	echo '</form>';
}

function _JenisKelamin($jkel)
{
	switch ($jkel) {
		case "L":
			$cjkel = "Laki-Laki";
			break;
		case "P":
			$cjkel = "Perempuan";
			break;
	}
	return $cjkel;
}



function _GetTotalPermohonan($kd_coa_dep, $unit_base, $kd_fiskal)
{
	$sql = "SELECT a.kd_coa_dep, sum(a.nominal) nominalpermohoan 
	FROM tra_pengajuan_detil a 
	WHERE a.kd_coa_dep = '" . $kd_coa_dep . "' 
	AND a.kd_fiskal = " . $kd_fiskal . " 
	AND a.unit_base = '" . $unit_base . "'";
	$view = new cView();
	$arrayview = $view->vViewData($sql);
	$nMohon = 0;
	foreach ($arrayview as $value) {
		$nMohon = $value["nominalpermohoan"];
	}
	return $nMohon;
}

function _GetPencairan($value)
{
	$sql = "SELECT a.idpengajuan, count(a.idpengajuan) nCair 
	FROM tra_pencairan_detil a 
	WHERE a.idpengajuan = '" . $value . "' GROUP BY 1";
	$view = new cView();
	$arrayview = $view->vViewData($sql);
	$nCair = 0;
	foreach ($arrayview as $value) {
		$nCair = $value["nCair"];
	}
	return $nCair;
}

function _GetJumlahAjuPerProses($value)
{
	$sql = "SELECT a.kodestatus, b.keterangan ketaju, b.warna, COUNT(a.idpengajuan) nAju
	FROM tra_pengajuan_detil a
	LEFT OUTER JOIN ref_statusaju b ON a.kodestatus = b.kodestatus
	WHERE a.idpengajuan = '" . $value . "' 
	GROUP BY 1, 2, 3";
	$view = new cView();
	$arrayview = $view->vViewData($sql);
	foreach ($arrayview as $dataview) {
		if ($dataview["kodestatus"] == 0) {
			$cl = "secondary";
		} elseif ($dataview["kodestatus"] == 1) {
			$cl = "success";
		} elseif ($dataview["kodestatus"] == 2) {
			$cl = "warning";
		} else {
			$cl = "danger";
		}
	?>
		<div class="row">
			<div class="col">
				<button type="button" class="btn btn-outline-<?= $cl; ?> btn-sm" style="border-radius:30px;">
					<?= $dataview["ketaju"]; ?>&nbsp;
					<span class="badge badge-secondary"><?= $dataview["nAju"]; ?></span>
				</button>
				</span>
			</div>
		</div>
<?php
	}
}


function _GetBulanTahun($value)
{
	$bln = substr($value, 0, 2);
	$thn = substr($value, 2, 4);
	$nmb = "";
	$sql = "SELECT a.kodebulan, a.namabulan FROM ref_bulan a WHERE a.kodebulan ='" . $bln . "'";
	$view = new cView();
	$arrayview = $view->vViewData($sql);
	foreach ($arrayview as $dataview) {
		$nmb = $dataview["namabulan"];
	}
	$tahunbulan = array($thn, $bln, $nmb);
	return $tahunbulan;
}



function _CekUnit($value)
{
	$sql = "SELECT a.* FROM ref_unit_base a WHERE a.kdunit = '" . $value . "'";
	$view = new cView();
	$arraydata = $view->vViewData($sql);
	$namaunitnya = "";
	foreach ($arraydata as $datadata) {
		$namaunitnya = $datadata["nama_unit"];
	}
	return $namaunitnya;
}

function _GetNamaBulan($value)
{
	$n = $value;
	switch ($n) {
		case 1:
			$bulan = "JANUARI";
			$sibul = "JAN";
			break;
		case 2:
			$bulan = "FEBRUARI";
			$sibul = "FEB";
			break;
		case 3:
			$bulan = "MARET";
			$sibul = "MAR";
			break;
		case 4:
			$bulan = "APRIL";
			$sibul = "APR";
			break;
		case 5:
			$bulan = "MEI";
			$sibul = "MEI";
			break;
		case 6:
			$bulan = "JUNI";
			$sibul = "JUN";
			break;
		case 7:
			$bulan = "JULI";
			$sibul = "JUL";
			break;
		case 8:
			$bulan = "AGUSTUS";
			$sibul = "AGU";
			break;
		case 9:
			$bulan = "SEPTEMBER";
			$sibul = "SEPT";
			break;
		case 10:
			$bulan = "OKTOBER";
			$sibul = "OKT";
			break;
		case 11:
			$bulan = "NOVEMBER";
			$sibul = "NOV";
			break;
		case 12:
			$bulan = "DESEMBER";
			$sibul = "DES";
			break;
	}
	$result = array($bulan, $sibul);
	return $result;
}
?>

<script>

</script>
<?php
	include "/sessions.php";
	include "/cssEditor.php";
	include "/fileManager.php";
	
	//check if logged in
	$account = new account();
	if (!$account->getLoggedIn()){
		header("location:login.php");
	}
	
	//check if enough righst
	if ($account->getRealRights() != 0 && $account->getRealRights() != 1){
		header("location:list.php");
	}
	
	//creat path and check if a username is given
	$path = "../groups/g" . $account->getGroup() . "/";
	if (!isset($_POST['user'], $_POST['mode'])){
		exit("E40");
	}
	
	//create css editer with valid username
	$_POST['user'] = upload::escape($_POST['user']);
	$path = $path . $_POST['user'] . ".css";
	$cssEditor = new CssEditor($path);
	$cssFile = $cssEditor->getCssData();
	$namingData = array();
	$counter = 0;
	function add($selector, $property, $value){
		global $counter;
		?>
			<div class="sort">
				<label id="property_<?php echo($counter); ?>"><?php echo($property); ?></label>
				<input type="text" value="<?php echo($value); ?>" id="value_<?php echo($counter); ?>" onchange="javascript:setChanged(<?php echo($counter) ?>)" />
				<input type="hidden" hidden="hidden" value="unchanged" id="status_<?php echo($counter); ?>" />
				<input type="hidden" hidden="hidden" value="<?php echo($selector); ?>" id="selector_<?php echo($counter); ?>"/>
			</div>
		<?php
		$counter++;
	}
	
	if ($_POST['mode'] == "ADV"){
		foreach ($cssFile as $selector => $data){
			?>
				<fieldset>
				<legend><?php echo($selector); ?></legend>
			<?php
			foreach ($data as $property => $value){
				add($selector, $property, $value);
				//$counter++;
			}
			
			?>
				</fieldset>
			<?php
		}
	} else if ($_POST['mode'] == "NORM"){	
		?>
			<fieldset>
				<legend>Media</legend>
				<?php
					add(".example img", "width", $cssFile['.example img']['width']);
					add(".example img", "height", $cssFile['.example img']['height']);
				?>
			</fieldset>
			
			<fieldset>
				<legend>Menu</legend>
				<?php
					add(".dropdownMenu", "background-color", $cssFile['.dropdownMenu']['background-color']);
					add(".dropdownMenu", "color", $cssFile['.dropdownMenu']['color']);
					//add(".dropdownMenu", "size");
				?>
			</fieldset>
			
			<fieldset>
				<legend>Algemeen</legend>
				<?php
					add("html", "color", $cssFile['html']['color']);
					add("html", "font-size", $cssFile['html']['font-size']);
				?>
			</fieldset>
		<?php
	}
?>
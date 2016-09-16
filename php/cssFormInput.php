<?php
	include "/sessions.php";
	include "/cssEditor.php";
	include "/fileManager.php";
	
	$account = new account();
	if (!$account->getLoggedIn()){
		header("location:login.php");
	}
	
	if ($account->getRealRights() != 0 && $account->getRealRights() != 1){
		header("location:list.php");
	}
	
	$path = "../groups/g" . $account->getGroup() . "/";
	if (!isset($_POST['user'])){
		exit("E40");
	}
	
	$_POST['user'] = upload::escape($_POST['user']);
	$path = $path . $_POST['user'] . ".css";
	$cssEditor = new CssEditor($path);
	$cssFile = $cssEditor->getCssData();
	
	$namingData = array();
	$counter = 0;
	
	foreach ($cssFile as $selector => $data){
		?>
			<fieldset>
			<legend><?php echo($selector); ?></legend>
		<?php
		foreach ($data as $property => $value){
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
		
		?>
			</fieldset>
		<?php
	}
?>
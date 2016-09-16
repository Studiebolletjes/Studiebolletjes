<?php
class Menu{
	public function setHeader(){
		?>
		<script src="/js/menu.js"></script>
		<?php
	}
	public function drawMenu(account $account) {
		?>
		<div id="MainMenu">
			<ul>
				<?php
				if ($account->getLoggedIn()){
					if ($account->getRights() == 1){
						//menu when logged in
						?>
							<li onclick="javascript:gotopage('/list.php')">Lijsten</li>
							<li onclick="javascript:gotopage('/new.php')">Nieuwe lijst</li>
							<li onclick="javascript:gotopage('/users.php')">Gebruikers</li>
							<li onclick="javascript:gotopage('styleEditor.php')">Style editor</li>
							<li onclick="javascript:gotopage('/setting.php')">Instellingen</li>
							
						<?php
					} elseif ($account->getRights() == 2){
						//menu when logged in
						?>
							<li onclick="javascript:gotopage('/list.php')">Lijsten</li>
							<li onclick="javascript:gotopage('/setting.php')">Instellingen</li>
						<?php
					} elseif ($account->getRights() == 0){
						
					}
					
					if ($account->isEditorMode()){
						?>
							<li onclick="javascript:escapeCssEditorMode()">Terug naar eigen account</li>
						<?php
					} else{
						?>
							<li onclick="javascript:bntLogout_OnClick('')">Logout</li>
						<?php
					}
				} else{
					//menu for guest
					?>
						<li onclick="javascript:gotopage('/login.php')">Inloggen</li>
					<?php
				}
			?>
			</ul>
		</div>
		<?php
	}
}
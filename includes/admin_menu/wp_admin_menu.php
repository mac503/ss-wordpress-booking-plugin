<?php
defined( 'ABSPATH' ) or die( 'Access denied.' );

add_action( 'admin_menu', 'add_massage_menu' );

function add_massage_menu() {
	add_menu_page( 'Massage Booking', 'Massage Booking', 'manage_options', 'sabai-salud-massage-booking', 'display_massage_menu', 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHZpZXdCb3g9IjAgMCA1MCA1MCIgdmVyc2lvbj0iMS4xIiB3aWR0aD0iNTBweCIgaGVpZ2h0PSI1MHB4Ij48ZyBpZD0ic3VyZmFjZTEiPjxwYXRoIHN0eWxlPSIgIiBkPSJNIDEyIDAgQyAxMC45MDYyNSAwIDEwIDAuOTA2MjUgMTAgMiBMIDEwIDQgTCA0IDQgQyAyLjgzOTg0NCA0IDIgNC44Mzk4NDQgMiA2IEwgMiAxMyBMIDQ4IDEzIEwgNDggNiBDIDQ4IDQuODM5ODQ0IDQ3LjE2MDE1NiA0IDQ2IDQgTCA0MCA0IEwgNDAgMiBDIDQwIDAuOTA2MjUgMzkuMDkzNzUgMCAzOCAwIEwgMzYgMCBDIDM0LjkwNjI1IDAgMzQgMC45MDYyNSAzNCAyIEwgMzQgNCBMIDE2IDQgTCAxNiAyIEMgMTYgMC45MDYyNSAxNS4wOTM3NSAwIDE0IDAgWiBNIDEyIDIgTCAxNCAyIEwgMTQgOCBMIDEyIDggWiBNIDM2IDIgTCAzOCAyIEwgMzggOCBMIDM2IDggWiBNIDIgMTUgTCAyIDQ2IEMgMiA0Ny4xNjAxNTYgMi44Mzk4NDQgNDggNCA0OCBMIDQ2IDQ4IEMgNDcuMTYwMTU2IDQ4IDQ4IDQ3LjE2MDE1NiA0OCA0NiBMIDQ4IDE1IFogTSAxMiAyMSBMIDE3IDIxIEwgMTcgMjYgTCAxMiAyNiBaIE0gMTkgMjEgTCAyNCAyMSBMIDI0IDI2IEwgMTkgMjYgWiBNIDI2IDIxIEwgMzEgMjEgTCAzMSAyNiBMIDI2IDI2IFogTSAzMyAyMSBMIDM4IDIxIEwgMzggMjYgTCAzMyAyNiBaIE0gMTIgMjggTCAxNyAyOCBMIDE3IDMzIEwgMTIgMzMgWiBNIDE5IDI4IEwgMjQgMjggTCAyNCAzMyBMIDE5IDMzIFogTSAyNiAyOCBMIDMxIDI4IEwgMzEgMzMgTCAyNiAzMyBaIE0gMzMgMjggTCAzOCAyOCBMIDM4IDMzIEwgMzMgMzMgWiBNIDEyIDM1IEwgMTcgMzUgTCAxNyA0MCBMIDEyIDQwIFogTSAxOSAzNSBMIDI0IDM1IEwgMjQgNDAgTCAxOSA0MCBaIE0gMjYgMzUgTCAzMSAzNSBMIDMxIDQwIEwgMjYgNDAgWiBNIDMzIDM1IEwgMzggMzUgTCAzOCA0MCBMIDMzIDQwIFogIi8+PC9nPjwvc3ZnPg==' );
}

function display_massage_menu(){
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	?>

	<h1>Massage Types</h1><br/>
<form id='ss_massage_types_form'>
	<div id='ss_massage_types'>
<?php
	foreach(get_massage_types() as $type){
		$checked = "";
		if($type['allowbookings'] == 1) $checked = " checked";
		echo "<div class='massageType' data-type-id='{$type["id"]}'><label>Name <input type='text' name='name' value='{$type["name"]}' disabled></input></label> <label>Display <input type='text' name='displayname' value='{$type["displayname"]}'></input></label> <label>Mins <input type='number' min='0' max='180' name='length' value='{$type["length"]}'></input></label> <label>Allow Bookings <input name='allowbookings' type='checkbox'$checked></input></label></div>";
	}
?>
	</div>
	<div class='button newType'>+</div>
	<br/><br/>
	<input type='submit' value='Save'></input> <div class='workingIndicator'></div><br/><br/>
</form>
	<h1>Default Opening Hours</h1>
<form id='ss_massage_openings_form'>
<?php
	$defaultOpenings = get_default_availability();
	$days = array(
		"1"=>"Monday",
		"2"=>"Tuesday",
		"3"=>"Wednesday",
		"4"=>"Thursday",
		"5"=>"Friday",
		"6"=>"Saturday",
		"0"=>"Sunday"
	);
	foreach($days as $i=>$name){
		echo "<h3>$name</h3>";
		for($j=0; $j<3; $j++){
			$start = $defaultOpenings[$i][$j]["start"];
			$start = str_pad(intval($start/60), 2, "0", STR_PAD_LEFT) . ":" . str_pad($start % 60, 2, "0", STR_PAD_LEFT);
			$end = $defaultOpenings[$i][$j]["end"];
			$end = str_pad(intval($end/60), 2, "0", STR_PAD_LEFT) . ":" . str_pad($end % 60, 2, "0", STR_PAD_LEFT);
			echo "<label>Start<input type='time' name='$i-$j-start' value='$start'></input></label> <label>End <input type='time' name='$i-$j-end' value='$end'></input></label><br/>";
		}
	}
?><br/>
	<div><input type='submit' value='Save'></input> <div class='workingIndicator'></div></div>
</form>
<br/>
<h1>Email Script Location (do not edit)</h1><br/>
<form id='ss_massage_script_form'>
<div id='ss_massage_script'>
<label>Location <input type='text' name='script_location' style='width: 50%;' value='<?php echo get_script_location(); ?>'></input></label>
</div>
<br/>
<input type='submit' value='Save'></input> <div class='workingIndicator'></div><br/><br/>
</form>

	<?php
	echo '</div>';
}
?>

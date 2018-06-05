<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

if (init('id') == '') {
	throw new Exception('{{L\'id de l\'opération ne peut etre vide : }}' . init('op_id'));
}

require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

$id = init('id');
sendVarToJS('_id', $id);
sendVarToJS('eqType', 'Multiloc');
$eqLogics = eqLogic::byType('Multiloc');
include_file("desktop", "leaflet", "js", "Multiloc");
foreach ($eqLogics as $eqLogic) {
	foreach ($eqLogic->getCmd('info') as $cmd) {
		if ($cmd->getEqLogic_id() == $id ){
			if ($cmd->getConfiguration("Typeloc") == "personne"){
				$personne = $personne .'L.marker(['. $cmd->getConfiguration("position") .']).addTo(mapmodal'. $cmd->getEqLogic_id().').bindPopup("' .$cmd->getName() .'").openPopup(); ';
				$tableau = $tableau . '<div class="panel panel-info"><div class="panel-heading"><img src="plugins/Multiloc/desktop/images/unknown.png" style="width:30px"; height:"30px"> <b>' .$cmd->getName() . '</b></div><div class="panel-body"><b>adresse:</b> '.$cmd->getConfiguration("rue").' '.$cmd->getConfiguration("codepostale").' '.$cmd->getConfiguration("ville").'</div> </div>  <br>';
			}elseif ($cmd->getConfiguration("Typeloc") == "lieu"){
				$lieu = $lieu.'L.circle(['. $cmd->getConfiguration("position") .'], 500, {color: "red",fillColor: "#f03",fillOpacity: 0.5}).addTo(mapmodal'.$cmd->getEqLogic_id().').bindPopup("' .$cmd->getName() .'");';
				$tableau = $tableau . '<div class="panel panel-success"><div class="panel-heading"><img src="plugins/Multiloc/desktop/images/house.png" style="width:30px"; height:"30px"> <b>' .$cmd->getName() . '</b></div><div class="panel-body"><b>adresse:</b> '.$cmd->getConfiguration("rue").' '.$cmd->getConfiguration("codepostale").' '.$cmd->getConfiguration("ville").'</div> </div>  <br>';

			}
		}
	}
}

?>


<div class="col-lg-9 col-md-9 col-sm-9">
	<div id="mapmodal<?php echo $id ?>" style="width:auto ; height: 550px;"></div>
</div>
<div class="col-lg-3 col-md-3 col-sm-3">
	<?php echo $tableau ?>
</div>

<script>
var mapmodal<?php echo $id ?> = L.map("mapmodal<?php echo $id ?>").setView([48.8401802,2.3670888], 9);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
	maxZoom: 16,
	attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
	'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
	'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
	id: 'mapbox.streets'
}).addTo(mapmodal<?php echo $id ?>);

<?php echo $personne ?>
<?php echo $lieu ?>

var popup = L.popup();

function onMapClick<?php echo $id ?>(e) {
	popup
	.setLatLng(e.latlng)
	.setContent("loc" + e.latlng.toString())
	.openOn(mapmodal<?php echo $id ?>);
}

mapmodal<?php echo $id ?>.on('click', onMapClick<?php echo $id ?>);

</script>

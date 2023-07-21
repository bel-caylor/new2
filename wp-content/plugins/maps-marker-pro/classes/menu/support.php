<?php
namespace MMP\Menu;

use MMP\Maps_Marker_Pro as MMP;

class Support extends Menu {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('admin_enqueue_scripts', array($this, 'load_resources'));
	}

	/**
	 * Loads the required resources
	 *
	 * @since 4.0
	 *
	 * @param string $hook The current admin page
	 */
	public function load_resources($hook) {
		if (substr($hook, -strlen('mapsmarkerpro_support')) !== 'mapsmarkerpro_support') {
			return;
		}

		$this->load_global_resources($hook);
	}

	/**
	 * Shows the support page
	 *
	 * @since 4.0
	 */
	protected function show() {
		?>
		<div class="wrap mmp-wrap">
			<h1><?= esc_html__('Support', 'mmp') ?></h1>
			<div class="mmp-main">
				<p>
					<?= sprintf(esc_html__('Before you post a new support ticket, please follow the instructions on %1$s for a guideline on how to deal with the most common issues.', 'mmp'), '<a href="https://www.mapsmarker.com/readme-first/" target="_blank">https://www.mapsmarker.com/readme-first/</a>') ?>
				</p>
				<h2><?= esc_html__('Translations', 'mmp') ?></h2>
				<p>
					<?= sprintf(esc_html__('Adding a new translation or updating an existing one is quite easy - please visit %s for more information.', 'mmp'), '<a href="https://translate.mapsmarker.com/" target="_blank">https://translate.mapsmarker.com/</a>') ?>
				</p>
				<ul class="mmp-support-list">
					<li>Afrikaans (af) thanks to <a href="http://bmarksa.org/nuus/" target="_blank">Hans</a></li>
					<li>Arabic (ar) thanks to Abdelouali Benkheil, Aladdin Alhamda, <a href="http://arabhosters.com" target="_blank">Nedal Elghamry</a>, yassin, <a href="http://www.benkh.be" target="_blank">Abdelouali Benkheil</a></li>
					<li>Bengali (ba_BD) thanks to <a href="http://www.answersbd.com" target="_blank">Nur Hasan</a></li>
					<li>Bosnian (bs_BA) thanks to <a href="http://dkenan.com" target="_blank">Kenan Dervišević</a></li>
					<li>Bulgarian (bg_BG) thanks to <a href="http://coffebreak.info" target="_blank">Andon Ivanov</a></li>
					<li>Catalan (ca) thanks to <a href="https://www.rocjumper.com" target="_blank">Roc</a>, <a href="http://vcubells.net" target="_blank">Vicent Cubells</a>, Efraim Bayarri, <a href="http://www.martika.es" target="_blank">Marta Espinalt</a></li>
					<li>Chinese (zh_CN) thanks to <a href="http://www.synyan.net" target="_blank">John Shen</a>, ck</li>
					<li>Chinese (zh_TW) thanks to <a href="http://outdooraccident.org" target="_blank">jamesho Ho</a></li>
					<li>Croatian (hr) thanks to <a href="http://www.airsoft-hrvatska.com" target="_blank">Neven Pausic</a>, Alan Benic, <a href="http://www.proprint.hr" target="_blank">Marijan Rajic</a></li>
					<li>Czech (cs_CZ) thanks to <a href="https://feeltrees.com" target="_blank">Tomáš Roštejnský</a>, Viktor Kleiner, <a href="http://kuzbici.eu" target="_blank">Vlad Kuzba</a></li>
					<li>Danish (da_DK) thanks to <a href="http://markaabo.dk" target="_blank">Mark Aabo Pedersen</a>, Mads Dyrmann Larsen, <a href="http://24-7news.dk" target="_blank">Peter Erfurt</a></li>
					<li>Dutch (nl_NL) thanks to <a href="www.ronaldsmeets.info" target="_blank">Ronald Smeets</a>, <a href="http://www.mergenmetz.nl" target="_blank">Marijke Metz</a>, Patrick Ruers, <a href="http://wandelenrondroden.nl" target="_blank">Fokko van der Leest</a>, <a href="http://www.wonderline.nl" target="_blank">Hans Temming</a></li>
					<li>English (en_US)</li>
					<li>Esperanto (eo) thanks to <a href="http://esperanto.pl " target="_blank">Kamil Getka</a></li>
					<li>Finnish (fi_FI) thanks to <a href="https://twitter.com/doaudit" target="_blank">Jari Harjus</a>, <a href="https://twitter.com/jessibjork" target="_blank">Jessi Björk</a></li>
					<li>French (fr_FR) thanks to <a href="http://www.faceaufleuve.fr" target="_blank">FaceAuFleuve</a>, <a href="http://www.skivr.com" target="_blank">Vincèn Pujol</a>, <a href="http://rodolphe.quiedeville.org" target="_blank">Rodolphe Quiedeville</a>, Fx Benard, <a href="http://www.cedric-cazal.com" target="_blank">cazal cédric</a>, <a href="http://hurelle.fr" target="_blank">Fabian Hurelle</a>, <a href="http://news.timtom.ch" target="_blank">Thomas Guignard</a></li>
					<li>Galician (gl_ES) thanks to <a href="http://www.indicepublicidad.com" target="_blank">Fernando Coello</a>, <a href="https://acorunhaliteraria.gal/" target="_blank">Jorge Castro Ruso</a></li>
					<li>German (de_DE)</li>
					<li>Greek (el) thanks to <a href="https://dialpa.org/" target="_blank">Charalampos Konstantopoulos</a>, <a href="http://www.mapdow.com" target="_blank">Philios Sazeides</a>, Evangelos Athanasiadis, <a href="http://avakon.com" target="_blank">Vardis Vavoulakis</a></li>
					<li>Hebrew (he_IL) thanks to <a href="http://pluto2go.co.il" target="_blank">Alon Gilad</a>, kobi levi</li>
					<li>Hindi (hi_IN) thanks to <a href="https://vidyut.net/" target="_blank">Vidyut</a>, <a href="http://outshinesolutions.com" target="_blank">Outshine Solutions</a>, <a href="http://indlinux.org" target="_blank">Guntupalli Karunakar</a></li>
					<li>Hungarian (hu_HU) thanks to <a href="http://www.logicit.hu" target="_blank">István Pintér</a>, <a href="http://www.foto-dvd.hu" target="_blank">Csaba Orban</a></li>
					<li>Indonesian (id_ID) thanks to Andy Aditya Sastrawikarta, <a href="http://whateverisaid.wordpress.com" target="_blank">Emir Hartato</a>, <a href="http://www.dedoho.pw/" target="_blank">Phibu Reza</a></li>
					<li>Italian (it_IT) thanks to <a href="mailto:lucabarbetti@gmail.com">Luca Barbetti</a>, <a href="https://www.mappeattive.com" target="_blank">Angelo Giammarresi</a></li>
					<li>Japanese (ja) thanks to <a href="http://twitter.com/higa4" target="_blank">Shu Higashi</a>, Taisuke Shimamoto</li>
					<li>Korean (ko_KR) thanks to <a href="http://wcpadventure.com" target="_blank">Andy Park</a></li>
					<li>Latvian (lv) thanks to <a href="http://lbpa.lv" target="_blank">Juris Orlovs</a>, Eriks Remess</li>
					<li>Lithuanian (lt_LT) thanks to <a href="http://www.transleta.co.uk" target="_blank">Donatas Liaudaitis</a>, <a href="http://www.manokarkle.lt" target="_blank">Ovidijus</a></li>
					<li>Malay (ms_MY) thanks to <a href="http://www.caridestinasi.com/" target="_blank">Mohd Zulkifli</a></li>
					<li>Norwegian (nb_NO) thanks to <a href="http://ingetang.com" target="_blank">Inge Tang</a></li>
					<li>Polish (pl_PL) thanks to <a href="https://commonwombat.pl/en/" target="_blank">Maciej Tkaczyk</a>, <a href="http://injit.pl" target="_blank">Pawel Wyszyński</a>, <a href="http://www.kochambieszczady.pl" target="_blank">Tomasz Rudnicki</a>, Robert Pawlak, <a href="http://mojelodzkie.pl" target="_blank">Daniel</a>, Paul Dworniak</li>
					<li>Portuguese (pt_BR) thanks to <a href="http://www.bibliomaps.com" target="_blank">Fabio Bianchi</a>, <a href="http://pelaeuropa.com.br" target="_blank">Andre Santos</a>, Antonio Hammerl</li>
					<li>Portuguese (pt_PT) thanks to <a href="http://www.all-about-portugal.com" target="_blank">Joao Campos</a></li>
					<li>Punjabi (pa) thanks to <a href="https://www.janbasktraining.com" target="_blank">Vikas Arora</a></li>
					<li>Romanian (ro_RO) thanks to <a href="http://administrare-cantine.ro" target="_blank">Arian</a>, <a href="http://www.inadcod.com" target="_blank">Daniel Codrea</a>, <a href="http://www.inboxtranslation.com" target="_blank">Flo Bejgu</a>, <a href="https://jurnalmontan.ro/" target="_blank">Tosa Razvan</a></li>
					<li>Russian (ru_RU) thanks to <a href="http://te-st.ru" target="_blank">Ekaterina Golubina (supported by Teplitsa of Social Technologies)</a>, <a href="http://slavblog.ru" target="_blank">Vyacheslav Strenadko</a></li>
					<li>Serbian (sr_RS) thanks to Radomir Vukobrat</a></li>
					<li>Slovak (sk_SK) thanks to Zdenko Podobny</a></li>
					<li>Slovenian (sl_SL) thanks to <a href="http://www.geocacher.si" target="_blank">Igor Čabrian</a></li>
					<li>Spanish (es_ES) thanks to <a href="http://www.hiperterminal.com" target="_blank">David Ramírez</a>, <a href="http://www.alvarolara.com" target="_blank">Alvaro Lara</a>, <a href="http://www.labviteri.com" target="_blank">Ricardo Viteri</a>, Juan Valdes, <a href="http://www.martika.es" target="_blank">Marta Espinalt</a>, <a href="http://www.indicepublicidad.com" target="_blank">Fernando Coello</a></li>
					<li>Spanish (es_MX) thanks to <a href="http://1sistemas.net" target="_blank">Victor Guevera</a>, Eze Lazcano</li>
					<li>Swahili (sw) thanks to <a href="https://info.demographyproject.org/" target="_blank" rel="noopener">Richard Muraya</a></li>
					<li>Swedish (sv_SE) thanks to Olof Odier, Tedy Warsitha, <a href="http://www.paulsson.eu" target="_blank">Dan Paulsson</a>, <a href="https://rundvandra.se/" target="_blank">Elger Lindgren</a>, <a href="http://andreasson.org/" target="_blank">Anton Andreasson</a>, <a href="https://www.dumsnal.se/" target="_blank">Tony Lygnersjö</a></li>
					<li>Thai (th) thanks to Makarapong Chathamma, <a href="http://siteprogroup.com/" target="_blank">Panupong Siriwichayakul</a></li>
					<li>Turkish (tr_TR) thanks to <a href="http://www.karalamalar.net" target="_blank">Emre Erkan</a>, <a href="http://www.bozukpusula.com" target="_blank">Mahir Tosun</a>, Cagatay Demir</li>
					<li>Uighur (ug) thanks to <a href="http://ug.wordpress.org/" target="_blank">Yidayet Begzad</a></li>
					<li>Ukrainian (uk_UK) thanks to <a href="http://terebotg.in.ua" target="_blank">Yaroslav B Yaroshevskyy</a>, <a href="http://all3d.com.ua" target="_blank">Andrexj</a>, <a href="http://zhitya.com" target="_blank">Sergey Zhitnitsky</a>, <a href="http://imgsplanet.com" target="_blank">Mykhailo</a></li>
					<li>Vietnamese (vi) thanks to <a href="http://bizover.net" target="_blank">Hoai Thu</a></li>
					<li>Yiddish (yi) thanks to <a href="http://www.cs.uky.edu/~raphael/yiddish.html" target="_blank">Raphael Finkel</a></li>
				</ul>
				<h2><?= esc_html__('Libraries, services and images', 'mmp') ?></h2>
				<ul class="mmp-support-list">
					<li><a href="https://www.leafletjs.com" target="_blank">Leaflet</a> by Vladimir Agafonkin</li>
					<li><a href="https://github.com/Leaflet/Leaflet.markercluster" target="_blank">Marker Clustering plugin</a> by David Leaver</li>
					<li><a href="https://gitlab.com/IvanSanchez/Leaflet.GridLayer.GoogleMutant" target="_blank">Google Maps plugin</a> by Iván Sánchez Ortega</li>
					<li><a href="https://github.com/digidem/leaflet-bing-layer" target="_blank">Bing Maps plugin</a> by Digital Democracy</li>
					<li><a href="https://esri.github.io/esri-leaflet/" target="_blank">Esri plugin</a> by Esri</li>
					<li><a href="https://github.com/TolonUK/Leaflet.EdgeBuffer" target="_blank">Edge buffer plugin</a> by Alex Paterson</li>
					<li><a href="https://github.com/Norkart/Leaflet-MiniMap" target="_blank">MiniMap plugin</a> by Norkart</li>
					<li><a href="https://github.com/mapbox/Leaflet.fullscreen" target="_blank">Fullscreen plugin</a> by MapBox</li>
					<li><a href="https://github.com/domoritz/leaflet-locatecontrol" target="_blank">Locate plugin</a> by Dominik Moritz</li>
					<li><a href="https://github.com/elmarquis/Leaflet.GestureHandling" target="_blank">Gesture handling plugin</a> by elmarquis</li>
					<li><a href="https://github.com/geoman-io/leaflet-geoman" target="_blank">Geometry plugin</a> by Sumit Kumar</li>
					<li><a href="https://www.chartjs.org" target="_blank">Chart.js</a> by Chart.js Contributors</li>
					<li><a href="https://github.com/TarekRaafat/autoComplete.js" target="_blank">autoComplete.js</a> by Tarek Raafat</li>
					<li><a href="https://select2.org" target="_blank">Select2</a> by Kevin Brown, Igor Vaynberg, and Select2 contributors</li>
					<li><a href="https://bgrins.github.io/spectrum/" target="_blank">Spectrum</a> by Brian Grinstead</li>
					<li><a href="https://flatpickr.js.org" target="_blank">flatpickr</a> by Gregory Petrosyan</li>
					<li><a href="https://date-fns.org" target="_blank">date-fns</a> by Sasha Koss</li>
					<li><a href="https://github.com/kazuhikoarase/qrcode-generator" target="_blank">QR-Code generator</a> by Kazuhiko Arase</li>
					<li><a href="https://mapicons.mapsmarker.com" target="_blank">Map Icons Collection</a> by Nicolas Mollet</li>
					<li><a href="https://tobiasahlin.com/spinkit/" target="_blank">Map loading animation</a> by Tobias Ahlin</li>
					<li>JSON & language icons by <a href="https://p.yusukekamiyamane.com" target="_blank">Yusuke Kamiyamane</a></li>
					<li>Map edit icon by <a href="https://www.fatcow.com" target="_blank">FatCow Web Hosting</a></li>
					<li>Fullscreen icons by <a href="https://pjonori.com" target="_blank">P.J. Onori</a></li>
					<li>Home icon by <a href="https://www.flaticon.com/authors/dave-gandy" target="_blank">Dave Gandy</a></li>
					<li>Pin icon by <a href="https://www.flaticon.com/authors/those-icons" target="_blank">Those Icons</a></li>
				</ul>
				<h2><?= esc_html__('Trademarks and copyright', 'mmp') ?></h2>
				<p>
					MapsMarker<sup>&reg;</sup><br />
					Copyright &copy; 2011-<?= gmdate('Y') ?>, MapsMarker.com e.U., All Rights Reserved
				</p>
				<img style="width: 400px;" src="<?= plugins_url('images/mmp-logo.svg', MMP::$path) ?>" />
			</div>
		</div>
		<?php
	}
}

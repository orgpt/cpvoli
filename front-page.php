<?php
/**
 * Front page template.
 *
 * @package ConfrontaPrezziVoli
 */

get_header();

$airports = CPV_Data::get_priority_airports();
$airlines = CPV_Data::get_priority_airlines();
$cities   = array_filter(
	array(
		CPV_Data::get_city_by_slug( 'londra' ),
		CPV_Data::get_city_by_slug( 'parigi' ),
		CPV_Data::get_city_by_slug( 'barcellona' ),
		CPV_Data::get_city_by_slug( 'dubai' ),
	)
);
$vs_left  = CPV_Data::get_airline_by_slug( 'ryanair' );
$vs_right = CPV_Data::get_airline_by_slug( 'wizz-air' );
?>
<section class="hero">
	<div class="shell">
		<div>
			<p class="eyebrow">ConfrontaPrezziVoli.it</p>
			<h2 class="hero__title">Confronta prezzi voli e trova voli in offerta.</h2>
			<p class="hero__lede">Confronta centinaia di offerte per prenotare i biglietti aerei al prezzo piu basso. Il comparatore di voli che stavi cercando per partire dall'Italia in modo semplice, veloce e conveniente.</p>
			<div class="hero__chips">
				<span class="chip">Non-stop</span>
				<span class="chip">Sotto EUR 50</span>
				<span class="chip">Partenza mattina</span>
				<span class="chip">Alert prezzo</span>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="shell">
		<div class="section__intro">
			<p class="eyebrow">Offerte in evidenza</p>
			<h2>Banner ispirazionali per scoprire rotte, city break e partenze strategiche dall'Italia.</h2>
		</div>
		<div class="banner-grid">
			<a class="banner-card" href="<?php echo esc_url( home_url( '/voli/milano-malpensa-airport-a-parigi/' ) ); ?>">
				<img src="<?php echo esc_url( CPV_THEME_URL . '/assets/images/banner-milano-parigi.svg' ); ?>" alt="Offerte voli da Milano a Parigi">
			</a>
			<a class="banner-card" href="<?php echo esc_url( home_url( '/voli/leonardo-da-vinci-fiumicino-airport-a-barcellona/' ) ); ?>">
				<img src="<?php echo esc_url( CPV_THEME_URL . '/assets/images/banner-roma-barcellona.svg' ); ?>" alt="Offerte voli da Roma a Barcellona">
			</a>
			<a class="banner-card" href="<?php echo esc_url( home_url( '/voli/naples-international-airport-a-dubai/' ) ); ?>">
				<img src="<?php echo esc_url( CPV_THEME_URL . '/assets/images/banner-napoli-dubai.svg' ); ?>" alt="Offerte voli da Napoli a Dubai">
			</a>
		</div>
	</div>
</section>

<section class="section">
	<div class="shell">
		<div class="section__intro">
			<p class="eyebrow">Confronta Voli</p>
			<h2>Esplora le principali offerte di biglietti aerei per trovare la soluzione piu conveniente per il tuo viaggio.</h2>
			<p class="hero__lede">Siamo un motore di ricerca per voli che analizza le principali compagnie aeree e i piu importanti fornitori di viaggi online. Ti permettiamo di confrontare facilmente tariffe aeree e costi di viaggio in un'unica piattaforma, per poi prenotare direttamente con il fornitore scelto.</p>
		</div>
		<div class="card-grid">
			<?php foreach ( $airports as $airport ) : ?>
				<a class="card card--link" href="<?php echo esc_url( home_url( '/voli-da/' . $airport['slug'] . '/' ) ); ?>">
					<p class="card__code"><?php echo esc_html( $airport['code'] ); ?></p>
					<h3><?php echo esc_html( $airport['display_name'] ); ?></h3>
					<p>Confronta offerte, controlla le rotte piu richieste e scopri le migliori opportunita di partenza da questo aeroporto.</p>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section section--tinted">
	<div class="shell">
		<div class="section__intro">
			<p class="eyebrow">Volo</p>
			<h2>Prenota i tuoi biglietti aerei a prezzi convenienti, semplicemente e rapidamente.</h2>
			<p class="hero__lede">Risparmia tempo e denaro scegliendo il volo piu veloce o l'offerta piu vantaggiosa, con una ricerca chiara e strumenti utili per trovare la tariffa giusta al momento giusto.</p>
		</div>
		<div class="results-shell">
			<div class="pill-row">
				<span class="pill is-active">Non-stop</span>
				<span class="pill">Sotto EUR 50</span>
				<span class="pill">Weekend</span>
				<span class="pill">Bagaglio incluso</span>
			</div>
			<div class="calendar-grid">
				<?php foreach ( CPV_Data::build_price_calendar( 'FCO', 'LON' ) as $day ) : ?>
					<div class="calendar-cell<?php echo $day['is_lowest'] ? ' is-lowest' : ''; ?>">
						<strong><?php echo esc_html( $day['day'] ); ?></strong>
						<span><?php echo esc_html( CPV_Data::format_price( $day['price'] ) ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="shell">
		<div class="triple-grid">
			<div class="panel">
				<h3>Confronta</h3>
				<p>Siamo un motore di ricerca per voli che esamina compagnie aeree e fornitori di viaggio online. Una volta trovata l'opzione migliore, puoi prenotare direttamente con il fornitore in pochi passaggi.</p>
			</div>
			<div class="panel">
				<h3>Low Cost</h3>
				<p>Dopo aver trovato il miglior biglietto aereo con noi, verrai reindirizzato alla compagnia aerea o al fornitore di viaggi per completare la prenotazione e personalizzare le opzioni del volo.</p>
			</div>
			<div class="panel">
				<h3>Compagnie piu cercate</h3>
				<?php foreach ( $airlines as $airline ) : ?>
					<a class="inline-link" href="<?php echo esc_url( home_url( '/compagnie-aeree/' . $airline['slug'] . '/' ) ); ?>"><?php echo esc_html( $airline['display_name'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="shell">
		<div class="section__intro">
			<p class="eyebrow">Voli per citta</p>
			<h2>Scopri alcune delle destinazioni piu cercate e confronta le migliori offerte in partenza dall'Italia.</h2>
		</div>
		<div class="triple-grid">
			<?php foreach ( $cities as $city ) : ?>
				<div class="panel">
					<h3>Voli per <?php echo esc_html( $city['display_name'] ); ?></h3>
					<p>Consulta le tratte piu richieste, confronta le compagnie disponibili e individua il periodo migliore per prenotare.</p>
					<a class="button button--secondary" href="<?php echo esc_url( home_url( '/voli-per/' . $city['slug'] . '/' ) ); ?>">Scopri le offerte</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="shell">
		<div class="section__intro">
			<p class="eyebrow">La Nostra Storia</p>
			<h2>ConfrontaPrezziVoli vanta un'esperienza pluriennale nel settore dei viaggi aerei.</h2>
			<p class="hero__lede">Offriamo un servizio affidabile e conveniente per chi cerca voli nazionali e internazionali, con l'obiettivo di individuare le migliori offerte e tariffe disponibili sul mercato.</p>
		</div>
		<div class="story-grid">
			<div class="panel">
				<h3>Scopri di piu</h3>
				<p>Confrontiamo compagnie aeree, agenzie online e fornitori di viaggio per aiutarti a partire con maggiore controllo sul budget e con una panoramica chiara delle alternative disponibili.</p>
				<a class="button button--secondary" href="<?php echo esc_url( ( $vs_left && $vs_right ) ? home_url( '/confronto/' . $vs_left['slug'] . '-vs-' . $vs_right['slug'] . '/' ) : home_url( '/' ) ); ?>">Vedi confronto compagnie</a>
			</div>
			<blockquote class="testimonial-card">
				<p>"Confronta Voli mi ha aiutato a risparmiare tempo e denaro nelle mie vacanze. Servizio impeccabile!"</p>
				<cite>Alex, Confronto Prezzi Volo</cite>
			</blockquote>
		</div>
	</div>
</section>

<section class="section section--tinted">
	<div class="shell">
		<div class="section__intro">
			<p class="eyebrow">Il Nostro Valore Unico</p>
			<h2>Un comparatore di voli pratico e facile per prenotare i tuoi biglietti aerei e risparmiare.</h2>
			<p class="hero__lede">Puoi confrontare centinaia di offerte di compagnie aeree online per trovare la soluzione migliore per la tua destinazione preferita. I filtri disponibili ti aiutano a personalizzare la ricerca in pochi clic.</p>
		</div>
		<div class="triple-grid">
			<div class="panel">
				<h3>Confronto Veloce</h3>
				<p>Trova rapidamente le migliori offerte e le tariffe piu convenienti. I risultati possono essere valutati in base al prezzo, alla durata del tragitto e alla comodita del volo.</p>
			</div>
			<div class="panel">
				<h3>Voli Economici</h3>
				<p>Per una ricerca ancora piu personalizzata, puoi usare filtri come senza scali, aeroporto di partenza preferito o compagnie da escludere, cosi da individuare il volo giusto in pochi minuti.</p>
			</div>
			<div class="panel">
				<h3>Prenotazione Flessibile</h3>
				<p>Dopo aver trovato l'offerta ideale, puoi completare la prenotazione aggiungendo servizi extra come bagaglio registrato, scelta del posto o assicurazione di viaggio.</p>
			</div>
		</div>
	</div>
</section>
<?php
get_footer();

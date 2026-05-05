# ConfrontaPrezziVoli WordPress Theme

Tema WordPress orientato a un modello flight-only con focus sulle partenze dagli aeroporti italiani e una struttura pSEO pronta a scalare.

## URL dinamici inclusi

- `/voli-da/{airport-slug}` per hub aeroporti italiani
- `/voli/{origin}-a-{destination}` per route pages
- `/voli-per/{city-slug}` per city hubs
- `/compagnie-aeree/{airline-slug}` per guide compagnia
- `/confronto/{airline-a}-vs-{airline-b}` per pagine comparative

## Dati usati

Il tema legge direttamente:

- `C:\xampp\htdocs\cpvoli\airports.json`
- `C:\xampp\htdocs\cpvoli\cities.json`
- `C:\xampp\htdocs\cpvoli\airlines.json`

## Note tecniche

- I prezzi, le durate e la disponibilita diretta sono generati con logica sintetica stabile, cosi il tema e navigabile subito anche senza API esterne.
- La UI e mobile-first, con hero search glassmorphism, chip filters, skeleton loading e prezzo in Euro.
- I metadata sono italianizzati e modellati per SEO dinamica.

## Prossimi step consigliati

1. Collegare il layer prezzi e timetable a un provider API voli o Amadeus.
2. Salvare alert e watchlists in custom tables o via plugin dedicato.
3. Aggiungere generation pipelines per creare internal linking massivo fra hub, rotte e guide.
4. Flush delle rewrite rules dopo l'attivazione del tema andando su `Impostazioni > Permalink` e salvando una volta.

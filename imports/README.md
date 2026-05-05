# Import Pack SEO

Questa cartella contiene:

- `categories/categories.csv` da importare per prima
- batch CSV per aeroporti, citta e compagnie
- `manifest.json` con conteggi e percorsi

## Ordine consigliato

1. Importa `categories/categories.csv`
2. Importa i batch aeroporti (`1` file)
3. Importa i batch citta (`36` file)
4. Importa i batch compagnie (`12` file)

## Formato campi principali

- `post_title`
- `post_name`
- `post_excerpt`
- `post_content`
- `post_category`
- `rank_math_title`
- `rank_math_description`
- `rank_math_focus_keyword`
- campi `cpv_*` per struttura e metadati interni

## Rigenerazione

Esegui:

```powershell
php tools/generate-import-batches.php
```
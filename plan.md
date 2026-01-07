# RFC: UA Les Module v2.2.0 — Ledenbeheer & Offline Seed + Modern Design

**Status:** Accepted  
**Datum:** 2026-01-07  
**Versie:** 1.0  
**Auteur:** Uno Animo (Paul Font Freide)

## 1. Samenvatting
Deze RFC beschrijft twee uitbreidingen voor UA Les Module:
1) **Ledenbeheer** (leiding & assistenten) rechtstreeks in het **Overzicht** met volledige CRUD.  
2) **Offline seed** op basis van een **meegeleverd JSON-bestand** om lessen/locaties/leiding te importeren zonder externe afhankelijkheden.  
Daarnaast wordt expliciet vastgelegd dat de UI een **modern design** moet gebruiken: responsief, toegankelijk (WCAG 2.1 AA), consistente typografie en duidelijke call‑to‑actions.

## 2. Doelstellingen
- **Gebruiksgemak:** Trainers/beheerders kunnen snel leiding en assistenten beheren.  
- **Betrouwbaarheid:** Seed-gegevens staan lokaal in de plugin en werken zonder internet.  
- **Professionele look & feel:** Modern, consistent en toegankelijk admin‑UI.

## 3. Scope
- Uitbreiding **Overzicht**: leden toevoegen, wijzigen, verwijderen.  
- Uitbreiding **Onderhoud**: seed‑import vanuit `assets/data/lesrooster.json` met **dry‑run**.  
- UI‑styling: moderne admin‑componenten en optioneel een lichte eigen CSS (`assets/css/admin.css`).

**Niet in scope:** uitgebreid zoeken/filteren, rollenmatrix per les, of externe API‑koppelingen.

## 4. Functionele eisen
- **Ledenbeheer**: 
  - Toevoegen: naam, flags `is_hoofd`, `is_assist`, `active`.  
  - Wijzigen/verwijderen: inline per rij met confirm‑dialoog voor verwijderen.  
- **Seed**: 
  - Import leest JSON en voert idempotent insert/update uit op Locaties, Leden (leiding) en Lessen.  
  - Dry‑run toont aantal herkende regels en geen writes.  
- **Modern design**: 
  - Gebruik WordPress admin‑patterns (buttons `.button`, tabellen `.widefat`).  
  - Consistente spacing en typografie, duidelijke calls‑to‑action, responsief.  
  - Toets op **WCAG 2.1 AA** (contrast, focus states, labels).  

## 5. Niet‑functionele eisen
- **Security:** Nonces op alle formulieren; capability checks; sanitization & escaping.  
- **Performance:** Seed‑import ≤ 2s voor ~200 items op normale hosting.  
- **Compatibiliteit:** WordPress 6.1+; PHP 7.4+.  

## 6. Architectuur & Data
### 6.1 Tabellen
- `ua_lm_members (name, is_hoofd, is_assist, active)`  
- `ua_lm_locations (name, hall, notes, active)`  
- `ua_lm_lessons (title, dow, start, end, location_id|location, default_hoofd_id, default_assist_id, active)`

### 6.2 Seed JSON (bundled)
Bestand: `assets/data/lesrooster.json` — lijst van objecten met velden:
```json
[
  {
    "dow": 1,
    "start": "17:30",
    "end": "20:30",
    "title": "Turnen wedstrijd",
    "leiding": "Marieke",
    "location": "Sporthal de Weijer zaal rechts"
  }
]
```
**Validatie:**
- `dow`: integer 1–7  
- `start`/`end`: `HH:MM`  
- `title`, `leiding`, `location`: non‑empty strings

## 7. UI Ontwerp (Modern Design – Verplicht)
- **Principes:** eenvoudig, duidelijk, responsief, consistent.  
- **Componenten:**  
  - Forms met duidelijke labels en helpteksten.  
  - Primaire acties als **Primary button**; destructive acties als **link‑delete** met confirm.  
- **Toegankelijkheid:** 
  - Focus‑stijlen zichtbaar; ARIA‑labels waar nodig.  
- **CSS:** optioneel `assets/css/admin.css` voor spacing/kleuren bovenop WP‑stijlen.

## 8. Implementatieplan (Stappen)
### Stap A — Seed (bundled file)
1. Voeg `assets/data/lesrooster.json` toe (voorbeelddata).  
2. Implementeer `UA_LM_Seeder::seed_from_file($path, $dryRun)`.
3. Admin‑handler `UA_LM_Admin::handle_seed_file()` met nonce `ua_lm_seed_file`.  
4. UI in **Onderhoud**: formulier *Bundled seed importeren* + checkbox **Dry‑run**.  
5. Success/error‑notices met aantallen: `count`, `added`, `updated`.

### Stap B — Ledenbeheer in Overzicht
1. Overzicht: sectie **Leiding & assistenten opvoeren** (naam + 3 checkboxes).  
2. Lijst weergave: tabel met knoppen **Wijzig** (inline) en **Verwijderen**.  
3. Handlers: `ua_lm_member_add`, `ua_lm_member_update`, `ua_lm_member_delete` (+ nonces).  
4. Server‑side: sanitize/escape; capability `manage_options`.

### Stap C — Modern Design toepassen
1. Pas componenten aan: gebruik `.button`, `.button-primary`, `.widefat`.  
2. Voeg `assets/css/admin.css` toe voor spacing en focus states.  
3. Controleer contrast en toetsenbordnavigatie (WCAG 2.1 AA).  

### Stap D — Beveiliging & QA
1. Voeg **nonces** en **check_admin_referer** toe aan alle formulieren.  
2. Unit/integration tests waar mogelijk; handmatige tests voor CRUD & seed.  
3. Test migratie vanaf v2.1.x (zonder dataverlies).

## 9. Acceptatiecriteria
- CRUD voor leden werkt en toont server‑notices bij acties.  
- Seed vanuit JSON werkt (dry‑run + echte import) met correcte aantallen.  
- UI voldoet aan moderne, toegankelijke designrichtlijnen.

## 10. Uitrol
- Bump versie naar **2.2.0**.  
- Changelog bijwerken; ZIP publiceren.  
- Communicatie naar trainers/beheerders met korte handleiding.

## 11. Risico’s & Mitigatie
- **Layoutwijzigingen** in seed‑bron: bundling voorkomt afhankelijkheid; JSON is aanpasbaar.  
- **Dataconsistentie**: idempotente upsert‑logica; dry‑run vooraf.  
- **Toegankelijkheid**: checklist uitvoeren (focus, labels, contrast).

## 12. Bijlagen
- Voorbeeld JSON  
- Screensketches (optioneel; te leveren in een aparte RFC‑bijlage)

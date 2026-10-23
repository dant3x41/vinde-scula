# Audit și clean — Vinde Scula 1.3.89

Data: 23 octombrie 2026. Sursa analizată este arhiva furnizată de utilizator, nu versiunea 1.0.0 din repository. Auditul a inclus codul PHP/JavaScript, verificarea permisiunilor WordPress, teste HTTP pe înregistrări temporare și reproduceri în Chromium.

## Rezultatul principal

Au fost reproduse **o problemă P1 și trei probleme P2**. Mai jos sunt separate patru riscuri de concurență identificate în cod, care nu au fost reproduse prin cereri paralele. Arhiva clean nu remediază aceste defecte.

| ID | Prioritate | Problemă | Dovezi |
|---|---|---|---|
| A1 | P1 — ridicată | Un vânzător cu rol Contributor/Author poate folosi operațiunile personalului pentru propria solicitare | Reprodus HTTP cu rol Contributor |
| A2 | P2 — medie | Acceptarea manuală poate bloca definitiv fluxul normal al vânzării | Reprodus: acceptare 200; finalizare, răspuns și ridicare 409 |
| A3 | P2 — medie | Filele duplicate pot amesteca fotografiile ciornelor între produse | Reprodus în Chromium |
| A4 | P2 — medie | Shortcode-ul fără carduri/categorie preselectată rămâne invizibil pentru vizitatori | Reprodus în Chromium |

### A1. Permisiunile de blog permit operațiuni comerciale rezervate personalului

Referință principală: [vinde-scula-categorii.php:3080](/workspace/.cloud-env/vinde-scula/sources/1.3.89/vinde-scula-categorii/vinde-scula-categorii.php:3080). Tipul de postare folosește capabilitățile generice `post`, iar panoul furnizează nonce-ul administrativ oricui poate edita solicitarea, la [linia 3353](/workspace/.cloud-env/vinde-scula/sources/1.3.89/vinde-scula-categorii/vinde-scula-categorii.php:3353).

Un Contributor care deține o solicitare privată are `edit_post=true`, deși `manage_options=false` și `manage_woocommerce=false`. În test, acesta a deschis pagina reală de editare, a obținut nonce-ul și a trimis oferta de 765,43 lei pentru propria solicitare. Endpointul a răspuns 200/success și a salvat starea `oferta_trimisa`.

Condiție: vânzătorul are și permisiuni de autor/contributor în WordPress. Rolul WooCommerce Customer obișnuit nu primește singur această permisiune. Pluginul nu transferă bani automat; marcarea plății și dezvăluirea IBAN-ului au verificări administrative suplimentare.

**Corecție recomandată:** capabilități distincte pentru solicitări și o verificare explicită a rolului personalului în endpointurile administrative și în panoul care expune nonce-ul. Accesul proprietarului din cont trebuie păstrat separat.

### A2. Acceptarea manuală lasă metadatele într-o stare incompatibilă

Referințe: [tranziția permisă, linia 2960](/workspace/.cloud-env/vinde-scula/sources/1.3.89/vinde-scula-categorii/vinde-scula-categorii.php:2960), [actualizarea administrativă, linia 3199](/workspace/.cloud-env/vinde-scula/sources/1.3.89/vinde-scula-categorii/vinde-scula-categorii.php:3199) și [condițiile finalizării, linia 2702](/workspace/.cloud-env/vinde-scula/sources/1.3.89/vinde-scula-categorii/vinde-scula-categorii.php:2702).

Acceptarea administrativă schimbă `_vsc_status` în `acceptata`, fără `_vsc_offer_reply=accept`. Proprietarul nu mai poate răspunde deoarece oferta nu mai este în așteptare, dar nici nu poate finaliza fără acel răspuns. Programarea ridicării cere tocmai finalizarea lipsă.

În test: acceptarea administrativă a reușit (200); finalizarea proprietarului, acceptarea proprietarului și programarea ridicării au fost toate respinse cu 409. Metadatele au confirmat `status=acceptata`, `reply=""`.

**Corecție recomandată:** eliminarea acceptării manuale pentru solicitările cu proprietar sau unificarea acceptării într-o singură operațiune care salvează coerent toate metadatele, autorul și istoricul.

### A3. Fotografii comune pentru ciorne din file duplicate

Referințe: [tokenul filei, frontend.js:222](/workspace/.cloud-env/vinde-scula/sources/1.3.89/vinde-scula-categorii/assets/frontend.js:222), [cheia IndexedDB, linia 323](/workspace/.cloud-env/vinde-scula/sources/1.3.89/vinde-scula-categorii/assets/frontend.js:323) și [restaurarea, linia 366](/workspace/.cloud-env/vinde-scula/sources/1.3.89/vinde-scula-categorii/assets/frontend.js:366).

La deschiderea unei file cu opener, browserul poate clona `sessionStorage`, inclusiv identificatorul fotografiilor. Textele ciornelor devin apoi independente, dar fotografiile folosesc aceeași înregistrare IndexedDB.

Reproducere: fila A are modelul „First product” și fotografia `first-product.webp`; fila B moștenește stocarea, schimbă produsul și fotografia. După reîncărcare, A păstrează modelul inițial și încarcă `second-product.webp`. Nu a fost trimis formularul final.

**Corecție recomandată:** identificatori distincți pentru file active diferite, inclusiv după clonare, cu teste separate pentru duplicare și reîncărcarea aceleiași file.

### A4. Pagina fără carduri de categorii poate afișa o zonă goală

Referințe: [guest-gate.js:95](/workspace/.cloud-env/vinde-scula/sources/1.3.89/vinde-scula-categorii/assets/guest-gate.js:95) și [randarea ascunsă, PHP:1459](/workspace/.cloud-env/vinde-scula/sources/1.3.89/vinde-scula-categorii/vinde-scula-categorii.php:1459).

Cu numai `[vinde_scula]`, fără `categoria` în URL și fără carduri Elementor, panoul de autentificare rămâne ascuns. Chromium a confirmat `gateHidden=true`, `cards=0`, `visibleLogin=false`. Cu `categoria=scule-electrice`, panoul este vizibil.

Problema este condiționată de lipsa cardurilor și a categoriei preselectate; nu a fost demonstrată pentru pagina live cu carduri configurate.

**Corecție recomandată:** un panou/selector utilizabil când nu există carduri; comportamentul actual cu carduri trebuie păstrat. Până la corecție, demonstrația locală poate folosi o categorie validă preselectată.

## Riscuri de concurență identificate în cod

Prioritate propusă P2. Acestea sunt constatări statice, nu rezultate ale unor teste HTTP simultane. Serverul PHP local folosește un singur worker.

| ID | Risc | Referințe | Corecție recomandată |
|---|---|---|---|
| C1 | Două încărcări pot trece simultan limita zilnică/IP și pot scrie același contor final | PHP 2033–2045, 2149–2150 | Rezervare atomică înainte de procesare, eliberată la eșec |
| C2 | O ofertă întârziată de e-mail poate redeschide o solicitare retrasă sau suprascrie o ofertă acceptată | PHP 3245–3246, 3291–3299 | Serializarea operațiunii și tranziție condiționată atomică |
| C3 | O a doua finalizare poate reciti cache-ul vechi după obținerea lockului și suprascrie datele confirmate | PHP 2702–2708, 2764–2787 | Recitire după lock și revendicare atomică a finalizării |
| C4 | Prima tranziție poate crea două rânduri `_vsc_status`; `add_post_meta(..., true)` nu oferă o constrângere SQL unică | PHP 2106–2115, 2996–3008 | Inițializare coerentă și protecție atomică a tranzițiilor |

Detalii și intercalările necesare sunt în [auditul de autorizare](audit-1389-auth.md) și [auditul fișierelor](audit-1389-files.md).

## Alte observații

- README-ul original începe cu noutățile 1.3.78, deși codul este 1.3.89. Varianta clean corectează documentația și păstrează istoricul integral.
- Fotografiile istorice `_vsc_fotografii` din Media Library pot rămâne publice; este compatibilitate documentată. Nu există aici dovada că asemenea fotografii există pe site-ul live. Cele noi au trecut testele de acces privat.
- Nu există exportator/ștergător WordPress dedicat sau retenție automată pentru toate datele personale. Aceasta este o limită de operare, nu o breșă de acces demonstrată.
- Focusul la schimbarea pasului ajunge pe `BODY`; impactul pentru utilizatorii de tastatură nu a fost cuantificat și nu este inclus în cele patru defecte prioritizate.

## Clean efectuat și validare

Arhiva [vinde-scula-1.3.89-clean.zip](vinde-scula-1.3.89-clean.zip) este un rezultat separat, neactivat în WordPress:

1. Corpul `try` din `process_background` are indentare consecventă.
2. README-ul descrie versiunea și cerințele actuale; originalul este păstrat în `docs/HISTORY.txt`.
3. `CLEANUP.md` enumeră schimbările.
4. Cele 12 fișiere de active sunt identice cu originalul. Tokenurile PHP sunt identice, exceptând whitespace-ul; parserul PHP a trecut. Nu s-a schimbat logica și nu s-a schimbat versiunea.
5. ZIP-ul are 16 fișiere și a trecut verificarea CRC și compararea integrală cu varianta pregătită.
6. Au fost eliminate trei cache-uri Python generate de verificările locale, în total 43.530 bytes.

Arhiva originală, cele 14 fișiere ale sursei active, credentialele locale, datele existente și repository-ul au fost păstrate. Probele de autorizare au folosit un utilizator și o solicitare temporară, apoi au eliminat doar acele probe și e-mailurile lor locale. Starea inițială a solicitărilor a fost verificată după curățare.

Probe: [rezultatele HTTP](audit-auth-evidence.json), [scriptul reproducerii](audit-reproduce-auth.py), [manifestul clean](cleanup-manifest.json) și [auditul frontend](audit-1389-frontend.md).

Acoperirea include fluxurile descrise mai sus. Nu include hostingul live, CDN/cache, o bază de date de producție, livrarea reală de e-mail, un inventar CVE al dependențelor sau validarea tuturor intercalărilor concurente. Nu am identificat un IDOR direct între clienți, traversal de fișiere, DOM XSS ori un defect direct al AES-GCM în căile inspectate; aceasta nu echivalează cu o certificare de securitate.

Ordinea recomandată de remediere: A1 → A2 → A3/A4 → C1–C4. Defectele rămân prezente în varianta clean, deoarece aceasta este o curățare de formatare/documentație, nu o versiune cu remedieri funcționale.

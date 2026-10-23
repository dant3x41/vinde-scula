# Vinde Scula 1.4.1

Flux simplificat la cererea clientului: **o singură sculă per solicitare**, cu statisticile păstrate.

- Formularul trimite direct o solicitare individuală, cu maximum 5 fotografii.
- După confirmare, „Trimite altă sculă” deschide un formular nou. Solicitările aceluiași client rămân separate.
- Fiecare solicitare are propria ofertă, decizie, adresă de ridicare, curier/AWB, verificare și plată. Nu se grupează automat.
- Au fost eliminate coșul cu scule multiple, ciornele de expediție, API-ul de grupare și sincronizarea ridicărilor.
- Statisticile administrative păstrează numărul solicitărilor, ofertele, acceptările, achizițiile, conversiile, timpul până la prima ofertă, categoriile și filtrul după data primirii. Indicatorii pentru expediții au fost eliminați.
- Cererile și datele existente sunt păstrate; metadatele istorice de grupare nu influențează fluxul individual. Programarea vechii curățări de expediții este oprită, fără ștergerea înregistrărilor istorice.

Sunt păstrate fotografiile private, criptarea datelor bancare/de ridicare și corecțiile de acces pentru evaluatori. Un formular vechi cu grupare este respins cu cerere de reîncărcare a paginii, înainte de crearea unei solicitări.

## Verificări

- Sintaxă PHP și JavaScript, arhivă și concordanța versiunii.
- Două uploaduri reale de la același client: două solicitări și două unități din cotă; fără grupare.
- Ofertă, finalizare criptată, adresă, curier/AWB și stare independente, inclusiv cu metadate istorice identice.
- Statistici cu rezultate exacte, categorii, metadate duplicate, intervale de date, valori fără bază de calcul și acces restricționat.
- Browser desktop/mobil: formular, cinci fotografii, ciorne locale separate în file diferite, statistici, succes și formular nou. Erorile și succesul au fost simulate în browser; uploadurile și finalizarea au fost verificate separat prin HTTP real.
- Datele și mesajele de test sunt eliminate, iar limitele sunt restaurate.

Înainte de actualizare, fă backup al bazei de date și al folderului privat de fotografii. Păstrează cheile/salturile WordPress. Directorul pluginului rămâne `vinde-scula-categorii`. Versiunile originale și `main` sunt păstrate; această arhivă este livrată pe o ramură separată.

Validarea este locală, cu WordPress 7.1.3, PHP 8.3 și WooCommerce 11.2.1; integrarea cu tema și livrarea reală a e-mailurilor necesită verificare pe site. Pluginul nu generează AWB-uri și nu execută plăți bancare. Riscul vechi de concurență la trimiterea ofertei inițiale nu este remediat de această simplificare.

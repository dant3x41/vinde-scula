# Vinde Scula 1.4.0

Versiune pregătită separat din copia curățată 1.3.89, la cererea clientului: expediere cu mai multe scule și statistici. Maximum **3 scule într-o expediere**, cu cel mult 5 fotografii pentru fiecare sculă.

## Comportament

- „Adaugă încă o sculă” păstrează fiecare produs și fotografiile sale într-o expediere în pregătire. „Trimite” confirmă întreaga expediere. Eliminarea este permisă înainte de trimitere; ciornele netrimise expiră după 48 de ore.
- Ofertele și acceptările/refuzurile rămân separate. Ridicarea comună se confirmă după soluționarea tuturor produselor; include numai sculele acceptate.
- Un singur formular salvează adresa de ridicare și contul bancar pentru toate sculele acceptate, cu criptare distinctă pentru fiecare solicitare. Curierul, AWB-ul și etapele până la sosirea la verificare se actualizează împreună. Revizuirile, plățile și retururile rămân individuale.
- O expediere consumă o singură trimitere din limitele zilnice și orare. Statisticile numără fiecare sculă separat.
- WordPress → Vinde Scula → Statistici: interval după data primirii, scule și expediții, oferte trimise, acceptări explicite, plăți, ofertă→acceptare, cerere→achiziție, timpul până la prima ofertă și distribuția pe categorii. Acces rezervat administratorului.

## Corecții necesare integrării

- Operațiile de evaluare cer un rol autorizat explicit; un autor/contributor nu își poate emite singur oferta.
- Acceptarea inițială se face de către client; schimbarea manuală care bloca finalizarea a fost eliminată.
- Sculele noi primesc o stare inițială explicită. Trimiterea și finalizarea comună folosesc blocări și tranzacții, cu reverificarea datelor după blocare.
- Formularele din file diferite folosesc copii separate pentru fotografiile în pregătire. Pagina independentă afișează autentificarea și în absența cardurilor de categorii.

## Validare

- PHP și JavaScript: verificări de sintaxă.
- Regresie reală pentru o singură sculă: autentificare, nonce, upload multipart, WebP și previzualizare private, accesul proprietarului, cont WooCommerce, cron și două mesaje capturate local.
- Integrare HTTP pentru expediere: autentificare și proprietar, creare/adăugare/trimitere idempotente, 3 scule și respingerea celei de-a patra, eliminare și curățarea fotografiilor, ciorne fără notificări, o singură cotă consumată, 2 acceptări/1 refuz, blocarea ridicării cu ofertă fără răspuns, criptare distinctă, AWB și etape comune, revizuire și plată individuale.
- Statistici pe înregistrări de test: calcule exacte, perioade, date invalide, categorii, metadate duplicate și acces neautorizat.
- Acces evaluatori: un contributor proprietar, cu nonce valid pentru sesiune, primește 403; acceptarea manuală de către administrator este refuzată fără modificarea stării.
- Modul expediții: limita ciornelor, blocări distincte, expirare și ștergerea ciornelor, selecție fixată pentru ridicare.
- Browser desktop/mobil: coș real cu 3 scule, reîncărcare, eliminare, autentificare fără carduri de categorii și afișarea statisticilor. Scenarii cu API simulat: file clonate cu fotografii și identificatori separați, retry după pierderea răspunsului, expirare și sesiune expirată, fără pierderea formularului curent. Fără erori JavaScript.
- Datele de test și mesajele aferente sunt eliminate; limitele sunt restaurate.

## Actualizare

Arhiva păstrează directorul `vinde-scula-categorii`. Fă backup al bazei de date, al folderului privat de fotografii și al configurației WordPress înainte de instalare. Păstrează cheile/salturile WordPress: datele bancare și de ridicare existente depind de ele.

Necesită PHP 7.4+, WordPress 6.0+, WooCommerce pentru interfața de cont, GD/Imagick cu WebP, OpenSSL AES-256-GCM și tabele InnoDB pentru tranzacțiile comune. Serviciile locale de test au folosit PHP 8.3, WordPress 7.1.3 și WooCommerce 11.2.1. Nu au fost testate în producție tema Bigxon/Elementor, livrarea reală a e-mailurilor sau integrarea cu un curier. Pluginul nu generează AWB-uri și nu efectuează transferuri bancare.

Această versiune implementează cele două funcții cerute și corecțiile de integrare descrise. Nu reprezintă remedierea integrală a tuturor observațiilor din auditul 1.3.89; riscul vechi al trimiterii ofertei simultan cu alte modificări rămâne în afara acestei livrări.

Arhiva originală 1.3.89, checkout-ul inițial și ramura `main` sunt păstrate. Publicarea arhivei se face pe o ramură separată de livrare.

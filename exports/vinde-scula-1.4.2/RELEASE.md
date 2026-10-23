# Vinde Scula 1.4.2

Actualizare a formularului, păstrând **o singură sculă per solicitare** și statisticile administrative.

- Selecția mărcii afișează direct cele 12 mărci, fără bară de căutare. La deschidere, focalizarea este pe marca selectată sau pe prima marcă.
- Butonul pentru marcă afișează logoul selectat, numele mărcii și „Schimbă”. Reutilizează aceeași imagine existentă în selector; dacă imaginea lipsește sau nu se poate încărca, numele rămâne disponibil.
- Marca, modelul și anul fabricației sunt împreună în partea de sus, pe un rând pe desktop și suprapuse pe mobil.
- Lista anilor coboară de la anul curent până la 2020. Anul este opțional; „Înainte de 2020” și „Nu știu / nu este vizibil” rămân disponibile.
- Platforma acumulatorului este activată după alegerea mărcii și conține efectiv numai platformele acelei firme, plus opțiunile generice. O marcă fără platforme predefinite păstrează opțiunile generice.
- Schimbarea mărcii elimină selecția sau textul unei platforme de la marca anterioară. Reîncărcarea păstrează o selecție validă pentru aceeași marcă.
- Ciornele locale cu ani mai vechi sunt adaptate opțiunii „Înainte de 2020”; anii din solicitările istorice nu sunt modificați.

## Verificări

Sintaxă PHP/JavaScript și verificări HTTP pentru ani, validarea platformei după marcă și uploaduri reale pentru sculă cu acumulator, altă categorie și platformă completată manual. Verificări în browser pentru selector, logo, focalizare, opțiuni de platformă, restaurare, ordinea câmpurilor și afișare desktop/mobil. Logo-urile din browserul local sunt imagini de test pentru verificarea afișării; imaginile existente pe site se reutilizează la instalare.

Datele de test și mesajele capturate local sunt curățate, iar limitele sunt restaurate. Nu se trimit notificări către destinatari externi în mediul de verificare.

Înainte de instalare, păstrează un backup al bazei de date și al fotografiilor private. Nu schimba cheile/salturile WordPress, de care depinde decriptarea datelor existente. Directorul pluginului rămâne `vinde-scula-categorii`; arhivele anterioare, sursele originale și `main` sunt păstrate.

Validarea este locală cu WordPress 7.1.3, PHP 8.3 și WooCommerce 11.2.1. Tema Bigxon/Elementor și livrarea reală a e-mailurilor nu sunt instalate/testate integral. Auditul anterior rămâne separat; această versiune nu modifică fluxul concurent al trimiterii ofertei inițiale.

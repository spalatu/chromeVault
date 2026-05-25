# ChromeVault

ChromeVault este o galerie de imagini digitală axată pe extracția automată de palete de culori. Interfața este complet inspirată de limbajul de design SwiftUI (Apple), oferind o experiență nativă, fluidă și minimalistă direct în browser.

Proiectul folosește un stack clasic (HTML, CSS, JS, PHP) și stochează datele în fișiere JSON, eliminând nevoia unei baze de date complexe și fiind extrem de ușor de rulat local.

## Structura Proiectului

```text
chromeVault/
  ├── index.php         # Pagina de prezentare (Landing page)
  ├── login.php         # Autentificare utilizator
  ├── register.php      # Înregistrare utilizator
  ├── dashboard.php     # Panoul principal (statistici și activitate recentă)
  ├── gallery.php       # Biblioteca completă cu filtre pe bază de culori
  ├── upload.php        # Modulul de upload (fișier sau URL) + Extracție K-Means
  ├── contact.php       # Formular de contact simplu
  ├── css/
  │   └── style.css     # Sistemul de design SwiftUI (Dark/Light mode via variabile CSS)
  ├── js/
  │   ├── colorExtract.js  # Algoritmul K-Means pentru culori (Pure JS)
  │   └── script.js        # Logica UI, teme, traduceri și notificări
  ├── php/
  │   ├── auth.php
  │   ├── functions.php
  │   └── saveData.php  # Manipularea fișierelor JSON
  └── data/             # Stocare persistenta (users.json, images.json, etc.)

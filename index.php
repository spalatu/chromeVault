<!DOCTYPE html>
<html lang="ro" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>chromeVault — Organizarea fișierelor de design</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .badge-version {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            background-color: var(--bg-elevated);
            border: 1px solid var(--border-light);
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }
        .dot-green {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
            margin-right: 8px;
        }
    </style>
</head>
<body class="landing-page">

    <!-- ── Navigation ──────────────────────────────────────────────── -->
    <nav class="navbar">
        <div class="logo" style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="22" height="22" style="fill: var(--text-primary);">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
            <span style="font-weight: 700; letter-spacing: -0.04em;">chromeVault</span>
        </div>

        <ul class="nav-links">
            <li><a href="#acasa">Acasă</a></li>
            <li><a href="#despre">Despre</a></li>
            <li><a href="#functionalitati">Funcționalități</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>

        <div class="nav-controls">
            <button id="theme-toggle" class="btn-icon" aria-label="Schimbă tema"
                    style="border: 1px solid var(--border-light); width: 32px; height: 32px;
                           display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                    <path d="M20 8.69V4h-4.69L12 .69 8.69 4H4v4.69L.69 12 4 15.31V20h4.69L12 23.31
                             15.31 20H20v-4.69L23.31 12 20 8.69zM12 18c-3.31 0-6-2.69-6-6s2.69-6
                             6-6 6 2.69 6 6-2.69 6-6 6zm0-10c-2.21 0-4 1.79-4 4s1.79 4 4 4
                             4-1.79 4-4-1.79-4-4-4z"/>
                </svg>
            </button>
            <a href="login.php"    class="btn btn-outline" style="padding: 6px 16px; font-size: 12px;">Login</a>
            <a href="register.php" class="btn btn-primary" style="padding: 6px 16px; font-size: 12px;">Register</a>
        </div>
    </nav>

    <!-- ── Main ────────────────────────────────────────────────────── -->
    <main class="lp-main">

        <!-- Hero -->
        <section class="lp-hero-section" id="acasa">
            <div class="badge-version">
                <span class="dot-green"></span>
                Ziua 7 — Înregistrare &amp; Salvare JSON
            </div>
            <h1 class="lp-hero-title">Organizarea fișierelor de design nu a fost niciodată mai ușoară</h1>
            <p class="lp-hero-subtitle">
                Un mod profesional de a colecta, căuta și organiza fișierele tale de design
                într-un mod frumos și logic — totul într-un singur loc centralizat.
            </p>
            <div class="lp-hero-ctas">
                <a href="register.php" class="btn btn-primary">Creează un cont</a>
                <a href="#despre"      class="btn btn-outline">Află mai multe</a>
            </div>
        </section>

        <!-- Despre -->
        <section class="lp-section" id="despre">
            <div class="lp-section-header">
                <h2 class="lp-section-title">Despre Proiect</h2>
                <p class="lp-section-subtitle">
                    Această aplicație este dezvoltată ca parte a practicii, concentrându-se pe
                    realizarea unei interfețe dinamice cu HTML, CSS, JavaScript și PHP.
                    Datele utilizatorilor sunt salvate în fișiere JSON.
                </p>
            </div>
        </section>

        <!-- Progres -->
        <section class="lp-section" id="functionalitati">
            <div class="lp-section-header">
                <h2 class="lp-section-title">Progresul dezvoltării</h2>
                <p class="lp-section-subtitle">
                    Etapele finalizate până la Ziua 7.
                </p>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                        gap: 20px; margin-top: 16px;">
                <?php
                $steps = [
                    ['zi' => 'Ziua 4', 'titlu' => 'Pagina principală', 'desc' => 'Structura inițială: pagina principală, meniu de navigare, footer și stiluri CSS de bază.', 'done' => true],
                    ['zi' => 'Ziua 6', 'titlu' => 'Formulare de bază', 'desc' => 'Crearea formularelor de înregistrare și autentificare. Transmiterea datelor către PHP via POST.', 'done' => true],
                    ['zi' => 'Ziua 7', 'titlu' => 'Înregistrare & JSON', 'desc' => 'Implementarea completă a paginii de înregistrare cu validare și salvare utilizatori în fișier users.json.', 'done' => true],
                ];
                foreach ($steps as $s): ?>
                <div style="background: var(--bg-elevated); border: 1px solid var(--border-light);
                            border-radius: 12px; padding: 24px; position: relative; overflow: hidden;">
                    <?php if ($s['done']): ?>
                    <span style="position: absolute; top: 16px; right: 16px; font-size: 11px;
                                 font-weight: 600; color: #10b981; background: rgba(16,185,129,0.1);
                                 border: 1px solid rgba(16,185,129,0.25); border-radius: 99px;
                                 padding: 2px 10px;">✓ Finalizat</span>
                    <?php endif; ?>
                    <div style="font-size: 11px; font-weight: 700; color: var(--text-tertiary);
                                text-transform: uppercase; letter-spacing: 0.07em;
                                margin-bottom: 8px;"><?= $s['zi'] ?></div>
                    <div style="font-size: 16px; font-weight: 700; margin-bottom: 10px;
                                letter-spacing: -0.02em;"><?= $s['titlu'] ?></div>
                    <div style="font-size: 13px; color: var(--text-secondary);
                                line-height: 1.55;"><?= $s['desc'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

    </main>

    <!-- ── Footer ──────────────────────────────────────────────────── -->
    <footer class="lp-footer">
        <div class="lp-footer-grid">
            <div class="lp-footer-brand">
                <div class="lp-footer-brand-title" style="display:flex; align-items:center; gap:8px;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                    <span>chromeVault</span>
                </div>
                <p class="lp-footer-brand-desc">
                    Aplicație web pentru organizarea și gestionarea asset-urilor digitale.
                    Proiect de practică — 2026.
                </p>
            </div>
            <div class="lp-footer-column">
                <h4>Navigare</h4>
                <ul>
                    <li><a href="#acasa">Acasă</a></li>
                    <li><a href="#despre">Despre</a></li>
                    <li><a href="register.php">Înregistrare</a></li>
                    <li><a href="login.php">Autentificare</a></li>
                </ul>
            </div>
        </div>
        <div class="lp-footer-bottom">
            <span>&copy; 2026 chromeVault — Proiect de practică. Toate drepturile rezervate.</span>
        </div>
    </footer>

    <script>
        document.getElementById('theme-toggle').addEventListener('click', function () {
            document.documentElement.classList.toggle('dark');
        });
    </script>
</body>
</html>
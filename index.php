<?php
/**
 * index.php — chromeVault
 * Premium, high-fidelity landing page for chromeVault asset manager.
 * Featuring interactive tab panels, linear grids, color-search simulator, and full translations.
 */
require_once 'php/functions.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>chromeVault — Organizing design files has never been easier</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Extra custom styling fine-tunes specifically for landing page layout elements */
        .badge-premium {
            background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
            border: 1px solid var(--border-light);
            color: var(--text-primary);
        }
        .text-glow {
            text-shadow: 0 0 40px rgba(255, 255, 255, 0.1);
        }
        /* Custom visual elements for the mockups */
        .mock-sidebar-li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .mock-sidebar-li.active, .mock-sidebar-li:hover {
            background: rgba(255,255,255,0.04);
            color: var(--text-primary);
        }
        .mock-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-light);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.25s ease;
            position: relative;
        }
        .mock-card:hover {
            transform: translateY(-2px);
            border-color: var(--border-strong);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .mock-card-img {
            aspect-ratio: 4/3;
            background-size: cover;
            background-position: center;
            background-color: rgba(255,255,255,0.02);
            border-bottom: 1px solid var(--border-light);
        }
        .mock-card-info {
            padding: 8px;
            font-size: 11px;
            font-weight: 500;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        /* Custom styles for language and theme row in navigation */
        .nav-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-select {
            background: var(--bg-elevated);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 12px;
            height: 28px;
            padding: 0 8px;
            cursor: pointer;
            outline: none;
            transition: border-color 0.2s ease;
        }
        .nav-select:hover {
            border-color: var(--border-strong);
        }
    </style>
</head>
<body class="landing-page">

    <!-- ── Header & Premium Navigation ─────────────────────────────────────── -->
    <nav class="navbar">
        <div class="logo" style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="22" height="22" style="fill: var(--text-primary); transition: transform 0.4s var(--ease-premium);" id="logo-icon">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
            <span style="font-weight: 700; letter-spacing: -0.04em;">chromeVault</span>
        </div>
        <ul class="nav-links" style="margin: 0;">
            <li><a href="#features" style="color: var(--text-secondary); transition: color 0.2s;">Features</a></li>
            <li><a href="#ai-integration" style="color: var(--text-secondary); transition: color 0.2s;">AI Engine</a></li>
            <li><a href="#plugins" style="color: var(--text-secondary); transition: color 0.2s;">Plugins</a></li>
            <li><a href="contact.php" style="color: var(--text-secondary); transition: color 0.2s;">Support</a></li>
        </ul>
        <div class="nav-controls">
            <!-- Language Switcher -->
            <select class="nav-select" onchange="location = this.value;">
                <option value="?lang=en" <?php echo $current_lang === 'en' ? 'selected' : ''; ?>>EN</option>
                <option value="?lang=ro" <?php echo $current_lang === 'ro' ? 'selected' : ''; ?>>RO</option>
                <option value="?lang=ru" <?php echo $current_lang === 'ru' ? 'selected' : ''; ?>>RU</option>
            </select>

            <!-- Theme Toggle -->
            <button id="theme-toggle" class="btn-icon" aria-label="Toggle Mode" style="border: 1px solid var(--border-light); width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px;">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                    <path d="M20 8.69V4h-4.69L12 .69 8.69 4H4v4.69L.69 12 4 15.31V20h4.69L12 23.31 15.31 20H20v-4.69L23.31 12 20 8.69zM12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6zm0-10c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z"/>
                </svg>
            </button>

            <!-- Dashboard / Client CTAs -->
            <?php if(is_logged_in()): ?>
                <a href="dashboard.php" class="btn btn-primary" style="padding: 6px 16px; font-size:12px;"><?php echo t('dashboard'); ?></a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline" style="padding: 6px 16px; font-size:12px;"><?php echo t('login'); ?></a>
                <a href="register.php" class="btn btn-primary" style="padding: 6px 16px; font-size:12px;"><?php echo t('register'); ?></a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- ── Main Landing Layout Container ───────────────────────────────────── -->
    <main class="lp-main">
        
        <!-- ── SECTION 1: Hero Header Section ──────────────────────────────── -->
        <section class="lp-hero-section">
            <div class="lp-hero-badge">
                <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:#10b981; margin-right:8px;"></span>
                Introducing chromeVault v4.0
            </div>
            <h1 class="lp-hero-title">Organizing design files has never been easier</h1>
            <p class="lp-hero-subtitle">A professional way to collect, search and organize your creative design files in a beautiful, logical way — all in one centralized place.</p>
            
            <div class="lp-hero-ctas">
                <a href="register.php" class="btn btn-primary" style="display: inline-flex; gap: 8px;">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align: middle;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/></svg>
                    Download macOS for Mac
                </a>
                <a href="dashboard.php" class="btn btn-outline">Open Web App</a>
            </div>

            <div class="lp-hero-stats">
                <span>v4.0.0 Build23 (2026-05-28)</span>
                <span style="margin: 0 12px; color: var(--text-tertiary);">•</span>
                <span>Supports macOS 10.15+ &amp; Windows 10+</span>
            </div>
        </section>

        <!-- ── SECTION 2: Interactive Mockup Window Showcase ───────────────── -->
        <section class="lp-mockup-wrapper">
            <div class="lp-mockup-window">
                <!-- Browser Title Bar -->
                <div class="lp-mockup-bar">
                    <div class="lp-mockup-dots">
                        <div class="lp-mockup-dot" style="background:#ff5f56;"></div>
                        <div class="lp-mockup-dot" style="background:#ffbd2e;"></div>
                        <div class="lp-mockup-dot" style="background:#27c93f;"></div>
                    </div>
                    <div style="font-size:11px; font-weight:500; color:var(--text-secondary); display:flex; align-items:center; gap:6px;">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--text-secondary);"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        chromeVault — secure-journal.vault
                    </div>
                    <div style="width: 48px;"></div>
                </div>

                <!-- Window Content Mockup -->
                <div class="lp-mockup-body">
                    <!-- Sidebar Mock -->
                    <div style="width: 200px; background:var(--bg-surface); border-right: 1px solid var(--border-light); display:flex; flex-direction:column; padding:16px 8px; justify-content:space-between;">
                        <div>
                            <div style="font-size: 9px; text-transform:uppercase; font-weight:700; color:var(--text-tertiary); letter-spacing:0.05em; padding-left:12px; margin-bottom:8px;">Smart Folders</div>
                            <div class="mock-sidebar-li active">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M3 3h8v8H3zm0 10h8v8H3zM13 3h8v8h-8zm0 10h8v8h-8z"/></svg>
                                All Items
                            </div>
                            <div class="mock-sidebar-li">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                                Uncategorized
                            </div>
                            <div class="mock-sidebar-li">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42z"/></svg>
                                Untagged
                            </div>
                            
                            <div style="font-size: 9px; text-transform:uppercase; font-weight:700; color:var(--text-tertiary); letter-spacing:0.05em; padding-left:12px; margin-top:20px; margin-bottom:8px;">Folders</div>
                            <div class="mock-sidebar-li">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                                Visual Journal
                            </div>
                            <div class="mock-sidebar-li">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                                Workspaces
                            </div>
                        </div>
                        
                        <div style="display:flex; justify-content:space-between; align-items:center; padding: 0 12px;">
                            <span style="font-size: 10px; color: var(--text-tertiary);">Storage: 2.4 GB / 10 GB</span>
                        </div>
                    </div>

                    <!-- Main Grid Mock -->
                    <div style="flex: 1; padding: 20px; overflow:hidden; display:flex; flex-direction:column; gap:16px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div style="font-size: 14px; font-weight:700;">Design Library</div>
                            <div style="display:flex; gap:8px;">
                                <div style="width: 120px; height: 24px; border-radius: 12px; background:var(--bg-surface); border:1px solid var(--border-light); display:flex; align-items:center; padding: 0 10px; font-size:10px; color:var(--text-secondary);">
                                    Search...
                                </div>
                                <div style="width: 50px; height: 24px; border-radius:12px; background:var(--text-primary); color:var(--bg-base); font-size:10px; font-weight:600; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                                    Upload
                                </div>
                            </div>
                        </div>

                        <!-- Card Grid -->
                        <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px; flex:1;">
                            <div class="mock-card">
                                <div class="mock-card-img" style="background-image: url('https://images.unsplash.com/photo-1586023492125-27b2c045efd7?q=80&w=400&auto=format&fit=crop');"></div>
                                <div class="mock-card-info">Minimalist Workspace</div>
                            </div>
                            <div class="mock-card">
                                <div class="mock-card-img" style="background-image: url('https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?q=80&w=400&auto=format&fit=crop');"></div>
                                <div class="mock-card-info">Editorial Interface</div>
                            </div>
                            <div class="mock-card">
                                <div class="mock-card-img" style="background-image: url('https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?q=80&w=400&auto=format&fit=crop');"></div>
                                <div class="mock-card-info">Cozy Desktop Setup</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar Properties Panel Mock -->
                    <div style="width: 220px; background:var(--bg-surface); border-left: 1px solid var(--border-light); display:flex; flex-direction:column; padding:16px; gap:16px;">
                        <div style="border-radius: 8px; border:1px solid var(--border-light); overflow:hidden; aspect-ratio:4/3; background-image: url('https://images.unsplash.com/photo-1586023492125-27b2c045efd7?q=80&w=400&auto=format&fit=crop'); background-size:cover; background-position:center;"></div>
                        
                        <div>
                            <div style="font-size: 11px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">Minimalist Workspace</div>
                            <div style="font-size:10px; color:var(--text-secondary); line-height:1.4;">Premium wood desk with minimalist studio monitors.</div>
                        </div>

                        <div>
                            <div style="font-size: 9px; text-transform:uppercase; font-weight:700; color:var(--text-tertiary); margin-bottom:6px;">Extracted Palette</div>
                            <div style="display:flex; gap:6px;">
                                <div style="width:18px; height:18px; border-radius:4px; background:#d97706;" title="#d97706"></div>
                                <div style="width:18px; height:18px; border-radius:4px; background:#16a34a;" title="#16a34a"></div>
                                <div style="width:18px; height:18px; border-radius:4px; background:#dc2626;" title="#dc2626"></div>
                                <div style="width:18px; height:18px; border-radius:4px; background:#2563eb;" title="#2563eb"></div>
                                <div style="width:18px; height:18px; border-radius:4px; background:#7c3aed;" title="#7c3aed"></div>
                            </div>
                        </div>

                        <div style="font-size:10px; border-top:1px solid var(--border-light); padding-top:12px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:4px;"><span style="color:var(--text-secondary);">Size</span><span style="font-weight:500;">2.4 MB</span></div>
                            <div style="display:flex; justify-content:space-between; margin-bottom:4px;"><span style="color:var(--text-secondary);">Rating</span><span style="color:#FFD700;">★★★★★</span></div>
                            <div style="display:flex; justify-content:space-between;"><span style="color:var(--text-secondary);">Type</span><span style="font-weight:500;">PNG</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── SECTION 3: Dynamic Interactive Feature Tabs ────────────────── -->
        <section class="lp-section" id="features">
            <div class="lp-section-header">
                <h2 class="lp-section-title">An asset manager tailored for designers</h2>
                <p class="lp-section-subtitle">With AI capabilities built directly into chromeVault, collecting, organizing, searching, and browsing feel smarter and more seamless than ever.</p>
            </div>

            <!-- Tab Menu -->
            <div class="lp-tabs-nav">
                <button class="lp-tab-trigger active" onclick="switchLpTab(event, 'tab-collect')">Collect</button>
                <button class="lp-tab-trigger" onclick="switchLpTab(event, 'tab-organize')">Organize</button>
                <button class="lp-tab-trigger" onclick="switchLpTab(event, 'tab-search')">Search</button>
                <button class="lp-tab-trigger" onclick="switchLpTab(event, 'tab-browse')">Browse</button>
            </div>

            <!-- Panel 1: Collect -->
            <div class="lp-tab-panel active" id="tab-collect">
                <div class="lp-tab-content">
                    <h3 style="font-size:28px; font-weight:700; margin-bottom:16px;">Save inspiration without breaking your flow</h3>
                    <p style="color:var(--text-secondary); font-size:14px; line-height:1.6; margin-bottom:24px;">Capture fleeting design mockups and raw inspirations instantly using drag-and-drop mechanics, remote URL capture tools, and standard local uploads.</p>
                    <div class="lp-tab-features">
                        <div class="lp-tab-feature">
                            <h4>Drag &amp; Drop Ingest</h4>
                            <p>Drop images directly into your vault interface. chromeVault parses and stores metadata immediately.</p>
                        </div>
                        <div class="lp-tab-feature">
                            <h4>Remote Image URL Fetch</h4>
                            <p>Paste image links from across the web. The backend fetches high-resolution assets seamlessly.</p>
                        </div>
                    </div>
                </div>
                <div class="lp-tab-visual">
                    <!-- Drop Zone Mockup visual -->
                    <div style="width: 100%; max-width: 400px; padding: 40px; border: 2px dashed var(--border-strong); border-radius: 16px; text-align: center; background: rgba(255,255,255,0.01);">
                        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:16px; color:var(--text-secondary);"><polyline points="16 16 12 12 8 16"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path></svg>
                        <p style="font-weight:600; font-size:14px; color:var(--text-primary); margin:0;">Drag design images here</p>
                        <p style="font-size:12px; color:var(--text-tertiary); margin:4px 0 0 0;">Supports JPG, PNG, WEBP, and GIF</p>
                    </div>
                </div>
            </div>

            <!-- Panel 2: Organize -->
            <div class="lp-tab-panel" id="tab-organize">
                <div class="lp-tab-content">
                    <h3 style="font-size:28px; font-weight:700; margin-bottom:16px;">Streamline your asset categories</h3>
                    <p style="color:var(--text-secondary); font-size:14px; line-height:1.6; margin-bottom:24px;">Avoid scattered folders. Take advantage of custom tag structures, nested folders, smart logic rules, and intuitive metadata tables.</p>
                    <div class="lp-tab-features">
                        <div class="lp-tab-feature">
                            <h4>Tag Categorization</h4>
                            <p>Attach rich metadata tags to speed up sorting and query accuracy.</p>
                        </div>
                        <div class="lp-tab-feature">
                            <h4>Folder Structuring</h4>
                            <p>Sort items cleanly inside custom hierarchies and isolated smart workspaces.</p>
                        </div>
                    </div>
                </div>
                <div class="lp-tab-visual">
                    <!-- Interactive tag items visual -->
                    <div style="display:flex; flex-wrap:wrap; gap:8px; max-width:320px;">
                        <span class="tag" style="padding: 8px 16px; font-size:13px; font-weight:500;">#cozy</span>
                        <span class="tag" style="padding: 8px 16px; font-size:13px; font-weight:500;">#darkmode</span>
                        <span class="tag" style="padding: 8px 16px; font-size:13px; font-weight:500;">#workstation</span>
                        <span class="tag" style="padding: 8px 16px; font-size:13px; font-weight:500;">#ui-design</span>
                        <span class="tag" style="padding: 8px 16px; font-size:13px; font-weight:500;">#inspiration</span>
                        <span class="tag" style="padding: 8px 16px; font-size:13px; font-weight:500;">#nordic</span>
                    </div>
                </div>
            </div>

            <!-- Panel 3: Search -->
            <div class="lp-tab-panel" id="tab-search">
                <div class="lp-tab-content">
                    <h3 style="font-size:28px; font-weight:700; margin-bottom:16px;">Swiftly locate your assets in under 0.5s</h3>
                    <p style="color:var(--text-secondary); font-size:14px; line-height:1.6; margin-bottom:24px;">Discover matching files using keywords, rating ranges, format types, or extracted dominant color palettes dynamically.</p>
                    <div class="lp-tab-features">
                        <div class="lp-tab-feature">
                            <h4>Color-Based Search</h4>
                            <p>Click on any color block to filter down assets built within identical design guidelines.</p>
                        </div>
                        <div class="lp-tab-feature">
                            <h4>Detailed Filtering</h4>
                            <p>Filter by dimensions, ratings, file extensions, and creation timestamps.</p>
                        </div>
                    </div>
                </div>
                <!-- Premium Color Swatch search interactive demo -->
                <div class="lp-tab-visual">
                    <div class="lp-visual-search-demo">
                        <div class="lp-demo-header">
                            <span style="font-size:11px; font-weight:600; color:var(--text-secondary);">Interactive Palette Filter</span>
                            <div class="lp-demo-palette">
                                <div class="lp-demo-swatch active" style="background:#d97706;" onclick="filterDemoColors(event, 'amber')"></div>
                                <div class="lp-demo-swatch" style="background:#16a34a;" onclick="filterDemoColors(event, 'green')"></div>
                                <div class="lp-demo-swatch" style="background:#2563eb;" onclick="filterDemoColors(event, 'blue')"></div>
                            </div>
                        </div>
                        <div class="lp-demo-grid">
                            <div class="lp-demo-item" data-color="amber" style="background-image:url('https://images.unsplash.com/photo-1586023492125-27b2c045efd7?q=80&w=200&auto=format&fit=crop');"></div>
                            <div class="lp-demo-item" data-color="green" style="background-image:url('https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?q=80&w=200&auto=format&fit=crop');"></div>
                            <div class="lp-demo-item" data-color="blue" style="background-image:url('https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?q=80&w=200&auto=format&fit=crop');"></div>
                            <div class="lp-demo-item" data-color="amber" style="background-image:url('https://images.unsplash.com/photo-1531403009284-440f080d1e12?q=80&w=200&auto=format&fit=crop');"></div>
                            <div class="lp-demo-item" data-color="green" style="background-image:url('https://images.unsplash.com/photo-1448375240586-882707db888b?q=80&w=200&auto=format&fit=crop');"></div>
                            <div class="lp-demo-item" data-color="blue" style="background-image:url('https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=200&auto=format&fit=crop');"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel 4: Browse -->
            <div class="lp-tab-panel" id="tab-browse">
                <div class="lp-tab-content">
                    <h3 style="font-size:28px; font-weight:700; margin-bottom:16px;">Browse assets smoothly</h3>
                    <p style="color:var(--text-secondary); font-size:14px; line-height:1.6; margin-bottom:24px;">No crop-offs, scaled grids, or sluggish loadings. Experience instantaneous spacebar preview actions and smooth zoom adjustments.</p>
                    <div class="lp-tab-features">
                        <div class="lp-tab-feature">
                            <h4>Spacebar Quick View</h4>
                            <p>Press the spacebar on any asset card to pop out high-resolution detail previews immediately.</p>
                        </div>
                        <div class="lp-tab-feature">
                            <h4>Color Copying Action</h4>
                            <p>Hover and click on color swatches to copy Hex code structures instantly to your clipboard.</p>
                        </div>
                    </div>
                </div>
                <div class="lp-tab-visual">
                    <div style="position:relative; width: 100%; max-width: 320px; border-radius:12px; overflow:hidden; border:1px solid var(--border-strong);">
                        <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?q=80&w=400&auto=format&fit=crop" style="width:100%; display:block;" alt="Minimalist Workspace">
                        <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.75); backdrop-filter:blur(8px); padding:12px; display:flex; align-items:center; justify-content:space-between;">
                            <span style="font-size:12px; color:#fff; font-weight:600;">Spacebar Preview</span>
                            <span style="font-size:10px; color:rgba(255,255,255,0.7); background:rgba(255,255,255,0.15); padding:2px 8px; border-radius:4px;">1920 × 1280</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── SECTION 4: AI Capabilities Highlight ────────────────────────── -->
        <section class="lp-ai-section" id="ai-integration">
            <div class="lp-ai-grid">
                
                <!-- Card 1: AI Search -->
                <div class="lp-ai-card">
                    <span class="lp-ai-card-badge">Intelligent Search</span>
                    <h3 class="lp-ai-card-title">AI Search</h3>
                    <p class="lp-ai-card-desc">Understand images the way you do. Set queries based on semantic meanings, styles, colors, and layout ratios without relying on complex filenames.</p>
                    <div class="lp-ai-chips">
                        <span class="lp-ai-chip">Semantic Search</span>
                        <span class="lp-ai-chip">Visual Color Match</span>
                        <span class="lp-ai-chip">Cross-Source Search</span>
                    </div>
                </div>

                <!-- Card 2: AI Automation -->
                <div class="lp-ai-card">
                    <span class="lp-ai-card-badge">Engine Actions</span>
                    <h3 class="lp-ai-card-title">AI Action Automation</h3>
                    <p class="lp-ai-card-desc">Configure automated naming systems, categorizations, and metadata pipelines. Let chromeVault handle naming details and auto-tag assignments instantly on import.</p>
                    <div class="lp-ai-chips">
                        <span class="lp-ai-chip">Auto-Rename Rules</span>
                        <span class="lp-ai-chip">Auto-Tag Allocation</span>
                        <span class="lp-ai-chip">Automated Folder Routing</span>
                    </div>
                </div>

            </div>
        </section>

        <!-- ── SECTION 5: Comprehensive Features Grid ──────────────────────── -->
        <section class="lp-section" id="plugins">
            <div class="lp-section-header">
                <h2 class="lp-section-title">Creative production utilities</h2>
                <p class="lp-section-subtitle">A collection of custom features designed to refine and support your digital asset management workflow.</p>
            </div>

            <div class="lp-features-grid">
                <!-- Feature 1 -->
                <div class="lp-feature-card">
                    <div class="lp-feature-card-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <h4 class="lp-feature-card-title">Cloud Sync Tools</h4>
                    <p class="lp-feature-card-desc">Sync your creative library across multiple workstations easily using standard cloud services.</p>
                </div>
                <!-- Feature 2 -->
                <div class="lp-feature-card">
                    <div class="lp-feature-card-icon">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                    </div>
                    <h4 class="lp-feature-card-title">All Formats Compatible</h4>
                    <p class="lp-feature-card-desc">Store vector SVG resources, JPGs, heavy WEBP layers, audio loops, and video captures safely.</p>
                </div>
                <!-- Feature 3 -->
                <div class="lp-feature-card">
                    <div class="lp-feature-card-icon">
                        <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                    </div>
                    <h4 class="lp-feature-card-title">Heavy Tag-Management</h4>
                    <p class="lp-feature-card-desc">Organize, rename, prune, and consolidate large indices of custom design tags with clean admin workflows.</p>
                </div>
                <!-- Feature 4 -->
                <div class="lp-feature-card">
                    <div class="lp-feature-card-icon">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="M21 15l-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
                    </div>
                    <h4 class="lp-feature-card-title">Video management</h4>
                    <p class="lp-feature-card-desc">Hover to scrub and preview video layouts dynamically without launching extra playback layers.</p>
                </div>
                <!-- Feature 5 -->
                <div class="lp-feature-card">
                    <div class="lp-feature-card-icon">
                        <svg viewBox="0 0 24 24"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                    </div>
                    <h4 class="lp-feature-card-title">Audio Assets Support</h4>
                    <p class="lp-feature-card-desc">Preview MP3, WAV, AAC, and lossless FLAC assets directly inside the gallery timeline.</p>
                </div>
                <!-- Feature 6 -->
                <div class="lp-feature-card">
                    <div class="lp-feature-card-icon">
                        <svg viewBox="0 0 24 24"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>
                    </div>
                    <h4 class="lp-feature-card-title">Font Cataloging</h4>
                    <p class="lp-feature-card-desc">Preview typefaces and fonts dynamically without local installation procedures.</p>
                </div>
            </div>
        </section>

        <!-- ── SECTION 6: Voices & Feedback Testimonials ───────────────────── -->
        <section class="lp-section">
            <div class="lp-section-header">
                <h2 class="lp-section-title">Trusted by creatives everywhere</h2>
                <p class="lp-section-subtitle">See why professional designers and art directors rely on chromeVault to streamline their workspaces.</p>
            </div>
            
            <div class="lp-voices-grid">
                <!-- Voice 1 -->
                <div class="lp-voice-card">
                    <p class="lp-voice-text">"chromeVault completely replaced my disorganized desktop folders. The auto color extraction saves hours when organizing visual mood boards."</p>
                    <div class="lp-voice-author">
                        <div class="lp-voice-avatar" style="background:#a78bfa;">EL</div>
                        <div class="lp-voice-meta">
                            <h5>Elena Lazar</h5>
                            <p>Brand Strategist</p>
                        </div>
                    </div>
                </div>
                <!-- Voice 2 -->
                <div class="lp-voice-card">
                    <p class="lp-voice-text">"The spacebar preview action is remarkably fast. Flicking through thousands of raw assets feels incredibly seamless and native."</p>
                    <div class="lp-voice-author">
                        <div class="lp-voice-avatar" style="background:#f472b6;">MD</div>
                        <div class="lp-voice-meta">
                            <h5>Mihai Dumitrescu</h5>
                            <p>Senior UI Designer</p>
                        </div>
                    </div>
                </div>
                <!-- Voice 3 -->
                <div class="lp-voice-card">
                    <p class="lp-voice-text">"Searching assets by colors makes finding inspiration for UI guides incredibly fast. The premium dark aesthetic feels spectacular."</p>
                    <div class="lp-voice-author">
                        <div class="lp-voice-avatar" style="background:#6ee7b7;">AN</div>
                        <div class="lp-voice-meta">
                            <h5>Ana Nistor</h5>
                            <p>Art Director</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── SECTION 7: Free Trial Call To Action ────────────────────────── -->
        <section class="lp-cta-section">
            <div class="lp-cta-box">
                <h2 class="lp-cta-title">Start your 30-day free trial</h2>
                <p class="lp-cta-desc">Centralize your vector resources, brand illustrations, and fonts now. Build a premium inspiration base today.</p>
                <div style="display:flex; gap:16px; width:100%; justify-content:center;">
                    <a href="register.php" class="btn btn-primary" style="padding: 14px 32px;">Download macOS</a>
                    <a href="dashboard.php" class="btn btn-outline" style="padding: 14px 32px;">Launch Web App</a>
                </div>
            </div>
        </section>

    </main>

    <!-- ── Footer Element ──────────────────────────────────────────────────── -->
    <footer class="lp-footer">
        <div class="lp-footer-grid">
            <div class="lp-footer-brand">
                <div class="lp-footer-brand-title" style="display:flex; align-items:center; gap:8px;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                    <span>chromeVault</span>
                </div>
                <p class="lp-footer-brand-desc">A premium desktop utility and web environment for modern digital asset organization.</p>
            </div>
            
            <div class="lp-footer-column">
                <h4>Product</h4>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#plugins">Plugins</a></li>
                    <li><a href="dashboard.php">Web App</a></li>
                </ul>
            </div>

            <div class="lp-footer-column">
                <h4>Resources</h4>
                <ul>
                    <li><a href="contact.php">Help Center</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="index.php">Community</a></li>
                </ul>
            </div>

            <div class="lp-footer-column">
                <h4>Legal</h4>
                <ul>
                    <li><a href="index.php">Privacy Policy</a></li>
                    <li><a href="index.php">Terms of Use</a></li>
                    <li><a href="index.php">EULA License</a></li>
                </ul>
            </div>
        </div>

        <div class="lp-footer-bottom">
            <span>&copy; 2017-2026 OGDESIGN.INC / chromeVault. All rights reserved.</span>
            <div style="display:flex; gap:16px;">
                <a href="?lang=en" style="color:var(--text-tertiary);">English</a>
                <a href="?lang=ro" style="color:var(--text-tertiary);">Română</a>
                <a href="?lang=ru" style="color:var(--text-tertiary);">Русский</a>
            </div>
        </div>
    </footer>

    <!-- Include Javascript files -->
    <script src="js/script.js"></script>
    <script>
        // ─── Logo Hover Micro-interaction ────────────────────────────────
        const logo = document.getElementById('logo-icon');
        if (logo) {
            logo.parentNode.addEventListener('mouseenter', () => {
                logo.style.transform = 'rotate(180deg) scale(1.1)';
            });
            logo.parentNode.addEventListener('mouseleave', () => {
                logo.style.transform = 'rotate(0) scale(1)';
            });
        }

        // ─── Interactive Feature Tab Switcher ──────────────────────────
        function switchLpTab(event, panelId) {
            // Remove active class from all triggers
            const triggers = document.querySelectorAll('.lp-tab-trigger');
            triggers.forEach(t => t.classList.remove('active'));

            // Add active class to clicked trigger
            event.currentTarget.classList.add('active');

            // Hide all tab panels
            const panels = document.querySelectorAll('.lp-tab-panel');
            panels.forEach(p => p.classList.remove('active'));

            // Show selected panel
            const targetPanel = document.getElementById(panelId);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        }

        // ─── Interactive Visual Color search filter demo ────────────────
        function filterDemoColors(event, colorName) {
            // Remove active state from all swatch elements
            const swatches = document.querySelectorAll('.lp-demo-swatch');
            swatches.forEach(s => s.classList.remove('active'));

            // Add active state to selected swatch
            event.currentTarget.classList.add('active');

            // Filter demo item display opacity
            const items = document.querySelectorAll('.lp-demo-item');
            items.forEach(item => {
                if (item.dataset.color === colorName) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }
        
        // Trigger initial filtering state for color demo
        document.addEventListener('DOMContentLoaded', () => {
            const initialSwatch = document.querySelector('.lp-demo-swatch');
            if (initialSwatch) {
                initialSwatch.click();
            }
        });
    </script>
</body>
</html>

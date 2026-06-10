<?php
require_once 'php/functions.php';
if(!is_logged_in()) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('dashboard'); ?> - chromeVault</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard-body">
    <div class="app-container">
        <!-- Left Sidebar -->
        <aside class="sidebar-left">
            <div class="sidebar-header">
                <div class="app-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" style="fill: var(--bg-base); margin: 3px;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                </div>
                <span class="app-title">Design Library</span>
            </div>
            <nav class="sidebar-nav">
                <ul id="smart-folders">
                    <!-- Smart folders injected via js -->
                </ul>
                <div class="sidebar-section">
                    <div style="display:flex; justify-content:space-between; align-items:center; padding-right:12px;">
                        <h4>Folders</h4>
                        <button class="btn-icon" id="btn-add-folder" title="New folder" style="background:transparent; border:none; color:var(--text-secondary); cursor:pointer;">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                    </div>
                    <div id="new-folder-wrap" style="display:none; padding: 0 12px; margin-bottom: 8px;">
                        <input type="text" id="new-folder-input" placeholder="Folder name…" style="width:100%; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:4px; padding:4px 8px; font-size:12px;">
                    </div>
                    <ul id="user-folders">
                        <!-- User folders injected via js -->
                    </ul>
                </div>
            </nav>
            <div class="sidebar-footer" style="flex-direction: column; align-items: stretch; gap: 12px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <button id="theme-toggle-dash" class="btn-icon">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M20 8.69V4h-4.69L12 .69 8.69 4H4v4.69L.69 12 4 15.31V20h4.69L12 23.31 15.31 20H20v-4.69L23.31 12 20 8.69zM12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6zm0-10c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z"/></svg>
                    </button>
                    <div style="font-size:11px; font-weight:600; color:var(--text-secondary);">
                        <a href="?lang=ro">RO</a> &middot; <a href="?lang=en">EN</a> &middot; <a href="?lang=ru">RU</a>
                    </div>
                </div>
                <button id="btn-empty-trash" class="btn btn-danger btn-full" style="display:none; font-size:12px; padding: 6px 12px;">Empty Trash</button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="btn-icon" id="btn-back" disabled style="opacity:0.5;">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                    </button>
                    <button class="btn-icon" id="btn-fwd" disabled style="opacity:0.5;">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                    </button>
                    <span class="current-view"><?php echo t('all_items'); ?></span>
                </div>
                <div class="topbar-center">
                    <input type="range" min="1" max="5" value="3" class="zoom-slider" id="zoom-slider">
                </div>
                <div class="topbar-right">
                    <input type="text" placeholder="<?php echo t('search'); ?>" class="search-input" id="search-input">
                    <button class="btn btn-secondary" id="btn-unsplash" style="margin-right: 8px;">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" style="vertical-align:middle; margin-right:4px;"><rect x="3" y="3" width="7" height="7" rx="1" fill="currentColor"/><rect x="14" y="3" width="7" height="7" rx="1" fill="currentColor"/><rect x="3" y="14" width="7" height="7" rx="1" fill="currentColor"/><rect x="14" y="14" width="7" height="7" rx="1" fill="currentColor"/></svg>
                        Browse Unsplash
                    </button>
                    <button class="btn btn-primary" id="btn-add-item">Upload Image</button>
                    <a href="logout.php" class="btn btn-outline" style="margin-left: 10px;"><?php echo t('logout'); ?></a>
                </div>
            </header>
            
            <div class="gallery-container" id="gallery-container">
                <!-- Items will be injected here via JS -->
            </div>
        </main>

        <!-- Right Sidebar (Properties) -->
        <aside class="sidebar-right" id="properties-panel">
            <div class="prop-pin-icon" style="display: flex; justify-content: flex-end; gap: 8px;">
                <button id="btn-delete" title="Delete" style="background:none; border:none; color:var(--text-tertiary); cursor:pointer; display:none;">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                </button>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="var(--text-secondary)"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>
            </div>

            <div class="prop-empty-state" id="prop-empty">
                <p style="color:var(--text-tertiary); font-size:13px; text-align:center; padding: 40px 24px;">Select an image to view its properties</p>
            </div>
            
            <div id="prop-content" style="display:none; flex: 1; overflow-y: auto;">
                <div class="properties-preview-box">
                    <span class="type-badge" id="prop-type-badge">JPG</span>
                    <img id="prop-image" src="" alt="Preview">
                </div>

                <div class="color-palette-container" id="prop-colors">
                    <!-- swatches injected via js -->
                </div>

                <div class="properties-form">
                    <div class="form-group-dark">
                        <input type="text" id="prop-input-title" class="input-dark">
                    </div>
                    <div class="form-group-dark">
                        <textarea id="prop-input-notes" class="input-dark" placeholder="Notes..."></textarea>
                    </div>
                    <div class="form-group-dark with-icon">
                        <input type="text" id="prop-input-url" class="input-dark" readonly>
                        <button class="btn-icon-dark" title="Copy Link"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg></button>
                    </div>
                </div>

                <div class="sidebar-section-header">Tags</div>
                <div class="sidebar-section-content" id="prop-tags-container">
                    <div id="prop-tags-list" style="display:flex; flex-wrap:wrap; gap:4px; margin-bottom:8px;"></div>
                    <div class="form-group-dark" style="margin-bottom:0;">
                        <input type="text" id="prop-input-new-tag" class="input-dark" placeholder="Add tag + Enter" style="font-size:11px; padding:6px 10px;">
                    </div>
                </div>

                <div class="sidebar-section-header">Folders</div>
                <div class="sidebar-section-content" id="prop-folders-container">
                    <div id="prop-folders-list" style="display:flex; flex-wrap:wrap; gap:4px; margin-bottom:8px;"></div>
                    <div class="form-group-dark" style="margin-bottom:0;">
                        <input type="text" id="prop-input-new-folder" class="input-dark" placeholder="Add to folder + Enter" style="font-size:11px; padding:6px 10px;">
                    </div>
                </div>

                <div class="sidebar-section-header">Properties</div>
                <div class="properties-table">
                    <div class="prop-row">
                        <span class="prop-label">Rating</span>
                        <span class="prop-value rating-stars" id="prop-rating">
                            <span data-val="1">★</span><span data-val="2">★</span><span data-val="3">★</span><span data-val="4">★</span><span data-val="5">★</span>
                        </span>
                    </div>
                    <div class="prop-row">
                        <span class="prop-label">Dimensions</span>
                        <span class="prop-value" id="prop-dimensions"></span>
                    </div>
                    <div class="prop-row">
                        <span class="prop-label">Size</span>
                        <span class="prop-value" id="prop-size"></span>
                    </div>
                    <div class="prop-row">
                        <span class="prop-label">Type</span>
                        <span class="prop-value" id="prop-type"></span>
                    </div>
                    <div class="prop-row">
                        <span class="prop-label">Date Imported</span>
                        <span class="prop-value" id="prop-date-imported"></span>
                    </div>
                    <div class="prop-row">
                        <span class="prop-label">Date Created</span>
                        <span class="prop-value" id="prop-date-created"></span>
                    </div>
                    <div class="prop-row">
                        <span class="prop-label">Date Modified</span>
                        <span class="prop-value" id="prop-date-modified"></span>
                    </div>
                </div>
            </div>

            <div class="properties-footer" id="prop-footer" style="display:none; gap: 8px;">
                <button class="btn-dark-full" id="btn-export">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg> Export
                </button>
                <button class="btn-icon-round" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.05); width: 40px; height: 40px; border-radius: 50%; color:var(--text-secondary); cursor:pointer; font-weight:600; font-size:14px; display: flex; align-items: center; justify-content: center;">?</button>
            </div>
        </aside>
    </div>

    <!-- Upload Image Modal -->
    <div id="item-modal" class="modal">
        <div class="modal-content">
            <span class="close" id="close-modal">&times;</span>
            <h2 id="modal-title">Upload Image</h2>
            <form id="item-form" enctype="multipart/form-data">
                <input type="hidden" id="item-action" name="action" value="add">
                <input type="hidden" id="item-colors" name="colors" value="[]">
                <input type="hidden" id="item-last-modified" name="last_modified" value="">
                
                <div class="form-group">
                    <label for="title"><?php echo t('title'); ?></label>
                    <input type="text" id="item-title" name="title" required>
                </div>
                
                <div class="form-group" id="file-group">
                    <label>Image File</label>
                    <div class="upload-drop-zone" id="upload-drop-zone">
                        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:12px; color:var(--text-secondary);"><polyline points="16 16 12 12 8 16"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path></svg>
                        <p style="margin:0; font-weight:600; font-size:14px; color:var(--text-primary);">Click or drag to upload</p>
                        <p style="margin:4px 0 0 0; font-size:12px; color:var(--text-tertiary);">JPG, PNG, WebP</p>
                        <input type="file" id="item-file" name="image" accept="image/*" required>
                    </div>
                    <canvas id="color-canvas" style="display:none;"></canvas>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full"><?php echo t('save'); ?></button>
            </form>
        </div>
    </div>

    <!-- Unsplash API Modal -->
    <div id="api-modal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <span class="close" id="close-api-modal">&times;</span>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Browse Unsplash</h2>
                <div style="display: flex; gap: 8px;">
                    <input type="text" class="search-input" id="api-search-input" placeholder="Search imagery…" style="width: 250px;">
                    <button class="btn btn-primary" id="btn-api-search">Search</button>
                </div>
            </div>
            <div id="api-status" style="color:var(--text-secondary); text-align:center; padding:40px;">Searching…</div>
            <div id="api-grid" style="display:flex; gap:16px; margin-bottom:20px;"></div>
            <div style="text-align: center;">
                <button class="btn btn-secondary" id="btn-api-load-more" style="display:none;">Load more</button>
            </div>
        </div>
    </div>

    <!-- Context Menu -->
    <div id="context-menu" class="context-menu" style="display:none; position:absolute; z-index:1000; background:var(--bg-elevated); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:4px; box-shadow:0 4px 12px rgba(0,0,0,0.5); min-width:150px;">
        <div class="context-item" id="ctx-export-png" style="padding:8px 12px; font-size:13px; color:var(--text-primary); cursor:pointer; border-radius:4px;">Export as PNG</div>
        <div class="context-item" id="ctx-export-jpg" style="padding:8px 12px; font-size:13px; color:var(--text-primary); cursor:pointer; border-radius:4px;">Export as JPG</div>
        <div style="height:1px; background:rgba(255,255,255,0.05); margin:4px 0;"></div>
        <div class="context-item" id="ctx-trash" style="padding:8px 12px; font-size:13px; color:#ff4d4f; cursor:pointer; border-radius:4px;">Move to Trash</div>
        <div class="context-item" id="ctx-restore" style="display:none; padding:8px 12px; font-size:13px; color:var(--text-primary); cursor:pointer; border-radius:4px;">Restore</div>
    </div>

    <style>
        .context-item:hover { background:rgba(255,255,255,0.05); }
    </style>

    <script src="js/api.js"></script>
    <script src="js/script.js"></script>
    <script>document.addEventListener('DOMContentLoaded', () => { loadItems(); });</script>
</body>
</html>

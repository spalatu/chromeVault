<?php
/**
 * upload.php — ChromeVault
 * The protected Upload & Import page supporting local files and remote image URLs.
 * Runs client-side canvas color quantification and populates a hidden input.
 */

// Include auth verification guard
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/functions.php';

$userId = $_SESSION['user_id'];

$uploadError = '';
$uploadSuccess = '';

if (isset($_SESSION['upload_error'])) {
    $uploadError = $_SESSION['upload_error'];
    unset($_SESSION['upload_error']);
}

if (isset($_SESSION['upload_success'])) {
    $uploadSuccess = $_SESSION['upload_success'];
    unset($_SESSION['upload_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Image — ChromeVault</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Embedded styling to manage drop zone hover and swatches exactly as specified */
        .upload-drop-zone {
            border: 1px dashed var(--border-strong); 
            border-radius: var(--radius-xl); 
            padding: var(--space-12) var(--space-8); 
            text-align: center; 
            transition: border-color 0.15s, background 0.15s;
            cursor: pointer;
            display: block;
        }
        .upload-drop-zone:hover,
        .upload-drop-zone.drag-over {
            border-color: var(--accent); 
            background: var(--accent-muted);
        }
        .upload-tab-panel {
            display: none;
        }
        .upload-tab-panel.active {
            display: block;
        }
        .live-palette-container {
            display: none; 
            gap: var(--space-2); 
            flex-wrap: wrap; 
            margin-top: var(--space-4);
        }
        .live-palette-container.visible {
            display: flex;
        }
        .live-swatch-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--space-1);
        }
        .live-swatch-box {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--bg-elevated);
            box-shadow: 0 0 0 1px var(--border-subtle);
        }
        .live-swatch-hex {
            font-size: 11px;
            font-family: monospace;
            color: var(--text-tertiary);
        }
    </style>
</head>
<body>

    <!-- ── Header & Navigation Shell ────────────────────────────────────────── -->
    <nav class="nav">
        <a class="nav-logo" href="index.php">ChromeVault</a>
        <div class="nav-links">
            <a href="index.php" class="nav-link" data-i18n="nav_home">Home</a>
            <a href="dashboard.php" class="nav-link" data-i18n="nav_dashboard">Dashboard</a>
            <a href="gallery.php" class="nav-link" data-i18n="nav_gallery">Library</a>
            <a href="upload.php" class="nav-link active" data-i18n="nav_upload">Add Image</a>
            <a href="contact.php" class="nav-link" data-i18n="nav_contact">Contact</a>
            
            <a href="logout.php" class="nav-link nav-auth-btn" data-i18n="nav_logout">Sign Out</a>
        </div>

        <div style="margin-left: var(--space-4); display: flex; align-items: center; gap: var(--space-2);">
            <!-- Theme Toggle Button -->
            <button id="theme-toggle" class="btn btn-ghost btn-sm" aria-label="Toggle dark mode">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"/>
                    <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                </svg>
            </button>

            <!-- Language Selector -->
            <select id="lang-select" style="background: var(--bg-elevated); border: 1px solid var(--border-default); border-radius: var(--radius-sm); color: var(--text-primary); font-size: 13px; height: 28px; padding: 0 8px;">
                <option value="en">EN</option>
                <option value="ro">RO</option>
                <option value="ru">RU</option>
            </select>
        </div>
    </nav>

    <!-- ── Main Layout Wrapper ──────────────────────────────────────────────── -->
    <div class="container">
        
        <header class="page-header">
            <h1 class="page-title" data-i18n="upload_title">Upload image</h1>
            <p class="page-subtitle">Add a new image to your library</p>
        </header>

        <!-- System feedback alerts -->
        <?php if ($uploadError !== ''): ?>
            <div style="background: var(--danger-muted); color: var(--danger); border: 1px solid rgba(220, 38, 38, 0.2); padding: var(--space-3); border-radius: var(--radius-md); font-size: 13px; margin-bottom: var(--space-4);">
                <?php echo htmlspecialchars($uploadError); ?>
            </div>
        <?php endif; ?>

        <?php if ($uploadSuccess !== ''): ?>
            <div style="background: var(--success-muted); color: var(--success); border: 1px solid rgba(22, 163, 74, 0.2); padding: var(--space-3); border-radius: var(--radius-md); font-size: 13px; margin-bottom: var(--space-4);">
                <?php echo htmlspecialchars($uploadSuccess); ?>
            </div>
        <?php endif; ?>

        <!-- ── Main Add/Import Form ────────────────────────────────────────────── -->
        <form id="upload-form" method="POST" action="php/saveData.php" enctype="multipart/form-data" novalidate style="margin-bottom: var(--space-12);">
            <input type="hidden" name="source_type" id="source-type" value="file">
            <input type="hidden" name="palette" id="palette-input" value="[]">

            <!-- ── Tabs Switcher Row ────────────────────────────────────────────── -->
            <div style="display: flex; gap: var(--space-2); padding-bottom: var(--space-4); border-bottom: 1px solid var(--border-subtle); margin-bottom: var(--space-6);">
                <button type="button" class="btn btn-ghost btn-sm" id="tab-file-btn" data-i18n="upload_tab_file">Upload File</button>
                <button type="button" class="btn btn-ghost btn-sm" id="tab-url-btn" data-i18n="upload_tab_url">From URL</button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--space-6);">
                
                <!-- ── Column 1: Source Selection & Preview ──────────────────────── -->
                <div class="card" style="display: flex; flex-direction: column; gap: var(--space-4);">
                    
                    <!-- File Upload Panel -->
                    <div id="panel-file" class="upload-tab-panel active">
                        <label for="file-input" class="upload-drop-zone" id="drop-zone">
                            <span style="font-size: 28px; display: block; margin-bottom: var(--space-2);" aria-hidden="true">📤</span>
                            <span style="font-size: 15px; font-weight: 500; color: var(--text-primary); display: block;" data-i18n="upload_drop_zone">
                                Drag and drop your image here, or <strong>browse</strong>
                            </span>
                            <span style="font-size: 14px; color: var(--text-secondary); display: block; margin-top: var(--space-1);" data-i18n="upload_drop_sub">
                                Supports JPG, PNG, WEBP, GIF
                            </span>
                            <input type="file" name="image_file" id="file-input" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">
                        </label>
                    </div>

                    <!-- URL Remote Link Panel -->
                    <div id="panel-url" class="upload-tab-panel">
                        <div class="field">
                            <label class="label" for="url-input" data-i18n="upload_tab_url">From URL</label>
                            <input class="input" type="url" name="image_url" id="url-input" placeholder="Paste image URL..." data-i18n="upload_url_placeholder">
                        </div>
                    </div>

                    <!-- Image Preview -->
                    <div style="width: 100%; border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--border-subtle); display: none;" id="preview-wrapper">
                        <img id="image-preview" style="aspect-ratio: 3/2; width: 100%; object-fit: cover; display: block;" alt="Upload preview" crossorigin="anonymous">
                    </div>

                    <!-- Dynamic Color Swatches Row (Hidden initially) -->
                    <div>
                        <div style="font-size: 12px; color: var(--danger); margin-bottom: var(--space-2); display: none;" id="cors-warning"></div>
                        <div class="live-palette-container" id="palette-preview-container"></div>
                    </div>
                </div>

                <!-- ── Column 2: Metadata Fields ─────────────────────────────────── -->
                <div class="card" style="display: flex; flex-direction: column; gap: var(--space-4); height: fit-content;">
                    <div class="field">
                        <label class="label" for="title-input" data-i18n="field_title">Title</label>
                        <input class="input" type="text" name="title" id="title-input" placeholder="e.g. Minimalist Workspace" required>
                    </div>

                    <div class="field">
                        <label class="label" for="collection-input" data-i18n="field_collection">Collection</label>
                        <input class="input" type="text" name="collection" id="collection-input" placeholder="e.g. Workspaces" required>
                    </div>

                    <div class="field">
                        <label class="label" for="tags-input" data-i18n="field_tags">Tags (comma separated)</label>
                        <input class="input" type="text" name="tags" id="tags-input" placeholder="e.g. cozy, desk, darkmode">
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: var(--space-4);" data-i18n="btn_upload_submit">
                        Add to Vault
                    </button>
                </div>
                
            </div>
        </form>
        
    </div>

    <!-- Include Javascript files -->
    <script src="js/colorExtract.js"></script>
    <script src="js/script.js"></script>

    <script>
        // Form & Panel elements
        const uploadForm = document.getElementById('upload-form');
        const fileTabBtn = document.getElementById('tab-file-btn');
        const urlTabBtn = document.getElementById('tab-url-btn');
        const filePanel = document.getElementById('panel-file');
        const urlPanel = document.getElementById('panel-url');
        const sourceTypeInput = document.getElementById('source-type');
        const paletteInput = document.getElementById('palette-input');

        const fileInput = document.getElementById('file-input');
        const urlInput = document.getElementById('url-input');
        const imagePreview = document.getElementById('image-preview');
        const previewWrapper = document.getElementById('preview-wrapper');
        const paletteContainer = document.getElementById('palette-preview-container');
        const corsWarning = document.getElementById('cors-warning');
        const dropZone = document.getElementById('drop-zone');

        // Set initial active state correctly for tabs
        fileTabBtn.style.background = 'var(--bg-elevated)';
        fileTabBtn.style.color = 'var(--text-primary)';

        // ── Tab Navigation Mechanics ─────────────────────────────────────────
        fileTabBtn.addEventListener('click', () => {
            fileTabBtn.style.background = 'var(--bg-elevated)';
            fileTabBtn.style.color = 'var(--text-primary)';
            urlTabBtn.style.background = 'transparent';
            urlTabBtn.style.color = 'var(--text-secondary)';
            
            filePanel.classList.add('active');
            urlPanel.classList.remove('active');
            sourceTypeInput.value = 'file';
            clearPreview();
        });

        urlTabBtn.addEventListener('click', () => {
            urlTabBtn.style.background = 'var(--bg-elevated)';
            urlTabBtn.style.color = 'var(--text-primary)';
            fileTabBtn.style.background = 'transparent';
            fileTabBtn.style.color = 'var(--text-secondary)';
            
            urlPanel.classList.add('active');
            filePanel.classList.remove('active');
            sourceTypeInput.value = 'url';
            clearPreview();
        });

        function clearPreview() {
            imagePreview.src = '';
            previewWrapper.style.display = 'none';
            paletteContainer.innerHTML = '';
            paletteContainer.classList.remove('visible');
            paletteInput.value = '[]';
            corsWarning.textContent = '';
            corsWarning.style.display = 'none';
        }

        // ── Local File Preview & Extraction ──────────────────────────────────
        fileInput.addEventListener('change', (e) => {
            const file = e.target.value ? e.target.files[0] : null;
            if (file) {
                const objectUrl = URL.createObjectURL(file);
                processImageSrc(objectUrl);
                
                const titleInput = document.getElementById('title-input');
                if (titleInput && !titleInput.value) {
                    titleInput.value = file.name.replace(/\.[^.]+$/, '').replace(/[-_]/g, ' ');
                }
            }
        });

        // Drag & Drop visual indicators
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('drag-over');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                const file = e.dataTransfer.files[0];
                const objectUrl = URL.createObjectURL(file);
                processImageSrc(objectUrl);
                
                const titleInput = document.getElementById('title-input');
                if (titleInput && !titleInput.value) {
                    titleInput.value = file.name.replace(/\.[^.]+$/, '').replace(/[-_]/g, ' ');
                }
            }
        });

        // ── Remote URL Preview & Extraction ──────────────────────────────────
        urlInput.addEventListener('input', () => {
            const url = urlInput.value.trim();
            if (url !== '' && /^(https?:\/\/|\/)/i.test(url)) {
                processImageSrc(url);
            }
        });

        // Process image source, load to canvas, and run extractColors
        function processImageSrc(src) {
            clearPreview();
            corsWarning.textContent = '';
            corsWarning.style.display = 'none';

            // Show dynamic loading indicator
            imagePreview.src = src;
            previewWrapper.style.display = 'block';

            imagePreview.onload = () => {
                try {
                    extractColors(imagePreview, (colors) => {
                        if (colors && colors.length > 0) {
                            renderPalettePreviewLocal(colors, paletteContainer);
                            paletteInput.value = JSON.stringify(colors);
                        } else {
                            handleCORSFallback();
                        }
                    });
                } catch (err) {
                    console.error("Color extraction failed due to canvas security:", err);
                    handleCORSFallback();
                }
            };

            imagePreview.onerror = () => {
                showToast('Failed to load image preview. Please check link/file.', 'error');
                clearPreview();
            };
        }

        // Clean fallback in case image domain blocks cross-origin reading (CORS)
        function handleCORSFallback() {
            corsWarning.textContent = 'Note: CORS restrictions active. Used fallback palette.';
            corsWarning.style.display = 'block';
            
            // Standard curated SwiftUI-like colorful palette fallback
            const fallbackColors = ['#d97706', '#16a34a', '#dc2626', '#2563eb', '#7c3aed'];
            renderPalettePreviewLocal(fallbackColors, paletteContainer);
            paletteInput.value = JSON.stringify(fallbackColors);
        }

        // Custom local renderer matching required swatch dimensions (36px x 36px)
        function renderPalettePreviewLocal(colors, container) {
            container.innerHTML = '';
            colors.forEach(hex => {
                const item = document.createElement('div');
                item.className = 'live-swatch-item';

                const dot = document.createElement('div');
                dot.className = 'live-swatch-box';
                dot.style.backgroundColor = hex;
                dot.title = hex;

                const label = document.createElement('span');
                label.className = 'live-swatch-hex';
                label.textContent = hex;

                item.appendChild(dot);
                item.appendChild(label);
                container.appendChild(item);
            });
            container.classList.add('visible');
        }

        // Setup validation rules
        setupFormValidation(uploadForm, {
            title: {
                required: true,
                requiredMsg: 'Title is required'
            },
            collection: {
                required: true,
                requiredMsg: 'Collection name is required'
            },
            image_file: {
                fileExt: ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                fileExtMsg: 'Supported: JPG, PNG, WEBP, GIF'
            }
        });
    </script>
</body>
</html>

document.addEventListener('DOMContentLoaded', () => {
    
    // ─── Theme Toggle ───────────────────────────────────────────
    const themeToggleBtn = document.getElementById('theme-toggle') || document.getElementById('theme-toggle-dash');
    const currentTheme = localStorage.getItem('theme') || 'dark';
    
    if (currentTheme === 'light') {
        document.documentElement.classList.remove('dark');
    } else {
        document.documentElement.classList.add('dark');
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
        });
    }

    // ─── Auth Forms ─────────────────────────────────────────────
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(registerForm);
            fetch('php/auth.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                showMessage('message', data.success, data.message);
                if (data.success) setTimeout(() => window.location.href = 'login.php', 1500);
            });
        });
    }

    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);
            fetch('php/auth.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                showMessage('message', data.success, data.message);
                if (data.success) setTimeout(() => window.location.href = 'dashboard.php', 1000);
            });
        });
    }

    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            showMessage('contact-message', true, 'Message sent successfully!');
            contactForm.reset();
        });
    }

    function showMessage(id, success, text) {
        const el = document.getElementById(id);
        if (!el) return;
        el.className = 'message ' + (success ? 'success' : 'error');
        el.textContent = text;
    }

    // ─── Color Extraction (K-Means-Like Dominant Colors) ────────
    function extractDominantColors(file, count = 10) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.getElementById('color-canvas');
                    if (!canvas) { resolve([]); return; }
                    const ctx = canvas.getContext('2d');
                    
                    // Downscale for performance
                    const maxDim = 100;
                    const scale = Math.min(maxDim / img.width, maxDim / img.height, 1);
                    const w = Math.floor(img.width * scale);
                    const h = Math.floor(img.height * scale);
                    canvas.width = w;
                    canvas.height = h;
                    ctx.drawImage(img, 0, 0, w, h);
                    
                    const data = ctx.getImageData(0, 0, w, h).data;
                    const pixels = [];
                    
                    // Sample every 4th pixel for speed
                    for (let i = 0; i < data.length; i += 16) {
                        const r = data[i], g = data[i+1], b = data[i+2];
                        // Skip near-white and near-black (noise)
                        const brightness = (r + g + b) / 3;
                        if (brightness > 10 && brightness < 245) {
                            pixels.push([r, g, b]);
                        }
                    }
                    
                    if (pixels.length === 0) {
                        // All pixels were filtered, just sample raw
                        for (let i = 0; i < data.length; i += 16) {
                            pixels.push([data[i], data[i+1], data[i+2]]);
                        }
                    }
                    
                    // Simple k-means clustering
                    const colors = kMeansColors(pixels, count);
                    resolve(colors.map(c => rgbToHexStr(c[0], c[1], c[2])));
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    function kMeansColors(pixels, k) {
        if (pixels.length <= k) return pixels;
        
        // Init centroids by sampling evenly
        let centroids = [];
        const step = Math.floor(pixels.length / k);
        for (let i = 0; i < k; i++) {
            centroids.push([...pixels[i * step]]);
        }
        
        // Run 10 iterations
        for (let iter = 0; iter < 10; iter++) {
            const clusters = Array.from({ length: k }, () => []);
            
            // Assign each pixel to nearest centroid
            for (const px of pixels) {
                let minDist = Infinity;
                let minIdx = 0;
                for (let c = 0; c < k; c++) {
                    const dr = px[0] - centroids[c][0];
                    const dg = px[1] - centroids[c][1];
                    const db = px[2] - centroids[c][2];
                    const dist = dr*dr + dg*dg + db*db;
                    if (dist < minDist) { minDist = dist; minIdx = c; }
                }
                clusters[minIdx].push(px);
            }
            
            // Recalculate centroids
            for (let c = 0; c < k; c++) {
                if (clusters[c].length === 0) continue;
                const sum = [0, 0, 0];
                for (const px of clusters[c]) {
                    sum[0] += px[0]; sum[1] += px[1]; sum[2] += px[2];
                }
                centroids[c] = [
                    Math.round(sum[0] / clusters[c].length),
                    Math.round(sum[1] / clusters[c].length),
                    Math.round(sum[2] / clusters[c].length)
                ];
            }
        }
        
        // Sort by brightness (dark to light)
        centroids.sort((a, b) => (a[0]+a[1]+a[2]) - (b[0]+b[1]+b[2]));
        return centroids;
    }
    
    function rgbToHexStr(r, g, b) {
        return '#' + [r, g, b].map(x => x.toString(16).padStart(2, '0')).join('');
    }

    // ─── Modal Logic ────────────────────────────────────────────
    const modal = document.getElementById('item-modal');
    const btnAdd = document.getElementById('btn-add-item');
    const closeBtn = document.getElementById('close-modal');
    const itemForm = document.getElementById('item-form');
    const fileInput = document.getElementById('item-file');

    function openModal() {
        if (!modal) return;
        modal.style.display = 'flex';
        requestAnimationFrame(() => modal.classList.add('show'));
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.remove('show');
        modal.style.display = 'none';
        
        // Reset upload drop zone label & styling to original state
        const nameEl = dropZoneUpload?.querySelector('p');
        if (nameEl) nameEl.textContent = 'Click or drag to upload';
        if (dropZoneUpload) {
            dropZoneUpload.style.borderColor = '';
            dropZoneUpload.style.background = '';
        }
    }

    if (modal && btnAdd) {
        btnAdd.onclick = () => {
            if (itemForm) itemForm.reset();
            openModal();
        };
        if (closeBtn) closeBtn.onclick = closeModal;
        window.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    }

    // ─── Upload: title autofill & drop zone interactions ───────
    const dropZoneUpload = document.getElementById('upload-drop-zone');
    if (fileInput) {
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const titleInput = document.getElementById('item-title');
                if (titleInput && !titleInput.value) {
                    titleInput.value = file.name.replace(/\.[^.]+$/, '').replace(/[-_]/g, ' ');
                }
                const nameEl = dropZoneUpload?.querySelector('p');
                if (nameEl) nameEl.textContent = file.name;
                if (dropZoneUpload) {
                    dropZoneUpload.style.borderColor = 'var(--text-secondary)';
                    dropZoneUpload.style.background = 'var(--bg-elevated)';
                }
            }
        });
    }

    if (dropZoneUpload) {
        dropZoneUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZoneUpload.classList.add('drag-over');
        });
        dropZoneUpload.addEventListener('dragleave', () => dropZoneUpload.classList.remove('drag-over'));
        dropZoneUpload.addEventListener('drop', () => dropZoneUpload.classList.remove('drag-over'));
    }

    if (itemForm) {
        itemForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = itemForm.querySelector('[type="submit"]');
            if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Uploading…'; }
            const formData = new FormData(itemForm);

            if (fileInput && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                formData.set('last_modified', file.lastModified);
                try {
                    const colors = await extractDominantColors(file, 10);
                    formData.set('colors', JSON.stringify(colors));
                } catch (err) {
                    console.error('Color extraction failed', err);
                }
            }

            fetch('php/save_data.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    if (data.item && window.currentItems) {
                        currentItems.unshift(data.item);
                        applyFiltersAndRender();
                    } else {
                        loadItems();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .finally(() => {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Save'; }
            });
        });
    }

    // ─── Folders Logic ───────────────────────────────────────────
    const btnAddFolder = document.getElementById('btn-add-folder');
    const folderInputWrap = document.getElementById('new-folder-wrap');
    const folderInput = document.getElementById('new-folder-input');

    if (btnAddFolder && folderInputWrap) {
        btnAddFolder.addEventListener('click', () => {
            if (folderInputWrap.style.display === 'none') {
                folderInputWrap.style.display = 'block';
                folderInput.focus();
            } else {
                folderInputWrap.style.display = 'none';
            }
        });
    }

    if (folderInput) {
        folderInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const name = folderInput.value.trim();
                if (name) {
                    const fd = new FormData();
                    fd.append('action', 'add');
                    fd.append('name', name);
                    fetch('php/folders.php', { method: 'POST', body: fd })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            folderInput.value = '';
                            folderInputWrap.style.display = 'none';
                            loadItems();
                        } else {
                            alert(d.message);
                        }
                    });
                }
            }
        });
    }

    // ─── Delete Item ────────────────────────────────────────────
    const btnDelete = document.getElementById('btn-delete');
    if (btnDelete) {
        btnDelete.addEventListener('click', () => {
            if (!confirm('Are you sure you want to delete this item?')) return;
            const id = btnDelete.dataset.id;
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            fetch('php/save_data.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    clearSelection();
                    loadItems();
                }
            });
        });
    }

    // ─── Export Button (Downloads the Image) ────────────────────
    const btnExport = document.getElementById('btn-export');
    if (btnExport) {
        btnExport.addEventListener('click', () => {
            const img = document.getElementById('prop-image');
            if (img && img.src) {
                const a = document.createElement('a');
                a.href = img.src;
                a.download = (document.getElementById('prop-input-title')?.value || 'image') + '.jpg';
                a.click();
            }
        });
    }

    // ─── Inline Edits (Title + Notes from sidebar) ──────────────
    const propTitle = document.getElementById('prop-input-title');
    const propNotes = document.getElementById('prop-input-notes');

    function saveInlineEdit() {
        const id = btnDelete?.dataset.id;
        if (!id) return;
        const formData = new FormData();
        formData.append('action', 'edit');
        formData.append('id', id);
        if (propTitle) formData.append('title', propTitle.value);
        if (propNotes) formData.append('notes', propNotes.value);

        fetch('php/save_data.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) loadItems();
        });
    }

    if (propTitle) propTitle.addEventListener('change', saveInlineEdit);
    if (propNotes) propNotes.addEventListener('change', saveInlineEdit);

    function updateItemTags(id, tags) {
        const fd = new FormData();
        fd.append('action', 'edit_tags');
        fd.append('id', id);
        fd.append('tags', JSON.stringify(tags));
        fetch('php/save_data.php', { method: 'POST', body: fd }).then(r=>r.json()).then(d=>{
            if (d.success) loadItems();
        });
    }

    function updateItemFolders(id, folders) {
        const fd = new FormData();
        fd.append('action', 'edit_folders');
        fd.append('id', id);
        fd.append('folders', JSON.stringify(folders));
        fetch('php/save_data.php', { method: 'POST', body: fd }).then(r=>r.json()).then(d=>{
            if (d.success) loadItems();
        });
    }

    window.removeTag = function(tag) {
        if (!selectedItemId) return;
        const item = currentItems.find(i => i.id === selectedItemId);
        if (item) {
            const tags = (item.tags || []).filter(t => t !== tag);
            updateItemTags(item.id, tags);
        }
    };

    window.removeFolder = function(folder) {
        if (!selectedItemId) return;
        const item = currentItems.find(i => i.id === selectedItemId);
        if (item) {
            const flds = (item.folders || []).filter(f => f !== folder);
            updateItemFolders(item.id, flds);
        }
    };

    // ─── Zoom Slider ────────────────────────────────────────────
    const zoomSlider = document.getElementById('zoom-slider');
    if (zoomSlider) {
        zoomSlider.addEventListener('input', (e) => {
            const val = e.target.value;
            const container = document.getElementById('gallery-container');
            if (!container) return;
            const size = 150 + (val * 35);
            container.style.gridTemplateColumns = `repeat(auto-fill, minmax(${size}px, 1fr))`;
            container.style.gridAutoRows = `${size}px`;
        });
    }

    // ─── Search Input ───────────────────────────────────────────
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const filtered = currentItems.filter(item => item.title.toLowerCase().includes(term));
            renderItems(filtered);
        });
    }

    // ─── Rating Stars — handled via event delegation in renderStars() ──

    // ─── Tooltip ────────────────────────────────────────────────
    const tooltip = document.createElement('div');
    tooltip.className = 'color-tooltip';
    tooltip.style.display = 'none';
    tooltip.style.position = 'fixed';
    tooltip.style.background = 'var(--bg-elevated)';
    tooltip.style.color = 'var(--text-primary)';
    tooltip.style.padding = '4px 8px';
    tooltip.style.borderRadius = '4px';
    tooltip.style.fontSize = '11px';
    tooltip.style.border = '1px solid var(--border-light)';
    tooltip.style.zIndex = '1000';
    document.body.appendChild(tooltip);

    // ─── History Navigation ──────────────────────────────────────
    const btnBack = document.getElementById('btn-back');
    const btnFwd = document.getElementById('btn-fwd');
    if (btnBack) btnBack.addEventListener('click', () => {
        if (navIndex > 0) {
            navIndex--;
            navigateToFilter(navHistory[navIndex], false);
        }
    });
    if (btnFwd) btnFwd.addEventListener('click', () => {
        if (navIndex < navHistory.length - 1) {
            navIndex++;
            navigateToFilter(navHistory[navIndex], false);
        }
    });

    // ─── Keyboard Shortcuts ─────────────────────────────────────
    document.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        if ((e.key === 'Delete' || e.key === 'Backspace') && selectedItemId) {
            e.preventDefault();
            updateItemTrashState(selectedItemId, true);
        }
        if (e.key === 'Escape') {
            clearSelection();
        }
    });

    // ─── Empty Trash ────────────────────────────────────────────
    const btnEmptyTrash = document.getElementById('btn-empty-trash');
    if (btnEmptyTrash) {
        btnEmptyTrash.addEventListener('click', () => {
            if (confirm('Permanently delete all trashed items?')) {
                const fd = new FormData();
                fd.append('action', 'empty_trash');
                fetch('php/save_data.php', { method: 'POST', body: fd })
                .then(r => r.json()).then(d => {
                    if (d.success) loadItems();
                });
            }
        });
    }

    // ─── Tags & Folders Inputs ──────────────────────────────────
    const tagInput = document.getElementById('prop-input-new-tag');
    if (tagInput) tagInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && tagInput.value.trim() && selectedItemId) {
            const item = currentItems.find(i => i.id === selectedItemId);
            if (item) {
                const tags = item.tags || [];
                if (!tags.includes(tagInput.value.trim())) {
                    tags.push(tagInput.value.trim());
                    updateItemTags(item.id, tags);
                }
                tagInput.value = '';
            }
        }
    });

    const folderInputUI = document.getElementById('prop-input-new-folder');
    if (folderInputUI) folderInputUI.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && folderInputUI.value.trim() && selectedItemId) {
            const item = currentItems.find(i => i.id === selectedItemId);
            if (item) {
                const flds = item.folders || [];
                if (!flds.includes(folderInputUI.value.trim())) {
                    flds.push(folderInputUI.value.trim());
                    updateItemFolders(item.id, flds);
                }
                folderInputUI.value = '';
            }
        }
    });
});

// ─── Global State ───────────────────────────────────────────────
let currentItems = [];
let currentFolders = [];
let selectedItemId = null;
let currentFilter = 'all';
let navHistory = ['all'];
let navIndex = 0;

// Icons for sidebar
const ICONS = {
    all: '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M3 3h8v8H3zm0 10h8v8H3zM13 3h8v8h-8zm0 10h8v8h-8z"/></svg>',
    uncategorized: '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>',
    untagged: '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/></svg>',
    trash: '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>',
    folder: '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>'
};

// ─── Load Items & Folders ───────────────────────────────────────
function loadFolders() {
    fetch('php/folders.php')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            currentFolders = data.folders;
            renderLeftSidebar();
        }
    });
}

function loadItems() {
    loadFolders();
    const container = document.getElementById('gallery-container');
    if (!container) return;

    fetch('php/save_data.php?action=list&_t=' + Date.now())
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            currentItems = data.items;
            applyFiltersAndRender();
            if (selectedItemId) {
                const item = currentItems.find(i => i.id === selectedItemId);
                if (item) {
                    const el = document.querySelector(`.gallery-item[data-id="${item.id}"]`);
                    if (el) selectItem(item, el);
                }
            }
            updateNavButtons();
        }
    });
}

function navigateToFilter(filter, pushState = true) {
    if (pushState && filter !== currentFilter) {
        navHistory = navHistory.slice(0, navIndex + 1);
        navHistory.push(filter);
        navIndex++;
    }
    currentFilter = filter;
    applyFiltersAndRender();
    updateNavButtons();
}

function updateNavButtons() {
    const btnBack = document.getElementById('btn-back');
    const btnFwd = document.getElementById('btn-fwd');
    if (btnBack) {
        btnBack.disabled = navIndex <= 0;
        btnBack.style.opacity = btnBack.disabled ? '0.5' : '1';
    }
    if (btnFwd) {
        btnFwd.disabled = navIndex >= navHistory.length - 1;
        btnFwd.style.opacity = btnFwd.disabled ? '0.5' : '1';
    }

    const btnEmptyTrash = document.getElementById('btn-empty-trash');
    if (btnEmptyTrash) {
        btnEmptyTrash.style.display = currentFilter === 'trash' ? 'block' : 'none';
    }
}

function applyFiltersAndRender() {
    let filtered = currentItems;
    
    // Default: don't show trashed unless in trash
    if (currentFilter === 'trash') {
        filtered = filtered.filter(i => i.trashed === true);
    } else {
        filtered = filtered.filter(i => !i.trashed);
        
        if (currentFilter === 'uncategorized') {
            filtered = filtered.filter(i => !i.folders || i.folders.length === 0);
        } else if (currentFilter === 'untagged') {
            filtered = filtered.filter(i => !i.tags || i.tags.length === 0);
        } else if (currentFilter !== 'all') {
            filtered = filtered.filter(i => i.folders && i.folders.includes(currentFilter));
        }
    }

    renderItems(filtered);
    renderLeftSidebar(); // update counts
}

function renderLeftSidebar() {
    const smartFoldersContainer = document.getElementById('smart-folders');
    const userFoldersContainer = document.getElementById('user-folders');
    if (!smartFoldersContainer || !userFoldersContainer) return;

    // Smart Folders
    const smart = [
        { id: 'all', label: 'All Items', icon: ICONS.all },
        { id: 'uncategorized', label: 'Uncategorized', icon: ICONS.uncategorized },
        { id: 'untagged', label: 'Untagged', icon: ICONS.untagged },
        { id: 'trash', label: 'Trash', icon: ICONS.trash }
    ];

    smartFoldersContainer.innerHTML = '';
    smart.forEach(f => {
        let li = document.createElement('li');
        if (currentFilter === f.id) li.className = 'active';
        li.innerHTML = f.icon + ' ' + f.label;
        li.onclick = () => { navigateToFilter(f.id); document.querySelector('.current-view').textContent = f.label; };
        smartFoldersContainer.appendChild(li);
    });

    // User Folders
    userFoldersContainer.innerHTML = '';
    currentFolders.forEach(f => {
        let li = document.createElement('li');
        if (currentFilter === f.name) li.className = 'active';
        li.style.display = 'flex';
        li.style.justifyContent = 'space-between';
        
        let labelSpan = document.createElement('span');
        labelSpan.innerHTML = ICONS.folder + ' ' + f.name;
        
        let delBtn = document.createElement('span');
        delBtn.innerHTML = '×';
        delBtn.style.cursor = 'pointer';
        delBtn.style.opacity = '0.5';
        delBtn.onclick = (e) => {
            e.stopPropagation();
            if (confirm(`Delete folder "${f.name}"?`)) {
                const fd = new FormData();
                fd.append('action', 'delete');
                fd.append('id', f.id);
                fetch('php/folders.php', { method:'POST', body: fd })
                .then(r => r.json()).then(d => { if(d.success) { if(currentFilter === f.name) currentFilter='all'; loadItems(); } });
            }
        };

        li.appendChild(labelSpan);
        li.appendChild(delBtn);
        li.onclick = () => { navigateToFilter(f.name); document.querySelector('.current-view').textContent = f.name; };
        userFoldersContainer.appendChild(li);
    });
}

function renderItems(items) {
    const container = document.getElementById('gallery-container');
    if (!container) return;
    container.innerHTML = '';

    if (items.length === 0) {
        container.innerHTML = '<p style="color:var(--text-tertiary); text-align:center; width:100%; padding-top:80px; font-size:14px;">No items yet. Upload an image to begin.</p>';
        return;
    }

    items.forEach((item, index) => {
        const div = document.createElement('div');
        div.className = 'gallery-item';
        div.dataset.id = item.id;
        div.style.animationDelay = `${index * 0.04}s`;
        div.onclick = () => selectItem(item, div);
        
        div.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            selectItem(item, div);
            showContextMenu(e, item);
        });

        const img = document.createElement('div');
        img.className = 'item-img';
        img.style.backgroundImage = `url('${item.url}')`;

        const info = document.createElement('div');
        info.className = 'item-info';
        info.textContent = item.title;

        div.appendChild(img);
        div.appendChild(info);
        container.appendChild(div);
    });
}

// ─── Select Item ────────────────────────────────────────────────
function selectItem(item, el) {
    selectedItemId = item.id;
    document.querySelectorAll('.gallery-item').forEach(i => i.classList.remove('selected'));
    el.classList.add('selected');

    // Hide empty state, show content
    const emptyState = document.getElementById('prop-empty');
    if (emptyState) emptyState.style.display = 'none';

    // Image preview
    const img = document.getElementById('prop-image');
    if (img) img.src = item.url;

    // Inputs
    const titleInput = document.getElementById('prop-input-title');
    if (titleInput) titleInput.value = item.title || '';

    const notesInput = document.getElementById('prop-input-notes');
    if (notesInput) notesInput.value = item.notes || '';

    const urlInput = document.getElementById('prop-input-url');
    if (urlInput) urlInput.value = window.location.origin + '/' + item.url;

    // Color palette
    const colorsContainer = document.getElementById('prop-colors');
    if (colorsContainer) {
        colorsContainer.innerHTML = '';
        const colors = (item.colors && item.colors.length > 0) ? item.colors : [];
        if (colors.length > 0) {
            colors.forEach(hex => {
                const swatch = document.createElement('div');
                swatch.className = 'swatch';
                swatch.style.backgroundColor = hex;
                swatch.style.cursor = 'pointer';
                swatch.style.transition = 'transform 0.2s, box-shadow 0.2s';

                swatch.addEventListener('mouseenter', (e) => {
                    swatch.style.transform = 'scale(1.25)';
                    swatch.style.boxShadow = '0 4px 12px rgba(0,0,0,0.4)';
                    const t = document.querySelector('.color-tooltip');
                    if(t) { t.style.display='block'; t.textContent=hex; t.style.left=(e.pageX+14)+'px'; t.style.top=(e.pageY+14)+'px'; }
                });
                swatch.addEventListener('mouseleave', () => {
                    swatch.style.transform = '';
                    swatch.style.boxShadow = '';
                    const t = document.querySelector('.color-tooltip');
                    if(t) t.style.display='none';
                });
                swatch.addEventListener('click', () => {
                    navigator.clipboard.writeText(hex).then(() => {
                        const t = document.querySelector('.color-tooltip');
                        if (t) {
                            const prev = t.textContent;
                            t.textContent = 'Copied!';
                            t.style.background = '#10b981';
                            t.style.color = '#fff';
                            t.style.border = '1px solid #10b981';
                            setTimeout(() => {
                                t.textContent = prev;
                                t.style.background = '';
                                t.style.color = '';
                                t.style.border = '';
                            }, 1200);
                        }
                    });
                });
                colorsContainer.appendChild(swatch);
            });
        }
    }

    // Render tags and folders arrays
    const tagsList = document.getElementById('prop-tags-list');
    if (tagsList) {
        tagsList.innerHTML = '';
        (item.tags || []).forEach(tag => {
            const el = document.createElement('span');
            el.className = 'tag';
            el.innerHTML = tag + ' <span style="cursor:pointer;margin-left:4px;" onclick="removeTag(\''+tag+'\')">×</span>';
            tagsList.appendChild(el);
        });
    }

    const foldersList = document.getElementById('prop-folders-list');
    if (foldersList) {
        foldersList.innerHTML = '';
        (item.folders || []).forEach(folder => {
            const el = document.createElement('span');
            el.className = 'tag';
            el.innerHTML = folder + ' <span style="cursor:pointer;margin-left:4px;" onclick="removeFolder(\''+folder+'\')">×</span>';
            foldersList.appendChild(el);
        });
    }

    // Type badge
    const badge = document.getElementById('prop-type-badge');
    if (badge) badge.textContent = item.type || 'IMG';

    // Properties table
    setTextById('prop-dimensions', item.dimensions || '-');
    setTextById('prop-size', item.size || '-');
    setTextById('prop-type', item.type || '-');
    setTextById('prop-date-imported', item.date_imported || '-');
    setTextById('prop-date-created', item.date_created || '-');
    setTextById('prop-date-modified', item.date_modified || '-');

    // Rating stars
    renderStars(item.rating || 0);

    // Delete button data
    const btnDel = document.getElementById('btn-delete');
    if (btnDel) btnDel.dataset.id = item.id;

    // Show panels
    const content = document.getElementById('prop-content');
    if (content) content.style.display = 'block';
    const footer = document.getElementById('prop-footer');
    if (footer) footer.style.display = 'flex';
    if (btnDel) btnDel.style.display = 'block';
}

function clearSelection() {
    selectedItemId = null;
    const emptyState = document.getElementById('prop-empty');
    if (emptyState) emptyState.style.display = 'block';
    const content = document.getElementById('prop-content');
    if (content) content.style.display = 'none';
    const footer = document.getElementById('prop-footer');
    if (footer) footer.style.display = 'none';
    const btnDel = document.getElementById('btn-delete');
    if (btnDel) btnDel.style.display = 'none';
}

function setTextById(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
}

function renderStars(rating, skipSave) {
    const ratingEl = document.getElementById('prop-rating');
    if (!ratingEl) return;

    ratingEl.innerHTML = '';
    for (let i = 1; i <= 5; i++) {
        const star = document.createElement('span');
        star.dataset.val = i;
        star.textContent = '★';
        star.style.color = i <= rating ? '#FFD700' : 'var(--text-tertiary)';
        star.style.textShadow = i <= rating ? '0 0 10px rgba(255,215,0,0.5)' : 'none';
        star.style.fontSize = '22px';
        star.style.padding = '0 3px';
        star.style.cursor = 'pointer';
        star.style.display = 'inline-block';
        star.style.transition = 'transform 0.15s, color 0.15s';

        star.addEventListener('mouseenter', () => {
            const val = parseInt(star.dataset.val);
            ratingEl.querySelectorAll('span').forEach(s => {
                const sv = parseInt(s.dataset.val);
                s.style.color = sv <= val ? '#FFD700' : 'var(--text-tertiary)';
                s.style.transform = sv === val ? 'scale(1.3)' : '';
            });
        });
        star.addEventListener('mouseleave', () => {
            const cur = parseInt(ratingEl.dataset.rating || 0);
            ratingEl.querySelectorAll('span').forEach(s => {
                const sv = parseInt(s.dataset.val);
                s.style.color = sv <= cur ? '#FFD700' : 'var(--text-tertiary)';
                s.style.transform = '';
            });
        });
        star.addEventListener('click', () => {
            const val = parseInt(star.dataset.val);
            ratingEl.dataset.rating = val;
            renderStars(val, false);
            saveRating(val);
        });

        ratingEl.appendChild(star);
    }
    ratingEl.dataset.rating = rating;

    if (!skipSave) return;
}

function saveRating(rating) {
    const btnDelete = document.getElementById('btn-delete');
    const id = btnDelete?.dataset.id;
    if (!id) return;
    const formData = new FormData();
    formData.append('action', 'edit_rating');
    formData.append('id', id);
    formData.append('rating', rating);
    fetch('php/save_data.php', { method: 'POST', body: formData }).then(r=>r.json()).then(d=>{
        if(d.success) {
            const item = currentItems.find(i => i.id === id);
            if (item) item.rating = rating;
        }
    });
}

// ─── Context Menu Logic ─────────────────────────────────────────
let contextItem = null;
const ctxMenu = document.getElementById('context-menu');
const ctxExportPng = document.getElementById('ctx-export-png');
const ctxExportJpg = document.getElementById('ctx-export-jpg');
const ctxTrash = document.getElementById('ctx-trash');
const ctxRestore = document.getElementById('ctx-restore');

function showContextMenu(e, item) {
    if (!ctxMenu) return;
    contextItem = item;
    ctxMenu.style.display = 'block';
    ctxMenu.style.left = e.pageX + 'px';
    ctxMenu.style.top = e.pageY + 'px';

    if (item.trashed) {
        ctxTrash.style.display = 'none';
        ctxRestore.style.display = 'block';
    } else {
        ctxTrash.style.display = 'block';
        ctxRestore.style.display = 'none';
    }
}

document.addEventListener('click', () => {
    if (ctxMenu) ctxMenu.style.display = 'none';
});

if (ctxExportPng) {
    ctxExportPng.addEventListener('click', () => {
        if (!contextItem) return;
        const a = document.createElement('a');
        a.href = contextItem.url;
        a.download = (contextItem.title || 'image') + '.png';
        a.click();
    });
}

if (ctxExportJpg) {
    ctxExportJpg.addEventListener('click', () => {
        if (!contextItem) return;
        const a = document.createElement('a');
        a.href = contextItem.url;
        a.download = (contextItem.title || 'image') + '.jpg';
        a.click();
    });
}

if (ctxTrash) {
    ctxTrash.addEventListener('click', () => {
        if (!contextItem) return;
        updateItemTrashState(contextItem.id, true);
    });
}

if (ctxRestore) {
    ctxRestore.addEventListener('click', () => {
        if (!contextItem) return;
        updateItemTrashState(contextItem.id, false);
    });
}

function updateItemTrashState(id, trashed) {
    const fd = new FormData();
    fd.append('action', 'trash');
    fd.append('id', id);
    fd.append('trashed', trashed ? '1' : '0');
    fetch('php/save_data.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            clearSelection();
            loadItems();
        }
    });
}

// ─── Drag & Drop Import ─────────────────────────────────────────
const dropZone = document.getElementById('gallery-container');
if (dropZone) {
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.border = '2px dashed var(--accent-blue)';
    });
    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.style.border = 'none';
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.border = 'none';
        if (e.dataTransfer.files.length > 0) {
            const file = e.dataTransfer.files[0];
            const itemForm = document.getElementById('item-form');
            const fileInput = document.getElementById('item-file');
            if (fileInput && itemForm) {
                // Populate modal and open
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                
                let title = file.name.split('.')[0];
                document.getElementById('item-title').value = title;
                
                document.getElementById('item-modal').style.display = 'flex';
                requestAnimationFrame(() => document.getElementById('item-modal').classList.add('show'));
            }
        }
    });
}

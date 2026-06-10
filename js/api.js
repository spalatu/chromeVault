let UNSPLASH_ACCESS_KEY = 'brLyW-27YytbSqrtkiYyfJ9oz1ifbnOkMPStsvDIHoQ';
let apiPage = 1;
let apiQuery = '';

document.addEventListener('DOMContentLoaded', () => {
    const btnUnsplash = document.getElementById('btn-unsplash');
    const apiModal = document.getElementById('api-modal');
    const closeApiModal = document.getElementById('close-api-modal');
    const apiSearchInput = document.getElementById('api-search-input');
    const btnApiSearch = document.getElementById('btn-api-search');
    const btnApiLoadMore = document.getElementById('btn-api-load-more');

    if (btnUnsplash) {
        btnUnsplash.addEventListener('click', () => {
            apiModal.style.display = 'block';
            if (apiQuery === '') {
                apiQuery = 'editorial design minimal';
                apiSearchInput.value = apiQuery;
                searchApi(true);
            }
        });
    }

    if (closeApiModal) {
        closeApiModal.addEventListener('click', () => {
            apiModal.style.display = 'none';
        });
    }

    if (btnApiSearch) {
        btnApiSearch.addEventListener('click', () => {
            searchApi(true);
        });
    }
    
    if (apiSearchInput) {
        apiSearchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') searchApi(true);
        });
    }

    if (btnApiLoadMore) {
        btnApiLoadMore.addEventListener('click', () => {
            searchApi(false);
        });
    }
});

function searchApi(reset) {
    let query = document.getElementById('api-search-input').value.trim();
    if (query === '') query = 'editorial design minimal';

    let statusEl = document.getElementById('api-status');
    let gridEl = document.getElementById('api-grid');
    let loadMoreBtn = document.getElementById('btn-api-load-more');

    if (reset) {
        apiPage = 1;
        apiQuery = query;
        gridEl.innerHTML = '';
        statusEl.style.display = 'block';
        statusEl.textContent = 'Searching…';
        loadMoreBtn.style.display = 'none';
        
        // Setup 3 columns
        for (let i = 0; i < 3; i++) {
            let col = document.createElement('div');
            col.className = 'unsplash-col';
            col.style.flex = '1';
            col.style.display = 'flex';
            col.style.flexDirection = 'column';
            col.style.gap = '16px';
            gridEl.appendChild(col);
        }
    }

    let url = 'https://api.unsplash.com/search/photos?query=' + encodeURIComponent(query) + '&per_page=15&page=' + apiPage;

    fetch(url, {
        headers: { 'Authorization': 'Client-ID ' + UNSPLASH_ACCESS_KEY }
    })
    .then(res => {
        if (!res.ok) throw new Error('API error');
        return res.json();
    })
    .then(data => {
        if (!data.results || data.results.length === 0) {
            if (reset) statusEl.textContent = 'No results found.';
            return;
        }

        statusEl.style.display = 'none';
        let columns = gridEl.querySelectorAll('.unsplash-col');
        let colHeights = [0, 0, 0];

        // Approximate heights from current children
        columns.forEach((col, i) => {
            Array.from(col.children).forEach(child => {
                colHeights[i] += child.getBoundingClientRect().height || 200;
            });
        });

        data.results.forEach(photo => {
            let shortestIndex = 0;
            let shortestHeight = colHeights[0];
            for (let c = 1; c < colHeights.length; c++) {
                if (colHeights[c] < shortestHeight) {
                    shortestHeight = colHeights[c];
                    shortestIndex = c;
                }
            }

            let ar = photo.width / photo.height;
            colHeights[shortestIndex] += (210 / ar) + 16;

            let wrap = document.createElement('div');
            wrap.style.position = 'relative';
            wrap.style.borderRadius = '8px';
            wrap.style.overflow = 'hidden';
            wrap.style.cursor = 'pointer';

            let img = document.createElement('img');
            img.src = photo.urls.small;
            img.style.width = '100%';
            img.style.display = 'block';

            let overlay = document.createElement('div');
            overlay.className = 'unsplash-overlay';
            overlay.style.position = 'absolute';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.right = '0';
            overlay.style.bottom = '0';
            overlay.style.background = 'rgba(0,0,0,0.5)';
            overlay.style.display = 'flex';
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'center';
            overlay.style.opacity = '0';
            overlay.style.transition = 'opacity 0.2s';

            let addBtn = document.createElement('button');
            addBtn.className = 'btn btn-primary';
            addBtn.textContent = 'Add to Vault';

            wrap.addEventListener('mouseenter', () => overlay.style.opacity = '1');
            wrap.addEventListener('mouseleave', () => overlay.style.opacity = '0');

            addBtn.addEventListener('click', () => {
                importFromUnsplash(photo);
            });

            overlay.appendChild(addBtn);
            wrap.appendChild(img);
            wrap.appendChild(overlay);
            columns[shortestIndex].appendChild(wrap);
        });

        if (data.total_pages > apiPage) {
            apiPage++;
            loadMoreBtn.style.display = 'inline-block';
        } else {
            loadMoreBtn.style.display = 'none';
        }
    })
    .catch(err => {
        statusEl.style.display = 'block';
        statusEl.textContent = 'Error connecting to Unsplash.';
    });
}

function importFromUnsplash(photo) {
    let rawTitle = photo.alt_description || photo.description || 'Unsplash Image';
    let title = rawTitle.charAt(0).toUpperCase() + rawTitle.slice(1).replace(/\s+/g, ' ').trim();
    let srcUrl = photo.urls.regular || photo.urls.full;

    // Trigger Unsplash download endpoint if needed (per terms)
    fetch('https://api.unsplash.com/photos/' + photo.id + '/download', {
        headers: { 'Authorization': 'Client-ID ' + UNSPLASH_ACCESS_KEY }
    });

    // Close modal
    document.getElementById('api-modal').style.display = 'none';

    // Show loading state (could improve this later)
    alert("Downloading from Unsplash... Please wait.");

    // Extract colors client-side using a crossOrigin image
    let tempImg = new Image();
    tempImg.crossOrigin = 'anonymous';
    tempImg.onload = async function () {
        let colors = [];
        try {
            if (typeof extractDominantColors === 'function') {
                colors = await extractDominantColors(tempImg, 10);
            }
        } catch (e) { console.error('Color extraction failed', e); }

        let formData = new FormData();
        formData.append('action', 'add');
        formData.append('title', title);
        formData.append('unsplash_url', srcUrl);
        formData.append('colors', JSON.stringify(colors));
        formData.append('last_modified', Date.now());

        fetch('php/save_data.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof loadItems === 'function') loadItems();
                alert("Image added successfully!");
            } else {
                alert("Upload failed: " + data.message);
            }
        })
        .catch(err => alert("Error uploading Unsplash image."));
    };
    tempImg.onerror = () => alert("Failed to load Unsplash image for processing.");
    tempImg.src = srcUrl;
}

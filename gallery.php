<?php
/**
 * gallery.php — ChromeVault
 * The protected Library view displaying the complete card deck.
 * Supports inline card deletion, inline info update, and fast filter button toggling.
 */

// Include auth verification guard
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/functions.php';

$userId = $_SESSION['user_id'];
$imagesPath = __DIR__ . '/data/images.json';
$images = readJSON($imagesPath);

// ── Handle GET Delete Request ────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteId = $_GET['id'];
    $updatedImages = [];
    $deletedSuccessfully = false;
    
    foreach ($images as $img) {
        if ($img['id'] === $deleteId) {
            // Verify this image belongs to the active logged-in user
            if ($img['user_id'] === $userId) {
                // If it is a local file upload, remove it from the file system
                if (strpos($img['image_path'], 'images/') === 0) {
                    $localFilePath = __DIR__ . '/' . $img['image_path'];
                    if (file_exists($localFilePath)) {
                        unlink($localFilePath);
                    }
                }
                $deletedSuccessfully = true;
                continue; // skip adding to updated array
            }
        }
        $updatedImages[] = $img;
    }
    
    if ($deletedSuccessfully) {
        writeJSON($imagesPath, $updatedImages);
        $_SESSION['gallery_toast'] = json_encode(['message' => 'Image successfully removed!', 'type' => 'success']);
    } else {
        $_SESSION['gallery_toast'] = json_encode(['message' => 'Failed to delete image.', 'type' => 'error']);
    }
    
    header("Location: gallery.php");
    exit;
}

// ── Handle POST Edit Update Request ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $editId = isset($_POST['id']) ? $_POST['id'] : '';
    $updatedTitle = isset($_POST['title']) ? trim($_POST['title']) : '';
    $updatedCollection = isset($_POST['collection']) ? trim($_POST['collection']) : '';
    $updatedTagsRaw = isset($_POST['tags']) ? trim($_POST['tags']) : '';
    
    $editedSuccessfully = false;
    
    if ($editId !== '' && $updatedTitle !== '') {
        // Parse updated tags
        $updatedTags = [];
        if ($updatedTagsRaw !== '') {
            $updatedTags = array_filter(array_map('trim', explode(',', $updatedTagsRaw)));
        }
        
        foreach ($images as &$img) {
            if ($img['id'] === $editId && $img['user_id'] === $userId) {
                $img['title'] = $updatedTitle;
                $img['collection'] = $updatedCollection === '' ? 'General' : $updatedCollection;
                $img['tags'] = array_values($updatedTags);
                $editedSuccessfully = true;
                break;
            }
        }
        
        if ($editedSuccessfully) {
            writeJSON($imagesPath, $images);
            // Update collections store if new one is added
            $collectionsPath = __DIR__ . '/data/collections.json';
            $collections = readJSON($collectionsPath);
            $colName = $updatedCollection === '' ? 'General' : $updatedCollection;
            
            $colExists = false;
            foreach ($collections as $col) {
                if (isset($col['name']) && strtolower($col['name']) === strtolower($colName)) {
                    $colExists = true;
                    break;
                }
            }
            
            if (!$colExists) {
                $collections[] = [
                    'id' => uniqid('col_'),
                    'user_id' => $userId,
                    'name' => $colName
                ];
                writeJSON($collectionsPath, $collections);
            }
            
            $_SESSION['gallery_toast'] = json_encode(['message' => 'Details successfully updated!', 'type' => 'success']);
        } else {
            $_SESSION['gallery_toast'] = json_encode(['message' => 'Failed to update details.', 'type' => 'error']);
        }
    }
    
    header("Location: gallery.php");
    exit;
}

// Filter only user-owned images for display
$userImages = array_filter($images, function($img) use ($userId) {
    return isset($img['user_id']) && $img['user_id'] === $userId;
});

// Sort user images: latest first
usort($userImages, function($a, $b) {
    return strcmp($b['created_at'], $a['created_at']);
});

// Extract user's unique collection names for filter bar pills
$collectionNames = [];
foreach ($userImages as $img) {
    if (isset($img['collection'])) {
        $collectionNames[] = trim($img['collection']);
    }
}
$uniqueCollectionNames = array_unique($collectionNames);
sort($uniqueCollectionNames);

// Check if there's any toast message scheduled
$toastData = null;
if (isset($_SESSION['gallery_toast'])) {
    $toastData = json_decode($_SESSION['gallery_toast'], true);
    unset($_SESSION['gallery_toast']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library — ChromeVault</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Embedded styling to manage card action opacity exactly as requested */
        .gallery-card {
            background: var(--bg-elevated);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: border-color 0.15s, transform 0.15s;
            position: relative;
        }
        .gallery-card:hover {
            border-color: var(--border-default);
            transform: translateY(-2px);
        }
        .gallery-card-actions {
            display: flex;
            gap: 6px;
            margin-top: var(--space-3);
            opacity: 0;
            transition: opacity 0.15s ease-in-out;
        }
        .gallery-card:hover .gallery-card-actions,
        .gallery-card-actions.keep-visible {
            opacity: 1;
        }
        .gallery-edit-form {
            display: none;
            flex-direction: column;
            gap: var(--space-2);
            margin-top: var(--space-3);
            padding-top: var(--space-3);
            border-top: 1px solid var(--border-subtle);
        }
        .gallery-edit-form.visible {
            display: flex;
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
            <a href="gallery.php" class="nav-link active" data-i18n="nav_gallery">Library</a>
            <a href="upload.php" class="nav-link" data-i18n="nav_upload">Add Image</a>
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
            <h1 class="page-title" data-i18n="lib_title">Library</h1>
            <p class="page-subtitle">Filter, browse, edit details, and copy your extracted color palettes.</p>
        </header>

        <?php if (empty($userImages)): ?>
            <!-- Empty Vault state -->
            <div class="card" style="text-align: center; padding: var(--space-12) 0;">
                <div style="font-size: 36px; margin-bottom: var(--space-2);">🖼️</div>
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: var(--space-1); color: var(--text-primary);" data-i18n="dash_recent">Your Library is empty</h3>
                <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: var(--space-4);">Start saving inspiration by uploading your first image.</p>
                <a href="upload.php" class="btn btn-primary" data-i18n="nav_upload">Add Image</a>
            </div>
        <?php else: ?>
            
            <!-- ── Dynamic Collection Filter Pills ──────────────────────────────── -->
            <div style="display: flex; gap: var(--space-2); flex-wrap: wrap; margin-bottom: var(--space-6);">
                <button class="btn btn-secondary btn-sm" data-filter="all" id="btn-filter-all" data-i18n="filter_all">All</button>
                <?php foreach ($uniqueCollectionNames as $colName): ?>
                    <button class="btn btn-secondary btn-sm" data-filter="<?php echo htmlspecialchars(strtolower($colName)); ?>">
                        <?php echo htmlspecialchars($colName); ?>
                    </button>
                <?php endforeach; // End filter loop ?>
            </div>

            <!-- ── Images Card Grid ─────────────────────────────────────────────── -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: var(--space-3); margin-bottom: var(--space-12);" id="library-grid">
                <?php foreach ($userImages as $img): ?>
                    <article class="gallery-card" data-collection="<?php echo htmlspecialchars(strtolower(trim($img['collection']))); ?>">
                        <!-- Image thumbnail visual -->
                        <img src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="<?php echo htmlspecialchars($img['title']); ?>" style="aspect-ratio: 3/2; width: 100%; object-fit: cover; display: block;" loading="lazy">
                        
                        <div style="padding: var(--space-4);">
                            <!-- Title & Pill info -->
                            <h3 style="font-size: 14px; font-weight: 500; color: var(--text-primary); margin-bottom: var(--space-2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?php echo htmlspecialchars($img['title']); ?>
                            </h3>
                            <span class="badge badge-amber"><?php echo htmlspecialchars($img['collection']); ?></span>

                            <!-- Dominant palette swatches row -->
                            <div style="display: flex; gap: 5px; margin-top: var(--space-3);">
                                <?php if (isset($img['palette']) && is_array($img['palette'])): ?>
                                    <?php foreach ($img['palette'] as $color): ?>
                                        <button 
                                            style="width: 18px; height: 18px; border-radius: 50%; cursor: pointer; border: 1.5px solid var(--bg-elevated); box-shadow: 0 0 0 1px var(--border-subtle); background-color: <?php echo htmlspecialchars($color); ?>; transition: transform 0.1s; flex-shrink: 0;"
                                            title="Copy <?php echo htmlspecialchars($color); ?>"
                                            onclick="copyHex('<?php echo htmlspecialchars($color); ?>', event)"
                                            aria-label="Copy color <?php echo htmlspecialchars($color); ?>"
                                            onmouseover="this.style.transform='scale(1.2)'"
                                            onmouseout="this.style.transform='scale(1)'"
                                        ></button>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Edit and Delete Actions (Appear on hover) -->
                            <div class="gallery-card-actions" id="actions-<?php echo $img['id']; ?>">
                                <button class="btn btn-secondary btn-sm" onclick="toggleEditForm('<?php echo $img['id']; ?>')" data-i18n="btn_edit">Edit</button>
                                <a 
                                    href="gallery.php?action=delete&id=<?php echo $img['id']; ?>" 
                                    class="btn btn-danger btn-sm" 
                                    onclick="return confirm('Are you sure you want to delete this image?');"
                                    data-i18n="btn_delete"
                                >Delete</a>
                            </div>

                            <!-- Inline Edit form, activated via toggleEditForm -->
                            <form id="edit-form-<?php echo $img['id']; ?>" class="gallery-edit-form" method="POST" action="gallery.php">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($img['id']); ?>">
                                
                                <div class="field">
                                    <label class="label">Title</label>
                                    <input type="text" name="title" value="<?php echo htmlspecialchars($img['title']); ?>" required placeholder="Title" class="input">
                                </div>
                                <div class="field">
                                    <label class="label">Collection</label>
                                    <input type="text" name="collection" value="<?php echo htmlspecialchars($img['collection']); ?>" placeholder="Collection" class="input">
                                </div>
                                <div class="field">
                                    <label class="label">Tags</label>
                                    <?php $tagsStr = isset($img['tags']) && is_array($img['tags']) ? implode(', ', $img['tags']) : ''; ?>
                                    <input type="text" name="tags" value="<?php echo htmlspecialchars($tagsStr); ?>" placeholder="Tags (comma-separated)" class="input">
                                </div>

                                <div style="display: flex; gap: var(--space-1); margin-top: var(--space-2);">
                                    <button type="submit" class="btn btn-primary btn-sm" style="flex: 1;" data-i18n="btn_save">Save</button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEditForm('<?php echo $img['id']; ?>')" data-i18n="btn_cancel">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    </div>

    <!-- ── Toast System Container ───────────────────────────────────────────── -->
    <div class="toast-container" id="toast-container"></div>

    <script src="js/script.js"></script>
    <script>
        // Trigger Toast notifications on loaded redirects
        <?php if ($toastData): ?>
            showToast("<?php echo htmlspecialchars($toastData['message']); ?>", "<?php echo htmlspecialchars($toastData['type']); ?>");
        <?php endif; ?>

        // Toggle visibility of inline card edit forms
        function toggleEditForm(id) {
            const form = document.getElementById('edit-form-' + id);
            const actions = document.getElementById('actions-' + id);
            if (form) {
                form.classList.toggle('visible');
                // Keep actions visible if form is opened
                if (form.classList.contains('visible')) {
                    actions.classList.add('keep-visible');
                } else {
                    actions.classList.remove('keep-visible');
                }
            }
        }

        // ── Javascript Filter Bar Mechanics ───────────────────────────────────
        const filterButtons = document.querySelectorAll('[data-filter]');
        const imageCards = document.querySelectorAll('.gallery-card');

        // Set initial active state correctly
        const allBtn = document.getElementById('btn-filter-all');
        if (allBtn) {
            allBtn.style.background = 'var(--accent-muted)';
            allBtn.style.color = 'var(--accent-hover)';
            allBtn.style.borderColor = 'rgba(217,119,6,0.3)';
        }

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Clear active states of all filters
                filterButtons.forEach(btn => {
                    btn.style.background = 'var(--bg-elevated)';
                    btn.style.color = 'var(--text-primary)';
                    btn.style.borderColor = 'var(--border-default)';
                });
                
                // Add active state to clicked filter pill
                button.style.background = 'var(--accent-muted)';
                button.style.color = 'var(--accent-hover)';
                button.style.borderColor = 'rgba(217,119,6,0.3)';

                const filterValue = button.getAttribute('data-filter');

                imageCards.forEach(card => {
                    const cardCollection = card.getAttribute('data-collection');
                    if (filterValue === 'all' || cardCollection === filterValue) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>

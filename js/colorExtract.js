/**
 * colorExtract.js — ChromeVault
 * Extracts the 5 most dominant colors from an image using canvas pixel sampling.
 * Simple, fast, and beginner-readable — no external libraries needed.
 */

/**
 * Draws the image onto a tiny 50×50 canvas, samples every 8th pixel,
 * quantizes RGB to 32-step buckets, counts occurrences, returns top 5 HEX colors.
 *
 * @param {HTMLImageElement} imgElement - The image to analyse (must be loaded / CORS-safe)
 * @param {function} callback - Called with an array of up to 5 HEX color strings
 */
function extractColors(imgElement, callback) {
  // Create a small canvas — smaller = faster sampling
  const canvas = document.createElement('canvas');
  canvas.width  = 50;
  canvas.height = 50;

  const ctx = canvas.getContext('2d');

  // Draw the image scaled down to 50×50
  ctx.drawImage(imgElement, 0, 0, 50, 50);

  // Read every pixel's RGBA values as a flat Uint8ClampedArray
  const data = ctx.getImageData(0, 0, 50, 50).data;

  // Object to count how often each quantized color appears
  const colors = {};

  // Step through pixels, sampling every 8th (i += 4 bytes per pixel × 8)
  for (let i = 0; i < data.length; i += 4 * 8) {
    const alpha = data[i + 3];

    // Skip fully transparent pixels — they'd produce black (#000000)
    if (alpha < 128) continue;

    // Quantize each channel to the nearest multiple of 32 (0–224)
    // This groups similar colours together so we get meaningful dominant colours
    const r = Math.round(data[i]     / 32) * 32;
    const g = Math.round(data[i + 1] / 32) * 32;
    const b = Math.round(data[i + 2] / 32) * 32;

    // Clamp values to 0–255 range after rounding
    const rC = Math.min(255, r);
    const gC = Math.min(255, g);
    const bC = Math.min(255, b);

    // Convert to HEX string, padding single digits (e.g. 0 → "00")
    const hex = '#' + [rC, gC, bC]
      .map(v => v.toString(16).padStart(2, '0'))
      .join('');

    // Increment the count for this colour
    colors[hex] = (colors[hex] || 0) + 1;
  }

  // Sort by frequency (most common first), take the top 5
  const sorted = Object.entries(colors)
    .sort((a, b) => b[1] - a[1])
    .slice(0, 5)
    .map(entry => entry[0]);

  // Return the palette via callback
  callback(sorted);
}

/**
 * Renders palette swatches into a container element.
 * Each swatch shows a coloured circle and its HEX code below.
 *
 * @param {string[]} colors - Array of HEX color strings
 * @param {HTMLElement} container - The DOM element to render swatches into
 */
function renderPalettePreview(colors, container) {
  // Clear any existing swatches
  container.innerHTML = '';

  colors.forEach(hex => {
    // Wrapper div for the dot + label
    const item = document.createElement('div');
    item.className = 'palette-swatch';

    // Coloured circle
    const dot = document.createElement('div');
    dot.className = 'palette-swatch-dot';
    dot.style.background = hex;
    dot.title = hex;

    // HEX label below the dot
    const label = document.createElement('span');
    label.className = 'palette-swatch-hex';
    label.textContent = hex;

    item.appendChild(dot);
    item.appendChild(label);
    container.appendChild(item);
  });

  // Make the container visible
  container.classList.add('visible');
}

/**
 * Extracts colors from an image URL by temporarily loading it into an <img>.
 * Note: the server must send CORS headers for cross-origin images.
 *
 * @param {string} url - The image URL
 * @param {function} callback - Called with the palette array (or empty array on error)
 */
function extractColorsFromURL(url, callback) {
  const img = new Image();
  img.crossOrigin = 'anonymous'; // needed for CORS-safe pixel access
  img.onload  = () => extractColors(img, callback);
  img.onerror = () => callback([]);   // return empty palette on failure
  img.src = url;
}

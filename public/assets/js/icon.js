function updateFavicon() {
  // Detect dark mode using matchMedia.
  const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
  console.log("Dark mode active: ", isDarkMode);
  
  // Select the appropriate image.
  const newHref = isDarkMode 
    ? 'public/assets/images/logo/last_.png'  // White icon for dark mode
    : 'public/assets/images/logo/logo.png';   // Dark icon for light mode
  
  // Create a new <link> element.
  const newLink = document.createElement('link');
  newLink.rel = 'icon';
  newLink.type = 'image/png';
  // Append a query string to avoid caching issues.
  newLink.href = newHref + '?v=' + Date.now();
  newLink.id = 'favicon';
  
  // Remove the old favicon element if it exists.
  const oldLink = document.getElementById('favicon');
  if (oldLink) {
    oldLink.parentNode.removeChild(oldLink);
  }
  
  // Append the new favicon element.
  document.head.appendChild(newLink);
}

// Run on initial page load.
updateFavicon();

// Listen for changes in the color scheme.
const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
if (darkModeQuery.addEventListener) {
  darkModeQuery.addEventListener('change', updateFavicon);
} else if (darkModeQuery.addListener) {
  darkModeQuery.addListener(updateFavicon);
}

UI Prototype & Build notes

Overview
--------
This README explains how to preview the UI changes added to the project (glass/neumorphism prototype). A full Tailwind build and screenshot automation require Node/npm — instructions below are for local execution on your machine.

Quick preview (no Node required)
--------------------------------
1. Start the Laravel dev server from the project root:

```powershell
php artisan serve
```

2. Open the following pages in your browser to preview changes:
- http://127.0.0.1:8000/home
- http://127.0.0.1:8000/messages
- http://127.0.0.1:8000/statistics

Full Tailwind build & screenshots (optional, requires Node/npm)
----------------------------------------------------------------
1. Install Node.js and npm (Windows): download from https://nodejs.org and run the installer.
2. From project root, run:

```powershell
npm install
npm run build:css
php artisan serve
npm run screenshot:proto
```

Notes
- If `npm` isn't available, the repository includes a minimal fallback CSS so previews render without Node.
- The Puppeteer screenshot script (if present) will save PNGs to a `screenshots/` folder.

Reverting
--------
- To revert the UI changes, remove the link to `public/css/theme-dashboard.css` from `resources/views/layouts/app.blade.php` and remove `.glass-panel` wrappers where applied.

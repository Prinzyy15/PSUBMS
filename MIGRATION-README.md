Migration notes & rollback
-------------------------

How to roll out the theme safely:
1. Deploy `public/css/theme-dashboard.css` and include it in `resources/views/layouts/app.blade.php` as currently done.
2. Gradually add `.glass-panel` wrappers to pages in small batches and monitor UI/UX.
3. Run visual tests (screenshots) and manual accessibility checks before enabling for all users.

Rollback:
- Remove the theme CSS link from `resources/views/layouts/app.blade.php` and remove `.glass-panel` wrappers added.
- The rest of the app will fall back to original Bootstrap styles.

Notes:
- All style changes are non-destructive and small; they primarily add wrapper classes and CSS.

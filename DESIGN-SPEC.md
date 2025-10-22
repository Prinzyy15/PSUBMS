Design Spec — Neumorphism + Glass hybrid
========================================

Color tokens
------------
- --primary: #2563eb (blue)
- --accent: #06b6d4 (teal)
- --bg: #f6f8fb
- --card: rgba(255,255,255,0.9)
- --muted: #6b7280

Typography
----------
- Font: Inter (already included)
- Headings: weights 700, color var(--primary)
- Body: 16px base, color #0f172a

Spacing & radii
----------------
- Card radius: 12-16px
- Metric radius: 14px
- Gutter: 16–24px

Components
----------
- .glass-panel: translucent card with border + blur
- .metric-card: rounded metric card, strong number, small muted label
- .glass-table: wrapper for DataTables — rounded, subtle shadow
- .chart-controls: pill buttons with active state (gradient)

Accessibility
-------------
- Provide focus outlines for keyboard navigation (implemented)
- Use aria-pressed for pill controls and aria-labels for icon buttons
- Ensure color contrast for text on colored backgrounds (avoid low contrast)

How to extend
--------------
- Add tokens to `public/css/theme-dashboard.css` root variables.
- Prefer non-destructive wrappers (e.g., add `.glass-panel` to card containers) so the theme is reversible.

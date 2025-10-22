Accessibility Quick Checklist
-----------------------------

- Keyboard navigation: Tab through all interactive controls (links, buttons, form fields) — ensure focus ring is visible.
- ARIA: Use aria-labels for icon-only buttons, aria-pressed for toggle pills, role=tablist for control groups.
- Contrast: Verify text on colored backgrounds meets WCAG AA (4.5:1 for normal text, 3:1 for large text).
- Forms: Associate labels with inputs (use <label for=> and input id).
- Modals: Ensure modals trap focus and restore focus to the triggering element on close.
- Images: Provide meaningful alt text for decorative images or role=presentation when appropriate.

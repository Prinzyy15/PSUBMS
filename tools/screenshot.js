const puppeteer = require('puppeteer');
const pages = [
  { url: 'http://127.0.0.1:8000/prototype/tailwind-dashboard', name: 'dashboard' },
  { url: 'http://127.0.0.1:8000/prototype/tailwind-students', name: 'students' },
  { url: 'http://127.0.0.1:8000/prototype/tailwind-student-profile', name: 'student-profile' }
];

(async () => {
  const browser = await puppeteer.launch();
  const page = await browser.newPage();
  for (const p of pages) {
    console.log('Opening', p.url);
    await page.goto(p.url, { waitUntil: 'networkidle2' });
    await page.setViewport({ width: 1280, height: 900 });
    const path = `screenshots/${p.name}.png`;
    await page.screenshot({ path, fullPage: true });
    console.log('Saved', path);
  }
  await browser.close();
})();

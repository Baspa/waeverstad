<?php
/**
 * Builds the public concept page (GitHub Pages) with the same renderer as the plugin.
 *
 *   php demo/build.php
 *
 * Output: docs/index.html plus docs/assets/. Open docs/index.html locally or
 * serve it from GitHub Pages (branch main, folder /docs).
 *
 * The site's display face (Intro Rust) is a commercial font and is not shipped
 * here; the demo uses Lilita One as a stand-in. On the real site the plugin
 * picks up Intro Rust from the theme automatically. Lilita One and Poppins are
 * OFL fonts, self-hosted from demo/assets/fonts so the page makes no requests to Google.
 */

declare(strict_types=1);

require __DIR__ . '/../waeverstad-schedule/includes/class-renderer.php';

use Waeverstad\Schedule\Renderer;

$root = dirname(__DIR__);
$plugin = $root . '/waeverstad-schedule';
$sourceAssets = __DIR__ . '/assets';
$out = $root . '/docs';

$data = json_decode((string) file_get_contents($plugin . '/data/schedule.json'), true, 512, JSON_THROW_ON_ERROR);
$schedule = (new Renderer($data))->render();

$simulations = [
    ['Zaterdag 14 feb, 16:20', '2026-02-14T16:20'],
    ['Vrijdag 13 feb, 15:20', '2026-02-13T15:20'],
    ['Maandag 16 feb, 21:10', '2026-02-16T21:10'],
    ['Buiten carnaval', ''],
];

$links = '';
foreach ($simulations as [$label, $moment]) {
    $href = $moment === '' ? './' : '?now=' . $moment;
    $links .= '<a href="' . htmlspecialchars($href) . '" data-now="' . htmlspecialchars($moment) . '">' . htmlspecialchars($label) . '</a>';
}

$version = substr(md5((string) file_get_contents($plugin . '/assets/schedule.css') . file_get_contents($plugin . '/assets/schedule.js')), 0, 8);

$html = <<<HTML
<!doctype html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Programma | Waeverstadfeest (concept)</title>
<link rel="preload" href="assets/fonts/LilitaOne-Regular.ttf" as="font" type="font/ttf" crossorigin>
<link rel="icon" href="assets/favicon.png" sizes="192x192">
<link rel="stylesheet" href="assets/schedule.css?v={$version}">
<style>
/* Self-hosted OFL fonts (see assets/fonts/OFL-*.txt): no requests to Google, so no visitor IPs leave this page. */
@font-face { font-family: 'Lilita One'; font-style: normal; font-weight: 400; font-display: swap; src: url(assets/fonts/LilitaOne-Regular.ttf) format('truetype'); }
@font-face { font-family: 'Poppins'; font-style: normal; font-weight: 400; font-display: swap; src: url(assets/fonts/poppins-400.woff2) format('woff2'); }
@font-face { font-family: 'Poppins'; font-style: normal; font-weight: 500; font-display: swap; src: url(assets/fonts/poppins-500.woff2) format('woff2'); }
@font-face { font-family: 'Poppins'; font-style: normal; font-weight: 600; font-display: swap; src: url(assets/fonts/poppins-600.woff2) format('woff2'); }
@font-face { font-family: 'Poppins'; font-style: normal; font-weight: 700; font-display: swap; src: url(assets/fonts/poppins-700.woff2) format('woff2'); }
html { background: #09a552; }
body { margin: 0; color: #124a2f; font-family: Poppins, Helvetica, Arial, sans-serif; background: #09a552 url(assets/background.webp) left top / 1980px auto repeat; }
.wvp { --wvp-display: 'Lilita One', Poppins, Helvetica, Arial, sans-serif; }
.demo-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 14px; padding: 9px 16px; background: #124a2f; color: #fff; font-size: 13px; line-height: 1.4; }
.demo-bar strong { font-family: 'Lilita One', Poppins, sans-serif; font-weight: 400; color: #ffec96; letter-spacing: .03em; text-transform: uppercase; }
.demo-bar span { color: #d9f5d6; }
.demo-bar nav { display: flex; flex-wrap: wrap; gap: 4px 12px; }
.demo-bar a { color: #fff; text-underline-offset: 3px; }
.demo-bar a[aria-current] { color: #fbce06; font-weight: 600; }
.site-head { position: relative; height: 220px; background: #2a1d1a url(assets/hero.webp) center 30% / cover; }
.site-head::after { content: ''; position: absolute; inset: 0; background: rgba(0, 0, 0, .28); }
.site-nav { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 26px 24px; }
.site-nav img { display: block; height: 64px; width: auto; }
.site-nav ul { display: flex; gap: clamp(16px, 4vw, 64px); margin: 0; padding: 0; list-style: none; }
.site-nav a { display: inline-block; padding: 10px 14px 9px; border-radius: 5px; background: #09a552; color: #fff; font-family: 'Lilita One', Poppins, sans-serif; font-size: 15px; line-height: 1; text-decoration: none; text-transform: uppercase; }
.site-nav a.donate { background: #d42e24; }
.site-nav a[aria-current] { box-shadow: 0 0 0 2px #fff; }
.burger { display: none; width: 42px; height: 42px; place-content: center; gap: 5px; border: 2px solid #fff; border-radius: 5px; background: #e6342a; }
.burger i { display: block; width: 20px; height: 3px; border-radius: 2px; background: #fff; }
main { padding: 44px 0 72px; }
.row { width: 80%; max-width: 1080px; margin: 0 auto; }
.demo-foot { padding: 18px 16px 28px; background: #124a2f; color: #fff; font-size: 13px; text-align: center; }
@media (max-width: 980px) {
  .site-head { height: 120px; }
  .site-nav { padding: 16px; }
  .site-nav img { height: 46px; }
  .site-nav ul { display: none; }
  .burger { display: grid; }
  main { padding-top: 26px; }
  .row { width: auto; margin: 0 16px; }
}
</style>
</head>
<body>
<div class="demo-bar"><strong>Concept</strong><span>Nieuwe programmapagina met het voorbeeldprogramma van 2026. Bekijk alsof het nu is:</span><nav>{$links}</nav></div>
<header class="site-head">
  <div class="site-nav">
    <a href="https://waeverstadfeest.nl/" aria-label="Waeverstad Feest! home"><img src="assets/logo.webp" alt="Waeverstad Feest!" width="196" height="64"></a>
    <ul>
      <li><a href="https://waeverstadfeest.nl/info/">Info</a></li>
      <li><a href="https://waeverstadfeest.nl/fotos/">Foto's '26</a></li>
      <li><a href="./" aria-current="page">Programma</a></li>
      <li><a href="https://waeverstadfeest.nl/tickets/">Tickets</a></li>
      <li><a class="donate" href="https://waeverstadfeest.nl/doneren/">Doneren</a></li>
    </ul>
    <span class="burger" aria-hidden="true"><i></i><i></i><i></i></span>
  </div>
</header>
<main><div class="row">
{$schedule}
</div></main>
<footer class="demo-foot">Concept voor waeverstadfeest.nl. Het programma is het voorbeeld van 2026; het lettertype is een vervanger.</footer>
<script>
(function () {
  var now = new URLSearchParams(location.search).get('now') || '';
  document.querySelectorAll('.demo-bar a').forEach(function (a) {
    if (a.getAttribute('data-now') === now) a.setAttribute('aria-current', 'true');
  });
})();
</script>
<script src="assets/schedule.js?v={$version}" defer></script>
</body>
</html>
HTML;

@mkdir($out . '/assets', 0777, true);
file_put_contents($out . '/index.html', $html);
copy($plugin . '/assets/schedule.css', $out . '/assets/schedule.css');
copy($plugin . '/assets/schedule.js', $out . '/assets/schedule.js');
foreach (['background.webp', 'hero.webp', 'logo.webp', 'favicon.png'] as $file) {
    copy($sourceAssets . '/' . $file, $out . '/assets/' . $file);
}
@mkdir($out . '/assets/fonts', 0777, true);
foreach (glob($sourceAssets . '/fonts/*') as $file) {
    copy($file, $out . '/assets/fonts/' . basename($file));
}
touch($out . '/.nojekyll');

echo "docs/index.html\n";

<?php
/**
 * Turns the schedule data into the timeline HTML.
 *
 * Deliberately free of WordPress dependencies, so the same code can build
 * the standalone demo page (see demo/build.php).
 */

namespace Waeverstad\Schedule;

final class Renderer
{
    // Visitor-facing strings stay Dutch.
    private const DAY_NAMES = ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'];
    private const MONTHS = ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];
    private const MONTHS_SHORT = ['jan.', 'feb.', 'mrt.', 'apr.', 'mei', 'jun.', 'jul.', 'aug.', 'sep.', 'okt.', 'nov.', 'dec.'];

    private array $data;
    private array $options;
    private float $start;
    private float $end;
    /** @var array<string, array> */
    private array $locations = [];
    private string $currentDate = '';

    public function __construct(array $data, array $options = [])
    {
        $this->data = $data;
        $this->options = $options + ['title' => 'Programma'];

        $dayparts = $data['dayparts'] ?? [];
        $this->start = self::hours($dayparts['afternoon']['from'] ?? '13:00');
        $this->end = self::hours($dayparts['evening']['to'] ?? '24:00');

        foreach ($data['locations'] ?? [] as $location) {
            $this->locations[$location['id']] = $location;
        }
    }

    public function render(): string
    {
        $days = $this->data['days'] ?? [];
        if ($days === []) {
            return '';
        }

        $html = '<div class="wvp" data-wvp data-start="' . self::e((string) $this->start) . '" data-end="' . self::e((string) $this->end) . '" data-year="' . self::e((string) ($this->data['year'] ?? '')) . '" style="--hours:' . self::e((string) ($this->end - $this->start)) . '">';
        $html .= '<script>document.currentScript.parentNode.setAttribute("data-js","")</script>';
        $html .= $this->header($days);
        $html .= $this->dayTabs($days);
        $html .= $this->minePanel();

        foreach ($days as $index => $day) {
            $html .= $this->day($day, $index === 0);
        }

        // After the days in the DOM so it can stick to the bottom on phones; CSS moves it up on wide screens.
        $html .= $this->toolbar();
        $html .= $this->footer();
        $html .= '</div>';

        return $html;
    }

    private function header(array $days): string
    {
        $first = self::date($days[0]['date']);
        $last = self::date($days[count($days) - 1]['date']);
        $period = $first->format('j') . ' t/m ' . $last->format('j') . ' ' . self::MONTHS_SHORT[(int) $last->format('n') - 1] . ' ' . $last->format('Y');

        $html = '<div class="wvp-header">';
        if ($this->options['title'] !== '') {
            $html .= '<h2 class="wvp-title">' . self::e($this->options['title']) . '</h2>';
        }
        $html .= '<p class="wvp-period"><span>' . self::e($period) . '</span>';
        if (!empty($this->data['sample'])) {
            $html .= '<span class="wvp-sample">Voorbeeldprogramma</span>';
        }
        $html .= '</p></div>';

        return $html;
    }

    private function dayTabs(array $days): string
    {
        $html = '<div class="wvp-days" role="tablist" aria-label="Kies een dag">';
        foreach ($days as $index => $day) {
            $date = self::date($day['date']);
            $id = self::e($day['date']);
            $dayName = self::DAY_NAMES[(int) $date->format('w')];
            $html .= '<button type="button" role="tab" class="wvp-daytab" id="wvp-tab-' . $id . '" data-date="' . $id . '" aria-controls="wvp-day-' . $id . '" aria-selected="' . ($index === 0 ? 'true' : 'false') . '"' . ($index === 0 ? '' : ' tabindex="-1"') . '>'
                . '<span class="wvp-daytab-short" aria-hidden="true">' . self::e(mb_strtoupper(mb_substr($dayName, 0, 2))) . '</span>'
                . '<span class="wvp-daytab-long" aria-hidden="true">' . self::e($dayName) . '</span>'
                . '<span class="wvp-daytab-num" aria-hidden="true">' . self::e($date->format('j')) . '</span>'
                . '<span class="wvp-sr">' . self::e($dayName . ' ' . $date->format('j') . ' ' . self::MONTHS[(int) $date->format('n') - 1]) . '</span>'
                . '</button>';
        }

        return $html . '</div>';
    }

    private function day(array $day, bool $active): string
    {
        $this->currentDate = $day['date'];
        $date = self::date($day['date']);
        $id = self::e($day['date']);
        $longDate = ucfirst(self::DAY_NAMES[(int) $date->format('w')]) . ' ' . $date->format('j') . ' ' . self::MONTHS[(int) $date->format('n') - 1];

        $html = '<section class="wvp-day' . ($active ? ' is-active' : '') . '" id="wvp-day-' . $id . '" role="tabpanel" aria-labelledby="wvp-tab-' . $id . '" data-date="' . $id . '">';
        $html .= $this->theme($day, $longDate);

        $byLocation = [];
        foreach ($day['items'] ?? [] as $item) {
            $byLocation[$item['location']][] = $item;
        }

        $stages = [];
        $bars = [];
        $closed = [];
        foreach ($this->locations as $locationId => $location) {
            $items = $byLocation[$locationId] ?? [];
            if (($location['kind'] ?? '') === 'bar') {
                if ($items === []) {
                    $closed[] = $location['name'];
                } else {
                    $bars[$locationId] = $items;
                }
            } elseif ($items !== []) {
                $stages[$locationId] = $items;
            }
        }
        // Whatever starts first goes on top.
        uksort($stages, fn ($a, $b) => self::hours($stages[$a][0]['from'] ?? '24:00') <=> self::hours($stages[$b][0]['from'] ?? '24:00'));

        $html .= '<div class="wvp-timeline">';
        $html .= '<div class="wvp-scroller" tabindex="0" role="region" aria-label="' . self::e('Tijdlijn ' . lcfirst($longDate) . '. Veeg of scroll opzij voor de rest van de dag.') . '">';
        $html .= '<div class="wvp-canvas">';
        $html .= $this->ruler();

        foreach ($stages as $locationId => $items) {
            $html .= $this->stageRow($this->locations[$locationId], $items);
        }

        if ($bars !== []) {
            $html .= $this->barsHeader();
            foreach ($bars as $locationId => $items) {
                $html .= $this->barRow($this->locations[$locationId], $items);
            }
        }

        $html .= '<div class="wvp-now" hidden aria-hidden="true"><span class="wvp-now-tag">Nu <b></b></span></div>';
        $html .= '</div></div>';

        if ($closed !== []) {
            $html .= '<p class="wvp-closed">' . self::e('Geen programma op ' . self::DAY_NAMES[(int) $date->format('w')] . ': ' . self::joinNames($closed)) . '</p>';
        }

        return $html . '</div></section>';
    }

    private function theme(array $day, string $longDate): string
    {
        $themes = $day['themes'] ?? [];
        $html = '<header class="wvp-theme">';
        // The date is part of the title so it reads as one line without JS; with JS the active tab shows it.
        $datePrefix = '<span class="wvp-theme-date">' . self::e($longDate . ($themes === [] ? '' : ': ')) . '</span>';
        if ($themes === []) {
            $html .= '<h3 class="wvp-theme-title">' . $datePrefix . '</h3>';
        } else {
            $titles = [];
            $subtitles = [];
            foreach ($themes as $theme) {
                $title = self::e($theme['title']);
                if (!empty($theme['label'])) {
                    $title .= ' <span class="wvp-age">' . self::e($theme['label']) . '</span>';
                }
                $titles[] = '<span class="wvp-theme-part">' . $title . '</span>';
                if (!empty($theme['with'])) {
                    $subtitles[] = self::e($theme['with']);
                }
            }
            $html .= '<h3 class="wvp-theme-title">' . $datePrefix . implode('<span class="wvp-amp" aria-hidden="true"> &amp; </span><span class="wvp-sr">, </span>', $titles) . '</h3>';
            if ($subtitles !== []) {
                $html .= '<p class="wvp-theme-sub">' . implode(' · ', $subtitles) . '</p>';
            }
        }

        return $html . '</header>';
    }

    private function ruler(): string
    {
        $first = (int) ceil($this->start);
        $last = (int) floor($this->end);

        $html = '<div class="wvp-ruler" aria-hidden="true"><span class="wvp-corner"></span><div class="wvp-ticks">';
        for ($hour = $first; $hour <= $last; $hour++) {
            $t = $hour - $this->start;
            $html .= '<span class="wvp-tick" style="--t:' . $t . '">' . sprintf('%02d:00', $hour % 24) . '</span>';
            if ($hour < $this->end) {
                $html .= '<span class="wvp-tick wvp-tick--half" style="--t:' . ($t + 0.5) . '">' . sprintf('%02d:30', $hour % 24) . '</span>';
            }
        }
        $html .= '</div></div>';

        $html .= '<div class="wvp-hourlines" aria-hidden="true">';
        for ($hour = $first; $hour <= $last; $hour++) {
            $html .= '<i style="--t:' . ($hour - $this->start) . '"></i>';
        }

        return $html . '</div>';
    }

    private function stageRow(array $location, array $items): string
    {
        usort($items, fn ($a, $b) => self::hours($a['from']) <=> self::hours($b['from']));
        $hasHighlight = array_filter($items, fn ($item) => !empty($item['highlight'])) !== [];

        $html = '<div class="wvp-row' . ($hasHighlight ? ' wvp-row--flags' : '') . '" data-location="' . self::e($location['id']) . '">';
        $html .= $this->label($location);
        $html .= '<ol class="wvp-lane" style="--lanes:1">';

        $firstFrom = self::hours($items[0]['from']);
        if ($firstFrom - $this->start >= 1) {
            $html .= '<li class="wvp-gap" aria-hidden="true" style="--from:0;--dur:' . ($firstFrom - $this->start) . '"><span>'
                . self::e('Vanaf ' . $items[0]['from'] . ' · ' . $items[0]['title']) . '</span></li>';
        }

        $alternate = 0;
        foreach ($items as $item) {
            $from = self::hours($item['from']);
            $to = self::hours($item['to']);
            $highlight = !empty($item['highlight']);

            $classes = 'wvp-slot';
            if ($highlight) {
                $classes .= ' wvp-slot--highlight';
            } elseif ($alternate++ % 2) {
                $classes .= ' wvp-slot--alt';
            }
            if (!$highlight && $to - $from <= 0.5) {
                $classes .= ' wvp-slot--short';
            }
            if ($highlight && $to - $from >= 0.5) {
                $classes .= ' wvp-slot--roomy';
            }

            $html .= '<li class="' . $classes . '" style="--from:' . ($from - $this->start) . ';--dur:' . ($to - $from) . '" data-id="' . self::e($this->slotKey($item, $location['id'])) . '" data-from="' . $from . '" data-to="' . $to . '">';
            $html .= '<div class="wvp-slot-inner">';
            if ($highlight) {
                $html .= '<span class="wvp-flag">' . self::e($item['title']) . '</span>';
                $html .= '<time class="wvp-time">' . self::e($item['from']) . '</time>';
                $html .= '<span class="wvp-sr">' . self::e($item['title'] . (empty($item['with']) ? '' : ', ' . $item['with'])) . '</span>';
            } else {
                $html .= '<time class="wvp-time">' . self::e($item['from']) . '<span class="wvp-time-end">–' . self::e(self::clock($item['to'])) . '</span></time>';
                $html .= '<span class="wvp-name">' . self::e($item['title']);
                if (!empty($item['label'])) {
                    $html .= ' <span class="wvp-age">' . self::e($item['label']) . '</span>';
                }
                $html .= '</span>';
                if (!empty($item['with'])) {
                    $html .= '<span class="wvp-with">' . self::e($item['with']) . '</span>';
                }
                $html .= $this->star($item['title']);
            }
            $html .= '</div></li>';
        }

        return $html . '</ol></div>';
    }

    private function barsHeader(): string
    {
        $dayparts = $this->data['dayparts'] ?? [];
        $html = '<div class="wvp-row wvp-row--divider"><h4 class="wvp-label wvp-label--divider">Horeca</h4><div class="wvp-dayparts" aria-hidden="true">';
        foreach (['afternoon', 'evening'] as $key) {
            if (!isset($dayparts[$key])) {
                continue;
            }
            $from = self::hours($dayparts[$key]['from']);
            $to = self::hours($dayparts[$key]['to']);
            $html .= '<span style="--from:' . ($from - $this->start) . ';--dur:' . ($to - $from) . '"><b>' . self::e($dayparts[$key]['label']) . '</b></span>';
        }

        return $html . '</div></div>';
    }

    private function barRow(array $location, array $items): string
    {
        $dayparts = $this->data['dayparts'] ?? [];
        $lanes = ['afternoon' => 0, 'evening' => 0];
        $placed = [];

        // Several items in the same daypart get their own lane instead of invented times.
        foreach ($items as $item) {
            $daypart = $item['daypart'] ?? 'evening';
            if ($daypart === 'allday') {
                $lane = max($lanes);
                $lanes['afternoon'] = $lanes['evening'] = $lane + 1;
                $from = $this->start;
                $to = $this->end;
            } else {
                $lane = $lanes[$daypart] ?? 0;
                $lanes[$daypart] = $lane + 1;
                $from = self::hours($dayparts[$daypart]['from'] ?? '19:00');
                $to = self::hours($dayparts[$daypart]['to'] ?? '24:00');
            }
            $placed[] = [$item, $daypart, $lane, $from, $to];
        }

        $laneCount = max(1, max($lanes));
        $html = '<div class="wvp-row wvp-row--bar" data-location="' . self::e($location['id']) . '" style="--lanes:' . $laneCount . '">';
        $html .= $this->label($location);
        $html .= '<ol class="wvp-lane">';

        foreach ($placed as [$item, $daypart, $lane, $from, $to]) {
            $daypartLabel = $daypart === 'allday'
                ? ($dayparts['afternoon']['label'] ?? 'Middag') . ' + ' . mb_strtolower($dayparts['evening']['label'] ?? 'avond')
                : ($dayparts[$daypart]['label'] ?? ucfirst($daypart));

            $html .= '<li class="wvp-slot wvp-slot--daypart' . ($daypart === 'allday' ? ' wvp-slot--allday' : '') . '" style="--from:' . ($from - $this->start) . ';--dur:' . ($to - $from) . ';--lane:' . $lane . '" data-id="' . self::e($this->slotKey($item, $location['id'])) . '" data-from="' . $from . '" data-to="' . $to . '">';
            $html .= '<div class="wvp-slot-inner">';
            $html .= '<span class="wvp-part">' . self::e($daypartLabel) . '</span>';
            $html .= '<span class="wvp-name">' . self::e($item['title']) . '</span>';
            if (!empty($item['with'])) {
                $html .= '<span class="wvp-with">' . self::e($item['with']) . '</span>';
            }
            $html .= $this->star($item['title']);
            $html .= '</div></li>';
        }

        return $html . '</ol></div>';
    }

    private function label(array $location): string
    {
        $html = '<h4 class="wvp-label">' . self::e($location['name']);
        if (!empty($location['where'])) {
            $html .= '<small>' . self::e($location['where']) . '</small>';
        }

        return $html . '</h4>';
    }

    private function star(string $title): string
    {
        return '<button type="button" class="wvp-star" aria-pressed="false" aria-label="' . self::e($title . ' bewaren in Mijn programma') . '">'
            . '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 3.2l2.6 5.5 6 .8-4.4 4.1 1.1 5.9L12 16.6l-5.3 2.9 1.1-5.9-4.4-4.1 6-.8z"/></svg>'
            . '</button>';
    }

    private function toolbar(): string
    {
        return '<div class="wvp-toolbar">'
            . '<div class="wvp-filter" role="group" aria-label="Weergave">'
            . '<button type="button" class="wvp-chip" data-filter="all" aria-pressed="true">Hele dag</button>'
            . '<button type="button" class="wvp-chip" data-filter="mine" aria-pressed="false">Mijn programma <span class="wvp-count">0</span></button>'
            . '</div>'
            . '<button type="button" class="wvp-chip wvp-jump" hidden>Nu</button>'
            . '</div>';
    }

    private function minePanel(): string
    {
        return '<section class="wvp-mine" hidden aria-labelledby="wvp-mine-title">'
            . '<h3 class="wvp-mine-title" id="wvp-mine-title">Mijn programma</h3>'
            . '<div class="wvp-mine-list"></div>'
            . '<p class="wvp-hint" role="status">Tik op het sterretje bij een act. Dan staat hij hier, ook als je de pagina later weer opent.</p>'
            . '</section>';
    }

    private function footer(): string
    {
        $html = '<footer class="wvp-footer">';
        if (!empty($this->data['note'])) {
            $html .= '<p>' . self::e($this->data['note']) . '</p>';
        }

        return $html . '<p class="wvp-nix">NIX18</p></footer>';
    }

    public static function hours(string $time): float
    {
        [$h, $m] = array_map('intval', explode(':', $time) + [0, 0]);

        return $h + $m / 60;
    }

    private static function clock(string $time): string
    {
        return $time === '24:00' ? '00:00' : $time;
    }

    private static function date(string $date): \DateTimeImmutable
    {
        return new \DateTimeImmutable($date . ' 12:00:00', new \DateTimeZone('Europe/Amsterdam'));
    }

    private function slotKey(array $item, string $locationId): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $item['title']) ?: $item['title'];
        $slug = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $ascii), '-'));

        return implode('_', [$this->currentDate, $locationId, $item['from'] ?? ($item['daypart'] ?? ''), $slug]);
    }

    private static function joinNames(array $names): string
    {
        if (count($names) < 2) {
            return implode('', $names);
        }
        $last = array_pop($names);

        return implode(', ', $names) . ' en ' . $last;
    }

    private static function e(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

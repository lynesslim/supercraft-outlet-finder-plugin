<?php

if (!defined('ABSPATH')) {
    exit;
}

class TS_TOF_Shortcode
{
    public function __construct()
    {
        add_shortcode('ts_tyre_outlets', [$this, 'render']);
    }

    public function render()
    {
        ob_start();

        $outlets = $this->get_outlets();

        if (empty($outlets)) {
            return '<p style="padding:40px;text-align:center;color:#555;">' .
                   __('No outlets found. Add outlets in TS Outlets → Add New.', 'ts-tof') .
                   '</p>';
        }

        $brand       = TS_TOF_Settings::get('brand_name');
        $brand_sub   = TS_TOF_Settings::get('brand_subtitle');
        $region      = TS_TOF_Settings::get('region');
        $region_sub  = TS_TOF_Settings::get('region_sub');
        $map_lat     = TS_TOF_Settings::get('map_center_lat');
        $map_lng     = TS_TOF_Settings::get('map_center_lng');
        $map_zoom    = TS_TOF_Settings::get('map_zoom');
        $fly_zoom    = TS_TOF_Settings::get('fly_zoom');

        $brand_upper = strtoupper($brand);
        $brand_parts = explode(' ', $brand_upper, 2);
        $brand_first = $brand_parts[0] ?? $brand_upper;
        $brand_last  = isset($brand_parts[1]) ? ' <em>' . esc_html($brand_parts[1]) . '</em>' : '';

        $json = wp_json_encode($outlets, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        $outlet_count = count($outlets);

        $this->render_styles();
        ?>
        <div class="ts-tyre-outlet-widget">

            <div id="cur"></div>
            <div id="cur-dot"></div>

            <div id="ldr">
                <div class="ldr-brand"><?php echo $brand_first . $brand_last; ?></div>
                <div class="ldr-sub">Locating Outlets</div>
                <div class="ldr-ring"></div>
            </div>

            <div id="app">

                <div id="sb">

                    <div id="det">
                        <div id="back">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M9 2L4 7L9 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>All Outlets</span>
                        </div>

                        <div class="d-bignum" id="d-num">01</div>
                        <div class="d-title" id="d-name">—</div>
                        <div class="d-tag" id="d-area">—</div>
                        <div class="d-img-wrap" id="d-img-wrap" style="display:none;">
                            <img id="d-img" src="" alt="Outlet Image">
                        </div>
                        <div class="d-div"></div>

                        <div class="d-row">
                            <div class="d-ico">
                                <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                                    <circle cx="7" cy="6" r="2.5" stroke="currentColor" stroke-width="1.3" />
                                    <path d="M7 1C4.24 1 2 3.24 2 6c0 3.75 5 8 5 8s5-4.25 5-8c0-2.76-2.24-5-5-5z" stroke="currentColor" stroke-width="1.3" />
                                </svg>
                            </div>
                            <div>
                                <div class="d-lbl">Address</div>
                                <div class="d-val" id="d-addr">—</div>
                            </div>
                        </div>

                        <div class="d-row">
                            <div class="d-ico">
                                <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                                    <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.3" />
                                    <path d="M7 4v3.5l2.5 1.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <div class="d-lbl">Operating Hours</div>
                                <div class="d-val" id="d-hrs">—</div>
                            </div>
                        </div>

                        <div class="d-row">
                            <div class="d-ico">
                                <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                                    <path d="M2 2.5C2 2.5 3 5.5 5.5 8S11.5 12 11.5 12l1-3-3-1-1 1.5S7 8.5 6 7.5 5.5 5.5 5.5 5.5L7 4.5 6 1.5 2 2.5z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div>
                                <div class="d-lbl">Phone</div>
                                <div class="d-val" id="d-tel">—</div>
                            </div>
                        </div>

                        <div class="d-acts">
                            <button class="btn-p" id="btn-dir">Get Directions ↗</button>
                            <button class="btn-s" id="btn-call">Call Outlet</button>
                        </div>
                    </div>

                    <div id="hdr">
                        <div class="logo-row">
                            <svg class="logo-svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                                <circle cx="19" cy="19" r="17" stroke="currentColor" stroke-width="1.5" />
                                <circle cx="19" cy="19" r="9.5" stroke="currentColor" stroke-width="1.5" />
                                <circle cx="19" cy="19" r="3" fill="currentColor" />
                                <line x1="19" y1="1.5" x2="19" y2="9.5" stroke="currentColor" stroke-width="1.2" />
                                <line x1="19" y1="28.5" x2="19" y2="36.5" stroke="currentColor" stroke-width="1.2" />
                                <line x1="1.5" y1="19" x2="9.5" y2="19" stroke="currentColor" stroke-width="1.2" />
                                <line x1="28.5" y1="19" x2="36.5" y2="19" stroke="currentColor" stroke-width="1.2" />
                                <line x1="5" y1="5" x2="11" y2="11" stroke="currentColor" stroke-width="1" />
                                <line x1="27" y1="27" x2="33" y2="33" stroke="currentColor" stroke-width="1" />
                                <line x1="33" y1="5" x2="27" y2="11" stroke="currentColor" stroke-width="1" />
                                <line x1="11" y1="27" x2="5" y2="33" stroke="currentColor" stroke-width="1" />
                            </svg>
                            <div>
                                <div class="brand"><?php echo $brand_first . $brand_last; ?></div>
                                <div class="sub"><?php echo esc_html($brand_sub); ?></div>
                            </div>
                        </div>

                        <div class="eyebrow">Service Network</div>
                        <div class="headline">Find an Outlet</div>
                    </div>

                    <div id="srch-wrap">
                        <span id="srch-ico">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <circle cx="6" cy="6" r="4.5" stroke="currentColor" stroke-width="1.3" />
                                <path d="M10 10L13 13" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                            </svg>
                        </span>
                        <input type="text" id="srch" placeholder="Search area or outlet…" autocomplete="off" spellcheck="false">
                    </div>

                    <div id="list"></div>

                    <div id="ftr">
                        <div class="ftr-label">Outlets in<br><?php echo esc_html($region); ?></div>
                        <div class="ftr-num" id="fc"><?php echo $outlet_count; ?></div>
                    </div>

                </div>

                <div id="map-wrap">
                    <div id="map"></div>

                    <div id="topbar">
                        <div class="tb-badge"><?php echo esc_html($region_sub); ?></div>
                    </div>

                    <div id="hud">
                        <div class="hud-big" id="hud-n"><?php echo $outlet_count; ?></div>
                        <div class="hud-sm">Outlets</div>
                        <div class="hud-div"></div>
                        <div class="hud-tag" id="hud-loc"><?php echo esc_html($region); ?></div>
                    </div>
                </div>

            </div>

        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const root = document.querySelector('.ts-tyre-outlet-widget');
            if (!root) return;

            if (typeof L === 'undefined') {
                console.error('Leaflet failed to load.');
                return;
            }

            const outlets = <?php echo $json; ?>;

            const cur = root.querySelector('#cur');
            const cdot = root.querySelector('#cur-dot');

            root.addEventListener('mousemove', function (e) {
                cur.style.left = e.clientX + 'px';
                cur.style.top = e.clientY + 'px';
                cdot.style.left = e.clientX + 'px';
                cdot.style.top = e.clientY + 'px';
            });

            root.addEventListener('mousedown', function () {
                cur.classList.add('click');
            });

            root.addEventListener('mouseup', function () {
                cur.classList.remove('click');
            });

            root.addEventListener('mouseover', function (e) {
                if (e.target.closest('button,input,.oi,#back,.leaflet-control-zoom a')) {
                    cur.classList.add('expand');
                } else {
                    cur.classList.remove('expand');
                }
            });

            const mapElement = root.querySelector('#map');

            const map = L.map(mapElement, {
                center: [<?php echo $map_lat; ?>, <?php echo $map_lng; ?>],
                zoom: <?php echo $map_zoom; ?>,
                zoomControl: false,
                attributionControl: true
            });

            L.control.zoom({
                position: 'bottomright'
            }).addTo(map);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            const markers = {};
            let selId = null;

            function mkIcon(o, act) {
                let imgHtml = o.img ? '<div class="ts-tip-img" style="background-image:url('+o.img+')"></div>' : '';
                return L.divIcon({
                    className: '',
                    html:
                        '<div class="ts-m' + (act ? ' act' : '') + '" id="mw' + o.id + '">' +
                        '<div class="ts-ring"></div>' +
                        '<div class="ts-core">' + String(o.id).padStart(2, '0') + '</div>' +
                        '<div class="ts-tip ' + (o.img ? 'has-img' : '') + '">' + imgHtml + '<div class="ts-tip-txt">' + o.name + '</div></div>' +
                        '</div>',
                    iconSize: [44, 44],
                    iconAnchor: [22, 22],
                    popupAnchor: [0, -28]
                });
            }

            outlets.forEach(function (o) {
                const m = L.marker([o.lat, o.lng], {
                    icon: mkIcon(o, false)
                }).addTo(map);

                m.on('click', function () {
                    select(o.id);
                });

                markers[o.id] = m;
            });

            function renderList(arr) {
                const el = root.querySelector('#list');
                const fc = root.querySelector('#fc');

                el.innerHTML = '';

                if (!arr.length) {
                    el.innerHTML = '<div class="no-res">No outlets found</div>';
                    fc.textContent = '0';
                    return;
                }

                fc.textContent = arr.length;

                arr.forEach(function (o, i) {
                    const d = document.createElement('div');
                    d.className = 'oi' + (selId === o.id ? ' act' : '');
                    d.dataset.id = o.id;
                    d.style.animationDelay = (i * 0.045) + 's';

                    d.innerHTML =
                        (o.img ? '<div class="oi-bg-img" style="background-image:url(' + o.img + ')"></div>' : '') +
                        '<div class="oi-num">' + String(o.id).padStart(2, '0') + '</div>' +
                        '<div class="oi-info">' +
                        '<div class="oi-name">' + o.name + '</div>' +
                        '<div class="oi-area">' + o.area + '</div>' +
                        '</div>' +
                        '<div class="oi-arr">→</div>';

                    d.addEventListener('click', function () {
                        select(o.id);
                    });

                    el.appendChild(d);
                });
            }

            renderList(outlets);

            root.querySelector('#srch').addEventListener('input', function (e) {
                const q = e.target.value.toLowerCase();

                const filtered = outlets.filter(function (o) {
                    return o.name.toLowerCase().includes(q) || o.area.toLowerCase().includes(q);
                });

                renderList(filtered);
            });

            function select(id) {
                const o = outlets.find(function (x) {
                    return x.id === id;
                });

                if (!o) return;

                selId = id;

                outlets.forEach(function (x) {
                    markers[x.id].setIcon(mkIcon(x, x.id === id));
                });

                map.flyTo([o.lat, o.lng], <?php echo $fly_zoom; ?>, {
                    animate: true,
                    duration: 1.1,
                    easeLinearity: 0.2
                });

                root.querySelector('#d-num').textContent = String(o.id).padStart(2, '0');
                root.querySelector('#d-name').textContent = o.name;
                root.querySelector('#d-area').textContent = o.area;
                
                const dImgWrap = root.querySelector('#d-img-wrap');
                const dImg = root.querySelector('#d-img');
                if (o.img) {
                    dImg.src = o.img;
                    dImgWrap.style.display = 'block';
                } else {
                    dImgWrap.style.display = 'none';
                    dImg.src = '';
                }

                root.querySelector('#d-addr').textContent = o.addr;
                root.querySelector('#d-hrs').textContent = o.hrs;
                root.querySelector('#d-tel').textContent = o.phone;

                root.querySelector('#btn-dir').onclick = function () {
                    window.open(o.maps, '_blank');
                };

                root.querySelector('#btn-call').onclick = function () {
                    window.open('tel:' + o.phone, '_self');
                };

                root.querySelector('#det').classList.add('open');
                root.querySelector('#hud-loc').textContent = o.name;

                root.querySelectorAll('.oi').forEach(function (el) {
                    el.classList.toggle('act', Number(el.dataset.id) === id);
                });
            }

            root.querySelector('#back').addEventListener('click', function () {
                root.querySelector('#det').classList.remove('open');
                selId = null;

                outlets.forEach(function (o) {
                    markers[o.id].setIcon(mkIcon(o, false));
                });

                map.flyTo([<?php echo $map_lat; ?>, <?php echo $map_lng; ?>], <?php echo $map_zoom; ?>, {
                    animate: true,
                    duration: 1.0
                });

                root.querySelector('#hud-loc').textContent = '<?php echo esc_js($region); ?>';

                root.querySelectorAll('.oi').forEach(function (el) {
                    el.classList.remove('act');
                });
            });

            map.on('click', function () {
                if (selId) {
                    root.querySelector('#back').click();
                }
            });

            map.whenReady(function () {
                setTimeout(function () {
                    root.querySelector('#ldr').classList.add('gone');
                    map.invalidateSize();
                }, 900);
            });

            setTimeout(function () {
                root.querySelector('#ldr').classList.add('gone');
                map.invalidateSize();
            }, 3000);

            window.addEventListener('resize', function () {
                map.invalidateSize();
            });
        });
        </script>
        <?php

        return ob_get_clean();
    }

    private function render_styles()
    {
        $s = function ($key) {
            return TS_TOF_Settings::get($key);
        };
        ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Anton&family=Barlow+Condensed:ital,wght@0,300;0,400;0,600;0,700;1,300&family=JetBrains+Mono:wght@300;400&display=swap" rel="stylesheet">

        <style>
            .ts-tyre-outlet-widget,
            .ts-tyre-outlet-widget *,
            .ts-tyre-outlet-widget *::before,
            .ts-tyre-outlet-widget *::after {
                box-sizing: border-box;
            }

            .ts-tyre-outlet-widget {
                --black: <?php echo $s('color_bg'); ?>;
                --dark: <?php echo $s('color_dark'); ?>;
                --surface: <?php echo $s('color_surface'); ?>;
                --surface2: <?php echo $s('color_surface2'); ?>;
                --border: <?php echo $s('color_border'); ?>;
                --border2: <?php echo $s('color_border2'); ?>;
                --gold: <?php echo $s('color_primary'); ?>;
                --gold-dim: color-mix(in srgb, <?php echo $s('color_primary'); ?> 8%, transparent);
                --gold-glow: color-mix(in srgb, <?php echo $s('color_primary'); ?> 25%, transparent);
                --red: #E03030;
                --white: <?php echo $s('color_text'); ?>;
                --muted: <?php echo $s('color_muted'); ?>;
                --muted2: <?php echo $s('color_muted2'); ?>;
                --sw: 380px;

                width: 100%;
                height: 100vh;
                min-height: 720px;
                background: var(--black);
                color: var(--white);
                font-family: 'Barlow Condensed', sans-serif;
                overflow: hidden;
                position: relative;
                cursor: none;
            }

            .ts-tyre-outlet-widget #cur {
                position: fixed;
                width: 10px;
                height: 10px;
                border: 1.5px solid var(--gold);
                border-radius: 50%;
                pointer-events: none;
                z-index: 99999;
                transform: translate(-50%, -50%);
                transition: width .18s, height .18s, background .18s, border-color .18s, opacity .2s;
                mix-blend-mode: normal;
                opacity: 0;
            }

            .ts-tyre-outlet-widget #cur-dot {
                position: fixed;
                width: 3px;
                height: 3px;
                background: var(--gold);
                border-radius: 50%;
                pointer-events: none;
                z-index: 99999;
                transform: translate(-50%, -50%);
                opacity: 0;
                transition: opacity .2s;
            }

            .ts-tyre-outlet-widget:hover #cur,
            .ts-tyre-outlet-widget:hover #cur-dot {
                opacity: 1;
            }

            .ts-tyre-outlet-widget #cur.expand {
                width: 28px;
                height: 28px;
                border-color: color-mix(in srgb, var(--gold) 40%, transparent);
            }

            .ts-tyre-outlet-widget #cur.click {
                background: var(--gold-dim);
                width: 8px;
                height: 8px;
            }

            .ts-tyre-outlet-widget #app {
                display: flex;
                width: 100%;
                height: 100%;
                position: relative;
            }

            .ts-tyre-outlet-widget #sb {
                width: var(--sw);
                flex-shrink: 0;
                background: var(--dark);
                display: flex;
                flex-direction: column;
                border-right: 1px solid var(--border);
                position: relative;
                z-index: 100;
                overflow: hidden;
            }

            .ts-tyre-outlet-widget #sb::after {
                content: '';
                position: absolute;
                inset: 0;
                background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,.012) 2px, rgba(255,255,255,.012) 4px);
                pointer-events: none;
                z-index: 0;
            }

            .ts-tyre-outlet-widget #hdr {
                padding: 26px 28px 20px;
                border-bottom: 1px solid var(--border);
                position: relative;
                z-index: 1;
                flex-shrink: 0;
            }

            .ts-tyre-outlet-widget .logo-row {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 16px;
            }

            .ts-tyre-outlet-widget .logo-svg {
                flex-shrink: 0;
                color: var(--gold);
            }

            .ts-tyre-outlet-widget .brand {
                font-family: 'Anton', sans-serif;
                font-size: 26px;
                letter-spacing: 3px;
                line-height: 1;
                color: var(--white);
            }

            .ts-tyre-outlet-widget .brand em {
                color: var(--gold);
                font-style: normal;
            }

            .ts-tyre-outlet-widget .sub {
                font-size: 9px;
                letter-spacing: 5px;
                text-transform: uppercase;
                color: var(--muted);
                margin-top: 3px;
            }

            .ts-tyre-outlet-widget .eyebrow {
                font-size: 9px;
                letter-spacing: 5px;
                text-transform: uppercase;
                color: var(--muted);
                margin-bottom: 5px;
            }

            .ts-tyre-outlet-widget .headline {
                font-family: 'Anton', sans-serif;
                font-size: 20px;
                letter-spacing: 2px;
                color: var(--white);
                line-height: 1;
            }

            .ts-tyre-outlet-widget #srch-wrap {
                padding: 14px 28px;
                border-bottom: 1px solid var(--border);
                position: relative;
                z-index: 1;
                flex-shrink: 0;
            }

            .ts-tyre-outlet-widget #srch-ico {
                position: absolute;
                left: 42px;
                top: 50%;
                transform: translateY(-50%);
                color: var(--muted);
                font-size: 15px;
                pointer-events: none;
            }

            .ts-tyre-outlet-widget #srch {
                width: 100%;
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: 3px;
                padding: 9px 14px 9px 36px;
                color: var(--white);
                font-family: 'Barlow Condensed', sans-serif;
                font-size: 14px;
                letter-spacing: .5px;
                outline: none;
                transition: border-color .2s, background .2s;
                cursor: none;
            }

            .ts-tyre-outlet-widget #srch:focus {
                border-color: color-mix(in srgb, var(--gold) 35%, transparent);
                background: var(--surface2);
            }

            .ts-tyre-outlet-widget #srch::placeholder {
                color: var(--muted);
            }

            .ts-tyre-outlet-widget #list {
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
                position: relative;
                z-index: 1;
                padding: 6px 0;
            }

            .ts-tyre-outlet-widget #list::-webkit-scrollbar {
                width: 2px;
            }

            .ts-tyre-outlet-widget #list::-webkit-scrollbar-track {
                background: transparent;
            }

            .ts-tyre-outlet-widget #list::-webkit-scrollbar-thumb {
                background: var(--border2);
                border-radius: 1px;
            }

            .ts-tyre-outlet-widget .oi {
                display: flex;
                align-items: center;
                padding: 13px 28px;
                cursor: none;
                position: relative;
                transition: background .2s;
                gap: 14px;
                overflow: hidden;
                border-bottom: 1px solid var(--border);
                animation: tsTyreSi .45s cubic-bezier(.16,1,.3,1) both;
            }

            .ts-tyre-outlet-widget .oi::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                width: 2px;
                height: 100%;
                background: var(--gold);
                transform: scaleY(0);
                transition: transform .35s cubic-bezier(.16,1,.3,1);
                transform-origin: bottom;
            }

            .ts-tyre-outlet-widget .oi:hover,
            .ts-tyre-outlet-widget .oi.act {
                background: var(--gold-dim);
            }

            .ts-tyre-outlet-widget .oi:hover::before,
            .ts-tyre-outlet-widget .oi.act::before {
                transform: scaleY(1);
                transform-origin: top;
            }

            .ts-tyre-outlet-widget .oi-num {
                font-family: 'Anton', sans-serif;
                font-size: 11px;
                color: var(--gold);
                min-width: 24px;
                letter-spacing: 1px;
                opacity: .5;
                transition: opacity .2s;
            }

            .ts-tyre-outlet-widget .oi:hover .oi-num,
            .ts-tyre-outlet-widget .oi.act .oi-num {
                opacity: 1;
            }

            .ts-tyre-outlet-widget .oi-info {
                flex: 1;
                min-width: 0;
            }

            .ts-tyre-outlet-widget .oi-name {
                font-size: 15px;
                font-weight: 700;
                letter-spacing: 1.5px;
                text-transform: uppercase;
                color: var(--white);
                line-height: 1.05;
            }

            .ts-tyre-outlet-widget .oi-area {
                font-size: 10px;
                letter-spacing: 2px;
                color: var(--muted2);
                text-transform: uppercase;
                margin-top: 3px;
            }

            .ts-tyre-outlet-widget .oi-arr {
                color: var(--muted);
                font-size: 12px;
                transition: transform .2s, color .2s;
            }

            .ts-tyre-outlet-widget .oi:hover .oi-arr,
            .ts-tyre-outlet-widget .oi.act .oi-arr {
                transform: translateX(4px);
                color: var(--gold);
            }

            .ts-tyre-outlet-widget .no-res {
                padding: 48px 28px;
                text-align: center;
                color: var(--muted);
                font-size: 11px;
                letter-spacing: 3px;
                text-transform: uppercase;
            }

            @keyframes tsTyreSi {
                from { opacity: 0; transform: translateX(-16px); }
                to   { opacity: 1; transform: translateX(0); }
            }

            .ts-tyre-outlet-widget #ftr {
                padding: 14px 28px;
                border-top: 1px solid var(--border);
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: relative;
                z-index: 1;
                flex-shrink: 0;
            }

            .ts-tyre-outlet-widget .ftr-label {
                font-size: 9px;
                letter-spacing: 4px;
                text-transform: uppercase;
                color: var(--muted);
            }

            .ts-tyre-outlet-widget .ftr-num {
                font-family: 'Anton', sans-serif;
                font-size: 22px;
                color: var(--gold);
                line-height: 1;
            }

            .ts-tyre-outlet-widget #det {
                position: absolute;
                inset: 0;
                background: var(--dark);
                transform: translateX(-100%);
                transition: transform .55s cubic-bezier(.16,1,.3,1);
                z-index: 200;
                display: flex;
                flex-direction: column;
                padding: 28px;
                overflow: hidden;
            }

            .ts-tyre-outlet-widget #det::after {
                content: '';
                position: absolute;
                inset: 0;
                background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,.012) 2px, rgba(255,255,255,.012) 4px);
                pointer-events: none;
                z-index: 0;
            }

            .ts-tyre-outlet-widget #det.open {
                transform: translateX(0);
            }

            .ts-tyre-outlet-widget #back {
                display: flex;
                align-items: center;
                gap: 8px;
                color: var(--muted2);
                font-size: 10px;
                letter-spacing: 4px;
                text-transform: uppercase;
                cursor: none;
                margin-bottom: 28px;
                transition: color .2s;
                width: fit-content;
                position: relative;
                z-index: 1;
            }

            .ts-tyre-outlet-widget #back:hover {
                color: var(--gold);
            }

            .ts-tyre-outlet-widget #back svg {
                transition: transform .2s;
            }

            .ts-tyre-outlet-widget #back:hover svg {
                transform: translateX(-3px);
            }

            .ts-tyre-outlet-widget .d-bignum {
                font-family: 'Anton', sans-serif;
                font-size: 100px;
                line-height: 1;
                color: var(--surface2);
                position: absolute;
                right: 16px;
                top: 52px;
                letter-spacing: -3px;
                z-index: 0;
                user-select: none;
                transition: opacity .4s;
            }

            .ts-tyre-outlet-widget .d-title {
                font-family: 'Anton', sans-serif;
                font-size: 34px;
                line-height: 1.05;
                letter-spacing: 2px;
                text-transform: uppercase;
                color: var(--white);
                position: relative;
                z-index: 1;
                margin-bottom: 6px;
            }

            .ts-tyre-outlet-widget .d-tag {
                display: inline-block;
                background: var(--gold);
                color: var(--black);
                font-size: 9px;
                font-weight: 700;
                letter-spacing: 3px;
                text-transform: uppercase;
                padding: 3px 10px;
                border-radius: 2px;
                margin-bottom: 24px;
                position: relative;
                z-index: 1;
                width: fit-content;
            }

            .ts-tyre-outlet-widget .d-img-wrap {
                width: 100%;
                height: 140px;
                background-color: var(--surface2);
                border-radius: 4px;
                overflow: hidden;
                margin-bottom: 22px;
                position: relative;
                z-index: 1;
                border: 1px solid var(--border);
            }
            .ts-tyre-outlet-widget .d-img-wrap img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .ts-tyre-outlet-widget .d-div {
                height: 1px;
                background: var(--border);
                margin-bottom: 22px;
                position: relative;
                z-index: 1;
            }

            .ts-tyre-outlet-widget .d-row {
                display: flex;
                gap: 14px;
                margin-bottom: 18px;
                position: relative;
                z-index: 1;
                align-items: flex-start;
            }

            .ts-tyre-outlet-widget .d-ico {
                color: var(--gold);
                font-size: 13px;
                margin-top: 1px;
                min-width: 18px;
                text-align: center;
            }

            .ts-tyre-outlet-widget .d-lbl {
                font-size: 9px;
                letter-spacing: 3px;
                text-transform: uppercase;
                color: var(--muted);
                margin-bottom: 3px;
            }

            .ts-tyre-outlet-widget .d-val {
                font-size: 12px;
                font-family: 'JetBrains Mono', monospace;
                color: var(--white);
                line-height: 1.6;
                letter-spacing: .3px;
                white-space: pre-line;
            }

            .ts-tyre-outlet-widget .d-acts {
                margin-top: auto;
                position: relative;
                z-index: 1;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .ts-tyre-outlet-widget .btn-p {
                width: 100%;
                padding: 15px;
                background: var(--gold);
                color: var(--black);
                border: none;
                font-family: 'Anton', sans-serif;
                font-size: 15px;
                letter-spacing: 3px;
                text-transform: uppercase;
                cursor: none;
                transition: background .15s, transform .1s;
                border-radius: 2px;
            }

            .ts-tyre-outlet-widget .btn-p:hover {
                background: color-mix(in srgb, var(--gold) 85%, white);
                transform: translateY(-1px);
            }

            .ts-tyre-outlet-widget .btn-p:active {
                transform: translateY(0);
            }

            .ts-tyre-outlet-widget .btn-s {
                width: 100%;
                padding: 13px;
                background: transparent;
                color: var(--muted2);
                border: 1px solid var(--border2);
                font-family: 'Barlow Condensed', sans-serif;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: 3px;
                text-transform: uppercase;
                cursor: none;
                transition: border-color .2s, color .2s;
                border-radius: 2px;
            }

            .ts-tyre-outlet-widget .btn-s:hover {
                border-color: color-mix(in srgb, var(--gold) 40%, transparent);
                color: var(--gold);
            }

            .ts-tyre-outlet-widget #map-wrap {
                flex: 1;
                position: relative;
            }

            .ts-tyre-outlet-widget #map {
                width: 100%;
                height: 100%;
            }

            .ts-tyre-outlet-widget .leaflet-container {
                background: #0a0a0a !important;
            }

            .ts-tyre-outlet-widget .leaflet-control-attribution {
                background: rgba(0,0,0,.4) !important;
                color: #333 !important;
                font-size: 8px !important;
                padding: 2px 6px !important;
            }

            .ts-tyre-outlet-widget .leaflet-control-zoom {
                border: 1px solid rgba(255,255,255,.08) !important;
                border-radius: 3px !important;
                overflow: hidden;
            }

            .ts-tyre-outlet-widget .leaflet-control-zoom a {
                background: var(--dark) !important;
                color: var(--muted) !important;
                border-bottom-color: rgba(255,255,255,.05) !important;
                width: 34px !important;
                height: 34px !important;
                line-height: 34px !important;
                font-size: 16px !important;
            }

            .ts-tyre-outlet-widget .leaflet-control-zoom a:hover {
                background: var(--surface) !important;
                color: var(--gold) !important;
            }

            .ts-tyre-outlet-widget #map-wrap::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 50px;
                background: linear-gradient(to right, var(--dark), transparent);
                z-index: 50;
                pointer-events: none;
            }

            .ts-tyre-outlet-widget .ts-m {
                position: relative;
                width: 44px;
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .ts-tyre-outlet-widget .ts-ring {
                position: absolute;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                border: 1.5px solid color-mix(in srgb, var(--gold) 20%, transparent);
                animation: tsTyreRp 2.4s ease-out infinite;
            }

            .ts-tyre-outlet-widget .ts-core {
                width: 26px;
                height: 26px;
                background: var(--dark);
                border: 1.5px solid color-mix(in srgb, var(--gold) 55%, transparent);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Anton', sans-serif;
                font-size: 9px;
                color: var(--gold);
                letter-spacing: .5px;
                position: relative;
                z-index: 1;
                transition: all .35s cubic-bezier(.16,1,.3,1);
            }

            .ts-tyre-outlet-widget .ts-m.act .ts-core {
                background: var(--gold);
                color: var(--black);
                width: 34px;
                height: 34px;
                font-size: 11px;
                box-shadow: 0 0 0 4px color-mix(in srgb, var(--gold) 15%, transparent),
                            0 0 20px color-mix(in srgb, var(--gold) 30%, transparent);
            }

            .ts-tyre-outlet-widget .ts-m.act .ts-ring {
                animation: tsTyreRpa 1s ease-out infinite;
                border-color: color-mix(in srgb, var(--gold) 50%, transparent);
            }

            .ts-tyre-outlet-widget .ts-tip {
                position: absolute;
                top: -30px;
                background: var(--surface2);
                padding: 0;
                border: 1px solid var(--border2);
                border-radius: 4px;
                font-size: 10px;
                letter-spacing: 1px;
                text-transform: uppercase;
                color: var(--white);
                white-space: nowrap;
                opacity: 0;
                transform: translateY(10px);
                transition: all .2s;
                pointer-events: none;
                box-shadow: 0 4px 12px rgba(0,0,0,.4);
                z-index: 10;
                overflow: hidden;
            }
            .ts-tyre-outlet-widget .ts-tip-txt {
                padding: 4px 10px;
            }
            .ts-tyre-outlet-widget .ts-tip-img {
                width: 100%;
                height: 60px;
                background-size: cover;
                background-position: center;
                border-bottom: 1px solid rgba(255,255,255,0.1);
                display: none;
            }
            .ts-tyre-outlet-widget .ts-tip.has-img .ts-tip-img {
                display: block;
            }
            .ts-tyre-outlet-widget .oi-bg-img {
                position: absolute;
                inset: 0;
                background-size: cover;
                background-position: center;
                opacity: 0;
                transition: opacity 0.3s;
                z-index: -1;
            }
            .ts-tyre-outlet-widget .oi:hover .oi-bg-img,
            .ts-tyre-outlet-widget .oi.act .oi-bg-img {
                opacity: 0.15;
            }

            .ts-tyre-outlet-widget .ts-m:hover .ts-tip {
                opacity: 1;
                transform: translateY(0);
            }

            @keyframes tsTyreRp {
                0%   { transform: scale(.8); opacity: .4; }
                70%  { transform: scale(1.5); opacity: 0; }
                100% { transform: scale(1.5); opacity: 0; }
            }

            @keyframes tsTyreRpa {
                0%   { transform: scale(.85); opacity: .7; }
                70%  { transform: scale(2); opacity: 0; }
                100% { transform: scale(2); opacity: 0; }
            }

            .ts-tyre-outlet-widget #hud {
                position: absolute;
                bottom: 24px;
                right: 24px;
                z-index: 50;
                background: rgba(10,10,10,.85);
                backdrop-filter: blur(10px);
                border: 1px solid var(--border);
                padding: 14px 18px;
                border-radius: 3px;
                pointer-events: none;
                min-width: 130px;
            }

            .ts-tyre-outlet-widget .hud-big {
                font-family: 'Anton', sans-serif;
                font-size: 28px;
                color: var(--gold);
                line-height: 1;
            }

            .ts-tyre-outlet-widget .hud-sm {
                font-size: 9px;
                letter-spacing: 4px;
                text-transform: uppercase;
                color: var(--muted);
                margin-top: 2px;
            }

            .ts-tyre-outlet-widget .hud-div {
                height: 1px;
                background: var(--border);
                margin: 10px 0;
            }

            .ts-tyre-outlet-widget .hud-tag {
                font-size: 10px;
                letter-spacing: 3px;
                text-transform: uppercase;
                color: var(--muted2);
            }

            .ts-tyre-outlet-widget #topbar {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                z-index: 50;
                background: linear-gradient(to bottom, rgba(7,7,7,.7), transparent);
                padding: 20px 24px;
                pointer-events: none;
                display: flex;
                align-items: flex-start;
                justify-content: flex-end;
            }

            .ts-tyre-outlet-widget .tb-badge {
                background: rgba(10,10,10,.8);
                border: 1px solid var(--border);
                padding: 6px 14px;
                border-radius: 2px;
                font-size: 9px;
                letter-spacing: 4px;
                text-transform: uppercase;
                color: var(--muted2);
                backdrop-filter: blur(8px);
            }

            .ts-tyre-outlet-widget #ldr {
                position: absolute;
                inset: 0;
                background: var(--black);
                z-index: 99998;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                transition: opacity .7s, visibility .7s;
            }

            .ts-tyre-outlet-widget #ldr.gone {
                opacity: 0;
                visibility: hidden;
            }

            .ts-tyre-outlet-widget .ldr-brand {
                font-family: 'Anton', sans-serif;
                font-size: 52px;
                letter-spacing: 6px;
                color: var(--white);
                margin-bottom: 4px;
            }

            .ts-tyre-outlet-widget .ldr-brand em {
                color: var(--gold);
                font-style: normal;
            }

            .ts-tyre-outlet-widget .ldr-sub {
                font-size: 9px;
                letter-spacing: 7px;
                text-transform: uppercase;
                color: var(--muted);
                margin-bottom: 52px;
            }

            .ts-tyre-outlet-widget .ldr-ring {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                border: 2px solid var(--surface);
                border-top-color: var(--gold);
                animation: tsTyreSpin .7s linear infinite;
            }

            @keyframes tsTyreSpin {
                to { transform: rotate(360deg); }
            }

            .ts-tyre-outlet-widget .leaflet-popup-content-wrapper {
                background: var(--dark) !important;
                border: 1px solid var(--border2) !important;
                border-radius: 3px !important;
                box-shadow: 0 12px 40px rgba(0,0,0,.7) !important;
                color: var(--white) !important;
            }

            .ts-tyre-outlet-widget .leaflet-popup-content {
                color: var(--white) !important;
                font-family: 'Barlow Condensed', sans-serif !important;
                font-size: 12px !important;
                letter-spacing: 1px !important;
                margin: 8px 14px !important;
                text-transform: uppercase;
            }

            .ts-tyre-outlet-widget .leaflet-popup-tip-container .leaflet-popup-tip {
                background: var(--dark) !important;
            }

            .ts-tyre-outlet-widget .leaflet-popup-close-button {
                color: var(--muted) !important;
                font-size: 18px !important;
                top: 4px !important;
                right: 6px !important;
            }

            .ts-tyre-outlet-widget .leaflet-popup-close-button:hover {
                color: var(--gold) !important;
            }

            @media (max-width: 767px) {
                .ts-tyre-outlet-widget {
                    height: 850px;
                    min-height: 850px;
                }

                .ts-tyre-outlet-widget #app {
                    flex-direction: column;
                }

                .ts-tyre-outlet-widget #sb {
                    width: 100%;
                    height: 45%;
                    min-height: 380px;
                    border-right: 0;
                    border-bottom: 1px solid var(--border);
                }

                .ts-tyre-outlet-widget #map-wrap {
                    height: 55%;
                }

                .ts-tyre-outlet-widget #hdr {
                    padding: 22px 22px 16px;
                }

                .ts-tyre-outlet-widget #srch-wrap {
                    padding: 12px 22px;
                }

                .ts-tyre-outlet-widget .oi {
                    padding: 12px 22px;
                }

                .ts-tyre-outlet-widget #ftr {
                    padding: 12px 22px;
                }

                .ts-tyre-outlet-widget #hud { display: none; }
                .ts-tyre-outlet-widget #topbar { display: none; }
                .ts-tyre-outlet-widget #map-wrap::before { display: none; }

                .ts-tyre-outlet-widget #det {
                    padding: 24px;
                }

                .ts-tyre-outlet-widget .d-title {
                    font-size: 28px;
                }

                .ts-tyre-outlet-widget .d-bignum {
                    font-size: 80px;
                }
            }
        </style>
        <?php
    }

    private function get_outlets()
    {
        $posts = get_posts([
            'post_type'      => 'ts_outlet',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ]);

        $outlets = [];
        $i = 1;

        foreach ($posts as $post) {
            $lat = get_post_meta($post->ID, '_ts_outlet_lat', true);
            $lng = get_post_meta($post->ID, '_ts_outlet_lng', true);

            if (empty($lat) || empty($lng)) {
                continue;
            }

            $img = get_the_post_thumbnail_url($post->ID, 'large');
            if (!$img) {
                $img = '';
            }

            $outlets[] = [
                'id'    => $i,
                'name'  => $post->post_title,
                'area'  => get_post_meta($post->ID, '_ts_outlet_area', true),
                'addr'  => get_post_meta($post->ID, '_ts_outlet_address', true),
                'phone' => get_post_meta($post->ID, '_ts_outlet_phone', true),
                'hrs'   => get_post_meta($post->ID, '_ts_outlet_hours', true),
                'lat'   => (float) $lat,
                'lng'   => (float) $lng,
                'maps'  => get_post_meta($post->ID, '_ts_outlet_maps_url', true),
                'img'   => $img,
            ];

            $i++;
        }

        return $outlets;
    }
}

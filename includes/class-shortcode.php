<?php

if (!defined('ABSPATH')) {
    exit;
}

class SC_OF_Shortcode
{
    public function __construct()
    {
        add_shortcode('supercraft_outlets', [$this, 'render']);
    }

    public function render()
    {
        ob_start();

        $outlets = $this->get_outlets();

        if (empty($outlets)) {
            return '<p style="padding:40px;text-align:center;color:#555;">' .
                   __('No outlets found. Add outlets in Outlets → Add New.', 'supercraft-of') .
                   '</p>';
        }

        $brand       = SC_OF_Settings::get('brand_name');
        $brand_sub   = SC_OF_Settings::get('brand_subtitle');
        $region      = SC_OF_Settings::get('region');
        $region_sub  = SC_OF_Settings::get('region_sub');
        $map_lat     = SC_OF_Settings::get('map_center_lat');
        $map_lng     = SC_OF_Settings::get('map_center_lng');
        $map_zoom    = SC_OF_Settings::get('map_zoom');
        $fly_zoom    = SC_OF_Settings::get('fly_zoom');
        $marker_style     = SC_OF_Settings::get('marker_style');
        $map_style        = SC_OF_Settings::get('map_style');
        $custom_tile_url  = SC_OF_Settings::get('custom_tile_url');
        $custom_tile_attr = SC_OF_Settings::get('custom_tile_attr');

        $brand_upper = strtoupper($brand);
        $brand_parts = explode(' ', $brand_upper, 2);
        $brand_first = $brand_parts[0] ?? $brand_upper;
        $brand_last  = isset($brand_parts[1]) ? ' <em>' . esc_html($brand_parts[1]) . '</em>' : '';

        $json = wp_json_encode($outlets, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        $outlet_count = count($outlets);

        $this->render_styles();
        ?>
        <div class="sc-outlet-widget">

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
            const root = document.querySelector('.sc-outlet-widget');
            if (!root) return;

            if (typeof L === 'undefined') {
                console.error('Leaflet failed to load.');
                return;
            }

            const markerStyle = '<?php echo esc_js($marker_style); ?>';
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
                attributionControl: true,
                scrollWheelZoom: false
            });

            L.control.zoom({
                position: 'bottomright'
            }).addTo(map);

            var tileUrl = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
            var tileAttr = '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/attributions">CARTO</a>';
            var subdomains = 'abcd';

            <?php if ($map_style === 'dark') : ?>
            tileUrl = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
            <?php elseif ($map_style === 'light') : ?>
            tileUrl = 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
            <?php elseif ($map_style === 'esri_streets') : ?>
            tileUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}';
            tileAttr = 'Tiles &copy; Esri';
            subdomains = 'abc';
            <?php elseif ($map_style === 'satellite') : ?>
            tileUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
            tileAttr = 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community';
            subdomains = 'abc';
            <?php elseif ($map_style === 'topo') : ?>
            tileUrl = 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
            tileAttr = 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a>';
            subdomains = 'abc';
            <?php elseif ($map_style === 'streets') : ?>
            tileUrl = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';
            tileAttr = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';
            subdomains = 'abc';
            <?php elseif ($map_style === 'custom' && !empty($custom_tile_url)) : ?>
            tileUrl = <?php echo json_encode($custom_tile_url); ?>;
            tileAttr = <?php echo json_encode($custom_tile_attr); ?>;
            <?php endif; ?>

            L.tileLayer(tileUrl, {
                attribution: tileAttr,
                subdomains: subdomains,
                maxZoom: 20
            }).addTo(map);

            const markers = {};
            let selId = null;

            function mkIcon(o, act) {
                var color = o.color || '';
                var colorStyle = color ? '--mc:' + color + ';' : '';
                var imgHtml = o.img ? '<div class="sc-tip-img" style="background-image:url('+o.img+')"></div>' : '';
                var innerHtml;
                if (markerStyle === 'pinpoint') {
                    innerHtml =
                        '<div class="sc-m sc-pin' + (act ? ' act' : '') + '" id="mw' + o.id + '" style="' + colorStyle + '">' +
                        '<div class="sc-ring"></div>' +
                        '<div class="sc-pin-svg"><svg viewBox="0 0 24 34" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 0C5.37 0 0 5.37 0 12C0 21 12 34 12 34C12 34 24 21 24 12C24 5.37 18.63 0 12 0ZM12 16C9.79 16 8 14.21 8 12C8 9.79 9.79 8 12 8C14.21 8 16 9.79 16 12C16 14.21 14.21 16 12 16Z" fill="currentColor"/></svg></div>' +
                        '<div class="sc-tip ' + (o.img ? 'has-img' : '') + '">' + imgHtml + '<div class="sc-tip-txt">' + o.name + '</div></div>' +
                        '</div>';
                } else {
                    innerHtml =
                        '<div class="sc-m' + (act ? ' act' : '') + '" id="mw' + o.id + '" style="' + colorStyle + '">' +
                        '<div class="sc-ring"></div>' +
                        '<div class="sc-core">' + String(o.id).padStart(2, '0') + '</div>' +
                        '<div class="sc-tip ' + (o.img ? 'has-img' : '') + '">' + imgHtml + '<div class="sc-tip-txt">' + o.name + '</div></div>' +
                        '</div>';
                }
                return L.divIcon({
                    className: '',
                    html: innerHtml,
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

            var listEl = root.querySelector('#list');
            if (listEl) {
                listEl.addEventListener('wheel', function (e) {
                    e.stopPropagation();
                }, { passive: false });
            }
        });
        </script>
        <?php

        return ob_get_clean();
    }

    private function render_styles()
    {
        $s = function ($key) {
            return SC_OF_Settings::get($key);
        };
        $c = function ($key) use ($s) {
            $val = $s($key);
            if (strpos($val, 'e-global-color') === 0) {
                return 'var(--' . esc_attr($val) . ')';
            }
            return esc_attr($val);
        };
        $f = function ($key) use ($s) {
            $val = $s($key);
            if ($val === 'inherit') return 'inherit';
            if (strpos($val, 'e-global-typography') === 0) {
                return 'var(--' . esc_attr($val) . '-font-family), sans-serif';
            }
            return esc_attr($val) . ', sans-serif';
        };
        ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Anton&family=Barlow+Condensed:ital,wght@0,300;0,400;0,600;0,700;1,300&family=JetBrains+Mono:wght@300;400&display=swap" rel="stylesheet">

        <style>
            .sc-outlet-widget,
            .sc-outlet-widget *,
            .sc-outlet-widget *::before,
            .sc-outlet-widget *::after {
                box-sizing: border-box;
            }

            .sc-outlet-widget {
                /* Color variables - Support Elementor Global variables with plugin settings as fallbacks */
                --gold: <?php echo $c('color_primary'); ?>;
                --black: <?php echo $c('color_bg'); ?>;
                --dark: <?php echo $c('color_dark'); ?>;
                --surface: <?php echo $c('color_surface'); ?>;
                --surface2: <?php echo $c('color_surface2'); ?>;
                --border: <?php echo $c('color_border'); ?>;
                --border2: <?php echo $c('color_border2'); ?>;
                --white: <?php echo $c('color_text'); ?>;
                --muted: <?php echo $c('color_muted'); ?>;
                --muted2: <?php echo $c('color_muted2'); ?>;
                
                --gold-dim: color-mix(in srgb, var(--gold) 8%, transparent);
                --gold-glow: color-mix(in srgb, var(--gold) 25%, transparent);
                --red: #E03030;
                --sw: 380px;

                /* Typography variables - Support Elementor Global Typography with plugin fonts as fallbacks */
                --font-primary: <?php echo $f('font_heading'); ?>;
                --font-body: <?php echo $f('font_body'); ?>;

                width: 100%;
                height: 100vh;
                min-height: 720px;
                background: var(--black);
                color: var(--white);
                font-family: var(--font-body);
                overflow: hidden;
                position: relative;
                cursor: none;
            }

            .sc-outlet-widget #cur {
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

            .sc-outlet-widget #cur-dot {
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

            .sc-outlet-widget:hover #cur,
            .sc-outlet-widget:hover #cur-dot {
                opacity: 1;
            }

            .sc-outlet-widget #cur.expand {
                width: 28px;
                height: 28px;
                border-color: color-mix(in srgb, var(--gold) 40%, transparent);
            }

            .sc-outlet-widget #cur.click {
                background: var(--gold-dim);
                width: 8px;
                height: 8px;
            }

            .sc-outlet-widget #app {
                display: flex;
                width: 100%;
                height: 100%;
                position: relative;
            }

            .sc-outlet-widget #sb {
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

            .sc-outlet-widget #sb::after {
                content: '';
                position: absolute;
                inset: 0;
                background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,.012) 2px, rgba(255,255,255,.012) 4px);
                pointer-events: none;
                z-index: 0;
            }

            .sc-outlet-widget #hdr {
                padding: 26px 28px 20px;
                border-bottom: 1px solid var(--border);
                position: relative;
                z-index: 1;
                flex-shrink: 0;
            }

            .sc-outlet-widget .logo-row {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 16px;
            }

            .sc-outlet-widget .logo-svg {
                flex-shrink: 0;
                color: var(--gold);
            }

            .sc-outlet-widget .brand {
                font-family: var(--font-primary);
                font-size: 26px;
                letter-spacing: 3px;
                line-height: 1;
                color: var(--white);
            }

            .sc-outlet-widget .brand em {
                color: var(--gold);
                font-style: normal;
            }

            .sc-outlet-widget .sub {
                font-size: 9px;
                letter-spacing: 5px;
                text-transform: uppercase;
                color: var(--muted);
                margin-top: 3px;
            }

            .sc-outlet-widget .eyebrow {
                font-size: 9px;
                letter-spacing: 5px;
                text-transform: uppercase;
                color: var(--muted);
                margin-bottom: 5px;
            }

            .sc-outlet-widget .headline {
                font-family: var(--font-primary);
                font-size: 20px;
                letter-spacing: 2px;
                color: var(--white);
                line-height: 1;
            }

            .sc-outlet-widget #srch-wrap {
                padding: 14px 28px;
                border-bottom: 1px solid var(--border);
                position: relative;
                z-index: 1;
                flex-shrink: 0;
            }

            .sc-outlet-widget #srch-ico {
                position: absolute;
                left: 42px;
                top: 50%;
                transform: translateY(-50%);
                color: var(--muted);
                font-size: 15px;
                pointer-events: none;
            }

            .sc-outlet-widget #srch {
                width: 100%;
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: 3px;
                padding: 9px 14px 9px 36px;
                color: var(--white);
                font-family: var(--font-body);
                font-size: 14px;
                letter-spacing: .5px;
                outline: none;
                transition: border-color .2s, background .2s;
                cursor: none;
            }

            .sc-outlet-widget #srch:focus {
                border-color: color-mix(in srgb, var(--gold) 35%, transparent);
                background: var(--surface2);
            }

            .sc-outlet-widget #srch::placeholder {
                color: var(--muted);
            }

            .sc-outlet-widget #list {
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
                overscroll-behavior: contain;
                position: relative;
                z-index: 1;
                padding: 6px 0;
            }

            .sc-outlet-widget #list::-webkit-scrollbar {
                width: 2px;
            }

            .sc-outlet-widget #list::-webkit-scrollbar-track {
                background: transparent;
            }

            .sc-outlet-widget #list::-webkit-scrollbar-thumb {
                background: var(--border2);
                border-radius: 1px;
            }

            .sc-outlet-widget .oi {
                display: flex;
                align-items: center;
                padding: 13px 28px;
                cursor: none;
                position: relative;
                transition: background .2s;
                gap: 14px;
                overflow: hidden;
                border-bottom: 1px solid var(--border);
                animation: scSi .45s cubic-bezier(.16,1,.3,1) both;
            }

            .sc-outlet-widget .oi::before {
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

            .sc-outlet-widget .oi:hover,
            .sc-outlet-widget .oi.act {
                background: var(--gold-dim);
            }

            .sc-outlet-widget .oi:hover::before,
            .sc-outlet-widget .oi.act::before {
                transform: scaleY(1);
                transform-origin: top;
            }

            .sc-outlet-widget .oi-num {
                font-family: var(--font-primary);
                font-size: 11px;
                color: var(--gold);
                min-width: 24px;
                letter-spacing: 1px;
                opacity: .5;
                transition: opacity .2s;
            }

            .sc-outlet-widget .oi:hover .oi-num,
            .sc-outlet-widget .oi.act .oi-num {
                opacity: 1;
            }

            .sc-outlet-widget .oi-info {
                flex: 1;
                min-width: 0;
            }

            .sc-outlet-widget .oi-name {
                font-size: 15px;
                font-weight: 700;
                letter-spacing: 1.5px;
                text-transform: uppercase;
                color: var(--white);
                line-height: 1.05;
            }

            .sc-outlet-widget .oi-area {
                font-size: 10px;
                letter-spacing: 2px;
                color: var(--muted2);
                text-transform: uppercase;
                margin-top: 3px;
            }

            .sc-outlet-widget .oi-arr {
                color: var(--muted);
                font-size: 12px;
                transition: transform .2s, color .2s;
            }

            .sc-outlet-widget .oi:hover .oi-arr,
            .sc-outlet-widget .oi.act .oi-arr {
                transform: translateX(4px);
                color: var(--gold);
            }

            .sc-outlet-widget .no-res {
                padding: 48px 28px;
                text-align: center;
                color: var(--muted);
                font-size: 11px;
                letter-spacing: 3px;
                text-transform: uppercase;
            }

            @keyframes scSi {
                from { opacity: 0; transform: translateX(-16px); }
                to   { opacity: 1; transform: translateX(0); }
            }

            .sc-outlet-widget #ftr {
                padding: 14px 28px;
                border-top: 1px solid var(--border);
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: relative;
                z-index: 1;
                flex-shrink: 0;
            }

            .sc-outlet-widget .ftr-label {
                font-size: 9px;
                letter-spacing: 4px;
                text-transform: uppercase;
                color: var(--muted);
            }

            .sc-outlet-widget .ftr-num {
                font-family: var(--font-primary);
                font-size: 22px;
                color: var(--gold);
                line-height: 1;
            }

            .sc-outlet-widget #det {
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

            .sc-outlet-widget #det::after {
                content: '';
                position: absolute;
                inset: 0;
                background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,.012) 2px, rgba(255,255,255,.012) 4px);
                pointer-events: none;
                z-index: 0;
            }

            .sc-outlet-widget #det.open {
                transform: translateX(0);
            }

            .sc-outlet-widget #back {
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

            .sc-outlet-widget #back:hover {
                color: var(--gold);
            }

            .sc-outlet-widget #back svg {
                transition: transform .2s;
            }

            .sc-outlet-widget #back:hover svg {
                transform: translateX(-3px);
            }

            .sc-outlet-widget .d-bignum {
                font-family: var(--font-primary);
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

            .sc-outlet-widget .d-title {
                font-family: var(--font-primary);
                font-size: 34px;
                line-height: 1.05;
                letter-spacing: 2px;
                text-transform: uppercase;
                color: var(--white);
                position: relative;
                z-index: 1;
                margin-bottom: 6px;
            }

            .sc-outlet-widget .d-tag {
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

            /* Aspect ratio set to 4:3 for Featured Images in detail panel */
            .sc-outlet-widget .d-img-wrap {
                width: 100%;
                aspect-ratio: 4 / 3;
                background-color: var(--surface2);
                border-radius: 4px;
                overflow: hidden;
                margin-bottom: 22px;
                position: relative;
                z-index: 1;
                border: 1px solid var(--border);
            }
            
            .sc-outlet-widget .d-img-wrap img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            
            .sc-outlet-widget .d-div {
                height: 1px;
                background: var(--border);
                margin-bottom: 22px;
                position: relative;
                z-index: 1;
            }

            .sc-outlet-widget .d-row {
                display: flex;
                gap: 14px;
                margin-bottom: 18px;
                position: relative;
                z-index: 1;
                align-items: flex-start;
            }

            .sc-outlet-widget .d-ico {
                color: var(--gold);
                font-size: 13px;
                margin-top: 1px;
                min-width: 18px;
                text-align: center;
            }

            .sc-outlet-widget .d-lbl {
                font-size: 9px;
                letter-spacing: 3px;
                text-transform: uppercase;
                color: var(--muted);
                margin-bottom: 3px;
            }

            .sc-outlet-widget .d-val {
                font-size: 12px;
                font-family: 'JetBrains Mono', monospace;
                color: var(--white);
                line-height: 1.6;
                letter-spacing: .3px;
                white-space: pre-line;
            }

            .sc-outlet-widget .d-acts {
                margin-top: auto;
                position: relative;
                z-index: 1;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .sc-outlet-widget .btn-p {
                width: 100%;
                padding: 15px;
                background: var(--gold);
                color: var(--black);
                border: none;
                font-family: var(--font-primary);
                font-size: 15px;
                letter-spacing: 3px;
                text-transform: uppercase;
                cursor: none;
                transition: background .15s, transform .1s;
                border-radius: 2px;
            }

            .sc-outlet-widget .btn-p:hover {
                background: color-mix(in srgb, var(--gold) 85%, white);
                transform: translateY(-1px);
            }

            .sc-outlet-widget .btn-p:active {
                transform: translateY(0);
            }

            .sc-outlet-widget .btn-s {
                width: 100%;
                padding: 13px;
                background: transparent;
                color: var(--muted2);
                border: 1px solid var(--border2);
                font-family: var(--font-body);
                font-size: 12px;
                font-weight: 600;
                letter-spacing: 3px;
                text-transform: uppercase;
                cursor: none;
                transition: border-color .2s, color .2s;
                border-radius: 2px;
            }

            .sc-outlet-widget .btn-s:hover {
                border-color: color-mix(in srgb, var(--gold) 40%, transparent);
                color: var(--gold);
            }

            .sc-outlet-widget #map-wrap {
                flex: 1;
                position: relative;
            }

            .sc-outlet-widget #map {
                width: 100%;
                height: 100%;
            }

            .sc-outlet-widget .leaflet-container {
                background: #0a0a0a !important;
            }

            .sc-outlet-widget .leaflet-control-attribution {
                background: rgba(0,0,0,.4) !important;
                color: #333 !important;
                font-size: 8px !important;
                padding: 2px 6px !important;
            }

            .sc-outlet-widget .leaflet-control-zoom {
                border: 1px solid rgba(255,255,255,.08) !important;
                border-radius: 3px !important;
                overflow: hidden;
            }

            .sc-outlet-widget .leaflet-control-zoom a {
                background: var(--dark) !important;
                color: var(--muted) !important;
                border-bottom-color: rgba(255,255,255,.05) !important;
                width: 34px !important;
                height: 34px !important;
                line-height: 34px !important;
                font-size: 16px !important;
            }

            .sc-outlet-widget .leaflet-control-zoom a:hover {
                background: var(--surface) !important;
                color: var(--gold) !important;
            }

            .sc-outlet-widget #map-wrap::before {
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

            .sc-outlet-widget .sc-m {
                position: relative;
                width: 44px;
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .sc-outlet-widget .sc-ring {
                position: absolute;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                border: 1.5px solid color-mix(in srgb, var(--mc, var(--gold)) 20%, transparent);
                animation: scRp 2.4s ease-out infinite;
            }

            .sc-outlet-widget .sc-core {
                width: 26px;
                height: 26px;
                background: var(--dark);
                border: 1.5px solid color-mix(in srgb, var(--mc, var(--gold)) 55%, transparent);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: var(--font-primary);
                font-size: 9px;
                color: var(--mc, var(--gold));
                letter-spacing: .5px;
                position: relative;
                z-index: 1;
                transition: all .35s cubic-bezier(.16,1,.3,1);
            }

            .sc-outlet-widget .sc-m.act .sc-core {
                background: var(--mc, var(--gold));
                color: var(--black);
                width: 34px;
                height: 34px;
                font-size: 11px;
                box-shadow: 0 0 0 4px color-mix(in srgb, var(--mc, var(--gold)) 15%, transparent),
                            0 0 20px color-mix(in srgb, var(--mc, var(--gold)) 30%, transparent);
            }

            .sc-outlet-widget .sc-m.act .sc-ring {
                animation: scRpa 1s ease-out infinite;
                border-color: color-mix(in srgb, var(--mc, var(--gold)) 50%, transparent);
            }

            .sc-outlet-widget .sc-pin-svg {
                position: absolute;
                bottom: 50%;
                left: 50%;
                transform: translate(-50%, 0);
                color: var(--mc, var(--gold));
                width: 26px;
                height: 35px;
                display: flex;
                justify-content: center;
                align-items: flex-end;
                transition: all .35s cubic-bezier(.16,1,.3,1);
                filter: drop-shadow(0 4px 6px rgba(0,0,0,0.4));
            }

            .sc-outlet-widget .sc-pin-svg svg {
                width: 100%;
                height: 100%;
            }

            .sc-outlet-widget .sc-pin.act .sc-pin-svg {
                transform: translate(-50%, -6px) scale(1.15);
                filter: drop-shadow(0 8px 12px rgba(0,0,0,0.6));
            }

            .sc-outlet-widget .sc-pin .sc-ring {
                display: none;
            }

            .sc-outlet-widget .sc-tip {
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
            .sc-outlet-widget .sc-tip-txt {
                padding: 4px 10px;
            }
            .sc-outlet-widget .sc-tip-img {
                width: 100%;
                aspect-ratio: 3 / 2;
                background-size: cover;
                background-position: center;
                border-bottom: 1px solid rgba(255,255,255,0.1);
                display: none;
                transition: transform 0.3s cubic-bezier(.16,1,.3,1);
            }
            .sc-outlet-widget .sc-m:hover .sc-tip-img {
                transform: scale(1.08);
            }
            .sc-outlet-widget .sc-tip.has-img .sc-tip-img {
                display: block;
            }
            .sc-outlet-widget .oi-bg-img {
                position: absolute;
                inset: 0;
                background-size: cover;
                background-position: center;
                opacity: 0;
                transition: opacity 0.3s;
                z-index: -1;
            }
            .sc-outlet-widget .oi:hover .oi-bg-img,
            .sc-outlet-widget .oi.act .oi-bg-img {
                opacity: 0.15;
            }

            .sc-outlet-widget .sc-m:hover .sc-tip {
                opacity: 1;
                transform: translateY(0);
            }

            @keyframes scRp {
                0%   { transform: scale(.8); opacity: .4; }
                70%  { transform: scale(1.5); opacity: 0; }
                100% { transform: scale(1.5); opacity: 0; }
            }

            @keyframes scRpa {
                0%   { transform: scale(.85); opacity: .7; }
                70%  { transform: scale(2); opacity: 0; }
                100% { transform: scale(2); opacity: 0; }
            }

            .sc-outlet-widget #hud {
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

            .sc-outlet-widget .hud-big {
                font-family: var(--font-primary);
                font-size: 28px;
                color: var(--gold);
                line-height: 1;
            }

            .sc-outlet-widget .hud-sm {
                font-size: 9px;
                letter-spacing: 4px;
                text-transform: uppercase;
                color: var(--muted);
                margin-top: 2px;
            }

            .sc-outlet-widget .hud-div {
                height: 1px;
                background: var(--border);
                margin: 10px 0;
            }

            .sc-outlet-widget .hud-tag {
                font-size: 10px;
                letter-spacing: 3px;
                text-transform: uppercase;
                color: var(--muted2);
            }

            .sc-outlet-widget #topbar {
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

            .sc-outlet-widget .tb-badge {
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

            .sc-outlet-widget #ldr {
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

            .sc-outlet-widget #ldr.gone {
                opacity: 0;
                visibility: hidden;
            }

            .sc-outlet-widget .ldr-brand {
                font-family: var(--font-primary);
                font-size: 52px;
                letter-spacing: 6px;
                color: var(--white);
                margin-bottom: 4px;
            }

            .sc-outlet-widget .ldr-brand em {
                color: var(--gold);
                font-style: normal;
            }

            .sc-outlet-widget .ldr-sub {
                font-size: 9px;
                letter-spacing: 7px;
                text-transform: uppercase;
                color: var(--muted);
                margin-bottom: 52px;
            }

            .sc-outlet-widget .ldr-ring {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                border: 2px solid var(--surface);
                border-top-color: var(--gold);
                animation: scSpin .7s linear infinite;
            }

            @keyframes scSpin {
                to { transform: rotate(360deg); }
            }

            .sc-outlet-widget .leaflet-popup-content-wrapper {
                background: var(--dark) !important;
                border: 1px solid var(--border2) !important;
                border-radius: 3px !important;
                box-shadow: 0 12px 40px rgba(0,0,0,.7) !important;
                color: var(--white) !important;
            }

            .sc-outlet-widget .leaflet-popup-content {
                color: var(--white) !important;
                font-family: var(--font-body) !important;
                font-size: 12px !important;
                letter-spacing: 1px !important;
                margin: 8px 14px !important;
                text-transform: uppercase;
            }

            .sc-outlet-widget .leaflet-popup-tip-container .leaflet-popup-tip {
                background: var(--dark) !important;
            }

            .sc-outlet-widget .leaflet-popup-close-button {
                color: var(--muted) !important;
                font-size: 18px !important;
                top: 4px !important;
                right: 6px !important;
            }

            .sc-outlet-widget .leaflet-popup-close-button:hover {
                color: var(--gold) !important;
            }

            @media (max-width: 767px) {
                .sc-outlet-widget {
                    height: 850px;
                    min-height: 850px;
                }

                .sc-outlet-widget #app {
                    flex-direction: column;
                }

                .sc-outlet-widget #sb {
                    width: 100%;
                    height: 45%;
                    min-height: 380px;
                    border-right: 0;
                    border-bottom: 1px solid var(--border);
                }

                .sc-outlet-widget #map-wrap {
                    height: 55%;
                }

                .sc-outlet-widget #hdr {
                    padding: 22px 22px 16px;
                }

                .sc-outlet-widget #srch-wrap {
                    padding: 12px 22px;
                }

                .sc-outlet-widget .oi {
                    padding: 12px 22px;
                }

                .sc-outlet-widget #ftr {
                    padding: 12px 22px;
                }

                .sc-outlet-widget #hud { display: none; }
                .sc-outlet-widget #topbar { display: none; }
                .sc-outlet-widget #map-wrap::before { display: none; }

                .sc-outlet-widget #det {
                    padding: 24px;
                }

                .sc-outlet-widget .d-title {
                    font-size: 28px;
                }

                .sc-outlet-widget .d-bignum {
                    font-size: 80px;
                }
            }
        </style>
        <?php
    }

    private function get_outlets()
    {
        $posts = get_posts([
            'post_type'      => 'sc_outlet',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ]);

        $outlets = [];
        $i = 1;

        foreach ($posts as $post) {
            $lat = get_post_meta($post->ID, '_sc_outlet_lat', true);
            $lng = get_post_meta($post->ID, '_sc_outlet_lng', true);

            if (empty($lat) || empty($lng)) {
                continue;
            }

            $img = get_the_post_thumbnail_url($post->ID, 'large');
            if (!$img) {
                $img = '';
            }

            $marker_color = get_post_meta($post->ID, '_sc_outlet_marker_color', true);
            if (empty($marker_color)) {
                $marker_color = SC_OF_Settings::get('color_marker');
            }
            $outlets[] = [
                'id'    => $i,
                'name'  => $post->post_title,
                'area'  => get_post_meta($post->ID, '_sc_outlet_area', true),
                'addr'  => get_post_meta($post->ID, '_sc_outlet_address', true),
                'phone' => get_post_meta($post->ID, '_sc_outlet_phone', true),
                'hrs'   => get_post_meta($post->ID, '_sc_outlet_hours', true),
                'lat'   => (float) $lat,
                'lng'   => (float) $lng,
                'maps'  => get_post_meta($post->ID, '_sc_outlet_maps_url', true),
                'img'   => $img,
                'color' => !empty($marker_color) ? $marker_color : '',
            ];

            $i++;
        }

        return $outlets;
    }
}

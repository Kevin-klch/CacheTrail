import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const TILE_URL = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';
const ATTRIBUTION = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';
const TRAIL_COLOR = '#cc5a3d';

/**
 * Builds a DOM element with text content only, so cache names can never inject HTML.
 */
function element(tag, className, text) {
    const node = document.createElement(tag);

    if (className) {
        node.className = className;
    }

    if (text !== undefined) {
        node.textContent = text;
    }

    return node;
}

function markerIcon(cache, isStart, isEnd) {
    const classes = ['ct-marker'];

    if (cache.archived) classes.push('ct-marker--archived');
    if (cache.disabled) classes.push('ct-marker--disabled');
    if (isStart) classes.push('ct-marker--start');
    if (isEnd) classes.push('ct-marker--end');

    return L.divIcon({
        className: '',
        html: element('span', classes.join(' '), String(cache.number)).outerHTML,
        iconSize: [34, 34],
        iconAnchor: [17, 17],
        popupAnchor: [0, -18],
    });
}

const SVG_NS = 'http://www.w3.org/2000/svg';

/**
 * Small line icon (paths from the x-icon component), built without innerHTML.
 */
function icon(paths) {
    const svg = document.createElementNS(SVG_NS, 'svg');

    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('stroke', 'currentColor');
    svg.setAttribute('stroke-width', '1.75');
    svg.setAttribute('stroke-linecap', 'round');
    svg.setAttribute('stroke-linejoin', 'round');
    svg.setAttribute('aria-hidden', 'true');
    svg.setAttribute('class', 'ct-popup__icon');

    paths.forEach((d) => {
        const path = document.createElementNS(SVG_NS, 'path');
        path.setAttribute('d', d);
        svg.append(path);
    });

    return svg;
}

const ICONS = {
    external: ['M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5'],
    navigate: ['M3 11 21 3l-8 18-2-8Z'],
    previous: ['m15 18-6-6 6-6'],
    next: ['m9 18 6-6-6-6'],
};

/**
 * Difficulty or terrain (1–5 in half steps) as five pips, like the stars on Geocaching.com.
 */
function ratingPips(value) {
    const pips = element('span', 'ct-pips');

    for (let index = 1; index <= 5; index++) {
        const state = value >= index ? 'full' : value >= index - 0.5 ? 'half' : 'empty';
        pips.append(element('span', `ct-pip ct-pip--${state}`));
    }

    return pips;
}

function formatRating(value, labels) {
    return String(value).replace('.', labels.decimal ?? ',');
}

function popupRow(label, value) {
    const row = element('div', 'ct-popup__row');
    const valueNode = element('dd', 'ct-popup__value');

    if (value instanceof Node) {
        valueNode.append(value);
    } else {
        valueNode.textContent = value;
    }

    row.append(element('dt', 'ct-popup__label', label), valueNode);

    return row;
}

function popupAction(href, label, iconPaths) {
    const link = element('a', 'ct-popup__action');

    link.href = href;
    link.target = '_blank';
    link.rel = 'noopener noreferrer';
    link.append(icon(iconPaths), element('span', null, label));

    return link;
}

/**
 * Info card of one station: number, name and code, difficulty and terrain, status, links and
 * buttons to step through the stations in the recommended order.
 */
function popupContent(cache, labels, total, goTo) {
    const root = element('div', 'ct-popup ct-popup--cache');

    // Header: station number like the marker, name, code and type.
    const header = element('div', 'ct-popup__header');
    const badgeClasses = ['ct-popup__number'];
    if (cache.isStart) badgeClasses.push('ct-popup__number--start');
    if (cache.isEnd) badgeClasses.push('ct-popup__number--end');
    if (cache.disabled) badgeClasses.push('ct-popup__number--disabled');
    if (cache.archived) badgeClasses.push('ct-popup__number--archived');

    const heading = element('div', 'ct-popup__heading');
    heading.append(element('p', 'ct-popup__title', cache.name));

    const subline = element('p', 'ct-popup__subline');
    subline.append(element('span', 'ct-popup__code', cache.code), element('span', null, cache.type));
    if (cache.isStart) subline.append(element('span', 'ct-popup__tag', labels.start));
    if (cache.isEnd) subline.append(element('span', 'ct-popup__tag ct-popup__tag--end', labels.end));
    heading.append(subline);

    header.append(element('span', badgeClasses.join(' '), String(cache.number)), heading);
    root.append(header);

    // Key figures.
    const figures = element('dl', 'ct-popup__figures');
    if (typeof cache.difficulty === 'number') {
        const difficulty = element('span', 'ct-popup__rating');
        difficulty.append(ratingPips(cache.difficulty), element('span', null, formatRating(cache.difficulty, labels)));
        figures.append(popupRow(labels.difficulty, difficulty));
    }
    if (typeof cache.terrain === 'number') {
        const terrain = element('span', 'ct-popup__rating');
        terrain.append(ratingPips(cache.terrain), element('span', null, formatRating(cache.terrain, labels)));
        figures.append(popupRow(labels.terrain, terrain));
    }
    if (cache.status) figures.append(popupRow(labels.status, cache.status));
    root.append(figures);

    if (cache.hint) root.append(element('div', cache.disabled ? 'ct-popup__hint ct-popup__hint--disabled' : 'ct-popup__hint', cache.hint));

    // Actions.
    const actions = element('div', 'ct-popup__actions');
    if (cache.url) actions.append(popupAction(cache.url, labels.openCache, ICONS.external));
    actions.append(popupAction(
        `https://www.google.com/maps/dir/?api=1&destination=${cache.latitude},${cache.longitude}`,
        labels.navigate,
        ICONS.navigate,
    ));
    root.append(actions);

    // Step through the stations.
    if (total > 1) {
        const pager = element('div', 'ct-popup__pager');
        pager.append(element('span', null, labels.station.replace(':number', cache.number).replace(':total', total)));

        const buttons = element('span', 'ct-popup__pager-buttons');
        [[-1, labels.previous, ICONS.previous], [1, labels.next, ICONS.next]].forEach(([step, label, paths]) => {
            const button = element('button', 'ct-popup__pager-button');
            const target = cache.number - 1 + step;

            button.type = 'button';
            button.title = label;
            button.setAttribute('aria-label', label);
            button.disabled = target < 0 || target >= total;
            button.append(icon(paths));
            button.addEventListener('click', () => goTo(target));
            buttons.append(button);
        });

        pager.append(buttons);
        root.append(pager);
    }

    return root;
}

function parkingPopup(parking, labels) {
    const root = element('div', 'ct-popup ct-popup--cache');
    const header = element('div', 'ct-popup__header');
    const heading = element('div', 'ct-popup__heading');

    heading.append(element('p', 'ct-popup__title', labels.parking), element('p', 'ct-popup__subline', parking.label));
    header.append(element('span', 'ct-popup__number ct-popup__number--parking', 'P'), heading);

    const actions = element('div', 'ct-popup__actions');
    actions.append(popupAction(
        `https://www.google.com/maps/dir/?api=1&destination=${parking.latitude},${parking.longitude}`,
        labels.navigate,
        ICONS.navigate,
    ));

    root.append(header, actions);

    return root;
}

function directoryPopup(route, labels) {
    const root = element('div', 'ct-popup');
    const link = element('a', 'ct-popup__link', labels.view);

    link.href = route.url;
    root.append(element('p', 'ct-popup__title', route.name), element('p', 'ct-popup__meta', route.summary));

    if (route.distance) {
        root.append(element('p', 'ct-popup__flag', route.distance));
    }

    root.append(link);

    return root;
}

/**
 * Alpine component for the map view of the route directory: one marker per route start.
 */
export function registerDirectoryMap(Alpine) {
    Alpine.data('directoryMap', (routes = [], location = null, labels = {}) => ({
        init() {
            const map = L.map(this.$refs.map, { scrollWheelZoom: false });
            const layer = L.featureGroup().addTo(map);

            L.tileLayer(TILE_URL, { maxZoom: 19, attribution: ATTRIBUTION }).addTo(map);

            routes.forEach((route) => {
                L.marker([route.latitude, route.longitude], {
                    icon: L.divIcon({ className: '', html: element('span', 'ct-pin').outerHTML, iconSize: [28, 28], iconAnchor: [14, 28], popupAnchor: [0, -26] }),
                    title: route.name,
                    keyboard: true,
                })
                    .bindPopup(directoryPopup(route, labels))
                    .addTo(layer);
            });

            if (location) {
                L.circleMarker([location.latitude, location.longitude], { radius: 8, color: '#fff', weight: 3, fillColor: TRAIL_COLOR, fillOpacity: 1 })
                    .bindTooltip(labels.you)
                    .addTo(layer);
            }

            if (layer.getLayers().length > 0) {
                map.fitBounds(layer.getBounds(), { padding: [40, 40], maxZoom: 13 });
            } else {
                map.setView([51.16, 10.45], 5);
            }

            new ResizeObserver(() => map.invalidateSize()).observe(this.$refs.map);
        },
    }));
}

/**
 * Alpine component for the "use my location" button: asks the browser for the position and submits it.
 */
export function registerShareLocation(Alpine) {
    Alpine.data('shareLocation', (messages = {}) => ({
        busy: false,
        error: null,

        locate() {
            if (!('geolocation' in navigator)) {
                this.error = messages.unavailable;

                return;
            }

            this.busy = true;
            this.error = null;

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.$root.querySelector('[name="latitude"]').value = position.coords.latitude;
                    this.$root.querySelector('[name="longitude"]').value = position.coords.longitude;
                    this.$root.submit();
                },
                (failure) => {
                    this.busy = false;
                    this.error = failure.code === failure.PERMISSION_DENIED ? messages.denied : messages.unavailable;
                },
                { enableHighAccuracy: false, timeout: 15000, maximumAge: 600000 },
            );
        },
    }));
}

/**
 * Alpine component: <div x-data="trailMap(caches, labels)"><div x-ref="map"></div></div>
 * Listens for "trail-caches-updated" window events to redraw without a page reload.
 */
export function registerTrailMap(Alpine) {
    Alpine.data('trailMap', (initialCaches = [], labels = {}, parking = null) => ({
        map: null,
        layer: null,

        init() {
            this.map = L.map(this.$refs.map, { scrollWheelZoom: false, zoomControl: true });

            L.tileLayer(TILE_URL, { maxZoom: 19, attribution: ATTRIBUTION }).addTo(this.map);

            this.layer = L.featureGroup().addTo(this.map);
            this.render(initialCaches);

            // Maps inside elements that change size (e.g. the editor layout) need to re-measure.
            new ResizeObserver(() => this.map.invalidateSize()).observe(this.$refs.map);
        },

        update(event) {
            this.render(event.detail.caches ?? []);
        },

        render(caches) {
            this.layer.clearLayers();

            if (parking) {
                L.marker([parking.latitude, parking.longitude], {
                    icon: L.divIcon({
                        className: '',
                        html: element('span', 'ct-marker ct-marker--parking', 'P').outerHTML,
                        iconSize: [30, 30],
                        iconAnchor: [15, 15],
                        popupAnchor: [0, -16],
                    }),
                    title: labels.parking,
                    keyboard: true,
                })
                    .bindPopup(parkingPopup(parking, labels), { className: 'ct-leaflet-popup', minWidth: 220 })
                    .addTo(this.layer);
            }

            if (caches.length === 0) {
                if (parking) {
                    this.map.setView([parking.latitude, parking.longitude], 15);
                } else {
                    this.map.setView([51.16, 10.45], 5);
                }

                return;
            }

            const points = caches.map((cache) => [cache.latitude, cache.longitude]);

            if (points.length > 1) {
                L.polyline(points, { color: TRAIL_COLOR, weight: 4, opacity: 0.85, dashArray: '8 10', lineCap: 'round' }).addTo(this.layer);
            }

            const markers = [];
            const goTo = (index) => {
                const marker = markers[index];

                if (marker) {
                    this.map.panTo(marker.getLatLng());
                    marker.openPopup();
                }
            };

            caches.forEach((cache, index) => {
                const isStart = index === 0;
                const isEnd = index === caches.length - 1 && caches.length > 1;

                const marker = L.marker([cache.latitude, cache.longitude], {
                    icon: markerIcon(cache, isStart, isEnd),
                    title: `${cache.number}. ${cache.name}`,
                    keyboard: true,
                })
                    .bindPopup(popupContent({ ...cache, isStart, isEnd }, labels, caches.length, goTo), {
                        minWidth: 280,
                        maxWidth: 320,
                        className: 'ct-leaflet-popup',
                    })
                    .addTo(this.layer);

                markers.push(marker);
            });

            this.map.fitBounds(this.layer.getBounds(), { padding: [36, 36], maxZoom: 17 });
        },
    }));
}

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

function popupContent(cache, labels) {
    const root = element('div', 'ct-popup');
    const title = element('p', 'ct-popup__title', `${cache.number}. ${cache.name}`);
    const meta = element('p', 'ct-popup__meta', `${cache.code} · ${cache.type} · ${cache.rating}`);

    root.append(title, meta);

    if (cache.isStart) root.append(element('p', 'ct-popup__flag', labels.start));
    if (cache.isEnd) root.append(element('p', 'ct-popup__flag', labels.end));
    if (cache.hint) root.append(element('p', 'ct-popup__hint', cache.hint));

    return root;
}

/**
 * Alpine component: <div x-data="trailMap(caches, labels)"><div x-ref="map"></div></div>
 * Listens for "trail-caches-updated" window events to redraw without a page reload.
 */
export function registerTrailMap(Alpine) {
    Alpine.data('trailMap', (initialCaches = [], labels = {}) => ({
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

            if (caches.length === 0) {
                this.map.setView([51.16, 10.45], 5);

                return;
            }

            const points = caches.map((cache) => [cache.latitude, cache.longitude]);

            if (points.length > 1) {
                L.polyline(points, { color: TRAIL_COLOR, weight: 4, opacity: 0.85, dashArray: '8 10', lineCap: 'round' }).addTo(this.layer);
            }

            caches.forEach((cache, index) => {
                const isStart = index === 0;
                const isEnd = index === caches.length - 1 && caches.length > 1;

                L.marker([cache.latitude, cache.longitude], {
                    icon: markerIcon(cache, isStart, isEnd),
                    title: `${cache.number}. ${cache.name}`,
                    keyboard: true,
                })
                    .bindPopup(popupContent({ ...cache, isStart, isEnd }, labels))
                    .addTo(this.layer);
            });

            this.map.fitBounds(this.layer.getBounds(), { padding: [36, 36], maxZoom: 17 });
        },
    }));
}

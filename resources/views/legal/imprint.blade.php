{{-- Legal text in German only; operator details come from config('cachetrail.legal'). --}}
<x-layouts.app :title="__('Imprint')">
    <x-page-header :title="__('Imprint')" :eyebrow="__('Legal')" />

    <div class="mx-auto -mt-20 max-w-3xl px-4 sm:px-6 lg:px-8">
        <x-card class="legal-text">
            <h2>Angaben gemäß § 5 DDG</h2>
            <address>
                <x-legal-value key="name" label="Name" /><br>
                <x-legal-value key="street" label="Straße und Hausnummer" /><br>
                <x-legal-value key="city" label="PLZ und Ort" />
            </address>

            <h2>Kontakt</h2>
            <p>
                E-Mail: <x-legal-value key="email" label="E-Mail-Adresse" />
                @if (filled(config('cachetrail.legal.phone')))
                    <br>Telefon: {{ config('cachetrail.legal.phone') }}
                @endif
            </p>

            <h2>Verantwortlich für den Inhalt nach § 18 Abs. 2 MStV</h2>
            <address>
                <x-legal-value key="name" label="Name" /><br>
                <x-legal-value key="street" label="Straße und Hausnummer" /><br>
                <x-legal-value key="city" label="PLZ und Ort" />
            </address>

            <h2>Inhalte der Community</h2>
            <p>
                Routen, Beschreibungen, Bilder, Bewertungen und Profiltexte auf CacheTrail werden von den Nutzerinnen und Nutzern erstellt.
                Wir prüfen diese Inhalte nicht vor der Veröffentlichung. Sobald wir von rechtswidrigen Inhalten erfahren, entfernen wir sie umgehend.
                Bitte nutze dafür die Funktion „Melden“ an der jeweiligen Route oder Bewertung oder schreib uns eine E-Mail.
            </p>

            <h2>Haftung für Links</h2>
            <p>
                CacheTrail enthält Links zu Websites anderer Anbieter, auf deren Inhalte wir keinen Einfluss haben.
                Für diese Inhalte ist der jeweilige Anbieter verantwortlich. Werden uns Rechtsverletzungen bekannt, entfernen wir solche Links umgehend.
            </p>

            <h2>Marken und Kartendaten</h2>
            <p>
                Geocaching und Geocaching.com sind Marken von Groundspeak, Inc. (Geocaching HQ). CacheTrail ist ein unabhängiges Community-Projekt
                und steht in keiner Verbindung zu Geocaching HQ.
            </p>
            <p>
                Kartendaten © <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap-Mitwirkende</a>,
                verfügbar unter der Open Database License (ODbL).
            </p>
        </x-card>
    </div>
</x-layouts.app>

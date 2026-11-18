{{-- Legal text in German only; operator details come from config('cachetrail.legal'). Keep it in sync with
     what the application actually does (cookies, location, OpenStreetMap, Nominatim, uploads, emails). --}}
<x-layouts.app :title="__('Privacy policy')">
    <x-page-header :title="__('Privacy policy')" :eyebrow="__('Legal')">
        {{ __('Which data CacheTrail processes, what for, and which rights you have.') }}
    </x-page-header>

    <div class="mx-auto -mt-20 max-w-3xl px-4 sm:px-6 lg:px-8">
        <x-card class="legal-text" lang="de">
            @unless (app()->getLocale() === 'de')
                <p class="mb-6 rounded-2xl bg-sand-100 px-4 py-3 text-sm text-ink-muted" lang="en">{{ __('This page is only available in German.') }}</p>
            @endunless
            <h2>1. Verantwortlicher</h2>
            <address>
                <x-legal-value key="name" label="Name" /><br>
                <x-legal-value key="street" label="Straße und Hausnummer" /><br>
                <x-legal-value key="city" label="PLZ und Ort" /><br>
                E-Mail: <x-legal-value key="email" label="E-Mail-Adresse" />
            </address>
            <p>Bei Fragen zum Datenschutz erreichst du uns unter dieser E-Mail-Adresse.</p>

            <h2>2. Das Wichtigste in Kürze</h2>
            <ul>
                <li>Wir verwenden keine Werbe- oder Analyse-Tracker und setzen nur technisch notwendige Cookies.</li>
                <li>Dein Standort wird nur auf deinen Klick hin abgefragt, auf etwa einen Kilometer gerundet und nur für die Dauer deiner Sitzung gespeichert.</li>
                <li>Die Karten werden von OpenStreetMap geladen. Dabei erhält die OpenStreetMap Foundation deine IP-Adresse.</li>
                <li>Was du veröffentlichst (Routen, Bilder, Bewertungen, Profiltext), sehen andere angemeldete Nutzerinnen und Nutzer mit deinem Namen.</li>
                <li>Du kannst dein Konto jederzeit selbst löschen. Dabei werden auch deine Routen und Bilder gelöscht.</li>
            </ul>

            <h2>3. Hosting und Server-Logdateien</h2>
            <p>
                CacheTrail wird bei folgendem Anbieter betrieben: <x-legal-value key="hosting" label="Hoster mit Anschrift" />.
                Beim Aufruf der Website verarbeitet der Server technisch notwendige Daten wie IP-Adresse, Datum und Uhrzeit, aufgerufene Adresse,
                Browser und Betriebssystem. Das ist nötig, um die Website auszuliefern und ihre Sicherheit zu gewährleisten
                (Art. 6 Abs. 1 lit. f DSGVO). Mit dem Hoster besteht ein Vertrag zur Auftragsverarbeitung nach Art. 28 DSGVO.
            </p>

            <h2>4. Cookies und Sitzung</h2>
            <p>CacheTrail setzt ausschließlich Cookies, die für den Betrieb technisch notwendig sind (§ 25 Abs. 2 Nr. 2 TDDDG):</p>
            <ul>
                <li><strong>Sitzungs-Cookie</strong>: hält dich angemeldet und speichert vorübergehende Einstellungen wie deinen gerundeten Standort. Zur Sitzung speichern wir auch IP-Adresse und Browserkennung, um Missbrauch zu erkennen. Die Sitzung endet nach {{ config('session.lifetime') }} Minuten ohne Aktivität.</li>
                <li><strong>XSRF-Token</strong>: schützt Formulare vor Angriffen von fremden Websites.</li>
                <li><strong>„Angemeldet bleiben“</strong>: nur wenn du diese Option beim Anmelden wählst, merkt sich ein Cookie deine Anmeldung länger.</li>
            </ul>
            <p>Analyse-, Werbe- oder Tracking-Cookies verwenden wir nicht. Die Schriftarten werden von unserem eigenen Server geladen.</p>

            <h2>5. Benutzerkonto</h2>
            <p>
                Für die Registrierung speichern wir deinen Namen, deine E-Mail-Adresse und dein Passwort (nur als verschlüsselter Hash).
                Deine E-Mail-Adresse bestätigst du über einen Link, den wir dir schicken. Die Daten brauchen wir, um dir das Konto
                bereitzustellen (Art. 6 Abs. 1 lit. b DSGVO). Deine E-Mail-Adresse wird anderen Nutzerinnen und Nutzern nie angezeigt.
            </p>

            <h2>6. Was andere von dir sehen</h2>
            <p>
                CacheTrail ist eine Community-Plattform. Angemeldete Nutzerinnen und Nutzer sehen dein öffentliches Profil mit deinem Namen, deinem Profil- und Titelbild (falls hochgeladen),
                deinem Infotext, dem Datum deiner Registrierung, der Anzahl deiner absolvierten und veröffentlichten Routen,
                den zuletzt absolvierten Routen, deinen Abzeichen und der Zahl der „Gefällt mir“ für deine Routen. Außerdem sehen sie deine veröffentlichten
                Routen mit Beschreibung, Titelbild und Caches sowie deine Bewertungen. Eine einzelne Beispielroute ist auch ohne Anmeldung
                sichtbar, mit dem Namen der Person, die sie erstellt hat.
            </p>
            <p>
                Lade bitte nur Bilder hoch, an denen du die Rechte hast, und achte darauf, dass keine anderen Personen oder Kennzeichen
                erkennbar sind. Deine Merkliste ist privat. Bei „Gefällt mir“ wird nur die Anzahl angezeigt, nicht wer geklickt hat.
            </p>

            <h2>7. Meldungen</h2>
            <p>
                Wenn du eine Route oder Bewertung meldest, speichern wir den Grund, deine optionale Beschreibung und dein Konto, damit
                unser Team die Meldung prüfen und Missbrauch der Meldefunktion verhindern kann (Art. 6 Abs. 1 lit. f DSGVO).
                Wer etwas gemeldet hat, sieht nur unser Team.
            </p>

            <h2>8. Standort</h2>
            <p>
                Wenn du auf „Meinen Standort verwenden“ klickst, fragt dein Browser um Erlaubnis und übermittelt uns deinen Standort.
                Wir runden ihn sofort auf etwa einen Kilometer und speichern ihn nur in deiner Sitzung, nicht in deinem Konto.
                Er dient dazu, Entfernungen anzuzeigen und Routen in deiner Nähe zu finden (Art. 6 Abs. 1 lit. a DSGVO).
                Du kannst ihn jederzeit über das ✕ wieder entfernen.
            </p>
            <p>
                Um den Ortsnamen anzuzeigen („Dein Standort: Moers“), schickt unser Server den gerundeten Standort an den Dienst Nominatim
                (siehe Abschnitt 9). Deine IP-Adresse wird dabei nicht übermittelt.
            </p>

            <h2>9. Ortssuche mit Nominatim</h2>
            <p>
                Für die Umkreissuche („Moers + 10 km“) und für Ortsnamen nutzen wir Nominatim, einen Dienst der OpenStreetMap Foundation,
                St John’s Innovation Centre, Cowley Road, Cambridge, CB4 0WS, Vereinigtes Königreich. Die Anfrage stellt unser Server; übermittelt
                werden nur der eingegebene Ort bzw. der gerundete Standort, nicht deine IP-Adresse. Ergebnisse speichern wir bis zu 30 Tage
                zwischen (Art. 6 Abs. 1 lit. f DSGVO). Für das Vereinigte Königreich besteht ein Angemessenheitsbeschluss der EU-Kommission.
            </p>

            <h2>10. Karten von OpenStreetMap</h2>
            <p>
                Für die Karten laden wir Kartenkacheln von den Servern der OpenStreetMap Foundation (Anschrift siehe Abschnitt 9).
                Dabei lädt dein Browser die Kacheln direkt dort und übermittelt deine IP-Adresse sowie technische Angaben deines Browsers.
                Wir nutzen OpenStreetMap, um Routen anschaulich und ohne kommerzielle Kartendienste darzustellen (Art. 6 Abs. 1 lit. f DSGVO).
                Mehr dazu in der <a href="https://osmfoundation.org/wiki/Privacy_Policy" target="_blank" rel="noopener noreferrer">Datenschutzerklärung der OpenStreetMap Foundation</a>.
            </p>

            <h2>11. Navigation mit Google Maps</h2>
            <p>
                Im Fenster einer Station gibt es den Link „Navigation starten“. Erst wenn du ihn anklickst, öffnet sich Google Maps
                mit den Koordinaten des Caches als Ziel. Ab dann gilt die Datenschutzerklärung von Google. Vorher werden keine Daten an Google übertragen.
            </p>

            <h2>12. Geocaching.com</h2>
            <p>
                CacheTrail ist derzeit nicht mit Geocaching.com verbunden; die angezeigten Caches stammen aus einem Testdatensatz.
                Sobald eine Verbindung möglich ist, ergänzen wir diese Erklärung, bevor du dein Konto verbinden kannst.
            </p>

            <h2>13. E-Mails</h2>
            <p>
                Wir schicken dir nur E-Mails, die für dein Konto nötig sind, etwa zur Bestätigung deiner E-Mail-Adresse oder zum Zurücksetzen
                deines Passworts (Art. 6 Abs. 1 lit. b DSGVO). Newsletter verschicken wir nicht.
                Für den Versand nutzen wir: <x-legal-value key="mail_provider" label="E-Mail-Dienstleister mit Anschrift" />.
            </p>

            <h2>14. Speicherdauer und Löschung</h2>
            <p>
                Deine Kontodaten speichern wir, solange dein Konto besteht. Du kannst es in den Kontoeinstellungen jederzeit selbst löschen.
                Dabei löschen wir dein Profil, deine Routen mit Bildern, Bewertungen, Merkliste, „Gefällt mir“, absolvierte Routen und Meldungen.
                Sitzungsdaten werden nach Ablauf der Sitzung gelöscht, Server-Logdateien nach den Fristen des Hosters.
            </p>

            <h2>15. Deine Rechte</h2>
            <p>Du hast das Recht auf</p>
            <ul>
                <li>Auskunft über deine gespeicherten Daten (Art. 15 DSGVO),</li>
                <li>Berichtigung (Art. 16 DSGVO) und Löschung (Art. 17 DSGVO),</li>
                <li>Einschränkung der Verarbeitung (Art. 18 DSGVO) und Datenübertragbarkeit (Art. 20 DSGVO),</li>
                <li>Widerspruch gegen Verarbeitungen auf Grundlage berechtigter Interessen (Art. 21 DSGVO),</li>
                <li>Widerruf einer Einwilligung mit Wirkung für die Zukunft (Art. 7 Abs. 3 DSGVO), zum Beispiel für deinen Standort.</li>
            </ul>
            <p>
                Schreib uns dafür einfach eine E-Mail. Außerdem kannst du dich bei einer Datenschutz-Aufsichtsbehörde beschweren,
                zum Beispiel bei der Behörde deines Bundeslandes (Art. 77 DSGVO).
            </p>

            <p class="text-sm text-ink-muted">Stand: {{ \Illuminate\Support\Carbon::parse(config('cachetrail.legal.privacy_updated_at'))->translatedFormat('j. F Y') }}</p>
        </x-card>
    </div>
</x-layouts.app>

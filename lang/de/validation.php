<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | German default error messages used by the validator class.
    |
    */

    'accepted' => ':Attribute muss akzeptiert werden.',
    'accepted_if' => ':Attribute muss akzeptiert werden, wenn :other :value ist.',
    'active_url' => ':Attribute muss eine gültige URL sein.',
    'after' => ':Attribute muss ein Datum nach dem :date sein.',
    'after_or_equal' => ':Attribute muss ein Datum am oder nach dem :date sein.',
    'alpha' => ':Attribute darf nur Buchstaben enthalten.',
    'alpha_dash' => ':Attribute darf nur Buchstaben, Zahlen, Binde- und Unterstriche enthalten.',
    'alpha_num' => ':Attribute darf nur Buchstaben und Zahlen enthalten.',
    'any_of' => ':Attribute ist ungültig.',
    'array' => ':Attribute muss eine Liste sein.',
    'array_keys' => ':Attribute darf nur folgende Schlüssel enthalten: :values.',
    'ascii' => ':Attribute darf nur einfache Buchstaben, Zahlen und Zeichen enthalten.',
    'base64' => ':Attribute muss ein gültiger Base64-Text sein.',
    'before' => ':Attribute muss ein Datum vor dem :date sein.',
    'before_or_equal' => ':Attribute muss ein Datum am oder vor dem :date sein.',
    'between' => [
        'array' => ':Attribute muss zwischen :min und :max Einträge haben.',
        'file' => ':Attribute muss zwischen :min und :max Kilobyte groß sein.',
        'numeric' => ':Attribute muss zwischen :min und :max liegen.',
        'string' => ':Attribute muss zwischen :min und :max Zeichen lang sein.',
    ],
    'boolean' => ':Attribute muss wahr oder falsch sein.',
    'can' => ':Attribute enthält einen unzulässigen Wert.',
    'confirmed' => 'Die Bestätigung von :attribute stimmt nicht überein.',
    'contains' => ':Attribute fehlt ein erforderlicher Wert.',
    'current_password' => 'Das Passwort ist falsch.',
    'date' => ':Attribute muss ein gültiges Datum sein.',
    'date_equals' => ':Attribute muss das Datum :date sein.',
    'date_format' => ':Attribute muss dem Format :format entsprechen.',
    'decimal' => ':Attribute muss :decimal Nachkommastellen haben.',
    'declined' => ':Attribute muss abgelehnt werden.',
    'declined_if' => ':Attribute muss abgelehnt werden, wenn :other :value ist.',
    'different' => ':Attribute und :other müssen sich unterscheiden.',
    'digits' => ':Attribute muss :digits Ziffern haben.',
    'digits_between' => ':Attribute muss zwischen :min und :max Ziffern haben.',
    'dimensions' => ':Attribute hat ungültige Bildabmessungen.',
    'distinct' => ':Attribute enthält einen doppelten Wert.',
    'doesnt_contain' => ':Attribute darf nichts davon enthalten: :values.',
    'doesnt_end_with' => ':Attribute darf nicht mit einem dieser Werte enden: :values.',
    'doesnt_start_with' => ':Attribute darf nicht mit einem dieser Werte beginnen: :values.',
    'email' => ':Attribute muss eine gültige E-Mail-Adresse sein.',
    'encoding' => ':Attribute muss in :encoding kodiert sein.',
    'ends_with' => ':Attribute muss mit einem dieser Werte enden: :values.',
    'enum' => 'Der gewählte Wert für :attribute ist ungültig.',
    'exists' => 'Der gewählte Wert für :attribute ist ungültig.',
    'extensions' => ':Attribute muss eine dieser Dateiendungen haben: :values.',
    'file' => ':Attribute muss eine Datei sein.',
    'filled' => ':Attribute muss ausgefüllt sein.',
    'gt' => [
        'array' => ':Attribute muss mehr als :value Einträge haben.',
        'file' => ':Attribute muss größer als :value Kilobyte sein.',
        'numeric' => ':Attribute muss größer als :value sein.',
        'string' => ':Attribute muss länger als :value Zeichen sein.',
    ],
    'gte' => [
        'array' => ':Attribute muss mindestens :value Einträge haben.',
        'file' => ':Attribute muss mindestens :value Kilobyte groß sein.',
        'numeric' => ':Attribute muss mindestens :value sein.',
        'string' => ':Attribute muss mindestens :value Zeichen lang sein.',
    ],
    'hex_color' => ':Attribute muss eine gültige Hex-Farbe sein.',
    'image' => ':Attribute muss ein Bild sein.',
    'in' => 'Der gewählte Wert für :attribute ist ungültig.',
    'in_array' => ':Attribute muss in :other vorkommen.',
    'in_array_keys' => ':Attribute muss mindestens einen dieser Schlüssel enthalten: :values.',
    'integer' => ':Attribute muss eine ganze Zahl sein.',
    'ip' => ':Attribute muss eine gültige IP-Adresse sein.',
    'ipv4' => ':Attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6' => ':Attribute muss eine gültige IPv6-Adresse sein.',
    'json' => ':Attribute muss gültiges JSON sein.',
    'list' => ':Attribute muss eine Liste sein.',
    'lowercase' => ':Attribute muss kleingeschrieben sein.',
    'lt' => [
        'array' => ':Attribute muss weniger als :value Einträge haben.',
        'file' => ':Attribute muss kleiner als :value Kilobyte sein.',
        'numeric' => ':Attribute muss kleiner als :value sein.',
        'string' => ':Attribute muss kürzer als :value Zeichen sein.',
    ],
    'lte' => [
        'array' => ':Attribute darf höchstens :value Einträge haben.',
        'file' => ':Attribute darf höchstens :value Kilobyte groß sein.',
        'numeric' => ':Attribute darf höchstens :value sein.',
        'string' => ':Attribute darf höchstens :value Zeichen lang sein.',
    ],
    'mac_address' => ':Attribute muss eine gültige MAC-Adresse sein.',
    'max' => [
        'array' => ':Attribute darf höchstens :max Einträge haben.',
        'file' => ':Attribute darf höchstens :max Kilobyte groß sein.',
        'numeric' => ':Attribute darf höchstens :max sein.',
        'string' => ':Attribute darf höchstens :max Zeichen lang sein.',
    ],
    'max_digits' => ':Attribute darf höchstens :max Ziffern haben.',
    'mimes' => ':Attribute muss eine Datei dieses Typs sein: :values.',
    'mimetypes' => ':Attribute muss eine Datei dieses Typs sein: :values.',
    'min' => [
        'array' => ':Attribute muss mindestens :min Einträge haben.',
        'file' => ':Attribute muss mindestens :min Kilobyte groß sein.',
        'numeric' => ':Attribute muss mindestens :min sein.',
        'string' => ':Attribute muss mindestens :min Zeichen lang sein.',
    ],
    'min_digits' => ':Attribute muss mindestens :min Ziffern haben.',
    'missing' => ':Attribute darf nicht vorhanden sein.',
    'missing_if' => ':Attribute darf nicht vorhanden sein, wenn :other :value ist.',
    'missing_unless' => ':Attribute darf nicht vorhanden sein, außer :other ist :value.',
    'missing_with' => ':Attribute darf nicht vorhanden sein, wenn :values angegeben ist.',
    'missing_with_all' => ':Attribute darf nicht vorhanden sein, wenn :values angegeben sind.',
    'multiple_of' => ':Attribute muss ein Vielfaches von :value sein.',
    'not_in' => 'Der gewählte Wert für :attribute ist ungültig.',
    'not_regex' => 'Das Format von :attribute ist ungültig.',
    'numeric' => ':Attribute muss eine Zahl sein.',
    'password' => [
        'letters' => ':Attribute muss mindestens einen Buchstaben enthalten.',
        'mixed' => ':Attribute muss mindestens einen Groß- und einen Kleinbuchstaben enthalten.',
        'numbers' => ':Attribute muss mindestens eine Zahl enthalten.',
        'symbols' => ':Attribute muss mindestens ein Sonderzeichen enthalten.',
        'uncompromised' => 'Dieses :attribute ist in einem Datenleck aufgetaucht. Bitte wähle ein anderes.',
    ],
    'present' => ':Attribute muss vorhanden sein.',
    'present_if' => ':Attribute muss vorhanden sein, wenn :other :value ist.',
    'present_unless' => ':Attribute muss vorhanden sein, außer :other ist :value.',
    'present_with' => ':Attribute muss vorhanden sein, wenn :values angegeben ist.',
    'present_with_all' => ':Attribute muss vorhanden sein, wenn :values angegeben sind.',
    'prohibited' => ':Attribute ist nicht erlaubt.',
    'prohibited_if' => ':Attribute ist nicht erlaubt, wenn :other :value ist.',
    'prohibited_if_accepted' => ':Attribute ist nicht erlaubt, wenn :other akzeptiert ist.',
    'prohibited_if_declined' => ':Attribute ist nicht erlaubt, wenn :other abgelehnt ist.',
    'prohibited_unless' => ':Attribute ist nur erlaubt, wenn :other einen dieser Werte hat: :values.',
    'prohibits' => ':Attribute verbietet, dass :other angegeben wird.',
    'regex' => 'Das Format von :attribute ist ungültig.',
    'required' => ':Attribute ist ein Pflichtfeld.',
    'required_array_keys' => ':Attribute muss Einträge für :values enthalten.',
    'required_if' => ':Attribute ist ein Pflichtfeld, wenn :other :value ist.',
    'required_if_accepted' => ':Attribute ist ein Pflichtfeld, wenn :other akzeptiert ist.',
    'required_if_declined' => ':Attribute ist ein Pflichtfeld, wenn :other abgelehnt ist.',
    'required_unless' => ':Attribute ist ein Pflichtfeld, außer :other hat einen dieser Werte: :values.',
    'required_with' => ':Attribute ist ein Pflichtfeld, wenn :values angegeben ist.',
    'required_with_all' => ':Attribute ist ein Pflichtfeld, wenn :values angegeben sind.',
    'required_without' => ':Attribute ist ein Pflichtfeld, wenn :values fehlt.',
    'required_without_all' => ':Attribute ist ein Pflichtfeld, wenn keines von :values angegeben ist.',
    'same' => ':Attribute und :other müssen übereinstimmen.',
    'size' => [
        'array' => ':Attribute muss genau :size Einträge haben.',
        'file' => ':Attribute muss genau :size Kilobyte groß sein.',
        'numeric' => ':Attribute muss genau :size sein.',
        'string' => ':Attribute muss genau :size Zeichen lang sein.',
    ],
    'starts_with' => ':Attribute muss mit einem dieser Werte beginnen: :values.',
    'string' => ':Attribute muss ein Text sein.',
    'timezone' => ':Attribute muss eine gültige Zeitzone sein.',
    'unique' => ':Attribute wird bereits verwendet.',
    'uploaded' => ':Attribute konnte nicht hochgeladen werden.',
    'uppercase' => ':Attribute muss großgeschrieben sein.',
    'url' => ':Attribute muss eine gültige URL sein.',
    'ulid' => ':Attribute muss eine gültige ULID sein.',
    'uuid' => ':Attribute muss eine gültige UUID sein.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'Name',
        'email' => 'E-Mail-Adresse',
        'password' => 'Passwort',
        'password_confirmation' => 'Passwortbestätigung',
        'current_password' => 'Aktuelles Passwort',
    ],

];

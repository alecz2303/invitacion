<?php

return [
  // colores
  'accent' => ['type' => 'color', 'default' => '#b59b6a'],
  'accent2' => ['type' => 'color', 'default' => '#3e6b5b'],

  // textos
  'subtitle' => ['type' => 'string', 'default' => 'Prepárate para una celebración inolvidable...'],
  'whatsapp_url' => ['type' => 'string', 'default' => null],
  'hashtag' => ['type' => 'string', 'default' => '#MISQUINCE'],
  'quote_1' => ['type' => 'string', 'default' => 'Cada amanecer trae consigo una promesa...'],
  'quote_2' => ['type' => 'string', 'default' => 'Y hoy es el inicio de una hermosa historia por escribir...'],

  // bloques
  'parents' => ['type' => 'textarea', 'default' => "Texto Texto\nTexto Texto"],
  'godparents' => ['type' => 'textarea', 'default' => "Texto Texto\nTexto Texto"],

  // urls assets
  'asset_base_url' => ['type' => 'string', 'default' => null],
 // 'hero_image_url' => ['type' => 'url', 'default' => null],

  // ceremonia / recepción
  'ceremony_title' => ['type' => 'string', 'default' => 'Ceremonia Religiosa'],
  'ceremony_place' => ['type' => 'string', 'default' => 'Parroquia'],
  'ceremony_address' => ['type' => 'string', 'default' => null],
  'ceremony_time' => ['type' => 'string', 'default' => null],
  'ceremony_maps_url' => ['type' => 'string', 'default' => null],
  'ceremony_photo_url' => [
    'type' => 'image',           // ✅ antes string/url
    'default' => null,
    'label' => 'Foto ceremonia',
    'group' => 'Ceremonia / Recepción',
    'help'  => 'Sube una foto (JPG/PNG/WebP).'
  ],

  'reception_title' => ['type' => 'string', 'default' => 'Recepción'],
  'reception_place' => ['type' => 'string', 'default' => 'Salón'],
  'reception_address' => ['type' => 'string', 'default' => null],
  'reception_time' => ['type' => 'string', 'default' => null],
  'reception_maps_url' => ['type' => 'string', 'default' => null],
  'reception_photo_url' => [
    'type' => 'image',           // ✅ antes string/url
    'default' => null,
    'label' => 'Foto recepción',
    'group' => 'Ceremonia / Recepción',
    'help'  => 'Sube una foto (JPG/PNG/WebP).'
  ],

  // vestimenta
  'dress_title' => ['type' => 'string', 'default' => null],
  'dress_note' => ['type' => 'string', 'default' => 'Trae tu antifaz'],
  'reserved_color_note' => ['type' => 'string', 'default' => 'COLOR VERDE RESERVADO PARA LA QUINCEAÑERA'],
  'dress_image_url' => [
    'type' => 'image',           // ✅ antes string/url
    'default' => null,
    'label' => 'Foto vestimenta',
    'group' => 'Vestimenta',
    'help'  => 'Sube una foto (JPG/PNG/WebP).'
  ],

  // pase
  'pass_bg_url' => [
    'type' => 'image',           // ✅ antes string/url
    'default' => null,
    'label' => 'Foto pase',
    'group' => 'Pase',
    'help'  => 'Sube una foto (JPG/PNG/WebP).'
  ],
  'pass_quote' => ['type' => 'string', 'default' => '"El futuro pertenece a quienes creen en la belleza de sus sueños"'],

  // listas (arrays)
  'itinerary' => ['type' => 'json', 'default' => []],
  'gifts' => ['type' => 'json', 'default' => []],

  // iconos opcionales
  'ico_ceremony_url' => ['type' => 'string', 'default' => null],
  'ico_cocktail_url' => ['type' => 'string', 'default' => null],
  'ico_social_url' => ['type' => 'string', 'default' => null],
  'ico_dinner_url' => ['type' => 'string', 'default' => null],
  'ico_social2_url' => ['type' => 'string', 'default' => null],
  'ico_cake_url' => ['type' => 'string', 'default' => null],
  'ico_party_url' => ['type' => 'string', 'default' => null],
];

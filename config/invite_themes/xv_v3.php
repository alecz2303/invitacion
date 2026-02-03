<?php

return [
  // =========================
  // 🎨 Colores
  // =========================
  'accent' => [
    'type' => 'color',
    'default' => '#b59b6a',
    'label' => 'Color principal',
    'tab'   => 'Colores',
    'group' => 'Paleta',
    'order' => 10,
    'help'  => 'Color para detalles y acentos.'
  ],
  'accent2' => [
    'type' => 'color',
    'default' => '#3e6b5b',
    'label' => 'Color secundario',
    'tab'   => 'Colores',
    'group' => 'Paleta',
    'order' => 20,
    'help'  => 'Color secundario de apoyo.'
  ],

  // =========================
  // 🖼️ Imágenes
  // =========================
  'imgNombre' => [
    'type' => 'image',
    'default' => null,
    'label' => 'Imagen del nombre (PNG)',
    'tab'   => 'Básico',
    'group' => 'Imágenes',
    'order' => 30,
    'help'  => 'Ideal con fondo transparente.'
  ],

  // =========================
  // ✍️ Textos
  // =========================
  'subtitle' => [
    'type' => 'string',
    'default' => 'Prepárate para una celebración inolvidable...',
    'label' => 'Subtítulo',
    'tab'   => 'Básico',
    'group' => 'Textos',
    'order' => 10,
  ],
  'whatsapp_url' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Link de WhatsApp',
    'tab'   => 'Básico',
    'group' => 'Textos',
    'order' => 20,
    'help'  => 'Ejemplo: https://wa.me/521XXXXXXXXXX?text=...'
  ],
  'hashtag' => [
    'type' => 'string',
    'default' => '#MISQUINCE',
    'label' => 'Hashtag',
    'tab'   => 'Básico',
    'group' => 'Textos',
    'order' => 30,
  ],
  'quote_1' => [
    'type' => 'string',
    'default' => 'Cada amanecer trae consigo una promesa...',
    'label' => 'Frase 1',
    'tab'   => 'Básico',
    'group' => 'Frases',
    'order' => 40,
  ],
  'quote_2' => [
    'type' => 'string',
    'default' => 'Y hoy es el inicio de una hermosa historia por escribir...',
    'label' => 'Frase 2',
    'tab'   => 'Básico',
    'group' => 'Frases',
    'order' => 50,
  ],

  // =========================
  // 👨‍👩‍👧‍👦 Familia
  // =========================
  'parents' => [
    'type' => 'textarea',
    'default' => "Texto Texto\nTexto Texto",
    'label' => 'Padres',
    'tab'   => 'Familia',
    'group' => 'Nombres',
    'order' => 10,
    'help'  => "Una línea por nombre."
  ],
  'godparents' => [
    'type' => 'textarea',
    'default' => "Texto Texto\nTexto Texto",
    'label' => 'Padrinos',
    'tab'   => 'Familia',
    'group' => 'Nombres',
    'order' => 20,
    'help'  => "Una línea por nombre."
  ],

  // =========================
  // 🔗 URLs / Assets
  // =========================
  'asset_base_url' => [
    'type' => 'string',
    'default' => null,
    'label' => 'URL base de assets',
    'tab'   => 'Avanzado',
    'group' => 'Assets',
    'order' => 10,
    'help'  => 'Opcional si manejas CDN o carpeta pública para imágenes.'
  ],

  // =========================
  // ⛪ Ceremonia
  // =========================
  'ceremony_title' => [
    'type' => 'string',
    'default' => 'Ceremonia Religiosa',
    'label' => 'Título de ceremonia',
    'tab'   => 'Ceremonia',
    'group' => 'Ceremonia',
    'order' => 10,
  ],
  'ceremony_place' => [
    'type' => 'string',
    'default' => 'Parroquia',
    'label' => 'Lugar de ceremonia',
    'tab'   => 'Ceremonia',
    'group' => 'Ceremonia',
    'order' => 20,
  ],
  'ceremony_address' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Dirección de ceremonia',
    'tab'   => 'Ceremonia',
    'group' => 'Ceremonia',
    'order' => 30,
  ],
  'ceremony_time' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Hora de ceremonia',
    'tab'   => 'Ceremonia',
    'group' => 'Ceremonia',
    'order' => 40,
    'help'  => 'Ejemplo: 6:00 PM'
  ],
  'ceremony_maps_url' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Link de Google Maps (ceremonia)',
    'tab'   => 'Ceremonia',
    'group' => 'Ceremonia',
    'order' => 50,
  ],
  'ceremony_photo_url' => [
    'type' => 'image',
    'default' => null,
    'label' => 'Foto de la ceremonia',
    'tab'   => 'Ceremonia',
    'group' => 'Ceremonia',
    'order' => 60,
    'help'  => 'Sube una foto (JPG/PNG/WebP).'
  ],

  // =========================
  // 🥂 Recepción
  // =========================
  'reception_title' => [
    'type' => 'string',
    'default' => 'Recepción',
    'label' => 'Título de recepción',
    'tab'   => 'Recepción',
    'group' => 'Recepción',
    'order' => 10,
  ],
  'reception_place' => [
    'type' => 'string',
    'default' => 'Salón',
    'label' => 'Lugar de recepción',
    'tab'   => 'Recepción',
    'group' => 'Recepción',
    'order' => 20,
  ],
  'reception_address' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Dirección de recepción',
    'tab'   => 'Recepción',
    'group' => 'Recepción',
    'order' => 30,
  ],
  'reception_time' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Hora de recepción',
    'tab'   => 'Recepción',
    'group' => 'Recepción',
    'order' => 40,
    'help'  => 'Ejemplo: 8:00 PM'
  ],
  'reception_maps_url' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Link de Google Maps (recepción)',
    'tab'   => 'Recepción',
    'group' => 'Recepción',
    'order' => 50,
  ],
  'reception_photo_url' => [
    'type' => 'image',
    'default' => null,
    'label' => 'Foto de la recepción',
    'tab'   => 'Recepción',
    'group' => 'Recepción',
    'order' => 60,
    'help'  => 'Sube una foto (JPG/PNG/WebP).'
  ],

  // =========================
  // 👗 Vestimenta
  // =========================
  'dress_title' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Título de vestimenta',
    'tab'   => 'Vestimenta',
    'group' => 'Vestimenta',
    'order' => 10,
  ],
  'dress_note' => [
    'type' => 'string',
    'default' => 'Trae tu antifaz',
    'label' => 'Nota de vestimenta',
    'tab'   => 'Vestimenta',
    'group' => 'Vestimenta',
    'order' => 20,
  ],
  'reserved_color_note' => [
    'type' => 'string',
    'default' => 'COLOR VERDE RESERVADO PARA LA QUINCEAÑERA',
    'label' => 'Nota de color reservado',
    'tab'   => 'Vestimenta',
    'group' => 'Vestimenta',
    'order' => 30,
  ],
  'dress_image_url' => [
    'type' => 'image',
    'default' => null,
    'label' => 'Foto / referencia de vestimenta',
    'tab'   => 'Vestimenta',
    'group' => 'Vestimenta',
    'order' => 40,
    'help'  => 'Sube una imagen (JPG/PNG/WebP).'
  ],

  // =========================
  // 🎟️ Pase
  // =========================
  'pass_bg_url' => [
    'type' => 'image',
    'default' => null,
    'label' => 'Imagen de fondo del pase',
    'tab'   => 'Pase',
    'group' => 'Pase',
    'order' => 10,
  ],
  'pass_quote' => [
    'type' => 'string',
    'default' => '"El futuro pertenece a quienes creen en la belleza de sus sueños"',
    'label' => 'Frase del pase',
    'tab'   => 'Pase',
    'group' => 'Pase',
    'order' => 20,
  ],

  // =========================
  // 📋 Listas (se manejan fuera del schema loop)
  // =========================
  'itinerary' => [
    'type' => 'json',
    'default' => [],
    'label' => 'Itinerario',
    'tab'   => 'Avanzado',
    'group' => 'Listas',
    'order' => 10,
  ],
  'gifts' => [
    'type' => 'json',
    'default' => [],
    'label' => 'Mesa de regalos',
    'tab'   => 'Avanzado',
    'group' => 'Listas',
    'order' => 20,
  ],

  // =========================
  // 🧩 Íconos
  // =========================
  'ico_ceremony_url' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Ícono ceremonia (URL)',
    'tab'   => 'Íconos',
    'group' => 'Íconos',
    'order' => 10,
  ],
  'ico_cocktail_url' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Ícono cóctel (URL)',
    'tab'   => 'Íconos',
    'group' => 'Íconos',
    'order' => 20,
  ],
  'ico_social_url' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Ícono social (URL)',
    'tab'   => 'Íconos',
    'group' => 'Íconos',
    'order' => 30,
  ],
  'ico_dinner_url' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Ícono cena (URL)',
    'tab'   => 'Íconos',
    'group' => 'Íconos',
    'order' => 40,
  ],
  'ico_social2_url' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Ícono social 2 (URL)',
    'tab'   => 'Íconos',
    'group' => 'Íconos',
    'order' => 50,
  ],
  'ico_cake_url' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Ícono pastel (URL)',
    'tab'   => 'Íconos',
    'group' => 'Íconos',
    'order' => 60,
  ],
  'ico_party_url' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Ícono fiesta (URL)',
    'tab'   => 'Íconos',
    'group' => 'Íconos',
    'order' => 70,
  ],
];

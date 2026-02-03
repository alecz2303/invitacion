<?php

return [

  // =========================
  // 🌄 Parallax (fondos)
  // =========================
  'px_countdown_url' => [
    'type' => 'image',
    'default' => null,
    'label' => 'Parallax cuenta regresiva',
    'tab'   => 'Parallax',
    'group' => 'Fondos',
    'order' => 10,
    'help'  => 'Imagen de fondo para la sección de cuenta regresiva.'
  ],

  'px_detalles_url' => [
    'type' => 'image',
    'default' => null,
    'label' => 'Parallax detalles',
    'tab'   => 'Parallax',
    'group' => 'Fondos',
    'order' => 20,
    'help'  => 'Imagen de fondo para la sección de detalles del evento.'
  ],

  'px_ubicacion_url' => [
    'type' => 'image',
    'default' => null,
    'label' => 'Parallax ubicación',
    'tab'   => 'Parallax',
    'group' => 'Fondos',
    'order' => 30,
    'help'  => 'Imagen de fondo para la sección de ubicación.'
  ],

  // =========================
  // ✍️ Textos
  // =========================
  'badge_text' => [
    'type' => 'string',
    'default' => '🎉 ¡Estás invitado!',
    'label' => 'Texto del badge',
    'tab'   => 'Básico',
    'group' => 'Textos',
    'order' => 10,
  ],

  'subtitle' => [
    'type' => 'string',
    'default' => 'Una fiesta llena de alegría y diversión',
    'label' => 'Subtítulo',
    'tab'   => 'Básico',
    'group' => 'Textos',
    'order' => 20,
  ],

  'note_countdown' => [
    'type' => 'string',
    'default' => '✨ Ven con ganas de celebrar, abrazar y tomarte fotos bonitas.',
    'label' => 'Nota de cuenta regresiva',
    'tab'   => 'Básico',
    'group' => 'Textos',
    'order' => 30,
  ],

  'details_note' => [
    'type' => 'string',
    'default' => 'Si puedes, llega puntual para disfrutar todo desde el inicio 🥳',
    'label' => 'Nota de detalles',
    'tab'   => 'Básico',
    'group' => 'Textos',
    'order' => 40,
  ],

  'maps_hint' => [
    'type' => 'string',
    'default' => 'Toca el botón para abrir Google Maps',
    'label' => 'Texto ubicación',
    'tab'   => 'Ubicación',
    'group' => 'Textos',
    'order' => 10,
  ],

  // =========================
  // 🏷️ Chips del hero
  // =========================
  'chips' => [
    'type' => 'json',
    'default' => [
      '🎪 Fiesta infantil',
      '🎈 Globos & confeti',
      '🎂 Cumpleaños'
    ],
    'label' => 'Chips del encabezado',
    'tab'   => 'Básico',
    'group' => 'Chips',
    'order' => 50,
    'help'  => 'Lista de etiquetas mostradas en el encabezado.'
  ],


  // =========================
  // 💬 WhatsApp
  // =========================
  'whatsapp_number' => [
    'type' => 'string',
    'default' => null,
    'label' => 'Número de WhatsApp',
    'tab'   => 'RSVP',
    'group' => 'WhatsApp',
    'order' => 10,
    'help'  => 'Ejemplo: 521XXXXXXXXXX'
  ],

  'whatsapp_message_template' => [
    'type' => 'textarea',
    'default' =>
      "🎉 ¡Hola! Confirmo mi asistencia al evento el {DATE} a las {TIME} en {VENUE}. 💛\n\nAsistimos: {ADULTS} adulto(s) y {KIDS} niño(s).",
    'label' => 'Plantilla de mensaje',
    'tab'   => 'RSVP',
    'group' => 'WhatsApp',
    'order' => 20,
  ],

  // =========================
  // 🧾 Footer
  // =========================
  'footer_text' => [
    'type' => 'string',
    'default' => 'Hecho con 💛',
    'label' => 'Texto del footer',
    'tab'   => 'Avanzado',
    'group' => 'Footer',
    'order' => 10,
  ],

];

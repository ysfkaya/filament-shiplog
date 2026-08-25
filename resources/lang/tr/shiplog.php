<?php

return [

    'navigation' => [
        'label' => 'Değişiklik Günlüğü',
        'title' => 'Değişiklik Günlüğü',
    ],

    'timeline' => [
        'label' => 'Yenilikler',
        'heading' => 'Yenilikler',
        'subheading' => 'Yayınladığımız her şey, en yeniden başlayarak.',
        'empty' => 'Henüz bir yayın yok.',
        'error' => 'Değişiklik günlüğü yüklenemedi.',
    ],

    'actions' => [
        'manage' => 'Yayınları yönet',
        'flush' => 'Önbelleği temizle',
        'flushed' => 'Değişiklik günlüğü önbelleği temizlendi.',
    ],

    'resource' => [
        'label' => 'Yayın',
        'plural_label' => 'Yayınlar',
    ],

    'form' => [
        'details' => 'Yayın bilgileri',
        'version' => 'Sürüm',
        'title' => 'Başlık',
        'title_placeholder' => 'Koyu tema, daha hızlı arama',
        'released_at' => 'Yayın tarihi',
        'released_at_hint' => 'Yayınlanmamış olarak göstermek için boş bırakın. İleri bir tarih, o güne kadar gizli tutar.',
        'status' => 'Durum',
        'environments' => 'Ortamlar',
        'environments_hint' => 'Bu yayını her ortamda göstermek için boş bırakın.',
        'yanked' => 'Geri çekildi',
        'yanked_hint' => 'Yayınlandıktan sonra geri çekilen sürümleri işaretleyin.',
        'body' => 'Yayın notları',
        'body_hint' => 'Markdown, ayrıca > [!WARNING] uyarı kutuları ve ^[ipuçları](bunun gibi).',
    ],

    'table' => [
        'unreleased' => 'Yayınlanmadı',
        'all_environments' => 'Tümü',
    ],

    'status' => [
        'draft' => 'Taslak',
        'published' => 'Yayında',
    ],

    'change_type' => [
        'added' => 'Eklendi',
        'changed' => 'Değişti',
        'deprecated' => 'Kullanımdan kaldırıldı',
        'removed' => 'Kaldırıldı',
        'fixed' => 'Düzeltildi',
        'security' => 'Güvenlik',
    ],

    'fab_position' => [
        'top-left' => 'Sol üst',
        'top-right' => 'Sağ üst',
        'bottom-left' => 'Sol alt',
        'bottom-right' => 'Sağ alt',
    ],
];

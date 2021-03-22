<?php

require_once(__DIR__ . '/vendor/autoload.php');


event_bind('module.content.front.render', function ($data) {
    $json_arr = [];
    $currency = mw()->shop_manager->currency_get();

    foreach ($data as $item) {


        if ($item['content_type'] !== 'product') {
            continue;
        }

        $content_data = content_data($item['id']);
        $content_link = content_link($item['id']);

        $item['in_stock'] = false;
        if (isset($content_data['qty']) and $content_data['qty'] != 0) {
            $item['in_stock'] = true;
        }

        $schema_item = [];
        $schema_item['name'] = $item['title'];
        $schema_item['description'] = $item['description'];
        $schema_item['image'] = get_picture($item['id']);
        if (isset($content_data['sku'])) {
            $schema_item['sku'] = $content_data['sku'];
        } else {
            $schema_item['sku'] = content_link($item['id']);

        }
        $schema_item['offers'] = [
            'url' => $item['link'],
            'price' => $item['price'],
            'priceCurrency' => $currency,
            'availability' => ($item['in_stock'] ? "https://schema.org/InStock" : "https://schema.org/OutOfStock"),
            'sku' => $content_link
        ];

        $json_arr[] = $schema_item;
        $context = \JsonLd\Context::create('product', $schema_item);

        echo $context;
    }
});

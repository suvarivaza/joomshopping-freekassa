<?php

/**
 * Рендеринг шаблона
 */
function render_template($template, $render_data)
{
    $plugin_dir = dirname(__FILE__);
    $templates_dir = '/../templates/';
    $extension = '.php';

    $template_path = implode([$plugin_dir, $templates_dir, $template, $extension]);

    if (!file_exists($template_path)) {
        die('Файл ' . $template . $extension . ' шаблона не найден.');
    }

    if ($render_data !== null) {
        extract($render_data);
    }

    include($template_path);
}

/**
 * Создание сигнатуры
 */
function generate_signature($data, $separator)
{
    return md5(implode($separator, $data));
}


/**
 * Преобразование цены товара
 */
function format_amount($amount)
{
    return ((float) $amount == (int) $amount) ? (int) $amount : number_format((float) $amount, 1, '.', '');
}

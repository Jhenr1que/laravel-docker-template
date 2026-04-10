<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

add_action('init', static function (): void {
    $postType = defined('WORDPRESS_BLOG_POST_TYPE') ? (string) WORDPRESS_BLOG_POST_TYPE : 'blog_post';
    $taxonomy = defined('WORDPRESS_BLOG_TAXONOMY') ? (string) WORDPRESS_BLOG_TAXONOMY : 'blog_category';

    register_post_type($postType, [
        'labels' => [
            'name' => 'Blog',
            'singular_name' => 'Post do Blog',
            'menu_name' => 'Blog',
            'add_new' => 'Adicionar novo',
            'add_new_item' => 'Adicionar post do blog',
            'edit_item' => 'Editar post do blog',
            'new_item' => 'Novo post do blog',
            'view_item' => 'Ver post do blog',
            'search_items' => 'Buscar posts do blog',
            'not_found' => 'Nenhum post do blog encontrado',
            'not_found_in_trash' => 'Nenhum post do blog na lixeira',
            'all_items' => 'Todos os posts do blog',
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-welcome-write-blog',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
        'has_archive' => false,
        'rewrite' => ['slug' => 'blog'],
    ]);

    register_taxonomy($taxonomy, [$postType], [
        'labels' => [
            'name' => 'Categorias do blog',
            'singular_name' => 'Categoria do blog',
            'search_items' => 'Buscar categorias do blog',
            'all_items' => 'Todas as categorias do blog',
            'edit_item' => 'Editar categoria do blog',
            'update_item' => 'Atualizar categoria do blog',
            'add_new_item' => 'Adicionar categoria do blog',
            'new_item_name' => 'Nova categoria do blog',
            'menu_name' => 'Categorias do blog',
        ],
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'blog-categoria'],
    ]);
}, 5);

add_action('init', static function (): void {
    $version = '1';
    $option = 'blog_content_type_rewrite_version';

    if (get_option($option) === $version) {
        return;
    }

    flush_rewrite_rules(false);
    update_option($option, $version, false);
}, 20);

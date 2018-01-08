<?php
// Home
Breadcrumbs::register('index', function ($breadcrumbs) {
    $breadcrumbs->push('Главная', route('index'));
});

// Home > About
Breadcrumbs::register('credits', function ($breadcrumbs) {
    $breadcrumbs->parent('index');
    $breadcrumbs->push('Кредиты', route('credit_index'));
});

//// Home > Blog
//Breadcrumbs::register('blog', function ($breadcrumbs) {
//    $breadcrumbs->parent('home');
//    $breadcrumbs->push('Blog', route('blog'));
//});
//
//// Home > Blog > [Category]
//Breadcrumbs::register('category', function ($breadcrumbs, $category) {
//    $breadcrumbs->parent('blog');
//    $breadcrumbs->push($category->title, route('category', $category->id));
//});
//
//// Home > Blog > [Category] > [Post]
//Breadcrumbs::register('post', function ($breadcrumbs, $post) {
//    $breadcrumbs->parent('category', $post->category);
//    $breadcrumbs->push($post->title, route('post', $post));
//});
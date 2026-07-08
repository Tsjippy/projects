<?php

namespace TSJIPPY\PROJECTS;

use TSJIPPY;

add_action('tsjippy-pdf-before-print-content', __NAMESPACE__ . '\beforePrint', 10, 2);
function beforePrint($post, $pdf)
{
    if ($post->post_type != 'project') {
        return;
    }

    $pdf->printImage(get_the_post_thumbnail_url($post), -1, 20, -1, -1, true, true);

    //Project number
    $url = TSJIPPY\pathToUrl(PLUGINPATH . 'pictures/project.png');

    $pdf->printImage($url, 10, -1, 10, 10);
    $pdf->write(10, get_post_meta(get_the_ID(), 'tsjippy_number', true));

    //Manager name
    $url = TSJIPPY\pathToUrl(PLUGINPATH . 'pictures/manager.png');
    $pdf->printImage($url, 55, -1, 10, 10);
    $pdf->write(10, get_post_meta(get_the_ID(), 'tsjippy_manager_name', true));

    //Manager tel
    $url = TSJIPPY\pathToUrl(PLUGINPATH . 'pictures/tel.png');
    $pdf->printImage($url, 100, -1, 10, 10);
    $pdf->write(10, get_post_meta(get_the_ID(), 'tsjippy_manager_tel', true));

    //Manager e-mail
    $url    = TSJIPPY\pathToUrl(PLUGINPATH . 'pictures/email.png');
    $y      = $pdf->getY() + 12;
    $pdf->printImage($url, 10, $y, 10, 10);
    $pdf->write(10, get_post_meta(get_the_ID(), 'tsjippy_manager_email', true));

    //Url
    $imageUrl = TSJIPPY\pathToUrl(PLUGINPATH . 'pictures/url.png');
    $y      = $pdf->getY() + 12;
    $url    = get_post_meta(get_the_ID(), 'tsjippy_url', true);
    if (!empty($url)) {
        $pdf->printImage($imageUrl, 10, $y, 10, 10);
        $pdf->write(10, $url);
    }

    $pdf->Ln(20);
    $pdf->writeHTML('<b>Description:</b>');
}

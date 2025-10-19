<?php
declare(strict_types=1);
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

$iconList = [];
foreach (
[
    'gadgetogoogle-plugin-map' => 'gadgetogoogle-plugin-map.svg',
    'gadgetogoogle-plugin-list' => 'gadgetogoogle-plugin-list.svg',
    'gadgetogoogle-plugin-detail' => 'gadgetogoogle-plugin-detail.svg',
    'gadgetogoogle-plugin-teaser' => 'gadgetogoogle-plugin-teaser.svg',
] as $identifier => $path) {
    $iconList[$identifier] = [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:gadgeto_google/Resources/Public/Icons/' . $path,
    ];
}

return $iconList;

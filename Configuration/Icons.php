<?php
declare(strict_types=1);
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

$iconList = [];
foreach ([
    'gadgetogoogle-plugin-map' => 'gadgetogoogle-plugin-map.svg',
] as $identifier => $path) {
    $iconList[$identifier] = [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:gadgetogoogle/Resources/Public/Icons/' . $path,
    ];
}

return $iconList;

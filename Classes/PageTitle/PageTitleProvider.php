<?php
declare(strict_types=1);
namespace Madj2k\GadgetoGoogle\PageTitle;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Madj2k\GadgetoGoogle\Domain\Model\FilterableInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class PageTitleProvider
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
*  @package Madj2k_GadgetoGoogle
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageTitleProvider extends AbstractPageTitleProvider implements PageTitleProviderInterface
{

    /**
     * @param \Madj2k\GadgetoGoogle\Domain\Model\FilterableInterface $filterable
     * @return void
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function setTitle(FilterableInterface $filterable): void
    {
        $configReader = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $extensionConfig = $configReader->get('gadgeto_google');

        // get relevant fields
        $fields = GeneralUtility::trimExplode(',', ($extensionConfig['pageTitleFields'] ?? 'label'),true);
        $separator = $extensionConfig['pageTitleSeparator'] ?? '';
        $includePageName = (bool) $extensionConfig['pageTitleIncludePageName'] ?? false;
        $combineFields = (bool) $extensionConfig['pageTitleCombineFields'] ?? false;

        $title = [];
        foreach ($fields as $field) {

            $getter = 'get' . GeneralUtility::underscoredToUpperCamelCase($field);
            if (
                (method_exists($filterable, $getter))
                && ($value = $filterable->$getter())
                && (is_string($value))
            ){
                $title[] = trim(str_replace('­', '', strip_tags($value)));
                if (! $separator || ! $combineFields) {
                    break;
                }
            }
        }

        if ($separator && $includePageName) {
            /** @var \TYPO3\CMS\Core\Site\Entity\Site $config */
            $site = $this->getRequest()->getAttribute('site');
            $title[] = $site->getConfiguration()['websiteTitle'];
        }

        if ($title) {
           $this->title = implode(' ' . $separator . ' ', $title);
        }
    }


    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }

}

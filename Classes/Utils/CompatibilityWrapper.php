<?php
declare(strict_types=1);

namespace Tx\Tinyurls\Utils;

/*                                                                        *
 * This script belongs to the TYPO3 extension "tinyurls".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class CompatibilityWrapper implements SingletonInterface
{
    /**
     * @var string
     */
    protected $typo3Version;

    public function __construct()
    {
        if (defined('TYPO3_version')) {
            // @codeCoverageIgnoreStart
            $this->typo3Version = (string)TYPO3_version;
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Returns a path to the tinyurls Extension that can be used for the [ctrl][iconfile] setting
     * in the TCA: https://docs.typo3.org/typo3cms/TCAReference/Ctrl/Index.html#iconfile
     *
     * Newer versions support the EXT:tinyurls/ syntax. Older versions require a relativ path
     * generated by ExtensionManagementUtility::extRelPath().
     *
     * If the extRelPath() method still exists in the ExtensionManagementUtility class we use
     * it to generate the path to the tinyurls Extension.
     *
     * @return string
     */
    public function getExtensionPathPrefixForTcaIconfile(): string
    {
        if (VersionNumberUtility::convertVersionNumberToInteger($this->typo3Version) >= 7006000) {
            return 'EXT:tinyurls/';
        }

        /** @noinspection PhpDeprecationInspection We already use the newer method for newer TYPO3 versions. */
        return ExtensionManagementUtility::extRelPath('tinyurls');
    }

    /**
     * @param int $count
     * @return string
     * @codeCoverageIgnore
     */
    public function getRandomHexString(int $count): string
    {
        if (class_exists('TYPO3\\CMS\\Core\\Crypto\\Random')) {
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection We can not import it if it does not exist. */
            $random = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Crypto\Random::class);
            return $random->generateRandomHexString($count);
        } else {
            /** @noinspection PhpDeprecationInspection We already use the newer method if it exists. */
            return GeneralUtility::getRandomHexString($count);
        }
    }

    /**
     * @param string $typo3Version
     */
    public function setTypo3Version(string $typo3Version)
    {
        $this->typo3Version = $typo3Version;
    }
}

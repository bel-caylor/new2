<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocks\Composer\Installers;

class MakoInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'package' => 'app/packages/{$name}/',
    );
}

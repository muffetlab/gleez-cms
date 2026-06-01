<?php

/**
 * Tests plural form key selection in Gleez_I18n.
 *
 * @group Gleez
 * @group Gleez.core
 * @group Gleez.core.i18n
 * @package     Gleez
 * @category    Tests
 * @author      Loong <loong2460@gmail.com>
 * @copyright   (c) 2026 Muffet Lab
 * @license     https://gleez.muffetlab.com/license
 */
class Gleez_I18nTest extends Unittest_TestCase
{
    public function provider_plural_key(): array
    {
        return [
            // default (en, de, etc.)
            ['en', 1, 'one'],
            ['en', 0, 'other'],
            ['en', 2, 'other'],
            ['de', 5, 'other'],

            // languages with a single "other" form
            ['zh', 1, 'other'],
            ['zh', 5, 'other'],
            ['ja', 0, 'other'],

            // Arabic
            ['ar', 0, 'zero'],
            ['ar', 1, 'one'],
            ['ar', 2, 'two'],
            ['ar', 3, 'few'],
            ['ar', 10, 'few'],
            ['ar', 11, 'many'],
            ['ar', 99, 'many'],
            ['ar', 100, 'other'],

            // Portuguese / Hindi group
            ['pt', 0, 'one'],
            ['pt', 1, 'one'],
            ['pt', 2, 'other'],
            ['hi', 1, 'one'],
            ['hi', 2, 'other'],

            // French
            ['fr', 0, 'one'],
            ['fr', 1, 'one'],
            ['fr', 2, 'other'],

            // Latvian
            ['lv', 0, 'zero'],
            ['lv', 1, 'one'],
            ['lv', 11, 'other'],
            ['lv', 21, 'one'],

            // Irish / Sami group
            ['ga', 1, 'one'],
            ['ga', 2, 'two'],
            ['ga', 3, 'other'],

            // Lithuanian
            ['lt', 1, 'one'],
            ['lt', 21, 'one'],
            ['lt', 2, 'few'],
            ['lt', 9, 'few'],
            ['lt', 12, 'other'],
            ['lt', 19, 'other'],
            ['lt', 22, 'few'],

            // Russian / Slavic group
            ['ru', 1, 'one'],
            ['ru', 21, 'one'],
            ['ru', 2, 'few'],
            ['ru', 4, 'few'],
            ['ru', 22, 'few'],
            ['ru', 24, 'few'],
            ['ru', 5, 'many'],
            ['ru', 11, 'many'],
            ['ru', 12, 'many'],

            // Czech / Slovak
            ['cs', 1, 'one'],
            ['cs', 2, 'few'],
            ['cs', 4, 'few'],
            ['cs', 5, 'other'],

            // Slovenian
            ['sl', 1, 'one'],
            ['sl', 2, 'two'],
            ['sl', 3, 'few'],
            ['sl', 4, 'few'],
            ['sl', 5, 'other'],

            // Welsh
            ['cy', 1, 'one'],
            ['cy', 2, 'two'],
            ['cy', 8, 'many'],
            ['cy', 11, 'many'],
            ['cy', 3, 'other'],

            // Macedonian
            ['mk', 1, 'one'],
            ['mk', 11, 'one'],
            ['mk', 2, 'other'],

            // Polish
            ['pl', 1, 'one'],
            ['pl', 2, 'few'],
            ['pl', 3, 'few'],
            ['pl', 4, 'few'],
            ['pl', 5, 'other'],
            ['pl', 12, 'other'],
            ['pl', 22, 'other'],
            ['pl', 24, 'other'],
            ['pl', 102, 'few'],

            // Romanian / Moldovan
            ['ro', 1, 'one'],
            ['ro', 0, 'few'],
            ['ro', 2, 'few'],
            ['ro', 5, 'few'],
            ['ro', 19, 'few'],
            ['ro', 20, 'other'],
            ['ro', 21, 'other'],
            ['ro', 101, 'few'],
            ['mo', 0, 'few'],
            ['mo', 19, 'few'],
        ];
    }

    /**
     * @dataProvider provider_plural_key
     * @throws ReflectionException
     */
    public function test_get_plural_key($lang, $count, $expected)
    {
        $method = new ReflectionMethod('Gleez_I18n', 'get_plural_key');
        $method->setAccessible(true);

        $result = $method->invoke(null, $lang, $count);

        $this->assertSame($expected, $result);
    }
}

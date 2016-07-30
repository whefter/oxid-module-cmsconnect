<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * cmsconnect_setup_testcontent
 */
class cmsconnect_setup_testcontent extends cmsconnect_setup_main
{
    /**
     * Configures the variables used by this module.
     *
     * @var mixed[]
     */
    protected $_aModuleSettings = array(
        array(  'name'      => 'sCMScTestContent',
                'type'      => 'str',
                'global'    => false,
            ),
    );

    /**
     * Returns the default test content
     *
     * @return string
     */
    public function getDefaultTestContent ()
    {
        return CMSc_Utils::getDefaultTestContent();
    }
}
<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   William Hefter 2014
 */

/**
 * cmsxid_setup_main
 */
class cmsxid_setup_testcontent extends cmsxid_setup_main
{
    /**
     * Configures the variables used by this module.
     *
     * @var mixed[]
     */
    protected $_aModuleSettings = array(
        array(  'name'      => 'sCmsxidTestContent',
                'type'      => 'str',
                'global'    => false,
            ),
    );

    /**
     * Returns the default test content
     *
     * @return string
     */
    public function getCmsxidDefaultTestContent ()
    {
        return "defaultest";
    }
}
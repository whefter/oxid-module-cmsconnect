<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Controllers\Admin\Setup;

/**
 * cmsconnect_setup_testcontent
 */
class TestContent extends Main
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
     * Constructor. Sets the template variable to the current class name with .tpl suffix
     *
     * @return string
     */
    function __construct()
    {
        parent::__construct();

        $this->_sThisTemplate = 'modules/wh/cmsconnect/admin/setup_testcontent.tpl';
    }

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
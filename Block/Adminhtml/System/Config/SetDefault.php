<?php

namespace Pmclain\OneClickCheckout\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;

class SetDefault extends Field
{
    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('Pmclain_OneClickCheckout::system/config/set-default.phtml');
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element = clone $element;
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl('buynow/config/setdefault'),
            ]
        );

        return $this->_toHtml();
    }
}

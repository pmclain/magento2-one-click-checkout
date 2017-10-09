<?php

namespace Pmclain\OneClickCheckout\Api;

/**
 * @api
 */
interface ButtonInterface
{
    /**
     * @return bool
     */
    public function canShow();
}

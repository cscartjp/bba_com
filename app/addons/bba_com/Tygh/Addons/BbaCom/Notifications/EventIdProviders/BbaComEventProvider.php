<?php
/**
 * BbaComEventProvider
 *
 * @package Tygh\Addons\BbaCom\Notifications\EventIdProviders
 */

namespace Tygh\Addons\BbaCom\Notifications\EventIdProviders;

use Tygh\Notifications\EventIdProviders\IProvider;

class BbaComEventProvider implements IProvider
{
    /**
     * @var string
     */
    protected string $prefix = 'bba_com.';

    /**
     * @var string
     */
    protected string $id;

    /**
     * MkmReportViolationEventProvider constructor.
     *
     * @param int $email_id Product review identifier
     */
    public function __construct(int $email_id)
    {
        $this->id = $this->prefix . $email_id;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id;
    }
}

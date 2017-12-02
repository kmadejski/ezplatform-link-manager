<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueURL extends Constraint
{
    /**
     * %url% placeholder is passed.
     *
     * @var string
     */
    public $message = 'ez.url.unique';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'ezplatform.link_manager.validator.unique_url';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

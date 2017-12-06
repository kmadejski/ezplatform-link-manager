<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Validator\Constraints;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use EzSystems\EzPlatformLinkManager\API\Repository\URLService;
use EzSystems\EzPlatformLinkManagerBundle\Form\Data\URLUpdateData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueURLValidator extends ConstraintValidator
{
    /** @var URLService */
    private $urlService;

    /**
     * UniqueURLValidator constructor.
     *
     * @param URLService $urlService
     */
    public function __construct(URLService $urlService)
    {
        $this->urlService = $urlService;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof URLUpdateData || $value->url === null) {
            return;
        }

        try {
            $url = $this->urlService->loadByUrl($value->url);

            if ($url->id === $value->id) {
                return;
            }

            $this->context->buildViolation($constraint->message)
                ->atPath('url')
                ->setParameter('%url%', $value->url)
                ->addViolation();
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}

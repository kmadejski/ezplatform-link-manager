<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Form\Mapper;

use EzSystems\EzPlatformLinkManagerBundle\Form\Data\URLUpdateData;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\RepositoryForms\Data\Mapper\FormDataMapperInterface;

class URLMapper implements FormDataMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function mapToFormData(ValueObject $value, array $params = [])
    {
        $data = new URLUpdateData();
        $data->id = $value->id;
        $data->url = $value->url;

        return $data;
    }
}

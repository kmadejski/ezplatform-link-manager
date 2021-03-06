<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Form\Type\URL;

use EzSystems\EzPlatformLinkManagerBundle\Form\Data\URLListData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * URL list form.
 */
class URLListType extends AbstractType
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * URLListType constructor.
     *
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('status', ChoiceType::class, [
            'choices' => [
                0 => $this->translator->trans('url.status.invalid', [], 'linkmanager'),
                1 => $this->translator->trans('url.status.valid', [], 'linkmanager'),
            ],
            'placeholder' => $this->translator->trans('url.status.all', [], 'linkmanager'),
            'required' => false,
        ]);

        $builder->add('searchQuery', SearchType::class, [
            'required' => false,
            'constraints' => [
                new Assert\Length([
                    'min' => 3,
                    'max' => 255,
                ]),
            ],
        ]);

        $builder->add('limit', HiddenType::class);
        $builder->add('page', HiddenType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => URLListData::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ezplatformlinkmanager_url_list';
    }
}

<?php
namespace Arnm\MenuBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * Template form use to manage Templates as well as gets embedded into page form
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class ItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('text', 'text',
            array(
                'label' => 'menu.item.form.text.label',
                'attr' => array(
                    'rel' => 'tooltip',
                    'title' => 'menu.item.form.text.help',
                    'class' => 'span4'
                ),
                'translation_domain' => 'menu',
                'required' => false
            ));
        $builder->add('url', 'text',
            array(
                'label' => 'menu.item.form.url.label',
                'attr' => array(
                    'rel' => 'tooltip',
                    'title' => 'menu.item.form.url.help',
                    'class' => 'span4'
                ),
                'translation_domain' => 'menu',
                'required' => false
            ));
    }

    /**
     * {@inheritdoc}
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'menu_item';
    }

    /**
     * {@inheritdoc}
     * @see Symfony\Component\Form.AbstractType::getDefaultOptions()
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Arnm\MenuBundle\Entity\Item'
        );
    }
}

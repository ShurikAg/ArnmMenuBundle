<?php
namespace Arnm\MenuBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * Template form use to manage Templates as well as gets embedded into page form
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class MenuType extends AbstractType
{

    /**
     * {@inheritdoc}
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('code', 'text', array(
            'label' => 'menu.form.code.label',
            'attr' => array(
                'data-toggle' => 'popover',
                'content' => 'menu.form.code.help',
                'translation_domain' => 'menu',
                'class' => 'form-control'
            ),
            'translation_domain' => 'menu',
            'required' => false
        ));
        $builder->add('class', 'text', array(
            'label' => 'menu.form.class.label',
            'attr' => array(
                'data-toggle' => 'popover',
            	'content' => 'menu.form.class.help',
                'translation_domain' => 'menu',
                'class' => 'form-control'
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
        return 'menu';
    }

    /**
     * {@inheritdoc}
     * @see Symfony\Component\Form.AbstractType::getDefaultOptions()
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Arnm\MenuBundle\Entity\Menu'
        );
    }
}

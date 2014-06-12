<?php
namespace Arnm\MenuBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
/**
 * Form for menu widget management
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class MenuWidgetType extends AbstractType
{
    /**
     * {@inheritdoc}
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array(
            'label' => 'menu_widget.form.title.label',
            'attr' => array(
                'data-toggle' => 'popover',
                'content' => 'menu_widget.form.title.help',
                'class' => 'form-control',
                'ng-model' => 'wConfigForm.title',
                'translation_domain' => 'text_widget'
            ),
            'translation_domain' => 'menu',
            'required' => false
        ));
        $builder->add('menu', 'entity', array(
            'class' => 'Arnm\MenuBundle\Entity\Menu',
            'property' => 'code',
            'label' => 'menu_widget.form.menu.label',
            'attr' => array(
                'data-toggle' => 'popover',
                'content' => 'menu_widget.form.menu.help',
                'class' => 'form-control',
                'ng-model' => 'wConfigForm.menu',
                'translation_domain' => 'text_widget'
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
        return 'menu_widget';
    }

    /**
     * {@inheritdoc}
     * @see Symfony\Component\Form.AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arnm\MenuBundle\Model\MenuWidget',
            'csrf_protection' => false
        ));
    }
}

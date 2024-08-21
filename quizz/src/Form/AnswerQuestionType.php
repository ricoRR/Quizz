<?php
namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\AnswerResponseType;

class AnswerQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('responses', CollectionType::class, [
                'entry_type' => AnswerResponseType::class,
                'entry_options' => [
                    'responses' => $options['responses']
                ],
                'label' => $options['question']->getQuestion(),
                'by_reference' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'question' => null,
            'responses' => [],
        ]);

        $resolver->setAllowedTypes('question', ['null', Question::class]);
        $resolver->setAllowedTypes('responses', 'array');
    }
}

<?php

namespace App\Controller;

use App\Entity\PriceComputation;
use App\Entity\PriceRule;
use App\Form\Type\PriceComputationType;
use App\Service\CalculatePrices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'computation')]
class ComputationController extends AbstractController
{
    public function __construct(private readonly CalculatePrices $calculatePrices)
    {
    }

    public function __invoke(Request $request)
    {
        $computation = new PriceComputation();
        $form = $this->createForm(PriceComputationType::class, $computation);

        $result = null;

        $form->add('save', SubmitType::class, [
            'label' => 'calculer',
            'attr' => ['class' => 'btn btn-primary'],
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $computation
                ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0))
                ->addRule(new PriceRule(1, 7, 480, 1080, 0.4, 1))
                ->addRule(new PriceRule(6, 7, 0, 1440, 0.18, 99));

            $result = $this->calculatePrices->__invoke($computation);
        }

        return $this->render('computation/index.html.twig', [
            'result' => $result,
            'form' => $form->createView()
        ]);
    }
}

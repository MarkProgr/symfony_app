<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\CalculatingType;
use App\Form\ProductType;
use App\Repository\CountryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/calculate/{id}', name: 'app_product_calculate_form', methods: ['GET', 'POST'])]
    public function calculateTotalCostForm(Request $request, Product $product, CountryRepository $repository): Response
    {
        $form = $this->createForm(CalculatingType::class)
            ->get('cost')
            ->setData($product->getCost())
            ->getParent();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->get('calculating');

            $countryTaxValue = substr($data['tax'], 0, 2);

            $foundedCountry = $repository->findOneBy(['tax' => $countryTaxValue]);

            if ($foundedCountry) {
                $taxValue = $foundedCountry->getTaxValue();
                $totalCost = (($taxValue / 100) + 1) * $data['cost'];

                $this->addFlash(
                    'notice',
                    'Total cost = ' . $totalCost,
                );
            } else {
                $this->addFlash(
                    'notice',
                    'There are not such tax numbers'
                );
            }
        }

        return $this->renderForm('calculator.html.twig', compact('product', 'form'));
    }
}

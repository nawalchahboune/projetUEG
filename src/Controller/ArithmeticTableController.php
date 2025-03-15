<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArithmeticTableController extends AbstractController
{
    #[Route('/arithmetic/table', name: 'app_arithmetic_table')]
    public function index(): Response
    {
        return $this->render('arithmetic_table/index.html.twig', [
            'controller_name' => 'ArithmeticTableController',
        ]);
    }
    #[Route('/table/multiplication/accueil', name: 'accueil', methods:['GET'])]
    public function accueil(): Response
    {
        return $this->render('arithmetic_table/accueil.html.twig', [
            'controller_name' => 'ArithmeticTableController',
        ]);
    }

    #[Route('/table/multiplication/{number}/{taille}', name: 'multip_table',methods:['GET'])]
    public function multip($number,$taille): Response
    {
        $multiplicationTable = [];
        for ($i = 1; $i <= $taille; $i++) {
            $multiplicationTable[] = [
                'number' => $number,
                'symbol' => "x",
                'i'=>$i,
                'result' => $number * $i
            ];
        }
        return $this->render('arithmetic_table/multip.html.twig', [
            'controller_name' => 'ArithmeticTableController',
            'number'=> $number,
            'operation'=>"multiplication",
            'table'=> $multiplicationTable
        ]);
    }

    #[Route('/table/{operation}/{number}/{taille}', name: 'operations',methods:['GET'])]
    public function showOperation($operation,$number, $taille): Response
    {
        $table=[];
        if($operation=="multiplication"){
        for ($i = 1; $i <= $taille; $i++) {
            $table[] = [
                'number' => $number,
                'symbol' => "x",
                'i'=>$i,
                'result' => $number * $i
            ];
        }
        }if($operation=="addition"){
            for ($i = 1; $i <= $taille; $i++) {
                $table[] = [
                    'number' => $number,
                    'symbol' => "+",
                    'i'=>$i,
                    'result' => $number + $i
                ];
            }
            }
            if($operation=="division"){
                for ($i = 1; $i <= $taille; $i++) {
                    $table[] = [
                        'number' => $number,
                        'symbol' => "/",
                        'i'=>$i,
                        'result' => $number / $i
                    ];
                }
                }
        return $this->render('arithmetic_table/multip.html.twig', [
            'controller_name' => 'ArithmeticTableController',
            'number'=>$number,
            'operation'=>$operation,
            'table'=>$table
        ]);
    }
}

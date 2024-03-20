<?php

namespace App\Controller;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class AuthorController extends AbstractController
{
    #[Route('/api/authors', name: 'app_author',methods:['GET'])]
    public function getAllAuthor(AuthorRepository $authorRepository,SerializerInterface $serializer): JsonResponse
    {
       $AuthorList=$authorRepository->findAll();
       $JsonAuthor=$serializer->serialize($AuthorList,'json',['groups' => 'getAuthor']);
       return new JsonResponse($JsonAuthor,Response::HTTP_OK,[],true);
    }
    #[Route('/api/authors/{id}',name:'detailAuthor',methods:['GET'])]
    public function getDetailAuthor(Author $Author,Serializer $serializer){
        $JsonBook=$serializer->serialize($Author,'json',['groups' => 'getBooks']);
        return new JsonResponse($JsonBook,Response::HTTP_OK,[] ,true);

    }
}

<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    #[Route('/api/books', name: 'app_book',methods:['GET'])]
    public function getAllBooks(BookRepository $bookRepository,SerializerInterface $serializer): JsonResponse
    {
        $BookList=$bookRepository->findAll();
        $JsonBook=$serializer->serialize($BookList,'json',['groups' => 'getBooks']);
        return new JsonResponse($JsonBook,Response::HTTP_OK,[],true);
    }
    #[Route('/api/books/{id}',name:'DetailBook',methods:['GET'])]
    public function getDetailBook(Book $book,SerializerInterface $serializer,BookRepository $bookRepository){
        //$book=$bookRepository->find($id);
        // if ($book) {
        //     $JsonBook=$serializer->serialize($book,'json');
        //     return new JsonResponse($JsonBook,Response::HTTP_OK,[] ,true);
        //     }
        //     return new JsonResponse(null,Response::HTTP_NOT_FOUND);
        $JsonBook=$serializer->serialize($book,'json',['groups' => 'getBooks']);
        return new JsonResponse($JsonBook,Response::HTTP_OK,[] ,true);
    }
}

<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends AbstractController
{
    #[Route('/api/books', name: 'app_book',methods:['GET'])]
    public function getAllBooks(BookRepository $bookRepository,SerializerInterface $serializer,Request $request): JsonResponse
    {
        $page=$request->get('page',1);
        $limit=$request->get('limit',3);
        $BookList=$bookRepository->findAllWithPagination($page,$limit);
        $JsonBookList=$serializer->serialize($BookList,'json',['groups' => 'getBooks']);
        return new JsonResponse($JsonBookList,Response::HTTP_OK,[],true);
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

    #[Route('/api/books/{id}',name:'deleteBook',methods:['DELETE'])]
    public function deleteBook(Book $book,EntityManagerInterface $en):JsonResponse{
        $en->remove($book);
        $en->flush();
        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/books',name:"createBook",methods:['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un livre')]
    public function createBook(Request $request,SerializerInterface $serializer,EntityManagerInterface $en,UrlGeneratorInterface $urlgenerator,AuthorRepository $authorRepository,ValidatorInterface $validator):JsonResponse
    {
        $book =$serializer->deserialize($request->getContent(),Book::class,'json');
        //On verife les erreurs
        $errors=$validator->validate($book);
        if ($errors->count()>0) {
            return new JsonResponse($serializer->serialize($errors,'json'),JsonResponse::HTTP_BAD_REQUEST,[],true);
        }
        $en->persist($book);
        $en->flush();
       //recuperation de l'ensemble de donnees envoyes sous forme de tableau
       $content=$request->toArray();
       //Recuperation de l'idAuhtor. S'il n'est pas defini, alors on met -1 par defaut
       $idAuthor=$content['idAuthor'] ?? -1;
       // On cherche l'auteur qui correspond et on l'assigne au livre.
        // Si "find" ne trouve pas l'auteur, alors null sera retourné.
        $book->setAuthor($authorRepository->find($idAuthor));
       
        $JsonBook=$serializer->serialize($book,'json',['groups'=>'getBooks']);
        $location=$urlgenerator->generate('DetailBook',['id'=>$book->getId()],
        UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($JsonBook,Response::HTTP_CREATED,["Location"=>$location],true);
    }

    #[Route('/api/books/{id}',name:"updateBook",methods:['PUT'])]
    public function updateBook(Request $request,SerializerInterface $serializer,Book $curentBook,EntityManagerInterface $en,AuthorRepository $authorRepository ):JsonResponse
    {
        $updatedBook=$serializer->deserialize($request->getContent(),Book::class,'json',[AbstractNormalizer::OBJECT_TO_POPULATE=>$curentBook]);
        $content=$request->toArray();
        $idAuthor=$content['idAuthor'] ?? -1;
        $updatedBook->setAuthor($authorRepository->find($idAuthor));

        $en->persist($updatedBook);
        $en->flush();
        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT);

    }
}

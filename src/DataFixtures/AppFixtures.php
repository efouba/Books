<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
     $this->userPasswordHasher=$userPasswordHasher;   
    }
    
    public function load(ObjectManager $manager): void
    {
        //Creation d'un user 
        $user=new User();
        $user->setEmail("user@bookapi.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user,"password"));
        $manager->persist($user);

        //Creation du conpte administrateur
        $useradmin=new User();
        $useradmin->setEmail("admin@bookapi.com");
        $useradmin->setRoles(["ROLE_ADMIN"]);
        $useradmin->setPassword($this->userPasswordHasher->hashPassword($useradmin,"password"));
        $manager->persist($useradmin);

        // Création des auteurs.
        $listAuthor = [];
        for ($i = 0; $i < 10; $i++) {
        // Création de l'auteur lui-même.
        $author = new Author();
        $author->setFirstName("Prénom " . $i);
        $author->setLastName("Nom " . $i);
        $manager->persist($author);
        // On sauvegarde l'auteur créé dans un tableau.
        $listAuthor[] = $author;
        }

        for ($i=0; $i <20 ; $i++) { 
            
            $book=new Book();
            $book->setTitle('Livre'.$i);
            $book->setCoverText('Quatrieme de couverture N'.$i);
            $book->setAuthor($listAuthor[array_rand($listAuthor)]);
            $manager->persist($book);
        }
        $manager->flush();
        
    }
}

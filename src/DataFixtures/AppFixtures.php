<?php

namespace App\DataFixtures;

use App\Entity\Accomodation;
use App\Entity\Announcement;
use App\Entity\Convenience;
use App\Entity\Image;
use App\Entity\Message;
use App\Entity\Review;
use App\Entity\Service;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{

    private const STATUSES = ['pending', 'confirmed', 'cancelled'];
    private const GENDERS = ['male', 'female'];

    public function load(ObjectManager $manager): void
    {



        // j'importe mes données en json et les décode pour travailler avec un tableau associatif
        $accomodations = json_decode(file_get_contents(__DIR__ . '/data/accomodations.json'), true);
        $announcements = json_decode(file_get_contents(__DIR__ . '/data/announcements.json'), true);
        $reviews = json_decode(file_get_contents(__DIR__ . '/data/reviews.json'), true);
        $conveniences = json_decode(file_get_contents(__DIR__ . '/data/conveniences.json'), true);
        $services = json_decode(file_get_contents(__DIR__ . '/data/services.json'), true);


        // --------- USERS ----------------------------------------------------------

        $faker = Factory::create('fr_FR');
        $users = [];


        for ($i = 0; $i < 30; $i++) {
            $user = new User();
            $gender = $faker->randomElement(self::GENDERS);
            $user
                ->setEmail($faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPassword('test')
                ->setFirstName($faker->firstName($gender))
                ->setLastName($faker->lastName())
                ->setUsername($faker->userName())
                ->setBirthDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('1940-01-01', '-20 years', 'Europe/Paris')))
                ->setGender($gender)
                ->setBillingAddress($faker->address())
                ->setIsVerified($faker->boolean(70))
                ->setProfilePicture('generic-user.jpg')
                ->setCreatedAt(new DateTimeImmutable);

            $manager->persist($user);
            $users[] = $user;

        }

        $regularUser = new User();
        $regularUser
            ->setEmail('regular@user.com')
            ->setRoles(['ROLE_USER'])
            ->setPassword('test')
            ->setFirstName('john')
            ->setLastName('doe')
            ->setUsername('john.doe')
            ->setBirthDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('1980-01-01', '-20 years', 'Europe/Paris')))
            ->setGender('male')
            ->setBillingAddress($faker->address())
            ->setIsVerified(true)
            ->setProfilePicture('generic-user.jpg')
            ->setCreatedAt(new DateTimeImmutable);



        $manager->persist($regularUser);
        $users[] = $regularUser;


        $adminUser = new User();
        $adminUser
            ->setEmail('admin@livandco.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword('test')
            ->setFirstName('eleanor')
            ->setLastName('green')
            ->setUsername('eleanor.green')
            ->setBirthDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('1980-01-01', '-20 years', 'Europe/Paris')))
            ->setGender('female')
            ->setBillingAddress($faker->address())
            ->setIsVerified(true)
            ->setProfilePicture('admin-user.jpg')
            ->setCreatedAt(new DateTimeImmutable);

        $manager->persist($adminUser);
        $users[] = $adminUser;


        // --------- ACCOMODATIONS ----------------------------------------------------------

        $persistedAccomodations = [];

        foreach ($accomodations as $accomodationItem) {

            $user = $faker->randomElement($users);
            $accomodation = new Accomodation();
            $accomodation
                ->setAddressLine1($accomodationItem['addressLine1'])
                ->setAddressLine2($accomodationItem['addressLine2'])
                ->setCity($accomodationItem['city'])
                ->setZipCode($accomodationItem['zipCode'])
                ->setCountry($accomodationItem['country'])
                ->setLongitude($accomodationItem['longitude'])
                ->setLatitude($accomodationItem['latitude'])
                ->setSurface($accomodationItem['surface'])
                ->setMixedGender($faker->boolean(90))
                ->setOwnershipDeedPath($accomodationItem['ownershipDeedPath'])
                ->setInsuranceCertificatePath($accomodationItem['insuranceCertificatePath'])
                ->setCoverPicture($accomodationItem['coverPicture'])
                ->setOwner($user);

            $manager->persist($accomodation);
            $persistedAccomodations[] = $accomodation;


            // --------- ANNOUNCEMENTS ----------------------------------------------------------

            for ($i = 0; $i < $faker->randomNumber() - 3; $i++) {

                $announcementItem = $faker->randomElement($announcements);
                $announcement = new Announcement();
                $announcement
                    ->setTitle($announcementItem['title'])
                    ->setDescription($announcementItem['description'])
                    ->setDailyPrice($announcementItem['dailyPrice'])
                    ->setNbPlace($announcementItem['nbPlace'])
                    ->setAccomodation($accomodation)
                    ->setOwner($user);

                $manager->persist($announcement);
            }
        }

        // --------- IMAGES ----------------------------------------------------------

        foreach ($persistedAccomodations as $persistedAccomodation) {
            $randomNb = $faker->numberBetween(4, 8);
            for ($i = 1; $i < $randomNb; $i++) {
                $image = new Image();
                $image
                    ->setPath("generic-accomodation-$i.jpg")
                    ->setAccomodation($persistedAccomodation);
                $manager->persist($image);
            }
        }



        foreach ($users as $user) {
            // --------- MESSAGES ----------------------------------------------------------
            $randomNb = $faker->numberBetween(4, 12);

            do {
                $receiver = $faker->randomElement($users);
            } while ($receiver === $user);

            for ($i = 1; $i < $randomNb; $i++) {
                $message = new Message();
                $message
                    ->setContent($faker->realTextBetween(80, 500))
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setSender($user)
                    ->setReceiver($receiver);
                $manager->persist($message);
            }
            // --------- REVIEWS ----------------------------------------------------------

            $randomNb2 = $faker->numberBetween(0, 7);

            for ($i = 1; $i < $randomNb2; $i++) {

                $reviewItem = $faker->randomElement($reviews);
                $review = new Review();
                $review
                    ->setRating($reviewItem['rating'])
                    ->setComment($reviewItem['comment'])
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setUser($user);
                $manager->persist($review);
            }

        }

        // --------- CONVENIENCES ----------------------------------------------------------

        foreach ($conveniences['shared'] as $convenienceItem) {

            $convenience = new Convenience();
            $convenience
                ->setName($convenienceItem['name'])
                ->setIcon($convenienceItem['icon']);

            $manager->persist($convenience);

        }

        // --------- SERVICES ----------------------------------------------------------

        foreach ($services as $serviceItem) {

            $service = new Service();
            $service
                ->setName($serviceItem['name'])
                ->setDescription($serviceItem['description']);

            $manager->persist($service);

        }


        $manager->flush();
    }
}

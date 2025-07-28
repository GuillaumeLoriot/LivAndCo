<?php

namespace App\DataFixtures;

use App\Entity\Accomodation;
use App\Entity\Announcement;
use App\Entity\Convenience;
use App\Entity\Image;
use App\Entity\Message;
use App\Entity\Reservation;
use App\Entity\Review;
use App\Entity\Service;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private const STATUSES = ['pending', 'confirmed', 'cancelled'];
    private const GENDERS = ['male', 'female'];

        public function __construct(
        private UserPasswordHasherInterface $hasher,

    ) {
    }

    public function load(ObjectManager $manager): void
    {



        // j'importe mes données en json et les décode pour travailler avec un tableau associatif
        $accomodations = json_decode(file_get_contents(__DIR__ . '/data/accomodations.json'), true);
        $announcements = json_decode(file_get_contents(__DIR__ . '/data/announcements.json'), true);
        $reviews = json_decode(file_get_contents(__DIR__ . '/data/reviews.json'), true);
        $conveniences = json_decode(file_get_contents(__DIR__ . '/data/conveniences.json'), true);
        $services = json_decode(file_get_contents(__DIR__ . '/data/services.json'), true);
        $reservations = json_decode(file_get_contents(__DIR__ . '/data/reservations.json'), true);
        $announcementImages = json_decode(file_get_contents(__DIR__ . '/data/announcementImages.json'), true);
        $accomodationImages = json_decode(file_get_contents(__DIR__ . '/data/accomodationImages.json'), true);
        $occupations = json_decode(file_get_contents(__DIR__ . '/data/occupations.json'), true);


        // --------- USERS ----------------------------------------------------------

        $faker = Factory::create('fr_FR');
        $users = [];


        for ($i = 0; $i < 30; $i++) {
            $user = new User();
            $gender = $faker->randomElement(self::GENDERS);
            $user
                ->setEmail($faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->hasher->hashPassword($user, 'test'))
                ->setFirstName($faker->firstName($gender))
                ->setLastName($faker->lastName())
                ->setUsername($faker->userName())
                ->setBirthDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('1940-01-01', '-20 years', 'Europe/Paris')))
                ->setGender($gender)
                ->setBillingAddress($faker->address())
                ->setIsVerified($faker->boolean(70))
                ->setProfilePicture('generic-user.jpg')
                ->setPhoneNumber($faker->phoneNumber())
                ->setOccupation($faker->randomElement($occupations))
                ->setCreatedAt(new DateTimeImmutable);

            $manager->persist($user);
            $users[] = $user;

        }

        $regularUser = new User();
        $regularUser
            ->setEmail('regular@user.com')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->hasher->hashPassword($regularUser, 'test'))
            ->setFirstName('john')
            ->setLastName('doe')
            ->setUsername('john.doe')
            ->setBirthDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('1980-01-01', '-20 years', 'Europe/Paris')))
            ->setGender('male')
            ->setBillingAddress($faker->address())
            ->setIsVerified(true)
            ->setProfilePicture('generic-user.jpg')
            ->setPhoneNumber($faker->phoneNumber())
            ->setOccupation($faker->randomElement($occupations))
            ->setCreatedAt(new DateTimeImmutable);



        $manager->persist($regularUser);
        $users[] = $regularUser;


        $adminUser = new User();
        $adminUser
            ->setEmail('admin@livandco.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($adminUser, 'admin'))
            ->setFirstName('eleanor')
            ->setLastName('green')
            ->setUsername('eleanor.green')
            ->setBirthDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('1980-01-01', '-20 years', 'Europe/Paris')))
            ->setGender('female')
            ->setBillingAddress($faker->address())
            ->setIsVerified(true)
            ->setProfilePicture('admin-user.jpg')
            ->setPhoneNumber('0123456706')
            ->setOccupation('Liv&Co Administrator')
            ->setCreatedAt(new DateTimeImmutable);

        $manager->persist($adminUser);

        // --------- SERVICES ----------------------------------------------------------

        $persistedServices = [];

        foreach ($services as $serviceItem) {

            $service = new Service();
            $service
                ->setName($serviceItem['name'])
                ->setDescription($serviceItem['description']);

            $manager->persist($service);
            $persistedServices[] = $service;

        }

        // --------- CONVENIENCES ----------------------------------------------------------

        $privateConveniences = [];
        $sharedConveniences = [];
        $allConveniences = [];
        $wifiConvenience = new Convenience();
        $wifiConvenience
            ->setName('wifi')
            ->setIcon('wifi.svg');

        $manager->persist($wifiConvenience);


        foreach ($conveniences['private'] as $privateConvenienceItem) {

            $privateConvenience = new Convenience();
            $privateConvenience
                ->setName($privateConvenienceItem['name'])
                ->setIcon($privateConvenienceItem['icon']);

            $manager->persist($privateConvenience);
            $privateConveniences[] = $privateConvenience;

        }
        foreach ($conveniences['shared'] as $sharedConvenienceItem) {

            $sharedConvenience = new Convenience();
            $sharedConvenience
                ->setName($sharedConvenienceItem['name'])
                ->setIcon($sharedConvenienceItem['icon']);

            $manager->persist($sharedConvenience);
            $sharedConveniences[] = $sharedConvenience;

        }
        $allConveniences = array_merge($privateConveniences, $sharedConveniences);


        // --------- ACCOMODATIONS ----------------------------------------------------------

        $persistedAccomodations = [];
        $persistedAnnouncements = [];


        foreach ($accomodations as $accomodationItem) {

            $randomConveniences = $faker->randomElements($allConveniences, $faker->numberBetween(4, 9));

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
                ->setOwner($user)
                ->addConvenience($wifiConvenience);

            foreach ($randomConveniences as $randomConvenience) {
                $accomodation->addConvenience($randomConvenience);
            }

            $manager->persist($accomodation);
            $persistedAccomodations[] = $accomodation;

            // --------- ANNOUNCEMENTS ----------------------------------------------------------

            $randomPrivateConveniences = $faker->randomElements($privateConveniences, $faker->numberBetween(2, 4));
            $randomServices = $faker->randomElements($persistedServices, $faker->numberBetween(2, 4));

            for ($i = 0; $i < $faker->numberBetween(2, 7); $i++) {

                $announcementItem = $faker->randomElement($announcements);
                $randomAnnouncementImage = $faker->randomElement($announcementImages);
                $announcement = new Announcement();
                $announcement
                    ->setTitle($announcementItem['title'])
                    ->setDescription($announcementItem['description'])
                    ->setDailyPrice($announcementItem['dailyPrice'])
                    ->setNbPlace($announcementItem['nbPlace'])
                    ->setCoverPicture( $randomAnnouncementImage['coverPicture'])
                    ->setAccomodation($accomodation)
                    ->setOwner($user)
                    ->addConvenience($wifiConvenience);


                foreach ($randomPrivateConveniences as $randomPrivateConvenience) {
                    $announcement->addConvenience($randomPrivateConvenience);
                }

                foreach ($randomServices as $randomService) {
                    $announcement->addService($randomService);
                }

                $manager->persist($announcement);
                $persistedAnnouncements[] = $announcement;
            }
        }


        // --------- RESERVATIONS ----------------------------------------------------------

        foreach ($persistedAnnouncements as $persistedAnnouncement) {
            $announcementDailyPrice = $persistedAnnouncement->getDailyPrice();
            $owner = $persistedAnnouncement->getOwner();
            $randomNb = $faker->numberBetween(2, 8);
            $randomReservations = $faker->randomElements($reservations, $randomNb);
            foreach ($randomReservations as $randomReservation) {
                $dateStart = new DateTime($randomReservation['startDate']);
                $endDate = new DateTime($randomReservation['endDate']);
                $interval = $dateStart->diff($endDate);
                do {
                    $randomUser = $faker->randomElement($users);
                } while ($randomUser === $owner);


                $reservation = new Reservation();
                $reservation
                    ->setStartDate($dateStart)
                    ->setEndDate($endDate)
                    ->setStatus('confirmed')
                    ->setTotalPrice(($interval->days + 1) * $announcementDailyPrice)
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setAnnouncement($persistedAnnouncement)
                    ->setUser($randomUser);

                $manager->persist($reservation);


                // --------- REVIEW ----------------------------------------------------------


                $reviewItem = $faker->randomElement($reviews);
                $review = new Review();
                $review
                    ->setRating($reviewItem['rating'])
                    ->setComment($reviewItem['comment'])
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setUser($randomUser)
                    ->setReservation($reservation);

                $manager->persist($review);

            }
        }


        // --------- IMAGES ----------------------------------------------------------

        foreach ($persistedAccomodations as $persistedAccomodation) {
            $randomImages = $faker->randomElements($accomodationImages, $faker->numberBetween(4, 8));
            foreach ($randomImages as $randomImage) {
                $image = new Image();
                $image
                    ->setPath($randomImage['path'])
                    ->setAccomodation($persistedAccomodation);
                $manager->persist($image);
            }
        }


        // --------- MESSAGES ----------------------------------------------------------
        foreach ($users as $user) {

            do {
                $receiver = $faker->randomElement($users);
            } while ($receiver === $user);

            for ($i = 1; $i < $faker->numberBetween(4, 12); $i++) {
                $message = new Message();
                $message
                    ->setContent($faker->realTextBetween(80, 500))
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setSender($user)
                    ->setReceiver($receiver);
                $manager->persist($message);
            }

        }

        $manager->flush();
    }
}

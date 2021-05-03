<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\GasReading;
use DateInterval;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GasReadingFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $currentTime = new \DateTime();
        for ($i = 0; $i < 10; $i++) {
            $company = new Company();
            $company->setCompanyName($this->generateRandomCompanyName() . " OÜ");
            for ($j = 0; $j < 24; $j++) {
                $gasReading = new GasReading();
                $gasReadingTime = clone $currentTime;
                $gasReadingTime->sub(new DateInterval('PT' . $j . 'H'));
                $gasReading->setCreated($gasReadingTime);
                $gasReading->setValue(rand(0, 100));
                $manager->persist($gasReading);
                $company->addGasReading($gasReading);
            }

            $manager->persist($company);
        }

        $manager->flush();
    }

    private function generateRandomCompanyName($length = 5)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $randomString = ucwords($randomString);
        return $randomString;
    }
}

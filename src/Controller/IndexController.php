<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\GasReading;
use App\Entity\Person;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     *
     * @route("/", name="index", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
        $companiesReadings = $this->generateCompaniesReadings();
        $totalGasReadings = $this->generateTotalGasReadings();

        return $this->render('index.html.twig', [
            "companiesData" => $companiesReadings,
            "totalGasReadings" => $totalGasReadings
        ]);
    }

    private function generateCompaniesReadings() {
        $entityManager = $this->getDoctrine()->getManager();
        $companies = $entityManager->getRepository(Company::class)->findAll();
        $companiesData = [];

        /** @var Company $company */
        foreach ($companies as $company) {
            $companyData = [
                "company" => $company->getCompanyName(),
                "id" => $company->getId()
            ];
            $dataPoints = [];
            $gasReadings = $entityManager->getRepository(GasReading::class)->findByDateAfterAndCompanyId($company, new \DateTime("-25 hours"));
            /** @var GasReading $gasReading */
            foreach ($gasReadings as $gasReading) {
                $dataPoints[] = ['x' => (int)$gasReading->getCreated()->format("U") * 1000, 'y' => $gasReading->getValue()];
            }

            $companyData['data_points'] = $dataPoints;
            $companiesData[] = $companyData;
        }

        return $companiesData;
    }

    private function generateTotalGasReadings()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $beginningDate = new DateTime((new DateTime())->format("Y-m-d H:00:00"));
        $endDate = (clone $beginningDate)->modify("+1 hour");
        $totalGasReadings = [];
        for ($i = 0; $i < 24; $i++) {
            $gasReadings = $entityManager->getRepository(GasReading::class)->findAllGasReadingsBetweenDateTimes($beginningDate, $endDate);
            $totalValue = 0;
            /** @var GasReading $gasReading */
            foreach ($gasReadings as $gasReading) {
                $totalValue += $gasReading->getValue();
            }

            $totalGasReadings[] = ['x' => (int)$beginningDate->format("U") * 1000, "y" => $totalValue];

            $beginningDate = (clone $beginningDate)->modify("-1 hour");
            $endDate = (clone $endDate)->modify("-1 hour");
        }
        return $totalGasReadings;
    }
}